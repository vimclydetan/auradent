<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<!-- FullCalendar CDN -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

<div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800 uppercase tracking-tight">📅 Appointment Calendar</h3>
            <p class="text-xs text-slate-500 font-bold italic uppercase mt-1">Pending and Confirmed schedules only</p>
        </div>
        
        <!-- Legend -->
        <div class="flex gap-4 text-[10px] font-black uppercase tracking-widest">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                <span class="text-slate-600">Pending</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                <span class="text-slate-600">Confirmed</span>
            </div>
        </div>
    </div>

    <!-- Calendar Container -->
    <div id="fullCalendar" class="min-h-[700px]"></div>
</div>

<!-- VIEW APPOINTMENT MODAL -->
<div id="viewModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-[60]">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
        <!-- Header -->
        <div id="modalHeader" class="p-5 flex justify-between items-center text-white">
            <h4 class="font-bold uppercase tracking-widest text-sm">Appointment Details</h4>
            <button onclick="closeViewModal()" class="text-white/80 hover:text-white text-2xl">&times;</button>
        </div>
        
        <!-- Body -->
        <div class="p-6 space-y-5">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Patient Name</label>
                <p id="det_name" class="text-lg font-bold text-slate-800"></p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Date</label>
                    <p id="det_date" class="text-sm font-bold text-slate-700"></p>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Status</label>
                    <span id="det_status" class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"></span>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Time Schedule</label>
                <div class="flex items-center gap-2 text-blue-600 font-bold text-sm">
                    <i class="far fa-clock"></i>
                    <span id="det_time"></span>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <a href="<?= base_url('admin/appointments') ?>" class="block w-full text-center bg-slate-100 hover:bg-slate-200 text-slate-600 py-3 rounded-xl font-bold text-xs uppercase tracking-widest transition-all">
                    Go to Appointment List
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    /* AuraDent Calendar Styling */
    :root {
        --fc-button-bg-color: #f8fafc;
        --fc-button-text-color: #475569;
        --fc-button-border-color: #e2e8f0;
        --fc-button-hover-bg-color: #f1f5f9;
        --fc-button-active-bg-color: #2563eb;
        --fc-button-active-text-color: #ffffff;
        --fc-today-bg-color: #eff6ff;
    }

    /* Toolbar styling */
    .fc .fc-toolbar-title { font-size: 1.1rem !important; font-weight: 800; color: #1e293b; text-transform: uppercase; letter-spacing: -0.025em; }
    .fc .fc-button { font-weight: 700 !important; font-size: 0.7rem !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; border-radius: 8px !important; }
    .fc .fc-button-primary:not(:disabled).fc-button-active { background-color: #2563eb !important; border-color: #2563eb !important; }

    /* Day Grid Styling */
    .fc .fc-col-header-cell-cushion { font-size: 0.7rem; font-weight: 800; color: #64748b; text-transform: uppercase; padding: 12px 0 !important; text-decoration: none !important; }
    .fc-theme-standard td, .fc-theme-standard th { border-color: #f1f5f9 !important; }
    .fc .fc-daygrid-day-number { font-size: 0.8rem; font-weight: 700; color: #94a3b8; padding: 8px !important; text-decoration: none !important; }

    /* EVENT STYLING (The pill look) */
    .fc-event { 
        border: none !important; 
        margin: 2px 4px !important; 
        padding: 4px 8px !important; 
        border-radius: 6px !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: transform 0.1s ease;
    }
    .fc-event:hover { transform: scale(1.02); }

    /* Custom content inside the event */
    .event-content { display: flex; flex-direction: column; line-height: 1.2; }
    .event-time { font-size: 10px; font-weight: 800; opacity: 0.9; text-transform: uppercase; margin-bottom: 1px; }
    .event-title { font-size: 11px; font-weight: 700; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('fullCalendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            height: 750,
            events: '<?= base_url('admin/calendar/events') ?>',
            
            // CUSTOM EVENT DISPLAY
            eventContent: function(arg) {
                // Format time to 12h (h:mm A)
                let timeStr = arg.event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
                
                let container = document.createElement('div');
                container.classList.add('event-content');
                
                container.innerHTML = `
                    <div class="event-time">${timeStr}</div>
                    <div class="event-title">${arg.event.title}</div>
                `;
                
                return { domNodes: [container] };
            },

            eventClick: function(info) {
                openViewModal(info.event);
            }
        });
        calendar.render();
    });

    function openViewModal(event) {
        const modal = document.getElementById('viewModal');
        const header = document.getElementById('modalHeader');
        const status = event.extendedProps.status;

        // Set Data
        document.getElementById('det_name').innerText = event.title;
        document.getElementById('det_date').innerText = event.start.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
        
        let startTime = event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        let endTime = event.end ? event.end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '---';
        document.getElementById('det_time').innerText = `${startTime} - ${endTime}`;
        
        // Status Styling
        const statusEl = document.getElementById('det_status');
        statusEl.innerText = status;
        
        if (status === 'Pending') {
            header.className = 'p-5 flex justify-between items-center text-white bg-amber-500';
            statusEl.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-100 text-amber-600';
        } else {
            header.className = 'p-5 flex justify-between items-center text-white bg-blue-600';
            statusEl.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-blue-100 text-blue-600';
        }

        modal.classList.remove('hidden');
    }

    function closeViewModal() {
        document.getElementById('viewModal').classList.add('hidden');
    }

    // Close on outside click
    window.onclick = function(event) {
        let modal = document.getElementById('viewModal');
        if (event.target == modal) closeViewModal();
    }
</script>
<?= $this->endSection() ?>