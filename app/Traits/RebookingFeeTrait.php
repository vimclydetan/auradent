<?php

namespace App\Traits;

use App\Models\RebookingFeeModel;

/**
 * RebookingFeeTrait
 *
 * Mix this into AppointmentController.
 * Provides the two methods that handle fee imposition after a
 * cancel or no-show action, plus the helper that creates the
 * treatment_records entry when the patient returns and pays.
 *
 * Usage inside AppointmentController:
 *   use \App\Traits\RebookingFeeTrait;
 */
trait RebookingFeeTrait
{
    /**
     * Called right after an appointment is cancelled or marked no-show.
     *
     * $imposeFee = true  → create a fee row, flag the patient
     * $imposeFee = false → create a waived row (for the audit log)
     *
     * @param array  $appointment  Full appointment row from DB
     * @param string $triggerStatus 'cancelled' | 'no-show'
     * @param bool   $imposeFee
     * @param string $waivedReason  Only used when $imposeFee === false
     */
    protected function handleRebookingFee(
        array $appointment,
        string $triggerStatus,
        bool $imposeFee,
        string $waivedReason = ''
    ): void {
        $feeModel  = new RebookingFeeModel();
        $patientId = (int) $appointment['patient_id'];
        $apptDate  = $appointment['appointment_date'];
        $userId    = session()->get('user_id') ?? session()->get('id') ?? null;

        ['amount' => $amount, 'day_type' => $dayType] =
            RebookingFeeModel::calculateFee($apptDate);

        $feeModel->insert([
            'appointment_id' => (int) $appointment['id'],
            'patient_id'     => $patientId,
            'trigger_status' => $triggerStatus,
            'fee_amount'     => $imposeFee ? $amount : 0.00,
            'day_type'       => $dayType,
            'is_waived'      => $imposeFee ? 0 : 1,
            'waived_reason'  => $imposeFee ? null : $waivedReason,
            'imposed_by'     => $userId,
            'paid_at'        => null,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        if ($imposeFee) {
            // Flag the patient so the system knows to collect on next visit
            \Config\Database::connect()
                ->table('patients')
                ->where('id', $patientId)
                ->update(['has_pending_rebooking_fee' => 1]);
        }

        log_message('info', sprintf(
            '[RebookingFee] Patient #%d | Appointment #%d | Trigger: %s | Imposed: %s | Amount: %.2f | DayType: %s',
            $patientId,
            $appointment['id'],
            $triggerStatus,
            $imposeFee ? 'YES' : 'NO (waived)',
            $imposeFee ? $amount : 0,
            $dayType
        ));
    }

    /**
     * Called when checking how much a patient owes on their next visit.
     * Returns null if no pending fee exists.
     */
    protected function getPendingRebookingFee(int $patientId): ?array
    {
        return (new RebookingFeeModel())->getLatestPendingFee($patientId);
    }

    /**
     * Returns fee preview data (amount + day type) for a given
     * appointment date — used by the cancel/no-show modals via AJAX.
     */
    public function getRebookingFeePreview()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $date = $this->request->getGet('date');
        if (!$date) {
            return $this->response->setJSON(['success' => false, 'error' => 'Date required']);
        }

        ['amount' => $amount, 'day_type' => $dayType] =
            RebookingFeeModel::calculateFee($date);

        return $this->response->setJSON([
            'success'  => true,
            'amount'   => $amount,
            'day_type' => $dayType,
            'label'    => '₱' . number_format($amount, 2) . ' (' . ucfirst($dayType) . ' rate)',
        ]);
    }
}