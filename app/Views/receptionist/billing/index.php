<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<!-- Signature Pad Library -->
<!-- 1. JQUERY (Dapat ito ang pinakauna sa lahat ng scripts) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- 2. SELECT2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- 3. SIGNATURE PAD -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<style>
    .select2-container .select2-selection--single {
        height: 45px !important;
        border-radius: 0.75rem !important;
        border-color: #e2e8f0 !important;
        padding-top: 8px !important;
    }
</style>
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-2xl font-bold text-slate-800 italic uppercase tracking-tighter">🧾 Billing & Treatment Record</h3>
    </div>

    <!-- MAIN FORM -->
    <form id="billingForm" class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden mb-10">
        <?= csrf_field() ?>
        <input type="hidden" name="visit_id" id="visit_id">
        <input type="hidden" name="appointment_id" id="appointment_id">
        <input type="hidden" name="dentist_id" id="dentist_id">
        <input type="hidden" name="service_id" id="service_id">

        <div class="p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Patient Search -->
                <div class="p-4 bg-slate-50 rounded-xl border">
                    <label class="block text-[10px] font-black text-blue-600 uppercase mb-2">Search Patient *</label>
                    <select name="patient_id" id="patient_search" class="w-full" required></select>
                </div>
                <!-- Auto-filled Dentist -->
                <div class="p-4 rounded-xl border auto-filled transition-all">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">Assigned Dentist (Auto)</label>
                    <input type="text" id="display_dentist" readonly class="w-full bg-transparent font-bold text-slate-700 outline-none" placeholder="Awaiting Patient Selection...">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Procedure / Service Selection -->
                <div class="md:col-span-2 p-4 rounded-xl border bg-white transition-all" id="service_container">
                    <label class="block text-[10px] font-black text-blue-600 uppercase mb-2">Select Item to Bill/Pay *</label>
                    <select id="billable_item_selector" class="w-full p-2 border-b font-bold outline-none bg-transparent">
                        <option value="">-- Choose Appointment or Balance --</option>
                    </select>
                </div>
                <!-- Manual Input: Tooth No -->
                <div class="p-4 bg-white rounded-xl border">
                    <label class="block text-[10px] font-black text-slate-500 uppercase mb-2">Tooth No.</label>
                    <input type="text" name="tooth_number" placeholder="e.g. 14, 15" class="w-full p-1 border-b font-bold outline-none focus:border-blue-500">
                </div>
            </div>

            <!-- PRICING SECTION -->
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
                            class="w-full bg-transparent text-2xl font-black text-slate-800 outline-none cursor-not-allowed"
                            value="0.00">
                    </div>
                    <div class="absolute top-2 right-3">
                        <span class="text-[10px] font-bold text-slate-400 italic">Locked</span>
                    </div>
                </div>

                <!-- Amount Paid (Interactive) -->
                <div class="relative p-5 bg-white rounded-2xl border-2 border-green-500 shadow-md transform scale-105 z-10 transition-all">
                    <label class="block text-[10px] font-black text-green-600 uppercase tracking-widest mb-2">
                        Amt. Paid (Input)
                    </label>
                    <div class="flex items-center">
                        <span class="text-xl font-black text-green-600 mr-2">₱</span>
                        <input type="number" name="amount_paid" id="amount_paid" step="any" oninput="calculateLogic()"
                            class="w-full bg-transparent text-3xl font-black text-slate-900 outline-none focus:ring-0 placeholder-green-200"
                            placeholder="0.00" autofocus>
                    </div>
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-green-500 text-white px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter">
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
                            class="w-full bg-transparent text-2xl font-black text-slate-800 outline-none cursor-not-allowed"
                            value="0.00">
                    </div>
                </div>

            </div>

            <!-- Consent & Signature -->
            <div class="p-4 bg-blue-50/50 border border-blue-100 rounded-2xl">
                <label class="flex items-center gap-2 cursor-pointer mb-3">
                    <input type="checkbox" name="consent_given" value="1" class="w-4 h-4 rounded">
                    <span class="text-[10px] font-black text-blue-800 uppercase italic">Patient Consent Given</span>
                </label>
                <label class="block text-[9px] font-black text-slate-400 uppercase mb-1">Patient Signature</label>
                <div class="bg-white border rounded-lg overflow-hidden">
                    <canvas id="signature-pad" class="w-full h-32"></canvas>
                </div>
                <button type="button" id="clear-signature" class="text-[9px] text-red-500 font-bold uppercase mt-1">Clear Signature</button>
                <input type="hidden" name="signature_data" id="signature_data">
            </div>
        </div>


        <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-xl font-black uppercase text-xs tracking-widest shadow-xl hover:bg-blue-700 transition-all">Confirm & Save Treatment</button>
</div>
</form>

<!-- COMPLIANCE TABLE (ETO YUNG GUSTO MO) -->
<div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
    <div class="bg-slate-50 p-4 border-b">
        <h5 class="text-xs font-black text-slate-600 uppercase italic">Treatment Records History</h5>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-100 text-[10px] font-black uppercase text-slate-500">
                <tr>
                    <th class="p-4">Date</th>
                    <th class="p-4">Tooth No</th>
                    <th class="p-4">Procedure</th>
                    <th class="p-4">Dentist</th>
                    <th class="p-4 text-center">Patient Consent</th>
                    <th class="p-4">Signature</th>
                    <th class="p-4">Amt. Charge</th>
                    <th class="p-4">Amt. Paid</th>
                    <th class="p-4">Balance</th>
                </tr>
            </thead>
            <tbody id="treatment_history" class="divide-y">
                <!-- Data will be loaded via AJAX or PHP Loop -->
                <tr class="hover:bg-slate-50 transition-all">
                    <td colspan="9" class="p-10 text-center text-slate-400 italic">Select a patient to view history...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</div>

<script>
    // Signature Pad Initialization
    const canvas = document.getElementById('signature-pad');
    const signaturePad = new SignaturePad(canvas);
    document.getElementById('clear-signature').addEventListener('click', () => signaturePad.clear());

    $(document).ready(function() {
        // 1. Patient Search - Integrated with your Controller
        $('#patient_search').select2({
            placeholder: 'Search Name or Code...',
            width: '100%',
            ajax: {
                url: '<?= base_url('receptionist/appointments/searchPatients') ?>',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.id,
                                // Pinagsama natin name at code para madaling hanapin
                                text: (item.name || (item.first_name + ' ' + item.last_name)) +
                                    (item.patient_code ? ' [' + item.patient_code + ']' : '')
                            };
                        })
                    };
                }
            }
        });

        // 2. Automated Fetching
        $('#patient_search').on('change', function() {
            let patientId = $(this).val();
            if (!patientId) return;

            loadPatientHistory(patientId);

            $.get('<?= base_url('receptionist/billing/getActiveDetails') ?>/' + patientId, function(res) {
                let selector = $('#billable_item_selector');
                selector.empty().append('<option value="">-- Choose Appointment or Balance --</option>');

                if (res.status === 'success') {
                    // I-populate ang dropdown
                    res.items.forEach((item, index) => {
                        let option = $('<option></option>')
                            .attr('value', index)
                            .text(item.display_text)
                            .data('details', item);
                        selector.append(option);
                    });

                    // UI Feedback
                    $('#service_container').addClass('border-blue-500 ring-2 ring-blue-100');

                    // Kapag namili sa dropdown
                    selector.off('change').on('change', function() {
                        let selectedIndex = $(this).val();
                        if (selectedIndex === "") {
                            resetBillingFields();
                            return;
                        }

                        let data = $(this).find(':selected').data('details');

                        // I-fill ang form
                        $('#visit_id').val(data.visit_id);
                        $('#appointment_id').val(data.type === 'appointment' ? data.id : '');
                        $('#dentist_id').val(data.dentist_id);
                        $('#service_id').val(data.service_id);
                        $('#display_dentist').val(data.dentist_name);
                        $('#amount_charge').val(data.amount_charge);
                        $('input[name="tooth_number"]').val(data.tooth_number);

                        // Kung balance ang pinili, gawing red ang text ng amount charge
                        if (data.type === 'balance') {
                            $('#amount_charge').addClass('text-yellow-400');
                        } else {
                            $('#amount_charge').removeClass('text-yellow-400');
                        }

                        $('#amount_paid').val('0.00').focus().select();
                        calculateLogic();
                    });

                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'No Record',
                        text: res.message
                    });
                    resetBillingForm();
                }
            });
        });

    });

    // 3. Automation of Math
    // Helper Function para linisin ang form

    // Helper Function: Siguraduhin na ang calculation ay laging tama
    function calculateLogic() {
        let charge = parseFloat($('#amount_charge').val()) || 0;
        let paid = parseFloat($('#amount_paid').val()) || 0;

        let balance = charge - paid;

        // UI Feedback para sa Balance
        if (balance <= 0) {
            $('#balance').val("0.00").removeClass('text-red-600').addClass('text-green-600');
        } else {
            $('#balance').val(balance.toFixed(2)).addClass('text-red-600').removeClass('text-green-600');
        }
    }

    // Helper Function: Full Reset para sa Form
    function resetBillingForm() {
        $('#billingForm')[0].reset(); // Reset normal inputs
        $('#patient_search').val(null).trigger('change.select2'); // Reset Select2
        $('#appointment_id, #visit_id, #dentist_id, #service_id').val(''); // Clear hidden IDs
        $('#display_dentist').val('Awaiting Patient Selection...');
        $('#display_service').val('No active procedure found...');
        $('.auto-filled').removeClass('bg-blue-50 border-blue-300 bg-red-50 border-red-300');
    }


    function resetBillingFields() {
        $('#visit_id, #appointment_id, #dentist_id, #service_id').val('');
        $('#display_dentist').val('Awaiting selection...');
        $('#amount_charge, #amount_paid, #balance').val('0.00');
    }

    // Function to Load History to Table
    function loadPatientHistory(patientId) {
        $.get('<?= base_url('receptionist/billing/history/') ?>' + patientId, function(data) {
            let rows = '';
            data.forEach(r => {
                rows += `
                <tr class="hover:bg-slate-50 border-b">
                    <td class="p-4 font-bold text-slate-700">${r.treatment_date}</td>
                    <td class="p-4 font-bold text-blue-600">${r.tooth_number || 'N/A'}</td>
                    <td class="p-4">${r.service_name}</td>
                    <td class="p-4 font-medium text-slate-600">Dr. ${r.dentist_name}</td>
                    <td class="p-4 text-center">
                        ${r.consent_given == 1 ? '<span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-[9px] font-black uppercase">Yes</span>' : '<span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-[9px] font-black uppercase">No</span>'}
                    </td>
                    <td class="p-4">
                        ${r.signature_path ? `<img src="<?= base_url() ?>${r.signature_path}" class="h-8 border bg-white rounded">` : '<span class="text-slate-300 italic">None</span>'}
                    </td>
                    <td class="p-4 font-bold">₱${r.amount_charge}</td>
                    <td class="p-4 text-green-600 font-bold">₱${r.amount_paid}</td>
                    <td class="p-4 text-red-600 font-bold">₱${r.balance}</td>
                </tr>`;
            });
            $('#treatment_history').html(rows || '<tr><td colspan="9" class="p-10 text-center text-slate-400 italic">No records found for this patient.</td></tr>');
        });
    }

    $('#billingForm').submit(function(e) {
        e.preventDefault();

        // Convert Signature Pad to Base64
        if (!signaturePad.isEmpty()) {
            document.getElementById('signature_data').value = signaturePad.toDataURL();
        }

        $.post('<?= base_url('receptionist/billing/save') ?>', $(this).serialize(), function(res) {
            alert(res.message);
            if (res.status === 'success') {
                location.reload();
            }
        });
    });
</script>
<?= $this->endSection() ?>