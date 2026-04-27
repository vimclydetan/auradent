<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="min-h-screen bg-slate-50/50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-6xl mx-auto">
        
        <!-- HEADER NAV -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 no-print">
            <a href="<?= base_url('patient/appointments') ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-blue-600 transition-colors group">
                <i class="fas fa-arrow-left text-xs group-hover:-translate-x-1 transition-transform"></i>
                Back to My Appointments
            </a>
            <div class="flex items-center gap-3">
                <span class="text-[10px] font-mono font-bold text-slate-400 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-sm">
                    REF-ID: <?= str_pad($appointment['id'], 6, '0', STR_PAD_LEFT) ?>
                </span>
                <button onclick="window.print()" class="bg-white border border-slate-200 text-slate-600 px-4 py-2 rounded-xl text-xs font-bold hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- LEFT: MAIN DETAILS -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- STATUS ALERT -->
                <?php 
                    $status = strtolower($appointment['status']);
                    $c = \App\Constants\AppointmentStatus::color($status);
                    $statusLabel = ucwords(str_replace('_', ' ', $status));
                ?>
                <div class="bg-white border border-<?= $c ?>-200 rounded-3xl p-6 shadow-sm flex items-center gap-5 relative overflow-hidden">
                    <div class="w-14 h-14 rounded-2xl bg-<?= $c ?>-100 text-<?= $c ?>-600 flex items-center justify-center text-2xl shadow-inner">
                        <i class="fas <?= $status === 'completed' ? 'fa-check-double' : ($status === 'cancelled' ? 'fa-times' : 'fa-clock') ?>"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Current Status</p>
                        <h2 class="text-2xl font-black text-slate-800"><?= $statusLabel ?></h2>
                        <?php if($status === 'pending'): ?>
                            <p class="text-xs text-amber-600 font-medium mt-1 italic">Waiting for clinic confirmation...</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- INFO CARD -->
                <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                    <div class="p-8 border-b border-slate-50 bg-gradient-to-br from-white to-slate-50/50">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <h3 class="text-xs font-black text-blue-500 uppercase tracking-widest mb-2">Procedures & Services</h3>
                                <h1 class="text-xl font-bold text-slate-900"><?= esc($appointment['service_name']) ?></h1>
                                <div class="flex flex-wrap gap-2 mt-3">
                                    <?php foreach($appointment['services_list'] as $s): ?>
                                        <span class="bg-slate-100 text-slate-600 text-[10px] font-bold px-3 py-1 rounded-full border border-slate-200">
                                            <?= esc($s['service_name']) ?> (<?= esc($s['service_level']) ?>)
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="md:text-right">
                                <p class="text-xs font-bold text-slate-400 uppercase">Dentist</p>
                                <p class="text-lg font-bold text-slate-800"><?= esc($appointment['dentist_name']) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg"><i class="far fa-calendar-alt"></i></div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">Date</p>
                                <p class="text-sm font-bold text-slate-800"><?= date('F j, Y (l)', strtotime($appointment['appointment_date'])) ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg"><i class="far fa-clock"></i></div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">Time</p>
                                <p class="text-sm font-bold text-slate-800">
                                    <?= date('h:i A', strtotime($appointment['appointment_time'])) ?> - <?= $appointment['end_time'] ? date('h:i A', strtotime($appointment['end_time'])) : 'TBD' ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ✅ CANCELLATION LOGS -->
                <?php if (!empty($appointment['cancel_history'])): ?>
                <div class="space-y-4">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-history text-[10px]"></i> Cancellation Request History
                    </h3>
                    <?php foreach ($appointment['cancel_history'] as $req): ?>
                        <div class="bg-white border <?= $req['status'] === 'denied' ? 'border-red-100' : 'border-slate-200' ?> rounded-3xl p-6 shadow-sm">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex flex-wrap items-center gap-3">
                                    <?php 
                                        $s = $req['status'];
                                        $color = ($s === 'approved') ? 'green' : (($s === 'denied') ? 'red' : 'amber');
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase bg-<?= $color ?>-50 text-<?= $color ?>-600 border border-<?= $color ?>-100">
                                        <?= ucfirst($s) ?> Request
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-medium">
                                        <?= date('M d, Y @ h:i A', strtotime($req['created_at'])) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Your Reason</p>
                                    <p class="text-sm text-slate-600 italic leading-relaxed">"<?= esc($req['reason']) ?>"</p>
                                </div>
                                <?php if ($s === 'denied'): ?>
                                    <div class="bg-red-50/50 p-4 rounded-2xl border border-red-100">
                                        <p class="text-[9px] font-bold text-red-400 uppercase mb-1">Clinic Response</p>
                                        <p class="text-sm text-red-700 font-semibold italic">
                                            "<?= esc($req['denial_reason'] ?: 'No specific reason provided.') ?>"
                                        </p>
                                        <p class="text-[8px] text-red-400 mt-2 font-bold uppercase">Decided at <?= date('M d, h:i A', strtotime($req['action_at'])) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- RIGHT: SIDEBAR -->
            <div class="lg:col-span-1 space-y-6 no-print">
                
                <!-- ATTEMPTS COUNTER -->
                <div class="bg-slate-900 rounded-3xl p-6 text-white shadow-xl">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Request Limit</p>
                    <div class="flex items-end gap-2 mt-2">
                        <span class="text-4xl font-black"><?= $appointment['cancel_attempts'] ?></span>
                        <span class="text-slate-400 font-bold mb-1">/ 2 used</span>
                    </div>
                    <div class="w-full bg-slate-800 h-1.5 rounded-full mt-4 overflow-hidden">
                        <div class="bg-blue-500 h-full transition-all duration-700" style="width: <?= ($appointment['cancel_attempts'] / 2) * 100 ?>%"></div>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-4 italic">
                        Online cancellation is limited to 2 attempts only.
                    </p>
                </div>

                <!-- HELP BOX -->
                <div class="bg-white border border-slate-200 rounded-3xl p-6">
                    <h4 class="text-xs font-black text-slate-800 uppercase mb-4 tracking-widest">Clinic Contact</h4>
                    <div class="space-y-3">
                        <a href="tel:<?= esc($clinic_phone) ?>" class="flex items-center gap-3 p-3 rounded-2xl bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white transition-all group font-bold text-sm">
                            <i class="fas fa-phone-alt"></i> Call Us
                        </a>
                        <a href="sms:<?= esc($clinic_phone) ?>" class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 text-slate-600 hover:bg-slate-200 transition-all font-bold text-sm">
                            <i class="fas fa-comment-alt"></i> Send SMS
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>