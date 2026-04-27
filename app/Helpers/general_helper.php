<?php

/**
 * General Helper Functions for Auradent
 * 
 * Auto-loaded via app/Config/Autoload.php
 */

// ============================================================================
// NULL/EMPTY HANDLING
// ============================================================================
if (!function_exists('null_if_empty')) {
    /**
     * Convert empty strings (including whitespace-only) to null
     * 
     * @param mixed $value
     * @return mixed
     */
    function null_if_empty($value) {
        if ($value === null) {
            return null;
        }
        
        if (is_string($value)) {
            $trimmed = trim($value);
            return $trimmed === '' ? null : $trimmed;
        }
        
        if (is_array($value)) {
            return empty($value) ? null : $value;
        }
        
        return $value;
    }
}

// ============================================================================
// BOOLEAN TO INTEGER CONVERSION (for radio/checkbox values)
// ============================================================================
if (!function_exists('bool_to_int')) {
    /**
     * Convert boolean/radio values to integer or null
     * 
     * @param mixed $value
     * @return int|null
     */
    function bool_to_int($value): ?int {
        if ($value === null || $value === '') {
            return null;
        }
        return (int)$value;
    }
}

// ============================================================================
// PHILIPPINE MOBILE NUMBER HANDLING
// ============================================================================
if (!function_exists('clean_ph_mobile')) {
    function clean_ph_mobile(?string $mobile): ?string {
        if (empty($mobile)) return null;
        $cleaned = preg_replace('/\D/', '', $mobile);
        if (str_starts_with($cleaned, '639')) $cleaned = '0' . substr($cleaned, 2);
        if (!str_starts_with($cleaned, '09')) $cleaned = '09' . ltrim($cleaned, '0');
        return substr($cleaned, 0, 11);
    }
}

if (!function_exists('format_ph_mobile_display')) {
    function format_ph_mobile_display(?string $mobile): string {
        if (empty($mobile)) return '';
        $clean = clean_ph_mobile($mobile);
        if (empty($clean) || strlen($clean) !== 11) return esc($mobile);
        return substr($clean, 0, 4) . ' ' . substr($clean, 4, 3) . ' ' . substr($clean, 7, 4);
    }
}

if (!function_exists('validate_ph_mobile')) {
    function validate_ph_mobile(string $mobile): bool {
        $clean = clean_ph_mobile($mobile);
        return !empty($clean) && strlen($clean) === 11 && str_starts_with($clean, '09');
    }
}

// ============================================================================
// GENERAL PHONE HANDLING (International/Landline friendly)
// ============================================================================
if (!function_exists('clean_phone_general')) {
    function clean_phone_general(?string $phone): ?string {
        if (empty($phone)) return null;
        // Allow + and digits only, remove everything else
        $cleaned = preg_replace('/[^\d+]/', '', $phone);
        // Ensure + is only at the start
        if (strpos($cleaned, '+') > 0) {
            $cleaned = str_replace('+', '', $cleaned);
        }
        // Max 20 characters
        $result = substr($cleaned, 0, 20);
        return $result !== '' ? $result : null;
    }
}

if (!function_exists('format_phone_general_display')) {
    function format_phone_general_display(?string $phone): string {
        return empty($phone) ? '' : esc($phone);
    }
}