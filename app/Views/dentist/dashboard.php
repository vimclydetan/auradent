<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h3 class="text-2xl font-bold text-slate-800 italic uppercase tracking-tighter flex items-center gap-2">
            <i class="fas fa-tooth text-teal-600"></i>
            My Dashboard
        </h3>
        <p class="text-sm text-slate-500 font-medium mt-1">
            Welcome back, Dr. <?= session()->get('last_name') ?? 'Dentist' ?>
        </p>
    </div>
</div>

<!-- STATS GRID -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mb-8">
     
    <!-- My Appointments Today -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center justify-between transition hover:shadow-md">
        <div>
            <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider">My Appointments Today</p>
            <h3 class="text-2xl font-black text-slate-800"><?= $myTodayCount ?></h3>
        </div>
        <div class="bg-blue-50 p-3 rounded-xl text-blue-600"><i class="fas fa-calendar-day text-xl"></i></div>
    </div>

    <!-- Completed Treatments Today -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center justify-between transition hover:shadow-md">
        <div>
            <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider">Completed Today</p>
            <h3 class="text-2xl font-black text-slate-800"><?= $myCompletedCount ?></h3>
        </div>
        <div class="bg-green-50 p-3 rounded-xl text-green-600"><i class="fas fa-check-circle text-xl"></i></div>
    </div>

    <!-- Upcoming (Next 7 Days) -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center justify-between transition hover:shadow-md">
        <div>
            <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider">Upcoming (7 Days)</p>
            <h3 class="text-2xl font-black text-slate-800"><?= $myUpcomingCount ?></h3>
        </div>
        <div class="bg-purple-50 p-3 rounded-xl text-purple-600"><i class="fas fa-forward text-xl"></i></div>
    </div>
</div>

<!-- DATA TABLE SECTION -->
<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <h4 class="font-bold text-slate-700 uppercase tracking-widest text-xs flex items-center gap-2">
            <i class="fas fa-clipboard-list text-teal-500"></i>
            My Schedule For Today
        </h4>
        <span class="text-[10px] font-bold text-slate-400 bg-slate-50 px-3 py-1 rounded-full border border-slate-100">
            <?= date('F d, Y') ?>
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead class="bg-slate-50/50 text-slate-500 text-[10px] uppercase tracking-widest font-bold">
                <tr>
                    <th class="px-6 py-4">Time</th>
                    <th class="px-6 py-4">Patient Info</th>
                    <th class="px-6 py-4">Procedure / Service</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                <?php if (empty($mySchedule)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center italic text-slate-400 bg-slate-50/20">You have no appointments scheduled for today.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($mySchedule as $appt): ?>
                        <tr class="hover:bg-teal-50/30 transition-colors group">
                            <!-- TIME -->
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-800"><?= date('h:i A', strtotime($appt['appointment_time'])) ?></span>
                                    <span class="text-[10px] text-slate-400 font-medium italic">
                                        to <?= !empty($appt['end_time']) ? date('h:i A', strtotime($appt['end_time'])) : '--:--' ?>
                                    </span>
                                </div>
                            </td>

                            <!-- PATIENT INFO (Using the structure from your PatientModel) -->
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-700 group-hover:text-teal-600 block">
                                    <?= esc($appt['patient_name']) ?>
                                </span>
                                <span class="text-[10px] text-slate-400 font-bold tracking-wider">
                                    <?= esc($appt['patient_code']) ?>
                                </span>
                            </td>

                            <!-- SERVICE -->
                            <td class="px-6 py-4">
                                <span class="inline-block px-2 py-1 bg-slate-100 rounded text-[11px] text-slate-600 font-bold border border-slate-200">
                                    <?= esc($appt['service_name'] ?? 'General Checkup') ?>
                                </span>
                            </td>

                            <!-- STATUS -->
                            <td class="px-6 py-4 text-center">
                                <?php
                                $statusColor = [
                                    'Pending' => 'bg-amber-100 text-amber-700 ring-amber-200',
                                    'Confirmed' => 'bg-green-100 text-green-700 ring-green-200',
                                    'In Progress' => 'bg-indigo-100 text-indigo-700 ring-indigo-200',
                                    'Completed' => 'bg-blue-100 text-blue-700 ring-blue-200',
                                    'Cancelled' => 'bg-red-100 text-red-700 ring-red-200'
                                ];
                                $color = $statusColor[$appt['status']] ?? 'bg-slate-100 text-slate-600 ring-slate-200';
                                ?>
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase ring-1 ring-inset <?= $color ?>">
                                    <?= esc($appt['status']) ?>
                                </span>
                            </td>

                            <!-- ACTIONS -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Open Medical Record/Chart -->
                                    <a href="<?= base_url('dentist/medical-records/' . $appt['patient_id']) ?>" 
                                       class="px-3 py-1.5 bg-white border border-slate-200 text-slate-600 text-[11px] font-bold uppercase rounded hover:bg-teal-50 hover:text-teal-600 hover:border-teal-200 transition-colors"
                                       title="View Medical Record">
                                        <i class="fas fa-notes-medical"></i> Chart
                                    </a>
                                    
                                    <!-- Start Treatment / Update Status -->
                                    <button class="px-3 py-1.5 bg-teal-600 text-white text-[11px] font-bold uppercase rounded hover:bg-teal-700 shadow-sm transition-colors"
                                            title="Update Appointment">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>