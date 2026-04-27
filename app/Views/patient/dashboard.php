<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<style>
    /* ===== FLOATING LABELS ===== */
    .floating-label-group {
        position: relative;
    }

    .floating-input:focus~.floating-label,
    .floating-input:not(:placeholder-shown)~.floating-label {
        top: -0.5rem;
        left: 0.75rem;
        font-size: 0.7rem;
        color: #2563eb;
        background: #fff;
        padding: 0 0.35rem;
        font-weight: 600;
        z-index: 10;
    }

    .floating-label {
        position: absolute;
        pointer-events: none;
        left: 0.85rem;
        top: 0.85rem;
        transition: 0.2s ease all;
        color: #64748b;
        font-size: 0.875rem;
    }

    .floating-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        outline: none;
    }

    /* ===== SECTION STYLING ===== */
    .form-section {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.25rem;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.75rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid #cbd5e1;
    }

    .section-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #475569;
    }

    .section-icon {
        width: 1.5rem;
        height: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eff6ff;
        color: #2563eb;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }

    /* ===== MEDICAL GRID ===== */
    .medical-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
        gap: 0.35rem 0.75rem;
        padding: 0.75rem;
    }

    .medical-grid label {
        font-size: 0.75rem;
        color: #334155;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.25rem 0;
        transition: color 0.15s;
    }

    .medical-grid label:hover {
        color: #2563eb;
    }

    .medical-grid input[type="checkbox"] {
        width: 0.9rem;
        height: 0.9rem;
        border-radius: 0.25rem;
        border: 1px solid #cbd5e1;
        accent-color: #2563eb;
    }

    /* ===== MODAL OPTIMIZATION ===== */
    .modal-container {
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }

    .modal-body {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
        scroll-behavior: smooth;
    }

    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
        position: sticky;
        bottom: 0;
    }

    /* ===== RADIO/TOGGLE STYLING ===== */
    .radio-group {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .radio-option {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.8rem;
        font-weight: 500;
        color: #334155;
        cursor: pointer;
    }

    .radio-option input {
        width: 1rem;
        height: 1rem;
        accent-color: #2563eb;
    }

    /* ===== CONDITIONAL FIELD ===== */
    .conditional-field {
        margin-top: 0.5rem;
        padding-left: 1.5rem;
        border-left: 2px solid #e2e8f0;
    }

    .conditional-field input {
        width: 100%;
        padding: 0.4rem 0.6rem;
        font-size: 0.8rem;
        border: none;
        border-bottom: 1px solid #cbd5e1;
        background: transparent;
        outline: none;
        transition: border-color 0.2s;
    }

    .conditional-field input:focus {
        border-bottom-color: #2563eb;
    }

    .conditional-field:has(input:disabled) {
        opacity: 0.6;
    }

    /* ===== UTILITIES ===== */
    .required::after {
        content: "*";
        color: #ef4444;
        margin-left: 2px;
    }

    .text-muted {
        color: #64748b;
        font-size: 0.8rem;
    }

    .divider {
        height: 1px;
        background: #e2e8f0;
        margin: 1.25rem 0;
    }
</style>

<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Welcome, <?= esc(session()->get('full_name')) ?></h2>
        <p class="text-slate-500 text-sm">Here's what's happening with your dental health today.</p>
    </div>
    <div>
        <a href="/patient/book" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
            <i class="fas fa-plus mr-2"></i> Book New Appointment
        </a>
    </div>
</div>

<!-- Stats Cards (unchanged) -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-sm border flex items-center justify-between">
        <div>
            <p class="text-slate-500 text-sm font-medium">Next Appointment</p>
            <h3 class="text-xl font-bold text-slate-800 mt-1">
                <?php if ($upcoming_appointment): ?>
                    <?= date('M d, Y', strtotime($upcoming_appointment['appointment_date'])) ?>
                    <span class="block text-sm font-normal text-blue-600"><?= date('h:i A', strtotime($upcoming_appointment['appointment_time'])) ?></span>
                <?php else: ?>
                    <span class="text-slate-400 font-medium">No upcoming schedule</span>
                <?php endif; ?>
            </h3>
        </div>
        <div class="bg-blue-100 p-4 rounded-xl text-blue-600">
            <i class="fas fa-calendar-alt text-2xl"></i>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border flex items-center justify-between">
        <div>
            <p class="text-slate-500 text-sm font-medium">Total Clinic Visits</p>
            <h3 class="text-3xl font-bold text-slate-800 mt-1"><?= (int)$stats['total_appointments'] ?></h3>
        </div>
        <div class="bg-green-100 p-4 rounded-xl text-green-600">
            <i class="fas fa-history text-2xl"></i>
        </div>
    </div>
</div>

<!-- Recent Appointments Table (unchanged) -->
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="p-6 border-b flex justify-between items-center">
        <h4 class="font-bold text-slate-700">Recent Appointments</h4>
        <a href="/patient/appointments" class="text-sm text-blue-600 hover:underline font-medium">View all</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-semibold">Date & Time</th>
                    <th class="px-6 py-4 font-semibold">Dentist</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                <?php if ($recent_appointments): ?>
                    <?php foreach ($recent_appointments as $ra): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800"><?= date('M d, Y', strtotime($ra['appointment_date'])) ?></div>
                                <div class="text-xs text-slate-500"><?= date('h:i A', strtotime($ra['appointment_time'])) ?></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                Dr. <?= esc($ra['d_last'] ?? 'To be assigned') ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                $statusClass = match ($ra['status']) {
                                    'Completed' => 'bg-green-100 text-green-700',
                                    'Pending' => 'bg-amber-100 text-amber-700',
                                    'Cancelled' => 'bg-red-100 text-red-700',
                                    default => 'bg-blue-100 text-blue-700'
                                };
                                ?>
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase <?= $statusClass ?>">
                                    <?= esc($ra['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button class="text-slate-400 hover:text-blue-600 transition-colors" title="View details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-slate-400 italic text-sm">
                            No appointment records found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ============================================ -->
<!-- PERSONAL INFORMATION MODAL -->
<!-- ============================================ -->
<?php if ($showPersonalInfoModal): ?>
    <div id="personalInfoModal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-start justify-center p-4 z-[100] overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl my-4 flex flex-col overflow-hidden border border-slate-200">

            <!-- Modal Header -->
            <div class="p-4 border-b flex justify-between items-center bg-slate-50">
                <h4 class="font-bold text-lg text-slate-800 uppercase">Complete Your Profile</h4>
                <button type="button" onclick="closePersonalInfoModal()" class="text-slate-400 text-2xl hover:text-slate-600 transition-colors">&times;</button>
            </div>

            <form action="<?= base_url('patient/save-personal-info') ?>" method="POST" class="flex flex-col h-full">
                <?= csrf_field() ?>
                <input type="hidden" name="patient_id" value="<?= $patient['id'] ?>">
                <input type="hidden" name="birthdate" value="<?= esc($patient['birthdate']) ?>">
                <input type="hidden" name="gender" value="<?= esc($patient['gender']) ?>">

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

                <div class="p-6 space-y-6 overflow-y-auto">

                    <!-- 👤 SECTION 1: Personal Information -->
                    <div class="p-5 bg-slate-50/50 border border-slate-200 rounded-2xl space-y-5">
                        <div class="flex items-center gap-2 pb-3 border-b border-slate-200">
                            <div class="section-icon bg-slate-200 text-slate-700">
                                <i class="fas fa-user"></i>
                            </div>
                            <h5 class="text-xs font-black text-slate-600 uppercase italic">Personal Information</h5>
                        </div>

                        <!-- Name Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="floating-label-group">
                                <input type="text" value="<?= esc($patient['first_name']) ?>" disabled
                                    class="floating-input w-full p-2.5 border rounded-lg text-sm bg-slate-200 text-slate-600 cursor-not-allowed">
                                <label class="floating-label required">First Name</label>
                            </div>
                            <div class="floating-label-group">
                                <input type="text" value="<?= esc($patient['middle_name'] ?? '') ?>" disabled
                                    class="floating-input w-full p-2.5 border rounded-lg text-sm bg-slate-200 text-slate-600 cursor-not-allowed">
                                <label class="floating-label">Middle Name</label>
                            </div>
                            <div class="floating-label-group">
                                <input type="text" value="<?= esc($patient['last_name']) ?>" disabled
                                    class="floating-input w-full p-2.5 border rounded-lg text-sm bg-slate-200 text-slate-600 cursor-not-allowed">
                                <label class="floating-label required">Last Name</label>
                            </div>
                            <div class="floating-label-group">
                                <input type="text" value="<?= esc($patient['name_suffix'] ?? 'None') ?>" disabled
                                    class="floating-input w-full p-2.5 border rounded-lg text-sm bg-slate-200 text-slate-600 cursor-not-allowed">
                                <label class="floating-label">Suffix</label>
                            </div>
                        </div>

                        <!-- Birthdate, Gender, Age, Nickname -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="floating-label-group">
                                <input type="text" value="<?= date('F d, Y', strtotime($patient['birthdate'])) ?>" disabled
                                    class="floating-input w-full p-2.5 border rounded-lg text-sm bg-slate-200 text-slate-600 cursor-not-allowed">
                                <label class="floating-label required">Birthdate</label>
                            </div>
                            <div class="floating-label-group">
                                <input type="text" value="<?= esc($patient['gender']) ?>" disabled
                                    class="floating-input w-full p-2.5 border rounded-lg text-sm bg-slate-200 text-slate-600 cursor-not-allowed">
                                <label class="floating-label required">Gender</label>
                            </div>
                            <div class="floating-label-group">
                                <input type="text" value="<?= esc($patient_age) ?>" disabled
                                    class="floating-input w-full p-2.5 border rounded-lg text-sm bg-slate-200 text-slate-600 cursor-not-allowed">
                                <label class="floating-label required">Age</label>
                            </div>
                            <!-- Nickname (Editable) -->
                            <div class="floating-label-group">
                                <input type="text" name="nickname"
                                    value="<?= old('nickname', $patient['nickname'] ?? '') ?>"
                                    placeholder=" " maxlength="255"
                                    class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white">
                                <label class="floating-label">Nickname</label>
                            </div>
                        </div>

                        <!-- Username & Email (Read-Only) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                            <div class="floating-label-group">
                                <input type="text" value="<?= esc($user_data['username'] ?? 'N/A') ?>" disabled
                                    class="floating-input w-full p-2.5 border rounded-lg text-sm bg-slate-200 text-slate-600 cursor-not-allowed">
                                <label class="floating-label required">Username</label>
                            </div>
                            <div class="floating-label-group">
                                <input type="email" value="<?= esc($user_data['email'] ?? 'N/A') ?>" disabled
                                    class="floating-input w-full p-2.5 border rounded-lg text-sm bg-slate-200 text-slate-600 cursor-not-allowed">
                                <label class="floating-label required">Email Address</label>
                            </div>
                        </div>

                        <p class="text-[10px] text-slate-400 italic flex items-center gap-1">
                            <i class="fas fa-lock"></i> Locked fields can only be changed by administrator.
                        </p>
                    </div>

                    <!-- 🏠 SECTION 2: Address Information -->
                    <div class="p-5 bg-blue-50/50 border border-blue-100 rounded-2xl space-y-5">
                        <div class="flex items-center gap-2 pb-3 border-b border-blue-200">
                            <div class="section-icon bg-blue-100 text-blue-600">
                                <i class="fas fa-home"></i>
                            </div>
                            <h5 class="text-xs font-black text-blue-500 uppercase italic">Address Information</h5>
                        </div>

                        <!-- Street Address Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="floating-label-group">
                                <input type="text" name="house_number"
                                    value="<?= old('house_number', $patient['house_number'] ?? '') ?>"
                                    placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white">
                                <label class="floating-label">House/Unit No.</label>
                            </div>
                            <div class="floating-label-group">
                                <input type="text" name="building_name"
                                    value="<?= old('building_name', $patient['building_name'] ?? '') ?>"
                                    placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white">
                                <label class="floating-label">Building Name</label>
                            </div>
                            <div class="floating-label-group md:col-span-2">
                                <input type="text" name="street_name"
                                    value="<?= old('street_name', $patient['street_name'] ?? '') ?>"
                                    placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white">
                                <label class="floating-label">Street Name</label>
                            </div>
                            <div class="floating-label-group md:col-span-2">
                                <input type="text" name="subdivision"
                                    value="<?= old('subdivision', $patient['subdivision'] ?? '') ?>"
                                    placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white">
                                <label class="floating-label">Subdivision / Village</label>
                            </div>
                        </div>

                        <!-- PH Address Cascade -->
                        <h6 class="text-[10px] font-black text-blue-400 uppercase tracking-wider mt-2 mb-3">🇵🇭 Philippine Address</h6>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="floating-label-group">
                                <select name="region" id="reg_select" onchange="loadAddressLevel('province', this.value)"
                                    class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white">
                                    <option value="">Select Region</option>
                                </select>
                                <label class="floating-label required">Region</label>
                            </div>
                            <div class="floating-label-group">
                                <select name="province" id="prov_select" onchange="loadAddressLevel('city', this.value)" disabled
                                    class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white">
                                    <option value="">Select Province</option>
                                </select>
                                <label class="floating-label required">Province</label>
                            </div>
                            <div class="floating-label-group">
                                <select name="city" id="city_select" onchange="loadAddressLevel('barangay', this.value)" disabled
                                    class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white">
                                    <option value="">Select City/Municipality</option>
                                </select>
                                <label class="floating-label required">City/Municipality</label>
                            </div>
                            <div class="floating-label-group">
                                <select name="barangay" id="brgy_select" disabled
                                    class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white">
                                    <option value="">Select Barangay</option>
                                </select>
                                <label class="floating-label required">Barangay</label>
                            </div>
                            <div class="floating-label-group">
                                <input type="text" name="postal_code"
                                    value="<?= old('postal_code', $patient['postal_code'] ?? '') ?>"
                                    placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"
                                    pattern="\d{4}" maxlength="4">
                                <label class="floating-label">Postal Code</label>
                            </div>
                        </div>
                    </div>

                    <!-- 📞 SECTION 3: Contact Details -->
                    <div class="p-5 bg-green-50/50 border border-green-100 rounded-2xl space-y-5">
                        <div class="flex items-center gap-2 pb-3 border-b border-green-200">
                            <div class="section-icon bg-green-100 text-green-600">
                                <i class="fas fa-phone"></i>
                            </div>
                            <h5 class="text-xs font-black text-green-500 uppercase italic">Contact Details</h5>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Primary Mobile (Required, PH Format) -->
                            <div class="floating-label-group">
                                <input type="tel" name="primary_mobile" id="primary_mobile"
                                    value="<?= old('primary_mobile', format_ph_mobile_display($patient['primary_mobile'] ?? '')) ?>"
                                    pattern="^09\d{2}\s?\d{3}\s?\d{4}$" maxlength="13" inputmode="tel"
                                    placeholder=" "
                                    class="floating-input w-full p-2.5 border rounded-lg text-sm font-bold text-blue-600 bg-white" required>
                                <label class="floating-label required">Mobile Number</label>
                                <p class="text-[10px] text-slate-400 mt-1">Format: 09XX XXX XXXX</p>
                            </div>

                            <!-- Home Phone (Optional, International Friendly) -->
                            <div class="floating-label-group">
                                <input type="tel" name="home_number"
                                    value="<?= old('home_number', format_phone_general_display($patient['home_number'] ?? '')) ?>"
                                    placeholder=" " pattern="^[\d+]{1,20}$" maxlength="20" inputmode="tel"
                                    class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"
                                    title="Numbers and + only. Max 20 characters.">
                                <label class="floating-label">Home Phone</label>
                                <p class="text-[10px] text-slate-400 mt-1">Ex: +63281234567</p>
                            </div>

                            <!-- Office Phone (Optional, International Friendly) -->
                            <div class="floating-label-group">
                                <input type="tel" name="office_number"
                                    value="<?= old('office_number', format_phone_general_display($patient['office_number'] ?? '')) ?>"
                                    placeholder=" " pattern="^[\d+]{1,20}$" maxlength="20" inputmode="tel"
                                    class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"
                                    title="Numbers and + only. Max 20 characters.">
                                <label class="floating-label">Office Phone</label>
                                <p class="text-[10px] text-slate-400 mt-1">Ex: +639171234567</p>
                            </div>
                        </div>
                    </div>

                    <!-- 👨‍👩‍👧‍👦 SECTION 4: Guardian Info (For Minors Only) -->
                    <?php if ($is_minor): ?>
                        <div class="p-5 bg-pink-50/50 border border-pink-100 rounded-2xl space-y-5">
                            <div class="flex items-center gap-2 pb-3 border-b border-pink-200">
                                <div class="section-icon bg-pink-100 text-pink-600">
                                    <i class="fas fa-user-friends"></i>
                                </div>
                                <h5 class="text-xs font-black text-pink-500 uppercase italic">Guardian / Parent Info</h5>
                            </div>
                            <p class="text-[10px] text-pink-500 -mt-3">Required for patients under 18 years old</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="floating-label-group">
                                    <input type="text" name="guardian_first_name"
                                        value="<?= old('guardian_first_name', $guardian_info['contact_first_name'] ?? '') ?>"
                                        placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white" required>
                                    <label class="floating-label required">First Name</label>
                                </div>
                                <div class="floating-label-group">
                                    <input type="text" name="guardian_middle_name"
                                        value="<?= old('guardian_middle_name', $guardian_info['contact_middle_name'] ?? '') ?>"
                                        placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white">
                                    <label class="floating-label">Middle Name</label>
                                </div>
                                <div class="floating-label-group">
                                    <input type="text" name="guardian_last_name"
                                        value="<?= old('guardian_last_name', $guardian_info['contact_last_name'] ?? '') ?>"
                                        placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white" required>
                                    <label class="floating-label required">Last Name</label>
                                </div>
                                <div class="floating-label-group">
                                    <input type="tel" name="guardian_mobile" id="guardian_mobile"
                                        value="<?= old('guardian_mobile', format_ph_mobile_display($guardian_info['mobile_number'] ?? '')) ?>"
                                        pattern="^09\d{2}\s?\d{3}\s?\d{4}$" maxlength="13" inputmode="tel"
                                        placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm font-bold text-pink-600 bg-white" required>
                                    <label class="floating-label required">Mobile Number</label>
                                </div>
                                <div class="floating-label-group md:col-span-2">
                                    <input type="text" name="guardian_occupation"
                                        value="<?= old('guardian_occupation', $guardian_info['occupation'] ?? '') ?>"
                                        placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white">
                                    <label class="floating-label">Occupation</label>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t bg-slate-50 flex justify-end gap-3">
                    <a href="<?= base_url('logout') ?>" class="inline-flex items-center px-6 py-3 border border-red-400 text-red-600 rounded-xl font-bold uppercase text-xs hover:bg-red-50 hover:border-red-300 transition-colors" onclick="return confirm('Are you sure you want to logout?')">
                        <i class="fas fa-sign-out-alt mr-1.5"></i> Logout
                    </a>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold uppercase tracking-widest text-xs hover:bg-blue-700 shadow-xl transition-all flex items-center gap-2">
                        <i class="fas fa-save"></i> Save & Continue
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<!-- ============================================ -->
<!-- INSURANCE INFORMATION MODAL (Step 2) -->
<!-- ============================================ -->
<?php if ($showInsuranceModal && !$showPersonalInfoModal): ?>
    <div id="insuranceModal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-start justify-center p-4 z-[100] overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl my-4 flex flex-col overflow-hidden border border-slate-200">

            <!-- Modal Header -->
            <div class="p-4 border-b flex justify-between items-center bg-slate-50">
                <div class="flex items-center gap-3">
                    <div class="section-icon bg-emerald-50 text-emerald-600">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-lg">Dental Insurance Details</h4>
                        <p class="text-xs text-slate-500">Optional: Add your insurance for faster claims processing</p>
                    </div>
                </div>
                <button type="button" onclick="closeInsuranceModal()" class="text-slate-400 text-2xl hover:text-slate-600 transition-colors">&times;</button>
            </div>

            <form action="<?= base_url('patient/save-insurance-info') ?>" method="POST" class="flex flex-col h-full">
                <?= csrf_field() ?>
                <input type="hidden" name="patient_id" value="<?= $patient['id'] ?>">

                <div class="p-6 space-y-6 overflow-y-auto">

                    <!-- Insurance Toggle -->
                    <div class="form-section">
                        <label class="block text-sm font-semibold text-slate-700 mb-3">Do you have dental insurance coverage?</label>
                        <div class="radio-group">
                            <label class="radio-option">
                                <input type="radio" name="has_insurance" id="ins_yes" value="1"
                                    <?= old('has_insurance', $patient['has_insurance'] ?? 0) == 1 ? 'checked' : '' ?>
                                    onchange="toggleInsuranceFields(true)">
                                Yes, I have insurance
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="has_insurance" id="ins_no" value="0"
                                    <?= old('has_insurance', $patient['has_insurance'] ?? 0) != 1 ? 'checked' : '' ?>
                                    onchange="toggleInsuranceFields(false)">
                                No, I'll pay out-of-pocket
                            </label>
                        </div>
                    </div>

                    <!-- Conditional Insurance Fields -->
                    <div id="insuranceFields" class="<?= (old('has_insurance', $patient['has_insurance'] ?? 0) == 1) ? '' : 'hidden' ?> space-y-4 transition-all duration-200">

                        <!-- Insurance Provider -->
                        <div class="floating-label-group">
                            <input type="text" name="insurance_provider" id="insurance_provider"
                                value="<?= old('insurance_provider', $patient['insurance_provider'] ?? '') ?>"
                                placeholder=" "
                                class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"
                                <?= (old('has_insurance', $patient['has_insurance'] ?? 0) != 1) ? 'disabled' : '' ?>>
                            <label class="floating-label">Insurance Provider / Company Name</label>
                        </div>

                        <!-- Valid Until -->
                        <div class="floating-label-group">
                            <input type="date" name="insurance_valid_until" id="insurance_valid_until"
                                value="<?= old('insurance_valid_until', $patient['insurance_valid_until'] ?? '') ?>"
                                class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"
                                <?= (old('has_insurance', $patient['has_insurance'] ?? 0) != 1) ? 'disabled' : '' ?>
                                min="<?= date('Y-m-d') ?>">
                            <label class="floating-label">Policy Valid Until</label>
                            <p class="text-[10px] text-slate-400 mt-1"><i class="fas fa-info-circle mr-1"></i>Required if you have active coverage</p>
                        </div>

                    </div>

                    <!-- Info Box -->
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs text-emerald-800">
                        <i class="fas fa-lightbulb mr-1"></i>
                        <strong>Tip:</strong> You can update or add insurance details anytime from your profile settings.
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t bg-slate-50 flex justify-end gap-3">
                    <button type="button" onclick="closeInsuranceModal()" class="px-5 py-2.5 border border-slate-300 text-slate-600 rounded-lg font-medium hover:bg-slate-50 transition-colors">
                        Skip for Now
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 text-white rounded-lg font-semibold hover:bg-emerald-700 shadow-sm transition-all flex items-center gap-2">
                        <i class="fas fa-check-circle"></i> Save Insurance Details
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<!-- ============================================ -->
<!-- MEDICAL HISTORY MODAL - EXACT COPY FROM appointment.php -->
<!-- ============================================ -->
<?php if ($showMedicalModal && !$showPersonalInfoModal): ?>
    <div id="medicalHistoryModal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-start justify-center p-4 z-[100] overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl my-4 flex flex-col overflow-hidden border border-slate-200">

            <!-- Modal Header -->
            <div class="p-4 border-b flex justify-between items-center bg-slate-50">
                <h4 class="font-bold text-lg text-slate-800 uppercase flex items-center gap-2">
                    <i class="fas fa-file-medical text-blue-600"></i> Medical History Assessment
                </h4>
                <button type="button" onclick="closeMedicalModal()" class="text-slate-400 text-2xl hover:text-slate-600 transition-colors">&times;</button>
            </div>

            <form action="<?= base_url('patient/save-medical-history') ?>" method="POST" class="flex flex-col h-full" id="medicalHistoryForm">
                <?= csrf_field() ?>
                <input type="hidden" name="patient_id" value="<?= $patient['id'] ?>">

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

                <div class="p-6 space-y-6 overflow-y-auto">

                    <!-- 👨‍⚕️ Physician Info -->
                    <div class="p-5 bg-blue-50/50 border border-blue-100 rounded-2xl space-y-6">
                        <h5 class="text-xs font-black text-blue-500 uppercase italic flex items-center gap-2">
                            <i class="fas fa-user-md"></i> Attending Physician
                        </h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="floating-label-group">
                                <input type="text" name="physician_name" class="name-only floating-input w-full p-2.5 border rounded-lg text-sm bg-white" placeholder=" ">
                                <label class="floating-label">Physician’s Name</label>
                            </div>
                            <div class="floating-label-group"><input type="text" name="physician_specialty" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Speciality</label></div>
                            <div class="floating-label-group"><input type="text" name="physician_address" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Office Address</label></div>
                            <div class="floating-label-group"><input type="text" name="physician_phone" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Tel. No.</label></div>
                        </div>
                    </div>

                    <!-- ❓ Health Questions 1-7 -->
                    <div class="space-y-4 text-sm bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
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
                            <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-2 md:gap-4 border-b border-slate-50 pb-3 last:border-0 last:pb-0">
                                <label class="font-medium text-slate-700"><?= $label ?></label>
                                <div class="flex gap-4 shrink-0">
                                    <label class="flex items-center gap-1 cursor-pointer font-bold hover:text-blue-600 transition-colors">
                                        <input type="radio" name="<?= $key ?>" value="1" id="<?= $key ?>_yes" class="accent-blue-600 w-4 h-4"> Yes
                                    </label>
                                    <label class="flex items-center gap-1 cursor-pointer font-bold hover:text-blue-600 transition-colors">
                                        <input type="radio" name="<?= $key ?>" value="0" id="<?= $key ?>_no" checked class="accent-blue-600 w-4 h-4"> No
                                    </label>
                                </div>
                            </div>
                            <!-- Conditional details inputs -->
                            <?php if ($key == 'has_serious_illness'): ?>
                                <input type="text" name="serious_illness_details" id="has_serious_illness_details" disabled placeholder="If yes, what illness or operation?" class="w-full p-2 border-b border-slate-200 bg-slate-50 rounded-t text-xs outline-none focus:border-blue-500 mb-2 transition-all disabled:opacity-50 disabled:bg-transparent">
                            <?php elseif ($key == 'is_hospitalized'): ?>
                                <input type="text" name="hospitalization_details" id="is_hospitalized_details" disabled placeholder="If yes, when and why?" class="w-full p-2 border-b border-slate-200 bg-slate-50 rounded-t text-xs outline-none focus:border-blue-500 mb-2 transition-all disabled:opacity-50 disabled:bg-transparent">
                            <?php elseif ($key == 'is_taking_medication'): ?>
                                <input type="text" name="medication_details" id="is_taking_medication_details" disabled placeholder="If yes, please specify..." class="w-full p-2 border-b border-slate-200 bg-slate-50 rounded-t text-xs outline-none focus:border-blue-500 mb-2 transition-all disabled:opacity-50 disabled:bg-transparent">
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                    <!-- 🤧 Allergies Checklist -->
                    <div class="space-y-3 pt-2">
                        <label class="font-bold text-slate-800">8. Are you allergic to any of the following?</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <?php foreach ($allergies as $c): ?>
                                <label class="flex items-center text-xs cursor-pointer hover:text-blue-600 transition-colors">
                                    <input type="checkbox" name="medical_conditions[]" value="<?= esc($c['condition_key'] ?? $c['id']) ?>" class="mr-2 w-4 h-4 accent-blue-600 rounded">
                                    <?= esc($c['condition_label']) ?>
                                </label>
                            <?php endforeach; ?>
                            <!-- OTHER ALLERGY -->
                            <div class="flex items-center gap-2 md:col-span-2 mt-1">
                                <span class="text-xs font-semibold text-slate-600">Others:</span>
                                <input type="text" name="other_allergy" placeholder="Specify other allergies..." class="flex-1 border-b border-slate-300 text-xs p-1 outline-none bg-transparent focus:border-blue-500 transition-colors">
                            </div>
                        </div>
                    </div>

                    <!-- ⏱️🩸 Vitals Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">

                        <!-- Bleeding Time -->
                        <div class="bg-amber-50/50 p-4 rounded-xl border border-amber-100">
                            <label class="font-bold text-amber-800 mb-3 block text-xs uppercase flex items-center gap-2">
                                <i class="fas fa-stopwatch text-amber-500"></i> 9. Bleeding Time
                            </label>
                            <div class="flex items-center gap-3">
                                <div class="flex-1">
                                    <div class="floating-label-group">
                                        <input type="number" name="bleeding_mins" id="bleeding_mins" min="0" max="59" placeholder=" " value="<?= old('bleeding_mins') ?>" class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white">
                                        <label class="floating-label">Minutes</label>
                                    </div>
                                </div>
                                <span class="text-slate-400 font-bold">:</span>
                                <div class="flex-1">
                                    <div class="floating-label-group">
                                        <input type="number" name="bleeding_secs" id="bleeding_secs" min="0" max="59" placeholder=" " value="<?= old('bleeding_secs') ?>" class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white">
                                        <label class="floating-label">Seconds</label>
                                    </div>
                                </div>
                                <input type="hidden" name="bleeding_time_combined" id="bleeding_time_combined">
                            </div>
                        </div>

                        <!-- Blood Pressure & Type -->
                        <div class="bg-red-50/50 p-4 rounded-xl border border-red-100">
                            <label class="font-bold text-red-800 mb-3 block text-xs uppercase flex items-center gap-2">
                                <i class="fas fa-heartbeat text-red-500"></i> Blood Information
                            </label>
                            <div class="flex gap-4">
                                <div class="flex-1">
                                    <div class="floating-label-group">
                                        <select name="blood_type" class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white">
                                            <option value="" selected disabled hidden></option>
                                            <option value="A+" <?= old('blood_type') == 'A+' ? 'selected' : '' ?>>A+</option>
                                            <option value="A-" <?= old('blood_type') == 'A-' ? 'selected' : '' ?>>A-</option>
                                            <option value="B+" <?= old('blood_type') == 'B+' ? 'selected' : '' ?>>B+</option>
                                            <option value="B-" <?= old('blood_type') == 'B-' ? 'selected' : '' ?>>B-</option>
                                            <option value="AB+" <?= old('blood_type') == 'AB+' ? 'selected' : '' ?>>AB+</option>
                                            <option value="AB-" <?= old('blood_type') == 'AB-' ? 'selected' : '' ?>>AB-</option>
                                            <option value="O+" <?= old('blood_type') == 'O+' ? 'selected' : '' ?>>O+</option>
                                            <option value="O-" <?= old('blood_type') == 'O-' ? 'selected' : '' ?>>O-</option>
                                            <option value="Unknown" <?= old('blood_type') == 'Unknown' ? 'selected' : '' ?>>Unknown</option>
                                        </select>
                                        <label class="floating-label">Blood Type</label>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="floating-label-group">
                                        <input type="text" name="blood_pressure" id="blood_pressure" pattern="^\d{2,3}/\d{2,3}$" value="<?= old('blood_pressure') ?>" class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white">
                                        <label class="floating-label">Blood Pressure (e.g., 120/80)</label>
                                    </div>
                                </div>
                            </div>
                            <!-- Hidden fields for legacy database support -->
                            <input type="hidden" name="blood_pressure_systolic" id="bp_sys">
                            <input type="hidden" name="blood_pressure_diastolic" id="bp_dia">
                        </div>
                    </div>

                    <!-- 👩 Women's Section (Conditional) -->
                    <?php if (($patient['gender'] ?? '') !== 'Male'): ?>
                        <div id="women_section" class="bg-pink-50/50 p-5 rounded-xl border border-pink-200 space-y-4 transition-all">
                            <label class="font-bold text-pink-700 uppercase text-xs flex items-center gap-2">
                                <i class="fas fa-venus"></i> 10. For Women Only:
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-pink-100 shadow-sm">
                                    <span class="text-xs font-semibold text-slate-700">Are you pregnant?</span>
                                    <div class="flex gap-3">
                                        <label class="text-xs cursor-pointer"><input type="radio" name="is_pregnant" value="1" class="accent-pink-500 w-3.5 h-3.5"> Yes</label>
                                        <label class="text-xs cursor-pointer"><input type="radio" name="is_pregnant" value="0" checked class="accent-pink-500 w-3.5 h-3.5"> No</label>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-pink-100 shadow-sm">
                                    <span class="text-xs font-semibold text-slate-700">Are you nursing?</span>
                                    <div class="flex gap-3">
                                        <label class="text-xs cursor-pointer"><input type="radio" name="is_nursing" value="1" class="accent-pink-500 w-3.5 h-3.5"> Yes</label>
                                        <label class="text-xs cursor-pointer"><input type="radio" name="is_nursing" value="0" checked class="accent-pink-500 w-3.5 h-3.5"> No</label>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-pink-100 shadow-sm">
                                    <span class="text-xs font-semibold text-slate-700">Birth control pills?</span>
                                    <div class="flex gap-3">
                                        <label class="text-xs cursor-pointer"><input type="radio" name="is_taking_birth_control" value="1" class="accent-pink-500 w-3.5 h-3.5"> Yes</label>
                                        <label class="text-xs cursor-pointer"><input type="radio" name="is_taking_birth_control" value="0" checked class="accent-pink-500 w-3.5 h-3.5"> No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- 📋 Medical Checklist -->
                    <div class="border-t border-slate-200 pt-5">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shadow-inner">
                                <i class="fas fa-notes-medical text-lg"></i>
                            </div>
                            <div>
                                <label class="text-sm font-bold text-slate-800">Medical History Checklist</label>
                                <p class="text-[10px] text-slate-500">Please check all conditions that apply to the patient.</p>
                            </div>
                        </div>

                        <div class="bg-slate-50 p-5 rounded-xl border border-slate-200" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.5rem 1rem;">
                            <?php foreach ($grouped as $cat => $conditions): ?>
                                <div class="col-span-full text-[11px] font-black text-blue-500 uppercase mt-4 mb-2 border-b border-blue-100 pb-1 first:mt-0">
                                    <?= esc($cat) ?>
                                </div>
                                <?php foreach ($conditions as $c): ?>
                                    <label class="flex items-center text-xs text-slate-700 cursor-pointer hover:text-blue-600 transition-colors py-1">
                                        <input type="checkbox" name="medical_conditions[]" value="<?= esc($c['condition_key']) ?>" class="mr-2 w-4 h-4 accent-blue-600 rounded border-slate-300">
                                        <?= esc($c['condition_label']) ?>
                                    </label>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t bg-slate-50 flex justify-end gap-3">
                    <button type="button" onclick="closeMedicalModal()" class="px-5 py-2.5 border border-slate-300 text-slate-600 rounded-lg font-medium hover:bg-slate-50 transition-colors">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 shadow-sm transition-all flex items-center gap-2">
                        <i class="fas fa-check-circle"></i> Submit Medical Record
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<!-- Auto-show modal + JS helpers -->
<script>
    // ===== GLOBAL CONFIG: isaacdarcilla/philippine-addresses Format =====
    const BASE_URL = "<?= base_url('data/ph-addresses/') ?>";

    // ✅ Dropdown ID Mapping (MATCH YOUR HTML)
    const DROPDOWN_IDS = {
        region: 'reg_select',
        province: 'prov_select',
        city: 'city_select',
        barangay: 'brgy_select'
    };

    // ✅ JSON File Mapping (SINGULAR filenames)
    const JSON_FILES = {
        region: 'region.json',
        province: 'province.json',
        city: 'city.json',
        barangay: 'barangay.json'
    };

    // ✅ Field Mapping for isaacdarcilla format
    const FIELDS = {
        region: {
            name: 'region_name',
            code: 'region_code',
            filterBy: null
        },
        province: {
            name: 'province_name',
            code: 'province_code',
            filterBy: 'region_code'
        },
        city: {
            name: 'city_name',
            code: 'city_code',
            filterBy: 'province_code'
        },
        barangay: {
            name: 'brgy_name',
            code: 'brgy_code',
            filterBy: 'city_code'
        }
    };

    // ===== DOMContentLoaded =====
    document.addEventListener('DOMContentLoaded', async function() {
        // 1. Load Regions first
        const regionsLoaded = await loadAddressLevel('region');

        // 2. Pre-fill from database if patient has address data
        <?php if ($showPersonalInfoModal && !empty($patient['region'])): ?>
            if (regionsLoaded) {
                setTimeout(async () => {
                    await prefillAddressFromDB({
                        region: "<?= esc($patient['region'] ?? '', 'js') ?>",
                        province: "<?= esc($patient['province'] ?? '', 'js') ?>",
                        city: "<?= esc($patient['city'] ?? '', 'js') ?>",
                        barangay: "<?= esc($patient['barangay'] ?? '', 'js') ?>"
                    });
                }, 250);
            }
        <?php endif; ?>

        // 3. Show modal if needed
        <?php if ($showPersonalInfoModal): ?>
            document.getElementById('personalInfoModal')?.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        <?php endif; ?>
    });

    function closePersonalInfoModal() {
        const modal = document.getElementById('personalInfoModal');
        modal?.classList.add('hidden');
        document.body.style.overflow = '';
    }

    // ===== Mobile Number Formatting (PH) =====
    function formatPHMobile(input) {
        // 1. Remove all non-digits
        let val = input.value.replace(/\D/g, '');

        // 2. Extract suffix (digits AFTER the 09 prefix)
        let suffix = '';
        if (val.startsWith('09')) {
            suffix = val.substring(2);
        } else if (val.startsWith('639')) {
            // Convert international format to local
            suffix = val.substring(3);
        } else {
            // Remove any leading 0 or 9, treat the rest as suffix
            suffix = val.replace(/^0?9?/, '');
        }

        // 3. Limit suffix to 9 digits (09 + 9 = 11 digits total)
        if (suffix.length > 9) {
            suffix = suffix.substring(0, 9);
        }

        // 4. 🔒 LOCKED: Always reconstruct with "09" prefix
        val = '09' + suffix;

        // 5. Format for display: 09XX XXX XXXX
        if (val.length >= 4) {
            val = val.replace(/(\d{4})(\d{1,3})/, '$1 $2');
        }
        if (val.length >= 8) {
            val = val.replace(/(\d{4}\s\d{3})(\d{1,4})/, '$1 $2');
        }

        // 6. Update input value
        input.value = val;
    }

    // Attach to mobile inputs
    document.querySelectorAll('input[name="primary_mobile"], input[name="guardian_mobile"]').forEach(el => {
        el.addEventListener('input', (e) => formatPHMobile(e.target));
        // Format existing value on load
        if (el.value) formatPHMobile(el);
    });

    // ===== LOAD ADDRESS LEVEL (Generic for isaacdarcilla format) =====
    async function loadAddressLevel(level, parentCode = null) {
        const cfg = FIELDS[level];
        const select = document.getElementById(DROPDOWN_IDS[level]);
        if (!select) {
            console.warn(`Dropdown #${DROPDOWN_IDS[level]} not found`);
            return [];
        }

        const nextLevel = {
            region: 'province',
            province: 'city',
            city: 'barangay'
        } [level];
        const nextSelect = nextLevel ? document.getElementById(DROPDOWN_IDS[nextLevel]) : null;

        // Reset dropdown
        select.innerHTML = '<option value="">Loading...</option>';
        select.disabled = true;

        // If no parent code (and level needs one), reset and return
        if (cfg.filterBy && !parentCode) {
            select.innerHTML = `<option value="">Select ${level}</option>`;
            if (nextSelect) {
                nextSelect.innerHTML = `<option value="">Select ${nextLevel}</option>`;
                nextSelect.disabled = true;
            }
            return [];
        }

        try {
            // Fetch JSON file
            const res = await fetch(`${BASE_URL}${JSON_FILES[level]}`);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            let data = await res.json();
            if (!Array.isArray(data)) data = [];

            // Filter by parent code if needed (e.g., provinces filtered by region_code)
            if (cfg.filterBy && parentCode) {
                data = data.filter(item => item[cfg.filterBy] == parentCode);
            }

            // Sort alphabetically by name field
            data.sort((a, b) => (a[cfg.name] || '').localeCompare(b[cfg.name] || ''));

            // Populate dropdown: value=NAME (for form submission), data-code=CODE (for filtering)
            select.innerHTML = `<option value="">Select ${level}</option>`;
            data.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item[cfg.name] || ''; // Submit NAME to database
                opt.textContent = item[cfg.name] || ''; // Display NAME to user
                opt.dataset.code = item[cfg.code] || ''; // Store CODE for filtering children
                select.appendChild(opt);
            });

            select.disabled = false;

            // Reset next level dropdown
            if (nextSelect) {
                nextSelect.innerHTML = `<option value="">Select ${nextLevel}</option>`;
                nextSelect.disabled = true;
            }

            return data;

        } catch (err) {
            console.error(`Error loading ${level}:`, err);
            select.innerHTML = `<option value="">⚠ Failed to load ${level}</option>`;
            select.disabled = true;
            return [];
        }
    }

    // ===== 🔥 PRE-FILL FROM DATABASE VALUES 🔥 =====
    async function prefillAddressFromDB(patient) {
        const {
            region,
            province,
            city,
            barangay
        } = patient;

        // Helper: Find and select option by NAME (case-insensitive, trimmed)
        const selectByName = (selectId, targetName) => {
            const select = document.getElementById(selectId);
            if (!select || !targetName?.trim()) return null;

            const normalized = targetName.trim().toLowerCase();
            for (let opt of select.options) {
                if (opt.value && opt.value.trim().toLowerCase() === normalized) {
                    select.value = opt.value;
                    return opt; // Return the matched option
                }
            }
            return null;
        };

        // Step 1: Select Region by NAME
        if (region?.trim()) {
            const regionOpt = selectByName(DROPDOWN_IDS.region, region);
            if (regionOpt) {
                const regionCode = regionOpt.dataset.code;
                await delay(100);

                // Step 2: Load & Select Province
                if (regionCode && province?.trim()) {
                    await loadAddressLevel('province', regionCode);
                    await delay(100);

                    const provinceOpt = selectByName(DROPDOWN_IDS.province, province);
                    if (provinceOpt) {
                        const provinceCode = provinceOpt.dataset.code;

                        // Step 3: Load & Select City
                        if (provinceCode && city?.trim()) {
                            await loadAddressLevel('city', provinceCode);
                            await delay(100);

                            const cityOpt = selectByName(DROPDOWN_IDS.city, city);
                            if (cityOpt) {
                                const cityCode = cityOpt.dataset.code;

                                // Step 4: Load & Select Barangay
                                if (cityCode && barangay?.trim()) {
                                    await loadAddressLevel('barangay', cityCode);
                                    await delay(100);

                                    selectByName(DROPDOWN_IDS.barangay, barangay);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    // ===== HELPER: Promise-based delay =====
    function delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    function closeMedicalModal() {
        const modal = document.getElementById('medicalHistoryModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    // Auto-show modal if needed
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($showMedicalModal && !$showPersonalInfoModal): ?>
            const modal = document.getElementById('medicalHistoryModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        <?php endif; ?>

            // ===== Conditional Fields Toggle =====
            ['has_serious_illness', 'is_hospitalized', 'is_taking_medication'].forEach(key => {
                const radios = document.querySelectorAll(`input[name="${key}"]`);
                const detail = document.getElementById(`${key}_details`);
                if (radios.length && detail) {
                    radios.forEach(r => r.addEventListener('change', () => {
                        detail.disabled = (document.querySelector(`input[name="${key}"]:checked`)?.value === '0');
                        if (detail.disabled) detail.value = '';
                    }));
                    // Init state
                    const checked = document.querySelector(`input[name="${key}"]:checked`);
                    if (checked) detail.disabled = (checked.value === '0');
                }
            });

        // ===== Blood Pressure: Format + Split to Hidden Fields =====
        const bpInput = document.getElementById('blood_pressure');
        const bpSys = document.getElementById('bp_sys');
        const bpDia = document.getElementById('bp_dia');
        if (bpInput) {
            bpInput.addEventListener('input', function(e) {
                let val = e.target.value.replace(/[^\d\/]/g, '');
                if (val.length >= 3 && !val.includes('/')) val = val.slice(0, 3) + '/' + val.slice(3);
                const parts = val.split('/');
                if (parts[0]) parts[0] = parts[0].slice(0, 3);
                if (parts[1]) parts[1] = parts[1].slice(0, 3);
                e.target.value = parts.join('/');
                if (bpSys && bpDia) {
                    const [s, d] = e.target.value.split('/');
                    bpSys.value = s || '';
                    bpDia.value = d || '';
                }
            });
            bpInput.addEventListener('blur', function(e) {
                if (e.target.value && !/^\d{2,3}\/\d{2,3}$/.test(e.target.value)) {
                    e.target.setCustomValidity('Format: XXX/XXX (e.g., 120/80)');
                } else e.target.setCustomValidity('');
            });
        }

        // ===== Bleeding Time: Combine to Hidden Field =====
        const bMins = document.getElementById('bleeding_mins');
        const bSecs = document.getElementById('bleeding_secs');
        const bComb = document.getElementById('bleeding_time_combined');

        function updateBleed() {
            if (bComb) bComb.value = `${bMins?.value||0}m ${bSecs?.value||0}s`;
        }
        if (bMins) bMins.addEventListener('input', updateBleed);
        if (bSecs) bSecs.addEventListener('input', updateBleed);
        updateBleed();

        // ===== Hide Women's Section if Male =====
        <?php if (($patient['gender'] ?? '') === 'Male'): ?>
            const ws = document.getElementById('women_section');
            if (ws) ws.style.display = 'none';
        <?php endif; ?>
    });

    // ===== Form Submit Validation =====
    document.getElementById('medicalHistoryForm')?.addEventListener('submit', function(e) {
        const bp = document.getElementById('blood_pressure');
        if (bp && bp.value && !/^\d{2,3}\/\d{2,3}$/.test(bp.value.trim())) {
            e.preventDefault();
            alert('Blood Pressure must be XXX/XXX (e.g., 120/80)');
            bp.focus();
            return false;
        }
        // Update bleeding time combined
        const bMins = document.getElementById('bleeding_mins');
        const bSecs = document.getElementById('bleeding_secs');
        const bComb = document.getElementById('bleeding_time_combined');
        if (bComb && bMins && bSecs) bComb.value = `${bMins.value||0}m ${bSecs.value||0}s`;
    });
</script>
<?= $this->endSection() ?>