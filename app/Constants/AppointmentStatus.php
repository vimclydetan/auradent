<?php

namespace App\Constants;

class AppointmentStatus
{
    // ✅ Core statuses
    public const PENDING                = 'pending';
public const CONFIRMED              = 'confirmed';
public const REJECTED               = 'rejected';
public const COMPLETED              = 'completed';
public const CANCELLED              = 'cancelled';
public const NO_SHOW                = 'no-show';
public const CANCELLATION_REQUESTED = 'cancellation_requested';

    /**
     * Get all valid statuses
     */
    public static function all(): array
    {
        return [
            self::PENDING,
            self::CONFIRMED,
            self::REJECTED,
            self::COMPLETED,
            self::CANCELLED,
            self::CANCELLATION_REQUESTED,
            self::NO_SHOW
        ];
    }

    // ========================================
    // ✅ NEW: Case-Insensitive Comparison Helpers
    // ========================================

    /**
     * Case-insensitive status comparison
     * 
     * Usage: AppointmentStatus::is($dbStatus, AppointmentStatus::PENDING)
     */
    public static function is(string $actual, string $expected): bool
    {
        return strcasecmp(trim($actual), trim($expected)) === 0;
    }

    /**
     * Check if actual status matches any in the expected list (case-insensitive)
     * 
     * Usage: AppointmentStatus::isIn($dbStatus, [AppointmentStatus::COMPLETED, AppointmentStatus::CANCELLED])
     */
    public static function isIn(string $actual, array $expectedList): bool
    {
        $actualLower = strtolower(trim($actual));
        foreach ($expectedList as $expected) {
            if (strtolower(trim($expected)) === $actualLower) {
                return true;
            }
        }
        return false;
    }

    /**
     * Normalize status string to match constant format (for display/storage consistency)
     */
    public static function normalize(string $status): string
{
    return match (strtolower(trim($status))) {
        'pending'                 => self::PENDING,
        'confirmed'               => self::CONFIRMED,
        'rejected'                => self::REJECTED,
        'completed'               => self::COMPLETED,
        'cancelled', 'canceled'   => self::CANCELLED,
        'no-show', 'no_show'      => self::NO_SHOW,
        'cancellation requested'  => self::CANCELLATION_REQUESTED,
        default                   => strtolower(trim($status))
    };
}

    // ========================================
    // ✅ EXISTING: UI Helpers (unchanged)
    // ========================================

    /**
     * Get Tailwind color class for status badge styling
     */
    public static function color(string $status): string
    {
        $status = strtolower($status);
        
        return match($status) {
            'pending'                => 'amber',
            'confirmed'              => 'blue',
            'rejected'               => 'red',
            'completed'              => 'green',
            'cancelled'              => 'gray',
            'cancellation_requested' => 'orange',
            'no_show', 'no-show'     => 'orange',
            default                  => 'slate',
        };
    }
    
    /**
     * Get icon class for status (Font Awesome)
     */
    public static function icon(string $status): string
    {
        $status = strtolower($status);
        
        return match($status) {
            'pending'                => 'fa-clock',
            'confirmed'              => 'fa-check',
            'rejected'               => 'fa-circle-xmark',
            'completed'              => 'fa-clipboard-check',
            'cancelled'              => 'fa-ban',
            'cancellation_requested' => 'fa-hand-paper',
            'no_show', 'no-show'     => 'fa-user-slash',
            default                  => 'fa-question',
        };
    }
    
    /**
     * Check if status allows patient to request cancellation
     */
    public static function canRequestCancellation(string $status): bool
    {
        return self::is($status, self::CONFIRMED);
    }
    
    /**
     * Check if status is a "pending-like" state (not final)
     */
    public static function isPendingState(string $status): bool
    {
        $status = strtolower($status);
        return in_array($status, ['pending', 'cancellation_requested'], true);
    }
    
    /**
     * Check if status is final (no further actions allowed)
     */
    public static function isFinalState(string $status): bool
    {
        $status = strtolower($status);
        return in_array($status, ['completed', 'cancelled', 'rejected'], true);
    }
    
    /**
     * Get user-friendly label for status
     */
    public static function label(string $status): string
    {
        return match(strtolower($status)) {
            'pending'                => 'Pending Review',
            'confirmed'              => 'Confirmed',
            'rejected'               => 'Rejected',
            'completed'              => 'Completed',
            'cancelled'              => 'Cancelled',
            'cancellation_requested' => 'Cancellation Requested',
            'no_show', 'no-show'     => 'No Show',
            default                  => ucfirst($status),
        };
    }
}