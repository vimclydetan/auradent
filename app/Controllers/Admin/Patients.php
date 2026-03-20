<?php 
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\PatientModel;

class Patients extends BaseController {
    public function index() {
        $model = new PatientModel();
        $data['patients'] = $model->getPatientsWithUser();
        return view('admin/patients/index', $data);
    }

    public function create() {
        // Form to create User + Patient Profile (Admin Only)
        return view('admin/patients/create');
    }

    public function store() {
        $db = \Config\Database::connect();
        $userModel = new UserModel();
        $patientModel = new PatientModel();

        // Validation
        $rules = [
            'full_name' => 'required',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Transaction (Create User then Patient)
        $db->transStart();
        
        $userId = $userModel->insert([
            'username' => $this->request->getPost('full_name'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => 'patient'
        ]);

        if ($userId) {
            $patientModel->insert([
                'user_id' => $userId,
                'full_name' => $this->request->getPost('full_name'),
                'age' => $this->request->getPost('age'),
                'gender' => $this->request->getPost('gender'),
                'contact' => $this->request->getPost('contact'),
                'address' => $this->request->getPost('address'),
            ]);
        }
        
        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Failed to create patient');
        }

        return redirect()->to('/admin/patients')->with('success', 'Patient created successfully');
    }
}
?>