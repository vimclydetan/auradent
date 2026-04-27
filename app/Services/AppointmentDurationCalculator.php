<?php

namespace App\Services;

use App\Models\ServiceModel;

class AppointmentDurationCalculator
{
    /**
     * Calculate total duration for an appointment based on services + levels
     * Returns PURE duration (without buffer) - caller decides on buffer
     */
    private static function parseAdjustments($jsonString): array
{
    $adjustments = json_decode($jsonString ?? '{}', true);
    if (!is_array($adjustments)) return [];
    
    // ✅ TRIM ALL KEYS to handle "Simple " vs "Simple"
    $trimmed = [];
    foreach ($adjustments as $key => $value) {
        $cleanKey = trim($key);
        $trimmed[$cleanKey] = (int)$value;
    }
    return $trimmed;
}

// ✅ UPDATE calculate() method
public static function calculate(array $serviceIds, array $serviceLevels = []): int
{
    if (empty($serviceIds)) return 30;

    $serviceModel = new ServiceModel();
    $services = $serviceModel->whereIn('id', $serviceIds)->findAll();

    $totalMinutes = 0;

    foreach ($services as $service) {
        $baseDuration = (int)($service['estimated_duration_minutes'] ?? 30);

        if (!empty($service['has_levels']) && !empty($serviceLevels[$service['id']])) {
            // ✅ USE HELPER TO PARSE
            $adjustments = self::parseAdjustments($service['duration_adjustments']);
            $level = $serviceLevels[$service['id']] ?? 'Standard';

            if (isset($adjustments[$level])) {
                $baseDuration += $adjustments[$level];
            }
        }

        $totalMinutes += max(15, $baseDuration);
    }

    return $totalMinutes;
}

    /**
     * Calculate duration for existing appointment from DB
     */
    public static function calculateFromAppointment(int $appointmentId): int
    {
        $apptSvcModel = new \App\Models\AppointmentServiceModel();
        $services = $apptSvcModel
            ->select('
                appointment_services.service_id, 
                appointment_services.service_level, 
                services.estimated_duration_minutes, 
                services.has_levels,
                services.duration_adjustments,
                services.level_duration_adjustment
            ')
            ->join('services', 'services.id = appointment_services.service_id')
            ->where('appointment_services.appointment_id', $appointmentId)
            ->findAll();

        if (empty($services)) return 30;

        $totalMinutes = 0;

        foreach ($services as $svc) {
            $base = (int)($svc['estimated_duration_minutes'] ?? 30);

            // ✅ Apply adjustments only if has_levels
            if (!empty($svc['has_levels'])) {
                $jsonField = $svc['duration_adjustments'] ?? $svc['level_duration_adjustment'] ?? null;

                if (!empty($jsonField)) {
                    $adjustments = json_decode($jsonField, true);
                    if (is_array($adjustments)) {
                        $level = $svc['service_level'] ?? 'Standard';

                        // ✅ Trim keys for safety
                        $trimmed = [];
                        foreach ($adjustments as $k => $v) {
                            $trimmed[trim($k)] = (int)$v;
                        }

                        if (isset($trimmed[$level])) {
                            $base += $trimmed[$level];
                        }
                    }
                }
            }

            $totalMinutes += max(15, $base);
        }

        return $totalMinutes; // ✅ Pure duration
    }
}
