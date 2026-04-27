<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PatientModel;
use App\Models\AppointmentModel;
use App\Models\TreatmentRecordModel;

class DashboardController extends BaseController
{
    protected $patientModel;
    protected $appointmentModel;
    protected $treatmentModel;
    protected $db;

    public function __construct()
    {
        $this->patientModel = new PatientModel();
        $this->appointmentModel = new AppointmentModel();
        $this->treatmentModel = new TreatmentRecordModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data = [
            'title' => 'Dashboard',
            'stats' => $this->getDashboardStats(),
            'upcomingAppointments' => $this->getUpcomingAppointments(),
            'recentPatients' => $this->getRecentPatients(),
            'monthlyRevenue' => $this->getMonthlyRevenue(),
            'appointmentsByStatus' => $this->getAppointmentsByStatus(),
        ];

        return view('admin/dashboard', $data);
    }

    private function getDashboardStats()
    {
        $today = date('Y-m-d');
        $currentMonthStart = date('Y-m-01');
        $currentMonthEnd = date('Y-m-t');

        // Total Patients
        $totalPatients = $this->patientModel->countAll();

        // Appointments Today (Pending/Confirmed only)
        $appointmentsToday = $this->db->table('appointments')
            ->where('appointment_date', $today)
            ->whereIn('status', ['Pending', 'Confirmed'])
            ->countAllResults();

        // 💰 Monthly Revenue - FIXED: Use treatment_records instead of empty billing table
        $monthlyRevenue = $this->db->table('treatment_records tr')
            ->select('SUM(tr.amount_paid) as revenue')
            ->where('tr.treatment_date >=', $currentMonthStart . ' 00:00:00')
            ->where('tr.treatment_date <=', $currentMonthEnd . ' 23:59:59')
            ->where('tr.amount_paid >', 0)
            ->get()
            ->getRow();
        $monthlyRevenue = $monthlyRevenue->revenue ?? 0;

        // Pending Appointments (future dates)
        $pendingAppointments = $this->db->table('appointments')
            ->where('status', 'Pending')
            ->where('appointment_date >=', $today)
            ->countAllResults();

        // Completed Appointments This Month
        $completedThisMonth = $this->db->table('appointments')
            ->where('status', 'Completed')
            ->where('appointment_date >=', $currentMonthStart)
            ->where('appointment_date <=', $currentMonthEnd)
            ->countAllResults();

        // Active Dentists
        $activeDentists = $this->db->table('dentists')->countAllResults();

        return [
            'total_patients' => $totalPatients,
            'appointments_today' => $appointmentsToday,
            'monthly_revenue' => number_format($monthlyRevenue, 2),
            'pending_appointments' => $pendingAppointments,
            'completed_this_month' => $completedThisMonth,
            'active_dentists' => $activeDentists,
        ];
    }

    private function getUpcomingAppointments($limit = 10)
    {
        $today = date('Y-m-d');

        $appointments = $this->db->table('appointments a')
            ->select('
                a.id,
                a.appointment_date,
                a.appointment_time,
                a.end_time,
                a.status,
                a.remarks,
                CONCAT_WS(" ", p.first_name, p.middle_name, p.last_name, p.name_suffix) as patient_name,
                p.primary_mobile,
                CONCAT_WS(" ", d.first_name, d.middle_name, d.last_name, d.extension_name) as dentist_name,
                GROUP_CONCAT(s.service_name SEPARATOR ", ") as services
            ')
            ->join('patients p', 'p.id = a.patient_id', 'left')
            ->join('dentists d', 'd.id = a.dentist_id', 'left')
            ->join('appointment_services aps', 'aps.appointment_id = a.id', 'left')
            ->join('services s', 's.id = aps.service_id', 'left')
            ->where('a.appointment_date >=', $today)
            ->whereIn('a.status', ['Pending', 'Confirmed'])
            ->groupBy('a.id')
            ->orderBy('a.appointment_date', 'ASC')
            ->orderBy('a.appointment_time', 'ASC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        // Format time for display
        foreach ($appointments as &$apt) {
            if ($apt['appointment_time']) {
                $apt['appointment_time_display'] = date('h:i A', strtotime($apt['appointment_time']));
            }
            if ($apt['end_time']) {
                $apt['end_time_display'] = date('h:i A', strtotime($apt['end_time']));
            }
            $apt['services'] = $apt['services'] ?? 'No service assigned';
        }

        return $appointments;
    }

    private function getRecentPatients($limit = 5)
    {
        $patients = $this->db->table('patients')
            ->select('
                id,
                patient_code,
                CONCAT_WS(" ", first_name, middle_name, last_name, name_suffix) as full_name,
                primary_mobile,
                email,
                birthdate,
                gender,
                created_at
            ')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        return $patients;
    }

    private function getMonthlyRevenue()
    {
        // Initialize last 6 months with zero values
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $months[$month] = 0;
        }

        // 💰 FIXED: Get revenue from treatment_records (actual payment source)
        $revenue = $this->db->table('treatment_records')
            ->select('
                DATE_FORMAT(treatment_date, "%Y-%m") as month, 
                SUM(amount_paid) as total
            ')
            ->where('amount_paid >', 0)
            ->where('treatment_date >=', date('Y-m-01 00:00:00', strtotime('-5 months')))
            ->groupBy('month')
            ->get()
            ->getResultArray();

        // Map revenue to months array
        foreach ($revenue as $row) {
            if (isset($months[$row['month']])) {
                $months[$row['month']] = (float) $row['total'];
            }
        }

        // Format for Chart.js: labeled array
        $chartData = [];
        foreach ($months as $month => $amount) {
            $chartData[] = [
                'month' => date('M Y', strtotime($month . '-01')),
                'amount' => (float) $amount
            ];
        }

        return $chartData;
    }

    private function getAppointmentsByStatus()
    {
        $currentMonthStart = date('Y-m-01');
        $currentMonthEnd = date('Y-m-t');

        $statusCounts = $this->db->table('appointments')
            ->select('status, COUNT(*) as count')
            ->where('appointment_date >=', $currentMonthStart)
            ->where('appointment_date <=', $currentMonthEnd)
            ->groupBy('status')
            ->get()
            ->getResultArray();

        $result = [
            'Pending' => 0,
            'Confirmed' => 0,
            'Completed' => 0,
            'Cancelled' => 0,
        ];

        foreach ($statusCounts as $row) {
            if (isset($result[$row['status']])) {
                $result[$row['status']] = (int) $row['count'];
            }
        }

        return $result;
    }

    // ==================== AJAX API ENDPOINTS ====================

    public function getStats()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request'], 400);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $this->getDashboardStats()
        ]);
    }

    public function getAppointmentsByDate()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request'], 400);
        }

        $date = $this->request->getGet('date');
        if (!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $this->response->setJSON(['error' => 'Valid date (YYYY-MM-DD) is required'], 400);
        }

        $appointments = $this->db->table('appointments a')
            ->select('
                a.id,
                a.appointment_time,
                a.end_time,
                a.status,
                CONCAT_WS(" ", p.first_name, p.last_name) as patient_name,
                CONCAT_WS(" ", d.first_name, d.last_name) as dentist_name,
                s.service_name
            ')
            ->join('patients p', 'p.id = a.patient_id', 'left')
            ->join('dentists d', 'd.id = a.dentist_id', 'left')
            ->join('appointment_services aps', 'aps.appointment_id = a.id', 'left')
            ->join('services s', 's.id = aps.service_id', 'left')
            ->where('a.appointment_date', $date)
            ->orderBy('a.appointment_time', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON(['success' => true, 'data' => $appointments]);
    }

    public function getTodaySchedule()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request'], 400);
        }

        $today = date('Y-m-d');

        $appointments = $this->db->table('appointments a')
            ->select('
                a.id,
                a.appointment_time,
                a.end_time,
                a.status,
                a.remarks,
                CONCAT_WS(" ", p.first_name, p.middle_name, p.last_name, p.name_suffix) as patient_name,
                p.primary_mobile,
                CONCAT_WS(" ", d.first_name, d.middle_name, d.last_name, d.extension_name) as dentist_name,
                GROUP_CONCAT(s.service_name SEPARATOR ", ") as services
            ')
            ->join('patients p', 'p.id = a.patient_id', 'left')
            ->join('dentists d', 'd.id = a.dentist_id', 'left')
            ->join('appointment_services aps', 'aps.appointment_id = a.id', 'left')
            ->join('services s', 's.id = aps.service_id', 'left')
            ->where('a.appointment_date', $today)
            ->groupBy('a.id')
            ->orderBy('a.appointment_time', 'ASC')
            ->get()
            ->getResultArray();

        // Format times for display
        foreach ($appointments as &$apt) {
            $apt['appointment_time_display'] = $apt['appointment_time'] ? date('h:i A', strtotime($apt['appointment_time'])) : '';
            $apt['end_time_display'] = $apt['end_time'] ? date('h:i A', strtotime($apt['end_time'])) : '';
            $apt['services'] = $apt['services'] ?? 'No service assigned';
        }

        return $this->response->setJSON(['success' => true, 'data' => $appointments]);
    }
}