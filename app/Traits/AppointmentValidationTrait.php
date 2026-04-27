<?php
// app/Traits/AppointmentValidationTrait.php
namespace App\Traits;

use App\Constants\TimeConfig;
use App\Models\AppointmentModel;

trait AppointmentValidationTrait
{
    /**
     * Validate appointment time against business rules
     */
    protected function validateAppointmentTime(string $startTime): array
    {
        if (TimeConfig::isAfterCutoff($startTime)) {
            return [
                'valid' => false,
                'error' => 'Appointments cannot start at 4:00 PM or later. Please select an earlier time.'
            ];
        }
        return ['valid' => true];
    }

    /**
     * Check if a time slot is still available (prevents double-booking)
     */
    protected function isSlotAvailable(
        AppointmentModel $model,
        int $dentistId,
        string $date,
        string $startTime,
        int $duration
    ): array {
        $slots = $model->getAvailableSlots(
            $dentistId,
            $date,
            $duration,
            TimeConfig::CLINIC_START,
            TimeConfig::CLINIC_END
        );

        $targetTime = date('H:i:s', strtotime($startTime));
        $targetTimeShort = date('H:i', strtotime($startTime));

        foreach ($slots as $slot) {
            if (in_array($slot['start'], [$targetTime, $targetTimeShort], true)) {
                return [
                    'available' => (bool) $slot['available'],
                    'slot' => $slot
                ];
            }
        }

        // Slot not found in available list = likely booked
        return ['available' => false, 'slot' => null];
    }

    /**
     * Generic response handler for AJAX vs normal requests
     */
    protected function handleResponse(
        bool $success,
        string $message,
        array $data = [],
        string $redirectUrl = null
    ) {
        $isAjax = $this->request->isAJAX() 
            || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';

        if ($isAjax) {
            return $this->response->setJSON([
                'success' => $success,
                'message' => $message,
                'data' => $data
            ]);
        }

        if ($success) {
            return redirect()
                ->to($redirectUrl ?? base_url('receptionist/appointments'))
                ->with('success', $message);
        }

        return redirect()->back()->withInput()->with('error', $message);
    }
}