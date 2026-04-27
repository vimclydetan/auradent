<?= $this->extend('layouts/dashboard_layout') ?>
<?= $this->section('content') ?>
<?php

use App\Constants\AppointmentStatus; ?>
<style>
    /* Smooth modal transitions */
    @keyframes scaleIn {
        from {
            transform: scale(0.97);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .animate-scale-in {
        animation: scaleIn 0.2s ease-out forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fadeIn 0.25s ease-out forwards;
    }

    /* Canvas container optimization */
    #dentalCanvas {
        display: block;
        image-rendering: -webkit-optimize-contrast;
        image-rendering: crisp-edges;
    }

    /* Button loading state */
    .btn-loading {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Responsive table */
    @media (max-width: 640px) {
        table thead {
            display: none;
        }

        table tbody tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1rem;
        }

        table tbody td {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0 !important;
            border: none !important;
            font-size: 0.875rem;
        }

        table tbody td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #64748b;
            margin-right: 1rem;
        }
    }

    /* Smooth hover effect */
    button[class*="bg-"]:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
</style>

<!-- jQuery + Local fallback -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<!-- Alerts -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="mb-4 p-4 bg-blue-50 text-blue-800 rounded-lg border border-blue-200 shadow-sm flex items-start gap-3 animate-fade-in">
        <i class="fas fa-check-circle mt-0.5 text-blue-600"></i>
        <span class="text-sm font-medium"><?= session()->getFlashdata('success') ?></span>
        <button onclick="this.parentElement.remove()" class="ml-auto text-blue-400 hover:text-blue-600">&times;</button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="mb-4 p-4 bg-red-50 text-red-800 rounded-lg border border-red-200 shadow-sm flex items-start gap-3 animate-fade-in">
        <i class="fas fa-exclamation-circle mt-0.5 text-red-600"></i>
        <span class="text-sm font-medium"><?= session()->getFlashdata('error') ?></span>
        <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600">&times;</button>
    </div>
<?php endif; ?>

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
    <h3 class="text-xl sm:text-2xl font-bold text-slate-800 flex items-center gap-2">
        <i class="fas fa-clipboard-list text-blue-600"></i>
        My Assigned Appointments
    </h3>
    <span class="bg-white px-4 py-2 rounded-lg shadow-sm border border-slate-200 text-sm font-semibold text-slate-600">
        <?= date('F d, Y') ?>
    </span>
</div>

<!-- Appointments Table -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 text-slate-600 text-[10px] uppercase font-bold tracking-wider">
                <tr>
                    <th class="p-4 border-b">Patient</th>
                    <th class="p-4 border-b">Schedule</th>
                    <th class="p-4 border-b">Service</th>
                    <th class="p-4 border-b text-center">Status</th>
                    <th class="p-4 border-b text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="appointmentTable" class="text-sm divide-y divide-slate-100">
                <?php if (empty($mySchedule)): ?>
                    <tr>
                        <td colspan="5" class="p-10 text-center">
                            <div class="flex flex-col items-center gap-3 text-slate-400">
                                <i class="fas fa-calendar-check text-3xl"></i>
                                <p class="font-medium">No appointments assigned to you today.</p>
                                <p class="text-[10px] text-slate-500 max-w-xs text-center">
                                    Appointments will appear here once the receptionist assigns them to you.
                                </p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($mySchedule as $a):
                        $status = $a['status'] ?? 'Pending';
                        $color = AppointmentStatus::color($status);
                        $icon = AppointmentStatus::icon($status);
                        $label = AppointmentStatus::label($status);
                        $isFinal = AppointmentStatus::isFinalState($status);
                        $isToday = $a['appointment_date'] == date('Y-m-d');
                        $startTime = !empty($a['appointment_time']) ? date('h:i A', strtotime($a['appointment_time'])) : '--:--';
                        $endTime = !empty($a['end_time']) ? date('h:i A', strtotime($a['end_time'])) : '--:--';
                        $scheduleDisplay = "{$startTime} - {$endTime}";
                    ?>
                        <tr class="appt-row hover:bg-slate-50/50 transition-colors group"
                            data-status="<?= esc($a['status']) ?>"
                            data-date="<?= esc($a['appointment_date']) ?>">

                            <!-- PATIENT -->
                            <td class="p-4">
                                <div class="font-semibold text-slate-700 group-hover:text-blue-700 transition-colors">
                                    <?= esc($a['patient_name']) ?>
                                </div>
                                <?php if (!empty($a['patient_code'])): ?>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                        <i class="fas fa-id-card mr-1"></i><?= esc($a['patient_code']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- SCHEDULE -->
                            <td class="p-4">
                                <?php if ($isToday): ?>
                                    <span class="inline-flex items-center gap-1 text-[9px] font-black text-red-600 bg-red-50 px-1.5 py-0.5 rounded-full mb-1.5 animate-pulse">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>TODAY
                                    </span>
                                <?php endif; ?>
                                <div class="text-xs font-semibold text-slate-700">
                                    <?= date('M d, Y', strtotime($a['appointment_date'])) ?>
                                </div>
                                <div class="text-[10px] font-mono text-blue-600 font-bold mt-0.5">
                                    <i class="fas fa-clock mr-1"></i><?= $scheduleDisplay ?>
                                </div>
                            </td>

                            <!-- SERVICE -->
                            <td class="p-4">
                                <div class="font-medium text-slate-600">
                                    <?= esc($a['service_name'] ?? 'General Consultation') ?>
                                </div>
                                <?php if (!empty($a['service_notes'])): ?>
                                    <div class="text-[10px] text-slate-400 italic mt-0.5 line-clamp-1 max-w-[150px]">
                                        <?= esc($a['service_notes']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- STATUS -->
                            <td class="p-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[9px] font-bold uppercase bg-<?= $color ?>-100 text-<?= $color ?>-700 inline-flex items-center gap-1">
                                    <i class="fas <?= $icon ?>"></i>
                                    <?= esc($label) ?>
                                </span>
                            </td>
                            <!-- ACTIONS -->
                            <td class="p-3 text-center">
                                <div class="flex justify-center gap-1 flex-wrap">
                                    <a href="<?= base_url('dentist/patient/medical_history/' . ($a['patient_id'] ?? '#')) ?>"
                                        class="bg-slate-300 text-slate-800 px-2 py-1.5 rounded-md font-bold text-[9px] uppercase hover:bg-slate-600 hover:text-white transition flex items-center gap-1"
                                        title="View Medical History">
                                        <i class="fas fa-history text-[8px]"></i> <span class="hidden xl:inline">History</span>
                                    </a>
                                    <button onclick="openChartModal(<?= $a['patient_id'] ?? 0 ?>)"
                                        class="bg-blue-700 text-white px-2 py-1.5 rounded-md font-bold text-[9px] uppercase hover:bg-blue-800 transition flex items-center gap-1"
                                        title="Open Dental Chart">
                                        <i class="fas fa-tooth text-[8px]"></i> <span class="hidden xl:inline">Chart</span>
                                    </button>
                                    <?php if (($a['status'] ?? '') !== 'Completed'): ?>
                                        <button onclick="openTreatmentModal(<?= $a['id'] ?? 0 ?>, '<?= esc(addslashes($a['patient_name'])) ?>', '<?= esc($a['patient_code'] ?? '') ?>')"
                                            class="bg-green-600 text-white px-2 py-1.5 rounded-md font-bold text-[9px] uppercase hover:bg-green-800 transition flex items-center gap-1"
                                            title="Finalize Treatment">
                                            <i class="fas fa-check-circle text-[8px]"></i> <span class="hidden xl:inline">Finalize</span>
                                        </button>
                                    <?php else: ?>
                                        <span class="bg-green-50 text-green-700 border border-green-200 px-2 py-1.5 rounded-md font-bold text-[9px] uppercase inline-flex items-center gap-1" title="Completed">
                                            <i class="fas fa-flag-checkered text-[8px]"></i> <span class="hidden xl:inline">Done</span>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Table Footer -->
    <?php if (!empty($mySchedule)): ?>
        <div class="px-4 py-3 bg-slate-50 border-t border-slate-200 text-[10px] text-slate-500 flex flex-wrap justify-between items-center gap-2">
            <span><i class="fas fa-list mr-1"></i> Showing <strong><?= count($mySchedule) ?></strong> appointment<?= count($mySchedule) > 1 ? 's' : '' ?> for today</span>
            <div class="flex items-center gap-3">
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span> Today</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-400"></span> Pending</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500"></span> Completed</span>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- ========================================== -->
<!-- 🦷 CHART MODAL (BLUE THEME) -->
<!-- ========================================== -->
<div id="chartModal" class="hidden fixed inset-0 bg-slate-900/70 flex items-center justify-center p-2 sm:p-4 z-[70]">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[95vh] flex flex-col overflow-hidden border border-slate-200">
        <!-- Modal Header -->
        <div class="px-4 py-3 bg-gradient-to-r from-slate-800 to-slate-700 text-white flex justify-between items-center shrink-0">
            <div class="flex items-center gap-3">
                <i class="fas fa-tooth text-blue-400"></i>
                <h4 class="font-bold text-sm sm:text-base">Clinical Charting</h4>
            </div>
            <div class="flex items-center gap-2">
                <div class="flex bg-slate-600/50 rounded-lg p-0.5">
                    <button type="button" onclick="setTool('pen')" id="btn-pen"
                        class="tool-btn px-3 py-1.5 rounded text-[10px] font-bold uppercase flex items-center gap-1 transition bg-blue-500 text-white">
                        <i class="fas fa-pen"></i> <span class="hidden sm:inline">Pen</span>
                    </button>
                    <button type="button" onclick="setTool('eraser')" id="btn-eraser"
                        class="tool-btn px-3 py-1.5 rounded text-[10px] font-bold uppercase flex items-center gap-1 transition text-slate-200 hover:text-white">
                        <i class="fas fa-eraser"></i> <span class="hidden sm:inline">Eraser</span>
                    </button>
                </div>
                <button onclick="clearCanvas()" class="text-[10px] font-bold uppercase px-3 py-1.5 rounded bg-red-500/20 text-red-200 hover:bg-red-500/40 transition flex items-center gap-1">
                    <i class="fas fa-trash-alt"></i> <span class="hidden sm:inline">Clear</span>
                </button>
                <button onclick="closeChartModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/10 transition text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <form action="<?= base_url('dentist/appointments/save-chart') ?>" method="POST" id="chartForm" class="flex-1 overflow-y-auto p-4 space-y-4" style="max-height: calc(95vh - 80px);">
            <?= csrf_field() ?>
            <input type="hidden" name="patient_id" id="chart_patient_id">
            <input type="hidden" name="drawing_data" id="drawing_data">

            <!-- Canvas Area -->
            <div class="bg-slate-50 rounded-xl border border-slate-200 p-3">
                <label class="text-[10px] font-black text-slate-500 uppercase mb-2 block flex items-center gap-2">
                    <i class="fas fa-image"></i> Dental Chart Canvas
                </label>
                <div class="relative w-full bg-white rounded-lg border border-slate-200 overflow-hidden" style="height: 450px;">
                    <canvas id="bgCanvas" class="absolute inset-0 w-full h-full block" style="image-rendering: -webkit-optimize-contrast;"></canvas>
                    <canvas id="dentalCanvas" class="absolute inset-0 w-full h-full cursor-crosshair touch-none block" style="image-rendering: crisp-edges;"></canvas>
                </div>
                <p class="text-[10px] text-slate-400 mt-2 text-center">
                    <i class="fas fa-info-circle"></i> Draw directly on the chart. Use mouse or touch.
                </p>
            </div>

            <!-- Clinical Notes Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wide flex items-center gap-1">
                        <i class="fas fa-comment-medical text-blue-500"></i> Chief Complaint
                    </label>
                    <textarea name="complaint" rows="3" class="w-full p-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none transition" placeholder="Patient's main concern..."></textarea>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wide flex items-center gap-1">
                        <i class="fas fa-stethoscope text-blue-500"></i> Diagnosis
                    </label>
                    <textarea name="diagnosis" rows="3" class="w-full p-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none transition" placeholder="Clinical findings..."></textarea>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wide flex items-center gap-1">
                        <i class="fas fa-clipboard-list text-blue-500"></i> Treatment Plan
                    </label>
                    <textarea name="treatment_plan" rows="3" class="w-full p-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none transition" placeholder="Next steps & procedures..."></textarea>
                </div>
            </div>

            <!-- Save Button -->
            <button type="submit" id="chartSaveBtn" class="w-full bg-gradient-to-r from-blue-600 to-blue-500 text-white py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider hover:from-blue-500 hover:to-blue-400 transition-all shadow-lg hover:shadow-xl active:scale-[0.99] disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                <span class="btn-text"><i class="fas fa-save mr-1"></i> Save Medical Record</span>
                <span class="btn-loading hidden"><i class="fas fa-spinner fa-spin"></i> Saving...</span>
            </button>
        </form>
    </div>
</div>

<!-- ✅ FINALIZE TREATMENT MODAL -->
<div id="treatmentModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-start justify-center p-4 z-50 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl my-8 overflow-hidden border-2 border-blue-500">
        <div class="p-4 border-b flex justify-between items-center bg-gradient-to-r from-blue-600 to-blue-500 text-white">
            <h4 class="font-bold text-sm uppercase tracking-widest flex items-center gap-2">
                <i class="fas fa-check-circle"></i> Finalize Treatment
            </h4>
            <button onclick="closeTreatmentModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/10 transition">&times;</button>
        </div>
        <form action="<?= base_url('dentist/appointments/finalize') ?>" method="POST" class="p-5 space-y-6">
            <?= csrf_field() ?>
            <input type="hidden" name="appointment_id" id="t_appointment_id">

            <!-- Patient Info -->
            <div class="bg-gradient-to-r from-blue-50 to-slate-50 p-4 rounded-xl border border-blue-200">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase">Patient Code</p>
                        <h5 id="t_patient_code" class="text-sm font-bold text-slate-800 font-mono">--</h5>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase">Patient Name</p>
                        <h5 id="t_patient_name" class="text-base font-bold text-slate-800">--</h5>
                    </div>
                </div>
            </div>

            <!-- Services Section -->
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700 uppercase">Procedures Performed</label>
                        <p class="text-[10px] text-slate-500">Confirm or adjust the services provided to the patient.</p>
                    </div>
                    <button type="button" onclick="addTreatmentRow()" class="text-[10px] bg-blue-600 text-white px-3 py-1.5 rounded-md hover:bg-blue-700 font-bold uppercase transition flex items-center gap-1">
                        <i class="fas fa-plus"></i> Add Service
                    </button>
                </div>

                <div id="t_services_container" class="space-y-2">
                    <!-- Template Row (Hidden & Disabled) -->
                    <div class="t-service-row flex gap-2 items-start bg-slate-50 p-3 border border-slate-200 rounded-xl hidden" id="t_row_template">
                        <div class="flex-1">
                            <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1">Service Type</label>
                            <select name="services[]" onchange="checkLevels(this)" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" required disabled>
                                <option value="">-- Select Procedure --</option>
                                <?php foreach ($services as $s): ?>
                                    <option value="<?= $s['id'] ?>" data-haslevels="<?= $s['has_levels'] ?>">
                                        <?= esc($s['service_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="flex-1 level-div hidden">
                            <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1">Severity Level</label>
                            <select name="levels[]" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20" disabled>
                                <option value="Standard">Standard</option>
                                <option value="Moderate">Moderate</option>
                                <option value="Severe">Severe</option>
                            </select>
                        </div>
                        <div class="pt-5">
                            <button type="button" onclick="removeTreatmentRow(this)" class="text-red-400 hover:text-red-600 p-2 transition" title="Remove">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-500 text-white py-4 rounded-xl font-bold uppercase tracking-wider hover:from-blue-500 hover:to-blue-400 transition-all shadow-lg hover:shadow-blue-500/30 active:scale-[0.99] flex items-center justify-center gap-2">
                    <i class="fas fa-check-circle"></i> Complete Appointment
                </button>
                <p class="text-center text-[10px] text-slate-400 mt-3 italic">
                    Note: Clicking complete will update the patient record and notify the receptionist for billing.
                </p>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- 🚀 JAVASCRIPT -->
<!-- ========================================== -->
<script>
    // ===== TWO-CANVAS SYSTEM =====
    const canvas = document.getElementById('dentalCanvas');
    const ctx = canvas.getContext('2d', {
        alpha: true,
        desynchronized: true
    });
    const bgCanvas = document.getElementById('bgCanvas');
    const bgCtx = bgCanvas.getContext('2d', {
        desynchronized: true
    });

    const DRAW_W = 1310;
    const DRAW_H = 816;
    let drawing = false,
        currentTool = 'pen',
        lastX = 0,
        lastY = 0;
    let animationFrame = null,
        pendingDraw = false;

    const chartImage = new Image();
    chartImage.src = '<?= base_url("/assets/images/chart/image.png") ?>';
    chartImage.crossOrigin = "anonymous";

    function initCanvas() {
        canvas.width = DRAW_W;
        canvas.height = DRAW_H;
        bgCanvas.width = DRAW_W;
        bgCanvas.height = DRAW_H;
        canvas.style.width = '100%';
        canvas.style.height = '100%';
        bgCanvas.style.width = '100%';
        bgCanvas.style.height = '100%';
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.lineWidth = 3;
        ctx.strokeStyle = 'rgba(37, 99, 235, 0.95)'; // Blue ink
        drawBackgroundImage();
        ctx.clearRect(0, 0, DRAW_W, DRAW_H);
    }

    function drawBackgroundImage() {
        bgCtx.clearRect(0, 0, DRAW_W, DRAW_H);
        bgCtx.fillStyle = '#ffffff';
        bgCtx.fillRect(0, 0, DRAW_W, DRAW_H);
        if (chartImage.complete && chartImage.naturalWidth > 0) {
            bgCtx.drawImage(chartImage, 0, 0, DRAW_W, DRAW_H);
        }
    }

    function redrawChart() {
        ctx.clearRect(0, 0, DRAW_W, DRAW_H);
    }

    chartImage.onload = function() {
        initCanvas();
    };
    chartImage.onerror = function() {
        console.error('Failed to load chart image:', chartImage.src);
    };
    if (chartImage.complete && chartImage.naturalWidth > 0) {
        initCanvas();
    }

    function getCoords(e) {
        const rect = canvas.getBoundingClientRect();
        const scaleX = DRAW_W / rect.width,
            scaleY = DRAW_H / rect.height;
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        return {
            x: Math.max(0, Math.min(DRAW_W, (clientX - rect.left) * scaleX)),
            y: Math.max(0, Math.min(DRAW_H, (clientY - rect.top) * scaleY))
        };
    }

    function startDrawing(e) {
        e.preventDefault();
        drawing = true;
        const coords = getCoords(e);
        lastX = coords.x;
        lastY = coords.y;
        ctx.beginPath();
        ctx.moveTo(lastX, lastY);
    }

    function queueDraw(e) {
        if (!drawing) return;
        e.preventDefault();
        if (!pendingDraw) {
            pendingDraw = true;
            animationFrame = requestAnimationFrame(() => {
                doDraw(e);
                pendingDraw = false;
            });
        }
    }

    function doDraw(e) {
        const coords = getCoords(e);
        if (currentTool === 'eraser') {
            ctx.globalCompositeOperation = 'destination-out';
            ctx.lineWidth = 25;
            ctx.strokeStyle = 'rgba(0,0,0,1)';
        } else {
            ctx.globalCompositeOperation = 'source-over';
            ctx.lineWidth = 3;
            ctx.strokeStyle = 'rgba(37, 99, 235, 0.95)';
        }
        ctx.lineTo(coords.x, coords.y);
        ctx.stroke();
        lastX = coords.x;
        lastY = coords.y;
        ctx.beginPath();
        ctx.moveTo(lastX, lastY);
    }

    function stopDrawing(e) {
        if (!drawing) return;
        drawing = false;
        if (animationFrame) {
            cancelAnimationFrame(animationFrame);
            animationFrame = null;
        }
        if (pendingDraw) {
            doDraw(e);
            pendingDraw = false;
        }
        ctx.closePath();
        ctx.globalCompositeOperation = 'source-over';
    }

    function setTool(tool) {
        currentTool = tool;
        document.querySelectorAll('.tool-btn').forEach(btn => {
            btn.classList.remove('bg-blue-500', 'text-white');
            btn.classList.add('text-slate-200');
        });
        const activeBtn = document.getElementById(`btn-${tool}`);
        if (activeBtn) {
            activeBtn.classList.add('bg-blue-500', 'text-white');
            activeBtn.classList.remove('text-slate-200');
        }
    }

    function bindCanvasEvents() {
        canvas.addEventListener('mousedown', startDrawing, {
            passive: false
        });
        canvas.addEventListener('mousemove', queueDraw, {
            passive: false
        });
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);
        canvas.addEventListener('touchstart', startDrawing, {
            passive: false
        });
        canvas.addEventListener('touchmove', queueDraw, {
            passive: false
        });
        canvas.addEventListener('touchend', stopDrawing);
        canvas.addEventListener('touchcancel', stopDrawing);
        canvas.addEventListener('contextmenu', e => e.preventDefault());
    }

    function openChartModal(patientId) {
        document.getElementById('chart_patient_id').value = patientId;
        document.getElementById('chartModal').classList.remove('hidden');
        setTimeout(() => {
            initCanvas();
            bindCanvasEvents();
        }, 100);
    }

    function closeChartModal() {
        document.getElementById('chartModal').classList.add('hidden');
    }

    function clearCanvas() {
        if (confirm('Clear all markings from this chart?')) {
            redrawChart();
        }
    }

    // ===== AJAX SUBMIT =====
    document.getElementById('chartForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const $btn = document.getElementById('chartSaveBtn');
        const btnText = $btn.querySelector('.btn-text');
        const btnLoading = $btn.querySelector('.btn-loading');
        $btn.disabled = true;
        btnText.classList.add('hidden');
        btnLoading.classList.remove('hidden');

        const exportCanvas = document.createElement('canvas');
        const exportCtx = exportCanvas.getContext('2d');
        exportCanvas.width = DRAW_W;
        exportCanvas.height = DRAW_H;
        exportCtx.drawImage(bgCanvas, 0, 0, DRAW_W, DRAW_H);
        exportCtx.drawImage(canvas, 0, 0, DRAW_W, DRAW_H);
        const dataURL = exportCanvas.toDataURL('image/jpeg', 0.85);
        document.getElementById('drawing_data').value = dataURL;

        const formData = new FormData(this);
        fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json().catch(() => ({
                success: false,
                message: 'Invalid response'
            })))
            .then(data => {
                if (data?.success) {
                    closeChartModal();
                    location.reload();
                } else {
                    alert('⚠️ ' + (data?.message || 'Save failed. Please try again.'));
                }
            })
            .catch(error => {
                console.error('Save error:', error);
                alert('❌ Connection error. Please check your internet.');
            })
            .finally(() => {
                $btn.disabled = false;
                btnText.classList.remove('hidden');
                btnLoading.classList.add('hidden');
            });
    });

    // ===== TREATMENT MODAL =====
    function openTreatmentModal(id, patientName, patientCode = '') {
        // 1. Set basic info
        $('#t_appointment_id').val(id);
        $('#t_patient_name').text(patientName);
        $('#t_patient_code').text(patientCode || '--');

        // 2. Linisin muna ang container (tanggalin yung mga lumang rows)
        $('#t_services_container').find('.t-service-row:not(#t_row_template)').remove();

        // 3. Kunin ang data via AJAX
        $.get('<?= base_url('dentist/appointments/get-data/') ?>' + id, function(data) {
            // Kung may existing services na naka-attach sa appointment:
            if (data.services && data.services.length > 0) {
                data.services.forEach(svc => {
                    addTreatmentRow(svc.service_id, svc.service_level);
                });
            } else {
                // Kung wala (back-up), maglagay ng isang blankong row
                addTreatmentRow();
            }
            $('#treatmentModal').removeClass('hidden');
        }).fail(function() {
            alert("Failed to load appointment services.");
            addTreatmentRow();
            $('#treatmentModal').removeClass('hidden');
        });
    }

    function closeTreatmentModal() {
        $('#treatmentModal').addClass('hidden');
    }

    function addTreatmentRow(serviceId = '', level = '') {
        // 1. I-clone ang template
        const $row = $('#t_row_template').clone().removeAttr('id').removeClass('hidden');

        // 2. I-enable ang mga select elements sa loob ng CLONED row lang
        $row.find('select').prop('disabled', false);

        // 3. Kung may data na galing sa database (Auto-populate)
        if (serviceId) {
            const $svcSelect = $row.find('select[name="services[]"]');
            $svcSelect.val(serviceId);

            // I-check kung may levels (Moderate/Severe)
            const hasLevels = $svcSelect.find(':selected').data('haslevels') == '1';
            if (hasLevels) {
                $row.find('.level-div').removeClass('hidden');
                $row.find('select[name="levels[]"]').val(level || 'Standard').prop('disabled', false);
            } else {
                // Kung walang levels, i-disable yung level select para malinis ang POST data
                $row.find('select[name="levels[]"]').prop('disabled', true);
            }
        }

        // 4. I-append sa container
        $('#t_services_container').append($row);
    }


    function removeTreatmentRow(btn) {
        if ($('.t-service-row:not(#t_row_template)').length > 1) {
            $(btn).closest('.t-service-row').remove();
        } else {
            alert('At least one procedure is required.');
        }
    }

    function checkLevels(sel) {
        const hasLevels = $(sel).find(':selected').data('haslevels') == '1';
        $(sel).closest('.t-service-row').find('.level-div').toggleClass('hidden', !hasLevels);
        $(sel).closest('.t-service-row').find('select[name="levels[]"]').prop('disabled', !hasLevels);
    }

    // Mobile table labels
    const headers = ['Patient', 'Schedule', 'Service', 'Status', 'Actions'];
    document.querySelectorAll('#appointmentTable tr').forEach(row => {
        row.querySelectorAll('td').forEach((td, i) => {
            if (headers[i]) td.setAttribute('data-label', headers[i]);
        });
    });
</script>
<?= $this->endSection() ?>