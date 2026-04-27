<?php

namespace App\Services;

use CodeIgniter\Email\Email;
use Config\Services;

class EmailService
{
    protected $email;
    protected $db;

    // =====================================================
    // FIX: Added 'appointment_pending' to both maps.
    //
    // Previously this key was missing, so when a patient
    // self-booked (status = pending), queueEmail() would
    // hit the "Invalid email template key" guard and return
    // false — patient never received any booking email.
    //
    // Also renamed 'appointment_confirmation' → removed it
    // entirely since AppointmentService was using that wrong
    // key. The correct key everywhere is 'appointment_confirmed'.
    // =====================================================
    protected array $templateMap = [
        'appointment_confirmed'             => 'appointment_confirmed',
        'appointment_pending'               => 'appointment_pending',
        'appointment_rejected'              => 'appointment_rejected',
        'appointment_cancelled'             => 'appointment_cancelled',
        'appointment_cancellation_approved' => 'appointment_cancellation_approved',
        'appointment_cancellation_denied'   => 'appointment_cancellation_denied',
        'appointment_no-show'               => 'appointment_no-show',
        'cancellation_denied'               => 'cancellation_denied',
    ];

    protected array $subjectMap = [
        'appointment_confirmed'             => 'Auradent Dental Clinic: Your Appointment is Confirmed (Queue #{{queue_number}})',
        'appointment_pending'               => 'Auradent Dental Clinic: Appointment Request Received – Awaiting Approval',
        'appointment_rejected'              => 'Auradent Dental Clinic: Appointment Request Update',
        'appointment_cancelled'             => 'Auradent Dental Clinic: Appointment Cancelled',
        'appointment_cancellation_approved' => 'Auradent Dental Clinic: Cancellation Request Approved',
        'appointment_cancellation_denied'   => 'Auradent Dental Clinic: Cancellation Request Update',
        'appointment_no-show'               => 'Auradent Dental Clinic: Appointment Status Update',
        'cancellation_denied'               => 'Auradent Dental Clinic: Cancellation Request Denied',
    ];

    public function __construct()
    {
        $this->email = Services::email();
        $this->db    = \Config\Database::connect();
    }

    /**
     * Queue email with proper template mapping
     */
    public function queueEmail(string $to, string $name, string $templateKey, array $data, ?\DateTime $sendAt = null): bool
    {
        if (!isset($this->templateMap[$templateKey])) {
            log_message('error', "Invalid email template key: {$templateKey}");
            return false;
        }

        $template = $this->templateMap[$templateKey];
        $subject  = $this->buildSubject($templateKey, $data);
        log_message('debug', '[EMAIL QUEUE] Encoded payload contains patient_code: ' . (str_contains(json_encode($data), 'patient_code') ? 'YES' : 'NO'));
        return $this->db->table('email_queue')->insert([
            'recipient_email' => $to,
            'recipient_name'  => $name,
            'subject'         => $subject,
            'template'        => $template,
            'payload'         => json_encode($data),

            'scheduled_at'    => $sendAt?->format('Y-m-d H:i:s'),
            'status'          => 'pending',
            'retry_count'     => 0,
            'created_at'      => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Build subject line with dynamic placeholders
     */
    protected function buildSubject(string $templateKey, array $data): string
    {
        $subject = $this->subjectMap[$templateKey] ?? 'Auradent Dental Clinic Notification';

        foreach ($data as $key => $value) {
            $subject = str_replace("{{{$key}}}", (string) $value, $subject);
        }

        return mb_encode_mimeheader($subject, 'UTF-8', 'Q');
    }

    /**
     * Process queued emails (cron job)
     */
    public function processQueue(int $limit = 50): array
    {
        $results = ['sent' => 0, 'failed' => 0, 'errors' => []];

        $emails = $this->db->table('email_queue')
            ->where('status', 'pending')
            ->groupStart()
            ->where('scheduled_at <=', date('Y-m-d H:i:s'))
            ->orWhere('scheduled_at', null)
            ->groupEnd()
            ->orderBy('created_at', 'ASC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        foreach ($emails as $item) {
            try {
                $payload = json_decode($item['payload'], true);
                $view    = "emails/{$item['template']}";

                $viewPath = APPPATH . 'Views/' . str_replace('/', DIRECTORY_SEPARATOR, $view) . '.php';
                if (!file_exists($viewPath)) {
                    throw new \Exception("Email template not found: {$view}");
                }

                $this->email->clear();
                $this->email->setTo($item['recipient_email']);
                $this->email->setFrom(
                    env('EMAIL_FROM_ADDRESS', 'noreply@auradent.local'),
                    env('EMAIL_FROM_NAME', 'Auradent Dental')
                );
                $this->email->setSubject($item['subject']);
                $this->email->setMessage(view($view, $payload));

                if ($this->email->send()) {
                    $this->db->table('email_queue')->where('id', $item['id'])->update([
                        'status'     => 'sent',
                        'sent_at'    => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $results['sent']++;
                } else {
                    throw new \Exception($this->email->printDebugger(['headers']));
                }
            } catch (\Exception $e) {
                log_message('error', "Email send failed (ID {$item['id']}): {$e->getMessage()}");

                $this->db->table('email_queue')->where('id', $item['id'])->update(
                    $item['retry_count'] < 3
                        ? ['retry_count' => $item['retry_count'] + 1, 'error_message' => $e->getMessage(), 'updated_at' => date('Y-m-d H:i:s')]
                        : ['status' => 'failed', 'error_message' => $e->getMessage(), 'updated_at' => date('Y-m-d H:i:s')]
                );

                if ($item['retry_count'] >= 3) {
                    $results['errors'][] = $item['id'];
                }
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Send email immediately (for testing)
     */
    public function sendNow(string $to, string $name, string $templateKey, array $data): bool
    {
        if (!isset($this->templateMap[$templateKey])) {
            log_message('error', "Invalid email template key: {$templateKey}");
            return false;
        }

        $template = $this->templateMap[$templateKey];
        $subject  = $this->buildSubject($templateKey, $data);

        $this->email->clear();
        $this->email->setTo($to);
        $this->email->setFrom(
            env('EMAIL_FROM_ADDRESS', 'noreply@auradent.local'),
            env('EMAIL_FROM_NAME', 'Auradent Dental')
        );
        $this->email->setSubject($subject);
        $this->email->setMessage(view("emails/{$template}", $data));

        return $this->email->send();
    }
}
