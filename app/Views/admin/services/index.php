<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h3 class="text-2xl font-bold text-slate-800">🦷 Dental Services</h3>
        <p class="text-sm text-slate-500">Manage treatments and pricing levels.</p>
    </div>
    <button onclick="openModal('add')" class="bg-blue-600 text-white px-5 py-2.5 rounded-lg hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all flex items-center justify-center">
        <i class="fas fa-plus mr-2"></i> Add New Service
    </button>
</div>

<!-- SEARCH & FILTER -->
<div class="bg-white p-4 rounded-t-xl border border-slate-200 flex flex-col md:flex-row gap-4 justify-between">
    <div class="relative w-full md:w-72">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
            <i class="fas fa-search"></i>
        </span>
        <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search service..." class="pl-10 w-full p-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
    </div>
    <div class="flex items-center gap-2 text-sm text-slate-500">
        <span>Total Services: <span class="font-bold text-blue-600"><?= count($services) ?></span></span>
    </div>
</div>

<div class="bg-white rounded-b-xl shadow-sm border border-t-0 border-slate-200 overflow-hidden">
    <table class="w-full text-left border-collapse" id="servicesTable">
        <thead class="bg-slate-50 text-slate-600 uppercase text-xs font-bold">
            <tr>
                <th class="p-4 border-b">Service Details</th>
                <th class="p-4 border-b">Pricing Structure</th>
                <th class="p-4 border-b text-center">Status</th>
                <th class="p-4 border-b text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-sm">
            <?php if (empty($services)): ?>
                <tr>
                    <td colspan="4" class="p-10 text-center text-slate-400 italic">No services found. Click "Add New Service" to start.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($services as $s): ?>
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="p-4">
                        <div class="font-bold text-slate-800 text-base"><?= $s['service_name'] ?></div>
                        <div class="text-slate-500 text-xs line-clamp-1"><?= $s['description'] ?: 'No description provided.' ?></div>
                    </td>
                    <td class="p-4">
                        <?php if ($s['has_levels']): ?>
                            <div class="grid grid-cols-3 gap-2 w-full max-w-xs">
                                <div class="bg-green-50 p-1.5 rounded text-center">
                                    <span class="block text-[10px] uppercase text-green-600 font-bold">Simple</span>
                                    <span class="font-bold text-slate-700">₱<?= number_format($s['price_simple'], 0) ?></span>
                                </div>
                                <div class="bg-orange-50 p-1.5 rounded text-center">
                                    <span class="block text-[10px] uppercase text-orange-600 font-bold">Mod</span>
                                    <span class="font-bold text-slate-700">₱<?= number_format($s['price_moderate'], 0) ?></span>
                                </div>
                                <div class="bg-red-50 p-1.5 rounded text-center">
                                    <span class="block text-[10px] uppercase text-red-600 font-bold">Severe</span>
                                    <span class="font-bold text-slate-700">₱<?= number_format($s['price_severe'], 0) ?></span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="bg-blue-50 p-2 rounded w-fit px-4 border border-blue-100">
                                <span class="text-blue-700 font-bold text-base">₱<?= number_format($s['price'], 2) ?></span>
                                <span class="block text-[9px] text-slate-400"><?= $s['estimated_duration_minutes'] ?> mins</span>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="p-4 text-center">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase <?= $s['status'] == 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
                            <?= $s['status'] ?>
                        </span>
                    </td>
                    <td class="p-4 text-right space-x-1">
                        <!-- DYNAMIC EDIT BUTTON -->
                        <!-- Sa edit button, dagdagan ng duration data attributes -->
                        <button
                            type="button"
                            onclick="openEditModal(this)"
                            data-id="<?= $s['id'] ?>"
                            data-name="<?= $s['service_name'] ?>"
                            data-desc="<?= $s['description'] ?>"
                            data-haslevels="<?= $s['has_levels'] ?>"
                            data-price="<?= $s['price'] ?>"
                            data-psimple="<?= $s['price_simple'] ?>"
                            data-pmoderate="<?= $s['price_moderate'] ?>"
                            data-psevere="<?= $s['price_severe'] ?>"
                            data-status="<?= $s['status'] ?>"
                            data-duration="<?= $s['estimated_duration_minutes'] ?>"
                            data-dursimple="<?= json_decode($s['duration_adjustments'], true)['Simple'] ?? 0 ?>"
                            data-durmoderate="<?= json_decode($s['duration_adjustments'], true)['Moderate'] ?? 0 ?>"
                            data-dursevere="<?= json_decode($s['duration_adjustments'], true)['Severe'] ?? 0 ?>"
                            class="text-slate-400 hover:text-blue-600 p-2 transition-colors"
                            title="Edit Service">
                            <i class="fas fa-edit"></i>
                        </button>

                        <a href="<?= base_url('admin/services/delete/' . $s['id']) ?>"
                            class="text-slate-400 hover:text-red-600 p-2 transition-colors"
                            onclick="return confirm('Delete this service?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- ADD/EDIT MODAL -->
<div id="serviceModal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h4 id="modalTitle" class="font-bold text-xl text-slate-800">Add New Service</h4>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
        </div>
        <form id="serviceForm" action="<?= base_url('admin/services/store') ?>" method="POST" class="p-6 space-y-4">

            <input type="hidden" name="id" id="service_id">

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Service Name</label>
                <input type="text" name="service_name" id="service_name" class="w-full p-2.5 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <!-- STATUS (Lalabas lang 'to pag Edit) -->
            <div id="statusDiv" class="hidden">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Status</label>
                <select name="status" id="service_status" class="w-full p-2.5 border border-slate-200 rounded-lg outline-none">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                <div class="flex items-center justify-between">
                    <label for="has_levels" class="text-sm font-bold text-blue-800 uppercase italic cursor-pointer">Multiple Pricing Levels?</label>
                    <input type="hidden" name="has_levels" value="0">
                    <input type="checkbox" id="has_levels" name="has_levels" value="1" onchange="toggleLevels(this)" class="w-5 h-5 text-blue-600 rounded">
                </div>
            </div>

            <!-- SINGLE PRICE -->
            <div id="singlePriceDiv">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Standard Price (PHP)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 font-bold">₱</span>
                    <input type="number" name="price" id="price_input" class="w-full pl-8 p-2.5 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- LEVEL PRICES -->
            <div id="levelPricesDiv" class="hidden grid grid-cols-1 gap-3 p-4 bg-slate-50 rounded-xl border border-slate-200 border-dashed">
                <div>
                    <label class="block text-[10px] font-bold text-green-600 uppercase">Simple Case Price</label>
                    <input type="number" name="price_simple" id="price_simple" class="w-full p-2 border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-orange-600 uppercase">Moderate Case Price</label>
                    <input type="number" name="price_moderate" id="price_moderate" class="w-full p-2 border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-red-600 uppercase">Severe Case Price</label>
                    <input type="number" name="price_severe" id="price_severe" class="w-full p-2 border border-slate-200 rounded-lg">
                </div>
            </div>

            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">
                    <i class="fas fa-clock text-blue-500 mr-1"></i>
                    Estimated Duration (minutes)
                </label>
                <input type="number"
                    name="estimated_duration_minutes"
                    id="estimated_duration"
                    min="5"
                    max="480"
                    value="30"
                    class="w-full p-2.5 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-[10px] text-slate-400 mt-1">Base duration for this service. Used for auto end-time calculation.</p>
            </div>

            <!-- DURATION ADJUSTMENTS (Shows when has_levels is checked) -->
            <div id="durationAdjustmentsDiv" class="hidden grid grid-cols-3 gap-3 p-4 bg-slate-50 rounded-xl border border-slate-200 border-dashed">
                <div>
                    <label class="block text-[10px] font-bold text-green-600 uppercase">Simple (+/- mins)</label>
                    <input type="number"
                        name="duration_simple"
                        id="duration_simple"
                        value="0"
                        class="w-full p-2 border border-slate-200 rounded-lg text-center"
                        placeholder="0">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-orange-600 uppercase">Moderate (+/- mins)</label>
                    <input type="number"
                        name="duration_moderate"
                        id="duration_moderate"
                        value="0"
                        class="w-full p-2 border border-slate-200 rounded-lg text-center"
                        placeholder="0">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-red-600 uppercase">Severe (+/- mins)</label>
                    <input type="number"
                        name="duration_severe"
                        id="duration_severe"
                        value="0"
                        class="w-full p-2 border border-slate-200 rounded-lg text-center"
                        placeholder="0">
                </div>
                <p class="col-span-3 text-[9px] text-slate-400 text-center">
                    <i class="fas fa-info-circle mr-1"></i>
                    Positive = add time, Negative = reduce time from base duration
                </p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Description</label>
                <textarea name="description" id="service_desc" class="w-full p-2.5 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-500" rows="2"></textarea>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-3.5 rounded-xl font-bold hover:bg-blue-700 shadow-lg transition-all">
                Save Changes
            </button>
        </form>
    </div>
</div>

<script>
    function openModal(type) {
        // Reset Form for "Add" mode
        document.getElementById('serviceForm').reset();
        document.getElementById('serviceForm').action = "<?= base_url('admin/services/store') ?>";
        document.getElementById('modalTitle').innerText = "Add New Service";
        document.getElementById('statusDiv').classList.add('hidden');
        document.getElementById('serviceModal').classList.remove('hidden');
        toggleLevels(document.getElementById('has_levels'));
    }

    function openEditModal(btn) {
        // Set Action to Update
        const id = btn.getAttribute('data-id');
        document.getElementById('serviceForm').action = "<?= base_url('admin/services/update') ?>/" + id;
        document.getElementById('modalTitle').innerText = "Edit Service Details";
        document.getElementById('statusDiv').classList.remove('hidden');

        // Populate Fields
        document.getElementById('service_id').value = id;
        document.getElementById('service_name').value = btn.getAttribute('data-name');
        document.getElementById('service_desc').value = btn.getAttribute('data-desc');
        document.getElementById('service_status').value = btn.getAttribute('data-status');
        document.getElementById('estimated_duration').value = btn.getAttribute('data-duration') || 30;
        document.getElementById('duration_simple').value = btn.getAttribute('data-dursimple') || 0;
        document.getElementById('duration_moderate').value = btn.getAttribute('data-durmoderate') || 0;
        document.getElementById('duration_severe').value = btn.getAttribute('data-dursevere') || 0;

        // Checkbox logic
        const hasLevels = btn.getAttribute('data-haslevels') == '1';
        const checkbox = document.getElementById('has_levels');
        checkbox.checked = hasLevels;

        // Price fields
        document.getElementById('price_input').value = btn.getAttribute('data-price');
        document.getElementById('price_simple').value = btn.getAttribute('data-psimple');
        document.getElementById('price_moderate').value = btn.getAttribute('data-pmoderate');
        document.getElementById('price_severe').value = btn.getAttribute('data-psevere');

        toggleLevels(checkbox); // Ipakita ang tamang price inputs
        document.getElementById('serviceModal').classList.remove('hidden');
        toggleDurationAdjustments(document.getElementById('has_levels'));

        document.getElementById('serviceModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('serviceModal').classList.add('hidden');
    }

    function toggleDurationAdjustments(checkbox) {
        const durationDiv = document.getElementById('durationAdjustmentsDiv');

        if (checkbox.checked) {
            durationDiv.classList.remove('hidden');
        } else {
            durationDiv.classList.add('hidden');
        }
    }

    // ✅ Update toggleLevels to also call duration toggle
    function toggleLevels(checkbox) {
        const singleDiv = document.getElementById('singlePriceDiv');
        const levelDiv = document.getElementById('levelPricesDiv');
        const priceInput = document.getElementById('price_input');

        if (checkbox.checked) {
            levelDiv.classList.remove('hidden');
            singleDiv.classList.add('hidden');
            priceInput.required = false;
        } else {
            levelDiv.classList.add('hidden');
            singleDiv.classList.remove('hidden');
            priceInput.required = !document.getElementById('service_id').value;
        }

        // ✅ Also toggle duration adjustments
        toggleDurationAdjustments(checkbox);
    }

    function filterTable() {
        let input = document.getElementById("searchInput").value.toLowerCase();
        let rows = document.querySelectorAll("#servicesTable tbody tr");
        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(input) ? "" : "none";
        });
    }
</script>
<?= $this->endSection() ?>