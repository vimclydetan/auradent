<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto space-y-6">

    <!-- BREADCRUMB & ACTIONS -->
    <div class="flex justify-between items-center no-print">
        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400">
            <a href="<?= base_url('receptionist/patients') ?>" class="hover:text-blue-600 transition-all">Patients</a>
            <span>/</span>
            <span class="text-slate-600">Treatment History</span>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('receptionist/patients') ?>" class="bg-white border border-slate-200 text-slate-600 px-4 py-2 rounded-xl font-bold text-[10px] uppercase hover:bg-slate-50 transition-all flex items-center shadow-sm">
                <i class="fas fa-chevron-left mr-2"></i> Back to List
            </a>
            <button onclick="window.print()" class="bg-blue-600 text-white px-5 py-2 rounded-xl font-bold text-[10px] uppercase shadow-lg hover:bg-blue-700 transition-all flex items-center tracking-widest">
                <i class="fas fa-print mr-2"></i> Print Record
            </button>
        </div>
    </div>

    <!-- PATIENT HERO SECTION -->
    <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between gap-6 relative overflow-hidden">
        <div class="flex items-center gap-6 relative z-10">
            <div class="h-20 w-20 bg-blue-600 text-white rounded-2xl flex items-center justify-center text-3xl font-black shadow-lg">
                <?= substr($patient['first_name'], 0, 1) . substr($patient['last_name'], 0, 1) ?>
            </div>
            <div>
                <p class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] mb-1">Patient Profile</p>
                <h1 class="text-3xl md:text-4xl font-black text-slate-900 uppercase italic tracking-tight leading-none">
                    <?= strtoupper($patient['last_name']) ?>, <?= strtoupper($patient['first_name']) ?>
                </h1>
                <div class="flex flex-wrap items-center gap-y-2 gap-x-4 mt-3">
                    <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-lg text-xs font-mono font-bold border">
                        ID: #<?= str_pad($patient['id'], 5, '0', STR_PAD_LEFT) ?>
                    </span>
                    <span class="text-slate-500 text-xs font-bold uppercase tracking-widest">
                        <i class="fas fa-phone-alt mr-1"></i> <?= $patient['primary_mobile'] ?>
                    </span>
                    <span class="text-slate-500 text-xs font-bold uppercase tracking-widest">
                        <i class="fas fa-envelope mr-1"></i> <?= $patient['email'] ?: 'N/A' ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- QUICK FINANCIAL SUMMARY (Corrected Logic) -->
        <div class="grid grid-cols-2 gap-3 relative z-10">
            <div class="bg-white p-4 rounded-2xl border-2 border-green-500 flex flex-col justify-center min-w-[160px] shadow-sm">
                <p class="text-[9px] font-black text-green-600 uppercase tracking-widest mb-1">Total Amount Paid</p>
                <p class="text-2xl font-black text-slate-900">₱<?= number_format($total_paid, 2) ?></p>
            </div>
            <div class="bg-white p-4 rounded-2xl border-2 <?= $total_balance > 0 ? 'border-red-500' : 'border-slate-200' ?> flex flex-col justify-center min-w-[160px] shadow-sm">
                <p class="text-[9px] font-black <?= $total_balance > 0 ? 'text-red-500' : 'text-slate-400' ?> uppercase tracking-widest mb-1">Current Balance</p>
                <p class="text-2xl font-black <?= $total_balance > 0 ? 'text-red-600' : 'text-slate-300' ?>">
                    ₱<?= number_format($total_balance, 2) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- TABLE SECTION -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h5 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em] italic">Treatment History Log</h5>
            <span class="text-[10px] font-bold text-slate-400 uppercase"><?= count($history) ?> Records Found</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-100 text-slate-500 text-[10px] uppercase font-black tracking-[0.15em]">
                    <tr>
                        <th class="p-5">Date / Time</th>
                        <th class="p-5 text-center">Tooth</th>
                        <th class="p-5">Procedure</th>
                        <th class="p-5">Dentist</th>
                        <th class="p-5 text-center">Consent</th>
                        <th class="p-5 text-right">Amt. Charge</th>
                        <th class="p-5 text-right">Amt. Paid</th>
                        <th class="p-5 text-right">Balance</th>
                    </tr>
                </thead>
                <tbody class="text-xs divide-y divide-slate-50">
                    <?php if (empty($history)): ?>
                        <tr>
                            <td colspan="8" class="p-20 text-center text-slate-400 italic font-bold uppercase tracking-widest">
                                <i class="fas fa-folder-open text-3xl mb-3 block opacity-20"></i>
                                No medical records yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($history as $h): ?>
                            <tr class="hover:bg-slate-50/80 transition-all group">
                                <td class="p-5">
                                    <div class="font-bold text-slate-800 text-sm"><?= date('M d, Y', strtotime($h['date'])) ?></div>
                                    <div class="text-[10px] text-slate-400 font-medium uppercase tracking-tighter"><?= date('h:i A', strtotime($h['date'])) ?></div>
                                </td>

                                <td class="p-5 text-center">
                                    <span class="inline-block h-8 w-8 leading-8 rounded-full bg-slate-100 text-slate-600 font-black text-[11px] border">
                                        <?= $h['tooth_number'] ?: '--' ?>
                                    </span>
                                </td>

                                <td class="p-5">
                                    <div class="bg-blue-50 text-blue-700 px-3 py-1 rounded-lg font-black uppercase text-[10px] inline-block border border-blue-100">
                                        <?= $h['service_name'] ?: 'N/A' ?>
                                    </div>
                                </td>

                                <td class="p-5">
                                    <span class="font-bold text-slate-700 italic uppercase tracking-tighter">Dr. <?= $h['d_lname'] ?></span>
                                </td>

                                <td class="p-5 text-center">
                                    <?php if ($h['consent_given']): ?>
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-check-circle text-green-500 text-lg"></i>
                                            <?php if ($h['treat_sig']): ?>
                                                <span class="text-[7px] text-blue-500 font-black uppercase mt-1">Signed</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <i class="fas fa-minus-circle text-slate-200 text-lg"></i>
                                    <?php endif; ?>
                                </td>

                                <td class="p-5 text-right font-bold text-slate-500">
                                    ₱<?= number_format($h['charge'], 2) ?>
                                </td>
                                <td class="p-5 text-right font-black text-green-600">
                                    ₱<?= number_format($h['paid'], 2) ?>
                                </td>
                                <td class="p-5 text-right">
                                    <?php if ($h['balance'] > 0): ?>
                                        <span class="bg-red-50 text-red-600 px-2 py-1 rounded-md font-black border border-red-100">
                                            ₱<?= number_format($h['balance'], 2) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-green-500 font-black uppercase text-[10px] tracking-widest">
                                            <i class="fas fa-check mr-1"></i> Paid
                                        </span>
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
<?= $this->endSection() ?>