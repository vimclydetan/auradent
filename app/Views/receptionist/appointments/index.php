<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<!-- SELECT2 & JQUERY -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    .floating-label-group {
        position: relative;
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
        font-size: 0.70rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        margin-bottom: 0.25rem;
        display: block;
    }

    select.floating-input {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
    }

    .password-toggle {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #94a3b8;
        z-index: 10;
    }

    .password-toggle:hover {
        color: #2563eb;
    }

    .select2-container--default .select2-selection--single {
        height: 46px !important;
        display: flex;
        align-items: center;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.5rem !important;
        background-color: white !important;
        padding-left: 0.5rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #1e293b !important;
        font-size: 0.875rem !important;
        font-weight: 500;
    }

    .select2-dropdown {
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.5rem !important;
        z-index: 9999 !important;
    }

    .filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 1.25rem;
        border-radius: 1rem;
        margin-bottom: 1.5rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
    }

    .filter-input {
        width: 100%;
        padding: 0.6rem 0.75rem;
        border-radius: 0.5rem;
        border: 1px solid #cbd5e1;
        font-size: 0.875rem;
        outline: none;
    }

    .filter-input:focus {
        border-color: #2563eb;
        ring: 2px;
        ring-color: #bfdbfe;
    }

    .step-hidden {
        display: none;
    }

    .medical-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 0.5rem;
    }

    .step-indicator {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
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
</style>

<!-- ALERTS -->
<?php if (session()->getFlashdata('success')): ?>
    <div role="alert" class="mb-4 p-4 bg-green-50 text-green-800 rounded-lg border border-green-200 shadow-sm flex items-start gap-3">
        <!-- Icon -->
        <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <!-- Message -->
        <span class="text-sm font-medium"><?= session()->getFlashdata('success') ?></span>
    </div>
<?php endif; ?>

<div class="flex justify-between items-center mb-6">
    <h3 class="text-2xl font-bold text-slate-800">📅 Appointments</h3>
    <button onclick="document.getElementById('apptModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 shadow-lg font-bold transition-all active:scale-95">
        <i class="fas fa-plus mr-2"></i> Book Appointment
    </button>
</div>

<!-- FILTERS -->
<div class="filter-card shadow-sm">
    <div class="space-y-1">
        <label class="text-[10px] font-bold text-slate-500 uppercase">Search Patient / Service</label>
        <input type="text" id="tableSearch" placeholder="Type name..." class="filter-input">
    </div>
    <div class="space-y-1">
        <label class="text-[10px] font-bold text-slate-500 uppercase">Status</label>
        <select id="statusFilter" class="filter-input">
            <option value="">All Status</option>
            <option value="Pending">Pending</option>
            <option value="Confirmed">Confirmed</option>
            <option value="Completed">Completed</option>
            <option value="Cancelled">Cancelled</option>
        </select>
    </div>
    <div class="space-y-1">
        <label class="text-[10px] font-bold text-slate-500 uppercase">Dentist</label>
        <!-- Hanapin ang id="dentistFilter" at palitan ng ganito: -->
        <select id="dentistFilter" class="filter-input">
            <option value="">All Dentists</option>
            <?php foreach ($dentists as $d): ?>
                <option value="<?= $d['id'] ?>">Dr. <?= esc($d['first_name'] . ' ' . $d['last_name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="space-y-1">
        <label class="text-[10px] font-bold text-slate-500 uppercase">Filter Date</label>
        <input type="date" id="dateFilter" class="filter-input">
    </div>
    <div class="flex items-end">
        <button onclick="resetFilters()" class="w-full text-xs font-bold text-red-500 hover:bg-red-50 py-2.5 rounded-lg border border-red-200 transition-all uppercase">Reset</button>
    </div>
</div>

<!-- TABLE -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-bold">
            <tr>
                <th class="p-4 border-b">Patient</th>
                <th class="p-4 border-b">Dentist</th>
                <th class="p-4 border-b">Schedule</th>
                <th class="p-4 border-b">Service</th>
                <th class="p-4 border-b text-center">Status</th>
                <th class="p-4 border-b text-center">Actions</th>
            </tr>
        </thead>
        <tbody id="appointmentTable" class="text-sm divide-y divide-slate-100">
            <?php foreach ($appointments as $a): ?>
                <tr class="appt-row hover:bg-slate-50/50"
                    data-status="<?= $a['status'] ?>"
                    data-dentist="<?= $a['dentist_id'] ?>"
                    data-date="<?= $a['appointment_date'] ?>">
                    <td class="p-4 font-bold text-slate-700"><?= esc($a['patient_name']) ?></td>
                    <td class="p-4 font-medium text-blue-600"> <?= esc($a['dentist_name'] ?: 'N/A') ?></td>
                    <td class="p-4">
                        <div class="text-xs font-bold text-slate-700"><?= $a['fmt_date'] ?? date('M d, Y', strtotime($a['appointment_date'])) ?></div>
                        <div class="text-[10px] text-blue-600 italic"><?= $a['fmt_time'] ?? date('h:i A', strtotime($a['appointment_time'])) ?> - <?= $a['fmt_end'] ?? date('h:i A', strtotime($a['end_time'])) ?></div>
                    </td>
                    <td class="p-4 font-medium text-slate-600"><?= esc($a['service_name']) ?></td>
                    <td class="p-4 text-center">
                        <?php $c = ['Pending' => 'amber', 'Confirmed' => 'blue', 'Completed' => 'green', 'Cancelled' => 'red'][$a['status']]; ?>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-<?= $c ?>-100 text-<?= $c ?>-700"><?= $a['status'] ?></span>
                    </td>
                    <td class="p-4 text-center">
                        <div class="flex justify-center gap-2">
                            <button onclick='viewAppointment(<?= json_encode($a) ?>)' class="bg-slate-100 text-slate-600 px-3 py-1.5 rounded-lg font-bold text-[10px] uppercase hover:bg-slate-800 hover:text-white transition-all">View</button>

                            <?php if ($a['status'] === 'Pending'): ?>
                                <a href="<?= base_url('receptionist/appointments/status/' . $a['id'] . '/Confirmed') ?>" onclick="return confirm('Confirm?')" class="bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg font-bold text-[10px] uppercase hover:bg-blue-600 hover:text-white transition-colors">Confirm</a>
                                <button onclick="openRescheduleModal(<?= $a['id'] ?>, 
                                    '<?= $a['appointment_date'] ?>', 
                                    '<?= $a['appointment_time'] ?>', 
                                    '<?= $a['end_date'] ?>', 
                                    '<?= $a['end_time'] ?>', 
                                    '<?= $a['dentist_id'] ?>')"
                                    class="bg-amber-50 text-amber-600 px-3 py-1.5 rounded-lg font-bold text-[10px] uppercase hover:bg-amber-500 hover:text-white transition-colors">Reschedule
                                </button>
                                <a href="<?= base_url('receptionist/appointments/status/' . $a['id'] . '/Cancelled') ?>" onclick="return confirm('Cancel appointment?')" class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg font-bold text-[10px] uppercase hover:bg-red-600 hover:text-white transition-colors">Cancel</a>
                            <?php elseif ($a['status'] === 'Confirmed'): ?>
                                <a href="<?= base_url('receptionist/appointments/status/' . $a['id'] . '/Completed') ?>" onclick="return confirm('Mark as Completed?')" class="bg-green-600 text-white px-3 py-1.5 rounded-lg font-bold text-[10px] uppercase hover:bg-green-700 shadow-sm transition-all">Done</a>
                                <a href="<?= base_url('receptionist/appointments/status/' . $a['id'] . '/Cancelled') ?>" onclick="return confirm('Cancel appointment?')" class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg font-bold text-[10px] uppercase hover:bg-red-600 hover:text-white transition-colors">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- BOOKING MODAL -->
<div id="apptModal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-start justify-center p-4 z-50 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl my-4 flex flex-col overflow-hidden">
        <div class="p-4 border-b flex justify-between items-center bg-slate-50">
            <h4 class="font-bold text-lg text-slate-800 uppercase">Book New Appointment</h4>
            <button onclick="closeModal()" class="text-slate-400 text-2xl hover:text-slate-600">&times;</button>
        </div>

        <form action="<?= base_url('receptionist/appointments/store') ?>" method="POST" id="apptForm">
            <?= csrf_field() ?>

            <div class="px-6 pt-4">
                <div class="step-indicator">
                    <div id="dot1" class="step-dot active"></div>
                    <div id="dot2" class="step-dot"></div>
                </div>
                <h5 id="stepTitle" class="text-xs font-black text-blue-600 uppercase italic">Step 1: Appointment Details</h5>
            </div>

            <!-- ERROR DISPLAY -->
            <?php if (session()->getFlashdata('error') || session()->getFlashdata('validation_errors')): ?>
                <div class="m-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm text-xs">
                    <p class="font-bold mb-1 uppercase tracking-widest">Kailangang Ayusin:</p>
                    <ul class="list-disc ml-5">
                        <?php if (session()->getFlashdata('error')): ?><li><?= session()->getFlashdata('error') ?></li><?php endif; ?>
                        <?php if (session()->getFlashdata('validation_errors')): ?>
                            <?php foreach (session()->getFlashdata('validation_errors') as $err): ?><li><?= esc($err) ?></li><?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div id="step1_container" class="p-6 space-y-8">

                <div class="p-6 space-y-8">
                    <!-- 1. ACCOUNT TYPE & DENTIST -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <label class="fixed-label text-blue-600">Patient Type</label>
                            <div class="flex gap-4 p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="account_type" value="existing" <?= old('account_type', 'existing') === 'existing' ? 'checked' : '' ?> onchange="toggleAccountType()" class="w-4 h-4 text-blue-600">
                                    <span class="ml-2 text-sm font-medium">Existing Patient</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="account_type" value="new" <?= old('account_type') === 'new' ? 'checked' : '' ?> onchange="toggleAccountType()" class="w-4 h-4 text-blue-600">
                                    <span class="ml-2 text-sm font-bold text-blue-600 underline">New Patient Registration</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="fixed-label text-indigo-600">Assign Dentist *</label>
                            <select name="dentist_id" required class="w-full p-3 border rounded-xl text-sm font-bold bg-white">
                                <option value="">-- Choose Dentist --</option>
                                <?php foreach ($dentists as $d): ?>
                                    <option value="<?= $d['id'] ?>" <?= old('dentist_id') == $d['id'] ? 'selected' : '' ?>><?= esc($d['first_name'] . ' ' . $d['last_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- EXISTING PATIENT SEARCH -->
                    <div id="existing_patient_div" class="<?= old('account_type', 'existing') === 'new' ? 'hidden' : '' ?> space-y-1">
                        <label class="fixed-label text-blue-600">Search Patient Name</label>
                        <select name="patient_id" id="patient_search" class="w-full">
                            <?php if (old('patient_id')): ?>
                                <option value="<?= old('patient_id') ?>" selected>Patient Selected (Record ID: #<?= old('patient_id') ?>)</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- NEW PATIENT FORM -->
                    <div id="new_patient_div" class="<?= old('account_type') === 'new' ? '' : 'hidden' ?> space-y-6">
                        <div class="p-5 bg-blue-50/50 border border-blue-100 rounded-2xl space-y-6">
                            <h5 class="text-xs font-black text-blue-500 uppercase italic">Step 1: Personal Info</h5>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="floating-label-group md:col-span-1"><input type="text" name="first_name" value="<?= old('first_name') ?>" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">First Name *</label></div>
                                <div class="floating-label-group md:col-span-1"><input type="text" name="middle_name" value="<?= old('middle_name') ?>" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Middle Name</label></div>
                                <div class="floating-label-group md:col-span-1"><input type="text" name="last_name" value="<?= old('last_name') ?>" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Last Name *</label></div>
                                <select name="name_suffix" class="w-full p-2.5 border rounded-lg text-sm bg-white">
                                    <option value="">No Suffix</option>
                                    <?php foreach (['Jr.', 'Sr.', 'III', 'IV', 'V'] as $sfx): ?><option value="<?= $sfx ?>" <?= old('name_suffix') == $sfx ? 'selected' : '' ?>><?= $sfx ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div><label class="fixed-label text-slate-500">Birthdate *</label><input type="date" name="birthdate" value="<?= old('birthdate') ?>" class="w-full p-2 border rounded-lg text-sm"></div>
                                <div><label class="fixed-label text-slate-500">Gender</label><select name="gender" class="w-full p-2.5 border rounded-lg text-sm">
                                        <option value="Male" <?= old('gender') == 'Male' ? 'selected' : '' ?>>Male</option>
                                        <option value="Female" <?= old('gender') == 'Female' ? 'selected' : '' ?>>Female</option>
                                    </select></div>
                                <div class="floating-label-group"><input type="tel"
                                        name="primary_mobile"
                                        id="primary_mobile"
                                        value="<?= old('primary_mobile') ?>"
                                        pattern="^09\d{2}\s\d{3}\s\d{4}$"
                                        maxlength="13"
                                        placeholder=" " style="margin-top: 1.3rem;" class="floating-input w-full p-2.5 border rounded-lg text-sm font-bold text-blue-600"><label class="floating-label" style="margin-top: 1rem;">Mobile Number *</label></div>
                            </div>
                            <!-- ADDRESS SECTION -->
                            <h5 class="text-xs font-black text-blue-500 uppercase italic">Step 2: PH Address</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="floating-label-group"><select name="region" id="reg_select" onchange="loadAddressLevel('province', this.value)" class="floating-input w-full p-2.5 border rounded-lg text-sm">
                                        <option value="" selected disabled></option>
                                    </select><label class="floating-label">Region *</label></div>
                                <div class="floating-label-group"><select name="province" id="prov_select" onchange="loadAddressLevel('city', this.value)" disabled class="floating-input w-full p-2.5 border rounded-lg text-sm">
                                        <option value="" selected disabled></option>
                                    </select><label class="floating-label">Province *</label></div>
                                <div class="floating-label-group"><select name="city" id="city_select" onchange="loadAddressLevel('barangay', this.value)" disabled class="floating-input w-full p-2.5 border rounded-lg text-sm">
                                        <option value="" selected disabled></option>
                                    </select><label class="floating-label">City/Municipality *</label></div>
                                <div class="floating-label-group"><select name="barangay" id="brgy_select" disabled class="floating-input w-full p-2.5 border rounded-lg text-sm">
                                        <option value="" selected disabled></option>
                                    </select><label class="floating-label">Barangay *</label></div>
                            </div>
                            <h5 class="text-xs font-black text-blue-500 uppercase italic">Step 3: Security</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="floating-label-group"><input type="text" name="username" value="<?= old('username') ?>" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Username *</label></div>
                                <div class="floating-label-group"><input type="email" name="email" value="<?= old('email') ?>" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Email Address *</label></div>
                                <div class="floating-label-group relative"><input type="password" name="password" id="reg_pass" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Password *</label><i class="fas fa-eye password-toggle" onclick="togglePass('reg_pass', this)"></i></div>
                                <div class="floating-label-group relative"><input type="password" name="confirm_password" id="reg_confirm" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Confirm Password *</label><i class="fas fa-eye password-toggle" onclick="togglePass('reg_confirm', this)"></i></div>
                            </div>
                        </div>
                    </div>

                    <!-- SERVICES -->
                    <div class="space-y-4">
                        <div class="flex justify-between items-center"><label class="fixed-label text-blue-600">Selected Services *</label><button type="button" onclick="addServiceRow()" class="text-[10px] bg-slate-800 text-white px-3 py-1 rounded-full hover:bg-black transition-all">+ Add Service</button></div>
                        <div id="services_container" class="space-y-3">
                            <?php
                            $hasLevelsMap = [];
                            foreach ($services as $s) {
                                $hasLevelsMap[$s['id']] = $s['has_levels'];
                            }
                            $oldS = old('services', ['']);
                            $oldL = old('levels', []);
                            foreach ($oldS as $idx => $val):
                                $showL = isset($hasLevelsMap[$val]) && $hasLevelsMap[$val] == '1';
                            ?>
                                <div class="service-row flex gap-3 items-start bg-slate-50 p-3 border rounded-xl">
                                    <select name="services[]" onchange="checkLevels(this)" class="flex-1 p-2.5 border rounded-lg text-sm bg-white" required>
                                        <option value="">-- Choose Service --</option>
                                        <?php foreach ($services as $s): ?>
                                            <option value="<?= $s['id'] ?>" data-haslevels="<?= $s['has_levels'] ?>" <?= $val == $s['id'] ? 'selected' : '' ?>><?= $s['service_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="flex-1 level-div <?= $showL ? '' : 'hidden' ?>">
                                        <select name="levels[]" class="w-full p-2.5 border rounded-lg text-sm bg-white">
                                            <option value="Standard" <?= (isset($oldL[$idx]) && $oldL[$idx] == 'Standard') ? 'selected' : '' ?>>Standard</option>
                                            <option value="Moderate" <?= (isset($oldL[$idx]) && $oldL[$idx] == 'Moderate') ? 'selected' : '' ?>>Moderate</option>
                                        </select>
                                    </div>
                                    <button type="button" onclick="removeServiceRow(this)" class="text-red-400 p-2.5 hover:text-red-600">&times;</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- FINAL SCHEDULE -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-900 p-6 rounded-2xl">
                        <div>
                            <label class="fixed-label text-slate-400 italic">Start Appointment</label>
                            <div class="flex gap-2">
                                <input type="date" name="appointment_date" value="<?= old('appointment_date') ?>" min="<?= date('Y-m-d') ?>" class="w-full p-2.5 rounded-lg text-sm bg-slate-800 text-white border-slate-700" required>
                                <input type="time" name="appointment_time" value="<?= old('appointment_time') ?>" class="w-full p-2.5 rounded-lg text-sm bg-slate-800 text-white border-slate-700" required>
                            </div>
                        </div>
                        <div>
                            <label class="fixed-label text-slate-400 italic">Expected End</label>
                            <div class="flex gap-2">
                                <input type="date" name="end_date" value="<?= old('end_date') ?>" min="<?= date('Y-m-d') ?>" class="w-full p-2.5 rounded-lg text-sm bg-slate-800 text-white border-slate-700" required>
                                <input type="time" name="end_time" value="<?= old('end_time') ?>" class="w-full p-2.5 rounded-lg text-sm bg-slate-800 text-white border-slate-700" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t bg-slate-50"><button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold uppercase tracking-widest hover:bg-blue-700 shadow-xl transition-all">Confirm Appointment</button></div>
                <div class="pt-4 flex justify-end">
                    <button type="button" onclick="goToStep(2)" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold uppercase text-xs shadow-lg">Next: Medical History <i class="fas fa-arrow-right ml-2"></i></button>
                </div>
            </div>
            <!-- PAGE 2: MEDICAL HISTORY -->
            <div id="step2_container" class="p-6 space-y-6 step-hidden">
                <div class="bg-blue-50/50 p-6 border border-blue-100 rounded-2xl space-y-6">

                    <!-- Physician Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="floating-label-group"><input type="text" name="physician_name" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Physician’s Name</label></div>
                        <div class="floating-label-group"><input type="text" name="physician_specialty" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Speciality</label></div>
                        <div class="floating-label-group"><input type="text" name="physician_address" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Office Address</label></div>
                        <div class="floating-label-group"><input type="text" name="physician_phone" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Tel. No.</label></div>
                    </div>

                    <hr class="border-blue-100">

                    <!-- Questions 1-7 -->
                    <div class="space-y-4 text-sm">
                        <?php
                        $qs = [
                            'is_good_health' => '1. Are you in good health?',
                            'is_under_medical_treatment' => '2. Are you under medical treatment now?',
                            'has_serious_illness' => '3. Have you ever had serious illness or surgical operation?',
                            'is_hospitalized' => '4. Have you ever been hospitalized?',
                            'is_taking_medication' => '5. Are you taking any medication?',
                            'uses_tobacco' => '6. Do you use tobacco products?',
                            'uses_drugs' => '7. Do you use alcohol, cocaine or other dangerous drugs?'
                        ];
                        foreach ($qs as $key => $label): ?>
                            <div class="flex justify-between items-start gap-4">
                                <label class="font-medium text-slate-700"><?= $label ?></label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-1 cursor-pointer font-bold"><input type="radio" name="<?= $key ?>" value="1"> Yes</label>
                                    <label class="flex items-center gap-1 cursor-pointer font-bold"><input type="radio" name="<?= $key ?>" value="0" checked> No</label>
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

                        <!-- QUESTION 8: ALLERGIES (MANUAL LIST) -->
                        <div class="space-y-2 pt-2">
                            <label class="font-bold text-slate-800">8. Are you allergic to any of the following?</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 pl-4">
                                <!-- Ang values dito dapat tumutugma sa condition_key sa database mo -->
                                <label class="flex items-center text-xs cursor-pointer"><input type="checkbox" name="medical_conditions[]" value="local_anesthetic" class="mr-2"> Local Anesthetic</label>
                                <label class="flex items-center text-xs cursor-pointer"><input type="checkbox" name="medical_conditions[]" value="penicillin_antibiotics" class="mr-2"> Penicillin, Antibiotics</label>
                                <label class="flex items-center text-xs cursor-pointer"><input type="checkbox" name="medical_conditions[]" value="latex" class="mr-2"> Latex</label>
                                <label class="flex items-center text-xs cursor-pointer"><input type="checkbox" name="medical_conditions[]" value="sulfa_drugs" class="mr-2"> Sulfa Drugs</label>
                                <label class="flex items-center text-xs cursor-pointer"><input type="checkbox" name="medical_conditions[]" value="aspirin" class="mr-2"> Aspirin</label>

                                <div class="flex items-center gap-2 md:col-span-2">
                                    <span class="text-xs">Others:</span>
                                    <!-- PINALITAN: name="other_allergy" at tinanggal ang [] -->
                                    <input type="text" name="other_allergy" placeholder="Specify other allergies..." class="flex-1 border-b text-xs outline-none bg-transparent focus:border-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- QUESTION 9: BLEEDING TIME -->
                        <div class="flex items-center gap-3 pt-2">
                            <label class="font-bold text-slate-800">9. Bleeding Time:</label>
                            <input type="text" name="bleeding_time" class="flex-1 border-b bg-transparent text-sm outline-none focus:border-blue-500">
                        </div>

                        <!-- QUESTION 10: FOR WOMEN -->
                        <div id="women_section" class="bg-pink-50/50 p-4 rounded-xl border border-pink-100 space-y-3 transition-all">
                            <label class="font-bold text-pink-700 uppercase text-xs">10. For Women Only:</label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="flex gap-3 items-center text-xs">
                                    <span>Are you pregnant?</span>
                                    <div class="flex gap-2">
                                        <label><input type="radio" name="is_pregnant" value="1"> Yes</label>
                                        <label><input type="radio" name="is_pregnant" value="0" checked> No</label>
                                    </div>
                                </div>
                                <div class="flex gap-3 items-center text-xs">
                                    <span>Are you nursing?</span>
                                    <div class="flex gap-2">
                                        <label><input type="radio" name="is_nursing" value="1"> Yes</label>
                                        <label><input type="radio" name="is_nursing" value="0" checked> No</label>
                                    </div>
                                </div>
                                <div class="flex gap-3 items-center text-xs">
                                    <span>Taking birth control pills?</span>
                                    <div class="flex gap-2">
                                        <label><input type="radio" name="is_taking_birth_control" value="1"> Yes</label>
                                        <label><input type="radio" name="is_taking_birth_control" value="0" checked> No</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Vitals Section -->
                        <!-- Vitals Section -->
                        <div class="grid grid-cols-2 gap-4 pt-2">
                            <!-- BLOOD TYPE DROPDOWN -->
                            <div class="floating-label-group">
                                <select name="blood_type" class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white">
                                    <option value="" selected disabled hidden></option> <!-- Placeholder logic -->
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                    <option value="Unknown">Unknown</option>
                                </select>
                                <label class="floating-label">Blood Type</label>
                            </div>

                            <!-- BLOOD PRESSURE INPUTS -->
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="blood_pressure_systolic" placeholder="Systolic" class="p-2.5 border rounded-lg text-sm bg-white">
                                <input type="number" name="blood_pressure_diastolic" placeholder="Diastolic" class="p-2.5 border rounded-lg text-sm bg-white">
                            </div>
                        </div>
                    </div>

                    <!-- DYNAMIC MEDICAL CONDITIONS CHECKLIST -->
                    <!-- DYNAMIC MEDICAL CONDITIONS CHECKLIST -->
                    <div class="border-t pt-4">
                        <label class="fixed-label text-blue-600 mb-3 font-black">Medical History Checklist (Please check all that apply):</label>
                        <div class="medical-grid bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <?php
                            $grouped = [];

                            // Eto yung listahan ng mga labels na manually na nating nilagay sa Question #8
                            // Siguraduhin na parehas ang spelling nito sa database 'condition_label' mo
                            $manualAllergies = [
                                'Local Anesthetic',
                                'Penicillin, Antibiotics',
                                'Latex',
                                'Sulfa Drugs',
                                'Aspirin'
                            ];

                            foreach ($medical_conditions as $mc) {
                                // STEP 1: I-skip kung ang label ay nasa manual list sa Question 8
                                if (in_array($mc['condition_label'], $manualAllergies)) {
                                    continue;
                                }

                                // STEP 2: I-skip din kung ang category ay 'allergy' o 'allergies' (kung meron mang ganung category sa DB mo)
                                if (strtolower($mc['category']) == 'allergy' || strtolower($mc['category']) == 'allergies') {
                                    continue;
                                }

                                $grouped[$mc['category']][] = $mc;
                            }

                            // I-loop na ang mga natirang conditions (Cardio, Respiratory, etc.)
                            foreach ($grouped as $cat => $conditions): ?>
                                <div class="col-span-full text-[10px] font-black text-slate-400 uppercase mt-4 mb-1 border-b border-slate-200">
                                    <?= esc($cat) ?>
                                </div>
                                <?php foreach ($conditions as $c): ?>
                                    <label class="flex items-center text-[11px] text-slate-600 cursor-pointer hover:text-blue-600 transition-colors py-0.5">
                                        <input type="checkbox" name="medical_conditions[]" value="<?= esc($c['condition_key']) ?>" class="mr-2 rounded border-slate-300">
                                        <?= esc($c['condition_label']) ?>
                                    </label>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="pt-6 border-t flex justify-between gap-4">
                    <button type="button" onclick="goToStep(1)" class="w-1/3 bg-slate-200 text-slate-700 py-4 rounded-xl font-bold uppercase text-xs">Back</button>
                    <button type="submit" class="w-2/3 bg-blue-600 text-white py-4 rounded-xl font-bold uppercase tracking-widest hover:bg-blue-700 shadow-xl transition-all">Confirm Appointment & Save History</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- VIEW MODAL -->
<div id="viewModal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-start justify-center p-4 z-[70]">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl my-10 overflow-hidden">
        <div class="p-4 border-b flex justify-between items-center bg-slate-50">
            <h4 class="font-bold text-slate-800 uppercase text-sm">Appointment Summary</h4><button onclick="document.getElementById('viewModal').classList.add('hidden')" class="text-slate-400 text-2xl hover:text-slate-600">&times;</button>
        </div>
        <div class="p-6 space-y-4 text-sm">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Patient</p>
                    <p id="v_patient" class="font-bold text-slate-800 text-base"></p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Status</p><span id="v_status" class="px-2 py-0.5 rounded font-bold text-[10px]"></span>
                </div>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 grid grid-cols-2 gap-4">
                <div>
                    <p class="text-[10px] font-black text-blue-500 uppercase tracking-tighter">Start Time</p>
                    <p id="v_start" class="font-bold text-slate-700"></p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-indigo-500 uppercase tracking-tighter">End Time</p>
                    <p id="v_end" class="font-bold text-slate-700"></p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Assigned Dentist</p>
                    <p id="v_dentist" class="font-bold text-blue-600"></p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Service</p>
                    <p id="v_service" class="font-bold text-slate-800"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- RESCHEDULE MODAL -->
<div id="rescheduleModal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-start justify-center p-4 z-[60]">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg my-20 overflow-hidden">
        <div class="p-4 border-b flex justify-between items-center bg-slate-50">
            <h4 class="font-bold text-slate-800 uppercase text-sm">Reschedule Booking</h4><button onclick="closeReschedModal()" class="text-slate-400 text-2xl hover:text-slate-600">&times;</button>
        </div>
        <form action="<?= base_url('receptionist/appointments/reschedule') ?>" method="POST" class="p-6 space-y-4"><?= csrf_field() ?><input type="hidden" name="appointment_id" id="resched_id">
            <div class="space-y-1"><label class="fixed-label text-indigo-600">Change Dentist</label><select name="dentist_id" id="resched_dentist" class="w-full p-3 border border-slate-200 rounded-xl text-sm font-bold bg-white"><?php foreach ($dentists as $d): ?><option value="<?= $d['id'] ?>">Dr. <?= $d['first_name'] ?> <?= $d['last_name'] ?></option><?php endforeach; ?></select></div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1"><label class="text-[10px] font-bold text-slate-400 uppercase">New Start Date</label><input type="date" name="appointment_date" id="resched_date" min="<?= date('Y-m-d') ?>" class="w-full p-2.5 border border-slate-200 rounded-lg text-sm" required></div>
                <div class="space-y-1"><label class="text-[10px] font-bold text-slate-400 uppercase">Start Time</label><input type="time" name="appointment_time" id="resched_time" class="w-full p-2.5 border border-slate-200 rounded-lg text-sm" required></div>
                <div class="space-y-1"><label class="text-[10px] font-bold text-slate-400 uppercase">New End Date</label><input type="date" name="end_date" id="resched_end_date" min="<?= date('Y-m-d') ?>" class="w-full p-2.5 border border-slate-200 rounded-lg text-sm" required></div>
                <div class="space-y-1"><label class="text-[10px] font-bold text-slate-400 uppercase">End Time</label><input type="time" name="end_time" id="resched_end_time" class="w-full p-2.5 border border-slate-200 rounded-lg text-sm" required></div>
            </div><button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-bold uppercase text-xs tracking-widest hover:bg-indigo-700 shadow-lg mt-4 transition-all">Update Appointment</button>
        </form>
    </div>
</div>

<script>
    // ===============================
    // 1. GLOBAL CONSTANTS
    // ===============================
    const BASE_URL = "<?= base_url('data/ph-addresses/') ?>";
    const OLD_DATA = {
        region: "<?= old('region') ?>",
        province: "<?= old('province') ?>",
        city: "<?= old('city') ?>",
        barangay: "<?= old('barangay') ?>"
    };

    // ===============================
    // 2. GENDER LOGIC
    // ===============================
    function handleGenderLogic() {
        const gender = $('select[name="gender"]').val();
        const womenSection = $('#women_section');
        const womenInputs = womenSection.find('input');

        if (gender === 'Male') {
            womenSection.css({
                opacity: '0.5',
                pointerEvents: 'none',
                backgroundColor: '#f1f5f9',
                borderColor: '#e2e8f0'
            });

            womenInputs.prop('disabled', true);
            womenSection.find('input[value="0"]').prop('checked', true);

            womenSection.find('label')
                .removeClass('text-pink-700')
                .addClass('text-slate-400');
        } else {
            womenSection.css({
                opacity: '1',
                pointerEvents: 'auto',
                backgroundColor: '',
                borderColor: ''
            });

            womenInputs.prop('disabled', false);
            womenSection.find('label')
                .first()
                .addClass('text-pink-700')
                .removeClass('text-slate-400');
        }
    }

    // ===============================
    // 3. MOBILE INPUT FORMAT
    // ===============================
    function setupMobileInput() {
        const mobileInput = document.getElementById('primary_mobile');

        mobileInput.addEventListener('focus', function() {
            if (this.value.trim() === '') this.value = '09';
        });

        mobileInput.addEventListener('input', function() {
            let raw = this.value.replace(/\D/g, '');

            if (!raw.startsWith('09')) {
                raw = '09' + raw.substring(2);
            }

            raw = raw.substring(0, 11);

            let formatted = '';
            if (raw.length <= 4) formatted = raw;
            else if (raw.length <= 7) formatted = raw.slice(0, 4) + ' ' + raw.slice(4);
            else formatted = raw.slice(0, 4) + ' ' + raw.slice(4, 7) + ' ' + raw.slice(7);

            this.value = formatted;
        });
    }

    // ===============================
    // 4. MODAL ERROR HANDLING
    // ===============================
    function reopenModalOnError() {
        <?php if (session()->getFlashdata('error') || session()->getFlashdata('validation_errors')): ?>
            document.getElementById('apptModal').classList.remove('hidden');
        <?php endif; ?>
    }

    // ===============================
    // 5. TABLE FILTER
    // ===============================
    function filterTable() {
        const searchText = $('#tableSearch').val().toLowerCase();
        const statusVal = $('#statusFilter').val();
        const dentistVal = $('#dentistFilter').val();
        const dateVal = $('#dateFilter').val();

        $('.appt-row').each(function() {
            const rowText = $(this).text().toLowerCase();
            const rowStatus = $(this).data('status');
            const rowDentist = $(this).attr('data-dentist');
            const rowDate = $(this).data('date');

            const matchText = rowText.includes(searchText);
            const matchStatus = !statusVal || rowStatus === statusVal;
            const matchDentist = !dentistVal || rowDentist == dentistVal;
            const matchDate = !dateVal || rowDate === dateVal;

            $(this).toggle(matchText && matchStatus && matchDentist && matchDate);
        });
    }

    function setupFilters() {
        $('#tableSearch, #statusFilter, #dentistFilter, #dateFilter')
            .on('input change', filterTable);
    }

    function resetFilters() {
        $('.filter-input').val('');
        $('.appt-row').show();
    }

    // ===============================
    // 6. STEP FORM
    // ===============================
    function goToStep(step) {
        const s1 = document.getElementById('step1_container');
        const s2 = document.getElementById('step2_container');
        const d2 = document.getElementById('dot2');
        const title = document.getElementById('stepTitle');

        if (step === 2) {
            const dentist = document.querySelector('select[name="dentist_id"]').value;
            const apptDate = document.querySelector('input[name="appointment_date"]').value;

            if (!dentist || !apptDate) {
                alert("Pakisagutan muna ang Dentist at Schedule bago magpatuloy.");
                return;
            }

            s1.classList.add('step-hidden');
            s2.classList.remove('step-hidden');
            d2.classList.add('active');
            title.innerText = "Step 2: Medical History Record";
            document.getElementById('apptModal').scrollTop = 0;
        } else {
            s1.classList.remove('step-hidden');
            s2.classList.add('step-hidden');
            d2.classList.remove('active');
            title.innerText = "Step 1: Appointment Details";
        }
    }

    function closeModal() {
        document.getElementById('apptModal').classList.add('hidden');
        goToStep(1);
    }

    // ===============================
    // 7. ACCOUNT TYPE
    // ===============================
    function toggleAccountType() {
        const isNew = document.querySelector('input[name="account_type"]:checked').value === 'new';

        document.getElementById('existing_patient_div')
            .classList.toggle('hidden', isNew);

        document.getElementById('new_patient_div')
            .classList.toggle('hidden', !isNew);
    }

    // ===============================
    // 8. SERVICES
    // ===============================
    function checkLevels(sel) {
        sel.closest('.service-row')
            .querySelector('.level-div')
            .classList.toggle('hidden', sel.options[sel.selectedIndex].dataset.haslevels !== '1');
    }

    function addServiceRow() {
        const cont = document.getElementById('services_container');
        const newRow = cont.querySelector('.service-row').cloneNode(true);

        newRow.querySelector('select[name="services[]"]').value = "";
        newRow.querySelector('.level-div').classList.add('hidden');

        cont.appendChild(newRow);
    }

    function removeServiceRow(btn) {
        if (document.querySelectorAll('.service-row').length > 1) {
            btn.closest('.service-row').remove();
        }
    }

    // ===============================
    // 9. PASSWORD TOGGLE
    // ===============================
    function togglePass(id, icon) {
        const input = document.getElementById(id);

        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.replace("fa-eye-slash", "fa-eye");
        }
    }

    // ===============================
    // 10. ADDRESS SYSTEM
    // ===============================
    const ADDRESS_CONFIG = {
        region: {
            file: 'region.json',
            filter: null,
            display: 'region_name',
            value: 'region_code',
            next: 'province',
            selectId: 'reg_select'
        },
        province: {
            file: 'province.json',
            filter: 'region_code',
            display: 'province_name',
            value: 'province_code',
            next: 'city',
            selectId: 'prov_select'
        },
        city: {
            file: 'city.json',
            filter: 'province_code',
            display: 'city_name',
            value: 'city_code',
            next: 'barangay',
            selectId: 'city_select'
        },
        barangay: {
            file: 'barangay.json',
            filter: 'city_code',
            display: 'brgy_name',
            value: 'brgy_code',
            next: null,
            selectId: 'brgy_select'
        }
    };

    async function loadAddressLevel(level, parentValue = null) {
        const cfg = ADDRESS_CONFIG[level];
        const select = document.getElementById(cfg.selectId);

        select.disabled = true;
        select.innerHTML = '<option value="" selected disabled></option>';

        try {
            const res = await fetch(`${BASE_URL}${cfg.file}`);
            const data = await res.json();

            const filtered = cfg.filter ?
                data.filter(item => item[cfg.filter] == parentValue) :
                data;

            filtered.forEach(item => {
                const opt = new Option(item[cfg.display], item[cfg.value]);

                if (OLD_DATA[level] &&
                    (item[cfg.display] === OLD_DATA[level] || item[cfg.value] === OLD_DATA[level])) {
                    opt.selected = true;
                }

                select.add(opt);
            });

            select.disabled = false;

            if (select.value && cfg.next) {
                loadAddressLevel(cfg.next, select.value);
            }

        } catch (err) {
            console.error(`Error loading ${level}:`, err);
        }
    }

    // ===============================
    // 11. VIEW & RESCHEDULE
    // ===============================
    function viewAppointment(a) {
        document.getElementById('v_patient').innerText = a.patient_name;
        document.getElementById('v_start').innerText = a.fmt_date + " @ " + a.fmt_time;
        document.getElementById('v_end').innerText = a.fmt_end_date + " @ " + a.fmt_end;
        document.getElementById('v_dentist').innerText = 'Dr. ' + (a.dentist_name || 'N/A');
        document.getElementById('v_service').innerText = a.service_name;

        const statusEl = document.getElementById('v_status');
        statusEl.innerText = a.status;
        statusEl.className = "px-2 py-0.5 rounded font-bold text-[10px] uppercase ";

        if (a.status === 'Pending') statusEl.className += "bg-amber-100 text-amber-700";
        else if (a.status === 'Confirmed') statusEl.className += "bg-blue-100 text-blue-700";
        else if (a.status === 'Completed') statusEl.className += "bg-green-100 text-green-700";
        else statusEl.className += "bg-red-100 text-red-700";

        document.getElementById('viewModal').classList.remove('hidden');
    }

    function openRescheduleModal(id, d, t, ed, et, did) {
        document.getElementById('resched_id').value = id;
        document.getElementById('resched_date').value = d;
        document.getElementById('resched_time').value = t;
        document.getElementById('resched_end_date').value = ed;
        document.getElementById('resched_end_time').value = et;
        document.getElementById('resched_dentist').value = did;
        document.getElementById('rescheduleModal').classList.remove('hidden');
    }

    function closeReschedModal() {
        document.getElementById('rescheduleModal').classList.add('hidden');
    }

    // ===============================
    // 12. FINAL FORM SUBMIT FIX
    // ===============================
    document.getElementById('apptForm').addEventListener('submit', function() {
        ['reg_select', 'prov_select', 'city_select', 'brgy_select'].forEach(id => {
            const sel = document.getElementById(id);
            if (sel.selectedIndex > 0) {
                sel.options[sel.selectedIndex].value = sel.options[sel.selectedIndex].text;
            }
        });

        let mobile = document.getElementById('primary_mobile');
        mobile.value = mobile.value.replace(/\s/g, '');
    });

    // ===============================
    // 13. INIT
    // ===============================
    $(document).ready(function() {
        loadAddressLevel('region');
        setupMobileInput();
        reopenModalOnError();
        setupFilters();

        $('select[name="gender"]').on('change', handleGenderLogic);
        handleGenderLogic();

        $('#patient_search').select2({
            dropdownParent: $('#apptModal'),
            placeholder: 'Type name...',
            minimumInputLength: 2,
            width: '100%',
            ajax: {
                url: '<?= base_url('receptionist/appointments/searchPatients') ?>',
                dataType: 'json',
                delay: 250,
                data: p => ({
                    q: p.term
                }),
                processResults: data => ({
                    results: data
                })
            }
        });
    });
</script>
<?= $this->endSection() ?>