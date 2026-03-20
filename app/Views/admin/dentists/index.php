<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<style>
    [x-cloak] { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>

<div class="container mx-auto" x-data="{ openModal: false }">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-2xl font-bold text-slate-800">Dentist Directory</h3>
            <p class="text-slate-500 text-sm">Manage all registered dentists and their information.</p>
        </div>
        <button @click="openModal = true" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg transition flex items-center shadow-md font-medium">
            <i class="fas fa-plus mr-2 text-sm"></i> Add New Dentist
        </button>
    </div>

    <!-- Table Section -->
    <div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 uppercase text-[11px] font-bold tracking-wider">
                        <th class="px-6 py-4">Dentist</th>
                        <th class="px-6 py-4">Gender & Age</th>
                        <th class="px-6 py-4">Complete Address</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (!empty($dentists)): ?>
                        <?php foreach ($dentists as $d): ?>
                            <tr class="hover:bg-blue-50/30 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-4">
                                        <img class="h-10 w-10 rounded-full object-cover border border-slate-200"
                                            src="<?= base_url('uploads/profile/' . ($d['profile_pic'] ?: 'default.png')) ?>">
                                        <div>
                                            <div class="font-bold text-slate-800 text-sm">
                                                <?= esc($d['first_name'] . ' ' . $d['last_name']) ?> <?= esc($d['extension_name'] ?: '') ?>
                                            </div>
                                            <div class="text-[10px] text-slate-400 uppercase"><?= esc($d['contact_number']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600"><?= esc($d['gender']) ?></td>
                                <td class="px-6 py-4 text-xs text-slate-500 max-w-[200px] truncate">
                                    <?= esc($d['house_number'] . ' ' . $d['street'] . ', ' . $d['barangay'] . ', ' . $d['city'] . ', ' . $d['province']) ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="<?= base_url('admin/dentists/view/' . $d['id']) ?>" class="p-2 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition"><i class="fas fa-eye"></i></a>
                                        <button class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100"><i class="fas fa-edit"></i></button>
                                        <button class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100"><i class="fas fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center py-20 text-slate-400 italic">No records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL OVERLAY -->
    <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-60 backdrop-blur-sm" @click="openModal = false"></div>

            <div class="inline-block w-full max-w-3xl my-8 text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl relative">
                
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center rounded-t-2xl">
                    <h3 class="text-lg font-bold text-slate-800 uppercase tracking-wider">Register New Dentist</h3>
                    <button @click="openModal = false" class="text-slate-400 hover:text-red-500 transition"><i class="fas fa-times"></i></button>
                </div>

                <form action="<?= base_url('admin/dentists/store') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="p-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Profile Pic -->
                            <div class="flex flex-col items-center space-y-4">
                                <div class="relative group">
                                    <div id="image-preview" class="w-32 h-32 bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl overflow-hidden flex items-center justify-center relative">
                                        <i class="fas fa-user text-slate-200 text-4xl"></i>
                                        <img id="preview-img" class="absolute inset-0 w-full h-full object-cover hidden">
                                    </div>
                                    <label for="profile_pic" class="absolute -bottom-2 -right-2 bg-blue-600 text-white p-2 rounded-full cursor-pointer hover:bg-blue-700 shadow-lg transition">
                                        <i class="fas fa-camera text-sm"></i>
                                        <input type="file" name="profile_pic" id="profile_pic" class="hidden" accept="image/*" onchange="previewImage(event)">
                                    </label>
                                </div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase">Profile Picture</span>
                            </div>

                            <!-- PERSONAL INFO SECTION (UPDATED) -->
                            <div class="md:col-span-2 grid grid-cols-2 gap-4">
                                <div class="col-span-1">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">First Name</label>
                                    <input type="text" name="first_name" required maxlength="50" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                                <div class="col-span-1">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Middle Name (Optional)</label>
                                    <input type="text" name="middle_name" maxlength="50" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none">
                                </div>
                                <div class="col-span-1">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Last Name</label>
                                    <input type="text" name="last_name" required maxlength="50" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                                <div class="col-span-1">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Extension Name</label>
                                    <select name="extension_name" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none">
                                        <option value="">None</option>
                                        <option value="Jr">Jr</option>
                                        <option value="Sr">Sr</option>
                                        <option value="I">I</option>
                                        <option value="II">II</option>
                                        <option value="III">III</option>
                                        <option value="IV">IV</option>
                                        <option value="V">V</option>
                                    </select>
                                </div>
                                <div class="col-span-1">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Gender</label>
                                    <select name="gender" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-span-1">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Birthdate</label>
                                    <input type="date" name="birthdate" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- Login Credentials -->
                        <div class="mt-6 bg-blue-50/50 p-4 rounded-xl border border-blue-100/50">
                            <h4 class="text-[10px] font-bold text-blue-600 uppercase mb-3 tracking-widest flex items-center">
                                <i class="fas fa-lock mr-2"></i> Login Account
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" name="username" placeholder="Username" required class="px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none">
                                <input type="email" name="email" placeholder="Email Address" required class="px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none">
                                <input type="password" name="password" placeholder="Password" required class="px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none">
                                <input type="password" name="confirm_password" placeholder="Confirm Password" required class="px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none">
                            </div>
                        </div>

                        <!-- Address & Region (Field 10 to 16 in DB) -->
                        <div class="mt-6">
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase mb-4 tracking-widest">Address & Contact Details</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" name="house_number" placeholder="House / Unit #" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
                                <input type="text" name="street" placeholder="Street / Building" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">

                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase">Region</label>
                                    <select id="region" name="region" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white"></select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase">Province</label>
                                    <select id="province" name="province" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white"></select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase">City / Municipality</label>
                                    <select id="city" name="city" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white"></select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase">Barangay</label>
                                    <select id="barangay" name="barangay" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white"></select>
                                </div>

                                <div class="col-span-full">
                                    <input type="text" name="contact_number" placeholder="Contact Number (e.g. 09123456789)" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="mt-4 px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end space-x-3 rounded-b-2xl">
                        <button type="button" @click="openModal = false" class="px-6 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-200 rounded-lg transition">Cancel</button>
                        <button type="submit" class="px-6 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-lg transition">Save Dentist</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const address_url = "<?= base_url('data/ph-addresses/') ?>";

    async function fetchAddress(file) {
        try {
            const response = await fetch(`${address_url}${file}.json`);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return await response.json();
        } catch (error) {
            console.error("Could not fetch address data:", error);
            return [];
        }
    }

    async function populateSelect(elementId, file, filterValue = null, filterKey = '', label = '') {
        const select = document.getElementById(elementId);
        select.innerHTML = `<option value="">Loading...</option>`;
        
        const data = await fetchAddress(file);
        if (!data || data.length === 0) {
            select.innerHTML = `<option value="">Error loading data</option>`;
            return;
        }

        let nameKey = 'name';
        let codeKey = 'code';

        if (file === 'region') { nameKey = 'region_name'; codeKey = 'region_code'; }
        else if (file === 'province') { nameKey = 'province_name'; codeKey = 'province_code'; }
        else if (file === 'city') { nameKey = 'city_name'; codeKey = 'city_code'; }
        else if (file === 'barangay') { nameKey = 'brgy_name'; codeKey = 'brgy_code'; }

        let filteredData = data;
        if (filterValue && filterKey) {
            filteredData = data.filter(item => String(item[filterKey]) === String(filterValue));
        }

        filteredData.sort((a, b) => {
            const nameA = a[nameKey] || "";
            const nameB = b[nameKey] || "";
            return nameA.localeCompare(nameB);
        });

        let options = `<option value="">Select ${label}</option>`;
        filteredData.forEach(item => {
            options += `<option value="${item[nameKey]}" data-code="${item[codeKey]}">${item[nameKey]}</option>`;
        });
        
        select.innerHTML = options;
    }

    document.addEventListener('DOMContentLoaded', async () => {
        await populateSelect('region', 'region', null, '', 'Region');

        document.getElementById('region').onchange = async (e) => {
            const code = e.target.options[e.target.selectedIndex].dataset.code;
            if (code) {
                await populateSelect('province', 'province', code, 'region_code', 'Province');
            }
            document.getElementById('city').innerHTML = '<option value="">Select City</option>';
            document.getElementById('barangay').innerHTML = '<option value="">Select Barangay</option>';
        };

        document.getElementById('province').onchange = async (e) => {
            const code = e.target.options[e.target.selectedIndex].dataset.code;
            if (code) {
                await populateSelect('city', 'city', code, 'province_code', 'City');
            }
            document.getElementById('barangay').innerHTML = '<option value="">Select Barangay</option>';
        };

        document.getElementById('city').onchange = async (e) => {
            const code = e.target.options[e.target.selectedIndex].dataset.code;
            if (code) {
                await populateSelect('barangay', 'barangay', code, 'city_code', 'Barangay');
            }
        };
    });

    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('preview-img');
            output.src = reader.result;
            output.classList.remove('hidden');
            document.querySelector('#image-preview i').classList.add('hidden');
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
<?= $this->endSection() ?>