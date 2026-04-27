<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * RebookingFeeModel
 *
 * Handles the appointment_rebooking_fees table.
 * A fee row is created every time a receptionist cancels or marks
 * no-show — either with the fee imposed OR waived.
 */
class RebookingFeeModel extends Model
{
    protected $table         = 'appointment_rebooking_fees';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false;   // we manage created_at manually
    protected $returnType    = 'array';

    protected $allowedFields = [
        'appointment_id',
        'patient_id',
        'trigger_status',
        'fee_amount',
        'day_type',
        'is_waived',
        'waived_reason',
        'imposed_by',
        'paid_at',
        'created_at',
    ];

    // ----------------------------------------------------------------
    // Fee calculation helpers
    // ----------------------------------------------------------------

    /**
     * Returns the correct fee amount based on the appointment date.
     * 300 on weekdays, 500 on weekends (Sat/Sun).
     */
    public static function calculateFee(string $appointmentDate): array
    {
        $dow     = (int) date('N', strtotime($appointmentDate)); // 1=Mon … 7=Sun
        $weekend = ($dow >= 6);

        return [
            'amount'   => $weekend ? 500.00 : 300.00,
            'day_type' => $weekend ? 'weekend' : 'weekday',
        ];
    }

    // ----------------------------------------------------------------
    // Queries
    // ----------------------------------------------------------------

    /**
     * Get all unpaid (non-waived) fees for a patient.
     */
    public function getPendingFees(int $patientId): array
    {
        return $this->where('patient_id', $patientId)
                    ->where('is_waived', 0)
                    ->where('paid_at IS NULL')
                    ->asArray()
                    ->findAll();
    }

    /**
     * Get the most recent unpaid fee for a patient
     * (used to pre-fill the payment amount at check-in).
     */
    public function getLatestPendingFee(int $patientId): ?array
    {
        return $this->where('patient_id', $patientId)
                    ->where('is_waived', 0)
                    ->where('paid_at IS NULL')
                    ->orderBy('created_at', 'DESC')
                    ->asArray()
                    ->first();
    }

    /**
     * Mark all unpaid fees for a patient as paid now,
     * then record a treatment_records row for each one.
     */
    public function markAllPaid(int $patientId, int $visitId, int $dentistId): void
    {
        $unpaid = $this->getPendingFees($patientId);
        if (empty($unpaid)) {
            return;
        }

        $db          = \Config\Database::connect();
        $now         = date('Y-m-d H:i:s');
        $serviceId   = 999; // sentinel "Rebooking Fee" service

        foreach ($unpaid as $fee) {
            // 1. Mark the fee as paid
            $this->update($fee['id'], ['paid_at' => $now]);

            // 2. Insert a treatment_records row of type 'penalty'
            $db->table('treatment_records')->insert([
                'visit_id'         => $visitId,
                'transaction_type' => 'penalty',
                'patient_id'       => $patientId,
                'dentist_id'       => $dentistId,
                'service_id'       => $serviceId,
                'amount'           => $fee['fee_amount'],
                'description'      => sprintf(
                    'Rebooking fee (%s) — ref appointment #%d',
                    strtoupper($fee['trigger_status']),
                    $fee['appointment_id']
                ),
                'treatment_date'   => $now,
                'created_at'       => $now,
            ]);
        }

        // 3. Clear the pending flag on the patient
        $db->table('patients')
           ->where('id', $patientId)
           ->update(['has_pending_rebooking_fee' => 0]);
    }
}