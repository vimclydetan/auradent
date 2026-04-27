<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Constants\AppointmentStatus;

class AppointmentModel extends BaseModel
{
    protected $table      = 'appointments';
    protected $primaryKey = 'id';

    // =========================================================
    // BUG #5 FIX: Added missing fields that were being used in
    // update() calls across the codebase but silently ignored
    // by CodeIgniter because they weren't listed here.
    // Added: updated_at, original_status_before_request
    // =========================================================
    protected $allowedFields = [
        'patient_id',
        'dentist_id',
        'service_id',
        'booked_by',
        'appointment_date',
        'end_date',
        'appointment_time',
        'end_time',
        'status',
        'remarks',
        'queue_number',
        'queue_date',
        'arrival_time',
        'email_confirmation_sent',
        'email_reminder_sent',
        'expected_duration_minutes',
        'buffer_minutes',
        'actual_start_time',
        'delay_minutes',

        // cancellation-related
        'cancel_requested_at',
        'cancel_request_reason',
        'cancel_approved_by',
        'cancelled_by',
        'cancel_reason',
        'cancelled_at',
        'cancellation_denied_at',
        'cancellation_denied_reason',
        'cancel_attempts',

        // BUG #5 FIX — these two were missing:
        'updated_at',
        'original_status_before_request',
    ];

    protected $returnType    = 'array';
    protected $useSoftDeletes = false;

    // Let CodeIgniter manage timestamps automatically now that
    // updated_at exists in the DB (after running the migration).
    protected $useTimestamps = false; // keep manual control for safety

    /**
     * @param int|string|null $id
     * @return array|null
     */
    public function find($id = null): array|null
    {
        $result = parent::find($id);
        return is_array($result) ? $result : null;
    }

    // =========================================================
    // QUERIES
    // =========================================================

    public function getAppointments($filters = [])
    {
        $builder = $this->buildBaseQuery();

        if (!empty($filters['status'])) {
            $builder->where('appointments.status', $filters['status']);
        } else {
            $builder->whereIn('appointments.status', [
                AppointmentStatus::PENDING,
                AppointmentStatus::CONFIRMED,
                AppointmentStatus::COMPLETED
            ]);
        }

        return $builder
            ->orderBy('appointments.appointment_date', 'DESC')
            ->orderBy('appointments.appointment_time', 'ASC')
            ->findAll();
    }

    public function getTodaySchedule()
    {
        return $this->buildBaseQuery()
            ->where('appointments.appointment_date', date('Y-m-d'))
            ->whereIn('appointments.status', [
                AppointmentStatus::PENDING,
                AppointmentStatus::CONFIRMED
            ])
            ->orderBy('appointments.appointment_time', 'ASC')
            ->findAll();
    }

    public function getAppointmentByID(int $id)
    {
        return $this->buildBaseQuery()
            ->where('appointments.id', $id)
            ->first();
    }

    protected function buildBaseQuery()
    {
        return $this->select('
            appointments.*,
            patients.patient_code,
            CONCAT(patients.first_name, " ", patients.last_name) as patient_name,
            patients.primary_mobile as patient_phone,
            CONCAT("Dr. ", dentists.first_name, " ", dentists.last_name) as dentist_name,
            GROUP_CONCAT(DISTINCT services.service_name SEPARATOR ", ") as service_name,
            GROUP_CONCAT(DISTINCT appointment_services.service_level SEPARATOR ", ") as service_level,
            latest_acr.reason as cancel_request_reason,
            latest_acr.denial_reason as cancellation_denied_reason,
            latest_acr.status as cancel_request_status
        ')
            ->join('patients', 'patients.id = appointments.patient_id')
            ->join('dentists', 'dentists.id = appointments.dentist_id', 'left')
            ->join('appointment_services', 'appointment_services.appointment_id = appointments.id', 'left')
            ->join('services', 'services.id = appointment_services.service_id', 'left')
            ->join(
                '(SELECT * FROM appointment_cancel_requests WHERE id IN (SELECT MAX(id) FROM appointment_cancel_requests GROUP BY appointment_id)) latest_acr',
                'latest_acr.appointment_id = appointments.id',
                'left'
            )
            ->groupBy('appointments.id');
    }

    // =========================================================
    // BUG #4 FIX: Unified, single conflict-check method.
    //
    // Previously there were TWO separate implementations:
    //   1. isDentistBusy()   — date-only comparison, no time granularity
    //   2. isSlotAvailable() — time-only comparison, date-unaware
    //
    // Both were incomplete. A booking spanning two dates, or two
    // appointments on the same date with different times, could
    // slip through depending on which check ran first.
    //
    // This single method does a proper datetime overlap check:
    //   Overlap exists when: StartA < EndB AND EndA > StartB
    // using BOTH date AND time combined, with an optional exclude
    // ID for reschedule scenarios.
    // =========================================================
    public function hasConflict(
        int    $dentistId,
        string $startDatetime,  // 'Y-m-d H:i:s'
        string $endDatetime,    // 'Y-m-d H:i:s'
        ?int   $excludeId = null
    ): bool {
        $builder = $this->db->table($this->table)
            ->where('dentist_id', $dentistId)
            ->whereIn('status', [
                AppointmentStatus::PENDING,
                AppointmentStatus::CONFIRMED,
            ]);

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        // Overlap condition: existing.start < new.end AND existing.end > new.start
        // Using CONCAT of date+time columns for a proper datetime comparison.
        $builder->where(
            "CONCAT(appointment_date, ' ', appointment_time) <",
            $endDatetime
        );
        $builder->where(
            "CONCAT(end_date, ' ', end_time) >",
            $startDatetime
        );

        return $builder->countAllResults() > 0;
    }

    /**
     * @deprecated Use hasConflict() instead — kept temporarily for
     *             any callers not yet migrated.
     */
    public function isDentistBusy($dentistId, $start, $end, $excludeId = null): bool
    {
        return $this->hasConflict(
            (int) $dentistId,
            $start,
            $end,
            $excludeId ? (int) $excludeId : null
        );
    }

    // =========================================================
    // FILTERED LIST & COUNTS
    // =========================================================

    public function getFilteredAppointments(string $tab = 'today'): array
    {
        $builder = $this->buildBaseQuery();
        $today    = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        $visibleStatuses = [
            AppointmentStatus::PENDING,
            AppointmentStatus::CONFIRMED,
            AppointmentStatus::COMPLETED,
            'cancellation_requested',
            'no-show',
        ];

        switch ($tab) {
            case 'today':
                $builder->where('appointments.appointment_date', $today)
                    ->whereIn('appointments.status', $visibleStatuses);
                break;
            case 'tomorrow':
                $builder->where('appointments.appointment_date', $tomorrow)
                    ->whereIn('appointments.status', $visibleStatuses);
                break;
            case 'upcoming':
                $builder->where('appointments.appointment_date >', $today)
                    ->whereIn('appointments.status', $visibleStatuses);
                break;
            case 'all':
                $builder->whereIn('appointments.status', [
                    ...$visibleStatuses,
                    AppointmentStatus::CANCELLED,
                    AppointmentStatus::REJECTED,
                ]);
                break;
        }

        $results = $builder
            ->orderBy('appointments.appointment_date', 'ASC')
            ->orderBy('appointments.appointment_time', 'ASC')
            ->findAll();

        return $this->formatMany($results);
    }

    public function getCounts(): array
    {
        $today    = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        $activeStatus = [
            AppointmentStatus::PENDING,
            AppointmentStatus::CONFIRMED,
            AppointmentStatus::COMPLETED,
            'cancellation_requested',
            'no-show',
        ];

        return [
            'today' => $this->builder()
                ->where('appointment_date', $today)
                ->whereIn('status', $activeStatus)
                ->countAllResults(),
            'tomorrow' => $this->builder()
                ->where('appointment_date', $tomorrow)
                ->whereIn('status', $activeStatus)
                ->countAllResults(),
            'upcoming' => $this->builder()
                ->where('appointment_date >', $today)
                ->whereIn('status', $activeStatus)
                ->countAllResults(),
        ];
    }

    protected function formatMany(array $appointments): array
    {
        foreach ($appointments as &$a) {
            if (!empty($a['appointment_date'])) {
                $a['fmt_date']     = date('M d, Y', strtotime($a['appointment_date']));
                $a['fmt_time']     = date('h:i A', strtotime($a['appointment_time']));
                $a['fmt_end']      = date('h:i A', strtotime($a['end_time']));
                $a['fmt_end_date'] = date('M d, Y', strtotime($a['end_date']));
            } else {
                $a['fmt_date'] = $a['fmt_time'] = $a['fmt_end'] = $a['fmt_end_date'] = 'N/A';
            }
        }
        return $appointments;
    }

    // =========================================================
    // SLOT HELPERS
    // =========================================================

    public function getBookedSlots(int $dentistId, string $date): array
    {
        return $this->db->table($this->table)
            ->select('appointment_time, end_time, status')
            ->where('dentist_id', $dentistId)
            ->where('appointment_date', $date)
            ->whereIn('status', [
                AppointmentStatus::PENDING,
                AppointmentStatus::CONFIRMED,
            ])
            ->get()
            ->getResultArray();
    }

    public function getAvailableSlots(
        int    $dentistId,
        string $date,
        int    $requiredDuration,
        string $clinicOpen  = '09:00',
        string $clinicClose = '18:00'
    ): array {
        $slots    = [];
        $interval = 30 * 60;

        $normalizedDate = date('Y-m-d', strtotime($date));
        $isToday        = ($normalizedDate === date('Y-m-d'));
        $bufferMinutes  = (int) env('APPOINTMENT_SLOT_BUFFER_MINUTES', 5);
        $currentTime    = $isToday ? date('H:i', strtotime("+{$bufferMinutes} minutes")) : null;

        $current = strtotime("{$normalizedDate} {$clinicOpen}");
        $close   = strtotime("{$normalizedDate} {$clinicClose}");

        $existing = $this->select('appointment_time, end_time')
            ->where('dentist_id', $dentistId)
            ->where('appointment_date', $normalizedDate)
            ->whereIn('status', [AppointmentStatus::PENDING, AppointmentStatus::CONFIRMED])
            ->findAll();

        while ($current + ($requiredDuration * 60) <= $close) {
            $slotStart   = date('H:i', $current);
            $slotEnd     = $current + ($requiredDuration * 60);
            $isPast      = $isToday && ($slotStart < $currentTime);
            $isAvailable = !$isPast;

            if (!$isPast) {
                foreach ($existing as $appt) {
                    $existStart = strtotime("{$normalizedDate} {$appt['appointment_time']}");
                    $existEnd   = strtotime("{$normalizedDate} {$appt['end_time']}");

                    if ($current < $existEnd && $slotEnd > $existStart) {
                        $isAvailable = false;
                        break;
                    }
                }
            }

            $slots[] = [
                'start'            => date('H:i', $current),
                'start_db'         => date('H:i:00', $current),
                'end_display'      => date('H:i', $current + $interval),
                'end_actual'       => date('H:i', $slotEnd),
                'end_actual_db'    => date('H:i:00', $slotEnd),
                'label'            => date('H:i', $current) . ' - ' . date('H:i', $current + $interval),
                'available'        => $isAvailable,
                'duration_minutes' => $requiredDuration,
                'reason'           => $isPast ? 'past' : ($isAvailable ? 'available' : 'booked'),
                'disabled'         => !$isAvailable,
            ];

            $current += $interval;
        }

        return $slots;
    }

    // =========================================================
    // PATIENT HISTORY
    // =========================================================

    public function getPatientHistory(int $patientId): array
    {
        return $this->select('
            appointments.id,
            appointments.appointment_date,
            appointments.end_date,
            appointments.appointment_time,
            appointments.end_time,
            appointments.status,
            appointments.queue_number,
            appointments.remarks,
            appointments.expected_duration_minutes,
            dentists.last_name as dentist_last,
            GROUP_CONCAT(services.service_name ORDER BY services.service_name SEPARATOR ", ") as services_rendered
        ')
            ->join('dentists', 'dentists.id = appointments.dentist_id', 'left')
            ->join('appointment_services', 'appointment_services.appointment_id = appointments.id', 'left')
            ->join('services', 'services.id = appointment_services.service_id', 'left')
            ->where('appointments.patient_id', $patientId)
            ->groupBy('appointments.id')
            ->orderBy('appointments.appointment_date', 'DESC')
            ->orderBy('appointments.appointment_time', 'DESC')
            ->findAll();
    }

    public function getDentistTodaySchedule(int $dentistId, string $date): array
    {
        return $this->buildBaseQuery()
            ->where('appointments.dentist_id', $dentistId)
            ->where('appointments.appointment_date', $date)
            ->orderBy('appointments.appointment_time', 'ASC')
            ->findAll();
    }

    public function cancelAppointment(int $appointmentId, string $reason = ''): bool
    {
        $db = \Config\Database::connect();
        try {
            $db->transBegin();
            $appt = $this->find($appointmentId);
            if (!$appt) return false;

            $this->update($appointmentId, [
                'status'       => AppointmentStatus::CANCELLED,
                'cancelled_at' => date('Y-m-d H:i:s'),
                'cancel_reason' => $reason,
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);

            $db->transCommit();
            return true;
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', "[cancelAppointment] {$e->getMessage()}");
            return false;
        }
    }
}
