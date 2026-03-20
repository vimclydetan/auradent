<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<div class="container mx-auto max-w-5xl">
    
    <!-- Breadcrumbs / Back Button -->
    <div class="mb-6">
        <a href="<?= base_url('admin/dentists') ?>" class="text-slate-500 hover:text-blue-600 transition flex items-center text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dentist Directory
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- LEFT COLUMN: Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="h-24 bg-gradient-to-r from-blue-500 to-blue-700"></div>
                <div class="px-6 pb-6 flex flex-col items-center -mt-12">
                    <img class="h-32 w-32 rounded-2xl object-cover border-4 border-white shadow-md bg-white" 
                         src="<?= base_url('uploads/profile/' . ($dentist['profile_pic'] ?: 'default.png')) ?>" 
                         alt="Profile">
                    <h2 class="mt-4 text-xl font-bold text-slate-800 text-center">
                        <?= esc($dentist['first_name'] . ' ' . $dentist['last_name']) ?>
                    </h2>
                    <p class="text-blue-600 font-semibold text-sm uppercase tracking-wider">
                        <?= esc($dentist['role']) ?>
                    </p>
                    
                    <div class="w-full mt-6 pt-6 border-t border-slate-100 flex justify-around text-center">
                        <div>
                            <p class="text-xs text-slate-400 uppercase font-bold">Gender</p>
                            <p class="text-sm font-medium text-slate-700"><?= esc($dentist['gender']) ?></p>
                        </div>
                        <div class="border-l border-slate-100 h-8"></div>
                        <div>
                            <p class="text-xs text-slate-400 uppercase font-bold">Age</p>
                            <p class="text-sm font-medium text-slate-700">
                                <?= date_diff(date_create($dentist['birthdate']), date_create('today'))->y; ?> yrs old
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 space-y-3">
                <button class="w-full bg-blue-600 text-white py-2.5 rounded-xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition flex items-center justify-center">
                    <i class="fas fa-edit mr-2"></i> Edit Profile
                </button>
                <button class="w-full bg-white text-red-600 border border-red-100 py-2.5 rounded-xl font-bold hover:bg-red-50 transition flex items-center justify-center">
                    <i class="fas fa-trash-alt mr-2"></i> Delete Account
                </button>
            </div>
        </div>

        <!-- RIGHT COLUMN: Detailed Info -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Personal Information -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                    <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-user text-sm"></i>
                    </span>
                    Personal Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
                    <div>
                        <p class="text-xs text-slate-400 uppercase font-bold mb-1 tracking-tight">Full Name</p>
                        <p class="text-slate-700 font-medium">
                            <?= esc($dentist['first_name'] . ' ' . $dentist['middle_name'] . ' ' . $dentist['last_name'] . ' ' . $dentist['extension_name']) ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase font-bold mb-1 tracking-tight">Birthdate</p>
                        <p class="text-slate-700 font-medium"><?= date('F d, Y', strtotime($dentist['birthdate'])) ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase font-bold mb-1 tracking-tight">Contact Number</p>
                        <p class="text-slate-700 font-medium"><?= esc($dentist['contact_number']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                    <span class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-map-marker-alt text-sm"></i>
                    </span>
                    Address Details
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs text-slate-400 uppercase font-bold mb-1">House No. & Street</p>
                        <p class="text-slate-700 font-medium"><?= esc($dentist['house_number'] . ' ' . $dentist['street']) ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase font-bold mb-1">Barangay</p>
                        <p class="text-slate-700 font-medium"><?= esc($dentist['barangay']) ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase font-bold mb-1">City / Municipality</p>
                        <p class="text-slate-700 font-medium"><?= esc($dentist['city']) ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase font-bold mb-1">Province</p>
                        <p class="text-slate-700 font-medium"><?= esc($dentist['province']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Account Credentials -->
            <div class="bg-slate-900 rounded-2xl shadow-lg p-8 text-white">
                <h3 class="text-lg font-bold mb-6 flex items-center text-blue-400">
                    <span class="w-8 h-8 bg-blue-900/50 text-blue-400 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-lock text-sm"></i>
                    </span>
                    Account Credentials
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-bold mb-1">Username</p>
                        <p class="text-slate-200 font-mono"><?= esc($dentist['username']) ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-bold mb-1">Login Email</p>
                        <p class="text-slate-200 font-mono"><?= esc($dentist['email']) ?></p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>