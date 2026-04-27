<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
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

    .slot-btn:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .slot-btn:active:not(:disabled) {
        transform: translateY(0);
    }

    /* ===== DAISYUI-STYLE UNIVERSAL NOTIFICATIONS ===== */
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(2rem);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Mobile responsive */
    @media (max-width: 640px) {
        .notification-container {
            left: 1rem;
            right: 1rem;
            max-width: none;
        }
    }
</style>
<!-- Universal Notification Container -->
<!-- UNIVERSAL ALERTS (PHP Flash Messages) -->

<div class="flex justify-between items-center mb-6">
    <h3 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
        <i class="fas fa-calendar-check text-blue-600"></i>
        Appointments
    </h3>
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
                <option value="<?= $d['id'] ?>"><?= esc($d['first_name'] . ' ' . $d['last_name']) ?></option>
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
<!-- QUICK TIMEFRAME TABS -->
<div class="flex flex-wrap gap-2 mb-4">
    <?php
    $tabConfig = [
        'today'    => ['label' => 'Today', 'count' => $counts['today']],
        'tomorrow' => ['label' => 'Tomorrow', 'count' => $counts['tomorrow']],
        'upcoming' => ['label' => 'Upcoming', 'count' => $counts['upcoming']],
        'all'      => ['label' => 'All Appointments', 'count' => null]
    ];

    foreach ($tabConfig as $key => $cfg):
        $isActive = ($currentTab === $key);
        $btnClass = $isActive
            ? 'bg-blue-600 text-white shadow-md border-blue-600'
            : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50';
    ?>
        <a href="?tab=<?= $key ?>"
            class="px-4 py-2 rounded-lg font-bold text-xs uppercase transition-all border flex items-center gap-2 <?= $btnClass ?>">
            <?= $cfg['label'] ?>
            <?php if ($cfg['count'] !== null): ?>
                <span class="<?= $isActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' ?> px-1.5 py-0.5 rounded text-[10px]">
                    <?= $cfg['count'] ?>
                </span>
            <?php endif; ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- TABLE -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full text-left border-collapse table-auto">
        <thead class="bg-slate-50 text-slate-500 text-[10px] uppercase font-bold tracking-wider">
            <tr>
                <th class="p-3 border-b">Patient Info</th>
                <th class="p-3 border-b">Dentist</th>
                <th class="p-3 border-b">Schedule</th>
                <th class="p-3 border-b">Service</th>
                <th class="p-3 border-b text-center">Status</th>
                <th class="p-3 border-b text-center">Actions</th>
            </tr>
        </thead>
        <tbody id="appointmentTable" class="divide-y divide-slate-100">
            <?php if (empty($appointments)): ?>
                <tr>
                    <td colspan="6" class="p-12 text-center text-slate-400 text-xs">No appointments found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($appointments as $a): ?>
                    <?php
                    $apptEndTimestamp = strtotime($a['appointment_date'] . ' ' . $a['end_time']);
                    $isLapsed = (time() > $apptEndTimestamp) && (strtolower($a['status'] ?? '') === 'confirmed');
                    $status = strtolower(trim($a['status'] ?? ''));
                    $bookedBy = $a['booked_by'] ?? 'receptionist';
                    ?>
                    <tr class="appt-row hover:bg-slate-50/50 transition-colors <?= str_replace('_', ' ', $status) === 'cancellation requested' ? 'bg-amber-50/40' : '' ?>"
                        data-status="<?= $a['status'] ?>" data-dentist="<?= $a['dentist_id'] ?>" data-date="<?= $a['appointment_date'] ?>">

                        <!-- Patient Info -->
                        <td class="p-3">
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-700 text-xs leading-tight"><?= esc($a['patient_name']) ?></span>
                                <?php if (!empty($a['patient_code'])): ?>
                                    <span class="text-[9px] font-mono font-bold text-blue-600 uppercase"><?= esc($a['patient_code']) ?></span>
                                <?php endif; ?>
                            </div>
                        </td>

                        <!-- Dentist -->
                        <td class="p-3">
                            <span class="text-[11px] font-medium text-slate-600 flex items-center gap-1">
                                <i class="fas fa-user-md text-blue-400 text-[10px]"></i>
                                <?= esc($a['dentist_name'] ?: 'N/A') ?>
                            </span>
                        </td>

                        <!-- Schedule -->
                        <td class="p-3 whitespace-nowrap">
                            <div class="flex flex-col text-[11px]">
                                <span class="font-bold text-slate-700 flex items-center gap-1">
                                    <i class="far fa-calendar text-slate-400 text-[10px]"></i>
                                    <?= date('M d, Y', strtotime($a['appointment_date'])) ?>
                                </span>
                                <span class="text-slate-500 font-medium italic">
                                    <?= date('h:i A', strtotime($a['appointment_time'])) ?> - <?= date('h:i A', strtotime($a['end_time'])) ?>
                                </span>
                            </div>
                        </td>

                        <!-- Service -->
                        <td class="p-3">
                            <span class="text-[11px] text-slate-600 italic line-clamp-1">
                                <?= esc($a['service_name'] ?: 'No service') ?>
                            </span>
                        </td>

                        <!-- Status Badge -->
                        <td class="p-3 text-center align-middle">
                            <?php
                            $c = \App\Constants\AppointmentStatus::color($a['status'] ?? '');
                            $statusLabel = ucwords(str_replace('_', ' ', $a['status'] ?? ''));
                            ?>
                            <div class="flex flex-col items-center gap-1">
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-<?= $c ?>-100 text-<?= $c ?>-700 border border-<?= $c ?>-200">
                                    <?= esc($statusLabel) ?>
                                </span>
                                <?php if ($isLapsed): ?>
                                    <span class="text-[8px] font-black text-red-600 uppercase flex items-center gap-0.5 animate-pulse">
                                        <i class="fas fa-clock"></i> Lapsed
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($a['cancellation_denied_at'])): ?>
                                    <span class="text-[8px] text-red-400 font-bold uppercase" title="<?= esc($a['cancellation_denied_reason']) ?>">
                                        (Request Denied)
                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>

                        <!-- Action Buttons -->
                        <td class="p-3 text-center">
                            <div class="flex justify-center items-center gap-1">

                                <!-- 1. PRIMARY WORKFLOW ACTIONS -->
                                <?php if (str_replace('_', ' ', $status) === 'cancellation requested'): ?>
                                    <button type="button" onclick="confirmDirectApprove(<?= $a['id'] ?>, '<?= esc($a['patient_name'], 'js') ?>')"
                                        class="bg-green-600 text-white px-2 py-1 rounded text-[10px] font-bold uppercase hover:bg-green-700 transition-all flex items-center gap-1" title="Approve Request">
                                        <i class="fas fa-check-circle"></i> Approve
                                    </button>
                                    <button type="button" onclick="openDenyCancelModal(<?= $a['id'] ?>, '<?= esc($a['patient_name'], 'js') ?>')"
                                        class="bg-red-500 text-white px-2 py-1 rounded text-[10px] font-bold uppercase hover:bg-red-600 transition-all flex items-center gap-1" title="Deny Request">
                                        <i class="fas fa-times-circle"></i> Deny
                                    </button>

                                <?php elseif ($status === 'pending'): ?>
                                    <a href="<?= base_url('receptionist/appointments/status/' . $a['id'] . '/confirmed') ?>"
                                        class="bg-blue-600 text-white px-2 py-1 rounded text-[10px] font-bold uppercase hover:bg-blue-700 flex items-center gap-1">
                                        <i class="fas fa-calendar-check"></i> Confirm
                                    </a>
                                    <button type="button" onclick="<?= ($bookedBy === 'patient') ? 'openRejectModal(' . $a['id'] . ',\'' . esc($a['patient_name'], 'js') . '\')' : 'openCancelModal(' . $a['id'] . ',\'' . esc($a['patient_name'], 'js') . '\')' ?>"
                                        class="text-red-500 hover:bg-red-50 px-2 py-1 rounded text-[10px] font-bold uppercase flex items-center gap-1">
                                        <i class="fas fa-ban"></i> <?= ($bookedBy === 'patient') ? 'Reject' : 'Cancel' ?>
                                    </button>

                                <?php elseif ($status === 'confirmed'): ?>
                                    <a href="<?= base_url('receptionist/appointments/status/' . $a['id'] . '/completed') ?>"
                                        class="bg-green-600 text-white px-2 py-1 rounded text-[10px] font-bold uppercase hover:bg-green-700 flex items-center gap-1">
                                        <i class="fas fa-check"></i> Done
                                    </a>
                                    <?php if ($isLapsed): ?>
                                        <button type="button" onclick="confirmNoShow(<?= $a['id'] ?>, '<?= esc($a['patient_name'], 'js') ?>')"
                                            class="bg-slate-800 text-white px-2 py-1 rounded text-[10px] font-bold uppercase hover:bg-black flex items-center gap-1">
                                            <i class="fas fa-user-slash"></i> No-Show
                                        </button>
                                    <?php endif; ?>
                                    <button onclick="openRescheduleModal(<?= $a['id'] ?>, '<?= $a['appointment_date'] ?>', '<?= $a['appointment_time'] ?>', '<?= $a['end_date'] ?>', '<?= $a['end_time'] ?>', '<?= $a['dentist_id'] ?>')"
                                        class="bg-amber-100 text-amber-700 px-2 py-1 rounded text-[10px] font-bold uppercase hover:bg-amber-200 flex items-center gap-1">
                                        <i class="fas fa-clock-rotate-left"></i> Resched
                                    </button>
                                <?php endif; ?>

                                <!-- 2. UTILITY ACTIONS (Separator) -->
                                <div class="w-[1px] h-4 bg-slate-200 mx-1"></div>

                                <!-- View History -->
                                <a href="<?= base_url('receptionist/appointments/history/' . $a['patient_id']) ?>" target="_blank"
                                    class="bg-slate-100 text-slate-500 hover:bg-slate-800 hover:text-white w-7 h-7 rounded flex items-center justify-center transition-all" title="Patient History">
                                    <i class="fas fa-history text-[11px]"></i>
                                </a>

                                <!-- View Request (Amber Eye) -->
                                <?php if (str_replace('_', ' ', $status) === 'cancellation requested'): ?>
                                    <button type="button" data-id="<?= $a['id'] ?>" data-name="<?= esc($a['patient_name'], 'html') ?>"
                                        data-reason="<?= esc($a['cancel_request_reason'] ?? 'No reason', 'html') ?>"
                                        data-date="<?= esc(date('M d, Y', strtotime($a['appointment_date']))) ?>"
                                        data-time="<?= esc(date('h:i A', strtotime($a['appointment_time']))) ?>"
                                        data-dentist="<?= esc($a['dentist_name'] ?? 'N/A') ?>"
                                        data-service="<?= esc($a['service_name'] ?? 'N/A') ?>"
                                        onclick="showCancellationDetails(this)"
                                        class="bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white w-7 h-7 rounded flex items-center justify-center transition-all">
                                        <i class="fas fa-eye text-[11px]"></i>
                                    </button>
                                <?php endif; ?>

                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
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

                                <div class="floating-label-group md:col-span-1">
                                    <input type="text" name="first_name" class="name-only floating-input w-full p-2.5 border rounded-lg text-sm bg-white" placeholder=" ">
                                    <label class="floating-label">First Name *</label>
                                </div>
                                <div class="floating-label-group md:col-span-1">
                                    <input type="text" name="middle_name" class="name-only floating-input w-full p-2.5 border rounded-lg text-sm bg-white" placeholder=" ">
                                    <label class="floating-label">Middle Name</label>
                                </div>
                                <div class="floating-label-group md:col-span-1">
                                    <input type="text" name="last_name" class="name-only floating-input w-full p-2.5 border rounded-lg text-sm bg-white" placeholder=" ">
                                    <label class="floating-label">Last Name *</label>
                                </div>
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
                                    <!-- In services_container loop, replace level dropdown with: -->
                                    <div class="flex-1 level-div <?= $showL ? '' : 'hidden' ?>">
                                        <select name="levels[]" class="w-full p-2.5 border rounded-lg text-sm bg-white">
                                            <!-- Nakatago na ang Standard gamit ang class="hidden" -->
                                            <option value="Standard" class="hidden" <?= (!$showL || (isset($oldL[$idx]) && $oldL[$idx] == 'Standard')) ? 'selected' : '' ?>>Standard</option>
                                            <option value="Simple" <?= (isset($oldL[$idx]) && $oldL[$idx] == 'Simple') ? 'selected' : '' ?>>Simple</option>
                                            <option value="Moderate" <?= (isset($oldL[$idx]) && $oldL[$idx] == 'Moderate') ? 'selected' : '' ?>>Moderate</option>
                                            <option value="Severe" <?= (isset($oldL[$idx]) && $oldL[$idx] == 'Severe') ? 'selected' : '' ?>>Severe</option>
                                        </select>
                                    </div>
                                    <button type="button" onclick="removeServiceRow(this)" class="text-red-400 p-2.5 hover:text-red-600">&times;</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- TIME SLOT AVAILABILITY SECTION - SMART RANGE SELECTION -->
                    <div id="timeSlotSection" class="space-y-4 hidden">
                        <div class="flex items-center justify-between">
                            <label class="fixed-label text-blue-600 flex items-center gap-2">
                                <i class="fas fa-clock text-blue-500"></i>
                                Select Appointment Time *
                            </label>
                            <span id="durationBadge" class="hidden text-[10px] font-bold bg-blue-100 text-blue-700 px-2 py-1 rounded">
                                <i class="fas fa-hourglass-half mr-1"></i>
                                <span id="durationValue">0</span> mins
                            </span>
                        </div>

                        <!-- Legend -->
                        <div class="flex flex-wrap gap-3 text-[10px] text-slate-500">
                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-green-100 border border-green-300"></span> Available</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-red-100 border border-red-300 line-through"></span> Booked</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-blue-100 border-2 border-blue-500"></span> Start</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-emerald-100 border-2 border-emerald-500"></span> End</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-indigo-50 border border-indigo-200"></span> Range</span>
                        </div>

                        <!-- Selection Status -->
                        <div id="selectionStatus" class="hidden p-3 bg-indigo-50 border border-indigo-200 rounded-lg flex items-center justify-between">
                            <span class="text-xs font-bold text-indigo-700">
                                <i class="fas fa-arrows-left-right mr-1"></i>
                                <span id="selectionStatusText"></span>
                            </span>
                            <button type="button" onclick="clearSelection()" class="text-[10px] text-indigo-500 hover:text-indigo-700 font-bold underline">
                                Clear
                            </button>
                        </div>

                        <!-- Loading State -->
                        <div id="slotLoading" class="hidden text-center py-6">
                            <div class="inline-flex items-center gap-2 text-slate-500">
                                <div class="animate-spin rounded-full h-4 w-4 border-2 border-blue-600 border-t-transparent"></div>
                                <span class="text-xs">Checking dentist's schedule...</span>
                            </div>
                        </div>

                        <!-- Slots Grid -->
                        <div id="slotsContainer" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-1.5 max-h-48 overflow-y-auto p-2 bg-slate-50 rounded-xl border border-slate-200">
                            <!-- Slots injected via JS -->
                        </div>

                        <!-- Selected Range Display -->
                        <div id="selectedRangeDisplay" class="hidden p-3 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-700">
                                <i class="fas fa-check-circle mr-1"></i> Selected: <span id="selectedRangeLabel"></span>
                            </span>
                            <button type="button" onclick="clearSelection()" class="text-[10px] text-emerald-500 hover:text-emerald-700 font-bold underline">
                                Change
                            </button>
                        </div>

                        <!-- Error Message -->
                        <p id="slotError" class="hidden p-3 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r text-xs font-medium flex items-start gap-2">
                            <i class="fas fa-exclamation-circle mt-0.5 flex-shrink-0"></i>
                            <span id="slotErrorMessage">Please select a valid time slot.</span>
                        </p>
                    </div>
                    <!-- Sa ilalim ng "Select Appointment Time" label -->
                    <div class="flex items-center justify-between">
                        <label class="fixed-label text-blue-600 flex items-center gap-2">
                            <i class="fas fa-clock text-blue-500"></i>
                            Select Appointment Time *
                        </label>
                        <!-- ✅ DURATION BADGE -->
                        <span id="durationBadge" class="hidden text-[10px] font-bold bg-blue-100 text-blue-700 px-2 py-1 rounded">
                            <i class="fas fa-hourglass-half mr-1"></i>
                            <span id="durationValue">0</span> mins
                        </span>
                    </div>
                    <!-- FINAL SCHEDULE SECTION - Clean version -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-900 p-6 rounded-2xl">

                        <!-- START TIME (User selects via slot picker OR manual) -->
                        <div>
                            <label class="fixed-label text-slate-400 italic">Start Appointment *</label>
                            <div class="flex gap-2">
                                <input type="date"
                                    name="appointment_date"
                                    id="appt_date"
                                    value="<?= old('appointment_date') ?>"
                                    min="<?= date('Y-m-d') ?>"
                                    class="w-full p-2.5 rounded-lg text-sm bg-slate-800 text-white border-slate-700"
                                    required>
                                <!-- Visible time input (for UX only) -->
                                <input type="time"
                                    id="appt_time_visible"
                                    class="w-full p-2.5 rounded-lg text-sm bg-slate-800 text-white border-slate-700"
                                    readonly>
                            </div>
                            <p class="text-[9px] text-slate-500 mt-1">Select from available slots below.</p>
                        </div>

                        <!-- END TIME (Auto-calculated, display only) -->
                        <div>
                            <label class="fixed-label text-slate-400 italic">Expected End (Auto)</label>
                            <div class="flex gap-2">
                                <input type="date"
                                    id="end_date_visible"
                                    readonly
                                    class="w-full p-2.5 rounded-lg text-sm bg-slate-700 text-slate-300 border-slate-600 cursor-not-allowed">
                                <input type="time"
                                    id="end_time_visible"
                                    readonly
                                    class="w-full p-2.5 rounded-lg text-sm bg-slate-700 text-slate-300 border-slate-600 cursor-not-allowed">
                            </div>
                            <p class="text-[9px] text-blue-400 mt-1">
                                <i class="fas fa-calculator mr-1"></i>
                                Based on service duration
                            </p>
                        </div>
                    </div>

                    <!-- ✅ HIDDEN FIELDS FOR ACTUAL FORM SUBMISSION (JS populates these) -->
                    <input type="hidden" name="appointment_time" id="hidden_appt_time" required>
                    <input type="hidden" name="end_time" id="hidden_end_time" required>
                    <input type="hidden" name="end_date" id="hidden_end_date">
                </div>
                <div class="p-6 border-t bg-slate-50 flex flex-col gap-3">
                    <!-- BUTTON PARA SA EXISTING: Diretsong Save -->
                    <button type="submit" id="btn_confirm_existing" class="hidden w-full bg-blue-600 text-white py-4 rounded-xl font-bold uppercase tracking-widest hover:bg-blue-700 shadow-xl transition-all">
                        Confirm Appointment
                    </button>

                    <!-- BUTTON PARA SA NEW: Kailangan mag Step 2 -->
                    <button type="button" id="btn_next_new" onclick="goToStep(2)" class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold uppercase tracking-widest hover:bg-blue-700 shadow-xl transition-all">
                        Next: Medical History Record <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>
            <!-- PAGE 2: MEDICAL HISTORY -->
            <div id="step2_container" class="p-6 space-y-6 step-hidden">
                <div class="bg-blue-50/50 p-6 border border-blue-100 rounded-2xl space-y-6">

                    <!-- Physician Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="floating-label-group">
                            <input type="text" name="physician_name" class="name-only floating-input w-full p-2.5 border rounded-lg text-sm bg-white" placeholder=" ">
                            <label class="floating-label">Physician’s Name</label>
                        </div>
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
                        <!-- QUESTION 8: ALLERGIES (DYNAMIC) -->
                        <div class="space-y-2 pt-2">
                            <label class="font-bold text-slate-800">8. Are you allergic to any of the following?</label>

                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 pl-4">
                                <?php foreach ($allergies as $c): ?>
                                    <label class="flex items-center text-xs cursor-pointer">
                                        <input type="checkbox"
                                            name="medical_conditions[]"
                                            value="<?= esc($c['condition_key'] ?? $c['id']) ?>"
                                            class="mr-2">
                                        <?= esc($c['condition_label']) ?>
                                    </label>
                                <?php endforeach; ?>

                                <!-- OTHER ALLERGY -->
                                <div class="flex items-center gap-2 md:col-span-2">
                                    <span class="text-xs">Others:</span>
                                    <input type="text"
                                        name="other_allergy"
                                        placeholder="Specify other allergies..."
                                        class="flex-1 border-b text-xs outline-none bg-transparent focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                        <!-- QUESTION 9: BLEEDING TIME (Mins + Secs) -->
                        <!-- BLEEDING TIME - Fix field names to match controller -->
                        <div class="pt-2">
                            <label class="font-bold text-slate-800 mb-2 block">9. Bleeding Time:</label>
                            <div class="flex items-center gap-3">
                                <div class="flex-1 max-w-[120px]">
                                    <div class="floating-label-group">
                                        <input type="number" name="bleeding_mins" id="bleeding_mins" min="0" max="59" placeholder=" " value="<?= old('bleeding_mins') ?>" class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white">
                                        <label class="floating-label">Minutes</label>
                                    </div>
                                </div>
                                <span class="text-slate-400 font-medium">:</span>
                                <div class="flex-1 max-w-[120px]">
                                    <div class="floating-label-group">
                                        <input type="number" name="bleeding_secs" id="bleeding_secs" min="0" max="59" placeholder=" " value="<?= old('bleeding_secs') ?>" class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white">
                                        <label class="floating-label">Seconds</label>
                                    </div>
                                </div>
                                <input type="hidden" name="bleeding_time_combined" id="bleeding_time_combined">
                            </div>
                        </div>

                        <!-- BLOOD PRESSURE - Combine into single field -->
                        <!-- BLOOD PRESSURE - Split Inputs, Combined Output -->
                        <div class="grid grid-cols-2 gap-4 pt-2">
                            <!-- BLOOD TYPE -->
                            <div class="floating-label-group">
                                <select name="blood_type" id="blood_type" class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white">
                                    <option value="" selected disabled hidden></option>
                                    <?php foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'Unknown'] as $bt): ?>
                                        <option value="<?= $bt ?>" <?= old('blood_type') == $bt ? 'selected' : '' ?>><?= $bt ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label class="floating-label">Blood Type</label>
                            </div>

                            <!-- BLOOD PRESSURE: SYSTOLIC / DIASTOLIC (Split Inputs) -->
                            <div class="floating-label-group">
                                <div class="flex items-center gap-2">
                                    <!-- Systolic Input -->
                                    <input type="number"
                                        id="bp_systolic"
                                        name="blood_pressure_systolic"
                                        min="70"
                                        max="200"
                                        placeholder=" "
                                        value="<?= old('blood_pressure_systolic', explode('/', old('blood_pressure') ?? '')[0] ?? '') ?>"
                                        class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white text-center font-bold"
                                        oninput="combineBloodPressure()"
                                        title="Normal: 90-120 mmHg">
                                    <label class="floating-label text-center w-full">Systolic *</label>

                                    <!-- Divider -->
                                    <span class="text-slate-400 font-bold text-lg self-end mb-3">/</span>

                                    <!-- Diastolic Input -->
                                    <input type="number"
                                        id="bp_diastolic"
                                        name="blood_pressure_diastolic"
                                        min="40"
                                        max="130"
                                        placeholder=" "
                                        value="<?= old('blood_pressure_diastolic', explode('/', old('blood_pressure') ?? '')[1] ?? '') ?>"
                                        class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white text-center font-bold"
                                        oninput="combineBloodPressure()"
                                        title="Normal: 60-80 mmHg">
                                    <label class="floating-label text-center w-full">Diastolic *</label>
                                </div>
                                <p class="text-[9px] text-slate-400 mt-1 text-center">
                                    Format: <code class="bg-slate-100 px-1 rounded">120/80</code> mmHg
                                </p>
                            </div>
                        </div>

                        <!-- ✅ Hidden Field for Database Submission (Combined Format) -->
                        <input type="hidden" name="blood_pressure" id="blood_pressure_combined">
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
                    </div>

                    <!-- DYNAMIC MEDICAL CONDITIONS CHECKLIST -->
                    <div class="border-t pt-4">
                        <label class="fixed-label text-blue-600 mb-3 font-black">
                            Medical History Checklist (Please check all that apply):
                        </label>

                        <div class="medical-grid bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <!-- Sa loob ng medical-grid loop -->
                            <?php foreach ($grouped as $cat => $conditions): ?>
                                <div class="col-span-full text-[10px] font-black text-slate-400 uppercase mt-4 mb-1 border-b border-slate-200">
                                    <?= esc($cat) ?>
                                </div>
                                <?php foreach ($conditions as $c): ?>
                                    <label class="flex items-center text-[11px] text-slate-600 cursor-pointer">
                                        <input type="checkbox" name="medical_conditions[]" value="<?= esc($c['condition_key']) ?>" class="mr-2">
                                        <?= esc($c['condition_label']) ?>
                                    </label>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="pt-6 border-t flex justify-between gap-4">

                    <input type="hidden" name="medical_history_submitted" value="1">
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

<!-- CANCEL APPOINTMENT MODAL - Minimalist Professional -->
<div id="cancelModal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] flex items-center justify-center p-4 z-[80]">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">

        <!-- Header: Clean & Functional -->
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-500 text-sm"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-slate-800 text-sm">Cancel Appointment</h4>
                    <p class="text-[10px] text-slate-400">This action cannot be undone</p>
                </div>
            </div>
            <button type="button" onclick="closeCancelModal()"
                class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="cancelForm" action="<?= base_url('receptionist/appointments/cancel') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="appointment_id" id="cancel_appt_id">

            <div class="px-6 py-5 space-y-4">

                <!-- Patient Name: Subtle display -->
                <div>
                    <p class="text-[10px] font-medium text-slate-400 uppercase tracking-wide mb-1.5">Patient</p>
                    <p id="cancel_patient_name" class="font-medium text-slate-700"></p>
                </div>

                <!-- Reason Input: Clean & Focused -->
                <div>
                    <label for="cancel_reason" class="block text-[10px] font-medium text-slate-500 uppercase tracking-wide mb-2">
                        Reason for Cancellation <span class="text-red-500">*</span>
                    </label>
                    <textarea name="cancel_reason" id="cancel_reason" rows="3"
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all resize-none placeholder:text-slate-300"
                        placeholder="Briefly describe why..." required></textarea>
                    <p class="text-[9px] text-slate-400 mt-1.5">
                        This will be logged and included in the patient's notification.
                    </p>
                </div>

                <!-- Error Message: Subtle but Clear -->
                <p id="cancel_error" class="hidden text-sm text-red-600 bg-red-50/50 border border-red-100 px-3 py-2 rounded-lg flex items-start gap-2">
                    <i class="fas fa-circle-exclamation text-xs mt-0.5 flex-shrink-0"></i>
                    <span class="font-medium"></span>
                </p>
            </div>

            <!-- Actions: Clear Hierarchy -->
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex gap-3 justify-end">
                <button type="button" onclick="closeCancelModal()"
                    class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all">
                    Back
                </button>
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 focus:ring-2 focus:ring-red-500/30 transition-all shadow-sm">
                    Confirm Cancellation
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ✅ UNIVERSAL CONFIRMATION MODAL (DaisyUI Style) -->
<div id="confirmModal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] flex items-center justify-center p-4 z-[90]">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-sm overflow-hidden animate-slide-up">

        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-circle-question text-amber-500 text-sm"></i>
            </div>
            <div>
                <h4 id="confirmTitle" class="font-semibold text-slate-800 text-sm">Confirm Action</h4>
                <p id="confirmDesc" class="text-[10px] text-slate-400">Please review before proceeding</p>
            </div>
        </div>

        <!-- Content -->
        <div class="px-6 py-4">
            <p id="confirmMessage" class="text-sm text-slate-600 leading-relaxed"></p>
        </div>

        <!-- Actions -->
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex gap-3 justify-end">
            <button type="button" id="confirmCancel"
                class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-all">
                Cancel
            </button>
            <button type="button" id="confirmProceed"
                class="px-4 py-2 text-sm font-medium text-white bg-amber-500 rounded-lg hover:bg-amber-600 transition-all shadow-sm">
                Confirm
            </button>
        </div>
    </div>
</div>

<!-- ✅ INFO MODAL (for alerts) -->
<div id="infoModal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] flex items-center justify-center p-4 z-[90]">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-sm overflow-hidden animate-slide-up">

        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-circle-info text-blue-500 text-sm"></i>
            </div>
            <h4 id="infoTitle" class="font-semibold text-slate-800 text-sm">Notice</h4>
        </div>

        <!-- Content -->
        <div class="px-6 py-4">
            <p id="infoMessage" class="text-sm text-slate-600 leading-relaxed"></p>
        </div>

        <!-- Actions -->
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex justify-end">
            <button type="button" id="infoClose"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-all shadow-sm">
                Got it
            </button>
        </div>
    </div>
</div>

<!-- REJECT APPOINTMENT MODAL (For Patient-Booked Pending Requests) -->
<div id="rejectModal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] flex items-center justify-center p-4 z-[80]">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <i class="fas fa-ban text-red-500 text-sm"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-slate-800 text-sm">Reject Appointment Request</h4>
                    <p class="text-[10px] text-slate-400">Patient will be notified of the rejection</p>
                </div>
            </div>
            <button type="button" onclick="closeRejectModal()"
                class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="rejectForm" action="<?= base_url('receptionist/appointments/reject') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="appointment_id" id="reject_appt_id">

            <div class="px-6 py-5 space-y-4">
                <div>
                    <p class="text-[10px] font-medium text-slate-400 uppercase tracking-wide mb-1.5">Patient</p>
                    <p id="reject_patient_name" class="font-medium text-slate-700"></p>
                </div>

                <div>
                    <label for="reject_reason" class="block text-[10px] font-medium text-slate-500 uppercase tracking-wide mb-2">
                        Reason for Rejection <span class="text-red-500">*</span>
                    </label>
                    <textarea name="reject_reason" id="reject_reason" rows="3"
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all resize-none placeholder:text-slate-300"
                        placeholder="e.g., Schedule conflict, Service not available..." required></textarea>
                    <p class="text-[9px] text-slate-400 mt-1.5">
                        This reason will be included in the rejection email to the patient.
                    </p>
                </div>

                <p id="reject_error" class="hidden text-sm text-red-600 bg-red-50/50 border border-red-100 px-3 py-2 rounded-lg flex items-start gap-2">
                    <i class="fas fa-circle-exclamation text-xs mt-0.5 flex-shrink-0"></i>
                    <span class="font-medium"></span>
                </p>
            </div>

            <!-- Actions -->
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex gap-3 justify-end">
                <button type="button" onclick="closeRejectModal()"
                    class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all">
                    Back
                </button>
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 focus:ring-2 focus:ring-red-500/30 transition-all shadow-sm">
                    Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ✅ DENY CANCELLATION REQUEST MODAL -->
<div id="denyCancelModal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] flex items-center justify-center p-4 z-[80]">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                    <i class="fas fa-hand-paper text-amber-500 text-sm"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-slate-800 text-sm">Deny Cancellation Request</h4>
                    <p class="text-[10px] text-slate-400">Patient will be notified that their request was denied</p>
                </div>
            </div>
            <button type="button" onclick="closeDenyCancelModal()"
                class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="denyCancelForm" action="<?= base_url('receptionist/appointments/deny-cancellation') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="appointment_id" id="deny_cancel_appt_id">

            <div class="px-6 py-5 space-y-4">
                <div>
                    <p class="text-[10px] font-medium text-slate-400 uppercase tracking-wide mb-1.5">Patient</p>
                    <p id="deny_cancel_patient_name" class="font-medium text-slate-700"></p>
                </div>

                <div>
                    <label for="deny_cancel_reason" class="block text-[10px] font-medium text-slate-500 uppercase tracking-wide mb-2">
                        Reason for Denial <span class="text-amber-500">*</span>
                    </label>
                    <textarea name="deny_cancel_reason" id="deny_cancel_reason" rows="3"
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all resize-none placeholder:text-slate-300"
                        placeholder="e.g., Clinic policy, Schedule already finalized..." required></textarea>
                    <p class="text-[9px] text-slate-400 mt-1.5">
                        This reason will be included in the email to the patient.
                    </p>
                </div>

                <p id="deny_cancel_error" class="hidden text-sm text-amber-600 bg-amber-50/50 border border-amber-100 px-3 py-2 rounded-lg flex items-start gap-2">
                    <i class="fas fa-circle-exclamation text-xs mt-0.5 flex-shrink-0"></i>
                    <span class="font-medium"></span>
                </p>
            </div>

            <!-- Actions -->
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex gap-3 justify-end">
                <button type="button" onclick="closeDenyCancelModal()"
                    class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all">
                    Back
                </button>
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-amber-500 rounded-lg hover:bg-amber-600 focus:ring-2 focus:ring-amber-500/30 transition-all shadow-sm">
                    Confirm Denial
                </button>
            </div>
        </form>
    </div>
</div>

<!-- CANCELLATION REQUEST DETAILS MODAL -->
<div id="cancellationDetailModal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] flex items-center justify-center p-4 z-[85]">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden animate-slide-up">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-calendar-xmark text-amber-500 text-sm"></i>
            </div>
            <div>
                <h4 class="font-semibold text-slate-800 text-sm">Cancellation Request</h4>
                <p class="text-[10px] text-slate-400">Patient requested to cancel this appointment</p>
            </div>
            <button onclick="document.getElementById('cancellationDetailModal').classList.add('hidden')"
                class="ml-auto w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <div class="px-6 py-5 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-[10px] font-medium text-slate-400 uppercase tracking-wide mb-1">Patient</p>
                    <p id="cd_patient_name" class="font-semibold text-slate-700 text-sm"></p>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-slate-400 uppercase tracking-wide mb-1">Schedule</p>
                    <p id="cd_schedule" class="font-semibold text-slate-700 text-sm"></p>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-slate-400 uppercase tracking-wide mb-1">Dentist</p>
                    <p id="cd_dentist" class="font-semibold text-blue-600 text-sm"></p>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-slate-400 uppercase tracking-wide mb-1">Service</p>
                    <p id="cd_service" class="font-semibold text-slate-700 text-sm"></p>
                </div>
            </div>

            <div>
                <p class="text-[10px] font-medium text-slate-400 uppercase tracking-wide mb-1.5">Reason for Cancellation</p>
                <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3">
                    <p id="cd_reason" class="text-sm text-amber-800 font-medium italic"></p>
                </div>
            </div>

            <p class="text-[10px] text-slate-400">
                <i class="fas fa-info-circle mr-1"></i>
                Approving will cancel the appointment and notify the patient. Denying will keep the appointment active.
            </p>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex gap-3 justify-end">
            <button type="button"
                onclick="document.getElementById('cancellationDetailModal').classList.add('hidden')"
                class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-all">
                Close
            </button>
            <button type="button" id="cd_deny_btn"
                class="px-4 py-2 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-600 hover:text-white transition-all">
                <i class="fas fa-times mr-1"></i> Deny
            </button>
            <button type="button" id="cd_approve_btn"
                class="px-4 py-2 text-sm font-medium text-white bg-green-500 rounded-lg hover:bg-green-600 transition-all shadow-sm">
                <i class="fas fa-check mr-1"></i> Approve
            </button>
        </div>
    </div>
</div>

<!-- ✅ Load external JS first -->
<script src="<?= base_url('js/appointments.js') ?>"></script>

<!-- ✅ Inline script for prompt functions -->
<script>
    const BASE_URL = "<?= base_url('data/ph-addresses/') ?>";
    const API_BASE = "<?= base_url('receptionist/appointments') ?>";

    // ===== NOTIFICATIONS =====


    // ===== CONFIRM / INFO MODALS =====
    function showConfirmModal(message, onConfirm, title = 'Confirm Action', confirmText = 'Confirm') {
        const modal = document.getElementById('confirmModal');
        // Allow HTML in title for Font Awesome icons
        document.getElementById('confirmTitle').innerHTML = title;
        document.getElementById('confirmMessage').innerHTML = message;
        const proceed = document.getElementById('confirmProceed');
        const cancel = document.getElementById('confirmCancel');
        proceed.innerHTML = confirmText; // Allow HTML in button text too
        proceed.replaceWith(proceed.cloneNode(true));
        cancel.replaceWith(cancel.cloneNode(true));
        document.getElementById('confirmProceed').addEventListener('click', () => {
            modal.classList.add('hidden');
            onConfirm?.();
        });
        document.getElementById('confirmCancel').addEventListener('click', () => modal.classList.add('hidden'));
        modal.addEventListener('click', e => {
            if (e.target === modal) modal.classList.add('hidden');
        });
        modal.classList.remove('hidden');
    }

    function showInfoModal(message, title = 'Notice') {
        const modal = document.getElementById('infoModal');
        document.getElementById('infoTitle').textContent = title;
        document.getElementById('infoMessage').textContent = message;
        const btn = document.getElementById('infoClose');
        btn.replaceWith(btn.cloneNode(true));
        document.getElementById('infoClose').addEventListener('click', () => modal.classList.add('hidden'));
        modal.addEventListener('click', e => {
            if (e.target === modal) modal.classList.add('hidden');
        });
        modal.classList.remove('hidden');
    }

    // ===== REJECT MODAL =====
    function openRejectModal(appointmentId, patientName) {
        document.getElementById('reject_appt_id').value = appointmentId;
        document.getElementById('reject_patient_name').textContent = patientName;
        document.getElementById('reject_reason').value = '';
        document.getElementById('reject_error').classList.add('hidden');
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }



    // ===== DENY CANCELLATION MODAL =====
    function openDenyCancelModal(appointmentId, patientName) {
        document.getElementById('deny_cancel_appt_id').value = appointmentId;
        document.getElementById('deny_cancel_patient_name').textContent = patientName;
        document.getElementById('deny_cancel_reason').value = '';
        document.getElementById('deny_cancel_error').classList.add('hidden');
        document.getElementById('denyCancelModal').classList.remove('hidden');
    }

    function closeDenyCancelModal() {
        document.getElementById('denyCancelModal').classList.add('hidden');
    }



    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeDenyCancelModal();
    });
    document.getElementById('denyCancelModal')?.addEventListener('click', e => {
        if (e.target === document.getElementById('denyCancelModal')) closeDenyCancelModal();
    });

    // ===== CANCELLATION REQUEST HANDLING =====
    async function handleCancellationRequest(appointmentId, action) {
        const csrfField = document.querySelector('input[name="<?= csrf_token() ?>"]');
        try {
            const res = await fetch(`<?= base_url('receptionist/appointments/handle-cancellation-request') ?>`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    appointment_id: appointmentId,
                    action,
                    [csrfField?.name || 'csrf_test_name']: csrfField?.value || '<?= csrf_hash() ?>'
                })
            });
            const result = await res.json();
            if (result.success) {
                showNotification(result.message, action === 'approve' ? 'success' : 'warning');
                setTimeout(() => location.reload(), 1000);
            } else showNotification(result.message || 'Failed to process request.', 'error');
        } catch {
            showNotification('Network error. Please try again.', 'error');
        }
    }

    // ===== CANCELLATION DETAILS MODAL =====
    function showCancellationDetails(btn) {
        const id = btn.dataset.id;
        const name = btn.dataset.name;
        const reason = btn.dataset.reason;
        const date = btn.dataset.date;
        const time = btn.dataset.time;
        const dentist = btn.dataset.dentist;
        const service = btn.dataset.service;

        document.getElementById('cd_patient_name').textContent = name;
        document.getElementById('cd_schedule').textContent = `${date} @ ${time}`;
        document.getElementById('cd_reason').textContent = reason || 'No reason provided';
        document.getElementById('cd_dentist').textContent = dentist || 'N/A';
        document.getElementById('cd_service').textContent = service || 'N/A';

        const approveBtn = document.getElementById('cd_approve_btn');
        const denyBtn = document.getElementById('cd_deny_btn');
        approveBtn.replaceWith(approveBtn.cloneNode(true));
        denyBtn.replaceWith(denyBtn.cloneNode(true));

        document.getElementById('cd_approve_btn').addEventListener('click', () => {
            document.getElementById('cancellationDetailModal').classList.add('hidden');
            showConfirmModal(`Approve cancellation for ${name}? This will cancel the appointment.`,
                () => handleCancellationRequest(id, 'approve'), 'Approve Cancellation Request', '✅ Yes, Approve');
        });
        document.getElementById('cd_deny_btn').addEventListener('click', () => {
            document.getElementById('cancellationDetailModal').classList.add('hidden');
            openDenyCancelModal(id, name);
        });

        document.getElementById('cancellationDetailModal').classList.remove('hidden');
    }

    // ===== COMPLETION PROMPT =====
    document.addEventListener('DOMContentLoaded', function() {
        <?php foreach ($appointments as $a): ?>
            <?php if (!empty($a['should_prompt_completion']) && strtolower(trim($a['status'] ?? '')) === 'confirmed'): ?>
                setTimeout(() => showCompletionPrompt(<?= (int)$a['id'] ?>, '<?= esc($a['patient_name'], 'js') ?>', <?= (int)($a['minutes_past_due'] ?? 0) ?>), 1500);
            <?php endif; ?>
        <?php endforeach; ?>
    });

    function showCompletionPrompt(appointmentId, patientName, minutesPast) {
        if (document.getElementById(`prompt-${appointmentId}`)) return;
        const el = document.createElement('div');
        el.id = `prompt-${appointmentId}`;
        el.className = 'fixed bottom-4 right-4 z-[9999] max-w-sm w-full';
        el.innerHTML = `
            <div class="bg-white rounded-2xl shadow-2xl border-l-4 border-rose-500 overflow-hidden animate-slide-up">
                <div class="p-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-bell text-rose-600 text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-slate-800 text-sm uppercase tracking-wide">Completion Check</h4>
                            <p class="text-[11px] text-slate-600 mt-1"><strong>${patientName}</strong>'s time was <span class="text-rose-600 font-bold">${minutesPast} mins ago</span>.</p>
                            <div class="flex gap-2 mt-4">
                                <button onclick="markAsCompleted(${appointmentId})" class="flex-1 px-3 py-2 bg-rose-600 hover:bg-rose-700 text-white text-[10px] font-bold rounded-xl transition-colors flex items-center justify-center gap-1.5"><i class="fas fa-check"></i> Yes, Completed</button>
                                <button onclick="dismissPrompt(${appointmentId})" class="flex-1 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold rounded-xl transition-colors flex items-center justify-center gap-1.5"><i class="fas fa-clock"></i> Remind Later</button>
                            </div>
                        </div>
                        <button onclick="dismissPrompt(${appointmentId})" class="text-slate-400 hover:text-slate-600 p-1"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            </div>`;
        document.body.appendChild(el);
        setTimeout(() => dismissPrompt(appointmentId), 30000);
    }

    function dismissPrompt(appointmentId) {
        const el = document.getElementById(`prompt-${appointmentId}`);
        if (!el) return;
        el.style.transition = 'opacity 0.2s';
        el.style.opacity = '0';
        setTimeout(() => el?.remove(), 200);
    }

    async function markAsCompleted(appointmentId) {
        const csrfField = document.querySelector('input[name="<?= csrf_token() ?>"]');
        try {
            const res = await fetch(`<?= base_url('receptionist/appointments/update-status') ?>/${appointmentId}/Completed`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    [csrfField?.name || 'csrf_test_name']: csrfField?.value || '<?= csrf_hash() ?>'
                })
            });
            const result = await res.json();
            if (result.success || res.ok) {
                showNotification('Appointment marked as completed.', 'success');
                dismissPrompt(appointmentId);
                setTimeout(() => location.reload(), 800);
            } else showNotification(result.message || 'Failed to update status.', 'error');
        } catch (err) {
            showNotification(err.message, 'error', 'Action Failed');
        }
    }
    // ===== DIRECT APPROVE CONFIRMATION (No details modal) =====
    function confirmDirectApprove(appointmentId, patientName) {
        showConfirmModal(
            `Are you sure you want to approve the cancellation for <strong>${patientName}</strong>?<br><br><i class="fas fa-info-circle text-slate-400"></i> This will cancel the appointment and notify the patient.`,
            () => handleCancellationRequest(appointmentId, 'approve'),
            '<i class="fas fa-calendar-check text-amber-500 mr-2"></i> Confirm Cancellation Approval',
            '<i class="fas fa-check"></i> Yes, Approve'
        );
    }

    // Function para sa Confirmation ng No-Show
    function confirmNoShow(appointmentId, patientName) {
        showConfirmModal(
            `Are you sure you want to mark <strong>${patientName}</strong> as a <strong>No-Show</strong>?<br><br><i class="fas fa-info-circle text-slate-400"></i> This means the patient did not arrive for their scheduled appointment.`,
            () => markAsNoShow(appointmentId),
            '<i class="fas fa-user-slash text-red-500 mr-2"></i> Confirm No-Show',
            '<i class="fas fa-check"></i> Yes, Mark as No-Show'
        );
    }

    // AJAX Call para i-update ang status
    async function markAsNoShow(appointmentId) {
        const csrfField = document.querySelector('input[name="<?= csrf_token() ?>"]');
        try {
            // Gagamitin natin ang existing update-status route mo pero 'no-show' ang ipapasa
            const res = await fetch(`<?= base_url('receptionist/appointments/update-status') ?>/${appointmentId}/no-show`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    [csrfField?.name || 'csrf_test_name']: csrfField?.value || '<?= csrf_hash() ?>'
                })
            });
            const result = await res.json();
            if (result.success) {
                showNotification('Patient marked as No-Show.', 'warning');
                setTimeout(() => location.reload(), 800);
            } else {
                showNotification(result.message || 'Failed to update status.', 'error');
            }
        } catch (err) {
            showNotification('Network error.', 'error');
        }
    }
</script>

<style>
    @keyframes slide-up {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slide-in {
        from {
            opacity: 0;
            transform: translateX(100px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .animate-slide-up {
        animation: slide-up 0.3s ease-out;
    }

    .animate-slide-in {
        animation: slide-in 0.3s ease-out;
    }

    /* Optional: Subtle pulse for important prompts */
    @keyframes gentle-pulse {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(225, 29, 72, 0.4);
        }

        50% {
            box-shadow: 0 0 0 8px rgba(225, 29, 72, 0);
        }
    }

    .border-rose-500 {
        animation: gentle-pulse 2s infinite;
    }
</style>

<?= $this->endSection() ?>