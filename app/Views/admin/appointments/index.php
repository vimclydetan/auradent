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
</style>

<!-- ALERTS -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg border-l-4 border-green-500 font-bold text-sm"><?= session()->getFlashdata('success') ?></div>
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
                        <div class="text-xs font-bold text-slate-700"><?= date('M d, Y', strtotime($a['appointment_date'])) ?></div>
                        <div class="text-[10px] text-blue-600 italic"><?= date('h:i A', strtotime($a['appointment_time'])) ?> - <?= date('h:i A', strtotime($a['end_time'])) ?></div>
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
                                <a href="<?= base_url('admin/appointments/status/' . $a['id'] . '/Confirmed') ?>" onclick="return confirm('Confirm?')" class="bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg font-bold text-[10px] uppercase hover:bg-blue-600 hover:text-white transition-colors">Confirm</a>
                                <button onclick="openRescheduleModal(<?= $a['id'] ?>, '<?= $a['appointment_date'] ?>', '<?= $a['appointment_time'] ?>', '<?= $a['end_date'] ?>', '<?= $a['end_time'] ?>', '<?= $a['dentist_id'] ?>')" class="bg-amber-50 text-amber-600 px-3 py-1.5 rounded-lg font-bold text-[10px] uppercase hover:bg-amber-500 hover:text-white transition-colors">Resched</button>
                                <a href="<?= base_url('admin/appointments/status/' . $a['id'] . '/Cancelled') ?>" onclick="return confirm('Cancel appointment?')" class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg font-bold text-[10px] uppercase hover:bg-red-600 hover:text-white transition-colors">Cancel</a>
                            <?php elseif ($a['status'] === 'Confirmed'): ?>
                                <a href="<?= base_url('admin/appointments/status/' . $a['id'] . '/Completed') ?>" onclick="return confirm('Mark as Completed?')" class="bg-green-600 text-white px-3 py-1.5 rounded-lg font-bold text-[10px] uppercase hover:bg-green-700 shadow-sm transition-all">Done</a>
                                <a href="<?= base_url('admin/appointments/status/' . $a['id'] . '/Cancelled') ?>" onclick="return confirm('Cancel appointment?')" class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg font-bold text-[10px] uppercase hover:bg-red-600 hover:text-white transition-colors">Cancel</a>
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

        <form action="<?= base_url('admin/appointments/store') ?>" method="POST" id="apptForm">
            <?= csrf_field() ?>

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
                                <?php foreach (['Jr.', 'Sr.', 'III'] as $sfx): ?><option value="<?= $sfx ?>" <?= old('name_suffix') == $sfx ? 'selected' : '' ?>><?= $sfx ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div><label class="fixed-label text-slate-500">Birthdate *</label><input type="date" name="birthdate" value="<?= old('birthdate') ?>" class="w-full p-2 border rounded-lg text-sm"></div>
                            <div><label class="fixed-label text-slate-500">Gender</label><select name="gender" class="w-full p-2.5 border rounded-lg text-sm">
                                    <option value="Male" <?= old('gender') == 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= old('gender') == 'Female' ? 'selected' : '' ?>>Female</option>
                                </select></div>
                            <div class="floating-label-group"><input type="tel" name="primary_mobile" id="primary_mobile" value="<?= old('primary_mobile') ?>" maxlength="15" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm font-bold text-blue-600"><label class="floating-label">Mobile Number *</label></div>
                        </div>
                        <!-- ADDRESS SECTION -->
                        <h5 class="text-xs font-black text-blue-500 uppercase italic">Step 2: PH Address</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="floating-label-group"><select name="region" id="reg_select" onchange="loadProvinces(this.value)" class="floating-input w-full p-2.5 border rounded-lg text-sm">
                                    <option value="" selected disabled></option>
                                </select><label class="floating-label">Region *</label></div>
                            <div class="floating-label-group"><select name="province" id="prov_select" onchange="loadCities(this.value)" disabled class="floating-input w-full p-2.5 border rounded-lg text-sm">
                                    <option value="" selected disabled></option>
                                </select><label class="floating-label">Province *</label></div>
                            <div class="floating-label-group"><select name="city" id="city_select" onchange="loadBarangays(this.value)" disabled class="floating-input w-full p-2.5 border rounded-lg text-sm">
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
        <form action="<?= base_url('admin/appointments/reschedule') ?>" method="POST" class="p-6 space-y-4"><?= csrf_field() ?><input type="hidden" name="appointment_id" id="resched_id">
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
    const BASE_URL = "<?= base_url('data/ph-addresses/') ?>";
    const OLD_DATA = {
        region: "<?= old('region') ?>",
        province: "<?= old('province') ?>",
        city: "<?= old('city') ?>",
        barangay: "<?= old('barangay') ?>"
    };

    $(document).ready(function() {
        loadRegions();
        $('#patient_search').select2({
            dropdownParent: $('#apptModal'),
            placeholder: 'Type name...',
            minimumInputLength: 2,
            width: '100%',
            ajax: {
                url: '<?= base_url('admin/appointments/searchPatients') ?>',
                dataType: 'json',
                delay: 250,
                data: function(p) {
                    return {
                        q: p.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            }
        });

        // RE-OPEN MODAL IF ERRORS EXIST
        <?php if (session()->getFlashdata('error') || session()->getFlashdata('validation_errors')): ?>
            document.getElementById('apptModal').classList.remove('hidden');
        <?php endif; ?>

        // DENTIST FILTER FIX
        // Hanapin ang function filterTable() sa loob ng <script> tag:

        function filterTable() {
            const searchText = $('#tableSearch').val().toLowerCase();
            const statusVal = $('#statusFilter').val();
            const dentistVal = $('#dentistFilter').val(); // Ngayon, ID na ang makukuha nito
            const dateVal = $('#dateFilter').val();

            $('.appt-row').each(function() {
                const rowText = $(this).text().toLowerCase();
                const rowStatus = $(this).data('status');
                const rowDentist = $(this).attr('data-dentist'); // ID na ang makukuha rito
                const rowDate = $(this).data('date');

                const matchText = rowText.includes(searchText);
                const matchStatus = statusVal === "" || rowStatus === statusVal;

                // Gagana na ito dahil parehas na silang ID (String comparison)
                const matchDentist = dentistVal === "" || rowDentist == dentistVal;

                const matchDate = dateVal === "" || rowDate === dateVal;

                $(this).toggle(matchText && matchStatus && matchDentist && matchDate);
            });
        }
        $('#tableSearch, #statusFilter, #dentistFilter, #dateFilter').on('input change', filterTable);
    });

    // FUNCTIONS
    function resetFilters() {
        $('.filter-input').val('');
        $('.appt-row').show();
    }

    function closeModal() {
        document.getElementById('apptModal').classList.add('hidden');
    }

    function toggleAccountType() {
        const isNew = document.querySelector('input[name="account_type"]:checked').value === 'new';
        document.getElementById('existing_patient_div').classList.toggle('hidden', isNew);
        document.getElementById('new_patient_div').classList.toggle('hidden', !isNew);
    }

    function checkLevels(sel) {
        sel.closest('.service-row').querySelector('.level-div').classList.toggle('hidden', sel.options[sel.selectedIndex].dataset.haslevels !== '1');
    }

    function addServiceRow() {
        const cont = document.getElementById('services_container');
        const rows = cont.querySelectorAll('.service-row');
        const newRow = rows[0].cloneNode(true);
        newRow.querySelector('select[name="services[]"]').value = "";
        newRow.querySelector('.level-div').classList.add('hidden');
        cont.appendChild(newRow);
    }

    function removeServiceRow(btn) {
        if (document.querySelectorAll('.service-row').length > 1) btn.closest('.service-row').remove();
    }

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

    // Address Loaders (Recursive)
    async function loadRegions() {
        const response = await fetch(`${BASE_URL}region.json`);
        const data = await response.json();
        const select = document.getElementById('reg_select');
        data.forEach(r => {
            let opt = new Option(r.region_name, r.region_code);
            if (OLD_DATA.region && (r.region_name === OLD_DATA.region || r.region_code === OLD_DATA.region)) opt.selected = true;
            select.add(opt);
        });
        if (select.value) loadProvinces(select.value);
    }
    async function loadProvinces(rc) {
        const prov = document.getElementById('prov_select');
        prov.disabled = false;
        prov.innerHTML = '<option value="" selected disabled></option>';
        const response = await fetch(`${BASE_URL}province.json`);
        const data = await response.json();
        data.filter(p => p.region_code === rc).forEach(p => {
            let opt = new Option(p.province_name, p.province_code);
            if (OLD_DATA.province && (p.province_name === OLD_DATA.province || p.province_code === OLD_DATA.province)) opt.selected = true;
            prov.add(opt);
        });
        if (prov.value) loadCities(prov.value);
    }
    async function loadCities(pc) {
        const city = document.getElementById('city_select');
        city.disabled = false;
        city.innerHTML = '<option value="" selected disabled></option>';
        const response = await fetch(`${BASE_URL}city.json`);
        const data = await response.json();
        data.filter(c => c.province_code === pc).forEach(c => {
            let opt = new Option(c.city_name, c.city_code);
            if (OLD_DATA.city && (c.city_name === OLD_DATA.city || c.city_code === OLD_DATA.city)) opt.selected = true;
            city.add(opt);
        });
        if (city.value) loadBarangays(city.value);
    }
    async function loadBarangays(cc) {
        const brgy = document.getElementById('brgy_select');
        brgy.disabled = false;
        brgy.innerHTML = '<option value="" selected disabled></option>';
        const response = await fetch(`${BASE_URL}barangay.json`);
        const data = await response.json();
        data.filter(b => b.city_code === cc).forEach(b => {
            let opt = new Option(b.brgy_name, b.brgy_code);
            if (OLD_DATA.barangay && (b.brgy_name === OLD_DATA.barangay || b.brgy_code === OLD_DATA.barangay)) opt.selected = true;
            brgy.add(opt);
        });
    }

    // View & Reschedule
    function viewAppointment(a) {
        document.getElementById('v_patient').innerText = a.patient_name;
        document.getElementById('v_start').innerText = a.appointment_date + ' @ ' + a.appointment_time;
        document.getElementById('v_end').innerText = a.end_date + ' @ ' + a.end_time;
        document.getElementById('v_dentist').innerText = 'Dr. ' + (a.dentist_name || 'N/A');
        document.getElementById('v_service').innerText = a.service_name;
        document.getElementById('v_status').innerText = a.status;
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

    // Address Fix for Final Submission
    document.getElementById('apptForm').addEventListener('submit', function(e) {
        const selects = ['reg_select', 'prov_select', 'city_select', 'brgy_select'];
        selects.forEach(id => {
            const sel = document.getElementById(id);
            if (sel.selectedIndex > 0) sel.options[sel.selectedIndex].value = sel.options[sel.selectedIndex].text;
        });
    });
</script>
<?= $this->endSection() ?>