<?php
// app/Constants/TimeConfig.php
namespace App\Constants;

class TimeConfig
{
    public const CLINIC_START = '09:00:00';
    public const CLINIC_END = '17:00:00';
    public const BOOKING_CUTOFF = '16:00:00'; // No new appointments start at/after this time
    public const SLOT_DURATION = 30; // minutes
    public const SLOT_BREAK = 5; // minutes between slots
    
    public static function getCutoffTimestamp(): int
    {
        return strtotime(self::BOOKING_CUTOFF);
    }
    
    public static function isAfterCutoff(string $time): bool
    {
        return strtotime($time) >= self::getCutoffTimestamp();
    }
}