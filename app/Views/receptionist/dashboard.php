<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-2xl font-bold text-slate-800 italic uppercase tracking-tighter flex items-center gap-2">
                <i class="fas fa-home text-blue-600"></i>
                Dashboard
            </h3>
        </div>
    </div>

<!-- STATS GRID -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
     
    <!-- Today's Bookings -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center justify-between transition hover:shadow-md">
        <div>
            <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider">Today's Bookings</p>
            <h3 class="text-2xl font-black text-slate-800"><?= $countToday ?></h3>
        </div>
        <div class="bg-blue-50 p-3 rounded-xl text-blue-600"><i class="fas fa-calendar-check text-xl"></i></div>
    </div>

    <!-- Pending -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center justify-between transition hover:shadow-md">
        <div>
            <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider">Pending Confirmation</p>
            <h3 class="text-2xl font-black text-slate-800"><?= $countPending ?></h3>
        </div>
        <div class="bg-amber-50 p-3 rounded-xl text-amber-600"><i class="fas fa-clock text-xl"></i></div>
    </div>

    <!-- Confirmed -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center justify-between transition hover:shadow-md">
        <div>
            <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider">Confirmed Today</p>
            <h3 class="text-2xl font-black text-slate-800"><?= $countConfirmed ?></h3>
        </div>
        <div class="bg-green-50 p-3 rounded-xl text-green-600"><i class="fas fa-user-check text-xl"></i></div>
    </div>

    <!-- Total Patients -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center justify-between transition hover:shadow-md">
        <div>
            <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider">Total Patients</p>
            <h3 class="text-2xl font-black text-slate-800"><?= $totalPatients ?></h3>
        </div>
        <div class="bg-purple-50 p-3 rounded-xl text-purple-600"><i class="fas fa-users text-xl"></i></div>
    </div>
</div>

<!-- DATA TABLE SECTION -->
<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <h4 class="font-bold text-slate-700 uppercase tracking-widest text-xs flex items-center gap-2">
            <i class="fas fa-list-ol text-blue-500"></i>
            Today's Appointment Queue
        </h4>
        <span class="text-[10px] font-bold text-slate-400 bg-slate-50 px-3 py-1 rounded-full border border-slate-100">
            <?= date('F d, Y') ?>
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[700px]">
            <thead class="bg-slate-50/50 text-slate-500 text-[10px] uppercase tracking-widest font-bold">
                <tr>
                    <th class="px-6 py-4">Schedule</th>
                    <th class="px-6 py-4">Patient Name</th>
                    <th class="px-6 py-4">Services</th>
                    <th class="px-6 py-4">Dentist</th>
                    <th class="px-6 py-4 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                <?php if (empty($todayAppointments)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center italic text-slate-400 bg-slate-50/20">No appointments scheduled for today.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($todayAppointments as $appt): ?>
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-800"><?= date('h:i A', strtotime($appt['appointment_time'])) ?></span>
                                    <span class="text-[10px] text-slate-400 font-medium italic">
                                        to <?= $appt['end_time'] ? date('h:i A', strtotime($appt['end_time'])) : '--:--' ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-700 group-hover:text-blue-600"><?= $appt['patient_name'] ?></td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-2 py-1 bg-slate-100 rounded text-[11px] text-slate-500 font-medium">
                                    <?= $appt['service_name'] ?: 'General Checkup' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs italic font-medium">Dr. <?= $appt['dentist_name'] ?></td>
                            <td class="px-6 py-4 text-center">
                                <?php
                                $statusColor = [
                                    'Pending' => 'bg-amber-100 text-amber-700 ring-amber-200',
                                    'Confirmed' => 'bg-green-100 text-green-700 ring-green-200',
                                    'Completed' => 'bg-blue-100 text-blue-700 ring-blue-200',
                                    'Cancelled' => 'bg-red-100 text-red-700 ring-red-200'
                                ];
                                $color = $statusColor[$appt['status']] ?? 'bg-slate-100 text-slate-600 ring-slate-200';
                                ?>
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase ring-1 ring-inset <?= $color ?>">
                                    <?= $appt['status'] ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>