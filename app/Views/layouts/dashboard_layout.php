<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AuraDent Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-slate-50 flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-slate-900 text-white flex flex-col">
        <div class="p-6 text-2xl font-bold border-b border-slate-800 text-blue-400">
            🦷 AuraDent
        </div>

        <!-- Sa loob ng layouts/dashboard_layout.php -->
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
            <a href="<?= base_url('dashboard') ?>" class="flex items-center p-3 rounded-lg hover:bg-slate-800 transition">
                <i class="fas fa-home mr-3"></i> Dashboard
            </a>

            <!-- SIDEBAR (Snippet) -->
<?php if (session()->get('role') == 'admin'): ?>
    <a href="<?= base_url('admin/appointments') ?>" class="flex items-center p-3 rounded-lg hover:bg-slate-800">
        <i class="fas fa-list mr-3"></i> Appointment List
    </a>

    <a href="<?= base_url('admin/appointments/calendar') ?>" class="flex items-center p-3 rounded-lg hover:bg-slate-800">
        <i class="fas fa-calendar-alt mr-3"></i> Calendar View
    </a>

    <!-- ITO ANG DAGDAG NA LINK -->
    <a href="<?= base_url('admin/dentists') ?>" class="flex items-center p-3 rounded-lg hover:bg-slate-800 transition">
        <i class="fas fa-user-md mr-3"></i> Dentists
    </a>

    <a href="#" class="flex items-center p-3 rounded-lg hover:bg-slate-800">
        <i class="fas fa-users mr-3"></i> Patients
    </a>
    <a href="<?= base_url('admin/services') ?>" class="flex items-center p-3 rounded-lg hover:bg-slate-800">
        <i class="fas fa-tooth mr-3"></i> Services
    </a>
<?php endif; ?>
        </nav>

        <div class="p-4 border-t border-slate-800">
            <a href="<?= base_url('logout') ?>" class="flex items-center p-3 text-red-400 hover:bg-red-900/20 rounded-lg transition">
                <i class="fas fa-sign-out-alt mr-3"></i> Logout
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col overflow-hidden">
        <!-- TOP NAV -->
        <header class="h-16 bg-white border-b flex items-center justify-between px-8">
            <h2 class="text-xl font-semibold text-slate-700 uppercase tracking-wider">
                <?= $title ?? 'Dashboard' ?>
            </h2>
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-slate-600"><?= session()->get('username') ?></span>
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                    <?= strtoupper(substr(session()->get('username'), 0, 1)) ?>
                </div>
            </div>
        </header>

        <!-- DASHBOARD CONTENT -->
        <section class="flex-1 p-8 overflow-y-auto">
            <?= $this->renderSection('content') ?>
        </section>
    </main>

    <script src="<?= base_url('js/alpine.min.js') ?>" defer></script>
</body>

</html>