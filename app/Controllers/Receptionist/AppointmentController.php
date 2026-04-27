<?php

namespace App\Controllers\Receptionist;

use App\Controllers\BaseController;
use App\Models\AppointmentModel;
use App\Models\PatientModel;
use App\Models\ServiceModel;
use App\Models\DentistModel;
use App\Models\MedicalConditionModel;
use App\Services\AppointmentService;
use App\Constants\AppointmentStatus;
use App\Constants\TimeConfig;
use App\Traits\AppointmentValidationTrait;
use \App\Traits\RebookingFeeTrait;
class AppointmentController extends BaseController
{
    use AppointmentValidationTrait;

    protected AppointmentModel $apptModel;
    protected PatientModel $patientModel;
    protected ServiceModel $serviceModel;
    protected DentistModel $dentistModel;
    protected MedicalConditionModel $mcModel;
    protected $db;
    protected ?AppointmentService $appointmentService = null;

    public function __construct()
    {
        $this->apptModel    = new AppointmentModel();
        $this->patientModel = new PatientModel();
        $this->serviceModel = new ServiceModel();
        $this->dentistModel = new DentistModel();
        $this->mcModel      = new MedicalConditionModel();
        $this->db           = \Config\Database::connect();
    }

    // ==================== PUBLIC ACTIONS ====================

    public function index()
    {
        $tab = $this->request->getGet('tab') ?? 'today';
        [$allergies, $grouped] = $this->groupConditions(
            $this->fetchMedicalConditions()
        );

        return view('receptionist/appointments/index', [
            'title'        => 'Appointments',
            'currentTab'   => $tab,
            'counts'       => $this->apptModel->getCounts(),
            'appointments' => $this->apptModel->getFilteredAppointments($tab),
            'services'     => $this->getActiveServices(),
            'dentists'     => $this->getActiveDentists(),
            'allergies'    => $allergies,
            'grouped'      => $grouped,
        ]);
    }

    public function store()
    {
        $accountType = $this->request->getPost('account_type');

        if (!$this->validate($this->getStoreValidationRules($accountType))) {
            return redirect()->back()->withInput()
                ->with('validation_errors', $this->validator->getErrors());
        }

        try {
            $appointmentData = $this->prepareAppointmentData($accountType);
            $result = $this->getAppointmentService()->createWithQueueAndEmail(
                $this->requestWithMergedData($appointmentData)
            );

            if (!$result['success']) {
                throw new \RuntimeException($result['error'] ?? "Failed to create appointment.");
            }

            // =========================
            // BUG #2 FIX: REMOVED the second email send that was here.
            //
            // Previously this block existed and caused every receptionist
            // booking to send TWO emails to the patient:
            //
            //   if (!empty($result['patient_email'])) {
            //       $emailService->queueEmail(...'appointment_confirmed'...);
            //   }
            //
            // The email is already sent inside createWithQueueAndEmail()
            // in AppointmentService, using the correct template based on
            // the actual appointment status. No second send needed here.
            // =========================

            return $this->handleResponse(
                true,
                $this->formatSuccessMessage($result),
                ['queue_number' => $result['queue_number'] ?? null]
            );
        } catch (\Exception $e) {
            log_message('error', '[STORE] ' . $e->getMessage() . ' @L' . $e->getLine());
            return $this->handleResponse(false, 'Failed to book: ' . $e->getMessage());
        }
    }

    public function checkAvailability()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $dentistId  = $this->request->getGet('dentist_id');
        $date       = $this->request->getGet('date');
        $serviceIds = $this->request->getGet('service_ids');

        if (!$dentistId || !$date) {
            return $this->response->setJSON(['error' => 'Missing parameters']);
        }

        try {
            $requiredDuration = $this->calculateRequiredDuration($serviceIds);
            $slots = $this->apptModel->getAvailableSlots(
                $dentistId,
                $date,
                $requiredDuration,
                TimeConfig::CLINIC_START,
                TimeConfig::CLINIC_END
            );

            return $this->response->setJSON([
                'success'                    => true,
                'date'                       => $date,
                'dentist_id'                 => $dentistId,
                'required_duration_minutes'  => $requiredDuration,
                'slots'                      => $slots,
                'count'                      => count($slots),
                'duration_label'             => $this->formatDuration($requiredDuration),
            ]);
        } catch (\Exception $e) {
            log_message('error', '[AVAILABILITY] ' . $e->getTraceAsString());
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => 'Failed to check availability']);
        }
    }

    public function reschedule()
    {
        if (!$this->validate($this->getRescheduleRules())) {
            return $this->handleResponse(false, 'Invalid input.');
        }

        try {
            $appointmentId = $this->request->getPost('appointment_id');
            $original      = $this->apptModel->find($appointmentId);

            if (!$original) {
                return $this->handleResponse(false, 'Appointment not found.');
            }

            $newData = $this->prepareRescheduleData($original);
            $this->getAppointmentService()->reschedule(
                $this->requestWithMergedData($newData)
            );

            return $this->handleResponse(
                true,
                'Rescheduled successfully!',
                ['appointment_id' => $appointmentId]
            );
        } catch (\Exception $e) {
            log_message('error', '[RESCHEDULE] ' . $e->getMessage());
            return $this->handleResponse(false, $e->getMessage());
        }
    }

    public function searchPatients()
    {
        $term = trim($this->request->getGet('term') ?? $this->request->getPost('term') ?? '');
        if (!$term || strlen($term) < 2) {
            return $this->response->setJSON([]);
        }
        try {
            return $this->response->setJSON(
                $this->patientModel->searchPatients($term)
            );
        } catch (\Exception $e) {
            log_message('error', '[searchPatients] ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => 'Search failed: ' . $e->getMessage()]);
        }
    }

    public function patientHistory(int $patientId)
    {
        $patient = $this->patientModel
            ->select('id, patient_code, primary_mobile, gender, birthdate, first_name, middle_name, last_name, name_suffix')
            ->find($patientId);

        if (!$patient) {
            return redirect()->back()->with('error', 'Patient not found.');
        }

        $history = array_map(
            fn($h) => $this->formatHistoryEntry($h),
            $this->apptModel->getPatientHistory($patientId)
        );

        return view('appointments/patient_appointment_history', [
            'title'   => 'History: ' . $patient['first_name'] . ' ' . $patient['last_name'],
            'patient' => $patient,
            'history' => $history,
        ]);
    }

    public function updateStatus(int $id = null, string $status = null)
    {
        $id     = $id ?? (int) ($this->request->getGet('id') ?? $this->request->getPost('id'));
        $status = $status ?? $this->request->getGet('status') ?? $this->request->getPost('status');
        $status = strtolower(trim($status ?? ''));

        if (!$this->isValidStatusUpdate($id, $status)) {
            return $this->handleResponse(false, 'Invalid parameters.');
        }

        $appointment = $this->apptModel->asArray()->find($id);
        if (!$appointment) {
            return $this->handleResponse(false, 'Appointment not found.');
        }

        if ($appointment['status'] === $status) {
            return $this->handleResponse(true, 'Appointment is already ' . strtolower($status) . '.');
        }

        $this->apptModel->update($id, [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s'), // Bug #1 fix
        ]);
        $this->sendStatusUpdateEmail($appointment, $status);
        $this->logStatusChange($id, $appointment['status'], $status);

        $templateKey = $this->statusToTemplateKey($status);
        return $this->handleResponse(
            true,
            $this->getStatusUpdateMessage($templateKey),
            ['appointment_id' => $id, 'new_status' => $status]
        );
    }

    public function cancel()
    {
        $id     = $this->request->getPost('appointment_id');
        $reason = trim($this->request->getPost('deny_cancel_reason') ?? '');

        $validation = $this->validateCancellation($id, $reason);
        if (!$validation['valid']) {
            return $this->handleResponse(false, $validation['error']);
        }

        try {
            $appointment    = $this->apptModel->find($id);
            $originalStatus = $appointment['original_status_before_request'] ?? 'confirmed';

            $this->apptModel->update($id, [
                'status'                     => $originalStatus,
                'cancellation_denied_at'     => date('Y-m-d H:i:s'),
                'cancellation_denied_reason' => $reason,
                'updated_at'                 => date('Y-m-d H:i:s'), // Bug #1 fix — column now exists
            ]);

            $this->sendStatusUpdateEmail($appointment, AppointmentStatus::CANCELLED);
            $this->logCancellation($id, $reason);

            return $this->handleResponse(
                true,
                'Appointment cancelled successfully.',
                ['appointment_id' => $id]
            );
        } catch (\Exception $e) {
            log_message('error', '[CANCEL] #' . $id . ' - ' . $e->getMessage());
            return $this->handleResponse(false, 'Failed to cancel: ' . $e->getMessage());
        }
    }

    // ==================== PRIVATE HELPERS ====================

    protected function prepareAppointmentData(string $accountType): array
    {
        $serviceIds    = $this->request->getPost('services') ?? [];
        $serviceLevels = $this->request->getPost('levels') ?? [];
        $totalDuration = $this->calculateTotalDuration($serviceIds, $serviceLevels);
        $startDate     = $this->request->getPost('appointment_date');
        $startTime     = $this->request->getPost('appointment_time');

        if (preg_match('/^\d{2}:\d{2}$/', $startTime)) {
            $startTime .= ':00';
        }

        $timeValidation = $this->validateAppointmentTime($startTime);
        if (!$timeValidation['valid']) {
            throw new \RuntimeException($timeValidation['error']);
        }

        $dentistId = $this->request->getPost('dentist_id');

        // =========================
        // BUG #4 FIX: Use the unified hasConflict() instead of the
        // old isSlotAvailable() which was date-unaware (compared
        // time strings only, without checking the appointment_date).
        // =========================
        $startTimestamp = strtotime("{$startDate} {$startTime}");
        $endTimestamp   = $startTimestamp + ($totalDuration * 60);

        $hasConflict = $this->apptModel->hasConflict(
            (int) $dentistId,
            date('Y-m-d H:i:s', $startTimestamp),
            date('Y-m-d H:i:s', $endTimestamp)
        );

        if ($hasConflict) {
            throw new \RuntimeException(
                'Sorry, the selected time slot is no longer available or conflicts with another appointment. Please choose a different time.'
            );
        }

        $endTime = $this->calculateEndTime($startDate, $startTime, $totalDuration);

        return [
            'booked_by'                 => 'receptionist',
            'end_date'                  => $startDate,
            'end_time'                  => $endTime,
            'expected_duration_minutes' => $totalDuration,
        ];
    }

    protected function prepareRescheduleData(array $original): array
    {
        $newStartDate = $this->request->getPost('appointment_date');
        $newStartTime = $this->request->getPost('appointment_time');
        $dentistId    = $this->request->getPost('dentist_id') ?? $original['dentist_id'];
        $duration     = (int) ($original['expected_duration_minutes'] ?? 30);

        $timeValidation = $this->validateAppointmentTime($newStartTime);
        if (!$timeValidation['valid']) {
            throw new \RuntimeException($timeValidation['error']);
        }

        // BUG #4 FIX: same unified check here
        $startTimestamp = strtotime("{$newStartDate} {$newStartTime}");
        $endTimestamp   = $startTimestamp + ($duration * 60);

        $hasConflict = $this->apptModel->hasConflict(
            (int) $dentistId,
            date('Y-m-d H:i:s', $startTimestamp),
            date('Y-m-d H:i:s', $endTimestamp),
            (int) $original['id']
        );

        if ($hasConflict) {
            throw new \RuntimeException(
                'The new time slot conflicts with an existing appointment.'
            );
        }

        $newEndTime = $this->calculateEndTime($newStartDate, $newStartTime, $duration);

        return [
            'end_date' => $newStartDate,
            'end_time' => $newEndTime,
        ];
    }

    protected function calculateRequiredDuration(?array $serviceIds): int
    {
        if (empty($serviceIds)) return TimeConfig::SLOT_DURATION;

        $serviceLevels = $this->request->getGet('service_levels') ?? [];
        return $this->calculateTotalDuration(
            array_map('intval', $serviceIds),
            $serviceLevels
        );
    }

    protected function calculateEndTime(string $date, string $startTime, int $durationMinutes): string
    {
        return date(
            'H:i:s',
            strtotime("{$date} {$startTime}") + ($durationMinutes * 60)
        );
    }

    protected function calculateTotalDuration(array $serviceIds, array $serviceLevels): int
    {
        if (empty($serviceIds)) return TimeConfig::SLOT_DURATION;

        $services = $this->serviceModel
            ->select('id, estimated_duration_minutes, has_levels, duration_adjustments')
            ->whereIn('id', $serviceIds)
            ->asArray()
            ->findAll();

        $total = array_reduce($services, function ($carry, $service) use ($serviceLevels) {
            $baseDuration = (int) ($service['estimated_duration_minutes'] ?? TimeConfig::SLOT_DURATION);

            if ($service['has_levels'] && !empty($serviceLevels[$service['id']])) {
                $adjustments = json_decode($service['duration_adjustments'] ?? '{}', true);
                if (is_array($adjustments)) {
                    $level   = $serviceLevels[$service['id']];
                    $trimmed = array_combine(
                        array_map('trim', array_keys($adjustments)),
                        array_map('intval', $adjustments)
                    );
                    return $carry + $baseDuration + ($trimmed[$level] ?? 0);
                }
            }
            return $carry + $baseDuration;
        }, 0);

        return max($total, 15);
    }

    protected function formatDuration(int $minutes): string
    {
        if ($minutes < 60) return "{$minutes}m";
        $hours = intdiv($minutes, 60);
        $mins  = $minutes % 60;
        return $mins > 0 ? "{$hours}h {$mins}m" : "{$hours}h";
    }

    protected function formatSuccessMessage(array $result): string
    {
        $status = $result['status'] ?? AppointmentStatus::CONFIRMED;

        $statusLabel = ($status === AppointmentStatus::CONFIRMED)
            ? '<i class="fas fa-check-double mr-1"></i> Confirmed'
            : '<i class="fas fa-hourglass-half mr-1"></i> Pending Approval';

        $emailMsg = !empty($result['patient_email'])
            ? ' <span class="text-[10px] opacity-80">(<i class="fas fa-envelope mr-1"></i> Email sent)</span>'
            : ' <span class="text-[10px] opacity-80">(<i class="fas fa-envelope-slash mr-1"></i> No email on file)</span>';

        return "Appointment created! Queue #{$result['queue_number']} {$statusLabel}.{$emailMsg}";
    }

    protected function statusToTemplateKey(string $status): string
    {
        return match ($status) {
            AppointmentStatus::CONFIRMED              => 'appointment_confirmed',
            AppointmentStatus::REJECTED               => 'appointment_rejected',
            AppointmentStatus::CANCELLED              => 'appointment_cancelled',
            AppointmentStatus::COMPLETED              => 'appointment_completed',
            AppointmentStatus::CANCELLATION_REQUESTED => 'appointment_cancellation_requested',
            AppointmentStatus::NO_SHOW                => 'appointment_no-show',
            default                                   => 'appointment_status_updated',
        };
    }

    protected function getStatusUpdateMessage(string $templateKey): string
    {
        return match ($templateKey) {
            'appointment_confirmed'             => '<i class="fas fa-check-circle mr-1"></i> Appointment confirmed! Email sent.',
            'appointment_rejected'              => '<i class="fas fa-circle-xmark mr-1"></i> Appointment rejected. Email sent.',
            'appointment_cancelled'             => '<i class="fas fa-ban mr-1"></i> Appointment cancelled. Email sent.',
            'appointment_completed'             => '<i class="fas fa-clipboard-check mr-1"></i> Appointment marked as Completed.',
            'appointment_cancellation_approved' => '<i class="fas fa-check-circle mr-1"></i> Cancellation approved. Email sent.',
            'appointment_cancellation_denied'   => '<i class="fas fa-times-circle mr-1"></i> Cancellation denied. Email sent.',
            'appointment_no-show'               => '<i class="fas fa-user-slash mr-1"></i> Marked as No-Show. Email sent.',
            default                             => '<i class="fas fa-info-circle mr-1"></i> Status updated.',
        };
    }

    protected function formatHistoryEntry(array $entry): array
    {
        return [
            ...$entry,
            'fmt_date' => $entry['appointment_date'] ? date('M d, Y', strtotime($entry['appointment_date'])) : '',
            'fmt_time' => $entry['appointment_time'] ? date('h:i A', strtotime($entry['appointment_time'])) : '',
            'fmt_end'  => $entry['end_time']         ? date('h:i A', strtotime($entry['end_time'])) : '',
            'duration_label' => $entry['expected_duration_minutes']
                ? $this->formatDuration($entry['expected_duration_minutes'])
                : '',
        ];
    }

    protected function isValidStatusUpdate(?int $id, ?string $status): bool
    {
        return $id
            && $status
            && in_array($status, AppointmentStatus::all(), true)
            && $status !== AppointmentStatus::CANCELLED;
    }

    protected function validateCancellation(?int $id, string $reason): array
    {
        if (!$id || !is_numeric($id)) {
            return ['valid' => false, 'error' => 'Invalid appointment ID.'];
        }

        $appointment = $this->apptModel->find($id);
        if (!$appointment) {
            return ['valid' => false, 'error' => 'Appointment not found.'];
        }

        if (in_array($appointment['status'], [
            AppointmentStatus::REJECTED,
            AppointmentStatus::COMPLETED,
            AppointmentStatus::CANCELLED,
        ], true)) {
            return ['valid' => false, 'error' => 'This appointment cannot be cancelled.'];
        }

        if (empty($reason)) {
            return ['valid' => false, 'error' => 'Cancellation reason is required.'];
        }

        return ['valid' => true];
    }

    protected function requestWithMergedData(array $additionalData): \CodeIgniter\HTTP\IncomingRequest
    {
        $this->request->setGlobal('post', array_merge(
            $this->request->getPost(),
            $additionalData
        ));
        return $this->request;
    }

    protected function getAppointmentService(): AppointmentService
    {
        if ($this->appointmentService === null) {
            $this->appointmentService = new AppointmentService();
        }
        return $this->appointmentService;
    }

    protected function fetchMedicalConditions(): array
    {
        return $this->mcModel
            ->select('id, condition_key, condition_label, category, is_critical')
            ->where('is_active', 1)
            ->orderBy('category', 'ASC')
            ->asArray()
            ->findAll();
    }

    protected function groupConditions(array $medicalConditions): array
    {
        $allergies = [];
        $grouped   = [];
        foreach ($medicalConditions as $mc) {
            $category = strtolower(trim($mc['category'] ?? ''));
            if (in_array($category, ['allergy', 'allergies'], true)) {
                $allergies[] = $mc;
                continue;
            }
            $grouped[$mc['category']][] = $mc;
        }
        return [$allergies, $grouped];
    }

    protected function getActiveServices(): array
    {
        return $this->serviceModel
            ->select('id, service_name, price, estimated_duration_minutes, has_levels')
            ->where('status', 'active')
            ->asArray()
            ->findAll();
    }

    protected function getActiveDentists(): array
    {
        return $this->dentistModel
            ->select('id, first_name, last_name')
            ->where('status', 'Active')
            ->asArray()
            ->findAll();
    }

    protected function getStoreValidationRules(string $accountType): array
    {
        $baseRules = [
            'dentist_id'               => 'required|is_natural_no_zero',
            'appointment_date'         => 'required|valid_date',
            'appointment_time'         => 'required|regex_match[/^([0-1]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/]',
            'end_time'                 => 'required|regex_match[/^([0-1]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/]',
            'services'                 => 'required',
            'blood_pressure'           => 'permit_empty|regex_match[/^\d{2,3}\/\d{2,3}$/]',
            'blood_pressure_systolic'  => 'permit_empty|is_natural|greater_than[69]|less_than[201]',
            'blood_pressure_diastolic' => 'permit_empty|is_natural|greater_than[39]|less_than[131]',
        ];

        $accountRules = ($accountType === 'new') ? [
            'first_name'               => 'required',
            'last_name'                => 'required',
            'username'                 => 'required|is_unique[users.username]',
            'email'                    => 'required|valid_email|is_unique[users.email]',
            'password'                 => 'required|min_length[8]',
            'region'                   => 'required',
            'blood_pressure_systolic'  => 'required|is_natural|greater_than[69]|less_than[201]',
            'blood_pressure_diastolic' => 'required|is_natural|greater_than[39]|less_than[131]',
        ] : [
            'patient_id' => 'required|is_natural_no_zero',
        ];

        return [...$baseRules, ...$accountRules];
    }

    protected function getRescheduleRules(): array
    {
        return [
            'appointment_id'   => 'required|is_natural_no_zero',
            'dentist_id'       => 'required|is_natural_no_zero',
            'appointment_date' => 'required|valid_date',
            'appointment_time' => 'required|regex_match[/^([0-1]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/]',
            'end_time'         => 'required|regex_match[/^([0-1]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/]',
        ];
    }

    protected function sendStatusUpdateEmail(array $appointment, string $statusOrTemplate): void
    {
        $email = $this->getPatientEmail($appointment);
        if (!$email) return;

        $emailService = new \App\Services\EmailService();
        $key = str_starts_with($statusOrTemplate, 'appointment_')
            ? $statusOrTemplate
            : $this->statusToTemplateKey($statusOrTemplate);

        // ✅ Extract reason if applicable (for extra safety)
        $rejectionReason = null;
        if ($key === 'appointment_rejected' && !empty($appointment['remarks'])) {
            $rejectionReason = $appointment['remarks'];
        }
        if ($key === 'appointment_cancellation_denied' && !empty($appointment['cancellation_denied_reason'])) {
            $rejectionReason = $appointment['cancellation_denied_reason'];
        }

        $emailService->queueEmail(
            $email,
            $this->getPatientFullName($appointment),
            $key,
            $this->prepareEmailPayload($appointment, $rejectionReason) // ✅ Now compatible
        );
    }

    protected function getPatientEmail(array $appointment): ?string
    {
        $patient = $this->patientModel->asArray()->find($appointment['patient_id']);
        if (!empty($patient['email']) && is_string($patient['email']) && filter_var($patient['email'], FILTER_VALIDATE_EMAIL)) {
            return (string) $patient['email'];
        }
        if (!empty($patient['user_id'])) {
            $user = (new \App\Models\UserModel())->asArray()->find($patient['user_id']);
            if ($user && isset($user['email']) && is_string($user['email']) && filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
                return $user['email'];
            }
        }
        return null;
    }

    protected function getPatientFullName(array $appointment): string
    {
        $patient = $this->patientModel->asArray()->find($appointment['patient_id']);
        return trim("{$patient['first_name']} {$patient['last_name']}");
    }

    protected function prepareEmailPayload(array $appointment, ?string $rejectionReason = null): array
    {
        $patientId = $appointment['patient_id'] ?? null;
        $dentistId = $appointment['dentist_id'] ?? null;

        $patient = $patientId
            ? $this->patientModel->asArray()
            ->select('id, patient_code, first_name, middle_name, last_name, name_suffix, email, user_id')
            ->find($patientId)
            : null;

        $dentist = $dentistId ? $this->dentistModel->asArray()->find($dentistId) : null;

        return [
            'patient_name'     => $appointment['patient_name'] ?? ($patient ? trim($patient['first_name'] . ' ' . $patient['last_name']) : 'Valued Patient'),
            'queue_number'     => $appointment['queue_number'] ?? 'N/A',
            'appointment_date' => isset($appointment['appointment_date']) ? date('F d, Y', strtotime($appointment['appointment_date'])) : 'N/A',
            'appointment_time' => isset($appointment['appointment_time']) ? date('h:i A', strtotime($appointment['appointment_time'])) : 'N/A',
            'dentist_name'     => $dentist ? 'Dr. ' . ($dentist['last_name'] ?? '') : 'TBD',
            'appointment_id'   => $appointment['id'] ?? $appointment['appointment_id'] ?? null,
            'patient_code'     => $patient['patient_code'] ?? 'N/A',

            // ✅ Rejection reason with proper fallback chain
            'rejection_reason' => $rejectionReason
                ?? $appointment['remarks']              // From fresh DB fetch
                ?? $appointment['cancellation_denied_reason']
                ?? 'The requested time slot is no longer available.',
        ];
    }

    public function reject()
    {
        $id     = $this->request->getPost('appointment_id');
        $reason = trim($this->request->getPost('reject_reason') ?? '');

        if (!$id || !is_numeric($id)) {
            return $this->handleResponse(false, 'Invalid appointment ID.');
        }

        $appointment = $this->apptModel->find($id);
        if (!$appointment) {
            return $this->handleResponse(false, 'Appointment not found.');
        }

        $currentStatus = $appointment['status'];
        $bookedBy      = $appointment['booked_by'] ?? 'receptionist';

        if (!AppointmentStatus::is($currentStatus, AppointmentStatus::PENDING)) {
            return $this->handleResponse(false, 'Cannot reject: Appointment status is "' . $currentStatus . '" (must be Pending).');
        }

        if (!AppointmentStatus::is($bookedBy, 'patient')) {
            return $this->handleResponse(false, 'Cannot reject: Appointment was booked by "' . $bookedBy . '" (only patient bookings can be rejected).');
        }

        if (empty($reason)) {
            return $this->handleResponse(false, 'Rejection reason is required.');
        }

        try {
            // 1. Update the appointment with rejection reason
            $this->apptModel->update($id, [
                'status'     => AppointmentStatus::REJECTED,
                'remarks'    => $reason,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // ✅ 2. RE-FETCH the appointment to get the updated remarks
            $updatedAppointment = $this->apptModel->asArray()->find($id);

            // ✅ 3. Pass the FRESH data to email (now has remarks)
            $this->sendStatusUpdateEmail($updatedAppointment, 'appointment_rejected');

            $this->logRejection($id, $reason);

            return $this->handleResponse(
                true,
                'Appointment request rejected. Rejection email sent to patient.',
                ['appointment_id' => $id]
            );
        } catch (\Exception $e) {
            log_message('error', '[REJECT] #' . $id . ' - ' . $e->getMessage());
            return $this->handleResponse(false, 'Failed to reject: ' . $e->getMessage());
        }
    }

    // ==================== CANCELLATION REQUEST HANDLING ====================

    public function handleCancellationRequest()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $appointmentId = $this->request->getJSON()->appointment_id ?? null;
        $action        = $this->request->getJSON()->action ?? null;

        if (!$appointmentId || !in_array($action, ['approve', 'deny'], true)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid parameters.']);
        }

        $appointment = $this->apptModel->find($appointmentId);
        if (!$appointment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Appointment not found.']);
        }

        $acrModel = new \App\Models\AppointmentCancelRequestModel();

        try {
            $this->db->transStart();

            if ($action === 'approve') {
                $acrModel->where('appointment_id', $appointmentId)
                    ->where('status', 'pending')
                    ->set([
                        'status'    => 'approved',
                        'action_by' => session()->get('user_id') ?? 1,
                        'action_at' => date('Y-m-d H:i:s'),
                    ])->update();

                $this->apptModel->update($appointmentId, [
                    'status'        => AppointmentStatus::CANCELLED,
                    'cancelled_by'  => 'STAFF',
                    'cancel_reason' => 'Cancellation request approved by staff',
                    'cancelled_at'  => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'), // Bug #1 fix
                ]);

                $message = '<i class="fas fa-check-circle mr-1"></i> Cancellation request approved.';
                $this->sendStatusUpdateEmail($appointment, 'appointment_cancellation_approved');
            } else {
                $acrModel->where('appointment_id', $appointmentId)
                    ->where('status', 'pending')
                    ->set([
                        'status'        => 'denied',
                        'denial_reason' => 'Denied by receptionist',
                        'action_by'     => session()->get('user_id') ?? 1,
                        'action_at'     => date('Y-m-d H:i:s'),
                    ])->update();

                $originalStatus = $appointment['original_status_before_request'] ?? AppointmentStatus::CONFIRMED;
                $this->apptModel->update($appointmentId, [
                    'status'     => $originalStatus,
                    'updated_at' => date('Y-m-d H:i:s'), // Bug #1 fix
                ]);

                $message = '<i class="fas fa-times-circle mr-1"></i> Cancellation request denied.';
                $this->sendStatusUpdateEmail($appointment, 'appointment_cancellation_denied');
            }

            $this->db->transComplete();
            return $this->response->setJSON(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function denyCancellation()
    {
        $id     = $this->request->getPost('appointment_id');
        $reason = trim($this->request->getPost('deny_cancel_reason') ?? '');

        $appointment = $this->apptModel->find($id);
        if (!$appointment) return $this->handleResponse(false, 'Appointment not found.');
        if (empty($reason))  return $this->handleResponse(false, 'Reason for denial is required.');

        $acrModel = new \App\Models\AppointmentCancelRequestModel();

        try {
            $this->db->transStart();

            $acrModel->where('appointment_id', $id)
                ->where('status', 'pending')
                ->set([
                    'status'        => 'denied',
                    'denial_reason' => $reason,
                    'action_by'     => session()->get('user_id') ?? 1,
                    'action_at'     => date('Y-m-d H:i:s'),
                ])->update();

            $originalStatus = !empty($appointment['original_status_before_request'])
                ? $appointment['original_status_before_request']
                : AppointmentStatus::CONFIRMED;

            $this->apptModel->update($id, [
                'status'                     => $originalStatus,
                'cancellation_denied_at'     => date('Y-m-d H:i:s'),
                'cancellation_denied_reason' => $reason,
                'updated_at'                 => date('Y-m-d H:i:s'), // Bug #1 fix
            ]);

            $this->db->transComplete();
            $this->sendCancellationDeniedEmail($appointment, $reason);

            return $this->handleResponse(true, 'Cancellation denied and patient notified.');
        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->handleResponse(false, 'Error: ' . $e->getMessage());
        }
    }

    public function markNoShow(int $id)
    {
        $appointment = $this->apptModel->find($id);
        if (!$appointment) return $this->handleResponse(false, 'Appointment not found.');

        try {
            $this->apptModel->update($id, [
                'status'     => AppointmentStatus::NO_SHOW,
                'updated_at' => date('Y-m-d H:i:s'), // Bug #1 fix
            ]);

            $this->sendStatusUpdateEmail($appointment, 'appointment_no-show');
            return $this->handleResponse(true, 'Appointment marked as No-Show and patient notified.');
        } catch (\Exception $e) {
            return $this->handleResponse(false, $e->getMessage());
        }
    }

    // ==================== PRIVATE HELPERS ====================

    protected function sendCancellationDeniedEmail(array $appointment, string $reason): void
    {
        $email = $this->getPatientEmail($appointment);
        if (!$email) return;

        // ✅ Re-fetch appointment to ensure we have queue_number
        $appointmentData = $this->apptModel->asArray()
            ->select('id, patient_id, dentist_id, queue_number, appointment_date, appointment_time')
            ->find($appointment['id']);

        // ✅ Fetch patient data
        $patient = $this->patientModel->asArray()
            ->select('id, patient_code, first_name, last_name')
            ->find($appointmentData['patient_id']);

        $emailService = new \App\Services\EmailService();
        $emailService->queueEmail(
            $email,
            $this->getPatientFullName($appointment),
            'appointment_cancellation_denied',
            [
                'patient_name'     => $this->getPatientFullName($appointment),
                'queue_number'     => $appointmentData['queue_number'] ?? 'N/A', // ✅ From fresh fetch
                'appointment_date' => date('F d, Y', strtotime($appointmentData['appointment_date'])),
                'appointment_time' => date('h:i A', strtotime($appointmentData['appointment_time'])),
                'dentist_name'     => $this->getDentistName($appointmentData['dentist_id'] ?? null),
                'denial_reason'    => $reason,
                'appointment_id'   => $appointment['id'],
                'patient_code'     => $patient['patient_code'] ?? 'N/A',
            ]
        );
    }

    protected function getDentistName(?int $dentistId): string
    {
        if (!$dentistId) return 'TBD';
        $dentist = $this->dentistModel->asArray()->find($dentistId);
        return $dentist ? 'Dr. ' . trim(($dentist['first_name'] ?? '') . ' ' . ($dentist['last_name'] ?? '')) : 'TBD';
    }

    protected function logRejection(int $id, string $reason): void
    {
        $userId = session()->get('user_id') ?? session()->get('id') ?? 'RECEPTIONIST';
        log_message('info', "Appointment #{$id} REJECTED by RECEPTIONIST (User: {$userId}) - Reason: {$reason}");
    }

    protected function logStatusChange(int $id, string $oldStatus, string $newStatus): void
    {
        $userId = session()->get('user_id') ?? session()->get('id') ?? 'SYSTEM';
        log_message('info', "Appointment #{$id}: {$oldStatus} → {$newStatus} by user {$userId}");
    }

    protected function logCancellation(int $id, string $reason): void
    {
        $userId = session()->get('user_id') ?? session()->get('id') ?? 'RECEPTIONIST';
        log_message('info', "Appointment #{$id} CANCELLED by RECEPTIONIST (User: {$userId}) - Reason: {$reason}");
    }
}
