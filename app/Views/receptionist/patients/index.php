<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<style>
    .filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 1.25rem;
        border-radius: 1rem;
        margin-bottom: 1.5rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
    }
</style>

<!-- HEADER -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h3 class="text-2xl font-bold text-slate-800 tracking-tight italic uppercase">👥 Patients Directory</h3>
        <p class="text-xs text-slate-500">Manage patient records, view histories, and update profiles.</p>
    </div>
    <a href="<?= base_url('receptionist/walkin') ?>" class="bg-blue-600 text-white px-5 py-2.5 rounded-xl hover:bg-blue-700 shadow-lg font-bold transition-all active:scale-95 text-xs uppercase tracking-wider">
        <i class="fas fa-plus mr-2"></i> New Patient
    </a>
</div>

<!-- FILTERS -->
<div class="filter-card shadow-sm">
    <div class="space-y-1">
        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Search Patients</label>
        <input type="text" id="patientSearch" placeholder="Search Code, Name, or Mobile..." class="filter-input">
    </div>
    <div class="space-y-1">
        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Gender</label>
        <select id="genderFilter" class="filter-input">
            <option value="">All Genders</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>
    </div>
    <div class="space-y-1">
        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Filter by Last Visit</label>
        <input type="date" id="lastVisitFilter" class="filter-input">
    </div>
    <div class="flex items-end">
        <button onclick="resetFilters()" class="w-full text-[10px] font-black text-red-500 hover:bg-red-50 py-3 rounded-lg border border-red-200 transition-all uppercase tracking-widest">Reset Filters</button>
    </div>
</div>

<!-- TABLE -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50 text-slate-400 text-[10px] uppercase font-black tracking-widest">
            <tr>
                <th class="p-4 border-b">Patient Code</th> <!-- BAGO ITONG COLUMN -->
                <th class="p-4 border-b">Full Name</th>
                <th class="p-4 border-b">Contact Info</th>
                <th class="p-4 border-b text-center">Gender / Age</th>
                <th class="p-4 border-b">Address</th>
                <th class="p-4 border-b text-center">Actions</th>
            </tr>
        </thead>
        <tbody id="patientTableBody" class="text-sm divide-y divide-slate-100">
            <?php foreach ($patients as $p): ?>
                <tr class="patient-row hover:bg-slate-50/50 transition-all"
                    data-gender="<?= $p['gender'] ?>"
                    data-lastvisit="<?= $p['last_visit_date'] ? date('Y-m-d', strtotime($p['last_visit_date'])) : '' ?>">

                    <!-- PATIENT CODE BADGE -->
                    <td class="p-4">
                        <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-md font-mono font-black text-[11px] border border-blue-100 tracking-tighter">
                            <?= $p['patient_code'] ?: 'NO-CODE' ?>
                        </span>
                    </td>

                    <!-- NAME -->
                    <td class="p-4">
                        <div class="font-bold text-slate-700"><?= strtoupper($p['last_name']) ?>, <?= $p['first_name'] ?></div>
                        <div class="text-[10px] text-slate-400 font-medium tracking-tight"><?= $p['email'] ?: 'no-email@record.com' ?></div>
                    </td>

                    <!-- CONTACT -->
                    <td class="p-4">
                        <div class="font-bold text-blue-600 text-xs tracking-tight italic"><?= $p['primary_mobile'] ?></div>
                    </td>

                    <!-- GENDER / AGE -->
                    <td class="p-4 text-center">
                        <div class="text-xs font-bold text-slate-700"><?= $p['gender'] ?></div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">
                            <?php
                            $age = date_diff(date_create($p['birthdate']), date_create('today'))->y;
                            echo $age . " Years Old";
                            ?>
                        </div>
                    </td>

                    <!-- ADDRESS -->
                    <td class="p-4">
                        <div class="text-[11px] text-slate-600 font-medium italic leading-tight">
                            <span class="text-slate-400 font-bold not-italic"><?= $p['province'] ?></span> <br>
                            <?= $p['city'] ?>, <?= $p['barangay'] ?>
                        </div>
                    </td>

                    <!-- ACTIONS -->
                    <td class="p-4 text-center">
                        <div class="flex justify-center gap-1.5">
                            <!-- VISIT HISTORY -->
                            <a href="<?= base_url('receptionist/patients/history/' . $p['id']) ?>"
                                class="bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-lg font-bold text-[10px] uppercase hover:bg-indigo-600 hover:text-white transition-all shadow-sm flex items-center gap-1">
                                <i class="fas fa-history text-[9px]"></i> History
                            </a>

                            <!-- VIEW PROFILE -->
                            <a href="<?= base_url('receptionist/patients/view/' . $p['id']) ?>"
                                class="bg-slate-100 text-slate-500 px-3 py-1.5 rounded-lg font-bold text-[10px] uppercase hover:bg-slate-800 hover:text-white transition-all shadow-sm flex items-center gap-1">
                                <i class="fas fa-user text-[9px]"></i> View
                            </a>

                            <!-- EDIT PROFILE -->
                            <a href="<?= base_url('receptionist/patients/edit/' . $p['id']) ?>"
                                class="bg-amber-50 text-amber-600 px-3 py-1.5 rounded-lg font-bold text-[10px] uppercase hover:bg-amber-500 hover:text-white transition-all shadow-sm flex items-center gap-1">
                                <i class="fas fa-user-edit text-[9px]"></i> Edit
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        function filterPatients() {
            const searchTerm = $('#patientSearch').val().toLowerCase();
            const genderTerm = $('#genderFilter').val();
            const dateTerm = $('#lastVisitFilter').val();

            $('.patient-row').each(function() {
                // Kukunin nito ang lahat ng text sa row kasama ang Patient Code
                const text = $(this).text().toLowerCase();
                const gender = $(this).data('gender');
                const lastVisit = $(this).data('lastvisit');

                const matchesSearch = text.includes(searchTerm);
                const matchesGender = !genderTerm || gender === genderTerm;
                const matchesDate = !dateTerm || lastVisit === dateTerm;

                $(this).toggle(matchesSearch && matchesGender && matchesDate);
            });
        }

        $('#patientSearch, #genderFilter, #lastVisitFilter').on('input change', filterPatients);
    });

    function resetFilters() {
        $('.filter-input').val('');
        $('.patient-row').show();
    }
</script>
<?= $this->endSection() ?>