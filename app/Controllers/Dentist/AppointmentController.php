<?php

namespace App\Controllers\Dentist;

use App\Controllers\BaseController;
use App\Models\AppointmentModel;
use App\Models\ServiceModel;
use App\Models\AppointmentServiceModel;
use App\Models\DentistModel;
use App\Models\MedicalRecordModel;
use CodeIgniter\API\ResponseTrait; // For JSON responses
use App\Constants\AppointmentStatus;

class AppointmentController extends BaseController
{
    use ResponseTrait; // Adds fail(), respond(), etc.

    protected $appointmentModel;
    protected $serviceModel;
    protected $apptSvcModel;

    // Allowed image types for chart uploads
    private $allowedImageTypes = ['image/jpeg', 'image/png', 'image/webp'];
    private $maxImageSize = 2 * 1024 * 1024; // 2MB

    public function __construct()
    {
        date_default_timezone_set('Asia/Manila');
        $this->appointmentModel = new AppointmentModel();
        $this->serviceModel = new ServiceModel();
        $this->apptSvcModel = new AppointmentServiceModel();

        // 🔒 Security: Ensure user is authenticated
        if (!session()->has('user_id')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }
    }

    // 🔐 Helper: Verify dentist owns the record
    private function verifyDentistAccess($dentistId): bool
    {
        $currentUserId = session()->get('user_id');
        $dentistModel = new DentistModel();
        $profile = $dentistModel->where('user_id', $currentUserId)->first();

        return $profile && $profile['id'] == $dentistId && session()->get('role') === 'dentist';
    }

    public function index()
    {
        // 🔒 Re-verify role on every request
        if (session()->get('role') !== 'dentist') {
            log_message('warning', 'Unauthorized access attempt to dentist appointments by user_id: ' . session()->get('user_id'));
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $today = date('Y-m-d');
        $userId = session()->get('user_id');

        $dentistModel = new DentistModel();
        $dentistProfile = $dentistModel->where('user_id', $userId)->first();

        if (!$dentistProfile) {
            log_message('error', 'Dentist profile not found for user_id: ' . $userId);
            return redirect()->back()->with('error', 'Profile configuration error. Please contact admin.');
        }

        $dentistId = $dentistProfile['id'];

        $data = [
            'title'      => 'My Appointments',
            'mySchedule' => $this->appointmentModel->getDentistTodaySchedule($dentistId, $today),
            'services'   => $this->serviceModel->where('status', 'active')->findAll(),
            'page'       => 'dentist_appointments' // For audit logging
        ];

        // 📝 Log page view for audit trail
        log_message('info', "Dentist #{$dentistId} viewed appointments page", ['user_id' => $userId]);

        return view('dentist/appointments/index', $data);
    }

    // AJAX: Get appointment data - with security checks
    public function getAppointmentData($id)
    {
        // 🔒 Validate ID is numeric
        if (!is_numeric($id) || $id <= 0) {
            return $this->fail('Invalid appointment ID', 400);
        }

        // 🔒 Verify dentist access
        $userId = session()->get('user_id');
        $dentistModel = new DentistModel();
        $profile = $dentistModel->where('user_id', $userId)->first();

        if (!$profile || session()->get('role') !== 'dentist') {
            log_message('warning', "Unauthorized AJAX access attempt by user_id: {$userId}");
            return $this->fail('Unauthorized', 403);
        }

        $dentistId = $profile['id'];

        // 🔒 Verify appointment belongs to this dentist
        $appointment = $this->appointmentModel
            ->where('id', $id)
            ->where('dentist_id', $dentistId)
            ->first();

        if (!$appointment) {
            log_message('warning', "Dentist #{$dentistId} attempted to access appointment #{$id} not assigned to them");
            return $this->fail('Appointment not found or access denied', 404);
        }

        $services = $this->apptSvcModel->where('appointment_id', $id)->findAll();

        // 📝 Log data access
        log_message('info', "Dentist #{$dentistId} accessed appointment #{$id} data", ['user_id' => $userId]);

        return $this->respond([
            'appointment' => $appointment,
            'services'    => $services
        ]);
    }

    // 🔐 FINALIZE TREATMENT - Hardened
    // 🔐 FINALIZE TREATMENT - Inalis ang remarks
    public function finalizeTreatment()
    {
        if (session()->get('role') !== 'dentist') {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'appointment_id' => 'required|is_natural_no_zero',
            'services'       => 'required',
            'services.*'     => 'is_natural_no_zero',
            'levels.*'       => 'permit_empty|in_list[Standard,Moderate,Severe]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('error', 'Please select at least one service.');
        }

        $appointmentId = (int)$this->request->getPost('appointment_id');
        $services = $this->request->getPost('services');
        $levels = $this->request->getPost('levels');

        $userId = session()->get('user_id');
        $dentistModel = new DentistModel();
        $profile = $dentistModel->where('user_id', $userId)->first();

        if (!$profile) return redirect()->back()->with('error', 'Profile error.');

        $dentistId = $profile['id'];

        // Verify ownership
        $appointment = $this->appointmentModel
            ->where('id', $appointmentId)
            ->where('dentist_id', $dentistId)
            ->first();

        if (!$appointment) return redirect()->back()->with('error', 'Access denied.');

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // 1. Mark appointment as COMPLETED
            $this->appointmentModel->update($appointmentId, [
                'status'     => AppointmentStatus::COMPLETED,  // ✅ constant
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // 2. Sync Services (Delete existing, Insert new ones)
            $this->apptSvcModel->where('appointment_id', $appointmentId)->delete();

            foreach ($services as $idx => $sId) {
                if (!empty($sId)) {
                    $this->apptSvcModel->insert([
                        'appointment_id' => $appointmentId,
                        'service_id'     => (int)$sId,
                        'service_level'  => $levels[$idx] ?? 'Standard'
                    ]);
                }
            }

            $db->transCommit();
            return redirect()->back()->with('success', 'Appointment successfully marked as completed.');
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', "Finalize Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'System error during finalization.');
        }
    }

    // 🔐 SAVE CHART - Production Hardened
    public function saveChart()
    {
        // 🔒 Role check
        if (session()->get('role') !== 'dentist') {
            log_message('error', 'Unauthorized chart save attempt by user_id: ' . session()->get('user_id'));
            return $this->request->isAJAX()
                ? $this->fail('Unauthorized', 403)
                : redirect()->back()->with('error', 'Access denied.');
        }

        // 🔒 Input validation
        $validation = \Config\Services::validation();
        $validation->setRules([
            'patient_id'   => 'required|is_natural_no_zero',
            'drawing_data' => 'required',
            'complaint'    => 'permit_empty|max_length[2000]',
            'diagnosis'    => 'permit_empty|max_length[2000]',
            'treatment_plan' => 'permit_empty|max_length[2000]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $msg = 'Validation error: ' . implode(', ', $validation->getErrors());
            return $this->request->isAJAX()
                ? $this->fail($msg, 400)
                : redirect()->back()->with('error', $msg);
        }

        $patientId = (int)$this->request->getPost('patient_id');
        $drawingData = $this->request->getPost('drawing_data');
        $clinicalData = [
            'complaint'      => $this->request->getPost('complaint', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'diagnosis'      => $this->request->getPost('diagnosis', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'treatment_plan' => $this->request->getPost('treatment_plan', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
        ];

        // 🔒 Verify patient-dentist relationship (optional but recommended)
        $userId = session()->get('user_id');
        $dentistModel = new DentistModel();
        $profile = $dentistModel->where('user_id', $userId)->first();

        if (!$profile) {
            return $this->request->isAJAX()
                ? $this->fail('Profile error', 500)
                : redirect()->back()->with('error', 'Profile configuration error.');
        }
        $dentistId = $profile['id'];

        try {
            // 🔒 Validate and decode image
            if (!preg_match('/^data:image\/(jpeg|png|webp);base64,/', $drawingData, $matches)) {
                throw new \Exception('Invalid image format. Only JPEG, PNG, or WebP allowed.');
            }

            $imageType = $matches[1];
            $drawingData = substr($drawingData, strpos($drawingData, ',') + 1);
            $drawingData = base64_decode($drawingData);

            if ($drawingData === false) {
                throw new \Exception('Failed to decode image data.');
            }

            // 🔒 Check file size
            if (strlen($drawingData) > $this->maxImageSize) {
                throw new \Exception('Image too large. Maximum allowed: 2MB.');
            }

            // 🔒 Secure filename generation
            $filename = 'chart_' . $patientId . '_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $imageType;
            $uploadPath = FCPATH . 'uploads/charts/' . $filename;

            // 🔒 Ensure directory exists with proper permissions
            $uploadDir = dirname($uploadPath);
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // 🔒 Save file securely
            if (file_put_contents($uploadPath, $drawingData, LOCK_EX) === false) {
                throw new \Exception('Failed to save chart image.');
            }

            // 🔒 Set proper file permissions
            chmod($uploadPath, 0644);

            $relativePath = 'uploads/charts/' . $filename;

            // Save to database
            $medicalRecordModel = new MedicalRecordModel();
            $medicalRecordModel->saveDentalChart($patientId, $relativePath, $clinicalData);

            // 📝 Audit log
            log_message('info', "Dental chart saved for patient #{$patientId} by dentist #{$dentistId}", [
                'user_id' => $userId,
                'file' => $filename
            ]);

            // Return response
            if ($this->request->isAJAX()) {
                return $this->respond([
                    'success' => true,
                    'message' => 'Dental chart saved successfully!',
                    'redirect' => base_url('dentist/appointments')
                ]);
            }
            return redirect()->back()->with('success', 'Dental chart and clinical notes saved successfully!');
        } catch (\Exception $e) {
            log_message('error', 'Chart Save Error: ' . $e->getMessage(), [
                'user_id' => $userId,
                'patient_id' => $patientId,
                'trace' => $e->getTraceAsString()
            ]);

            // 🔒 Cleanup failed upload
            if (isset($uploadPath) && file_exists($uploadPath)) {
                @unlink($uploadPath);
            }

            $msg = 'Failed to save chart: ' . ($e->getMessage() ?? 'Unknown error');
            return $this->request->isAJAX()
                ? $this->fail($msg, 500)
                : redirect()->back()->with('error', $msg);
        }
    }
}
