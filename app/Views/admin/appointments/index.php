<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<meta name="csrf-token" content="<?= csrf_hash() ?>">
<meta name="csrf-name" content="<?= csrf_token() ?>">
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
    <h3 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
        <i class="fas fa-calendar-check text-blue-600"></i>
        Appointments
    </h3>
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
            <?php if (empty($appointments)): ?>
                <tr>
                    <td colspan="6" class="p-12 text-center">
                        <div class="flex flex-col items-center gap-4 text-slate-400">
                            <!-- Icon with subtle animation -->
                            <div class="p-4 bg-slate-50 rounded-full">
                                <i class="fas fa-calendar-check text-4xl text-slate-300"></i>
                            </div>

                            <!-- Main Message (Dynamic based on tab) -->
                            <p class="font-semibold text-slate-600 text-base">
                                <?php
                                $emptyMessages = [
                                    'today'    => 'No appointments scheduled for today.',
                                    'tomorrow' => 'No appointments scheduled for tomorrow.',
                                    'upcoming' => 'No upcoming appointments found.',
                                    'all'      => 'No appointments found.'
                                ];
                                echo $emptyMessages[$currentTab] ?? 'No appointments found.';
                                ?>
                            </p>

                            <!-- Helper Text -->
                            <p class="text-[10px] text-slate-400 max-w-xs text-center leading-relaxed">
                                <?php
                                $helperMessages = [
                                    'today'    => 'Appointments will appear here once they are booked and assigned to your schedule.',
                                    'tomorrow' => 'Check back later or book a new appointment to fill your schedule.',
                                    'upcoming' => 'Try adjusting your filters or book a new appointment to get started.',
                                    'all'      => 'Try adjusting your filters or create a new appointment to get started.'
                                ];
                                echo $helperMessages[$currentTab] ?? 'Try adjusting your filters or create a new appointment.';
                                ?>
                            </p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($appointments as $a): ?>
                    <tr class="appt-row hover:bg-slate-50/50"
                        data-status="<?= $a['status'] ?>"
                        data-dentist="<?= $a['dentist_id'] ?>"
                        data-date="<?= $a['appointment_date'] ?>">
                        <td class="p-4">
                            <div class="flex flex-col">
                                <!-- Patient Name -->
                                <span class="font-bold text-slate-700 text-sm">
                                    <?= esc($a['patient_name']) ?>
                                </span>

                                <!-- Patient Code (shows only if available) -->
                                <?php if (!empty($a['patient_code'])): ?>
                                    <span class="text-[10px] font-mono font-semibold text-white bg-blue-600 px-2 py-0.5 rounded mt-1 w-fit">
                                        <?= esc($a['patient_code']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="p-4 font-medium text-blue-600"> <?= esc($a['dentist_name'] ?: 'N/A') ?></td>
                        <td class="p-4">
                            <?php
                            $apptDate = $a['appointment_date'];
                            $today = date('Y-m-d');
                            $tomorrow = date('Y-m-d', strtotime('+1 day'));

                            $badge = "";
                            if ($apptDate == $today) {
                                $badge = '<span class="bg-red-100 text-red-600 text-[9px] px-1.5 py-0.5 rounded-full font-black animate-pulse mr-1">TODAY</span>';
                            } elseif ($apptDate == $tomorrow) {
                                $badge = '<span class="bg-orange-100 text-orange-600 text-[9px] px-1.5 py-0.5 rounded-full font-black mr-1">TOMORROW</span>';
                            }
                            ?>
                            <div class="flex items-center">
                                <?= $badge ?>
                                <div class="text-xs font-bold text-slate-700"><?= $a['fmt_date'] ?? date('M d, Y', strtotime($a['appointment_date'])) ?></div>
                            </div>
                            <div class="text-[10px] text-blue-600 italic">
                                <?= $a['fmt_time'] ?? date('h:i A', strtotime($a['appointment_time'])) ?> - <?= $a['fmt_end'] ?? date('h:i A', strtotime($a['end_time'])) ?>
                            </div>
                        </td>
                        <td class="p-4 font-medium text-slate-600"><?= esc($a['service_name']) ?></td>
                        <td class="p-4 text-center">
                            <?php $c = ['Pending' => 'amber', 'Confirmed' => 'blue', 'Completed' => 'green', 'Cancelled' => 'red'][$a['status']]; ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-<?= $c ?>-100 text-<?= $c ?>-700"><?= $a['status'] ?></span>
                        </td>
                        <td class="p-3 text-center">
                            <div class="flex justify-center gap-1">
                                <!-- VIEW HISTORY (Compact) -->
                                <a href="<?= base_url('admin/appointments/history/' . $a['patient_id']) ?>"
                                    target="_blank"
                                    title="View History"
                                    class="bg-slate-800 text-white px-2 py-1 rounded-md font-bold text-[9px] uppercase hover:bg-black transition-all flex items-center gap-1">
                                    <i class="fas fa-history text-[8px]"></i> History
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
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

    $(document).ready(function() {

        loadAddressLevel('region');
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
        $('select[name="gender"]').on('change', function() {
            handleGenderLogic();
        });
        handleGenderLogic();
    });

    // FUNCTIONS
    function resetFilters() {
        $('.filter-input').val('');
        $('.appt-row').show();
    }


    function goToStep(step) {
        const s1 = document.getElementById('step1_container');
        const s2 = document.getElementById('step2_container');
        const d1 = document.getElementById('dot1');
        const d2 = document.getElementById('dot2');
        const title = document.getElementById('stepTitle');

        if (step === 2) {
            // Validation check for Step 1
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

    // I-update ang closeModal function para mag-reset sa Step 1
    function closeModal() {
        document.getElementById('apptModal').classList.add('hidden');
        goToStep(1); // Reset to first step
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
            const response = await fetch(`${BASE_URL}${cfg.file}`);
            const data = await response.json();

            // Filter kung may parent value (e.g., provinces need region_code)
            const filtered = cfg.filter ? data.filter(item => item[cfg.filter] == parentValue) : data;

            filtered.forEach(item => {
                const opt = new Option(item[cfg.display], item[cfg.value]);
                // Check OLD_DATA para sa form persistence
                if (OLD_DATA[level] && (item[cfg.display] === OLD_DATA[level] || item[cfg.value] === OLD_DATA[level])) {
                    opt.selected = true;
                }
                select.add(opt);
            });

            select.disabled = false;

            // Auto-load next level kung may selected value
            if (select.value && cfg.next) {
                loadAddressLevel(cfg.next, select.value);
            }
        } catch (error) {
            console.error(`Error loading ${level}:`, error);
        }
    }

    // View & Reschedule
    function viewAppointment(a) {
        // Direct use na lang ng formatted data galing Controller
        document.getElementById('v_patient').innerText = a.patient_name;
        document.getElementById('v_start').innerText = `${a.fmt_date} @ ${a.fmt_time}`;
        document.getElementById('v_end').innerText = `${a.fmt_end_date} @ ${a.fmt_end}`;
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