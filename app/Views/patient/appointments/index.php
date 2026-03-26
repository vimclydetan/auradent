<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<!-- Custom Minimalist Styles -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    
    .minimalist-container {
        font-family: 'Inter', sans-serif;
        color: #1e293b;
    }

    .status-dot {
        height: 8px;
        width: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }

    /* Table-like row behavior but cleaner */
    .appt-row {
        transition: background-color 0.2s;
        border-bottom: 1px solid #f1f5f9;
    }
    .appt-row:hover {
        background-color: #f8fafc;
    }
</style>

<div class="w-full minimalist-container">
    
    <!-- TOP HEADER BAR -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10 pb-6 border-b border-slate-100">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">My Appointments</h2>
            <p class="text-sm text-slate-500 mt-1">View and manage your scheduled dental procedures.</p>
        </div>
        <button onclick="document.getElementById('bookModal').classList.remove('hidden')" 
            class="bg-blue-600 text-white px-6 py-3 rounded-xl text-sm font-semibold hover:bg-blue-700 transition-all shadow-sm flex items-center gap-2">
            <i class="fas fa-plus text-xs"></i> Request Appointment
        </button>
    </div>

    <!-- MAIN CONTENT GRID -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        
        <!-- LEFT & MIDDLE: UPCOMING VISITS (Wider Column) -->
        <div class="xl:col-span-2 space-y-6">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Upcoming Schedules</h4>
                <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-md"><?= count($upcoming) ?> Active</span>
            </div>

            <?php if(empty($upcoming)): ?>
                <div class="py-20 text-center border border-dashed border-slate-200 rounded-2xl bg-slate-50/50">
                    <p class="text-sm text-slate-400 font-medium">No upcoming visits found.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach($upcoming as $u): ?>
                        <div class="bg-white border border-slate-200 rounded-2xl p-6 flex flex-col justify-between hover:border-blue-200 transition-all group">
                            <div>
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                                            <?= date('d', strtotime($u['appointment_date'])) ?>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-slate-400 uppercase"><?= date('M Y', strtotime($u['appointment_date'])) ?></p>
                                            <p class="text-sm font-bold text-slate-700"><?= $u['fmt_time'] ?></p>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-bold uppercase tracking-tight text-slate-400 flex items-center">
                                        <span class="status-dot <?= ($u['status'] === 'Confirmed') ? 'bg-green-500' : 'bg-amber-400' ?>"></span>
                                        <?= $u['status'] ?>
                                    </span>
                                </div>
                                <h5 class="text-lg font-bold text-slate-800 mb-1"><?= esc($u['service_name']) ?></h5>
                                <p class="text-sm text-slate-500 mb-6 flex items-center gap-1">
                                    <i class="far fa-user-circle text-xs"></i> <?= $u['dentist_name'] ?>
                                </p>
                            </div>

                            <div class="flex gap-2 pt-4 border-t border-slate-50">
                                <button onclick='viewDetails(<?= json_encode($u) ?>)' class="flex-1 py-2 rounded-lg text-xs font-bold text-slate-600 border border-slate-100 hover:bg-slate-50 transition-all">View Details</button>
                                <button onclick="openRescheduleModal(<?= $u['id'] ?>, '<?= $u['appointment_date'] ?>', '<?= $u['appointment_time'] ?>')" class="px-3 py-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all">
                                    <i class="far fa-calendar-alt text-xs"></i>
                                </button>
                                <a href="<?= base_url('patient/appointments/status/'.$u['id'].'/Cancelled') ?>" onclick="return confirm('Cancel visit?')" class="px-3 py-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all">
                                    <i class="far fa-trash-alt text-xs"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- RIGHT: HISTORY LIST (Sidebar Column) -->
        <div class="xl:col-span-1">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6">Recent History</h4>
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                <?php if(empty($past)): ?>
                    <p class="p-10 text-xs text-slate-400 text-center italic">No records yet.</p>
                <?php else: ?>
                    <div class="flex flex-col">
                        <?php foreach($past as $p): ?>
                            <div class="appt-row p-5 flex items-center justify-between group">
                                <div class="flex items-center gap-4">
                                    <div class="text-center">
                                        <p class="text-[9px] font-bold text-slate-400 uppercase leading-none"><?= date('M', strtotime($p['appointment_date'])) ?></p>
                                        <p class="text-sm font-bold text-slate-700 leading-tight"><?= date('d', strtotime($p['appointment_date'])) ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800 leading-snug"><?= esc($p['service_name']) ?></p>
                                        <p class="text-[10px] text-slate-400 font-medium"><?= $p['dentist_name'] ?></p>
                                    </div>
                                </div>
                                <button onclick='viewDetails(<?= json_encode($p) ?>)' class="text-slate-300 group-hover:text-blue-500 transition-colors">
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- HELP CARD -->
            <div class="mt-6 p-6 bg-slate-900 rounded-2xl text-white">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Need Help?</p>
                <p class="text-xs text-slate-300 leading-relaxed mb-4">If you need to cancel a confirmed appointment on short notice, please call us directly.</p>
                <a href="tel:09123456789" class="text-sm font-bold text-blue-400 hover:underline">0912-345-6789</a>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: BOOKING (Standard Minimalist) -->
<div id="bookModal" class="hidden fixed inset-0 bg-slate-900/40 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate__animated animate__fadeIn animate__faster">
        <div class="p-6 border-b flex justify-between items-center">
            <h4 class="font-bold text-slate-800">New Appointment</h4>
            <button onclick="document.getElementById('bookModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
        </div>
        <form action="<?= base_url('patient/appointments/store') ?>" method="POST" class="p-6 space-y-5">
            <?= csrf_field() ?>
            <div>
                <label class="text-[11px] font-bold text-slate-400 uppercase block mb-1.5">Service Type</label>
                <select name="service_id" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    <option value="">Select procedure...</option>
                    <?php foreach($services as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= $s['service_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[11px] font-bold text-slate-400 uppercase block mb-1.5">Preferred Date</label>
                    <input type="date" name="appointment_date" min="<?= date('Y-m-d') ?>" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                </div>
                <div>
                    <label class="text-[11px] font-bold text-slate-400 uppercase block mb-1.5">Start Time</label>
                    <input type="time" name="appointment_time" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                </div>
            </div>
            <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-xl font-bold text-sm hover:bg-black transition-all">Submit Request</button>
        </form>
    </div>
</div>

<script>
    function viewDetails(a) {
        // Logic same as previous
        alert(a.service_name + "\n" + a.fmt_date + " @ " + a.fmt_time + "\nStatus: " + a.status);
    }

    function openRescheduleModal(id, date, time) {
        document.getElementById('resched_id').value = id;
        document.getElementById('resched_date').value = date;
        document.getElementById('resched_time').value = time;
        document.getElementById('rescheduleModal').classList.remove('hidden');
    }
</script>

<?= $this->endSection() ?>