<?php

if (!function_exists('view_exists')) {
    function view_exists(string $view): bool
    {
        $locator = \Config\Services::locator();
        return (bool) $locator->locateFile($view, 'Views', '.php');
    }
}

if (!function_exists('format_phone_for_link')) {
    function format_phone_for_link(string $phone): string
    {
        return preg_replace('/[^0-9+]/', '', $phone);
    }
}

// ➕ NEW: Format Philippine phone for display (09XX XXX XXXX)
if (!function_exists('format_philippine_phone')) {
    function format_philippine_phone(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        
        // Handle +63 prefix
        if (strpos($clean, '63') === 0) {
            $clean = substr($clean, 2);
        }
        
        // Format as 09XX XXX XXXX (11 digits)
        if (strlen($clean) === 11 && strpos($clean, '9') === 0) {
            return implode(' ', [
                substr($clean, 0, 4),
                substr($clean, 4, 3),
                substr($clean, 7, 4)
            ]);
        }
        
        // Fallback: return original if format unknown
        return $phone;
    }
}

// ➕ NEW: Prepare email payload with clinic config defaults
if (!function_exists('prepare_clinic_email_payload')) {
    function prepare_clinic_email_payload(array $payload = []): array
    {
        $clinic = config('Clinic');
        
        $defaults = [
            'clinic_name'    => $clinic->name,
            'clinic_phone'   => $clinic->phone,
            'clinic_address' => $clinic->address,
            'clinic_email'   => $clinic->email,
        ];
        
        // Merge: payload > defaults, with null-coalescing safety
        return array_merge($defaults, array_filter($payload, fn($v) => $v !== null));
    }
}

// ➕ NEW: Escape multiple variables at once (for email views)
if (!function_exists('esc_many')) {
    function esc_many(array $data, string $context = 'html'): array
    {
        return array_map(fn($value) => esc($value, $context), $data);
    }
}