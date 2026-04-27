<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<!-- Custom Minimalist Styles -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    .minimalist-container {
        font-family: 'Inter', sans-serif;
        color: #1e293b;
    }

    .status-dot {
        height: 8px;
        width: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }

    /* Table-like row behavior but cleaner */
    .appt-row {
        transition: background-color 0.2s;
        border-bottom: 1px solid #f1f5f9;
    }

    .appt-row:hover {
        background-color: #f8fafc;
    }

    /* Toast Slide-In Animation */
    @keyframes slideInRight {
        from {
            transform: translateX(120%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }

        to {
            transform: translateX(120%);
            opacity: 0;
        }
    }

    .animate-slide-in-right {
        animation: slideInRight 0.3s ease-out forwards;
    }

    .animate-slide-out-right {
        animation: slideOutRight 0.2s ease-in forwards;
    }

    /* Toast Container Scroll Fix */
    #toastContainer {
        max-height: calc(100vh - 2rem);
        overflow-y: auto;
    }

    /* Hide scrollbar but keep functionality */
    #toastContainer::-webkit-scrollbar {
        width: 4px;
    }

    #toastContainer::-webkit-scrollbar-track {
        background: transparent;
    }

    #toastContainer::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
</style>

<div class="w-full minimalist-container">

    <!-- TOP HEADER BAR - Single Always-Visible Button -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10 pb-6 border-b border-slate-100">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">My Appointments</h2>
            <p class="text-sm text-slate-500 mt-1">View and manage your scheduled dental procedures.</p>
        </div>

        <!-- ✅ SINGLE BUTTON: Always visible, same label, just disabled when locked -->
        <button type="button"
            id="requestApptBtn"
            data-active-appt-id="<?= $active_appt['id'] ?? '' ?>"
            <?= ($has_active_appt ?? false) ? 'disabled' : '' ?>
            onclick="<?= ($has_active_appt ?? false) ? 'showActiveApptToast(this.dataset.activeApptId)' : 'document.getElementById(\'bookModal\').classList.remove(\'hidden\')' ?>"
            class="px-6 py-3 rounded-xl text-sm font-semibold flex items-center gap-2 transition-all shadow-sm
            <?= ($has_active_appt ?? false)
                ? 'bg-slate-200 text-slate-400 cursor-not-allowed'
                : 'bg-blue-600 text-white hover:bg-blue-700 hover:shadow-md' ?>
            relative group">

            <!-- Icon: Always fa-plus, just styled differently when disabled -->
            <i class="fas fa-plus text-xs"></i>

            <!-- Text: ALWAYS "Request Appointment" -->
            <span>Request Appointment</span>

            <!-- Tooltip: Only shows when disabled -->
            <?php if ($has_active_appt ?? false): ?>
                <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-2 bg-slate-800 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none w-max z-10">
                    You have an active appointment
                    <i class="fas fa-caret-down absolute bottom-0 left-1/2 -translate-x-1/2 text-slate-800"></i>
                </span>
            <?php endif; ?>
        </button>
    </div>

    <!-- MAIN CONTENT GRID -->
    <div class="space-y-12">

        <!-- SECTION 1: ACTIVE / UPCOMING -->
        <section>
            <div class="flex items-center justify-between mb-6">
                <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    Active Appointments
                </h4>
            </div>

            <?php if (empty($upcoming)): ?>
                <div class="py-12 text-center border border-dashed border-slate-200 rounded-2xl bg-slate-50/50">
                    <p class="text-sm text-slate-400 font-medium">You have no active appointments at the moment.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($upcoming as $u): ?>
                        <div class="bg-white border border-slate-200 rounded-2xl p-6 relative overflow-hidden group">

                            <!-- 🔴 DENIAL NOTIFICATION BOX -->
                            <?php if ($u['last_request_status'] === 'denied'): ?>
                                <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-sm">
                                    <div class="flex items-center gap-2 mb-1">
                                        <i class="fas fa-exclamation-circle text-red-600 text-xs"></i>
                                        <span class="text-[10px] font-black text-red-600 uppercase">Cancellation Denied</span>
                                    </div>
                                    <p class="text-[11px] text-red-700 italic">
                                        "<?= esc($u['last_denial_reason']) ?>"
                                    </p>
                                    <p class="text-[9px] text-red-400 mt-1 uppercase font-bold">
                                        Attempt <?= $u['cancel_attempts'] ?> of 2
                                    </p>
                                </div>
                            <?php endif; ?>

                            <div class="flex justify-between items-start mb-4">
                                <span class="text-[10px] font-bold uppercase px-2 py-1 rounded-md <?= ($u['status'] === 'confirmed') ? 'bg-green-50 text-green-600' : 'bg-amber-50 text-amber-600' ?>">
                                    <?= str_replace('_', ' ', $u['status']) ?>
                                </span>
                                <div class="text-right">
                                    <p class="text-xs font-bold text-slate-800"><?= $u['fmt_date'] ?></p>
                                    <p class="text-[10px] text-slate-500"><?= $u['fmt_time'] ?></p>
                                </div>
                            </div>

                            <h5 class="text-base font-bold text-slate-800 mb-1"><?= esc($u['service_name']) ?></h5>
                            <p class="text-xs text-slate-500 mb-6 flex items-center gap-1">
                                <i class="fas fa-user-md text-blue-400"></i> <?= $u['dentist_name'] ?>
                            </p>

                            <div class="flex gap-2">
                                <a href="<?= base_url('patient/appointments/view/' . $u['id']) ?>" class="flex-1 py-2 bg-slate-50 text-slate-600 rounded-lg text-[11px] font-bold text-center">Details</a>

                                <?php
                                $status = strtolower($u['status']);
                                $maxAttempts = 2;
                                $attempts = (int)$u['cancel_attempts'];
                                ?>

                                <?php if ($status === 'cancellation_requested'): ?>
                                    <span class="px-3 py-2 bg-amber-50 text-amber-600 rounded-lg text-[11px] font-bold flex items-center gap-1">
                                        <i class="fas fa-spinner fa-spin"></i> Pending Review
                                    </span>
                                <?php elseif ($attempts < $maxAttempts): ?>
                                    <button type="button"
                                        data-action="cancel-request"
                                        data-appt-id="<?= $u['id'] ?>"
                                        data-service="<?= esc($u['service_name']) ?>"
                                        data-datetime="<?= $u['fmt_date'] . ' ' . $u['fmt_time'] ?>"
                                        data-remaining="<?= $maxAttempts - $attempts ?>"
                                        class="px-3 py-2 bg-red-50 text-red-600 rounded-lg text-[11px] font-bold hover:bg-red-100">
                                        Request Cancel
                                    </button>
                                <?php else: ?>
                                    <span class="px-3 py-2 bg-slate-100 text-slate-400 rounded-lg text-[11px] font-bold italic" title="Please call clinic">
                                        Limit Reached
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- SECTION 2: PAST APPOINTMENTS (HISTORY) -->
        <section>
            <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-6 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                Appointment History
            </h4>

            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 text-slate-500 text-[10px] uppercase font-bold">
                        <tr>
                            <th class="p-4 border-b">Service</th>
                            <th class="p-4 border-b">Dentist</th>
                            <th class="p-4 border-b">Date</th>
                            <th class="p-4 border-b text-center">Status</th>
                            <th class="p-4 border-b text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($past)): ?>
                            <tr>
                                <td colspan="5" class="p-10 text-center text-slate-400 text-xs italic">No past records found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($past as $p): ?>
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="p-4 text-xs font-bold text-slate-700"><?= esc($p['service_name']) ?></td>
                                    <td class="p-4 text-xs text-slate-600"><?= $p['dentist_name'] ?></td>
                                    <td class="p-4 text-xs text-slate-600"><?= $p['fmt_date'] ?></td>
                                    <td class="p-4 text-center">
                                        <?php
                                        $s = strtolower($p['status']);
                                        $color = ($s === 'completed') ? 'green' : (($s === 'no-show' || $s === 'rejected') ? 'red' : 'slate');
                                        ?>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-<?= $color ?>-50 text-<?= $color ?>-600 border border-<?= $color ?>-100">
                                            <?= $p['status'] ?>
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <a href="<?= base_url('patient/appointments/view/' . $p['id']) ?>" class="text-blue-500 hover:text-blue-700 text-xs">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</div>

<!-- MODAL: BOOKING (Smart Slot Picker Version) -->
<div id="bookModal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-start justify-center p-4 z-50 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl my-4 overflow-hidden">
        <!-- Header -->
        <div class="p-5 border-b flex justify-between items-center bg-slate-50">
            <h4 class="font-bold text-slate-800 uppercase text-sm">Request New Appointment</h4>
            <button onclick="closeBookModal()" class="text-slate-400 text-2xl hover:text-slate-600">&times;</button>
        </div>

        <form action="<?= base_url('patient/appointments/store') ?>" method="POST" id="patientApptForm" class="p-6 space-y-6">
            <?= csrf_field() ?>

            <!-- 1. SERVICE SELECTION -->
            <!-- 1. MULTI-SERVICE SELECTION -->
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <label class="text-[11px] font-bold text-slate-400 uppercase block">Select Services *</label>
                    <button type="button" onclick="addServiceRow()" class="text-[10px] bg-blue-600 text-white px-3 py-1 rounded-full hover:bg-blue-700 transition-all flex items-center gap-1">
                        <i class="fas fa-plus text-[8px]"></i> Add Service
                    </button>
                </div>

                <div id="services_container" class="space-y-3">
                    <!-- First service row (required) -->
                    <div class="service-row flex gap-3 items-start bg-slate-50 p-3 rounded-xl border border-slate-200">
                        <select name="services[]" class="service-select flex-1 p-2.5 border border-slate-200 rounded-lg text-sm bg-white" required onchange="updateServiceLevel(this)">
                            <option value="">-- Choose Procedure --</option>
                            <?php foreach ($services as $s): ?>
                                <option value="<?= $s['id'] ?>"
                                    data-duration="<?= $s['estimated_duration_minutes'] ?? 30 ?>"
                                    data-haslevels="<?= $s['has_levels'] ?? 0 ?>"
                                    data-adjustments='<?= $s['duration_adjustments'] ?? "{}" ?>'>
                                    <?= esc($s['service_name']) ?>
                                    <?= $s['has_levels'] ? '(Complexity: Simple/Moderate/Severe)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <!-- Level dropdown (hidden by default) -->
                        <div class="level-container hidden w-32">
                            <select name="levels[]" class="level-select w-full p-2.5 border border-slate-200 rounded-lg text-sm bg-white" onchange="calculateTotalDuration()">
                                <option value="Standard">Standard</option>
                                <option value="Simple">Simple</option>
                                <option value="Moderate">Moderate</option>
                                <option value="Severe">Severe</option>
                            </select>
                        </div>

                        <!-- Remove button (hidden for first row) -->
                        <button type="button" onclick="removeServiceRow(this)" class="remove-svc-btn hidden text-red-400 p-2 hover:text-red-600 transition-colors" title="Remove service">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Duration Badge -->
                <div id="patient_duration_badge" class="hidden inline-flex items-center gap-1 text-[10px] font-bold bg-blue-100 text-blue-700 px-2 py-1 rounded">
                    <i class="fas fa-hourglass-half"></i>
                    <span id="patient_duration_value">30</span> mins estimated
                </div>
            </div>

            <!-- 2. DENTIST (Optional - Auto-assign if not selected) -->
            <div>
                <label class="text-[11px] font-bold text-slate-400 uppercase block mb-1.5">Preferred Dentist (Optional)</label>
                <select name="dentist_id" id="patient_dentist"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                    <option value="">Any Available Dentist</option>
                    <?php foreach ($dentists as $d): ?>
                        <option value="<?= $d['id'] ?>">Dr. <?= esc($d['first_name'] . ' ' . $d['last_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 3. DATE SELECTION -->
            <div>
                <label class="text-[11px] font-bold text-slate-400 uppercase block mb-1.5">Preferred Date *</label>
                <input type="date" name="appointment_date" id="patient_date"
                    min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d', strtotime('+30 days')) ?>"
                    required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm">
            </div>

            <!-- 4. SMART SLOT PICKER (AJAX Loaded) -->
            <div id="patient_slot_section" class="hidden space-y-4">
                <div class="flex items-center justify-between">
                    <label class="text-[11px] font-bold text-slate-400 uppercase">Available Time Slots *</label>
                    <span id="slot_loading" class="hidden text-[10px] text-slate-400 flex items-center gap-1">
                        <i class="fas fa-spinner fa-spin"></i> Checking...
                    </span>
                </div>

                <!-- Legend -->
                <div class="flex gap-3 text-[9px] text-slate-400">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-green-100 border border-green-300"></span> Available</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-red-100 border border-red-300 line-through"></span> Booked</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-blue-100 border-2 border-blue-500"></span> Selected</span>
                </div>

                <!-- Slots Grid -->
                <div id="patient_slots" class="grid grid-cols-3 sm:grid-cols-4 gap-2 max-h-40 overflow-y-auto p-2 bg-slate-50 rounded-xl border border-slate-200">
                    <!-- Slots injected via JS -->
                </div>

                <!-- Error Message -->
                <p id="slot_error" class="hidden text-[10px] text-red-600 bg-red-50 px-3 py-2 rounded-lg">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    <span id="slot_error_msg">Please select a valid time slot.</span>
                </p>
            </div>

            <!-- 5. HIDDEN FIELDS FOR SUBMISSION -->
            <input type="hidden" name="appointment_time" id="hidden_patient_time" required>
            <input type="hidden" name="end_time" id="hidden_patient_end_time" required>
            <input type="hidden" name="expected_duration_minutes" id="hidden_patient_duration">

            <!-- 6. END TIME DISPLAY (Read-only) -->
            <div id="end_time_display" class="hidden p-3 bg-slate-50 rounded-xl border border-slate-200">
                <p class="text-[10px] font-bold text-slate-400 uppercase">Expected End Time</p>
                <p id="patient_end_time_label" class="text-sm font-bold text-slate-700">--:--</p>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="patient_submit_btn" disabled
                class="w-full bg-slate-300 text-white py-4 rounded-xl font-bold text-sm cursor-not-allowed transition-all">
                Submit Request
            </button>
            <p class="text-[9px] text-slate-400 text-center">
                <i class="fas fa-info-circle mr-1"></i>
                Your request will be reviewed by our staff. You'll receive a confirmation email once approved.
            </p>
        </form>
    </div>
</div>

<!-- ✅ MODAL: Cancellation Request (Red Theme) -->
<div id="cancelRequestModal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 z-[60]">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

        <!-- Header - Red Theme -->
        <div class="p-5 border-b border-slate-100 bg-red-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-hand-paper text-lg"></i>
                </div>
                <h4 class="font-bold text-slate-800">Request Cancellation</h4>
            </div>
            <button onclick="closeCancellationModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <form id="cancelRequestForm" class="p-6">
            <input type="hidden" id="cancel_request_appt_id">

            <p class="text-sm text-slate-600 mb-2">
                You're requesting to cancel:
            </p>

            <p id="cancel_attempt_info" class="text-[11px] text-amber-600 mb-3 hidden">
                You have <span id="remaining_attempts"></span> cancellation attempt(s) left.
            </p>

            <!-- Appointment Summary -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 mb-5">
                <p class="text-sm font-semibold text-slate-800" id="cancel_request_service">Loading...</p>
                <p class="text-xs text-slate-500 mt-1" id="cancel_request_datetime">Loading...</p>
            </div>

            <!-- Reason Field -->
            <div class="mb-5">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">
                    Reason for Cancellation <span class="text-red-500">*</span>
                </label>
                <textarea id="cancel_request_reason" name="reason" rows="3"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-300 transition-all"
                    placeholder="Please explain why you need to cancel (e.g., schedule conflict, illness, etc.)"
                    required></textarea>
                <p class="text-[10px] text-slate-400 mt-1">
                    Your request will be reviewed by our staff. You'll receive an email once approved.
                    <br>
                    <span class="text-red-600 font-medium">Note: All cancellation requests require staff approval.</span>
                </p>
            </div>

            <!-- Actions - Red Primary Button -->
            <div class="flex gap-3">
                <button type="button" onclick="closeCancellationModal()"
                    class="flex-1 py-3 px-4 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 border border-slate-200 hover:bg-slate-200 transition-all">
                    Go Back
                </button>
                <button type="submit" id="cancel_request_submit"
                    class="flex-1 py-3 px-4 rounded-xl text-sm font-semibold text-white bg-red-600 hover:bg-red-700 active:bg-red-800 transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">
                    Submit Request
                </button>
            </div>

            <!-- Error Message -->
            <p id="cancel_request_error" class="hidden mt-3 text-sm text-red-600 bg-red-50 border border-red-200 px-3 py-2 rounded-lg flex items-start gap-2">
                <i class="fas fa-circle-exclamation mt-0.5 flex-shrink-0"></i>
                <span></span>
            </p>
        </form>
    </div>
</div>


<!-- ✅ TOAST NOTIFICATION CONTAINER -->
<div id="toastContainer" class="fixed top-4 right-4 z-[70] space-y-3 pointer-events-none">
    <!-- Toasts will be injected here dynamically -->
</div>

<!-- ✅ TOAST TEMPLATE (Hidden by default) -->
<template id="toastTemplate">
    <div class="toast-item pointer-events-auto flex items-start gap-3 p-4 rounded-xl shadow-lg border bg-white animate-slide-in-right min-w-[320px] max-w-sm">
        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
            <i class="fas fa-exclamation-triangle text-sm"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-slate-800">Cannot Book Appointment</p>
            <p class="text-sm text-slate-600 mt-0.5">You already have an active appointment. Please wait for it to be completed or cancelled before booking a new one.</p>
            <div class="mt-3 flex gap-2">
                <a href="#" class="toast-view-link text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline">View Active Appointment</a>
                <button class="toast-dismiss text-xs text-slate-400 hover:text-slate-600">Got it</button>
            </div>
        </div>
        <button class="toast-close absolute top-2 right-2 text-slate-400 hover:text-slate-600">
            <i class="fas fa-times text-xs"></i>
        </button>
    </div>
</template>

<script>
    // ===============================
    // PATIENT SIDE - TIME SLOT LOGIC
    // ===============================

    const PATIENT_API = "<?= base_url('patient/appointments') ?>";
    let patientSelectedSlot = null;
    let patientTotalDuration = 30;
    const PATIENT_DEBUG = true;

    function pLog(msg, data = null) {
        if (PATIENT_DEBUG) console.log(`[PATIENT] ${msg}`, data || '');
    }

    function getPatientCsrf() {
        const tokenField = document.querySelector('input[name="<?= csrf_token() ?>"]');
        return {
            name: tokenField?.name || 'csrf_test_name',
            token: tokenField?.value || ''
        };
    }

    function timeToMinutes(timeStr) {
        if (!timeStr) return 0;
        const clean = timeStr.replace(/\s*(AM|PM|am|pm)/i, '').trim();
        const [hours, minutes] = clean.split(':').map(Number);
        const isPM = timeStr.toUpperCase().includes('PM');
        const isAM = timeStr.toUpperCase().includes('AM');
        let h = hours;
        if (isPM && h !== 12) h += 12;
        if (isAM && h === 12) h = 0;
        return h * 60 + (minutes || 0);
    }

    function formatTime12h(timeStr) {
        if (!timeStr) return '';
        const clean = timeStr.replace(/\s*(AM|PM|am|pm)/i, '').trim();
        let [time, period] = clean.split(' ');
        if (!period) {
            let [hours, minutes] = clean.split(':').map(Number);
            period = hours >= 12 ? 'PM' : 'AM';
            if (hours > 12) hours -= 12;
            if (hours === 0) hours = 12;
            return `${hours}:${String(minutes).padStart(2, '0')} ${period}`;
        }
        let [hours, minutes] = time.split(':').map(Number);
        if (hours > 12) hours -= 12;
        if (hours === 0) hours = 12;
        return `${hours}:${String(minutes).padStart(2, '0')} ${period.toUpperCase()}`;
    }

    function formatTimeForDB(timeStr) {
        let [time, period] = timeStr.trim().split(' ');
        let parts = time.split(':');
        let hours = parseInt(parts[0]);
        let minutes = parseInt(parts[1]) || 0;
        let seconds = parts[2] ? parseInt(parts[2]) : 0;
        if (period === 'PM' && hours !== 12) hours += 12;
        if (period === 'AM' && hours === 12) hours = 0;
        return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    }

    // ===============================
    // FETCH & RENDER SLOTS
    // ===============================


    function renderPatientSlots(slots) {
        const container = document.getElementById('patient_slots');
        container.innerHTML = '';

        const cutoff = timeToMinutes("16:00");
        const validSlots = slots.filter(slot => timeToMinutes(slot.start) < cutoff);

        if (validSlots.filter(s => s.available).length === 0) {
            container.innerHTML = `<div class="col-span-full text-center py-6"><i class="fas fa-circle-exclamation text-2xl text-red-400 mb-2"></i><p class="text-[10px] text-red-600 font-bold">No available slots</p><p class="text-[9px] text-slate-400 mt-1">Try another date or dentist</p></div>`;
            return;
        }

        validSlots.sort((a, b) => timeToMinutes(a.start) - timeToMinutes(b.start));

        validSlots.forEach(slot => {
            const btn = document.createElement('button');
            btn.type = 'button';
            const isAvailable = slot.available;
            const isSelected = patientSelectedSlot?.start === slot.start;

            let btnClass = "slot-btn px-3 py-2 rounded-lg text-[10px] font-bold transition-all border flex flex-col items-center gap-0.5 min-h-[40px] ";
            if (isSelected) {
                btnClass += "bg-blue-600 border-blue-600 text-white shadow-md";
            } else if (isAvailable) {
                btnClass += "bg-white border-slate-200 text-slate-700 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700";
            } else {
                btnClass += "bg-red-50 border-red-200 text-red-300 cursor-not-allowed line-through opacity-70";
            }

            btn.className = btnClass;
            btn.innerHTML = `
                <span class="font-bold">${formatTime12h(slot.start)}</span>
                <span class="text-[9px] opacity-80">${formatTime12h(slot.end_display || slot.end)}</span>
                ${!isAvailable ? '<i class="fas fa-lock text-[8px]"></i>' : ''}
                ${isSelected ? '<i class="fas fa-check absolute -top-1 -right-1 w-4 h-4 bg-white text-blue-600 rounded-full text-[9px] flex items-center justify-center shadow"></i>' : ''}
            `;
            btn.disabled = !isAvailable;
            btn.title = isAvailable ? "Click to select" : (slot.reason === 'past' ? 'Past time' : 'Already booked');

            if (isAvailable) btn.onclick = () => selectPatientSlot(slot, btn);
            container.appendChild(btn);
        });
    }

    function selectPatientSlot(slot, btnElement) {
        if (patientSelectedSlot?.start === slot.start) {
            patientSelectedSlot = null;
            document.getElementById('hidden_patient_time').value = '';
            document.getElementById('hidden_patient_end_time').value = '';
            document.getElementById('end_time_display')?.classList.add('hidden');
            updatePatientSubmitButton();
            return;
        }

        patientSelectedSlot = slot;
        document.getElementById('hidden_patient_time').value = formatTimeForDB(slot.start);
        document.getElementById('hidden_patient_end_time').value = formatTimeForDB(slot.end_actual || slot.end);

        const endDisplay = document.getElementById('end_time_display');
        if (endDisplay) {
            endDisplay.classList.remove('hidden');
            document.getElementById('patient_end_time_label').textContent = formatTime12h(slot.end_actual || slot.end);
        }

        document.getElementById('patient_slot_error')?.classList.add('hidden');
        fetchPatientSlots();
        updatePatientSubmitButton();
    }

    function updatePatientSubmitButton() {
        const btn = document.getElementById('patient_submit_btn');
        if (!btn) return;

        // ✅ Check: At least one service selected
        const selectedServices = Array.from(document.querySelectorAll('.service-select'))
            .filter(s => s.value !== '');

        // ✅ Check: Date selected
        const dateSelected = document.getElementById('patient_date')?.value;

        // ✅ Check: Time slot selected
        const slotSelected = !!patientSelectedSlot;

        const isValid = selectedServices.length > 0 && dateSelected && slotSelected;

        btn.disabled = !isValid;

        // Toggle styles
        btn.classList.toggle('bg-slate-300', !isValid);
        btn.classList.toggle('cursor-not-allowed', !isValid);
        btn.classList.toggle('bg-slate-900', isValid);
        btn.classList.toggle('hover:bg-black', isValid);

        pLog('🔘 Button state:', {
            isValid,
            selectedServices: selectedServices.length,
            dateSelected,
            slotSelected
        });
    }
    // ===============================
    // EVENT HANDLERS
    // ===============================

    function onServiceChange(select) {
        const option = select.options[select.selectedIndex];
        const hasLevels = option.dataset.haslevels === '1';
        const baseDuration = parseInt(option.dataset.duration) || 30;
        document.getElementById('level_container').classList.toggle('hidden', !hasLevels);
        patientTotalDuration = baseDuration;
        updateDurationBadge();
        fetchPatientSlots();
    }

    function onLevelChange() {
        const serviceSelect = document.getElementById('patient_service');
        const option = serviceSelect.options[serviceSelect.selectedIndex];
        const adjustments = JSON.parse(option.dataset.adjustments || '{}');
        const level = document.getElementById('patient_level').value;
        const baseDuration = parseInt(option.dataset.duration) || 30;
        patientTotalDuration = baseDuration + (adjustments[level] || 0);
        updateDurationBadge();
        fetchPatientSlots();
    }

    function updateDurationBadge() {
        const badge = document.getElementById('patient_duration_badge');
        const value = document.getElementById('patient_duration_value');
        if (value) value.textContent = patientTotalDuration;
        if (badge) badge.classList.remove('hidden');
        document.getElementById('hidden_patient_duration').value = patientTotalDuration;
    }

    // ===============================
    // INIT & UTILS
    // ===============================

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('patient_dentist')?.addEventListener('change', fetchPatientSlots);
        document.getElementById('patient_date')?.addEventListener('change', function() {
            fetchPatientSlots();
            updatePatientSubmitButton(); // ✅ Trigger validation on date change
        });

        // Delegate events for dynamic service/level rows
        document.getElementById('services_container')?.addEventListener('change', function(e) {
            if (e.target.classList.contains('service-select')) {
                updateServiceLevel(e.target);
                updatePatientSubmitButton(); // ✅ Trigger validation
            }
            if (e.target.classList.contains('level-select')) {
                calculateTotalDuration();
                fetchPatientSlots();
                updatePatientSubmitButton(); // ✅ Trigger validation
            }
        });
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-action="cancel-request"]');
            if (!btn) return;

            const apptId = btn.dataset.apptId;
            const serviceName = btn.dataset.service;
            const datetime = btn.dataset.datetime;

            openCancellationModal(apptId, serviceName, datetime);
        });

        document.querySelectorAll('.service-select').forEach(select => {
            select.addEventListener('change', function() {
                updateServiceLevel(this);
            });
        });

        document.querySelectorAll('.level-select').forEach(select => {
            select.addEventListener('change', calculateTotalDuration);
        });

        // Initial duration calculation
        calculateTotalDuration();
        document.getElementById('patientApptForm')?.addEventListener('submit', function(e) {
            // Check for at least one selected service
            const selectedServices = Array.from(document.querySelectorAll('.service-select'))
                .filter(s => s.value !== '')
                .map(s => s.value);

            if (selectedServices.length === 0) {
                e.preventDefault();
                showToast('warning', 'Please select at least one service.');
                document.getElementById('services_container')?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                return false;
            }

            // Check for time slot selection
            if (!patientSelectedSlot) {
                e.preventDefault();
                const errorEl = document.getElementById('slot_error');
                const errorMsg = document.getElementById('slot_error_msg');
                if (errorEl) errorEl.classList.remove('hidden');
                if (errorMsg) errorMsg.textContent = 'Please select a time slot from the available options.';
                document.getElementById('patient_slots')?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                return false;
            }

            return true;
        });
        const initialHasActive = <?= json_encode($has_active_appt ?? false) ?>;
        const initialActiveId = <?= json_encode($active_appt['id'] ?? null) ?>;
        updateRequestButton(initialHasActive, initialActiveId);

        // Periodic AJAX sync (every 30s)
        setInterval(async () => {
            try {
                const response = await fetch("<?= base_url('patient/appointments/check-active') ?>", {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    cache: 'no-store'
                });

                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                const data = await response.json();

                if (typeof data.has_active === 'boolean') {
                    updateRequestButton(data.has_active, data.active_id ?? null);
                }
            } catch (e) {
                console.warn('[Button Sync] Failed:', e);
                // Keep current state on error
            }
        }, 30000);
    });

    // ===============================
    // CANCELLATION REQUEST FUNCTIONS
    // ===============================

    function openCancellationModal(apptId, serviceName, datetime, remaining = null) {
        console.log('[DEBUG] openCancellationModal:', {
            apptId,
            serviceName,
            datetime,
            remaining
        });

        const modal = document.getElementById('cancelRequestModal');
        const serviceEl = document.getElementById('cancel_request_service');
        const datetimeEl = document.getElementById('cancel_request_datetime');

        document.getElementById('cancel_request_appt_id').value = apptId;
        if (serviceEl) serviceEl.textContent = serviceName || 'Unknown Service';
        if (datetimeEl) datetimeEl.textContent = datetime || 'Unknown Date/Time';

        const attemptInfo = document.getElementById('cancel_attempt_info');
        const remainingEl = document.getElementById('remaining_attempts');

        if (remaining !== null) {
            attemptInfo.classList.remove('hidden');
            remainingEl.textContent = remaining;
        } else {
            attemptInfo.classList.add('hidden');
        }

        document.getElementById('cancel_request_reason').value = '';
        document.getElementById('cancel_request_error').classList.add('hidden');
        document.getElementById('cancel_request_submit').disabled = false;

        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeCancellationModal() {
        document.getElementById('cancelRequestModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Handle form submission
    document.getElementById('cancelRequestForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();

        const apptId = document.getElementById('cancel_request_appt_id').value;
        const reason = document.getElementById('cancel_request_reason').value.trim();
        const errorEl = document.getElementById('cancel_request_error');
        const submitBtn = document.getElementById('cancel_request_submit');

        if (!reason) {
            errorEl.textContent = 'Please provide a reason for cancellation.';
            errorEl.classList.remove('hidden');
            return;
        }

        // Loading state
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Submitting...';
        errorEl.classList.add('hidden');

        try {
            const csrf = getPatientCsrf();
            const formData = new FormData();
            formData.append('reason', reason);
            formData.append(csrf.name, csrf.token);

            const response = await fetch(`<?= base_url('patient/appointments/request-cancellation/') ?>${apptId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                closeCancellationModal();
                showToast('info', result.message || 'Cancellation request submitted successfully.');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                throw new Error(result.error || 'Request failed');
            }

        } catch (err) {
            console.error('Cancellation request error:', err);
            errorEl.textContent = '❌ ' + err.message;
            errorEl.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCancellationModal();
        }
    });

    // Close modal when clicking outside
    document.getElementById('cancelRequestModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeCancellationModal();
        }
    });

    function closeBookModal() {
        document.getElementById('bookModal').classList.add('hidden');
        resetPatientForm();
    }

    function resetPatientForm() {
        document.getElementById('patientApptForm')?.reset();
        document.getElementById('patient_slots').innerHTML = '';
        document.getElementById('end_time_display')?.classList.add('hidden');
        document.getElementById('patient_duration_badge')?.classList.add('hidden');
        patientSelectedSlot = null;
        patientTotalDuration = 30;
        updatePatientSubmitButton();
    }

    function openRescheduleModal(id, date, time) {
        document.getElementById('resched_id').value = id;
        document.getElementById('resched_date').value = date;
        document.getElementById('resched_time').value = time;
        document.getElementById('rescheduleModal')?.classList.remove('hidden');
    }

    // ===============================
    // BUTTON STATE SYNC (Simple Enable/Disable)
    // ===============================

    function updateRequestButton(hasActive, activeId = null) {
        const btn = document.getElementById('requestApptBtn');
        if (!btn) return;

        if (hasActive) {
            // 🔒 DISABLED STATE: Gray, not clickable, shows tooltip on hover
            btn.disabled = true;
            btn.dataset.activeApptId = activeId || '';
            btn.onclick = () => showActiveApptToast(activeId);

            // Update styling
            btn.classList.remove('bg-blue-600', 'text-white', 'hover:bg-blue-700', 'hover:shadow-md');
            btn.classList.add('bg-slate-200', 'text-slate-400', 'cursor-not-allowed');
        } else {
            // 🔓 ENABLED STATE: Blue, clickable, opens modal
            btn.disabled = false;
            btn.onclick = () => document.getElementById('bookModal').classList.remove('hidden');

            // Update styling
            btn.classList.remove('bg-slate-200', 'text-slate-400', 'cursor-not-allowed');
            btn.classList.add('bg-blue-600', 'text-white', 'hover:bg-blue-700', 'hover:shadow-md');
        }
        // ✅ Text and icon NEVER change - always "Request Appointment" + fa-plus
    }

    // ===============================
    // TOAST NOTIFICATION FUNCTIONS
    // ===============================

    function showToast(type = 'warning', message = '', options = {}) {
        const container = document.getElementById('toastContainer');
        const template = document.getElementById('toastTemplate');
        if (!container || !template) return;

        const toast = template.content.cloneNode(true).querySelector('.toast-item');
        const configs = {
            warning: {
                icon: 'fa-exclamation-triangle',
                iconBg: 'bg-red-100',
                iconColor: 'text-red-600',
                title: 'Cannot Book Appointment',
                border: 'border-red-200'
            },
            error: {
                icon: 'fa-circle-xmark',
                iconBg: 'bg-red-100',
                iconColor: 'text-red-600',
                title: 'Booking Not Allowed',
                border: 'border-red-200'
            },
            info: {
                icon: 'fa-info-circle',
                iconBg: 'bg-blue-100',
                iconColor: 'text-blue-600',
                title: 'Information',
                border: 'border-blue-200'
            }
        };
        const cfg = configs[type] || configs.warning;

        toast.querySelector('i').className = `fas ${cfg.icon} text-sm`;
        toast.querySelector('.flex-shrink-0').className = `flex-shrink-0 w-8 h-8 rounded-full ${cfg.iconBg} ${cfg.iconColor} flex items-center justify-center`;
        toast.querySelector('.font-semibold').textContent = cfg.title;
        toast.classList.add(cfg.border);
        if (message) toast.querySelector('.text-slate-600').textContent = message;
        if (options.activeApptId) {
            const link = toast.querySelector('.toast-view-link');
            link.href = `<?= base_url('patient/appointments/view/') ?>${options.activeApptId}`;
            link.classList.remove('hidden');
        } else {
            toast.querySelector('.toast-view-link')?.classList.add('hidden');
        }

        container.appendChild(toast);
        setupToastDismiss(toast);
        setTimeout(() => dismissToast(toast), 6000);
        return toast;
    }

    function setupToastDismiss(toast) {
        toast.querySelector('.toast-close')?.addEventListener('click', (e) => {
            e.stopPropagation();
            dismissToast(toast);
        });
        toast.querySelector('.toast-dismiss')?.addEventListener('click', () => dismissToast(toast));
        toast.addEventListener('click', (e) => {
            if (!e.target.closest('a') && !e.target.closest('button')) dismissToast(toast);
        });
    }

    function dismissToast(toast) {
        toast.classList.remove('animate-slide-in-right');
        toast.classList.add('animate-slide-out-right');
        setTimeout(() => toast.remove(), 200);
    }

    function showActiveApptToast(activeApptId = null) {
        showToast('warning', '', {
            activeApptId
        });
    }
    // ===============================
    // MULTI-SERVICE FUNCTIONS
    // ===============================


    // Add new service row
    function addServiceRow() {
        const container = document.getElementById('services_container');
        const firstRow = container.querySelector('.service-row');
        const newRow = firstRow.cloneNode(true);

        // Reset values
        newRow.querySelector('.service-select').value = '';
        newRow.querySelector('.level-container').classList.add('hidden');
        newRow.querySelector('.level-select').value = 'Standard';
        newRow.querySelector('.remove-svc-btn').classList.remove('hidden');

        // Add event listeners
        newRow.querySelector('.service-select').addEventListener('change', function() {
            updateServiceLevel(this);
        });
        newRow.querySelector('.level-select').addEventListener('change', calculateTotalDuration);

        container.appendChild(newRow);
        calculateTotalDuration();
        fetchPatientSlots(); // Re-fetch slots with new duration
    }

    // Remove service row
    function removeServiceRow(btn) {
        const container = document.getElementById('services_container');
        const rows = container.querySelectorAll('.service-row');

        // Don't allow removing the last row
        if (rows.length <= 1) {
            showToast('warning', 'At least one service is required.');
            return;
        }

        btn.closest('.service-row').remove();

        // Hide remove button if only one row left
        if (container.querySelectorAll('.service-row').length === 1) {
            container.querySelector('.remove-svc-btn')?.classList.add('hidden');
        }

        calculateTotalDuration();
        fetchPatientSlots();
    }

    // Show/hide level dropdown based on service selection
    function updateServiceLevel(select) {
        const row = select.closest('.service-row');
        const levelContainer = row.querySelector('.level-container');
        const hasLevels = select.options[select.selectedIndex]?.dataset.haslevels === '1';

        if (hasLevels) {
            levelContainer.classList.remove('hidden');
        } else {
            levelContainer.classList.add('hidden');
            row.querySelector('.level-select').value = 'Standard';
        }

        calculateTotalDuration();
        fetchPatientSlots();
    }

    // Calculate total duration from all selected services
    function calculateTotalDuration() {
        let total = 0;
        const rows = document.querySelectorAll('.service-row');

        rows.forEach(row => {
            const serviceSelect = row.querySelector('.service-select');
            const levelSelect = row.querySelector('.level-select');

            if (!serviceSelect.value) return;

            const option = serviceSelect.options[serviceSelect.selectedIndex];
            const baseDuration = parseInt(option.dataset.duration) || 30;
            const hasLevels = option.dataset.haslevels === '1';
            const adjustments = JSON.parse(option.dataset.adjustments || '{}');

            let duration = baseDuration;

            if (hasLevels && levelSelect?.value) {
                const level = levelSelect.value;
                const trimmed = {};
                for (const [k, v] of Object.entries(adjustments)) {
                    trimmed[k.trim()] = parseInt(v) || 0;
                }
                duration += trimmed[level] || 0;
            }

            total += duration;
        });

        patientTotalDuration = Math.max(total, 15); // Minimum 15 mins

        // Update badge
        const badge = document.getElementById('patient_duration_badge');
        const value = document.getElementById('patient_duration_value');
        if (value) value.textContent = patientTotalDuration;
        if (badge) badge.classList.remove('hidden');

        // Update hidden field
        document.getElementById('hidden_patient_duration').value = patientTotalDuration;

        return patientTotalDuration;
    }

    // ===============================
    // FETCH & RENDER SLOTS (Updated for multi-service)
    // ===============================

    async function fetchPatientSlots() {
        const dentistId = document.getElementById('patient_dentist')?.value || '';
        const date = document.getElementById('patient_date')?.value;

        // ✅ Collect all selected services and levels
        const serviceIds = [];
        const serviceLevels = {};

        document.querySelectorAll('.service-row').forEach(row => {
            const svcSelect = row.querySelector('.service-select');
            const lvlSelect = row.querySelector('.level-select');

            if (svcSelect.value) {
                serviceIds.push(svcSelect.value);
                serviceLevels[svcSelect.value] = lvlSelect?.value || 'Standard';
            }
        });

        const slotsContainer = document.getElementById('patient_slots');
        const loadingEl = document.getElementById('slot_loading');
        const errorEl = document.getElementById('slot_error');
        const errorMsg = document.getElementById('slot_error_msg');

        if (!date || serviceIds.length === 0) {
            slotsContainer.innerHTML = '<p class="text-[10px] text-slate-400 col-span-full text-center py-4">Select at least one service and a date to view available times</p>';
            document.getElementById('patient_slot_section')?.classList.add('hidden');
            return;
        }

        document.getElementById('patient_slot_section')?.classList.remove('hidden');
        loadingEl?.classList.remove('hidden');
        errorEl?.classList.add('hidden');
        slotsContainer.innerHTML = '';
        slotsContainer.classList.add('opacity-50');

        try {
            const csrf = getPatientCsrf();
            const params = new URLSearchParams({
                dentist_id: dentistId,
                date: date,
                service_ids: serviceIds.join(','), // ✅ Send as comma-separated string
                service_levels: JSON.stringify(serviceLevels), // ✅ Send as JSON
                [csrf.name]: csrf.token
            });

            const requestUrl = `${PATIENT_API}/check-availability?${params}`;
            pLog('🔗 Fetching slots:', requestUrl);

            const response = await fetch(requestUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            pLog('📡 Response status:', response.status, response.ok ? '✅ OK' : '❌ Error');
            const data = await response.json();
            pLog('📦 API Response:', data);

            loadingEl?.classList.add('hidden');
            slotsContainer.classList.remove('opacity-50');

            if (!data.success) {
                pLog('❌ API returned success: false', data.error);
                slotsContainer.innerHTML = `<p class="text-[10px] text-red-600 col-span-full text-center py-4"><i class="fas fa-circle-exclamation mr-1"></i>${data.error || 'No available slots for this date'}</p>`;
                return;
            }

            if (!Array.isArray(data.slots)) {
                pLog('❌ slots is not an array:', typeof data.slots);
                slotsContainer.innerHTML = `<p class="text-[10px] text-red-600 col-span-full text-center py-4">Invalid slot data format</p>`;
                return;
            }

            pLog(`📊 Received ${data.slots.length} total slots`);

            const cutoff = timeToMinutes("16:00");
            const beforeCutoff = data.slots.filter(slot => timeToMinutes(slot.start) < cutoff);
            const availableCount = beforeCutoff.filter(s => s.available).length;
            pLog(`⏰ After 4PM cutoff: ${beforeCutoff.length} slots, ${availableCount} available`);

            if (availableCount === 0) {
                pLog('⚠️ No available slots after filtering');
                slotsContainer.innerHTML = data.slots.length > 0 ?
                    `<div class="col-span-full text-center py-6"><i class="fas fa-circle-exclamation text-2xl text-red-400 mb-2"></i><p class="text-[10px] text-red-600 font-bold">All slots are booked</p><p class="text-[9px] text-slate-400 mt-1">Try another date or select "Any Available Dentist"</p></div>` :
                    `<div class="col-span-full text-center py-6"><i class="fas fa-calendar-xmark text-2xl text-slate-300 mb-2"></i><p class="text-[10px] text-red-600 font-bold">No slots configured</p><p class="text-[9px] text-slate-400 mt-1">This date may be outside clinic hours</p></div>`;
                return;
            }

            if (data.required_duration_minutes) {
                patientTotalDuration = data.required_duration_minutes;
                document.getElementById('patient_duration_value').textContent = patientTotalDuration;
                document.getElementById('patient_duration_badge')?.classList.remove('hidden');
                document.getElementById('hidden_patient_duration').value = patientTotalDuration;
            }

            renderPatientSlots(data.slots);

        } catch (err) {
            console.error('💥 Slot fetch error:', err);
            loadingEl?.classList.add('hidden');
            slotsContainer.classList.remove('opacity-50');
            errorEl?.classList.remove('hidden');
            if (errorMsg) errorMsg.textContent = 'Failed to load slots. Check console for details.';
        }
    }
</script>

<?= $this->endSection() ?>