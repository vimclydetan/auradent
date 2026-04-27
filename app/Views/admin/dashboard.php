<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Patients -->
    <div class="bg-white p-6 rounded-xl shadow-sm border flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <p class="text-slate-500 text-sm font-medium">Total Patients</p>
            <h3 class="text-3xl font-bold text-slate-800"><?= number_format($stats['total_patients']) ?></h3>
            <p class="text-xs text-green-600 mt-1">
                <i class="fas fa-arrow-up"></i> Active patients
            </p>
        </div>
        <div class="bg-blue-100 p-4 rounded-lg text-blue-600">
            <i class="fas fa-users text-2xl"></i>
        </div>
    </div>

    <!-- Appointments Today -->
    <div class="bg-white p-6 rounded-xl shadow-sm border flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <p class="text-slate-500 text-sm font-medium">Today's Appointments</p>
            <h3 class="text-3xl font-bold text-slate-800"><?= $stats['appointments_today'] ?></h3>
            <p class="text-xs text-slate-500 mt-1">
                <?= $stats['pending_appointments'] ?> pending
            </p>
        </div>
        <div class="bg-green-100 p-4 rounded-lg text-green-600">
            <i class="fas fa-calendar-check text-2xl"></i>
        </div>
    </div>

    <!-- Monthly Revenue -->
    <div class="bg-white p-6 rounded-xl shadow-sm border flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <p class="text-slate-500 text-sm font-medium">Monthly Revenue</p>
            <h3 class="text-3xl font-bold text-slate-800">₱<?= $stats['monthly_revenue'] ?></h3>
            <p class="text-xs text-slate-500 mt-1">
                <?= $stats['completed_this_month'] ?> completed
            </p>
        </div>
        <div class="bg-purple-100 p-4 rounded-lg text-purple-600">
            <i class="fas fa-coins text-2xl"></i>
        </div>
    </div>

    <!-- Active Dentists -->
    <div class="bg-white p-6 rounded-xl shadow-sm border flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <p class="text-slate-500 text-sm font-medium">Active Dentists</p>
            <h3 class="text-3xl font-bold text-slate-800"><?= $stats['active_dentists'] ?></h3>
            <p class="text-xs text-blue-600 mt-1">
                <i class="fas fa-user-md"></i> Available
            </p>
        </div>
        <div class="bg-indigo-100 p-4 rounded-lg text-indigo-600">
            <i class="fas fa-user-md text-2xl"></i>
        </div>
    </div>
</div>

<!-- Appointment Status Chart -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-sm border lg:col-span-2">
        <div class="flex items-center justify-between mb-6">
            <h4 class="font-bold text-slate-700 text-lg">Appointment Status (This Month)</h4>
            <select class="text-sm border rounded-lg px-3 py-1.5 text-slate-600">
                <option>This Month</option>
                <option>Last Month</option>
                <option>Last 3 Months</option>
            </select>
        </div>
        <div class="grid grid-cols-4 gap-4">
            <div class="text-center p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <div class="text-3xl font-bold text-yellow-600"><?= $appointmentsByStatus['Pending'] ?></div>
                <div class="text-sm text-slate-600 mt-1">Pending</div>
            </div>
            <div class="text-center p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="text-3xl font-bold text-blue-600"><?= $appointmentsByStatus['Confirmed'] ?></div>
                <div class="text-sm text-slate-600 mt-1">Confirmed</div>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg border border-green-200">
                <div class="text-3xl font-bold text-green-600"><?= $appointmentsByStatus['Completed'] ?></div>
                <div class="text-sm text-slate-600 mt-1">Completed</div>
            </div>
            <div class="text-center p-4 bg-red-50 rounded-lg border border-red-200">
                <div class="text-3xl font-bold text-red-600"><?= $appointmentsByStatus['Cancelled'] ?></div>
                <div class="text-sm text-slate-600 mt-1">Cancelled</div>
            </div>
        </div>
    </div>

    <!-- Recent Patients -->
    <div class="bg-white p-6 rounded-xl shadow-sm border">
        <h4 class="font-bold text-slate-700 text-lg mb-4">Recent Patients</h4>
        <div class="space-y-3">
            <?php if (!empty($recentPatients)): ?>
                <?php foreach ($recentPatients as $patient): ?>
                    <div class="flex items-center justify-between p-3 hover:bg-slate-50 rounded-lg transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                <?= strtoupper(substr($patient['full_name'], 0, 1)) ?>
                            </div>
                            <div>
                                <div class="font-medium text-slate-700 text-sm"><?= esc($patient['full_name']) ?></div>
                                <div class="text-xs text-slate-500"><?= esc($patient['patient_code']) ?></div>
                            </div>
                        </div>
                        <a href="<?= base_url('patients/view/' . $patient['id']) ?>" class="text-blue-600 hover:text-blue-700">
                            <i class="fas fa-arrow-right text-sm"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-slate-400 text-sm text-center py-4">No recent patients</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Upcoming Appointments Table -->
<div class="bg-white rounded-xl shadow-sm border">
    <div class="p-6 border-b flex items-center justify-between">
        <h4 class="font-bold text-slate-700 text-lg">Upcoming Appointments</h4>
        <a href="<?= base_url('appointments') ?>" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
            View All <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Date & Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Patient</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Dentist</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Services</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                <?php if (!empty($upcomingAppointments)): ?>
                    <?php foreach ($upcomingAppointments as $appointment): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900">
                                    <?= date('M d, Y', strtotime($appointment['appointment_date'])) ?>
                                </div>
                                <div class="text-xs text-slate-500">
                                    <?= date('g:i A', strtotime($appointment['appointment_time'])) ?> -
                                    <?= date('g:i A', strtotime($appointment['end_time'])) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-slate-900"><?= esc($appointment['patient_name']) ?></div>
                                <div class="text-xs text-slate-500"><?= esc($appointment['primary_mobile']) ?></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">
                                <?= esc($appointment['dentist_name']) ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-slate-700"><?= esc($appointment['services'] ?: 'N/A') ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $statusColors = [
                                    'Pending' => 'bg-yellow-100 text-yellow-800',
                                    'Confirmed' => 'bg-blue-100 text-blue-800',
                                    'Completed' => 'bg-green-100 text-green-800',
                                    'Cancelled' => 'bg-red-100 text-red-800'
                                ];
                                $colorClass = $statusColors[$appointment['status']] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $colorClass ?>">
                                    <?= esc($appointment['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="<?= base_url('appointments/view/' . $appointment['id']) ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= base_url('appointments/edit/' . $appointment['id']) ?>" class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                            <i class="fas fa-calendar-times text-4xl mb-2"></i>
                            <p>No upcoming appointments</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>