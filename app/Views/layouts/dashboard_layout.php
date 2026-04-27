<?php
function isActive($url)
{
    $current = current_url();
    $target = base_url($url);

    if ($current === $target || strpos($current, $target . '/') !== false || strpos($current, $target) !== false) {
        return 'bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold shadow-lg shadow-blue-500/30';
    }
    return 'text-slate-400 hover:bg-slate-800 hover:text-white transition-all duration-200';
}
?>
<!DOCTYPE html>
<html lang="en" x-data="{ sidebarOpen: false }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AuraDent Dashboard</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/tailwind.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/daisyui5_theme.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/daisyui5.css') ?>">
    <link rel="stylesheet" href="<?= base_url('font-awesome/css/all.min.css') ?>">
    <link rel="icon" href="<?= base_url('assets/images/logo/whitebg_logo.png') ?>" type="image/x-icon">

    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-track {
            background: #0f172a;
        }

        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 5px;
        }

        .content-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .content-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
        }

        .nav-icon {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }

        #notificationContainer {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            width: 100%;
            /* Kakainin niya ang buong width ng container */
            margin-bottom: 1.25rem;
            /* Space para sa content sa ilalim */
        }

        .notification-item {
            pointer-events: auto;
        }

        /* Animation para sa pag-alis */
        .notification-item.hiding {
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.4s ease-in;
        }

        /* ✨ BEAUTIFUL ALERT ANIMATION */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-alert {
            animation: fadeInDown 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
    </style>

    <!-- Scripts -->
    <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('js/alpine.min.js') ?>" defer></script>
    <script src="<?= base_url('assets/js/select2.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/select2.full.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/sweetalert2.all.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/signature_pad.umd.js') ?>"></script>
    <script src="<?= base_url('assets/js/tailwind.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('assets/css/select2.min.css') ?>">
</head>

<body class="bg-slate-50 flex h-screen overflow-hidden font-sans">

    <!-- MOBILE SIDEBAR OVERLAY -->
    <div x-show="sidebarOpen"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden">
    </div>

    <!-- SIDEBAR -->
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white flex flex-col shadow-2xl transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">

        <!-- Logo Section -->
        <div class="h-20 px-6 border-b border-slate-800 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                <img src="<?= base_url('assets/images/logo/transparent_logo.png') ?>" alt="Logo" class="w-10 h-10 object-contain">
                <div>
                    <h1 class="text-lg font-bold text-white tracking-wide">AuraDent</h1>
                    <p class="text-[10px] text-slate-400 uppercase tracking-wider">Clinic System</p>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-3 space-y-1 overflow-y-auto sidebar-scroll">
            <div class="pt-2 pb-1 px-4 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Menu</div>

            <a href="<?= base_url(session()->get('role') . '/dashboard') ?>"
                class="flex items-center px-4 py-3 rounded-xl <?= isActive(session()->get('role') . '/dashboard') ?>">
                <i class="fas fa-home nav-icon mr-3"></i>
                <span class="text-sm font-medium">Dashboard</span>
            </a>

            <?php if (session()->get('role') == 'admin'): ?>
                <a href="<?= base_url('admin/appointments') ?>" class="flex items-center px-4 py-3 rounded-xl <?= isActive('admin/appointments') ?>">
                    <i class="fas fa-calendar-check nav-icon mr-3"></i><span class="text-sm font-medium">Appointments</span>
                </a>
                <a href="<?= base_url('admin/walkin') ?>" class="flex items-center px-4 py-3 rounded-xl <?= isActive('admin/walkin') ?>">
                    <i class="fas fa-person-walking nav-icon mr-3"></i><span class="text-sm font-medium">Walk-in Patients</span>
                </a>
                <a href="<?= base_url('admin/patients') ?>" class="flex items-center px-4 py-3 rounded-xl <?= isActive('admin/patients') ?>">
                    <i class="fas fa-users nav-icon mr-3"></i><span class="text-sm font-medium">Patients</span>
                </a>
                <a href="<?= base_url('admin/calendar') ?>" class="flex items-center px-4 py-3 rounded-xl <?= isActive('admin/calendar') ?>">
                    <i class="fas fa-calendar-days nav-icon mr-3"></i><span class="text-sm font-medium">Calendar</span>
                </a>
                <a href="<?= base_url('admin/dentists') ?>" class="flex items-center px-4 py-3 rounded-xl <?= isActive('admin/dentists') ?>">
                    <i class="fas fa-user-doctor nav-icon mr-3"></i><span class="text-sm font-medium">Dentists</span>
                </a>
                <a href="<?= base_url('admin/services') ?>" class="flex items-center px-4 py-3 rounded-xl <?= isActive('admin/services') ?>">
                    <i class="fas fa-tooth nav-icon mr-3"></i><span class="text-sm font-medium">Services</span>
                </a>
                <a href="<?= base_url('admin/user-logs') ?>" class="flex items-center px-4 py-3 rounded-xl <?= isActive('admin/user-logs') ?>">
                    <i class="fas fa-clipboard-list nav-icon mr-3"></i><span class="text-sm font-medium">User Logs</span>
                </a>

            <?php elseif (session()->get('role') == 'patient'): ?>
                <a href="<?= base_url('patient/appointments') ?>" class="flex items-center px-4 py-3 rounded-xl <?= isActive('patient/appointments') ?>">
                    <i class="fas fa-calendar-check nav-icon mr-3"></i><span class="text-sm font-medium">My Appointments</span>
                </a>
                <a href="<?= base_url('patient/profile') ?>" class="flex items-center px-4 py-3 rounded-xl <?= isActive('patient/profile') ?>">
                    <i class="fas fa-user nav-icon mr-3"></i><span class="text-sm font-medium">My Profile</span>
                </a>

            <?php elseif (session()->get('role') == 'dentist'): ?>
                <a href="<?= base_url('dentist/appointments') ?>" class="flex items-center px-4 py-3 rounded-xl <?= isActive('dentist/appointments') ?>">
                    <i class="fas fa-calendar-check nav-icon mr-3"></i><span class="text-sm font-medium">Appointments</span>
                </a>
                <a href="<?= base_url('dentist/profile') ?>" class="flex items-center px-4 py-3 rounded-xl <?= isActive('dentist/profile') ?>">
                    <i class="fas fa-user-doctor nav-icon mr-3"></i><span class="text-sm font-medium">My Profile</span>
                </a>

            <?php elseif (session()->get('role') == 'receptionist'): ?>
                <a href="<?= base_url('receptionist/appointments') ?>" class="flex items-center px-4 py-3 rounded-xl <?= isActive('receptionist/appointments') ?>">
                    <i class="fas fa-calendar-check nav-icon mr-3"></i><span class="text-sm font-medium">Appointments</span>
                </a>
                <a href="<?= base_url('receptionist/walkin') ?>" class="flex items-center px-4 py-3 rounded-xl <?= isActive('receptionist/walkin') ?>">
                    <i class="fas fa-person-walking nav-icon mr-3"></i><span class="text-sm font-medium">Walk-in</span>
                </a>
                <a href="<?= base_url('receptionist/patients') ?>" class="flex items-center px-4 py-3 rounded-xl <?= isActive('receptionist/patients') ?>">
                    <i class="fas fa-users nav-icon mr-3"></i><span class="text-sm font-medium">Patients</span>
                </a>
                <a href="<?= base_url('receptionist/billing') ?>" class="flex items-center px-4 py-3 rounded-xl <?= isActive('receptionist/billing') ?>">
                    <i class="fas fa-file-invoice-dollar nav-icon mr-3"></i><span class="text-sm font-medium">Billing</span>
                </a>
                <a href="<?= base_url('receptionist/profile') ?>" class="flex items-center px-4 py-3 rounded-xl <?= isActive('receptionist/profile') ?>">
                    <i class="fas fa-user nav-icon mr-3"></i><span class="text-sm font-medium">My Profile</span>
                </a>
            <?php endif; ?>
        </nav>

        <div class="p-3 border-t border-slate-800 flex-shrink-0">
            <a href="<?= base_url('logout') ?>" class="flex items-center px-4 py-3 text-red-400 hover:bg-red-500/10 rounded-xl transition-all">
                <i class="fas fa-right-from-bracket nav-icon mr-3"></i>
                <span class="text-sm font-medium">Logout</span>
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 lg:px-8 flex-shrink-0 shadow-sm">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-lg">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="hidden lg:block">
                    <h2 class="text-sm font-medium text-slate-500">
                        Welcome back, <span class="font-bold text-slate-800"><?= session()->get('username') ?></span>!
                    </h2>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <div class="hidden sm:block text-right">
                    <p class="text-xs font-bold text-slate-800 leading-none"><?= session()->get('username') ?></p>
                    <p class="text-[10px] text-slate-400 uppercase"><?= session()->get('role') ?></p>
                </div>
                <div class="w-9 h-9 lg:w-10 lg:h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold shadow-md">
                    <?= strtoupper(substr(session()->get('username'), 0, 1)) ?>
                </div>
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <section class="flex-1 p-4 lg:p-8 overflow-y-auto content-scroll">
            <div class="max-w-7xl mx-auto">
                <div id="notificationContainer"></div>
                <!-- ✨ GLOBAL FLASH ALERTS -->
                <?php
                $alerts = [
                    'success' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-800', 'icon' => 'fa-check-circle', 'iconBg' => 'bg-emerald-500'],
                    'error'   => ['bg' => 'bg-rose-50', 'border' => 'border-rose-200', 'text' => 'text-rose-800', 'icon' => 'fa-exclamation-circle', 'iconBg' => 'bg-rose-500'],
                    'warning' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-800', 'icon' => 'fa-triangle-exclamation', 'iconBg' => 'bg-amber-500'],
                    'info'    => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-800', 'icon' => 'fa-info-circle', 'iconBg' => 'bg-blue-500']
                ];

                foreach ($alerts as $key => $style):
                    if (session()->getFlashdata($key)): ?>
                        <div class="mb-5 p-4 <?= $style['bg'] ?> <?= $style['text'] ?> rounded-xl border <?= $style['border'] ?> shadow-sm flex items-start gap-3 animate-alert relative overflow-hidden group">
                            <div class="<?= $style['iconBg'] ?> text-white p-1.5 rounded-lg shrink-0 shadow-sm">
                                <i class="fas <?= $style['icon'] ?> text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-bold uppercase tracking-wider mb-0.5"><?= $key ?></p>
                                <p class="text-sm opacity-90 font-medium"><?= session()->getFlashdata($key) ?></p>
                            </div>
                            <button onclick="this.parentElement.remove()" class="opacity-50 hover:opacity-100 transition p-1">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                <?php endif;
                endforeach; ?>

                <!-- DITO LALABAS YUNG PAGE CONTENT -->
                <?= $this->renderSection('content') ?>
            </div>
        </section>
    </main>


    <script>
        // Automatic hide flash alerts after 5 seconds
        $(document).ready(function() {
            setTimeout(function() {
                $('.animate-alert').fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 10000);
        });

        function showNotification(message, type = 'info', title = null, duration = 10000) {
            const container = document.getElementById('notificationContainer');
            if (!container) return;

            // Eto yung config na kapareho nung PHP alerts mo
            const styles = {
                success: {
                    bg: 'bg-emerald-50',
                    border: 'border-emerald-200',
                    text: 'text-emerald-800',
                    icon: 'fa-check-circle',
                    iconBg: 'bg-emerald-500'
                },
                error: {
                    bg: 'bg-rose-50',
                    border: 'border-rose-200',
                    text: 'text-rose-800',
                    icon: 'fa-exclamation-circle',
                    iconBg: 'bg-rose-500'
                },
                warning: {
                    bg: 'bg-amber-50',
                    border: 'border-amber-200',
                    text: 'text-amber-800',
                    icon: 'fa-triangle-exclamation',
                    iconBg: 'bg-amber-500'
                },
                info: {
                    bg: 'bg-blue-50',
                    border: 'border-blue-200',
                    text: 'text-blue-800',
                    icon: 'fa-info-circle',
                    iconBg: 'bg-blue-500'
                }
            };

            const s = styles[type] || styles.info;
            const label = title || type.charAt(0).toUpperCase() + type.slice(1);

            const el = document.createElement('div');
            // Ginamit ang 'animate-alert' para parehas sila ng animation ng PHP version
            el.className = `notification-item mb-2 p-4 ${s.bg} ${s.text} rounded-xl border ${s.border} shadow-lg flex items-start gap-3 animate-alert relative overflow-hidden`;

            el.innerHTML = `
        <div class="${s.iconBg} text-white p-1.5 rounded-lg shrink-0 shadow-sm">
            <i class="fas ${s.icon} text-xs"></i>
        </div>
        <div class="flex-1">
            <p class="text-xs font-bold uppercase tracking-wider mb-0.5">${label}</p>
            <p class="text-sm opacity-90 font-medium">${message}</p>
        </div>
        <button onclick="hideNotification(this.parentElement)" class="opacity-50 hover:opacity-100 transition p-1">
            <i class="fas fa-times text-sm"></i>
        </button>
    `;

            container.appendChild(el);

            // Auto hide
            if (duration > 0) {
                setTimeout(() => hideNotification(el), duration);
            }
            return el;
        }

        function hideNotification(el) {
            if (!el) return;
            el.classList.add('hiding');
            setTimeout(() => el.remove(), 10000);
        }
    </script>
</body>

</html>