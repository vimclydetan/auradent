<?php
namespace Config;

class AppointmentConfig extends \CodeIgniter\Config\BaseConfig
{
    public const STATUS_PENDING   = 'Pending';
    public const STATUS_CONFIRMED = 'Confirmed';
    public const STATUS_COMPLETED = 'Completed';
    public const STATUS_CANCELLED = 'Cancelled';
    
    public const ACCOUNT_EXISTING = 'existing';
    public const ACCOUNT_NEW      = 'new';
    
    public static function getAllowedStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];
    }
}