<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<!-- SELECT2 & JQUERY -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    /* UI Refinement Only */
    .floating-label-group {
        position: relative;
        margin-bottom: 1rem;
    }

    .floating-input:focus~.floating-label,
    .floating-input:not(:placeholder-shown)~.floating-label {
        top: -0.5rem;
        left: 0.6rem;
        font-size: 0.70rem;
        color: #2563eb;
        background-color: white;
        padding: 0 0.4rem;
        font-weight: 700;
    }

    .floating-label {
        position: absolute;
        pointer-events: none;
        left: 0.75rem;
        top: 0.75rem;
        transition: 0.2s ease all;
        color: #94a3b8;
    }

    .fixed-label {
        font-size: 0.65rem;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        margin-bottom: 0.35rem;
        display: block;
        letter-spacing: 0.025em;
    }

    .step-hidden {
        display: none !important;
    }

    .step-dot {
        height: 6px;
        flex: 1;
        border-radius: 10px;
        background: #e2e8f0;
    }

    .step-dot.active {
        background: #2563eb;
    }

    .medical-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 0.5rem;
    }

    .form-section {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 1.5rem;
        border-radius: 1rem;
        margin-bottom: 1.5rem;
    }
</style>
<!-- Sa taas ng form -->
<?php if (session()->getFlashdata('error')): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
        <?= session()->getFlashdata('error') ?>
        <?php if (session()->getFlashdata('validation_errors')): ?>
            <ul class="list-disc ml-5 mt-2">
                <?php foreach (session()->getFlashdata('validation_errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
<?php endif; ?>
<!-- SUCCESS MESSAGE -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="bg-emerald-100 border border-emerald-400 text-emerald-800 px-4 py-4 rounded-2xl relative mb-6 flex items-center shadow-sm">
        <div class="bg-emerald-500 text-white rounded-full p-1 mr-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
        </div>
        <div>
            <p class="text-xs font-black uppercase tracking-widest">Success!</p>
            <p class="text-sm font-medium opacity-90"><?= session()->getFlashdata('success') ?></p>
        </div>
    </div>
<?php endif; ?>
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-2xl font-bold text-slate-800 italic uppercase tracking-tighter">🚶 Walk-in Consultation</h3>
            <p class="text-sm text-slate-500">Record consultation for new or existing patient.</p>
        </div>
        <a href="<?= base_url('receptionist/dashboard') ?>" class="text-slate-400 hover:text-red-500 font-bold text-xs uppercase transition-all">Cancel</a>
    </div>

    <form action="<?= base_url('receptionist/walkin/store') ?>" method="POST" id="walkinForm" class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden mb-10">
        <?= csrf_field() ?>

        <!-- Progress Indicator -->
        <div class="px-8 pt-8">
            <div class="flex gap-2 mb-4">
                <div id="dot1" class="step-dot active"></div>
                <div id="dot2" class="step-dot"></div>
                <div id="dot3" class="step-dot"></div>
            </div>
            <h5 id="stepTitle" class="text-xs font-black text-blue-600 uppercase italic">Step 1: Consultation Details</h5>
        </div>

        <!-- STEP 1: CONSULTATION (DENTIST & SERVICE) -->
        <div id="step1_container" class="step-content p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-6">
                    <div>
                        <label class="fixed-label text-blue-600">Assigned Dentist *</label>
                        <select name="dentist_id" required class="w-full p-3 border rounded-xl text-sm font-bold bg-white focus:ring-2 focus:ring-blue-100 outline-none">
                            <option value="">-- Choose Dentist --</option>
                            <?php foreach ($dentists as $d): ?>
                                <option value="<?= $d['id'] ?>">Dr. <?= esc($d['first_name'] . ' ' . $d['last_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="fixed-label text-slate-500">Reason for Consultation</label>
                        <textarea name="reason_for_consultation" rows="4" placeholder="Chief complaint..." class="w-full p-3 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-100 bg-slate-50"></textarea>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <label class="fixed-label text-indigo-600">Procedures/Services *</label>
                        <button type="button" onclick="addServiceRow()" class="text-[10px] bg-slate-800 text-white px-3 py-1 rounded-full hover:bg-black transition-all">+ Add Service</button>
                    </div>
                    <div id="services_container" class="space-y-3">
                        <div class="service-row flex gap-3 items-start bg-slate-50 p-3 border rounded-xl">
                            <select name="services[]" class="flex-1 p-2 border rounded-lg text-sm bg-white" required>
                                <option value="">-- Select Service --</option>
                                <?php foreach ($services as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= esc($s['service_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" onclick="removeServiceRow(this)" class="text-red-400 p-2 hover:text-red-600">&times;</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end pt-4 border-t">
                <button type="button" onclick="goToStep(2)" class="bg-blue-600 text-white px-10 py-4 rounded-xl font-bold uppercase text-xs shadow-lg hover:bg-blue-700">Next: Patient Information <i class="fas fa-arrow-right ml-2"></i></button>
            </div>
        </div>

        <!-- STEP 2: PATIENT FULL INFORMATION -->
        <div id="step2_container" class="step-content p-8 space-y-8 step-hidden">
            <div class="flex gap-4 p-2 bg-slate-100 rounded-xl w-fit">
                <label class="flex items-center cursor-pointer px-4 py-2 rounded-lg has-[:checked]:bg-white has-[:checked]:shadow-sm transition-all">
                    <input type="radio" name="account_type" value="new" checked onchange="toggleAccountType()" class="hidden">
                    <span class="text-xs font-bold text-slate-600">Register New Patient</span>
                </label>
                <label class="flex items-center cursor-pointer px-4 py-2 rounded-lg has-[:checked]:bg-white has-[:checked]:shadow-sm transition-all">
                    <input type="radio" name="account_type" value="existing" onchange="toggleAccountType()" class="hidden">
                    <span class="text-xs font-bold text-slate-600">Existing Record</span>
                </label>
            </div>

            <div id="existing_patient_div" class="hidden p-6 border-2 border-dashed border-blue-200 rounded-2xl bg-blue-50/30">
                <label class="fixed-label text-blue-600">Search Patient Name</label>
                <select name="patient_id" id="patient_search" class="w-full"></select>
            </div>

            <div id="new_patient_div" class="space-y-6">
                <!-- Personal Details -->
                <div class="form-section">
                    <h5 class="fixed-label text-blue-500 mb-4 border-b pb-2">Personal Details</h5>
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="floating-label-group"><input type="text" name="first_name" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm"><label class="floating-label">First Name *</label></div>
                        <div class="floating-label-group"><input type="text" name="middle_name" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm"><label class="floating-label">Middle Name</label></div>
                        <div class="floating-label-group"><input type="text" name="last_name" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm"><label class="floating-label">Last Name *</label></div>
                        <div class="floating-label-group">
                            <select name="name_suffix" class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white">
                                <option value="" selected disabled hidden></option>
                                <?php foreach (['Jr.', 'Sr.', 'II', 'III', 'IV', 'V'] as $sfx): ?><option value="<?= $sfx ?>"><?= $sfx ?></option><?php endforeach; ?>
                            </select>
                            <label class="floating-label">Suffix</label>
                        </div>
                        <div class="floating-label-group"><input type="text" name="nickname" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm"><label class="floating-label">Nickname</label></div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-2">
                        <div><label class="fixed-label">Birthdate *</label><input type="date" name="birthdate" id="birthdate" onchange="calculateAgeAndMinor()" class="w-full p-2.5 border rounded-lg text-sm"></div>
                        <div><label class="fixed-label text-slate-400">Age</label><input type="text" id="display_age" readonly class="w-full p-2.5 border rounded-lg text-sm bg-slate-50 font-bold"></div>
                        <div>
                            <label class="fixed-label">Gender</label>
                            <select name="gender" class="w-full p-2.5 border rounded-lg text-sm bg-white">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Prefer not to say">Prefer not to say</option>
                            </select>
                        </div>
                        <div>
                            <label class="fixed-label">Civil Status</label>
                            <select name="civil_status" class="w-full p-2.5 border rounded-lg text-sm bg-white">
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                                <option value="Separated">Separated</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Guardian -->
                <div id="minor_section" class="hidden p-6 bg-amber-50 border border-amber-100 rounded-2xl">
                    <h5 class="fixed-label text-amber-600 mb-4 italic">Guardian Information (Required for Minors)</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="floating-label-group"><input type="text" name="guardian_name" id="guardian_name" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Guardian's Full Name</label></div>
                        <div class="floating-label-group"><input type="text" name="guardian_occupation" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Guardian's Occupation</label></div>
                    </div>
                </div>

                <!-- Contact & History -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="form-section space-y-4">
                        <h5 class="fixed-label text-slate-800 border-b pb-2">Contact & History</h5>
                        <div class="floating-label-group"><input type="tel" name="primary_mobile" id="primary_mobile" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm font-bold text-blue-600 bg-white"><label class="floating-label">Mobile Number *</label></div>
                        <div class="floating-label-group"><input type="email" name="email" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Email Address</label></div>
                        <div class="floating-label-group"><input type="text" name="occupation" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Occupation</label></div>
                        <div class="floating-label-group"><input type="text" name="previous_dentist" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Previous Dentist</label></div>
                    </div>

                    <div class="md:col-span-2 form-section space-y-4">
                        <h5 class="fixed-label text-slate-800 border-b pb-2">Complete Address</h5>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="floating-label-group"><input type="text" name="house_number" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm"><label class="floating-label">House #</label></div>
                            <div class="floating-label-group"><input type="text" name="building_name" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm"><label class="floating-label">Building Name</label></div>
                            <div class="floating-label-group"><input type="text" name="street_name" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm"><label class="floating-label">Street</label></div>
                            <div class="floating-label-group"><input type="text" name="subdivision" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm"><label class="floating-label">Subdiv.</label></div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div><label class="fixed-label">Region *</label><select name="region" id="reg_select" onchange="loadAddressLevel('province', this.value)" class="w-full p-2 border rounded text-xs bg-white"></select></div>
                            <div><label class="fixed-label">Province *</label><select name="province" id="prov_select" onchange="loadAddressLevel('city', this.value)" disabled class="w-full p-2 border rounded text-xs bg-white"></select></div>
                            <div><label class="fixed-label">City *</label><select name="city" id="city_select" onchange="loadAddressLevel('barangay', this.value)" disabled class="w-full p-2 border rounded text-xs bg-white"></select></div>
                            <div><label class="fixed-label">Barangay *</label><select name="barangay" id="brgy_select" disabled class="w-full p-2 border rounded text-xs bg-white"></select></div>
                        </div>
                        <div class="floating-label-group mt-2"><input type="text" name="postal_code" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm"><label class="floating-label">Postal Code</label></div>
                    </div>
                </div>

                <!-- Insurance & Referral -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="form-section space-y-4 bg-slate-50 border-slate-200">
                        <h5 class="fixed-label text-slate-800">Referral & Insurance</h5>
                        <div class="floating-label-group"><input type="text" name="referred_by" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Referred By</label></div>
                        <div class="flex items-center gap-3 p-3 bg-white border rounded-xl">
                            <input type="checkbox" name="has_insurance" id="has_insurance" value="1" onchange="toggleInsurance()" class="w-4 h-4">
                            <label for="has_insurance" class="text-xs font-bold text-slate-600 uppercase">With Dental Insurance?</label>
                        </div>
                    </div>
                    <div id="insurance_details" class="md:col-span-2 hidden grid grid-cols-1 md:grid-cols-3 gap-4 p-6 bg-indigo-50/50 border border-indigo-100 rounded-2xl">
                        <div class="floating-label-group"><input type="text" name="insurance_provider" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Provider</label></div>
                        <div class="floating-label-group"><input type="text" name="insurance_policy_number" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Policy #</label></div>
                        <div><label class="fixed-label">Valid Until</label><input type="date" name="insurance_valid_until" class="w-full p-2.5 border rounded-lg text-sm bg-white"></div>
                    </div>
                </div>
            </div>

            <div class="flex justify-between pt-4 border-t gap-4">
                <button type="button" onclick="goToStep(1)" class="w-1/3 bg-slate-100 text-slate-600 py-4 rounded-xl font-bold uppercase text-xs">Back</button>
                <button type="button" onclick="goToStep(3)" class="w-2/3 bg-blue-600 text-white py-4 rounded-xl font-bold uppercase text-xs shadow-lg">Next: Medical Records <i class="fas fa-arrow-right ml-2"></i></button>
            </div>
        </div>

        <!-- STEP 3: MEDICAL HISTORY -->
        <div id="step3_container" class="step-content p-8 space-y-6 step-hidden">
            <div class="form-section">
                <h5 class="fixed-label text-blue-600 mb-4 border-b pb-2">Physician & Vitals</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="floating-label-group"><input type="text" name="physician_name" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Physician’s Name</label></div>
                        <div class="floating-label-group"><input type="text" name="physician_phone" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Tel. No.</label></div>
                        <div class="floating-label-group md:col-span-2"><input type="text" name="physician_address" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Office Address</label></div>
                    </div>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="floating-label-group">
                                <select name="blood_type" class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white">
                                    <option value="Unknown" selected>Unknown</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                </select><label class="floating-label">Blood Type</label>
                            </div>
                            <div class="flex gap-1">
                                <input type="number" name="blood_pressure_systolic" placeholder="Sys" class="w-1/2 p-2.5 border rounded-lg text-sm">
                                <span class="pt-2 text-slate-400">/</span>
                                <input type="number" name="blood_pressure_diastolic" placeholder="Dia" class="w-1/2 p-2.5 border rounded-lg text-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h5 class="fixed-label text-slate-800 mb-4 border-b pb-2">Health Questionnaire</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <?php
                        $qs = [
                            'is_good_health' => '1. Are you in good health?',
                            'is_under_medical_treatment' => '2. Are you under medical treatment now?',
                            'has_serious_illness' => '3. Have serious illness or surgical operation?',
                            'is_hospitalized' => '4. Have you ever been hospitalized?',
                            'is_taking_medication' => '5. Are you taking any medication?',
                            'uses_tobacco' => '6. Do you use tobacco products?',
                            'uses_drugs' => '7. Use alcohol, cocaine or dangerous drugs?'
                        ];
                        foreach ($qs as $key => $label): ?>
                            <div class="flex justify-between items-center py-1 border-b border-slate-50">
                                <span class="text-xs text-slate-600"><?= $label ?></span>
                                <div class="flex gap-4">
                                    <label class="text-xs font-bold"><input type="radio" name="<?= $key ?>" value="1"> Yes</label>
                                    <label class="text-xs font-bold"><input type="radio" name="<?= $key ?>" value="0" checked> No</label>
                                </div>
                            </div>
                            <?php if ($key == 'has_serious_illness'): ?>
                                <input type="text" name="serious_illness_details" placeholder="If so, what illness or operation?" class="w-full p-2 border-b bg-transparent text-xs outline-none focus:border-blue-500 mb-2">
                            <?php elseif ($key == 'is_hospitalized'): ?>
                                <input type="text" name="hospitalization_details" placeholder="If so, when and why?" class="w-full p-2 border-b bg-transparent text-xs outline-none focus:border-blue-500 mb-2">
                            <?php elseif ($key == 'is_taking_medication'): ?>
                                <input type="text" name="medication_details" placeholder="If so, please specify..." class="w-full p-2 border-b bg-transparent text-xs outline-none focus:border-blue-500 mb-2">
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="space-y-4">
                        <div class="p-4 bg-pink-50 border border-pink-100 rounded-2xl">
                            <p class="text-[10px] font-black text-pink-700 uppercase mb-3">10. For Women Only</p>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center"><span class="text-xs">Are you pregnant?</span>
                                    <div class="flex gap-3"><label class="text-xs font-bold"><input type="radio" name="is_pregnant" value="1"> Yes</label><label class="text-xs font-bold"><input type="radio" name="is_pregnant" value="0" checked> No</label></div>
                                </div>
                                <div class="flex justify-between items-center"><span class="text-xs">Are you nursing?</span>
                                    <div class="flex gap-3"><label class="text-xs font-bold"><input type="radio" name="is_nursing" value="1"> Yes</label><label class="text-xs font-bold"><input type="radio" name="is_nursing" value="0" checked> No</label></div>
                                </div>
                                <div class="flex justify-between items-center"><span class="text-xs">Taking birth control pills?</span>
                                    <div class="flex gap-3"><label class="text-xs font-bold"><input type="radio" name="is_taking_birth_control" value="1"> Yes</label><label class="text-xs font-bold"><input type="radio" name="is_taking_birth_control" value="0" checked> No</label></div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="fixed-label text-[10px]">9. Bleeding Time</label>
                            <div class="flex gap-1">
                                <!-- Input Number -->
                                <input type="number"
                                    name="bleeding_time_value"
                                    placeholder="0"
                                    class="w-2/3 p-2.5 border rounded-lg text-sm"
                                    min="0">

                                <!-- Dropdown for Units -->
                                <select name="bleeding_time_unit" class="w-1/3 p-2.5 border rounded-lg text-sm bg-white">
                                    <option value="secs">Secs</option>
                                    <option value="mins" selected>Mins</option>
                                    <option value="hours">Hours</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 border-t pt-6">
                    <h5 class="fixed-label text-blue-600 mb-4 font-black">Medical History Checklist:</h5>
                    <div class="medical-grid bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <?php
                        $grouped = [];
                        $manualAllergies = [
                            'Local Anesthetic',
                            'Penicillin, Antibiotics',
                            'Latex',
                            'Sulfa Drugs',
                            'Aspirin'
                        ];
                        foreach ($medical_conditions as $mc) {
                            if (in_array($mc['condition_label'], $manualAllergies)) {
                                continue;
                            }
                            if (strtolower($mc['category']) == 'allergy' || strtolower($mc['category']) == 'allergies') {
                                continue;
                            }
                            $grouped[$mc['category']][] = $mc;
                        }
                        foreach ($grouped as $cat => $conditions): ?>
                            <div class="col-span-full text-[9px] font-black text-slate-400 uppercase mt-2 mb-1 border-b border-slate-100"><?= esc($cat) ?></div>
                            <?php foreach ($conditions as $c): ?>
                                <label class="flex items-center text-[11px] text-slate-600 cursor-pointer py-1 hover:text-blue-600">
                                    <input type="checkbox" name="medical_conditions[]" value="<?= esc($c['condition_key']) ?>" class="mr-2 rounded">
                                    <?= esc($c['condition_label']) ?>
                                </label>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="flex justify-between pt-4 border-t gap-4">
                <button type="button" onclick="goToStep(2)" class="w-1/3 bg-slate-100 text-slate-600 py-4 rounded-xl font-bold uppercase text-xs">Back</button>
                <button type="submit" class="w-2/3 bg-green-600 text-white py-4 rounded-xl font-bold uppercase tracking-widest hover:bg-green-700 shadow-xl transition-all">Confirm & Queue Patient</button>
            </div>
        </div>
    </form>
</div>

<script>
    const BASE_URL = "<?= base_url('data/ph-addresses/') ?>";

    $(document).ready(function() {
        loadAddressLevel('region');
        setupMobileInput();

        $('#patient_search').select2({
            placeholder: 'Search name...',
            minimumInputLength: 2,
            ajax: {
                url: '<?= base_url('receptionist/appointments/searchPatients') ?>',
                dataType: 'json',
                delay: 250,
                processResults: data => ({
                    results: data
                })
            }
        });
    });

    function goToStep(step) {
        if (step === 2) {
            const dentist = document.querySelector('select[name="dentist_id"]').value;
            if (!dentist) {
                alert("Please assign a dentist first.");
                return;
            }
        }
        $('.step-content').addClass('step-hidden');
        $(`#step${step}_container`).removeClass('step-hidden');
        $('.step-dot').removeClass('active');
        for (let i = 1; i <= step; i++) $(`#dot${i}`).addClass('active');
        const titles = ["Consultation Details", "Patient Full Information", "Medical & Vitals Record"];
        $('#stepTitle').text(`Step ${step}: ${titles[step-1]}`);
        window.scrollTo(0, 0);
    }

    function toggleAccountType() {
        const isExisting = $('input[name="account_type"]:checked').val() === 'existing';
        $('#existing_patient_div').toggleClass('hidden', !isExisting);
        $('#new_patient_div').toggleClass('hidden', isExisting);
    }

    function calculateAgeAndMinor() {
        const bdateInput = document.getElementById('birthdate').value;
        if (!bdateInput) return;
        const birthDate = new Date(bdateInput);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        if (today.getMonth() < birthDate.getMonth() || (today.getMonth() === birthDate.getMonth() && today.getDate() < birthDate.getDate())) age--;
        document.getElementById('display_age').value = age;
        const minorSection = document.getElementById('minor_section');
        if (age < 18 && age >= 0) {
            minorSection.classList.remove('hidden');
            document.getElementById('guardian_name').setAttribute('required', 'required');
        } else {
            minorSection.classList.add('hidden');
            document.getElementById('guardian_name').removeAttribute('required');
        }
    }

    function toggleInsurance() {
        $('#insurance_details').toggleClass('hidden', !$('#has_insurance').is(':checked'));
    }

    function addServiceRow() {
        const row = $('.service-row').first().clone();
        row.find('select').val('');
        $('#services_container').append(row);
    }

    function removeServiceRow(btn) {
        if ($('.service-row').length > 1) $(btn).closest('.service-row').remove();
    }

    function setupMobileInput() {
        $('#primary_mobile').on('input', function() {
            let val = this.value.replace(/\D/g, '').substring(0, 11);
            if (val.length > 4 && val.length <= 7) val = val.slice(0, 4) + ' ' + val.slice(4);
            else if (val.length > 7) val = val.slice(0, 4) + ' ' + val.slice(4, 7) + ' ' + val.slice(7);
            this.value = val;
        });
    }

    const ADDRESS_CONFIG = {
        region: {
            selectId: 'reg_select',
            file: 'region.json',
            next: 'province',
            display: 'region_name',
            value: 'region_code'
        },
        province: {
            selectId: 'prov_select',
            file: 'province.json',
            next: 'city',
            filter: 'region_code',
            display: 'province_name',
            value: 'province_code'
        },
        city: {
            selectId: 'city_select',
            file: 'city.json',
            next: 'barangay',
            filter: 'province_code',
            display: 'city_name',
            value: 'city_code'
        },
        barangay: {
            selectId: 'brgy_select',
            file: 'barangay.json',
            filter: 'city_code',
            display: 'brgy_name',
            value: 'brgy_code'
        }
    };

    async function loadAddressLevel(level, parentValue = null) {
        const cfg = ADDRESS_CONFIG[level];
        const select = document.getElementById(cfg.selectId);
        if (!select) return;

        select.disabled = true;
        select.innerHTML = '<option value="" selected disabled>Loading...</option>';

        try {
            const res = await fetch(`${BASE_URL}${cfg.file}`);
            const data = await res.json();

            // I-filter ang data gamit ang Code (parentValue)
            const filtered = cfg.filter ? data.filter(item => item[cfg.filter] == parentValue) : data;

            select.innerHTML = '<option value="" selected disabled>-- Select --</option>';
            filtered.forEach(item => {
                // Mahalaga: value ay Code, text ay Name. 
                // Magpapalit lang tayo sa 'submit' event sa taas.
                select.add(new Option(item[cfg.display], item[cfg.value]));
            });

            select.disabled = false;
        } catch (err) {
            console.error(`Error loading ${level}:`, err);
            select.innerHTML = '<option value="" selected disabled>Error loading</option>';
        }
    }

    document.getElementById('walkinForm').addEventListener('submit', function(e) {
        // 1. Linisin ang Mobile Number (Alisin ang spaces: "0912 345 6789" -> "09123456789")
        let mobile = document.getElementById('primary_mobile');
        if (mobile) {
            mobile.value = mobile.value.replace(/\s/g, '');
        }

        // 2. Palitan ang VALUE (Code) ng TEXT (Name) para sa Address
        const addressIds = ['reg_select', 'prov_select', 'city_select', 'brgy_select'];

        addressIds.forEach(id => {
            const sel = document.getElementById(id);
            if (sel && sel.selectedIndex > 0) {
                // Kinukuha ang nababasa nating text (hal. "CAVITE") 
                // at sineset ito bilang value na matatanggap ng PHP
                sel.options[sel.selectedIndex].value = sel.options[sel.selectedIndex].text;
            }
        });

        // Sa puntong ito, ang form ay magpapatuloy sa pag-submit gamit ang NAMES.
    });
</script>
<?= $this->endSection() ?>