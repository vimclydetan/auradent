<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<style>
    [x-cloak] {
        display: none !important;
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 5px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
</style>

<div class="container mx-auto" x-data="{ openModal: false }">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Dentist Directory</h1>
            <p class="text-slate-500 text-sm">Manage clinic staff, account access, and employment status.</p>
        </div>
        <button @click="openModal = true" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl transition-all flex items-center justify-center shadow-lg shadow-blue-200 font-bold text-sm">
            <i class="fas fa-plus mr-2 text-xs"></i> Register New Dentist
        </button>
    </div>

    <!-- Table Section -->
    <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-600 uppercase text-[11px] font-bold tracking-widest border-b border-slate-200">
                        <th class="px-6 py-4">Dentist Information</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Gender & Age</th>
                        <th class="px-6 py-4">Contact & Address</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (!empty($dentists)): ?>
                        <?php foreach ($dentists as $d): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-4">
                                        <img class="h-11 w-11 rounded-xl object-cover border border-slate-200 shadow-sm"
                                            src="<?= base_url('uploads/profile/' . ($d['profile_pic'] ?: 'default.png')) ?>">
                                        <div>
                                            <div class="font-bold text-slate-800 text-sm flex items-center">
                                                <?= esc($d['first_name'] . ' ' . $d['last_name']) ?>
                                                <?php if ($d['dentist_type'] === 'On-call'): ?>
                                                    <span class="ml-2 px-2 py-0.5 text-[9px] bg-amber-50 text-amber-600 rounded-md border border-amber-100 uppercase font-bold">On-call</span>
                                                <?php else: ?>
                                                    <span class="ml-2 px-2 py-0.5 text-[9px] bg-blue-50 text-blue-600 rounded-md border border-blue-100 uppercase font-bold">Regular</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-[11px] text-slate-400 font-medium">ID: #<?= str_pad($d['id'], 4, '0', STR_PAD_LEFT) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($d['status'] === 'Active'): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-600 border border-green-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-2"></span> Active
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-2"></span> Archived
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    <div class="font-medium"><?= esc($d['gender']) ?></div>
                                    <div class="text-[11px] text-slate-400 uppercase font-bold"><?= date_diff(date_create($d['birthdate']), date_create('today'))->y; ?> Years Old</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-700 font-medium"><?= esc($d['contact_number']) ?></div>
                                    <div class="text-[11px] text-slate-400 truncate max-w-[180px]"><?= esc($d['city'] . ', ' . $d['province']) ?></div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center items-center space-x-2 opacity-100">
                                        <a href="<?= base_url('admin/dentists/view/' . $d['id']) ?>" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View Profile"><i class="fas fa-eye"></i></a>
                                        <a href="<?= base_url('admin/dentists/edit/' . $d['id']) ?>" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit Information"><i class="fas fa-edit"></i></a>

                                        <?php if ($d['status'] === 'Active'): ?>
                                            <a href="<?= base_url('admin/dentists/deactivate/' . $d['id']) ?>" onclick="return confirm('Archive this dentist?')" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Archive Account"><i class="fas fa-user-slash"></i></a>
                                        <?php else: ?>
                                            <a href="<?= base_url('admin/dentists/activate/' . $d['id']) ?>" class="p-2 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition" title="Restore Account"><i class="fas fa-user-check"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-24">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 text-slate-300">
                                        <i class="fas fa-user-md text-3xl"></i>
                                    </div>
                                    <p class="text-slate-400 italic">No dentists registered yet.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- REGISTRATION MODAL -->
    <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" @click="openModal = false"></div>

            <div class="inline-block w-full max-w-3xl my-8 text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl relative overflow-hidden">

                <!-- Modal Header -->
                <div class="bg-slate-900 px-8 py-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-white tracking-tight">New Dentist Registration</h3>
                        <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold">Fill out all required credentials</p>
                    </div>
                    <button @click="openModal = false" class="text-slate-400 hover:text-white transition p-2 bg-slate-800 rounded-full">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="<?= base_url('admin/dentists/store') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="p-8 max-h-[75vh] overflow-y-auto custom-scrollbar space-y-8">

                        <!-- SECTION 1: Profile & Professional Info -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="flex flex-col items-center">
                                <div class="relative group">
                                    <div id="image-preview" class="w-36 h-36 bg-slate-50 border-2 border-dashed border-slate-200 rounded-3xl overflow-hidden flex items-center justify-center relative">
                                        <i class="fas fa-user-plus text-slate-200 text-4xl"></i>
                                        <img id="preview-img" class="absolute inset-0 w-full h-full object-cover hidden">
                                    </div>
                                    <label for="profile_pic" class="absolute -bottom-2 -right-2 bg-blue-600 text-white p-3 rounded-full cursor-pointer hover:bg-blue-700 shadow-xl transition transform hover:scale-110">
                                        <i class="fas fa-camera text-sm"></i>
                                        <input type="file" name="profile_pic" id="profile_pic" class="hidden" accept="image/*" onchange="previewImage(event)">
                                    </label>
                                </div>
                                <span class="text-[10px] text-slate-400 font-bold mt-4 uppercase tracking-widest">Upload Photo</span>
                            </div>

                            <div class="md:col-span-2 grid grid-cols-2 lg:grid-cols-6 gap-4">
                                <!-- Names -->
                                <div class="col-span-2 lg:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">First Name</label>
                                    <input type="text" name="first_name" required class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                                <div class="col-span-2 lg:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Middle Name</label>
                                    <input type="text" name="middle_name" class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                                <div class="col-span-2 lg:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Last Name</label>
                                    <input type="text" name="last_name" required class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>

                                <!-- Professional & Basic info -->
                                <div class="col-span-2 lg:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Extension</label>
                                    <select name="extension_name" class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm outline-none">
                                        <option value="">None</option>
                                        <option value="Jr">Jr</option>
                                        <option value="Sr">Sr</option>
                                        <option value="I">I</option>
                                        <option value="II">II</option>
                                        <option value="III">III</option>
                                    </select>
                                </div>
                                <div class="col-span-2 lg:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Dentist Type</label>
                                    <select name="dentist_type" required class="w-full px-4 py-2 border border-blue-100 bg-blue-50 text-blue-700 rounded-xl text-sm font-bold outline-none">
                                        <option value="Regular">Regular</option>
                                        <option value="On-call">On-call</option>
                                    </select>
                                </div>
                                <div class="col-span-2 lg:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Gender</label>
                                    <select name="gender" required class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm outline-none">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-span-full lg:col-span-3">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Birthdate</label>
                                    <input type="date" name="birthdate" required class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm outline-none">
                                </div>
                                <!-- Status is Active by default as per DB -->
                                <input type="hidden" name="status" value="Active">
                            </div>
                        </div>

                        <!-- SECTION 2: Account Access -->
                        <div class="bg-blue-50/50 p-6 rounded-2xl border border-blue-100">
                            <h4 class="text-[10px] font-bold text-blue-600 uppercase mb-4 tracking-widest flex items-center">
                                <i class="fas fa-shield-alt mr-2"></i> Account Access
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[9px] font-bold text-blue-400 uppercase ml-1">Username</label>
                                    <input type="text" name="username" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm outline-none bg-white">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-bold text-blue-400 uppercase ml-1">Email Address</label>
                                    <input type="email" name="email" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm outline-none bg-white">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-bold text-blue-400 uppercase ml-1">Password</label>
                                    <input type="password" name="password" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm outline-none bg-white">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-bold text-blue-400 uppercase ml-1">Confirm Password</label>
                                    <input type="password" name="confirm_password" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm outline-none bg-white">
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 3: Contact & Address Details -->
                        <div>
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase mb-4 tracking-widest flex items-center">
                                <i class="fas fa-map-marker-alt mr-2"></i> Contact & Address Details
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div class="col-span-full">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase mb-1 block">Contact Number</label>
                                    <input type="text" name="contact_number" placeholder="09xxxxxxxxx" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm outline-none">
                                </div>

                                <div class="col-span-2">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase mb-1 block">House / Unit #</label>
                                    <input type="text" name="house_number" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm">
                                </div>
                                <div class="col-span-2">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase mb-1 block">Street / Building</label>
                                    <input type="text" name="street" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm">
                                </div>

                                <div class="col-span-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase mb-1 block">Region</label>
                                    <select id="region" name="region" required class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm bg-white"></select>
                                </div>
                                <div class="col-span-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase mb-1 block">Province</label>
                                    <select id="province" name="province" required class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm bg-white"></select>
                                </div>
                                <div class="col-span-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase mb-1 block">City / Municipality</label>
                                    <select id="city" name="city" required class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm bg-white"></select>
                                </div>
                                <div class="col-span-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase mb-1 block">Barangay</label>
                                    <select id="barangay" name="barangay" required class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm bg-white"></select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-8 py-6 bg-slate-50 border-t border-slate-200 flex justify-end space-x-3">
                        <button type="button" @click="openModal = false" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-700 transition">Cancel</button>
                        <button type="submit" class="px-8 py-2.5 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-lg shadow-blue-200 transition transform active:scale-95">Create Account</button>
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

        if (file === 'region') {
            nameKey = 'region_name';
            codeKey = 'region_code';
        } else if (file === 'province') {
            nameKey = 'province_name';
            codeKey = 'province_code';
        } else if (file === 'city') {
            nameKey = 'city_name';
            codeKey = 'city_code';
        } else if (file === 'barangay') {
            nameKey = 'brgy_name';
            codeKey = 'brgy_code';
        }

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