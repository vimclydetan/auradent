<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Stats Card -->
    <div class="bg-white p-6 rounded-xl shadow-sm border flex items-center justify-between">
        <div>
            <p class="text-slate-500 text-sm">Total Patients</p>
            <h3 class="text-3xl font-bold text-slate-800">124</h3>
        </div>
        <div class="bg-blue-100 p-3 rounded-lg text-blue-600">
            <i class="fas fa-users text-2xl"></i>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-sm border flex items-center justify-between">
        <div>
            <p class="text-slate-500 text-sm">Appointments Today</p>
            <h3 class="text-3xl font-bold text-slate-800">8</h3>
        </div>
        <div class="bg-green-100 p-3 rounded-lg text-green-600">
            <i class="fas fa-calendar-check text-2xl"></i>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border flex items-center justify-between">
        <div>
            <p class="text-slate-500 text-sm">Monthly Revenue</p>
            <h3 class="text-3xl font-bold text-slate-800">₱45,000</h3>
        </div>
        <div class="bg-purple-100 p-3 rounded-lg text-purple-600">
            <i class="fas fa-coins text-2xl"></i>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border">
    <div class="p-6 border-b">
        <h4 class="font-bold text-slate-700">Upcoming Appointments</h4>
    </div>
    <div class="p-6">
        <p class="text-slate-400 italic">Table data will appear here...</p>
    </div>
</div>
<?= $this->endSection() ?>