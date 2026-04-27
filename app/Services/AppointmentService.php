<?php

namespace App\Services;

use App\Models\AppointmentModel;
use App\Models\PatientModel;
use App\Models\UserModel;
use App\Models\DentistModel;
use App\Models\MedicalRecordModel;
use App\Models\AppointmentServiceModel;
use App\Models\MedicalConditionModel;
use App\Services\AppointmentStatus;

class AppointmentService
{
    protected $db;

    private function createInternal($request): int
    {
        $apptModel    = new AppointmentModel();
        $patientModel = new PatientModel();
        $userModel    = new UserModel();
        $medModel     = new MedicalRecordModel();
        $apptSvcModel = new AppointmentServiceModel();
        $mcModel      = new MedicalConditionModel();

        $accountType = $request->getPost('account_type');

        // =========================
        // 1. FORMAT & CALCULATE TIMES
        // =========================
        $startDate = $this->formatDate($request->getPost('appointment_date'));
        $startTime = $request->getPost('appointment_time');

        $serviceIds = array_filter(array_map('intval', $request->getPost('services') ?? []));
        $levels     = $request->getPost('levels') ?? [];

        $levelMap = [];
        foreach ($serviceIds as $svcId) {
            if (isset($levels[$svcId])) {
                $levelMap[$svcId] = $levels[$svcId];
            }
        }

        $expectedDuration = \App\Services\AppointmentDurationCalculator::calculate(
            $serviceIds,
            $levelMap
        );

        $startTimestamp = strtotime("{$startDate} {$startTime}");
        $endTimestamp   = $startTimestamp + ($expectedDuration * 60);
        $endDate        = date('Y-m-d', $endTimestamp);
        $endTime        = date('H:i:s', $endTimestamp);

        // =========================
        // BUG #4 FIX: Use the unified hasConflict() instead of the
        // old isDentistBusy() which had incomplete date logic.
        // =========================
        if ($apptModel->hasConflict(
            (int) $request->getPost('dentist_id'),
            date('Y-m-d H:i:s', $startTimestamp),
            date('Y-m-d H:i:s', $endTimestamp)
        )) {
            throw new \Exception("Selected time slot is no longer available.");
        }

        // =========================
        // 2. PATIENT HANDLING
        // =========================
        $patientId = (int) $request->getPost('patient_id');

        if ($accountType === 'new') {
            $userData = [
                'username'  => trim($request->getPost('username')),
                'email'     => trim($request->getPost('email')),
                'password'  => password_hash($request->getPost('password'), PASSWORD_DEFAULT),
                'role'      => 'patient',
                'is_active' => 1,
            ];

            $userId = $userModel->insert($userData);
            if (!$userId) {
                $errors = $userModel->errors();
                log_message('error', '[User Insert Failed] ' . json_encode($errors));
                throw new \Exception("Failed to create user account: " . implode(', ', $errors ?? ['Unknown error']));
            }

            $mobile = trim($request->getPost('primary_mobile') ?? '');
            $mobile = preg_replace('/[^\d+]/', '', $mobile);
            if (empty($mobile) || strlen($mobile) < 10) {
                $mobile = '09' . str_pad(preg_replace('/\D/', '', $mobile), 9, '0', STR_PAD_LEFT);
            }

            $generatedCode = $patientModel->generatePatientCode();

            $patientData = [
                'user_id'        => $userId,
                'patient_code'   => $generatedCode,
                'first_name'     => trim($request->getPost('first_name')),
                'middle_name'    => trim($request->getPost('middle_name') ?? ''),
                'last_name'      => trim($request->getPost('last_name')),
                'name_suffix'    => $request->getPost('name_suffix'),
                'birthdate'      => $this->formatDate($request->getPost('birthdate')),
                'gender'         => $request->getPost('gender'),
                'primary_mobile' => $mobile,
                'region'         => trim($request->getPost('region') ?? ''),
                'province'       => trim($request->getPost('province') ?? ''),
                'city'           => trim($request->getPost('city') ?? ''),
                'barangay'       => trim($request->getPost('barangay') ?? ''),
                'country'        => 'Philippines',
            ];

            $patientModel->validate(false);
            $patientId = $patientModel->insert($patientData);
            $patientModel->validate(true);

            if (!$patientId) {
                $errors  = $patientModel->errors();
                $dbError = $patientModel->db->error();
                log_message('error', '[Patient Insert Failed] Validation: ' . json_encode($errors) . ' | DB Error: ' . json_encode($dbError));
                throw new \Exception("Failed to create patient record: " .
                    (!empty($errors) ? implode(', ', $errors) : ($dbError['message'] ?? 'Database error')));
            }
        }

        if (!$patientId || $patientId <= 0) {
            throw new \Exception("Invalid patient ID.");
        }

        // =========================
        // 3. CREATE APPOINTMENT
        // =========================
        $appointmentId = $apptModel->insert([
            'patient_id'                => $patientId,
            'dentist_id'                => $request->getPost('dentist_id'),
            'appointment_date'          => $startDate,
            'appointment_time'          => $startTime,
            'end_date'                  => $endDate,
            'end_time'                  => $endTime,
            'expected_duration_minutes' => $expectedDuration,
            'buffer_minutes'            => 10,
            'status'                    => $this->determineInitialStatus($request),
            'updated_at'                => date('Y-m-d H:i:s'), // Bug #1 fix
        ]);

        if (!$appointmentId) {
            throw new \Exception("Failed to create appointment.");
        }

        // =========================
        // 4. SAVE APPOINTMENT SERVICES
        // =========================
        $servicesData = [];
        foreach ($serviceIds as $svcId) {
            $servicesData[] = [
                'service_id'    => $svcId,
                'service_level' => $levelMap[$svcId] ?? 'Standard',
            ];
        }
        $apptSvcModel->insertServices($appointmentId, $servicesData);

        // =========================
        // 5. SAVE MEDICAL RECORD (CONDITIONAL)
        // =========================
        $medicalSubmitted = $request->getPost('medical_history_submitted');

        if ($medicalSubmitted == '1') {
            $conditions  = array_filter($request->getPost('medical_conditions') ?? []);
            $otherAllergy = $request->getPost('other_allergy');

            if (!empty($otherAllergy)) {
                $key    = strtolower(preg_replace('/[^a-z0-9]+/', '_', trim($otherAllergy)));
                $exists = $mcModel->where('condition_key', $key)->first();
                if (!$exists) {
                    $mcModel->insert([
                        'condition_key'   => $key,
                        'condition_label' => ucfirst(trim($otherAllergy)),
                        'category'        => 'allergy',
                        'is_active'       => 1,
                    ]);
                }
                $conditions[] = $key;
            }

            $medData = [
                'patient_id'                  => $patientId,
                'medical_conditions'          => !empty($conditions) ? json_encode(array_unique($conditions)) : null,
                'physician_name'              => null_if_empty($request->getPost('physician_name')),
                'physician_specialty'         => null_if_empty($request->getPost('physician_specialty')),
                'physician_address'           => null_if_empty($request->getPost('physician_address')),
                'physician_phone'             => null_if_empty($request->getPost('physician_phone')),
                'serious_illness_details'     => null_if_empty($request->getPost('serious_illness_details')),
                'hospitalization_details'     => null_if_empty($request->getPost('hospitalization_details')),
                'medication_details'          => null_if_empty($request->getPost('medication_details')),
                'other_allergy'               => null_if_empty($otherAllergy),
                'is_good_health'              => bool_to_int($request->getPost('is_good_health')),
                'is_under_medical_treatment'  => bool_to_int($request->getPost('is_under_medical_treatment')),
                'has_serious_illness'         => bool_to_int($request->getPost('has_serious_illness')),
                'is_hospitalized'             => bool_to_int($request->getPost('is_hospitalized')),
                'is_taking_medication'        => bool_to_int($request->getPost('is_taking_medication')),
                'uses_tobacco'                => bool_to_int($request->getPost('uses_tobacco')),
                'uses_drugs'                  => bool_to_int($request->getPost('uses_drugs')),
                'is_pregnant'                 => bool_to_int($request->getPost('is_pregnant')),
                'is_nursing'                  => bool_to_int($request->getPost('is_nursing')),
                'is_taking_birth_control'     => bool_to_int($request->getPost('is_taking_birth_control')),
                'blood_type'                  => null_if_empty($request->getPost('blood_type')),
                'blood_pressure'              => null_if_empty($request->getPost('blood_pressure')),
            ];

            $bleedingMins = (int) ($request->getPost('bleeding_mins') ?? 0);
            $bleedingSecs = (int) ($request->getPost('bleeding_secs') ?? 0);
            $medData['bleeding_time'] = ($bleedingMins > 0 || $bleedingSecs > 0)
                ? trim("{$bleedingMins}m {$bleedingSecs}s")
                : null;

            $hasMeaningfulData = (
                !empty(trim($medData['physician_name'] ?? ''))
                || !empty(trim($medData['blood_type'] ?? ''))
                || !empty(trim($medData['blood_pressure'] ?? ''))
                || !empty(trim($medData['bleeding_time'] ?? ''))
                || ($medData['is_good_health'] !== null)
                || ($medData['uses_tobacco'] !== null)
                || ($medData['uses_drugs'] !== null)
                || (!empty($medData['medical_conditions']) && $medData['medical_conditions'] !== '[]')
            );

            if ($hasMeaningfulData) {
                $existing = $medModel->where('patient_id', $patientId)->first();
                if (!$existing) {
                    $medModel->insert($medData);
                } else {
                    $medModel->update($existing['id'], $medData);
                }
            }
        }

        return $appointmentId;
    }

    public function create($request): int
    {
        $db = \Config\Database::connect();
        try {
            $db->transBegin();
            $appointmentId = $this->createInternal($request);
            $db->transCommit();
            return $appointmentId;
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '[AppointmentService::create] ' . $e->getMessage());
            throw $e;
        }
    }

    private function determineInitialStatus($request): string
    {
        $bookedBy = $request->getPost('booked_by') ?? 'patient';
        if ($bookedBy === 'receptionist') {
            return \App\Constants\AppointmentStatus::CONFIRMED;
        }
        return \App\Constants\AppointmentStatus::PENDING;
    }

    public function createWithQueueAndEmail($request): array
    {
        $this->db = \Config\Database::connect();
        $db = $this->db;

        try {
            $db->transBegin();

            $appointmentId = $this->createInternal($request);
            if (!$appointmentId) {
                throw new \RuntimeException("Failed to create appointment.");
            }

            $apptModel    = new AppointmentModel();
            $patientModel = new PatientModel();
            $dentistModel = new DentistModel();

            $appointment = $apptModel->asArray()->find($appointmentId);
            if (!$appointment || !is_array($appointment)) {
                throw new \RuntimeException("Could not retrieve appointment after creation. ID: {$appointmentId}");
            }

            $patient = $patientModel->asArray()->find($appointment['patient_id']);
            $dentist = $dentistModel->asArray()->find($appointment['dentist_id']);

            $dentistId = (int) ($appointment['dentist_id'] ?? 0);
            $apptDate  = (string) ($appointment['appointment_date'] ?? date('Y-m-d'));

            $queueNumber = $this->generateQueueNumber($dentistId, $apptDate);

            $apptModel->update($appointmentId, [
                'queue_number' => $queueNumber,
                'queue_date'   => $apptDate,
                'updated_at'   => date('Y-m-d H:i:s'), // Bug #1 fix
            ]);

            $parsedDate = $this->parseDateValue($appointment['appointment_date']);
            $parsedTime = $this->parseTimeValue($appointment['appointment_time']);

            $firstName = $request->getPost('first_name') ?: ($patient['first_name'] ?? '');
            $lastName  = $request->getPost('last_name')  ?: ($patient['last_name'] ?? '');
            $fullName  = trim("{$firstName} {$lastName}") ?: 'Valued Patient';

            $patientEmail = $request->getPost('email');
            if (empty($patientEmail)) {
                $patientEmail = $this->getValidPatientEmail($patient ?? []);
            }

            $currentStatus = $appointment['status'] ?? \App\Constants\AppointmentStatus::PENDING;

            $payload = [
                'patient_name'     => $fullName,
                'queue_number'     => $queueNumber ?? 'N/A',
                'appointment_date' => date('F d, Y', strtotime($parsedDate)),
                'appointment_time' => date('h:i A', strtotime($parsedTime)),
                'dentist_name'     => $dentist ? 'Dr. ' . ($dentist['last_name'] ?? 'TBD') : 'TBD',
                'service_name'     => 'Dental Service',
                'patient_code'     => $patient['patient_code'] ?? 'N/A',
            ];

            $emailQueued = false;

            // =========================
            // EMAIL QUEUING LOGIC (SIMPLIFIED - NO CONFIRMATION TABLE)
            // =========================
            if (!empty($patientEmail) && filter_var($patientEmail, FILTER_VALIDATE_EMAIL)) {
                $emailService = new EmailService();

                // ✅ Determine template based on who booked
                $emailTemplate = match ($currentStatus) {
                    \App\Constants\AppointmentStatus::CONFIRMED => 'appointment_confirmed',  // Receptionist booking
                    \App\Constants\AppointmentStatus::PENDING   => 'appointment_pending',    // Patient self-booking
                    default => null,
                };

                // ✅ NO confirmation token logic — removed entirely
                // Patient self-bookings get 'pending' email without confirmation link
                // Receptionist approvals happen via dashboard, not email links

                // ✅ Queue email only if template is valid
                if ($emailTemplate !== null) {
                    $emailService->queueEmail($patientEmail, $fullName, $emailTemplate, $payload);
                }

                // ✅ Queue 24h reminder ONLY for confirmed appointments
                if ($currentStatus === \App\Constants\AppointmentStatus::CONFIRMED) {
                    try {
                        $reminderTime = new \DateTime($parsedDate . ' ' . $parsedTime);
                        $reminderTime->modify('-24 hours');
                        $emailService->queueEmail($patientEmail, $fullName, 'reminder_24h', $payload, $reminderTime);
                    } catch (\Exception $e) {
                        log_message('warning', "Reminder scheduling failed: {$e->getMessage()}");
                    }
                }

                $emailQueued = true;
            }

            $db->transCommit();

            return [
                'success'          => true,
                'appointment_id'   => $appointmentId,
                'queue_number'     => $queueNumber,
                'status'           => $currentStatus,
                'patient_email'    => $emailQueued ? $patientEmail : null,
                'patient_id'       => $appointment['patient_id'] ?? null,
                'patient_name'     => $fullName,
                'appointment_date' => $parsedDate,
                'appointment_time' => $parsedTime,
                'dentist_id'       => $dentistId,
                'service_ids'      => array_filter(array_map('intval', $request->getPost('services') ?? [])),
            ];
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '[createWithQueueAndEmail] ' . $e->getMessage() . ' @L' . $e->getLine());
            throw $e;
        }
    }

    public function reschedule($request): void
    {
        $apptModel = new AppointmentModel();
        $id        = $request->getPost('appointment_id');

        $startDate = $this->formatDate($request->getPost('appointment_date'));
        $startTime = $request->getPost('appointment_time');

        $original = $apptModel->find($id);
        $duration = $original['expected_duration_minutes'] ?? 30;

        $startTimestamp = strtotime("{$startDate} {$startTime}");
        $endTimestamp   = $startTimestamp + ($duration * 60);
        $endDate        = date('Y-m-d', $endTimestamp);
        $endTime        = date('H:i:s', $endTimestamp);

        // BUG #4 FIX: Use unified hasConflict() for reschedule too
        if ($apptModel->hasConflict(
            (int) $request->getPost('dentist_id'),
            date('Y-m-d H:i:s', $startTimestamp),
            date('Y-m-d H:i:s', $endTimestamp),
            (int) $id
        )) {
            throw new \Exception("Dentist is busy at this time.");
        }

        $apptModel->update($id, [
            'dentist_id'       => $request->getPost('dentist_id'),
            'appointment_date' => $startDate,
            'appointment_time' => $startTime,
            'end_date'         => $endDate,
            'end_time'         => $endTime,
            'updated_at'       => date('Y-m-d H:i:s'), // Bug #1 fix
        ]);
    }

    // =========================
    // HELPERS
    // =========================

    private function generateQueueNumber(int $dentistId, string $date): int
    {
        $apptModel = new AppointmentModel();
        $count     = $apptModel
            ->where('dentist_id', $dentistId)
            ->where('queue_date', $date)
            ->countAllResults();
        return $count + 1;
    }

    private function getValidPatientEmail(array $patient): ?string
    {
        if (!empty($patient['email']) && filter_var($patient['email'], FILTER_VALIDATE_EMAIL)) {
            return $patient['email'];
        }
        if (!empty($patient['user_id'])) {
            $userModel = new UserModel();
            $user      = $userModel->asArray()->select('email')->find($patient['user_id']);
            $email     = $user['email'] ?? null;
            if (is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $email;
            }
        }
        return null;
    }

    private function parseDateValue($value): string
    {
        if (empty($value)) return date('Y-m-d');
        if (is_object($value) && method_exists($value, 'format')) return $value->format('Y-m-d');
        if (is_string($value)) return $value;
        return date('Y-m-d');
    }

    private function parseTimeValue($value): string
    {
        if (empty($value)) return '09:00:00';
        if (is_object($value) && method_exists($value, 'format')) return $value->format('H:i:s');
        if (is_string($value)) return $value;
        return '09:00:00';
    }

    private function formatDate($date): string
    {
        return empty($date) ? date('Y-m-d') : date('Y-m-d', strtotime($date));
    }
}
