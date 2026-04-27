<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<div class="container mx-auto max-w-4xl">
    <!-- Header/Back Button -->
    <div class="mb-6">
        <a href="<?= base_url('admin/dentists/view/' . $dentist['id']) ?>" class="text-slate-500 hover:text-blue-600 transition flex items-center text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Cancel and Go Back
        </a>
    </div>

    <form action="<?= base_url('admin/dentists/update/' . $dentist['id']) ?>" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <?= csrf_field() ?>

        <div class="p-8 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-xl font-bold text-slate-800 text-center uppercase tracking-tight">Edit Dentist Profile</h2>
        </div>

        <div class="p-8 space-y-8">
            <!-- PROFILE PHOTO & CLASSIFICATION -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="flex flex-col items-center">
                    <div class="relative group">
                        <img id="preview-img" class="w-40 h-40 rounded-2xl object-cover border-4 border-white shadow-lg"
                            src="<?= base_url('uploads/profile/' . ($dentist['profile_pic'] ?: 'default.png')) ?>">
                        <label for="profile_pic" class="absolute -bottom-2 -right-2 bg-blue-600 text-white p-2.5 rounded-full cursor-pointer hover:bg-blue-700 shadow-xl transition">
                            <i class="fas fa-camera text-sm"></i>
                            <input type="file" name="profile_pic" id="profile_pic" class="hidden" accept="image/*" onchange="previewImage(event)">
                        </label>
                    </div>
                    <p class="text-[10px] text-slate-400 font-bold mt-3 uppercase">Profile Picture</p>
                </div>

                <div class="md:col-span-2 space-y-4">
                    <div>
                        <label class="text-[10px] font-bold text-blue-500 uppercase italic">Dentist Classification</label>
                        <select name="dentist_type" required class="w-full px-3 py-2 border border-blue-200 bg-blue-50 rounded-lg text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="Regular" <?= $dentist['dentist_type'] == 'Regular' ? 'selected' : '' ?>>Regular (Full-time)</option>
                            <option value="On-call" <?= $dentist['dentist_type'] == 'On-call' ? 'selected' : '' ?>>On-call (Part-time / Call-in)</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Gender</label>
                            <select name="gender" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none">
                                <option value="Male" <?= $dentist['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= $dentist['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                                <option value="Other" <?= $dentist['gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Birthdate</label>
                            <input type="date" name="birthdate" value="<?= $dentist['birthdate'] ?>" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <!-- PERSONAL NAMES -->
            <div class="bg-white p-6 rounded-xl border border-slate-100 space-y-4">
                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest border-b pb-2 mb-4">Personal Names</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="md:col-span-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase">First Name</label>
                        <input type="text" name="first_name" value="<?= esc($dentist['first_name']) ?>" required class="w-full px-3 py-2 border rounded-lg text-sm">
                    </div>
                    <div class="md:col-span-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase">Middle Name</label>
                        <input type="text" name="middle_name" value="<?= esc($dentist['middle_name']) ?>" class="w-full px-3 py-2 border rounded-lg text-sm">
                    </div>
                    <div class="md:col-span-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase">Last Name</label>
                        <input type="text" name="last_name" value="<?= esc($dentist['last_name']) ?>" required class="w-full px-3 py-2 border rounded-lg text-sm">
                    </div>
                    <div class="md:col-span-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase">Extension</label>
                        <select name="extension_name" class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="">None</option>
                            <?php foreach(['Jr', 'Sr', 'I', 'II', 'III', 'IV', 'V'] as $ext): ?>
                                <option value="<?= $ext ?>" <?= $dentist['extension_name'] == $ext ? 'selected' : '' ?>><?= $ext ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- CONTACT & ADDRESS -->
            <div class="bg-white p-6 rounded-xl border border-slate-100 space-y-4">
                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest border-b pb-2 mb-4">Contact & Address</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-full">
                        <label class="text-[10px] font-bold text-slate-400 uppercase">Contact Number</label>
                        <input type="text" name="contact_number" value="<?= esc($dentist['contact_number']) ?>" placeholder="09xxxxxxxxx" class="w-full px-3 py-2 border rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase">House / Unit #</label>
                        <input type="text" name="house_number" value="<?= esc($dentist['house_number']) ?>" class="w-full px-3 py-2 border rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase">Street / Building</label>
                        <input type="text" name="street" value="<?= esc($dentist['street']) ?>" class="w-full px-3 py-2 border rounded-lg text-sm">
                    </div>
                    
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase">Region</label>
                        <select id="region" name="region" class="w-full px-3 py-2 border rounded-lg text-sm"></select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase">Province</label>
                        <select id="province" name="province" class="w-full px-3 py-2 border rounded-lg text-sm"></select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase">City / Municipality</label>
                        <select id="city" name="city" class="w-full px-3 py-2 border rounded-lg text-sm"></select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase">Barangay</label>
                        <select id="barangay" name="barangay" class="w-full px-3 py-2 border rounded-lg text-sm"></select>
                    </div>
                </div>
            </div>

            <!-- ACCOUNT ACCESS -->
            <div class="bg-slate-900 p-6 rounded-xl border border-slate-800 space-y-4">
                <h4 class="text-xs font-bold text-blue-400 uppercase tracking-widest border-b border-slate-800 pb-2 mb-4">Account Access</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-slate-500 uppercase">Username</label>
                        <input type="text" name="username" value="<?= esc($dentist['username']) ?>" class="w-full px-3 py-2 bg-slate-800 border-slate-700 text-white rounded-lg text-sm outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-500 uppercase">Email Address</label>
                        <input type="email" name="email" value="<?= esc($dentist['email']) ?>" class="w-full px-3 py-2 bg-slate-800 border-slate-700 text-white rounded-lg text-sm outline-none focus:border-blue-500">
                    </div>
                    <div class="col-span-full">
                        <label class="text-[10px] font-bold text-amber-500 uppercase">Change Password (Leave blank to keep current)</label>
                        <input type="password" name="password" placeholder="Enter new password" class="w-full px-3 py-2 bg-slate-800 border-slate-700 text-white rounded-lg text-sm outline-none focus:border-blue-500">
                    </div>
                </div>
            </div>
        </div>

        <div class="px-8 py-6 bg-slate-50 border-t flex justify-end space-x-3">
            <button type="submit" class="bg-blue-600 text-white px-10 py-3 rounded-xl font-bold shadow-lg hover:bg-blue-700 transition">Update Profile</button>
        </div>
    </form>
</div>

<script>
    // URL ng Address JSON files
    const address_url = "<?= base_url('data/ph-addresses/') ?>";
    
    // Existing Values from Database
    const savedRegion = "<?= $dentist['region'] ?>";
    const savedProvince = "<?= $dentist['province'] ?>";
    const savedCity = "<?= $dentist['city'] ?>";
    const savedBarangay = "<?= $dentist['barangay'] ?>";

    async function fetchAddress(file) {
        try {
            const response = await fetch(`${address_url}${file}.json`);
            return await response.json();
        } catch (error) { return []; }
    }

    async function populateSelect(elementId, file, filterValue = null, filterKey = '', label = '', defaultValue = '') {
        const select = document.getElementById(elementId);
        const data = await fetchAddress(file);
        
        let nameKey = 'name', codeKey = 'code';
        if (file === 'region') { nameKey = 'region_name'; codeKey = 'region_code'; }
        else if (file === 'province') { nameKey = 'province_name'; codeKey = 'province_code'; }
        else if (file === 'city') { nameKey = 'city_name'; codeKey = 'city_code'; }
        else if (file === 'barangay') { nameKey = 'brgy_name'; codeKey = 'brgy_code'; }

        let filteredData = filterValue ? data.filter(item => String(item[filterKey]) === String(filterValue)) : data;
        
        let options = `<option value="">Select ${label}</option>`;
        let selectedCode = '';

        filteredData.forEach(item => {
            const isSelected = item[nameKey] === defaultValue ? 'selected' : '';
            if(isSelected) selectedCode = item[codeKey];
            options += `<option value="${item[nameKey]}" data-code="${item[codeKey]}" ${isSelected}>${item[nameKey]}</option>`;
        });
        
        select.innerHTML = options;
        return selectedCode;
    }

    document.addEventListener('DOMContentLoaded', async () => {
        // Initial Load
        const regionCode = await populateSelect('region', 'region', null, '', 'Region', savedRegion);
        
        if (regionCode) {
            const provinceCode = await populateSelect('province', 'province', regionCode, 'region_code', 'Province', savedProvince);
            if (provinceCode) {
                const cityCode = await populateSelect('city', 'city', provinceCode, 'province_code', 'City', savedCity);
                if (cityCode) {
                    await populateSelect('barangay', 'barangay', cityCode, 'city_code', 'Barangay', savedBarangay);
                }
            }
        }

        // On Change Events
        document.getElementById('region').onchange = async (e) => {
            const code = e.target.options[e.target.selectedIndex].dataset.code;
            await populateSelect('province', 'province', code, 'region_code', 'Province');
            document.getElementById('city').innerHTML = '<option value="">Select City</option>';
            document.getElementById('barangay').innerHTML = '<option value="">Select Barangay</option>';
        };

        document.getElementById('province').onchange = async (e) => {
            const code = e.target.options[e.target.selectedIndex].dataset.code;
            await populateSelect('city', 'city', code, 'province_code', 'City');
            document.getElementById('barangay').innerHTML = '<option value="">Select Barangay</option>';
        };

        document.getElementById('city').onchange = async (e) => {
            const code = e.target.options[e.target.selectedIndex].dataset.code;
            await populateSelect('barangay', 'barangay', code, 'city_code', 'Barangay');
        };
    });

    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            document.getElementById('preview-img').src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
<?= $this->endSection() ?>