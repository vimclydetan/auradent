<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Today's Bookings -->
    <div class="bg-white p-6 rounded-xl shadow-sm border flex items-center justify-between">
        <div>
            <p class="text-slate-500 text-sm italic">Today's Bookings</p>
            <h3 class="text-3xl font-bold text-slate-800"><?= $countToday ?></h3>
        </div>
        <div class="bg-blue-100 p-3 rounded-lg text-blue-600"><i class="fas fa-calendar-check text-2xl"></i></div>
    </div>
    
    <!-- Pending Confirmations -->
    <div class="bg-white p-6 rounded-xl shadow-sm border flex items-center justify-between">
        <div>
            <p class="text-slate-500 text-sm italic">Pending Confirmation</p>
            <h3 class="text-3xl font-bold text-slate-800"><?= $countPending ?></h3>
        </div>
        <div class="bg-amber-100 p-3 rounded-lg text-amber-600"><i class="fas fa-clock text-2xl"></i></div>
    </div>

    <!-- Confirmed Today -->
    <div class="bg-white p-6 rounded-xl shadow-sm border flex items-center justify-between">
        <div>
            <p class="text-slate-500 text-sm italic">Confirmed Today</p>
            <h3 class="text-3xl font-bold text-slate-800"><?= $countConfirmed ?></h3>
        </div>
        <div class="bg-green-100 p-3 rounded-lg text-green-600"><i class="fas fa-user-check text-2xl"></i></div>
    </div>

    <!-- Total Registered Patients -->
    <div class="bg-white p-6 rounded-xl shadow-sm border flex items-center justify-between">
        <div>
            <p class="text-slate-500 text-sm italic">Total Patients</p>
            <h3 class="text-3xl font-bold text-slate-800"><?= $totalPatients ?></h3>
        </div>
        <div class="bg-purple-100 p-3 rounded-lg text-purple-600"><i class="fas fa-users text-2xl"></i></div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border">
    <div class="p-6 border-b flex justify-between items-center">
        <h4 class="font-bold text-slate-700 uppercase tracking-wider text-sm">Today's Appointments Queue</h4>
        <span class="text-xs font-medium text-slate-400"><?= date('M d, Y') ?></span>
    </div>
    <div class="p-0 overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-widest font-bold">
                <tr>
                    <!-- Binago ang header name -->
                    <th class="px-6 py-4">Schedule (Start - End)</th> 
                    <th class="px-6 py-4">Patient Name</th>
                    <th class="px-6 py-4">Service(s)</th>
                    <th class="px-6 py-4">Dentist</th>
                    <th class="px-6 py-4 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y text-sm text-slate-600">
                <?php if(empty($todayAppointments)): ?>
                    <tr><td colspan="5" class="px-6 py-10 text-center italic text-slate-400">No appointments scheduled for today.</td></tr>
                <?php else: ?>
                    <?php foreach($todayAppointments as $appt): ?>
                    <tr class="hover:bg-slate-50 transition">
                        <!-- Dito ipinapakita ang Start Time at End Time -->
                        <td class="px-6 py-4 font-medium text-slate-700">
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-800">
                                    <?= date('h:i A', strtotime($appt['appointment_time'])) ?>
                                </span>
                                <span class="text-[10px] text-slate-400 uppercase tracking-tighter">
                                    to <?= $appt['end_time'] ? date('h:i A', strtotime($appt['end_time'])) : '---' ?>
                                </span>
                            </div>
                        </td>

                        <td class="px-6 py-4 font-medium text-blue-600"><?= $appt['patient_name'] ?></td>
                        <td class="px-6 py-4">
                            <span class="text-xs bg-slate-100 px-2 py-1 rounded text-slate-500"><?= $appt['service_name'] ?: 'N/A' ?></span>
                        </td>
                        <td class="px-6 py-4 text-xs"><?= $appt['dentist_name'] ?></td>
                        <td class="px-6 py-4 text-center">
                            <?php 
                                $statusColor = [
                                    'Pending' => 'bg-amber-100 text-amber-700',
                                    'Confirmed' => 'bg-green-100 text-green-700',
                                    'Completed' => 'bg-blue-100 text-blue-700',
                                    'Cancelled' => 'bg-red-100 text-red-700'
                                ];
                                $color = $statusColor[$appt['status']] ?? 'bg-slate-100 text-slate-600';
                            ?>
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase <?= $color ?>">
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