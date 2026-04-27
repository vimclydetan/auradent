<?php

if (!function_exists('generateQueueNumber')) {
    /**
     * Generate sequential queue number for a dentist on a specific date
     */
    function generateQueueNumber(int $dentistId, string $appointmentDate): int
    {
        $db = \Config\Database::connect();

        $row = $db->table('appointments')
            ->selectMax('queue_number')
            ->where('dentist_id', $dentistId)
            ->where('queue_date', $appointmentDate)
            ->where('status !=', 'Cancelled')
            ->get()
            ->getRow();

        return ($row->queue_number ?? 0) + 1;
    }
}

if (!function_exists('getQueuePosition')) {
    /**
     * Get patient's current position in queue
     */
    function getQueuePosition(int $appointmentId): ?array
    {
        $db = \Config\Database::connect();

        $appt = $db->table('appointments')
            ->select('queue_number, queue_date, dentist_id, status')
            ->where('id', $appointmentId)
            ->get()
            ->getRowArray();

        if (!$appt || !$appt['queue_number']) {
            return null;
        }

        $ahead = $db->table('appointments')
            ->where('dentist_id', $appt['dentist_id'])
            ->where('queue_date', $appt['queue_date'])
            ->where('queue_number <', $appt['queue_number'])
            ->whereIn('status', ['Confirmed', 'Completed', 'InProgress'])
            ->countAllResults();

        return [
            'queue_number' => $appt['queue_number'],
            'position' => (int)$ahead + 1,
            'total_ahead' => (int)$ahead
        ];
    }
}
