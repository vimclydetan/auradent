<?php

namespace App\Controllers\Patient;

use App\Controllers\BaseController;
use App\Models\AppointmentModel;
use App\Models\PatientModel;
use App\Services\EmailService;

class AppointmentConfirmationController extends BaseController
{
    protected $appointmentModel;
    protected $patientModel;
    protected $db;

    public function __construct()
    {
        $this->appointmentModel = new AppointmentModel();
        $this->patientModel = new PatientModel(); // ✅ Initialized
        $this->db = \Config\Database::connect();
    }

    /**
     * Show confirmation page (with login redirect if needed)
     */
    public function confirm($appointmentId, $token)
    {
        // 1. Verify token exists, unused, and not expired
        $confirmation = $this->db->table('appointment_confirmations')
            ->where('appointment_id', $appointmentId)
            ->where('confirmation_token', $token)
            ->where('is_confirmed', 0)
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->get()
            ->getRow();

        if (!$confirmation) {
            $reason = !$confirmation ? 'Invalid confirmation link.' : 'Link has expired.';
            return redirect()->to('/')->with('error', $reason);
        }

        // 2. Get appointment with patient info
        $appointment = $this->appointmentModel
            ->select('appointments.*, patients.user_id, patients.first_name, patients.last_name, patients.email, dentists.last_name as dentist_last')
            ->join('patients', 'patients.id = appointments.patient_id')
            ->join('dentists', 'dentists.id = appointments.dentist_id')
            ->find($appointmentId);

        if (!$appointment) {
            return redirect()->to('/')->with('error', 'Appointment not found.');
        }

        // 3. If not logged in, store pending data & redirect to login
        if (!session()->get('isLoggedIn')) {
            session()->setTempdata('pending_confirmation', [
                'appointment_id' => $appointmentId,
                'token' => $token,
                'patient_name' => $appointment['first_name'] . ' ' . $appointment['last_name']
            ], 300); // 5 minutes

            return redirect()->to('/')
                ->with('info', 'Please login to confirm your appointment for ' .
                    esc($appointment['first_name'] . ' ' . $appointment['last_name']));
        }

        // 4. Verify logged-in user owns this appointment
        $userId = session()->get('user_id');
        if ($appointment['user_id'] != $userId) {
            log_message('warning', "User {$userId} tried to confirm appointment {$appointmentId} owned by {$appointment['user_id']}");
            return redirect()->to('/')->with('error', 'Unauthorized: This appointment is not linked to your account.');
        }

        // 5. Show confirmation page
        return view('patient/confirm_appointment', [
            'appointment' => $appointment,
            'title' => 'Confirm Appointment',
            'token' => $token // Pass token for form submission
        ]);
    }

    /**
     * Process the confirmation (POST - requires login)
     */
    public function processConfirmation()
    {
        // ✅ Ensure logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')
                ->with('info', 'Please login to confirm your appointment.');
        }

        $appointmentId = (int) $this->request->getPost('appointment_id');
        $token         = trim($this->request->getPost('token'));
        $confirm       = $this->request->getPost('confirm');

        // ✅ Basic validation
        if (!$appointmentId || !$token || $confirm !== 'yes') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid confirmation request.');
        }

        // 🔐 Validate token
        $confirmation = $this->db->table('appointment_confirmations')
            ->where([
                'appointment_id'     => $appointmentId,
                'confirmation_token' => $token,
                'is_confirmed'      => 0
            ])
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->get()
            ->getRow();
        if (!$confirmation) {
            return redirect()->to('/')
                ->with('error', 'Confirmation link is invalid or expired.');
        }

        // 🔍 Get appointment
        $appointment = $this->appointmentModel->find($appointmentId);
        if (!$appointment) {
            return redirect()->to('/')
                ->with('error', 'Appointment not found.');
        }

        // 👤 Ownership check
        $patient = $this->patientModel->find($appointment['patient_id']);
        $sessionUserId = session()->get('user_id');

        if (!$patient || ($patient['user_id'] ?? null) != $sessionUserId) {
            return redirect()->to('/')
                ->with('error', 'Unauthorized action.');
        }

        // 🧠 Prevent double confirm (important)
        if ($appointment['status'] === 'Confirmed') {
            return redirect()->to('/patient/appointments')
                ->with('info', 'Appointment already confirmed.');
        }

        // 🚀 Use TRANSACTION (VERY IMPORTANT)
        $this->db->transStart();

        // 1. Update appointment status ONLY
        $this->db->table('appointments')
            ->where('id', $appointmentId)
            ->update([
                'status' => 'Confirmed'
            ]);

        // 2. Mark confirmation token used
        $this->db->table('appointment_confirmations')
            ->where('id', $confirmation->id)
            ->update([
                'is_confirmed' => 1,
                'confirmed_at' => date('Y-m-d H:i:s')
            ]);

        $this->db->transComplete();

        // ❌ If transaction failed
        if ($this->db->transStatus() === false) {
            return redirect()->back()
                ->with('error', 'Failed to confirm appointment. Please try again.');
        }

        // 📧 Send email (safe, after DB success)
        try {
            $this->sendConfirmedEmail($appointmentId);
        } catch (\Throwable $e) {
            log_message('error', 'Email failed: ' . $e->getMessage());
        }

        return redirect()->to('/patient/appointments')
            ->with('success', '✓ Appointment confirmed! Queue #' . ($appointment['queue_number'] ?? ''));
    }

    /**
     * Send the "Confirmed" email using your working template
     */
    private function sendConfirmedEmail(int $appointmentId)
    {
        $appointment = $this->appointmentModel->find($appointmentId);
        $patient = $this->patientModel->find($appointment['patient_id']);
        $dentistModel = new \App\Models\DentistModel();
        $dentist = $dentistModel->find($appointment['dentist_id']);

        // Get email from patient or user table
        $email = $patient['email'] ?? null;
        if (!$email && !empty($patient['user_id'])) {
            $user = (new \App\Models\UserModel())->find($patient['user_id']);
            $email = $user->email ?? null;
        }
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            log_message('warning', "No valid email for confirmed appointment #{$appointmentId}");
            return;
        }

        $emailService = new EmailService();
        $emailService->queueEmail(
            $email,
            trim(($patient['first_name'] ?? '') . ' ' . ($patient['last_name'] ?? '')),
            'appointment_confirmation', // ✅ Your working template
            [
                'patient_name'   => $patient['first_name'] ?? 'Valued Patient',
                'queue_number'   => $appointment['queue_number'] ?? 'N/A',
                'appointment_date' => date('F d, Y', strtotime($appointment['appointment_date'])),
                'scheduled_time' => date('h:i A', strtotime($appointment['appointment_time'])), // ✅ Match your working template var name
                'dentist_name'   => $dentist ? $dentist['last_name'] : 'TBD',
                'service_name'   => 'Dental Service',
            ]
        );

        log_message('info', "Queued confirmed email for appointment #{$appointmentId} to {$email}");
    }
}
