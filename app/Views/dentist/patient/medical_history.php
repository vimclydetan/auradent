<?= $this->extend('layouts/dashboard_layout') ?>
<?= $this->section('content') ?>

<style>
    /* ===== THEME VARIABLES ===== */
    :root {
        --primary: #2563eb;
        --primary-light: #eff6ff;
        --primary-border: #bfdbfe;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --bg-card: #ffffff;
        --bg-section: #f8fafc;
        --border-light: #e2e8f0;
        --danger: #dc2626;
        --warning: #f59e0b;
        --success: #16a34a;
    }

    /* ===== CARD COMPONENT ===== */
    .card {
        background: var(--bg-card);
        border: 1px solid var(--border-light);
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }
    .card:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
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
    .card-title.danger { color: var(--danger); }
    .card-title.warning { color: var(--warning); }
    .card-title.success { color: var(--success); }

    /* ===== CARD SUBTITLE (Added) ===== */
    .card-subtitle {
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    /* ===== INFO DISPLAY ===== */
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
        line-height: 1.5;
    }
    .info-value.textarea {
        white-space: pre-wrap;
        word-break: break-word;
        background: var(--bg-section);
        padding: 0.75rem;
        border-radius: 0.5rem;
        border: 1px solid var(--border-light);
        font-family: ui-monospace, SFMono-Regular, monospace;
        font-size: 0.875rem;
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

    /* ===== DENTAL CHART ===== */
    .dental-chart-container {
        background: var(--bg-section);
        border: 2px dashed var(--border-light);
        border-radius: 1rem;
        aspect-ratio: 4/3;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }
    .dental-chart-container img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        transition: transform 0.3s ease;
    }
    .dental-chart-container:hover img {
        transform: scale(1.02);
    }
    .dental-chart-placeholder {
        text-align: center;
        color: var(--text-secondary);
        padding: 2rem;
    }
    .dental-chart-placeholder i {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
        display: block;
    }

    /* ===== VITALS DISPLAY ===== */
    .vital-box {
        text-align: center;
        padding: 1rem;
        border-radius: 0.75rem;
        background: var(--bg-section);
        border: 1px solid var(--border-light);
    }
    .vital-box.blood {
        background: #fef2f2;
        border-color: #fecaca;
    }
    .vital-box.pressure {
        background: #eff6ff;
        border-color: #bfdbfe;
    }
    .vital-box.bleeding {
        background: #f8fafc;
        border-color: #e2e8f0;
    }
    .vital-label {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
        display: block;
    }
    .vital-value {
        font-size: 1.5rem;
        font-weight: 800;
        line-height: 1;
    }
    .vital-box.blood .vital-value { color: var(--danger); }
    .vital-box.pressure .vital-value { color: var(--primary); }
    .vital-box.bleeding .vital-value { color: var(--text-primary); }

    /* ===== SECTION DIVIDER ===== */
    .section-divider {
        height: 1px;
        background: linear-gradient(to right, transparent, var(--border-light), transparent);
        margin: 1.5rem 0;
    }

    /* ===== ANIMATIONS ===== */
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out forwards;
        opacity: 0;
        transform: translateY(8px);
    }
    @keyframes fadeIn {
        to { opacity: 1; transform: translateY(0); }
    }

    /* ===== CUSTOM SCROLLBAR ===== */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* ===== PRINT STYLES ===== */
    @media print {
        .no-print { display: none !important; }
        .card {
            box-shadow: none !important;
            border: 1px solid #ccc !important;
            break-inside: avoid;
        }
        .info-value.textarea {
            white-space: pre-wrap;
            border: 1px solid #ccc;
        }
        body { background: white !important; }
    }
</style>

<div class="max-w-7xl mx-auto px-4 py-2">

    <!-- ===== PATIENT HEADER ===== -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-200">

        <!-- LEFT: Patient Info -->
        <div class="flex items-start gap-4 min-w-0">
            <!-- Back Button -->
            <a href="<?= base_url('dentist/appointments') ?>"
                class="no-print flex-shrink-0 w-10 h-10 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition-colors"
                aria-label="Back to appointments">
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
            <div class="flex flex-col sm:items-end gap-2">

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-2 no-print">
                    <button type="button"
                        class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm flex items-center gap-2"
                        onclick="window.print()"
                        aria-label="Print patient record">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <a href="<?= base_url('dentist/patients/edit/' . $patient['id']) ?>"
                        class="px-4 py-2.5 bg-blue-600 text-white rounded-lg font-semibold text-sm hover:bg-blue-700 transition-all shadow-md flex items-center gap-2"
                        aria-label="Edit patient profile">
                        <i class="fas fa-pen"></i> Edit
                    </a>
                </div>

                <!-- Updated Badge (BELOW buttons) -->
                <span class="text-[10px] font-medium text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full whitespace-nowrap self-start sm:self-end inline-flex items-center gap-1">
                    <i class="fas fa-clock opacity-60"></i>
                    Updated: <?= date('M d, Y', strtotime($patient['updated_at'])) ?>
                </span>

            </div>
        </div>
    </div>

    <!-- ===== MAIN GRID LAYOUT ===== -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

        <!-- ===== LEFT SIDEBAR (35%) ===== -->
        <div class="xl:col-span-4 space-y-6">

            <!-- Vitals Card -->
            <div class="card animate-fade-in border-t-4 border-t-blue-600">
                <div class="card-header">
                    <i class="fas fa-heartbeat text-blue-600"></i>
                    <span class="card-title">Patient Vitals</span>
                </div>
                <div class="grid grid-cols-3 gap-3 mb-4">
                    <div class="vital-box pressure">
                        <span class="vital-label">BP</span>
                        <p class="vital-value"><?= esc($medical_history['blood_pressure'] ?? '—') ?></p>
                    </div>
                    <div class="vital-box blood">
                        <span class="vital-label">Type</span>
                        <p class="vital-value"><?= esc($medical_history['blood_type'] ?? '—') ?></p>
                    </div>
                    <div class="vital-box bleeding">
                        <span class="vital-label">Bleed</span>
                        <p class="vital-value text-lg">
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
            </div>

            <!-- Physician Card -->
            <div class="card animate-fade-in" style="animation-delay: 0.1s">
                <div class="card-header">
                    <i class="fas fa-user-md text-indigo-600"></i>
                    <span class="card-title">Attending Physician</span>
                </div>
                <div class="space-y-3">
                    <div>
                        <span class="info-label">Name</span>
                        <p class="info-value"><?= esc($medical_history['physician_name'] ?? '—') ?></p>
                    </div>
                    <div>
                        <span class="info-label">Specialty</span>
                        <p class="info-value"><?= esc($medical_history['physician_specialty'] ?? '—') ?></p>
                    </div>
                    <div>
                        <span class="info-label">Clinic Address</span>
                        <p class="info-value text-sm"><?= esc($medical_history['physician_address'] ?? '—') ?></p>
                    </div>
                    <div>
                        <span class="info-label">Contact</span>
                        <p class="info-value font-mono text-sm"><?= esc($medical_history['physician_phone'] ?? '—') ?></p>
                    </div>
                </div>
            </div>

            <!-- Insurance Card -->
            <div class="card animate-fade-in" style="animation-delay: 0.2s">
                <div class="card-header">
                    <i class="fas fa-shield-alt text-green-600"></i>
                    <span class="card-title">Insurance</span>
                </div>
                <?php if ($patient['has_insurance']): ?>
                    <div class="space-y-3">
                        <div>
                            <span class="info-label">Provider</span>
                            <p class="info-value"><?= esc($patient['insurance_provider']) ?></p>
                        </div>
                        <div>
                            <span class="info-label">Policy #</span>
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

        <!-- ===== RIGHT CONTENT (65%) ===== -->
        <div class="xl:col-span-8 space-y-6">

            <!-- Health Questionnaire -->
            <div class="card animate-fade-in border-t-4 border-t-blue-600">
                <div class="card-header">
                    <i class="fas fa-clipboard-list text-blue-600"></i>
                    <span class="card-title">Health Questionnaire</span>
                </div>
                <div class="space-y-2">
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
                <div class="bg-pink-50/50 rounded-xl p-4 border border-pink-100 animate-fade-in" style="animation-delay: 0.1s">
                    <span class="card-subtitle text-pink-600 block mb-3">Women's Health</span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
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

            <!-- ===== ALLERGIES & CONDITIONS (Using Controller Data) ===== -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- 🔸 Allergies Card (Amber Theme) -->
                <div class="bg-amber-50/30 rounded-xl p-5 border border-amber-100 animate-fade-in">
                    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-amber-100">
                        <i class="fas fa-exclamation-triangle text-amber-500"></i>
                        <span class="font-bold text-amber-800 text-sm">Known Allergies</span>
                    </div>

                    <?php if (!empty($allergies_list)): ?>
                        <?php foreach ($allergies_list as $allergy): ?>
                            <div class="flex items-center gap-2.5 py-2">
                                <div class="w-5 h-5 rounded bg-amber-500 border border-amber-500 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-check text-white text-[10px]"></i>
                                </div>
                                <span class="text-sm font-semibold text-amber-900"><?= esc($allergy) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-amber-400/80 text-sm italic text-center py-4">No allergies recorded</p>
                    <?php endif; ?>

                    <?php if (!empty($other_allergy)): ?>
                        <div class="mt-4 pt-3 border-t border-amber-200">
                            <span class="text-xs font-semibold text-amber-700 block mb-2">Other:</span>
                            <p class="text-sm font-medium text-amber-900 bg-amber-100 px-3 py-2 rounded-lg">
                                <?= esc($other_allergy) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 🔸 Medical Conditions Card (Slate Theme) -->
                <div class="bg-slate-50 rounded-xl p-5 border border-slate-200 animate-fade-in" style="animation-delay: 0.1s">
                    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-200">
                        <i class="fas fa-notes-medical text-blue-500"></i>
                        <span class="font-bold text-slate-700 text-sm">Medical Conditions</span>
                    </div>

                    <?php if (!empty($conditions_list)): ?>
                        <div class="space-y-4 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                            <?php
                            // Group conditions by category (from medical_conditions table)
                            $grouped = [];
                            foreach ($conditions_list as $condition) {
                                // Extract category from condition label or default to 'Other'
                                $category = 'Other'; // Default fallback
                                $grouped[$category][] = $condition;
                            }
                            ?>
                            <?php foreach ($grouped as $category => $items): ?>
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">
                                        <?= esc($category) ?>
                                    </span>
                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach ($items as $item): ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-700">
                                                <i class="fas fa-circle text-[3px] text-blue-400"></i>
                                                <?= esc($item) ?>
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

            <!-- 🔹 SIDE-BY-SIDE: Clinical Notes + Dental Chart -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Clinical Notes -->
                <div class="card animate-fade-in border-t-4 border-t-blue-600">
                    <div class="card-header">
                        <i class="fas fa-stethoscope text-blue-600"></i>
                        <span class="card-title">Clinical Notes</span>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <span class="info-label">Chief Complaint</span>
                            <p class="info-value textarea"><?= esc($medical_history['chief_complaint'] ?? '—') ?></p>
                        </div>
                        <div>
                            <span class="info-label">Diagnosis Notes</span>
                            <p class="info-value textarea"><?= esc($medical_history['diagnosis_notes'] ?? '—') ?></p>
                        </div>
                        <div>
                            <span class="info-label">Treatment Plan</span>
                            <p class="info-value textarea"><?= esc($medical_history['treatment_plan_notes'] ?? '—') ?></p>
                        </div>
                    </div>
                </div>

                <!-- Dental Chart -->
                <div class="card animate-fade-in border-t-4 border-t-blue-600" style="animation-delay: 0.1s">
                    <div class="card-header">
                        <i class="fas fa-tooth text-blue-600"></i>
                        <span class="card-title">Dental Chart</span>
                    </div>
                    <?php if (!empty($medical_history['dental_chart_path'])): ?>
                        <div class="dental-chart-container group cursor-pointer" onclick="this.querySelector('img').requestFullscreen?.()">
                            <img src="<?= base_url($medical_history['dental_chart_path']) ?>"
                                alt="Dental Chart for <?= esc($patient['first_name']) ?>"
                                class="rounded-lg shadow-sm"
                                onerror="this.parentElement.innerHTML='<div class=\'dental-chart-placeholder\'><i class=\'fas fa-image\'></i><p class=\'text-sm\'>Image failed to load</p></div>'">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center pointer-events-none">
                                <i class="fas fa-expand text-white/80 text-2xl opacity-0 group-hover:opacity-100 transition-opacity"></i>
                            </div>
                        </div>
                        <div class="mt-3 text-center">
                            <a href="<?= base_url($medical_history['dental_chart_path']) ?>"
                                target="_blank"
                                class="text-blue-600 text-sm font-semibold hover:text-blue-700 inline-flex items-center gap-1">
                                <i class="fas fa-external-link-alt text-[10px]"></i> Open in New Tab
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="dental-chart-placeholder">
                            <i class="fas fa-file-medical"></i>
                            <p class="text-sm font-medium">No Dental Chart Uploaded</p>
                            <p class="text-xs text-slate-400 mt-1">Upload during treatment session</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
    // Fade-in animation on load
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.animate-fade-in').forEach((el, i) => {
            setTimeout(() => {
                el.style.animationDelay = (i * 0.05) + 's';
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, 50);
        });
    });
</script>

<?= $this->endSection() ?>