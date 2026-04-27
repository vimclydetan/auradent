<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<style>
    /* Select2 Responsive Fix */
    .select2-container .select2-selection--single {
        height: 45px !important;
        border-radius: 0.75rem !important;
        border-color: #e2e8f0 !important;
        padding-top: 8px !important;
    }

    /* Signature Pad Touch Fix */
    #signature-pad {
        touch-action: none;
        cursor: crosshair;
    }

    .table-container {
        position: relative;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
</style>

<div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-6 py-4">
    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h3 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight italic uppercase flex items-center gap-2">
                <i class="fas fa-money-bill text-blue-600 mr-2"></i>
                Create Bill
            </h3>
            <p class="text-[10px] sm:text-xs text-slate-500">
                Record and manage patient billing and treatment details.
            </p>
        </div>
    </div>

    <!-- MAIN FORM -->
    <form id="billingForm" class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden mb-10">
        <?= csrf_field() ?>
        <input type="hidden" name="visit_id" id="visit_id">
        <input type="hidden" name="appointment_id" id="appointment_id">
        <input type="hidden" name="dentist_id" id="dentist_id">
        <input type="hidden" name="service_id" id="service_id">

        <div class="p-4 sm:p-6 lg:p-8 space-y-6">
            <!-- Row 1: Patient & Dentist -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <!-- Patient Search -->
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                    <label class="block text-[10px] font-black text-blue-600 uppercase mb-2">Search Patient *</label>
                    <div class="relative w-full">
                        <select name="patient_id" id="patient_search" class="w-full" required></select>
                    </div>
                </div>
                <!-- Assigned Dentist (Dynamic: Readonly OR Selectable) -->
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 transition-all" id="dentist_container">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-2" id="dentist_label">Assigned Dentist</label>

                    <!-- Display For Appt/Balance -->
                    <input type="text" id="display_dentist" readonly class="w-full bg-transparent font-bold text-slate-700 outline-none text-sm sm:text-base" placeholder="Awaiting Patient Selection...">

                    <!-- Select For Walk-ins -->
                    <select id="select_dentist" class="w-full bg-transparent font-bold text-blue-600 outline-none text-sm sm:text-base hidden">
                        <option value="">-- Choose Dentist --</option>
                    </select>
                </div>
            </div>

            <!-- Row 2: Service & Tooth No -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                <!-- Procedure Selection -->
                <div class="md:col-span-2 p-4 rounded-xl border border-slate-200 bg-white transition-all" id="service_container">
                    <label class="block text-[10px] font-black text-blue-600 uppercase mb-2">Select Item to Bill/Pay *</label>
                    <select id="billable_item_selector" class="w-full p-2 border-b font-bold outline-none bg-transparent text-sm sm:text-base">
                        <option value="">-- Search Patient First --</option>
                    </select>
                </div>
                <!-- Manual Input: Tooth No -->
                <div class="p-4 bg-white rounded-xl border border-slate-200">
                    <label class="block text-[10px] font-black text-slate-500 uppercase mb-2">Tooth No.</label>
                    <input type="text" name="tooth_number" placeholder="e.g. 14, 15" class="w-full p-1 border-b font-bold outline-none focus:border-blue-500 text-sm sm:text-base">
                </div>
            </div>

            <!-- PRICING SECTION -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 border-t pt-8">

                <!-- Amount Charge (Read Only) -->
                <div class="relative p-5 bg-slate-100 rounded-2xl border border-slate-200 shadow-sm transition-all">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">
                        Amt. Charge (Total)
                    </label>
                    <div class="flex items-center">
                        <span class="text-xl font-black text-slate-400 mr-2">₱</span>
                        <input type="number" name="amount_charge" id="amount_charge" step="any" readonly
                            class="w-full bg-transparent text-xl sm:text-2xl font-black text-slate-800 outline-none cursor-not-allowed"
                            value="0.00">
                    </div>
                    <div class="absolute top-2 right-3">
                        <span class="text-[9px] font-bold text-slate-400 italic">Locked</span>
                    </div>
                </div>

                <!-- Amount Paid (Interactive) -->
                <div class="relative p-5 bg-white rounded-2xl border-2 border-green-500 shadow-md md:transform md:scale-105 z-10 transition-all">
                    <label class="block text-[10px] font-black text-green-600 uppercase tracking-widest mb-2">
                        Amt. Paid (Input)
                    </label>
                    <div class="flex items-center">
                        <span class="text-xl font-black text-green-600 mr-2">₱</span>
                        <input type="number" name="amount_paid" id="amount_paid" step="any" oninput="calculateLogic()"
                            class="w-full bg-transparent text-2xl sm:text-3xl font-black text-slate-900 outline-none focus:ring-0 placeholder-green-200"
                            placeholder="0.00" autofocus>
                    </div>
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-green-500 text-white px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter whitespace-nowrap">
                        Enter Payment Here
                    </div>
                </div>

                <!-- Balance (Dynamic Color) -->
                <div id="balance_container" class="relative p-5 bg-slate-100 rounded-2xl border border-slate-200 shadow-sm transition-all">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">
                        Remaining Balance
                    </label>
                    <div class="flex items-center">
                        <span class="text-xl font-black text-slate-400 mr-2">₱</span>
                        <input type="number" name="balance" id="balance" step="any" readonly
                            class="w-full bg-transparent text-xl sm:text-2xl font-black text-slate-800 outline-none cursor-not-allowed"
                            value="0.00">
                    </div>
                </div>
            </div>

            <!-- Consent & Signature -->
            <div class="p-4 bg-blue-50/50 border border-blue-100 rounded-2xl">
                <label class="flex items-center gap-2 cursor-pointer mb-3">
                    <input type="checkbox" name="consent_given" value="1" class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500">
                    <span class="text-[10px] font-black text-blue-800 uppercase italic">Patient Consent Given</span>
                </label>
                <label class="block text-[9px] font-black text-slate-400 uppercase mb-1">Patient Signature</label>
                <div class="bg-white border rounded-lg overflow-hidden touch-none">
                    <canvas id="signature-pad" class="w-full h-32 md:h-40 bg-white"></canvas>
                </div>
                <button type="button" id="clear-signature" class="text-[9px] text-red-500 font-bold uppercase mt-2 inline-flex items-center gap-1">
                    <i class="fas fa-eraser"></i> Clear Signature
                </button>
                <input type="hidden" name="signature_data" id="signature_data">
            </div>
        </div>

        <div class="px-4 pb-8 sm:px-8 sm:pb-8">
            <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-xl font-black uppercase text-xs tracking-widest shadow-xl hover:bg-blue-700 active:scale-[0.98] transition-all">
                Confirm & Save Transaction
            </button>
        </div>
    </form>

    <!-- COMPLIANCE TABLE -->
    <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden mb-10">
        <div class="bg-slate-50 p-4 border-b">
            <h5 class="text-xs font-black text-slate-600 uppercase italic">Treatment Records History</h5>
        </div>
        <div class="table-container">
            <table class="w-full text-left text-[11px] sm:text-xs">
                <thead class="bg-slate-100 text-[9px] sm:text-[10px] font-black uppercase text-slate-500">
                    <tr>
                        <th class="p-3 sm:p-4 whitespace-nowrap">Date</th>
                        <th class="p-3 sm:p-4 whitespace-nowrap">Tooth No</th>
                        <th class="p-3 sm:p-4 whitespace-nowrap">Procedure</th>
                        <th class="p-3 sm:p-4 whitespace-nowrap">Dentist</th>
                        <th class="p-3 sm:p-4 text-center">Consent</th>
                        <th class="p-3 sm:p-4">Signature</th>
                        <th class="p-3 sm:p-4">Amt. Charge</th>
                        <th class="p-3 sm:p-4">Amt. Paid</th>
                        <th class="p-3 sm:p-4">Balance</th>
                    </tr>
                </thead>
                <tbody id="treatment_history" class="divide-y">
                    <tr class="hover:bg-slate-50 transition-all">
                        <td colspan="9" class="p-10 text-center text-slate-400 italic">Select a patient to view history...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- DAISYUI MODAL & TOAST ELEMENTS -->
<!-- ========================================== -->
<!-- Loading Modal -->
<dialog id="daisyLoadingModal" class="modal modal-middle">
    <div class="modal-box bg-white p-6 text-center flex flex-col items-center justify-center gap-4 rounded-3xl shadow-xl max-w-[320px]">
        <span class="loading loading-spinner loading-lg text-blue-600"></span>
        <h3 class="font-black text-sm text-slate-800 uppercase tracking-wide">Processing...</h3>
        <p class="text-[11px] text-slate-500 font-medium">Please wait, saving transaction data.</p>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<!-- Alert Modal (Success, Error, Info, Warning) -->
<dialog id="daisyAlertModal" class="modal modal-middle">
    <!-- Backdrop -->
    <form method="dialog" class="modal-backdrop bg-slate-900/40 backdrop-blur-md">
        <button type="button" class="w-full h-12 px-6 mt-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all duration-200 cursor-pointer border-0 hover:shadow-lg active:scale-[0.98]">
            Close Backdrop
        </button>
    </form>

    <!-- Modal Container -->
    <div class="modal-box bg-white rounded-[1.25rem] shadow-2xl p-6 sm:p-8 max-w-[360px] text-center flex flex-col items-center justify-center gap-4 border border-slate-100">

        <!-- Dynamic Icon Container -->
        <div id="daisyAlertIcon" class="w-16 h-16 sm:w-20 sm:h-20 rounded-full flex items-center justify-center shadow-lg transition-all duration-300 icon-pulse">
            <!-- SVG injected via JS -->
        </div>

        <!-- Title -->
        <h3 id="daisyAlertTitle" class="text-base sm:text-lg font-black text-slate-900 uppercase tracking-wide leading-tight"></h3>

        <!-- Message -->
        <p id="daisyAlertMessage" class="text-sm text-slate-500 font-medium leading-relaxed -mt-1 px-4"></p>

        <!-- Action Button -->
        <button id="daisyAlertBtn" class="w-full h-12 px-6 mt-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all duration-200 cursor-pointer border-0 hover:shadow-lg active:scale-[0.98]">
            OK
        </button>
    </div>
</dialog>
<!-- ========================================== -->
<!-- END OF DAISYUI MODALS -->
<!-- ========================================== -->


<script>
    let signaturePad;
    const canvas = document.getElementById('signature-pad');

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        if (signaturePad) {
            signaturePad.clear();
        }
    }

    window.addEventListener("load", function() {
        resizeCanvas();
        signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)'
        });
    });

    window.addEventListener("resize", resizeCanvas);

    document.getElementById('clear-signature').addEventListener('click', () => {
        if (signaturePad) signaturePad.clear();
    });

    // Helper Functions for DaisyUI Modals
    function showLoading() {
        document.getElementById('daisyLoadingModal').showModal();
    }

    function hideLoading() {
        document.getElementById('daisyLoadingModal').close();
    }

    // Improved Alert Modal Function
    function showAlert(type = 'info', title = '', message = '', allowHtml = false) {
        const modal = document.getElementById('daisyAlertModal');
        const iconEl = document.getElementById('daisyAlertIcon');
        const titleEl = document.getElementById('daisyAlertTitle');
        const msgEl = document.getElementById('daisyAlertMessage');
        const btnEl = document.getElementById('daisyAlertBtn');

        titleEl.textContent = title;

        // Use innerHTML if HTML is allowed, otherwise use textContent for security
        if (allowHtml) {
            msgEl.innerHTML = message;
        } else {
            msgEl.textContent = message;
        }

        // Reset icon base classes
        iconEl.className = 'w-16 h-16 sm:w-20 sm:h-20 rounded-full flex items-center justify-center shadow-lg transition-all duration-300 icon-pulse';

        const themes = {
            success: {
                iconBg: 'bg-emerald-100',
                iconColor: 'text-emerald-600',
                btnBg: 'bg-gradient-to-r from-emerald-500 to-emerald-600',
                btnText: 'text-white',
                btnHover: 'hover:from-emerald-600 hover:to-emerald-700',
                boxShadow: 'shadow-[0_8px_24px_rgba(16,185,129,0.25)]',
                iconSvg: `<svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>`
            },
            error: {
                iconBg: 'bg-rose-100',
                iconColor: 'text-rose-600',
                btnBg: 'bg-gradient-to-r from-rose-500 to-rose-600',
                btnText: 'text-white',
                btnHover: 'hover:from-rose-600 hover:to-rose-700',
                boxShadow: 'shadow-[0_8px_24px_rgba(244,63,94,0.25)]',
                iconSvg: `<svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>`
            },
            warning: {
                iconBg: 'bg-amber-100',
                iconColor: 'text-amber-600',
                btnBg: 'bg-gradient-to-r from-amber-400 to-amber-500',
                btnText: 'text-white',
                btnHover: 'hover:from-amber-500 hover:to-amber-600',
                boxShadow: 'shadow-[0_8px_24px_rgba(245,158,11,0.25)]',
                iconSvg: `<svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`
            },
            info: {
                iconBg: 'bg-sky-100',
                iconColor: 'text-sky-600',
                btnBg: 'bg-gradient-to-r from-sky-500 to-sky-600',
                btnText: 'text-white',
                btnHover: 'hover:from-sky-600 hover:to-sky-700',
                boxShadow: 'shadow-[0_8px_24px_rgba(14,165,233,0.25)]',
                iconSvg: `<svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`
            }
        };

        const t = themes[type] || themes.info;

        iconEl.classList.add(t.iconBg, t.iconColor);
        iconEl.innerHTML = t.iconSvg;

        // Apply dynamic button classes
        btnEl.className = `w-full h-12 px-6 mt-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all duration-200 cursor-pointer border-0 ${t.btnBg} ${t.btnText} ${t.btnHover} ${t.boxShadow}`;

        // Set button text
        btnEl.textContent = type === 'success' ? 'Done' : 'Close';

        // Close handler - NOW WORKS PROPERLY
        modal.addEventListener('close', () => {
            console.log('Modal closed:', type);
        });

        modal.showModal();
    }

    // Legacy function with HTML support for reload confirmation
    function showAlertModal(title, message, type = 'info', reloadOnClose = false) {
        // Store reload flag globally
        window.alertReloadOnClose = reloadOnClose;

        if (reloadOnClose) {
            showAlert(type, title, `${message}`, true);
        } else {
            showAlert(type, title, message, false);
        }
    }

    // 👇 Usage Example (Exact match sa image mo)
    // showAlert('success', 'SUCCESS!', 'Transaction saved successfully!');
    $(document).ready(function() {
        $('#daisyAlertBtn').on('click', function(e) {
            e.preventDefault();
            const modal = document.getElementById('daisyAlertModal');
            modal.close();

            if (window.alertReloadOnClose) {
                location.reload();
                delete window.alertReloadOnClose;
            }
        });

        // FIX: Click on backdrop also closes modal
        $('.modal-backdrop button').on('click', function(e) {
            e.preventDefault();
            const modal = $(this).closest('.modal').first().attr('id') === 'daisyLoadingModal' ?
                document.getElementById('daisyLoadingModal') :
                document.getElementById('daisyAlertModal');
            if (modal) modal.close();
        });
        $('#patient_search').select2({
            placeholder: 'Search Name or Code...',
            width: '100%',
            ajax: {
                url: '<?= base_url('receptionist/appointments/searchPatients') ?>',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            minimumInputLength: 1
        });

        $('#patient_search').on('change', function() {
            let patientId = $(this).val();
            if (!patientId) return;

            loadPatientHistory(patientId);

            $.get('<?= base_url('receptionist/billing/details') ?>/' + patientId, function(res) {
                let selector = $('#billable_item_selector');
                selector.empty().append('<option value="">-- Choose Walk-in, Appointment, or Balance --</option>');

                if (res.status === 'success') {
                    let balancesHTML = '';
                    let appointmentsHTML = '';
                    let walkinsHTML = '';

                    res.items.forEach((item, index) => {
                        let val = encodeURIComponent(JSON.stringify(item));
                        let option = `<option value="${val}">${item.display_text}</option>`;

                        if (item.type === 'balance') balancesHTML += option;
                        else if (item.type === 'appointment') appointmentsHTML += option;
                        else if (item.type === 'walkin') walkinsHTML += option;
                    });

                    if (balancesHTML) selector.append(`<optgroup label="🔴 Outstanding Balances">${balancesHTML}</optgroup>`);
                    if (walkinsHTML) selector.append(`<optgroup label="- Pending Walk-ins -">${walkinsHTML}</optgroup>`);
                    if (appointmentsHTML) selector.append(`<optgroup label="- Pending Appointments -">${appointmentsHTML}</optgroup>`);

                    $('#service_container').addClass('border-blue-500 ring-2 ring-blue-100');

                    selector.off('change').on('change', function() {
                        let selectedValue = $(this).val();
                        if (!selectedValue) {
                            resetBillingFields();
                            return;
                        }

                        let data = JSON.parse(decodeURIComponent(selectedValue));

                        $('#visit_id').val(data.visit_id || '');
                        $('#appointment_id').val(data.appointment_id || '');
                        $('#dentist_id').val(data.dentist_id);
                        $('#service_id').val(data.service_id);
                        $('#display_dentist').val(data.dentist_name);
                        $('#amount_charge').val(data.amount_charge);
                        $('input[name="tooth_number"]').val(data.tooth_number || '');

                        if (data.type === 'balance') {
                            $('#amount_charge').addClass('text-yellow-600');
                        } else {
                            $('#amount_charge').removeClass('text-yellow-600');
                        }

                        $('#amount_paid').val('0.00').focus().select();
                        calculateLogic();
                    });
                } else {
                    showAlertModal('No Records Found', res.message, 'info');
                    resetBillingFields();
                }
            });
        });

        $('#billingForm').submit(function(e) {
            e.preventDefault();
            let paid = parseFloat($('#amount_paid').val()) || 0;

            if (paid <= 0) {
                showAlertModal('Invalid Amount', 'Please enter a payment higher than zero.', 'warning');
                return false;
            }

            if (signaturePad && !signaturePad.isEmpty()) {
                document.getElementById('signature_data').value = signaturePad.toDataURL();
            }

            // Show DaisyUI Loading Modal
            showLoading();

            $.ajax({
                url: '<?= base_url('receptionist/billing/save') ?>',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    hideLoading(); // Hide DaisyUI Loading Modal

                    if (res.status === 'success') {
                        showAlertModal('Success!', 'Transaction saved successfully!', 'success', true);
                    } else {
                        showAlertModal('Transaction Failed', res.message, 'error');
                    }
                },
                error: function() {
                    hideLoading();
                    showAlertModal('System Error', 'Connection lost or database failed.', 'error');
                }
            });
        });
    });

    function calculateLogic() {
        let charge = parseFloat($('#amount_charge').val()) || 0;
        let paid = parseFloat($('#amount_paid').val()) || 0;
        let balance = charge - paid;

        if (balance <= 0) {
            $('#balance').val("0.00").removeClass('text-red-600').addClass('text-green-600');
        } else {
            $('#balance').val(balance.toFixed(2)).addClass('text-red-600').removeClass('text-green-600');
        }
    }

    function resetBillingFields() {
        $('#visit_id, #appointment_id, #dentist_id, #service_id').val('');
        $('#display_dentist').val('Awaiting selection...');
        $('#amount_charge, #amount_paid, #balance').val('0.00');
    }

    function loadPatientHistory(patientId) {
        $.get('<?= base_url('receptionist/billing/history/') ?>' + patientId, function(data) {
            let rows = '';
            data.forEach(r => {
                rows += `
                <tr class="hover:bg-slate-50 border-b">
                    <td class="p-3 sm:p-4 font-bold text-slate-700 whitespace-nowrap">${r.treatment_date}</td>
                    <td class="p-3 sm:p-4 font-bold text-blue-600">${r.tooth_number || 'N/A'}</td>
                    <td class="p-3 sm:p-4">${r.service_name}</td>
                    <td class="p-3 sm:p-4 font-medium text-slate-600 whitespace-nowrap">Dr. ${r.dentist_name}</td>
                    <td class="p-3 sm:p-4 text-center">
                        ${r.consent_given == 1 ? '<span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-[8px] font-black uppercase">Yes</span>' : '<span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-[8px] font-black uppercase">No</span>'}
                    </td>
                    <td class="p-3 sm:p-4">
                        ${r.signature_path ? `<img src="<?= base_url() ?>${r.signature_path}" class="h-6 sm:h-8 border bg-white rounded">` : '<span class="text-slate-300 italic">None</span>'}
                    </td>
                    <td class="p-3 sm:p-4 font-bold">₱${r.amount_charge}</td>
                    <td class="p-3 sm:p-4 text-green-600 font-bold">₱${r.amount_paid}</td>
                    <td class="p-3 sm:p-4 text-red-600 font-bold">₱${r.balance}</td>
                </tr>`;
            });
            $('#treatment_history').html(rows || '<tr><td colspan="9" class="p-10 text-center text-slate-400 italic">No records found for this patient.</td></tr>');
        });
    }
</script>
<?= $this->endSection() ?>