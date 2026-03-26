<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<style>
    /* Styling para mag-match sa Admin UI */
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

    .medical-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 0.5rem;
    }

    .fixed-label {
        font-size: 0.70rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        margin-bottom: 0.25rem;
        display: block;
    }
</style>

<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Welcome, <?= session()->get('full_name') ?>! 👋</h2>
        <p class="text-slate-500 text-sm">Here's what's happening with your dental health today.</p>
    </div>
    <div>
        <a href="/patient/book" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
            <i class="fas fa-plus mr-2"></i> Book New Appointment
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <!-- Upcoming Appointment Card -->
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

    <!-- Total Visits Card -->
    <div class="bg-white p-6 rounded-xl shadow-sm border flex items-center justify-between">
        <div>
            <p class="text-slate-500 text-sm font-medium">Total Clinic Visits</p>
            <h3 class="text-3xl font-bold text-slate-800 mt-1"><?= $stats['total_appointments'] ?></h3>
        </div>
        <div class="bg-green-100 p-4 rounded-xl text-green-600">
            <i class="fas fa-history text-2xl"></i>
        </div>
    </div>
</div>

<!-- Recent Appointments Table -->
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
                                Dr. <?= $ra['d_last'] ?? '<span class="italic text-slate-400 text-xs">To be assigned</span>' ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                $statusClass = 'bg-blue-100 text-blue-700';
                                if ($ra['status'] == 'Completed') $statusClass = 'bg-green-100 text-green-700';
                                if ($ra['status'] == 'Pending') $statusClass = 'bg-amber-100 text-amber-700';
                                if ($ra['status'] == 'Cancelled') $statusClass = 'bg-red-100 text-red-700';
                                ?>
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase <?= $statusClass ?>">
                                    <?= $ra['status'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button class="text-slate-400 hover:text-blue-600 transition-colors">
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

<!-- MEDICAL HISTORY MODAL -->
<?php if ($showMedicalModal): ?>
    <div id="medicalHistoryModal" class="fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm flex items-start justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl my-4 overflow-hidden border border-slate-200">

            <div class="p-6 border-b bg-slate-50">
                <h4 class="font-black text-slate-800 uppercase tracking-tight text-xl">📋 Medical History Required</h4>
                <p class="text-sm text-slate-500">You need to complete your medical record before proceeding to the dashboard.</p>
            </div>

            <form action="<?= base_url('patient/save-medical-history') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="patient_id" value="<?= $patient['id'] ?>">

                <div class="p-6 space-y-8">

                    <!-- Physician Info -->
                    <div class="bg-blue-50/50 p-6 border border-blue-100 rounded-2xl space-y-6">
                        <h5 class="text-xs font-black text-blue-600 uppercase italic">Physician Information</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="floating-label-group"><input type="text" name="physician_name" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Physician’s Name</label></div>
                            <div class="floating-label-group"><input type="text" name="physician_specialty" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Speciality</label></div>
                            <div class="floating-label-group"><input type="text" name="physician_address" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Office Address</label></div>
                            <div class="floating-label-group"><input type="text" name="physician_phone" placeholder=" " class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white"><label class="floating-label">Tel. No.</label></div>
                        </div>

                        <hr class="border-blue-100">

                        <!-- Main Questions -->
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
                                        <label class="flex items-center gap-1 cursor-pointer font-bold">
                                            <input type="radio" name="<?= $key ?>" value="1" id="<?= $key ?>_yes"> Yes
                                        </label>
                                        <label class="flex items-center gap-1 cursor-pointer font-bold">
                                            <input type="radio" name="<?= $key ?>" value="0" id="<?= $key ?>_no" checked> No
                                        </label>
                                    </div>
                                </div>
                                <?php if ($key == 'has_serious_illness'): ?>
                                    <input type="text" name="serious_illness_details" id="<?= $key ?>_details"
                                        placeholder="If so, what illness or operation?"
                                        class="w-full p-2 border-b-[0.5px] border-gray-300 bg-transparent text-xs outline-none focus:border-gray-900 transition-all duration-200 mb-2"
                                        disabled>

                                <?php elseif ($key == 'is_hospitalized'): ?>
                                    <input type="text" name="hospitalization_details" id="<?= $key ?>_details"
                                        placeholder="If so, when and why?"
                                        class="w-full p-2 border-b-[0.5px] border-gray-300 bg-transparent text-xs outline-none focus:border-gray-900 transition-all duration-200 mb-2"
                                        disabled>

                                <?php elseif ($key == 'is_taking_medication'): ?>
                                    <input type="text" name="medication_details" id="<?= $key ?>_details"
                                        placeholder="If so, please specify..."
                                        class="w-full p-2 border-b-[0.5px] border-gray-300 bg-transparent text-xs outline-none focus:border-gray-900 transition-all duration-200 mb-2"
                                        disabled>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <!-- Q8 Allergies (Manual List) -->
                            <div class="space-y-2 pt-4 border-t border-blue-100">
                                <label class="font-bold text-slate-800">8. Are you allergic to any of the following?</label>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 pl-4">
                                    <label class="flex items-center text-xs cursor-pointer"><input type="checkbox" name="medical_conditions[]" value="local_anesthetic" class="mr-2"> Local Anesthetic</label>
                                    <label class="flex items-center text-xs cursor-pointer"><input type="checkbox" name="medical_conditions[]" value="penicillin_antibiotics" class="mr-2"> Penicillin, Antibiotics</label>
                                    <label class="flex items-center text-xs cursor-pointer"><input type="checkbox" name="medical_conditions[]" value="latex" class="mr-2"> Latex</label>
                                    <label class="flex items-center text-xs cursor-pointer"><input type="checkbox" name="medical_conditions[]" value="sulfa_drugs" class="mr-2"> Sulfa Drugs</label>
                                    <label class="flex items-center text-xs cursor-pointer"><input type="checkbox" name="medical_conditions[]" value="aspirin" class="mr-2"> Aspirin</label>
                                    <div class="flex items-center gap-2 md:col-span-2">
                                        <span class="text-xs">Others:</span>
                                        <input type="text" name="other_allergy" placeholder="Specify other allergies..." class="flex-1 border-b-[0.5px] border-gray-900 text-xs outline-none bg-transparent focus:border-blue-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Q9 & Vitals -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                                <div class="flex items-center gap-3">
                                    <label class="font-bold text-slate-800">9. Bleeding Time:</label>
                                    <input type="text" name="bleeding_time" class="flex-1 border-b bg-transparent text-sm outline-none focus:border-blue-500">
                                </div>
                                <div class="floating-label-group">
                                    <select name="blood_type" class="floating-input w-full p-2.5 border rounded-lg text-sm bg-white">
                                        <option value="" selected disabled hidden></option>
                                        <?php foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'Unknown'] as $bt): ?>
                                            <option value="<?= $bt ?>"><?= $bt ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label class="floating-label">Blood Type</label>
                                </div>
                            </div>

                            <!-- Q10 Women Only Section -->
                            <div id="women_section" class="bg-pink-50/50 p-4 rounded-xl border border-pink-100 space-y-3 transition-all <?= ($patient['gender'] == 'Male') ? 'opacity-50 pointer-events-none grayscale' : '' ?>">
                                <label class="font-bold text-pink-700 uppercase text-xs">10. For Women Only:</label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="flex gap-3 items-center text-[11px]">
                                        <span>Are you pregnant?</span>
                                        <div class="flex gap-2">
                                            <label><input type="radio" name="is_pregnant" value="1" <?= ($patient['gender'] == 'Male') ? 'disabled' : '' ?>> Yes</label>
                                            <label><input type="radio" name="is_pregnant" value="0" checked> No</label>
                                        </div>
                                    </div>
                                    <div class="flex gap-3 items-center text-[11px]">
                                        <span>Are you nursing?</span>
                                        <div class="flex gap-2">
                                            <label><input type="radio" name="is_nursing" value="1" <?= ($patient['gender'] == 'Male') ? 'disabled' : '' ?>> Yes</label>
                                            <label><input type="radio" name="is_nursing" value="0" checked> No</label>
                                        </div>
                                    </div>
                                    <div class="flex gap-3 items-center text-[11px]">
                                        <span>Taking birth control?</span>
                                        <div class="flex gap-2">
                                            <label><input type="radio" name="is_taking_birth_control" value="1" <?= ($patient['gender'] == 'Male') ? 'disabled' : '' ?>> Yes</label>
                                            <label><input type="radio" name="is_taking_birth_control" value="0" checked> No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Medical Checklist -->
                    <div class="border-t pt-4">
                        <label class="fixed-label text-blue-600 mb-3 font-black">Medical History Checklist (Please check all that apply):</label>
                        <div class="medical-grid bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <?php
                            $grouped = [];
                            $manualAllergies = ['Local Anesthetic', 'Penicillin, Antibiotics', 'Latex', 'Sulfa Drugs', 'Aspirin'];

                            foreach ($medical_conditions as $mc) {
                                if (in_array($mc['condition_label'], $manualAllergies)) continue;
                                if (strtolower($mc['category']) == 'allergy' || strtolower($mc['category']) == 'allergies') continue;
                                $grouped[$mc['category']][] = $mc;
                            }

                            foreach ($grouped as $cat => $conditions): ?>
                                <div class="col-span-full text-[10px] font-black text-slate-400 uppercase mt-4 mb-1 border-b border-slate-200"><?= esc($cat) ?></div>
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

                <div class="p-6 border-t bg-slate-50">
                    <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold uppercase tracking-widest hover:bg-blue-700 shadow-xl transition-all">
                        Save Medical Record & Proceed
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const questions = [
        'has_serious_illness',
        'is_hospitalized',
        'is_taking_medication'
    ];

    questions.forEach(key => {
        const yes = document.getElementById(key + '_yes');
        const no = document.getElementById(key + '_no');
        const input = document.getElementById(key + '_details');

        if (!yes || !no || !input) return;

        function toggle() {
            if (yes.checked) {
                input.disabled = false;
                input.focus();
            } else {
                input.disabled = true;
                input.value = '';
            }
        }

        yes.addEventListener('change', toggle);
        no.addEventListener('change', toggle);
    });
});
</script>

<?= $this->endSection() ?>