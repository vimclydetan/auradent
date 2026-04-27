<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<style>
    /* ===== CORE THEME VARIABLES ===== */
    :root {
        --primary: #2563eb;
        --primary-light: #eff6ff;
        --primary-border: #bfdbfe;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --bg-card: #ffffff;
        --bg-section: #f8fafc;
        --border-light: #e2e8f0;
    }

    /* ===== CARD STYLES ===== */
    .card {
        background: var(--bg-card);
        border: 1px solid var(--border-light);
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        transition: box-shadow 0.2s ease;
    }

    .card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 1rem;
        margin-bottom: 1.25rem;
        border-bottom: 1px solid var(--border-light);
    }

    .card-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--primary);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .card-subtitle {
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    /* ===== INFO DISPLAY ===== */
    .info-group {
        margin-bottom: 1rem;
    }

    .info-label {
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.025em;
        margin-bottom: 0.25rem;
        display: block;
    }

    .info-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--text-primary);
        line-height: 1.4;
    }

    .info-value.muted {
        color: var(--text-secondary);
        font-weight: 500;
    }

    /* ===== BADGES & TAGS ===== */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .badge-yes {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .badge-no {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fcd34d;
    }

    .tag {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.35rem 0.75rem;
        background: var(--primary-light);
        color: var(--primary);
        border: 1px solid var(--primary-border);
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* ===== MEDICAL GRID ===== */
    .medical-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 0.75rem;
    }

    /* ===== SECTION DIVIDER ===== */
    .section-divider {
        height: 1px;
        background: linear-gradient(to right, transparent, var(--border-light), transparent);
        margin: 1.5rem 0;
    }

    /* ===== UTILITIES ===== */
    .text-primary {
        color: var(--primary);
    }

    .bg-primary-light {
        background: var(--primary-light);
    }

    .border-primary {
        border-color: var(--primary-border);
    }

    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="max-w-7xl mx-auto px-4 py-2">

    <!-- ===== HEADER ===== -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-200">

        <!-- LEFT: Patient Info -->
        <div class="flex items-start gap-4 min-w-0">
            <!-- Back Button -->
            <a href="<?= base_url('receptionist/patients') ?>"
                class="no-print flex-shrink-0 w-10 h-10 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition-colors"
                aria-label="Back to patients list">
                <i class="fas fa-arrow-left"></i>
            </a>

            <!-- Patient Details -->
            <div class="min-w-0">
                <div class="flex items-center gap-3 mb-1">
                    <!-- Avatar Initials -->
                    <div class="flex-shrink-0 w-14 h-14 rounded-full bg-blue-600 flex items-center justify-center text-white text-lg font-bold shadow-lg">
                        <?= esc(strtoupper(substr($patient['first_name'], 0, 1) . substr($patient['last_name'], 0, 1))) ?>
                    </div>

                    <!-- Name & ID -->
                    <div class="min-w-0">
                        <h2 class="text-2xl font-bold text-slate-800 truncate">
                            <?= esc($patient['first_name'] . ' ' . $patient['middle_name'] . ' ' . $patient['last_name']) ?>
                            <?php if (!empty($patient['name_suffix'])): ?>
                                <span class="text-slate-500 font-normal"><?= esc($patient['name_suffix']) ?></span>
                            <?php endif; ?>
                        </h2>
                        <p class="text-sm text-blue-600 font-semibold">
                            ID: <?= esc($patient['patient_code']) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Actions + Updated Badge -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 min-w-0">

            <!-- Action Buttons + Badge Wrapper -->
            <div class="flex flex-col sm:items-end gap-2">

                <!-- Updated Badge (Now below Edit button) -->
                <span class="text-[10px] font-medium text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full whitespace-nowrap self-start sm:self-end inline-flex items-center gap-1">
                    <i class="fas fa-clock opacity-60"></i>
                    Updated: <?= date('M d, Y', strtotime($patient['updated_at'])) ?>
                </span>
                <!-- Buttons -->
                <div class="flex flex-wrap gap-2 no-print">
                    <button type="button"
                        class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm flex items-center gap-2"
                        onclick="window.print()"
                        aria-label="Print patient record">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <a href="<?= base_url('receptionist/patients/edit/' . $patient['id']) ?>"
                        class="px-4 py-2.5 bg-blue-600 text-white rounded-lg font-semibold text-sm hover:bg-blue-700 transition-all shadow-md flex items-center gap-2"
                        aria-label="Edit patient profile">
                        <i class="fas fa-pen"></i> Edit
                    </a>
                </div>


            </div>
        </div>
    </div>

    <!-- ===== MAIN GRID LAYOUT ===== -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

        <!-- ===== LEFT SIDEBAR (30%) ===== -->
        <div class="xl:col-span-4 space-y-6">

            <!-- Personal Info Card -->
            <div class="card animate-fade-in">
                <div class="card-header">
                    <i class="fas fa-user text-blue-600"></i>
                    <span class="card-title">Personal Information</span>
                </div>
                <div class="grid grid-cols-2 gap-x-6 gap-y-4">
                    <div class="col-span-2">
                        <span class="info-label">Full Name</span>
                        <p class="info-value"><?= esc($patient['first_name'] . ' ' . $patient['middle_name'] . ' ' . $patient['last_name']) ?></p>
                    </div>
                    <div>
                        <span class="info-label">Gender</span>
                        <p class="info-value"><?= esc($patient['gender']) ?></p>
                    </div>
                    <div>
                        <span class="info-label">Birthdate</span>
                        <p class="info-value"><?= date('M d, Y', strtotime($patient['birthdate'])) ?></p>
                    </div>
                    <div>
                        <span class="info-label">Age</span>
                        <p class="info-value"><?= date_diff(date_create($patient['birthdate']), date_create('today'))->y ?> years</p>
                    </div>
                </div>
            </div>

            <!-- Contact Card -->
            <div class="card animate-fade-in" style="animation-delay: 0.1s">
                <div class="card-header">
                    <i class="fas fa-contact-card text-blue-600"></i>
                    <span class="card-title">Contact Details</span>
                </div>
                <div class="space-y-4">
                    <div>
                        <span class="info-label">Mobile Number</span>
                        <p class="info-value text-blue-600 font-mono"><?= esc($patient['primary_mobile']) ?></p>
                    </div>
                    <div>
                        <span class="info-label">Email Address</span>
                        <p class="info-value"><?= esc($patient['email'] ?: '—') ?></p>
                    </div>
                    <div>
                        <span class="info-label">Complete Address</span>
                        <p class="info-value text-sm leading-relaxed">
                            <?= esc(implode(', ', array_filter([
                                $patient['house_number'],
                                $patient['street_name'],
                                $patient['barangay'],
                                $patient['city'],
                                $patient['province']
                            ]))) ?: '—' ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Insurance Card -->
            <div class="card animate-fade-in" style="animation-delay: 0.2s">
                <div class="card-header">
                    <i class="fas fa-shield-alt text-blue-600"></i>
                    <span class="card-title">Insurance</span>
                </div>
                <?php if ($patient['has_insurance']): ?>
                    <div class="space-y-3">
                        <div>
                            <span class="info-label">Provider</span>
                            <p class="info-value"><?= esc($patient['insurance_provider']) ?></p>
                        </div>
                        <div>
                            <span class="info-label">Policy Number</span>
                            <p class="info-value font-mono text-sm"><?= esc($patient['insurance_policy_number']) ?></p>
                        </div>
                        <div>
                            <span class="info-label">Valid Until</span>
                            <p class="info-value"><?= date('F d, Y', strtotime($patient['insurance_valid_until'])) ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-sm text-slate-400 italic text-center py-3">No insurance on file</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- ===== RIGHT CONTENT (70%) ===== -->
        <div class="xl:col-span-8 space-y-6">

            <!-- Medical Record Header -->
            <div class="card border-t-4 border-t-blue-600 animate-fade-in">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-heartbeat text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">Medical History</h3>
                            <p class="text-xs text-slate-500">Comprehensive health record</p>
                        </div>
                    </div>
                </div>

                <!-- Physician Info -->
                <div class="bg-blue-50/50 rounded-xl p-4 mb-6 border border-blue-100">
                    <span class="card-subtitle block mb-3">Attending Physician</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <span class="info-label">Name</span>
                            <p class="info-value"><?= esc($medical_history['physician_name'] ?? '—') ?></p>
                        </div>
                        <div>
                            <span class="info-label">Specialty</span>
                            <p class="info-value"><?= esc($medical_history['physician_specialty'] ?? '—') ?></p>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="info-label">Clinic Address</span>
                            <p class="info-value text-sm"><?= esc($medical_history['physician_address'] ?? '—') ?></p>
                        </div>
                        <div>
                            <span class="info-label">Contact</span>
                            <p class="info-value font-mono text-sm"><?= esc($medical_history['physician_phone'] ?? '—') ?></p>
                        </div>
                    </div>
                </div>

                <!-- Vital Signs -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <div class="bg-red-50 rounded-xl p-4 border border-red-100 text-center">
                        <span class="info-label text-red-600 block mb-1">Blood Type</span>
                        <p class="text-2xl font-black text-red-700"><?= esc($medical_history['blood_type'] ?? '—') ?></p>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-100 text-center">
                        <span class="info-label text-blue-600 block mb-1">Blood Pressure</span>
                        <p class="text-2xl font-black text-blue-700"><?= esc($medical_history['blood_pressure'] ?? '—') ?></p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 text-center">
                        <span class="info-label block mb-1">Bleeding Time</span>
                        <p class="text-2xl font-black text-slate-700">
                            <?php
                            $bt = $medical_history['bleeding_time'] ?? '';
                            if ($bt && preg_match('/(\d+)m\s*(\d+)s/', $bt, $m)) {
                                echo ($m[1] > 0 ? "{$m[1]}m " : "") . "{$m[2]}s";
                            } else {
                                echo esc($bt ?: '—');
                            }
                            ?>
                        </p>
                    </div>
                </div>

                <div class="section-divider"></div>

                <!-- Health Questionnaire -->
                <div class="mb-6">
                    <span class="card-subtitle block mb-4">Health Assessment</span>
                    <div class="space-y-3">
                        <?php
                        $questions = [
                            'is_good_health' => ['label' => 'Currently in good health', 'detail' => 'serious_illness_details'],
                            'is_under_medical_treatment' => ['label' => 'Under medical treatment', 'detail' => 'medication_details'],
                            'has_serious_illness' => ['label' => 'History of serious illness/surgery', 'detail' => 'serious_illness_details'],
                            'is_hospitalized' => ['label' => 'Previous hospitalization', 'detail' => 'hospitalization_details'],
                            'is_taking_medication' => ['label' => 'Currently taking medication', 'detail' => 'medication_details'],
                            'uses_tobacco' => ['label' => 'Uses tobacco products'],
                            'uses_drugs' => ['label' => 'Uses alcohol or substances']
                        ];
                        foreach ($questions as $key => $config):
                            $isYes = ($medical_history[$key] ?? 0) == 1;
                        ?>
                            <div class="flex items-center justify-between py-2.5 px-3 rounded-lg hover:bg-slate-50 transition-colors">
                                <span class="text-sm font-medium text-slate-700"><?= $config['label'] ?></span>
                                <span class="badge <?= $isYes ? 'badge-yes' : 'badge-no' ?>">
                                    <?= $isYes ? 'Yes' : 'No' ?>
                                </span>
                            </div>
                            <?php if ($isYes && isset($config['detail']) && !empty($medical_history[$config['detail']])): ?>
                                <div class="ml-4 mb-3 pl-3 border-l-2 border-amber-300">
                                    <p class="text-xs text-amber-800 bg-amber-50 px-3 py-2 rounded">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        <?= esc($medical_history[$config['detail']]) ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Women's Health (Conditional) -->
                <?php if ($patient['gender'] === 'Female'): ?>
                    <div class="bg-pink-50/50 rounded-xl p-4 mb-6 border border-pink-100">
                        <span class="card-subtitle text-pink-600 block mb-3">Women's Health</span>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <?php
                            $womenQs = [
                                'is_pregnant' => 'Pregnant',
                                'is_nursing' => 'Currently Nursing',
                                'is_taking_birth_control' => 'On Birth Control'
                            ];
                            foreach ($womenQs as $key => $label):
                            ?>
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-pink-100">
                                    <span class="text-sm font-medium text-slate-700"><?= $label ?></span>
                                    <span class="badge <?= ($medical_history[$key] ?? 0) ? 'badge-yes' : 'badge-no' ?>">
                                        <?= ($medical_history[$key] ?? 0) ? 'Yes' : 'No' ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="section-divider"></div>
                <?php endif; ?>

                <!-- Allergies & Conditions Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- Allergies -->
                    <div class="bg-amber-50/30 rounded-xl p-5 border border-amber-100">
                        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-amber-100">
                            <i class="fas fa-exclamation-triangle text-amber-500"></i>
                            <span class="font-bold text-amber-800 text-sm">Known Allergies</span>
                        </div>

                        <?php
                        $allergyKeys = ['local_anesthetic', 'penicillin_antibiotics', 'latex', 'sulfa_drugs', 'aspirin'];
                        $allergyLabels = [
                            'local_anesthetic' => 'Local Anesthetic',
                            'penicillin_antibiotics' => 'Penicillin/Antibiotics',
                            'latex' => 'Latex',
                            'sulfa_drugs' => 'Sulfa Drugs',
                            'aspirin' => 'Aspirin/NSAIDs'
                        ];
                        $hasAllergies = false;

                        foreach ($allergyKeys as $key):
                            $found = false;
                            if (!empty($patient_conditions)) {
                                foreach ($patient_conditions as $pc) {
                                    $ckey = is_array($pc) ? ($pc['condition_key'] ?? '') : ($pc->condition_key ?? '');
                                    if (strtolower($ckey) === strtolower($key)) {
                                        $found = true;
                                        $hasAllergies = true;
                                        break;
                                    }
                                }
                            }
                        ?>
                            <label class="flex items-center gap-2.5 py-2 cursor-pointer">
                                <div class="w-5 h-5 rounded border-2 flex items-center justify-center flex-shrink-0
                                    <?= $found ? 'bg-amber-500 border-amber-500' : 'border-amber-200 bg-white' ?>">
                                    <?php if ($found): ?><i class="fas fa-check text-white text-xs"></i><?php endif; ?>
                                </div>
                                <span class="text-sm <?= $found ? 'font-semibold text-amber-900' : 'text-slate-400' ?>">
                                    <?= $allergyLabels[$key] ?>
                                </span>
                            </label>
                        <?php endforeach; ?>

                        <?php if (!empty($medical_history['other_allergy'])):
                            $hasAllergies = true;
                        ?>
                            <div class="mt-4 pt-3 border-t border-amber-200">
                                <span class="text-xs font-semibold text-amber-700 block mb-2">Other:</span>
                                <p class="text-sm font-medium text-amber-900 bg-amber-100 px-3 py-2 rounded-lg">
                                    <?= esc($medical_history['other_allergy']) ?>
                                </p>
                            </div>
                        <?php endif; ?>

                        <?php if (!$hasAllergies): ?>
                            <p class="text-amber-400/80 text-sm italic text-center py-4">No allergies recorded</p>
                        <?php endif; ?>
                    </div>

                    <!-- Medical Conditions -->
                    <div class="bg-slate-50 rounded-xl p-5 border border-slate-200">
                        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-200">
                            <i class="fas fa-notes-medical text-blue-500"></i>
                            <span class="font-bold text-slate-700 text-sm">Medical Conditions</span>
                        </div>

                        <?php
                        $allergyKeys = ['local_anesthetic', 'penicillin_antibiotics', 'latex', 'sulfa_drugs', 'aspirin'];
                        $conditions = [];

                        if (!empty($patient_conditions) && is_array($patient_conditions)) {
                            foreach ($patient_conditions as $pc) {
                                $key = is_array($pc) ? ($pc['condition_key'] ?? '') : ($pc->condition_key ?? '');
                                $label = is_array($pc) ? ($pc['condition_label'] ?? '') : ($pc->condition_label ?? '');
                                $category = is_array($pc) ? ($pc['category'] ?? 'Other') : ($pc->category ?? 'Other');

                                if ($key && $label && !in_array(strtolower($key), $allergyKeys)) {
                                    $conditions[] = ['label' => $label, 'category' => $category];
                                }
                            }
                        }

                        if (!empty($conditions)):
                            $grouped = [];
                            foreach ($conditions as $c) {
                                $grouped[$c['category']][] = $c;
                            }
                        ?>
                            <div class="space-y-4 max-h-64 overflow-y-auto pr-2">
                                <?php foreach ($grouped as $category => $items): ?>
                                    <div>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">
                                            <?= esc($category) ?>
                                        </span>
                                        <div class="flex flex-wrap gap-2">
                                            <?php foreach ($items as $item): ?>
                                                <span class="tag">
                                                    <?= esc($item['label']) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-slate-400 text-sm italic text-center py-6">No medical conditions recorded</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    // Simple fade-in animation on load
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.animate-fade-in').forEach((el, i) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(8px)';
            el.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            setTimeout(() => {
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, 100 * i);
        });
    });
</script>

<?= $this->endSection() ?>