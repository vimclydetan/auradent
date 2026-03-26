<?php

namespace App\Controllers\Receptionist;

use App\Controllers\BaseController;
use App\Models\ServiceModel;
use App\Models\DentistModel;
use App\Models\MedicalConditionModel;

class WalkinController extends BaseController
{
    public function __construct()
    {
        date_default_timezone_set('Asia/Manila');
    }

    public function index()
    {
        $serviceModel = new ServiceModel();
        $dentistModel = new DentistModel();
        $mcModel = new MedicalConditionModel();

        $data = [
            'title'              => 'Walk-in Consultation',
            'services'           => $serviceModel->where('status', 'active')->findAll(),
            'dentists'           => $dentistModel->findAll(),
            'medical_conditions' => $mcModel->where('is_active', 1)->orderBy('category', 'ASC')->findAll()
        ];

        return view('receptionist/walkin/index', $data);
    }

    public function store()
    {
        $db = \Config\Database::connect();
        $accountType = $this->request->getPost('account_type');

        // 1. VALIDATION
        $rules = [
            'dentist_id' => 'required',
            'services'   => 'required',
        ];

        if ($accountType === 'new') {
            $rules += [
                'first_name'     => 'required',
                'last_name'      => 'required',
                'birthdate'      => 'required',
                'primary_mobile' => 'required',
                'gender'         => 'required',
                'region'         => 'required',   // Region Code
                'province'       => 'required',   // Province Code
                'city'           => 'required',   // City/Mun Code
                'barangay'       => 'required',   // Barangay Code
                'email'          => 'required|valid_email|is_unique[patients.email]',
            ];
        } else {
            $rules['patient_id'] = 'required';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validation failed.')->with('validation_errors', $this->validator->getErrors());
        }

        $db->transBegin();

        try {
            $patientId = $this->request->getPost('patient_id');

            if ($accountType === 'new') {
                $patientModel = new \App\Models\PatientModel();
                $generatedCode = $patientModel->generatePatientCode();

                // 2. INSERT PATIENT DIRECTLY (Storing CODES for address)
                $patientData = [
                    'patient_code'       => $generatedCode,
                    'user_id'            => null, // Walk-in has no user account
                    'first_name'         => $this->request->getPost('first_name'),
                    'middle_name'        => $this->request->getPost('middle_name'),
                    'last_name'          => $this->request->getPost('last_name'),
                    'name_suffix'        => $this->request->getPost('name_suffix'),
                    'nickname'           => $this->request->getPost('nickname'),
                    'email'              => $this->request->getPost('email'),
                    'birthdate'          => $this->request->getPost('birthdate'),
                    'gender'             => $this->request->getPost('gender'),
                    'civil_status'       => $this->request->getPost('civil_status'),
                    'primary_mobile'     => str_replace(' ', '', $this->request->getPost('primary_mobile')),
                    'occupation'         => $this->request->getPost('occupation'),
                    'house_number'       => $this->request->getPost('house_number'),
                    'building_name'      => $this->request->getPost('building_name'),
                    'street_name'        => $this->request->getPost('street_name'),
                    'subdivision'        => $this->request->getPost('subdivision'),
                    // Dito sinesave ang mga CODES
                    'region'             => $this->request->getPost('region'),
                    'province'           => $this->request->getPost('province'),
                    'city'               => $this->request->getPost('city'),
                    'barangay'           => $this->request->getPost('barangay'),
                    'postal_code'        => $this->request->getPost('postal_code'),
                    'has_insurance'      => $this->request->getPost('has_insurance') ? 1 : 0,
                    'insurance_provider' => $this->request->getPost('insurance_provider'),
                    'reason_for_consultation' => $this->request->getPost('reason_for_consultation'),
                    'created_at'         => date('Y-m-d H:i:s'),
                    'updated_at'         => date('Y-m-d H:i:s'),
                ];

                if (!$db->table('patients')->insert($patientData)) {
                    $error = $db->error();
                    throw new \Exception('Patient Insert Failed: ' . $error['message']);
                }
                $patientId = $db->insertID();
            }

            // 3. INSERT VISIT
            $services = $this->request->getPost('services');
            $serviceId = is_array($services) ? $services[0] : $services;

            $visitData = [
                'patient_id'     => $patientId,
                'appointment_id' => null,
                'visit_type'     => 'Walk-in',
                'service_id'     => $serviceId,
                'dentist_id'     => $this->request->getPost('dentist_id'),
                'check_in_time'  => date('Y-m-d H:i:s'),
            ];

            if (!$db->table('visits')->insert($visitData)) {
                $error = $db->error();
                throw new \Exception('Visit Insert Failed: ' . $error['message']);
            }

            // 4. INSERT MEDICAL RECORD
            $sys = $this->request->getPost('blood_pressure_systolic');
            $dia = $this->request->getPost('blood_pressure_diastolic');
            $bleedVal  = $this->request->getPost('bleeding_time_value');
            $bleedUnit = $this->request->getPost('bleeding_time_unit');

            $medicalRecord = [
                'patient_id'                     => $patientId,
                'physician_name'                 => $this->request->getPost('physician_name'),
                'blood_type'                     => $this->request->getPost('blood_type'),
                'blood_pressure'                 => ($sys && $dia) ? $sys . '/' . $dia : null,
                'bleeding_time'                  => ($bleedVal) ? $bleedVal . ' ' . $bleedUnit : null,
                'medical_conditions'             => json_encode($this->request->getPost('medical_conditions') ?? []),
                'is_good_health'                 => $this->request->getPost('is_good_health') ?? 0,
                'is_under_medical_treatment'     => $this->request->getPost('is_under_medical_treatment') ?? 0,
                'has_serious_illness'            => $this->request->getPost('has_serious_illness') ?? 0,
                'serious_illness_details'        => $this->request->getPost('serious_illness_details'),
                'is_hospitalized'                => $this->request->getPost('is_hospitalized') ?? 0,
                'is_taking_medication'           => $this->request->getPost('is_taking_medication') ?? 0,
                'uses_tobacco'                   => $this->request->getPost('uses_tobacco') ?? 0,
                'uses_drugs'                     => $this->request->getPost('uses_drugs') ?? 0,
                'is_pregnant'                    => $this->request->getPost('is_pregnant') ?? 0,
                'is_nursing'                     => $this->request->getPost('is_nursing') ?? 0,
                'is_taking_birth_control'        => $this->request->getPost('is_taking_birth_control') ?? 0,
            ];

            if (!$db->table('patient_medical_records')->insert($medicalRecord)) {
                $error = $db->error();
                throw new \Exception('Medical Record Insert Failed: ' . $error['message']);
            }

            $db->transCommit();
            return redirect()->to(base_url('receptionist/walkin'))->with('success', 'Walk-in saved successfully!');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
