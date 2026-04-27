<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<div class="flex justify-between items-center mb-6">
    <h3 class="text-2xl font-bold text-slate-700">Patient Records</h3>
    <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        <i class="fas fa-plus mr-2"></i> Add New Patient
    </button>
</div>

<!-- Patient Table -->
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50 border-b">
            <tr>
                <th class="p-4 font-semibold text-slate-600">Name</th>
                <th class="p-4 font-semibold text-slate-600">Contact</th>
                <th class="p-4 font-semibold text-slate-600">Gender</th>
                <th class="p-4 font-semibold text-slate-600">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($patients as $p): ?>
            <tr class="border-b hover:bg-slate-50 transition">
                <td class="p-4"><?= $p['full_name'] ?></td>
                <td class="p-4"><?= $p['contact_number'] ?></td>
                <td class="p-4"><?= $p['gender'] ?></td>
                <td class="p-4 space-x-2">
                    <button class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></button>
                    <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- ADD MODAL (Simple Tailwind Modal) -->
<div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md overflow-hidden">
        <div class="p-6 border-b flex justify-between">
            <h4 class="font-bold">Register New Patient</h4>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="text-slate-400">&times;</button>
        </div>
        <form action="<?= base_url('admin/save-patient') ?>" method="POST" class="p-6 space-y-4">
            <input type="text" name="full_name" placeholder="Full Name" class="w-full p-2 border rounded" required>
            <div class="grid grid-cols-2 gap-4">
                <input type="number" name="age" placeholder="Age" class="w-full p-2 border rounded" required>
                <select name="gender" class="w-full p-2 border rounded">
                    <option>Male</option>
                    <option>Female</option>
                </select>
            </div>
            <input type="text" name="contact_number" placeholder="Contact Number (Will be their password)" class="w-full p-2 border rounded" required>
            <input type="email" name="email" placeholder="Email Address" class="w-full p-2 border rounded" required>
            <input type="text" name="username" placeholder="Desired Username" class="w-full p-2 border rounded" required>
            <textarea name="address" placeholder="Address" class="w-full p-2 border rounded"></textarea>
            
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-bold">Create Account</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>