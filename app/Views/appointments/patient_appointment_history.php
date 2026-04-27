<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<div class="max-w-5xl mx-auto">
    <!-- HEADER SECTION -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-800 uppercase tracking-tight">Patient History</h1>
            <p class="text-slate-500 font-medium">Complete record of clinical procedures and appointments.</p>
        </div>
        <div class="bg-blue-600 text-white px-6 py-3 rounded-2xl shadow-lg shadow-blue-200">
            <p class="text-[10px] font-black uppercase opacity-80 leading-none">Patient ID</p>
            <p class="text-xl font-mono font-bold"><?= $patient['patient_code'] ?></p>
        </div>
    </div>

    <!-- PATIENT INFO CARD -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Full Name</p>
            <p class="font-bold text-slate-800 uppercase text-lg"><?= $patient['first_name'] ?> <?= $patient['last_name'] ?></p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Contact Number</p>
            <p class="font-bold text-slate-800 tracking-wider"><?= $patient['primary_mobile'] ?></p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Gender / Age</p>
            <p class="font-bold text-slate-800"><?= $patient['gender'] ?> (<?= date_diff(date_create($patient['birthdate']), date_create('today'))->y ?> yrs old)</p>
        </div>
    </div>

    <!-- HISTORY TABLE -->
    <!-- HISTORY TABLE -->
    <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-900 text-slate-300 text-[11px] font-black uppercase tracking-widest">
                <tr>
                    <th class="p-5">Schedule (Start - End)</th>
                    <th class="p-5">Procedure / Service</th>
                    <th class="p-5">Dentist</th>
                    <th class="p-5 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($history)): ?>
                    <tr>
                        <td colspan="4" class="p-20 text-center">
                            <i class="fas fa-folder-open text-slate-200 text-6xl mb-4"></i>
                            <p class="text-slate-400 font-bold uppercase text-xs">No previous records found for this patient.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($history as $h): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-5">
                                <!-- START SCHEDULE -->
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="far fa-calendar-alt text-blue-500 text-xs"></i>
                                    <span class="font-bold text-slate-700"><?= $h['fmt_date'] ?></span>
                                </div>

                                <!-- TIME RANGE -->
                                <div class="flex items-center gap-2 text-[10px] text-blue-600 font-bold uppercase italic bg-blue-50 w-fit px-2 py-1 rounded-md border border-blue-100">
                                    <i class="far fa-clock text-[9px]"></i>
                                    <?= $h['fmt_time'] ?> — <?= $h['fmt_end'] ?>

                                    <!-- IPAPAKITA LANG ANG END DATE KUNG MAGKAIBA SILA NG START DATE (Multi-day appointment) -->
                                    <?php if ($h['appointment_date'] !== $h['end_date']): ?>
                                        <span class="text-slate-400 ml-1"> until <?= $h['fmt_end_date'] ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="p-5">
                                <p class="font-bold text-slate-800 leading-tight"><?= $h['services_rendered'] ?: 'General Consultation' ?></p>
                                <?php if ($h['remarks']): ?>
                                    <p class="text-[11px] text-slate-500 mt-1 italic bg-slate-50 p-2 rounded border-l-2 border-slate-200">
                                        <i class="fas fa-comment-medical mr-1"></i> "<?= $h['remarks'] ?>"
                                    </p>
                                <?php endif; ?>
                            </td>
                            <td class="p-5">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-slate-600 uppercase tracking-tight">Dr. <?= $h['dentist_last'] ?: '---' ?></span>
                                </div>
                            </td>
                            <td class="p-5 text-center">
                                <?php
                                $colors = [
                                    'Completed' => 'bg-green-100 text-green-700 border-green-200',
                                    'Confirmed' => 'bg-blue-100 text-blue-700 border-blue-200',
                                    'Pending'   => 'bg-amber-100 text-amber-700 border-amber-200',
                                    'Cancelled' => 'bg-red-100 text-red-700 border-red-200'
                                ];
                                $c = $colors[$h['status']] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                                ?>
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase border <?= $c ?> shadow-sm">
                                    <?= $h['status'] ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- FOOTER ACTIONS -->
    <div class="mt-8 flex justify-center no-print">
        <button onclick="window.print()" class="bg-slate-100 text-slate-600 px-6 py-2 rounded-xl font-bold uppercase text-xs hover:bg-slate-200 transition-all flex items-center gap-2">
            <i class="fas fa-print"></i> Print Records
        </button>
    </div>
</div>

<style>
    @media print {
        .no-print {
            display: none !important;
        }

        body {
            background: white !important;
        }

        .bg-white {
            border: none !important;
            shadow: none !important;
        }
    }
</style>
<?= $this->endSection() ?>