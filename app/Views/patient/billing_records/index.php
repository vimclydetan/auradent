<?= $this->extend('layouts/patient_layout') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- HEADER & ACTIONS -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 print:hidden">
        <div>
            <nav class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">
                <a href="<?= base_url('patient/dashboard') ?>" class="hover:text-blue-600 transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Treatment Records</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">My Treatment History</h1>
            <p class="text-slate-500 text-sm mt-1">Track your dental visits, procedures, and payment status.</p>
        </div>
        <button onclick="window.print()" class="bg-white border border-slate-200 text-slate-600 px-4 py-2.5 rounded-xl font-bold text-[10px] uppercase hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm tracking-widest">
            <i class="fas fa-print"></i> Print Record
        </button>
    </div>

    <!-- QUICK SUMMARY CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Total Visits -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Total Visits</p>
                <p class="text-3xl font-black text-slate-800"><?= count($records ?? []) ?></p>
            </div>
            <div class="h-12 w-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>

        <!-- Total Paid -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[9px] font-black text-green-500 uppercase tracking-[0.2em] mb-1">Total Paid</p>
                <p class="text-3xl font-black text-green-600">₱<?= number_format($total_paid ?? 0, 2) ?></p>
            </div>
            <div class="h-12 w-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center text-xl">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>

        <!-- Outstanding Balance -->
        <div class="bg-white p-6 rounded-2xl border <?= ($total_balance ?? 0) > 0 ? 'border-red-200 bg-red-50/30' : 'border-slate-200' ?> shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[9px] font-black <?= ($total_balance ?? 0) > 0 ? 'text-red-500' : 'text-slate-400' ?> uppercase tracking-[0.2em] mb-1">Outstanding Balance</p>
                <p class="text-3xl font-black <?= ($total_balance ?? 0) > 0 ? 'text-red-600' : 'text-slate-300' ?>">
                    ₱<?= number_format($total_balance ?? 0, 2) ?>
                </p>
            </div>
            <div class="h-12 w-12 <?= ($total_balance ?? 0) > 0 ? 'bg-red-50 text-red-500' : 'bg-slate-50 text-slate-300' ?> rounded-xl flex items-center justify-center text-xl">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
    </div>

    <!-- RECORDS TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em] italic">Visit Log</h3>
            <span class="text-[10px] font-bold text-slate-400 uppercase"><?= count($records ?? []) ?> record(s)</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 text-slate-500 text-[10px] uppercase font-black tracking-[0.15em]">
                    <tr>
                        <th class="px-5 py-4">Date & Time</th>
                        <th class="px-5 py-4">Procedure</th>
                        <th class="px-5 py-4">Dentist</th>
                        <th class="px-5 py-4 text-center">Tooth #</th>
                        <th class="px-5 py-4 text-right">Amount</th>
                        <th class="px-5 py-4 text-center">Status</th>
                        <th class="px-5 py-4 text-center">Consent</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    <?php if (empty($records)): ?>
                        <tr>
                            <td colspan="7" class="px-5 py-16 text-center text-slate-400">
                                <i class="fas fa-clipboard-list text-4xl mb-3 block opacity-20"></i>
                                <p class="font-bold uppercase tracking-widest text-xs">No treatment records yet.</p>
                                <p class="text-[10px] mt-1 opacity-70">Your dental history will appear here after your first visit.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($records as $r): ?>
                            <tr class="hover:bg-slate-50/70 transition-colors group">
                                <!-- Date -->
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-800 text-sm"><?= date('M d, Y', strtotime($r['treatment_date'])) ?></div>
                                    <div class="text-[10px] text-slate-400 font-medium uppercase tracking-tighter"><?= date('h:i A', strtotime($r['treatment_date'])) ?></div>
                                </td>

                                <!-- Procedure -->
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 text-[10px] font-black uppercase border border-blue-100">
                                        <?= esc($r['service_name'] ?? 'Service #'.($r['service_id'] ?? '')) ?>
                                    </span>
                                </td>

                                <!-- Dentist -->
                                <td class="px-5 py-4">
                                    <span class="font-medium text-slate-700 text-sm"><?= esc($r['dentist_name'] ?? 'Dr. ' . ($r['dentist_id'] ?? 'TBD')) ?></span>
                                </td>

                                <!-- Tooth -->
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-600 font-black text-xs border border-slate-200">
                                        <?= esc($r['tooth_number'] ?: '--') ?>
                                    </span>
                                </td>

                                <!-- Amount -->
                                <td class="px-5 py-4 text-right">
                                    <div class="font-bold text-slate-700">₱<?= number_format($r['amount_charge'] ?? 0, 2) ?></div>
                                    <div class="text-[10px] text-slate-400">Paid: ₱<?= number_format($r['amount_paid'] ?? 0, 2) ?></div>
                                </td>

                                <!-- Payment Status -->
                                <td class="px-5 py-4 text-center">
                                    <?php 
                                        $balance = $r['balance'] ?? 0;
                                        $paid = $r['amount_paid'] ?? 0;
                                    ?>
                                    <?php if ($balance <= 0): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-green-50 text-green-700 text-[10px] font-bold border border-green-100">
                                            <i class="fas fa-check"></i> Paid
                                        </span>
                                    <?php elseif ($paid > 0): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-amber-50 text-amber-700 text-[10px] font-bold border border-amber-100">
                                            <i class="fas fa-clock"></i> Partial
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-red-50 text-red-700 text-[10px] font-bold border border-red-100">
                                            <i class="fas fa-exclamation"></i> Unpaid
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Consent -->
                                <td class="px-5 py-4 text-center">
                                    <?php if ($r['consent_given'] ?? 0): ?>
                                        <div class="flex flex-col items-center gap-1">
                                            <i class="fas fa-file-signature text-green-500 text-sm"></i>
                                            <?php if (!empty($r['signature_path'])): ?>
                                                <a href="<?= base_url('uploads/signatures/'.$r['signature_path']) ?>" target="_blank" class="text-[10px] font-bold text-blue-500 hover:underline">View</a>
                                            <?php else: ?>
                                                <span class="text-[10px] text-slate-400">Signed</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-[10px] text-slate-400 font-medium">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- PRINT STYLES -->
<style>
    @media print {
        .print\:hidden { display: none !important; }
        body { background: #fff !important; padding: 0 !important; }
        .max-w-7xl { max-width: 100% !important; padding: 0 !important; }
        .bg-white, .shadow-sm, .border { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
        table { page-break-inside: auto; }
        tr { page-break-inside: avoid; }
        thead { display: table-header-group; }
    }
</style>

<?= $this->endSection() ?>