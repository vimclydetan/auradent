// ===============================
// GLOBAL CONFIG
// ===============================
const OLD_DATA = {
    region:   "<?= old('region') ?>",
    province: "<?= old('province') ?>",
    city:     "<?= old('city') ?>",
    barangay: "<?= old('barangay') ?>",
    dentist:  "<?= old('dentist_id') ?>",
    date:     "<?= old('appointment_date') ?>",
};

let selectedSlot = null;

function getCsrfData() {
    const f = document.querySelector('input[name="<?= csrf_token() ?>"]');
    return { name: f?.name || 'csrf_test_name', token: f?.value || '' };
}

// ===============================
// TIME HELPERS
// ===============================
function timeToMinutes(t) {
    if (!t) return 0;
    const clean = t.replace(/\s*(AM|PM)/i, '').trim();
    let [h, m] = clean.split(':').map(Number);
    if (/PM/i.test(t) && h !== 12) h += 12;
    if (/AM/i.test(t) && h === 12) h = 0;
    return h * 60 + (m || 0);
}

function formatTime12h(t) {
    if (!t) return '';
    let [h, m] = t.replace(/\s*(AM|PM)/i, '').split(':').map(Number);
    const period = h >= 12 ? 'PM' : 'AM';
    if (h > 12) h -= 12;
    if (h === 0) h = 12;
    return `${h}:${String(m).padStart(2, '0')} ${period}`;
}

function formatTimeForDB(t) {
    const [time, period] = t.trim().split(' ');
    let [h, m, s] = time.split(':').map(Number);
    s = s || 0;
    if (period === 'PM' && h !== 12) h += 12;
    if (period === 'AM' && h === 12) h = 0;
    return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
}

// ===============================
// SLOT AVAILABILITY
// ===============================
function checkTimeSlots() {
    const dentistId = $('select[name="dentist_id"]').val();
    const date      = $('input[name="appointment_date"]').val();
    const serviceIds = [], serviceLevels = {};

    $('select[name="services[]"]').each(function () {
        const id = $(this).val();
        if (id) {
            serviceIds.push(id);
            serviceLevels[id] = $(this).closest('.service-row').find('select[name="levels[]"]').val() || 'Standard';
        }
    });

    if (!dentistId || !date) { $('#timeSlotSection').addClass('hidden'); return; }

    if (!serviceIds.length) {
        $('#timeSlotSection').removeClass('hidden');
        $('#slotsContainer').html(`<div class="col-span-full text-center py-8 text-[10px] text-slate-400"><i class="fas fa-tooth text-2xl mb-2"></i><br>Please select at least one service to view available times</div>`);
        $('#durationBadge').addClass('hidden');
        return;
    }

    $('#timeSlotSection').removeClass('hidden');
    $('#slotLoading').removeClass('hidden');
    $('#slotsContainer').empty().addClass('opacity-50');
    $('#slotError').addClass('hidden');

    $.ajax({
        url: API_BASE + '/checkAvailability',
        type: 'GET',
        data: { dentist_id: dentistId, date, service_ids: serviceIds, service_levels: serviceLevels, [getCsrfData().name]: getCsrfData().token },
        dataType: 'json',
        success(res) {
            if (!res.success) {
                $('#slotsContainer').html(`<div class="col-span-full text-center py-8 text-[10px] text-amber-600"><i class="fas fa-circle-exclamation text-xl mb-2"></i><br>${res.error || 'No available slots'}</div>`);
                $('#durationBadge').addClass('hidden');
                return;
            }

            $('#durationValue').text((res.required_duration_minutes || 0) + ' mins');
            $('#durationBadge').removeClass('hidden');
            $('#slotDateDisplay').html(`${res.date} • <span class="text-blue-600 font-bold">${res.duration_label || ''}</span>`);

            if (selectedSlot) {
                const updated = res.slots.find(s => s.start === selectedSlot.start);
                if (updated?.available) {
                    selectedSlot = updated;
                    $('#hidden_end_time').val(formatTimeForDB(updated.end_actual || updated.end));
                    $('#end_time_visible').val((updated.end_actual || updated.end).split(' ')[0]);
                    $('#slotError').addClass('hidden');
                } else {
                    selectedSlot = null;
                    $('#hidden_appt_time, #hidden_end_time').val('');
                    $('#appt_time_visible, #end_time_visible').val('');
                    $('#slotError').html("<i class='fas fa-exclamation-triangle'></i> May conflict na sa schedule. Pumili ng bagong oras.").removeClass('hidden');
                }
            }

            renderTimeSlots(res.slots);
        },
        error() {
            $('#slotsContainer').html(`<div class="col-span-full text-center py-8 text-[10px] text-red-600"><i class="fas fa-triangle-exclamation text-xl mb-2"></i><br>Failed to load slots. Please try again.</div>`);
            $('#durationBadge').addClass('hidden');
        },
        complete() {
            $('#slotLoading').addClass('hidden');
            $('#slotsContainer').removeClass('opacity-50');
        }
    });
}

function renderTimeSlots(slots) {
    const container = $('#slotsContainer').empty();

    if (!slots?.length) {
        container.html(`<div class="col-span-full text-center py-8"><p class="text-[10px] text-slate-400">No slots configured for this day</p></div>`);
        return;
    }

    // Filter out slots starting at/after 4PM
    slots = slots.filter(s => timeToMinutes(s.start) < timeToMinutes('16:00'));

    if (!slots.some(s => s.available)) {
        container.html(`<div class="col-span-full text-center py-8"><i class="fas fa-circle-exclamation text-3xl text-amber-400 mb-2"></i><p class="text-[10px] text-amber-600 font-bold">No available slots</p><p class="text-[9px] text-slate-400 mt-1">Try another date or dentist</p></div>`);
        return;
    }

    window.availableSlots = slots;
    slots.sort((a, b) => timeToMinutes(a.start) - timeToMinutes(b.start));

    // Group by hour
    const byHour = {};
    slots.forEach(s => {
        const h = Math.floor(timeToMinutes(s.start) / 60);
        (byHour[h] = byHour[h] || []).push(s);
    });

    Object.keys(byHour).sort((a,b) => a-b).forEach(h => {
        const num = parseInt(h), period = num >= 12 ? 'PM' : 'AM';
        const disp = num > 12 ? num - 12 : (num === 0 ? 12 : num);

        container.append(`<div class="col-span-full text-[8px] font-black text-slate-400 uppercase tracking-wider mt-1.5 mb-0.5 flex items-center gap-1"><span class="flex-1 h-px bg-slate-200"></span>${disp}:00 ${period}<span class="flex-1 h-px bg-slate-200"></span></div>`);

        byHour[h].forEach(slot => {
            const avail = slot.available;
            const selected = selectedSlot?.start === slot.start;
            const tooltip = !avail ? (slot.reason === 'past' ? 'Past time - unavailable' : 'Already booked') : (selected ? 'Selected Slot' : 'Click to select');

            let cls = 'slot-btn relative text-[10px] font-bold py-1.5 px-1.5 rounded-md border transition-all flex flex-col items-center gap-0.5 min-h-[40px] leading-tight';
            cls += selected ? ' bg-blue-600 border-blue-600 text-white shadow-md cursor-pointer'
                           : avail ? ' bg-white border-green-200 text-slate-700 hover:border-green-400 hover:bg-green-50 cursor-pointer'
                                   : ' bg-slate-100 border-slate-200 text-slate-400 cursor-not-allowed opacity-70';

            const btn = $(`
                <button type="button" class="${cls}" ${!avail ? 'disabled tabindex="-1"' : ''} title="${tooltip}">
                    <span class="font-bold">${formatTime12h(slot.start)}</span>
                    <span class="text-[9px] opacity-80">${formatTime12h(slot.end_display || slot.end)}</span>
                    ${!avail ? '<i class="fas fa-lock absolute top-1 right-1 text-[8px]"></i>' : ''}
                    ${selected ? '<i class="fas fa-check absolute -top-1 -right-1 w-4 h-4 bg-white text-blue-600 rounded-full text-[9px] flex items-center justify-center shadow"></i>' : ''}
                </button>
            `);

            if (avail) btn.on('click', () => handleSlotSelect(slot));
            container.append(btn);
        });
    });
}

function handleSlotSelect(slot) {
    if (selectedSlot?.start === slot.start) { clearSelection(); return; }

    selectedSlot = slot;
    const date = $('#appt_date').val();

    $('#hidden_appt_time').val(formatTimeForDB(slot.start));
    $('#hidden_end_time').val(formatTimeForDB(slot.end_actual || slot.end));
    $('#hidden_end_date').val(date);
    $('#appt_time_visible').val(slot.start.split(' ')[0]);
    $('#end_time_visible').val((slot.end_actual || slot.end).split(' ')[0]);
    $('#end_date_visible').val(date);

    if (slot.duration_minutes) {
        $('#durationValue').text(slot.duration_minutes + ' mins');
        $('#durationBadge').removeClass('hidden');
    }

    $('#slotError').addClass('hidden');
    renderTimeSlots(window.availableSlots);
}

function clearSelection() {
    selectedSlot = null;
    $('#hidden_appt_time, #hidden_end_time').val('');
    $('#appt_time_visible, #end_time_visible').val('');
    renderTimeSlots(window.availableSlots);
}

function resetSlotSelection() {
    clearSelection();
    $('#timeSlotSection').addClass('hidden');
    $('#slotsContainer').empty();
}
window.resetSlotSelection = resetSlotSelection;

// ===============================
// FILTERS
// ===============================
function applySearchFilter() {
    const search  = $('#tableSearch').val().toLowerCase();
    const status  = $('#statusFilter').val();
    const dentist = $('#dentistFilter').val();

    let visible = 0;
    $('.appt-row').each(function () {
        const show = $(this).text().toLowerCase().includes(search)
            && (!status  || $(this).data('status') === status)
            && (!dentist || $(this).attr('data-dentist') == dentist);
        $(this).toggle(show);
        if (show) visible++;
    });

    $('#no-results-row').remove();
    if (!visible) $('#appointmentTable').append('<tr id="no-results-row"><td colspan="6" class="p-10 text-center text-slate-400 italic font-medium">No records match your search/filter in this view.</td></tr>');
}

function resetFilters() {
    $('#tableSearch').val('');
    $('#statusFilter, #dentistFilter').val('');
    applySearchFilter();
}

// ===============================
// PATIENT / FORM LOGIC
// ===============================
function handleGenderLogic() {
    const male = $('select[name="gender"]').val() === 'Male';
    const sec  = $('#women_section');
    sec.css({ opacity: male ? '0.5' : '1', pointerEvents: male ? 'none' : 'auto', backgroundColor: male ? '#f1f5f9' : '' });
    sec.find('input').prop('disabled', male);
    if (male) sec.find('input[value="0"]').prop('checked', true);
    sec.find('label').first().toggleClass('text-pink-700', !male).toggleClass('text-slate-400', male);
}

function toggleAccountType() {
    const isNew = $('input[name="account_type"]:checked').val() === 'new';
    $('#existing_patient_div, #btn_confirm_existing').toggleClass('hidden', isNew);
    $('#new_patient_div, #btn_next_new').toggleClass('hidden', !isNew);
}

function goToStep(step) {
    if (step === 2) {
        if (!$('select[name="dentist_id"]').val() || !$('input[name="appointment_date"]').val()) {
            showInfoModal('Please select a Dentist and Schedule date before proceeding to medical history.', 'Missing Information');
            return;
        }
        if (!selectedSlot || !$('#hidden_appt_time').val()) {
            $('#slotError').html("<i class='fas fa-exclamation-triangle'></i> Please select a valid time slot before proceeding.").removeClass('hidden');
            $('#timeSlotSection')[0]?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
        $('#step1_container').addClass('step-hidden');
        $('#step2_container').removeClass('step-hidden');
        $('#dot2').addClass('active');
        $('#stepTitle').text('Step 2: Medical History Record');
        $('#apptModal').scrollTop(0);
    } else {
        $('#step1_container').removeClass('step-hidden');
        $('#step2_container').addClass('step-hidden');
        $('#dot2').removeClass('active');
        $('#stepTitle').text('Step 1: Appointment Details');
    }
}

// ===============================
// ADDRESS (PH)
// ===============================
const ADDRESS_CONFIG = {
    region:   { id: 'reg_select',  file: 'region.json',   next: 'province', filter: null },
    province: { id: 'prov_select', file: 'province.json', next: 'city',     filter: 'region_code' },
    city:     { id: 'city_select', file: 'city.json',     next: 'barangay', filter: 'province_code' },
    barangay: { id: 'brgy_select', file: 'barangay.json', next: null,       filter: 'city_code' },
};

const ADDR_DISPLAY = { region: 'region_name', province: 'province_name', city: 'city_name', barangay: 'brgy_name' };
const ADDR_VALUE   = { region: 'region_code', province: 'province_code', city: 'city_code', barangay: 'brgy_code' };

async function loadAddressLevel(level, parentValue = null) {
    const cfg = ADDRESS_CONFIG[level];
    const sel = document.getElementById(cfg.id);
    sel.disabled = true;
    sel.innerHTML = '<option value="" selected disabled></option>';

    try {
        const data = await fetch(`${BASE_URL}${cfg.file}`).then(r => r.json());
        const filtered = cfg.filter ? data.filter(i => i[cfg.filter] == parentValue) : data;

        filtered.forEach(item => {
            const display = item[ADDR_DISPLAY[level]];
            const value   = item[ADDR_VALUE[level]];
            const opt = new Option(display, value);
            if (OLD_DATA[level] && (display === OLD_DATA[level] || value === OLD_DATA[level])) opt.selected = true;
            sel.add(opt);
        });

        sel.disabled = false;
        if (sel.value && cfg.next) loadAddressLevel(cfg.next, sel.value);
    } catch (e) { console.error(`Error loading ${level}:`, e); }
}

// ===============================
// MODALS
// ===============================
function closeModal()        { $('#apptModal').addClass('hidden'); goToStep(1); resetSlotSelection(); }
function closeReschedModal() { $('#rescheduleModal').addClass('hidden'); }

function openRescheduleModal(id, d, t, ed, et, did) {
    $('#resched_id').val(id); $('#resched_date').val(d); $('#resched_time').val(t);
    $('#resched_end_date').val(ed); $('#resched_end_time').val(et); $('#resched_dentist').val(did);
    $('#rescheduleModal').removeClass('hidden');
}

function openCancelModal(id, name) {
    $('#cancel_appt_id').val(id);
    $('#cancel_patient_name').text(name);
    $('#cancel_reason').val('');
    $('#cancel_error').addClass('hidden');
    $('#cancelModal').removeClass('hidden');
    $('#cancel_reason').focus();
}
function closeCancelModal() { $('#cancelModal').addClass('hidden'); }

function openRejectModal(id, name) {
    $('#reject_appt_id').val(id);
    $('#reject_patient_name').text(name);
    $('#reject_reason').val('');
    $('#reject_error').addClass('hidden');
    $('#rejectModal').removeClass('hidden');
}
function closeRejectModal() { $('#rejectModal').addClass('hidden'); }

function openDenyCancelModal(id, name) {
    $('#deny_cancel_appt_id').val(id);
    $('#deny_cancel_patient_name').text(name);
    $('#deny_cancel_reason').val('');
    $('#deny_cancel_error').addClass('hidden');
    $('#denyCancelModal').removeClass('hidden');
}
function closeDenyCancelModal() { $('#denyCancelModal').addClass('hidden'); }

function showConfirmModal(message, onConfirm, title = 'Confirm Action', confirmText = 'Confirm') {
    const modal = document.getElementById('confirmModal');
    document.getElementById('confirmTitle').textContent   = title;
    document.getElementById('confirmMessage').textContent = message;

    // Clone to remove old listeners
    ['confirmProceed','confirmCancel'].forEach(id => {
        const el = document.getElementById(id);
        el.replaceWith(el.cloneNode(true));
    });

    document.getElementById('confirmProceed').textContent = confirmText;
    document.getElementById('confirmProceed').addEventListener('click', () => { modal.classList.add('hidden'); onConfirm?.(); });
    document.getElementById('confirmCancel').addEventListener('click',  () => modal.classList.add('hidden'));
    modal.classList.remove('hidden');
    modal.onclick = e => { if (e.target === modal) modal.classList.add('hidden'); };
}

function showInfoModal(message, title = 'Notice') {
    const modal = document.getElementById('infoModal');
    document.getElementById('infoTitle').textContent   = title;
    document.getElementById('infoMessage').textContent = message;

    const btn = document.getElementById('infoClose');
    btn.replaceWith(btn.cloneNode(true));
    document.getElementById('infoClose').addEventListener('click', () => modal.classList.add('hidden'));
    modal.classList.remove('hidden');
    modal.onclick = e => { if (e.target === modal) modal.classList.add('hidden'); };

    const esc = e => { if (e.key === 'Escape') { modal.classList.add('hidden'); document.removeEventListener('keydown', esc); } };
    document.addEventListener('keydown', esc);
}

// ===============================
// SERVICE ROWS
// ===============================
function addServiceRow() {
    const row = $('.service-row').first().clone();
    row.find('select[name="services[]"]').val('');
    row.find('select[name="levels[]"]').val('Standard');
    row.find('.level-div').addClass('hidden');
    $('#services_container').append(row);
}

function removeServiceRow(btn) {
    if ($('.service-row').length > 1) $(btn).closest('.service-row').remove();
}

function checkLevels(sel) {
    const hasLevels = $(sel).find(':selected').data('haslevels') == '1';
    const row = $(sel).closest('.service-row');
    row.find('.level-div').toggleClass('hidden', !hasLevels);
    const levelSel = row.find('select[name="levels[]"]');
    levelSel.val(hasLevels ? (levelSel.val() === 'Standard' || !levelSel.val() ? 'Simple' : levelSel.val()) : 'Standard');
}

function updateDurationBadge() {
    const hasService = $('select[name="services[]"]').toArray().some(s => s.value);
    $('#durationBadge').toggleClass('hidden', !hasService);
    if (hasService) $('#durationValue').text('Select date to calculate');
}

function togglePass(id, icon) {
    const el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
    $(icon).toggleClass('fa-eye fa-eye-slash');
}

// ===============================
// BLOOD PRESSURE
// ===============================
function combineBloodPressure() {
    const sys = document.getElementById('bp_systolic')?.value?.trim();
    const dia = document.getElementById('bp_diastolic')?.value?.trim();
    const combined = document.getElementById('blood_pressure_combined');
    if (!combined) return;

    if (sys && dia && /^\d+$/.test(sys) && /^\d+$/.test(dia)) {
        combined.value = `${sys}/${dia}`;
        document.getElementById('bp_systolic')?.classList.toggle('border-amber-400', +sys < 90 || +sys > 120);
        document.getElementById('bp_diastolic')?.classList.toggle('border-amber-400', +dia < 60 || +dia > 80);
    } else {
        combined.value = '';
    }
}

function validateBloodPressure() {
    const sys = +document.getElementById('bp_systolic')?.value?.trim();
    const dia = +document.getElementById('bp_diastolic')?.value?.trim();

    if (!sys || !dia) { showInfoModal('Please enter both Systolic and Diastolic blood pressure values.', 'Incomplete Input'); return false; }
    if (sys < 70 || sys > 200) { showInfoModal('Systolic value must be between 70-200 mmHg.', 'Invalid Value'); document.getElementById('bp_systolic')?.focus(); return false; }
    if (dia < 40 || dia > 130) { showInfoModal('Diastolic value must be between 40-130 mmHg.', 'Invalid Value'); document.getElementById('bp_diastolic')?.focus(); return false; }
    return true;
}

// ===============================
// AJAX ACTIONS
// ===============================
async function ajaxPost(url, formOrData, successCb, errorElId = null) {
    const isForm = formOrData instanceof HTMLFormElement;
    const body   = isForm ? new FormData(formOrData) : JSON.stringify(formOrData);
    const headers = { 'X-Requested-With': 'XMLHttpRequest' };
    if (!isForm) headers['Content-Type'] = 'application/json';

    try {
        const res    = await fetch(url, { method: 'POST', body, headers });
        const result = await res.json();
        if (result.success) { successCb(result); }
        else if (errorElId) {
            const el = document.getElementById(errorElId);
            if (el) { el.querySelector('span')?.textContent !== undefined ? el.querySelector('span').textContent = result.message : el.textContent = result.message; el.classList.remove('hidden'); }
        } else { showNotification(result.message || 'Request failed.', 'error'); }
    } catch (e) { showNotification('Network error. Please try again.', 'error'); }
}

async function handleCancellationRequest(id, action) {
    const csrf = getCsrfData();
    await ajaxPost(`<?= base_url('receptionist/appointments/handle-cancellation-request') ?>`,
        { appointment_id: id, action, [csrf.name]: csrf.token },
        result => { showNotification(result.message, action === 'approve' ? 'success' : 'warning'); setTimeout(() => location.reload(), 1000); }
    );
}

async function markAsCompleted(id) {
    const csrf = getCsrfData();
    await ajaxPost(`<?= base_url('receptionist/appointments/update-status') ?>/${id}/Completed`,
        { [csrf.name]: csrf.token },
        () => { showNotification('Appointment marked as completed.', 'success'); dismissPrompt(id); setTimeout(() => location.reload(), 800); }
    );
}

// ===============================
// NOTIFICATIONS
// ===============================
function showNotification(message, type = 'info', title = null, duration = 4000) {
    const icons = { success: 'fa-circle-check', error: 'fa-circle-xmark', warning: 'fa-triangle-exclamation', info: 'fa-circle-info' };
    const el = document.createElement('div');
    el.className = `notification notification--${type}`;
    el.setAttribute('role', 'alert');
    el.innerHTML = `
        <div class="notification__icon"><i class="fas ${icons[type]}"></i></div>
        <div class="notification__content">
            ${title ? `<div class="notification__title">${title}</div>` : ''}
            <div class="notification__message">${message}</div>
        </div>
        <button type="button" class="notification__close"><i class="fas fa-times text-sm"></i></button>`;

    el.querySelector('.notification__close').addEventListener('click', e => { e.stopPropagation(); hideNotification(el); });
    el.addEventListener('click', e => { if (!e.target.closest('button')) hideNotification(el); });
    document.getElementById('notificationContainer')?.appendChild(el);
    if (duration > 0) setTimeout(() => hideNotification(el), duration);
    return el;
}

function hideNotification(el) {
    el?.classList.add('hiding');
    setTimeout(() => el?.remove(), 200);
}

// ===============================
// COMPLETION PROMPT
// ===============================
function showCompletionPrompt(id, name, minutes) {
    if (document.getElementById(`prompt-${id}`)) return;
    const el = document.createElement('div');
    el.id = `prompt-${id}`;
    el.className = 'fixed bottom-4 right-4 z-[9999] max-w-sm w-full';
    el.innerHTML = `
        <div class="bg-white rounded-2xl shadow-2xl border-l-4 border-rose-500 overflow-hidden animate-slide-up">
            <div class="p-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-bell text-rose-600 text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-slate-800 text-sm uppercase tracking-wide">Appointment Completion Check</h4>
                        <p class="text-[11px] text-slate-600 mt-1"><strong>${name}</strong>'s scheduled time was <span class="text-rose-600 font-bold">${minutes} minutes ago</span>.</p>
                        <p class="text-[10px] text-slate-400 mt-2">Mark as completed if the patient has been attended to?</p>
                        <div class="flex gap-2 mt-4">
                            <button onclick="markAsCompleted(${id})" class="flex-1 px-3 py-2 bg-rose-600 hover:bg-rose-700 text-white text-[10px] font-bold rounded-xl transition-colors flex items-center justify-center gap-1.5"><i class="fas fa-check"></i> Yes, Completed</button>
                            <button onclick="dismissPrompt(${id})" class="flex-1 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold rounded-xl transition-colors flex items-center justify-center gap-1.5"><i class="fas fa-clock"></i> Remind Later</button>
                        </div>
                    </div>
                    <button onclick="dismissPrompt(${id})" class="text-slate-400 hover:text-slate-600 p-1"><i class="fas fa-times"></i></button>
                </div>
            </div>
        </div>`;
    document.body.appendChild(el);
    setTimeout(() => dismissPrompt(id), 30000);
}

function dismissPrompt(id) {
    const el = document.getElementById(`prompt-${id}`);
    if (el) { el.style.cssText = 'transition:opacity 0.2s;opacity:0'; setTimeout(() => el.remove(), 200); }
}

// ===============================
// INIT
// ===============================
$(document).ready(function () {
    loadAddressLevel('region');
    handleGenderLogic();
    toggleAccountType();
    combineBloodPressure();

    if ($('select[name="dentist_id"]').val() && $('input[name="appointment_date"]').val()) {
        setTimeout(checkTimeSlots, 300);
    }

    // Core triggers
    $('#tableSearch, #statusFilter, #dentistFilter').on('input change', applySearchFilter);
    $('#dateFilter').on('change', function () { if (this.value) location.href = `?tab=all&date=${this.value}`; });
    $('select[name="dentist_id"], input[name="appointment_date"], select[name="services[]"], select[name="levels[]"]').on('change', checkTimeSlots);
    $('select[name="services[]"]').on('change', updateDurationBadge);
    $('#appt_date').on('change', function () { $('#hidden_end_date, #end_date_visible').val(this.value); });
    $('select[name="gender"]').on('change', handleGenderLogic);
    $('[name="blood_pressure_systolic"], [name="blood_pressure_diastolic"]').on('input change', combineBloodPressure);

    // Name-only fields
    $(document).on('input', '.name-only', function () { this.value = this.value.replace(/[^a-zA-Z\s.\-]/g, ''); });

    // Mobile input formatting
    const mob = document.getElementById('primary_mobile');
    if (mob) {
        mob.addEventListener('focus', () => { if (!mob.value) mob.value = '09'; });
        mob.addEventListener('input', () => {
            let v = mob.value.replace(/\D/g, '');
            if (!v.startsWith('09')) v = '09' + v.substring(2);
            v = v.substring(0, 11);
            mob.value = v.length > 7 ? `${v.slice(0,4)} ${v.slice(4,7)} ${v.slice(7)}` : v.length > 4 ? `${v.slice(0,4)} ${v.slice(4)}` : v;
        });
    }

    // Select2 patient search
    $('#patient_search').select2({
        dropdownParent: $('#apptModal'),
        placeholder: 'Search by Name or Patient Code...',
        minimumInputLength: 2,
        width: '100%',
        ajax: {
            url: API_BASE + '/searchPatients',
            dataType: 'json',
            delay: 250,
            data: p => ({ term: p.term }),
            processResults: d => ({ results: d }),
        },
    });

    // Confirm/Complete button handlers
    $(document).on('click', '.confirm-appointment-btn, .confirm-completion-btn', function (e) {
        e.preventDefault();
        const url = $(this).attr('href');
        showConfirmModal($(this).data('message') || 'Are you sure?', () => location.href = url, $(this).data('title'), $(this).data('confirm-text') || 'Confirm');
    });

    // Form submit: remap levels + validate
    document.getElementById('apptForm')?.addEventListener('submit', function (e) {
        // Remap levels[] to levels[svcId]
        document.querySelectorAll('.service-row').forEach(row => {
            const svc = row.querySelector('select[name="services[]"]');
            const lvl = row.querySelector('select[name="levels[]"]');
            if (svc?.value && lvl) lvl.name = `levels[${svc.value}]`;
        });

        // Validate BP on step 2 for new patients
        const isNew = document.querySelector('input[name="account_type"]:checked')?.value === 'new';
        const step2 = document.getElementById('step2_container');
        if (step2 && !step2.classList.contains('step-hidden') && isNew && !validateBloodPressure()) {
            e.preventDefault(); return false;
        }

        // Slot validation
        if (!selectedSlot || !$('#hidden_appt_time').val() || !$('#hidden_end_time').val()) {
            e.preventDefault();
            $('#slotError').html("<i class='fas fa-exclamation-triangle'></i> Please select a valid time slot.").removeClass('hidden');
            document.getElementById('timeSlotSection')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }

        // Address: use text as value
        ['reg_select','prov_select','city_select','brgy_select'].forEach(id => {
            const s = document.getElementById(id);
            if (s?.selectedIndex > 0) s.options[s.selectedIndex].value = s.options[s.selectedIndex].text;
        });

        combineBloodPressure();

        const bt = document.getElementById('bleeding_time_combined');
        if (bt) bt.value = `${document.getElementById('bleeding_mins')?.value || '0'}m ${document.getElementById('bleeding_secs')?.value || '0'}s`;

        if (mob) mob.value = mob.value.replace(/\s/g, '');
    });

    // AJAX form handlers: cancel, reject, deny-cancel
    const ajaxForms = [
        { formId: 'cancelForm',     closeF: closeCancelModal,    errId: 'cancel_error',      successType: 'success' },
        { formId: 'rejectForm',     closeF: closeRejectModal,    errId: 'reject_error',      successType: 'success' },
        { formId: 'denyCancelForm', closeF: closeDenyCancelModal, errId: 'deny_cancel_error', successType: 'warning' },
    ];

    ajaxForms.forEach(({ formId, closeF, errId, successType }) => {
        document.getElementById(formId)?.addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const orig = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Processing...';
            document.getElementById(errId)?.classList.add('hidden');

            await ajaxPost(this.action, this,
                result => { showNotification(result.message, successType); closeF(); setTimeout(() => location.reload(), 1000); },
                errId
            );

            btn.disabled = false;
            btn.innerHTML = orig;
        });
    });

    // Global Escape key + backdrop close for modals
    const modalClosers = {
        cancelModal:    closeCancelModal,
        denyCancelModal: closeDenyCancelModal,
    };
    document.addEventListener('keydown', e => { if (e.key === 'Escape') Object.values(modalClosers).forEach(f => f()); });
    Object.entries(modalClosers).forEach(([id, fn]) => {
        document.getElementById(id)?.addEventListener('click', function (e) { if (e.target === this) fn(); });
    });
});