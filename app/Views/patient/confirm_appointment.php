<?php
// app/Views/patient/confirm_appointment.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Confirm Appointment') ?> - Auradent</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Local Tailwind CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/tailwind.css') ?>">
    <link rel="stylesheet" href="<?= base_url('font-awesome/css/all.min.css') ?>">

    <style>
        /* Loading animation for confirm button */
        .btn-loading {
            position: relative;
            pointer-events: none;
            opacity: 0.85;
        }

        .btn-loading::after {
            content: "";
            position: absolute;
            width: 1rem;
            height: 1rem;
            top: 50%;
            left: 50%;
            margin: -0.5rem 0 0 -0.5rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Subtle entrance animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-enter {
            animation: fadeInUp 0.35s ease-out forwards;
        }

        /* Smooth hover transitions */
        .btn {
            transition: transform 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
        }

        .btn:active {
            transform: scale(0.98);
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-lg w-full bg-white rounded-2xl shadow-lg overflow-hidden animate-enter border border-slate-100">

        <!-- Header with Logo -->
        <div class="bg-white px-6 py-5 text-center border-b border-slate-100">
            <div class="flex justify-center mb-3">
                <img src="<?= base_url('assets/images/logo/transparent_logo.png') ?>"
                    alt="Auradent Logo"
                    class="h-12 w-auto object-contain">
            </div>
            <h1 class="text-lg font-bold text-slate-800 tracking-tight">Confirm Your Appointment</h1>
            <p class="text-slate-500 text-xs mt-0.5">Review and secure your booking</p>
        </div>

        <!-- Body -->
        <div class="p-6">
            <p class="text-slate-600 mb-5 text-sm">
                Hello <strong class="text-slate-800"><?= esc($appointment['first_name'] ?? 'Valued Patient') ?></strong>,
                please review your appointment details below:
            </p>

            <!-- Details Card -->
            <div class="bg-slate-50 rounded-xl p-4 mb-5 border border-slate-200">
                <div class="grid grid-cols-2 gap-y-3 gap-x-2 text-xs">
                    <div>
                        <span class="text-slate-400 block mb-0.5">Queue #</span>
                        <p class="font-bold text-slate-800 text-base">#<?= esc($appointment['queue_number'] ?? 'N/A') ?></p>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Date</span>
                        <p class="font-semibold text-slate-800">
                            <?= esc(date('M d, Y', strtotime($appointment['appointment_date'] ?? 'now'))) ?>
                        </p>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Time</span>
                        <p class="font-semibold text-slate-800">
                            <?= esc(date('h:i A', strtotime($appointment['appointment_time'] ?? 'now'))) ?>
                        </p>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Dentist</span>
                        <p class="font-semibold text-slate-800">
                            Dr. <?= esc($appointment['dentist_last'] ?? 'TBD') ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Status Badge -->
            <div class="flex items-start gap-2 bg-amber-50 border border-amber-200 rounded-lg px-3.5 py-3 mb-6 text-xs text-amber-800">
                <i class="fas fa-circle-info mt-0.5 flex-shrink-0"></i>
                <span>
                    This appointment is currently <strong class="font-semibold">Pending</strong>.
                    Confirm to secure your slot.
                </span>
            </div>

            <!-- Form -->
            <form method="POST" action="<?= site_url('appointments/confirm/process') ?>" id="confirmForm">
                <?= csrf_field() ?>
                <input type="hidden" name="appointment_id" value="<?= esc($appointment['id']) ?>">
                <input type="hidden" name="token" value="<?= esc($token) ?>">
                <input type="hidden" name="confirm" value="yes">
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit" name="confirm" value="yes" id="confirmBtn"
                        class="btn flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold 
                               py-3 px-4 rounded-xl transition flex items-center justify-center gap-2 
                               shadow-sm hover:shadow-md">
                        <i class="fas fa-check"></i>
                        <span>Confirm Appointment</span>
                    </button>
                    <a href="<?= site_url('patient/appointments') ?>"
                        class="btn flex-1 bg-white hover:bg-slate-50 text-slate-700 font-semibold py-3 px-4 
                          rounded-xl text-center transition border border-slate-300 hover:border-slate-400">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
            <p class="text-center text-xs text-slate-400 flex items-center justify-center gap-1.5">
                <i class="fas fa-shield-halved"></i>
                <span>Secure confirmation • Link expires in 48 hours</span>
            </p>
        </div>
    </div>

    <script>
        // Loading state on form submit
        document.getElementById('confirmForm')?.addEventListener('submit', function(e) {
            const btn = document.getElementById('confirmBtn');
            if (!btn) return;

            btn.classList.add('btn-loading');
            btn.querySelector('span').textContent = 'Confirming...';
            btn.disabled = true;
        });
    </script>

</body>

</html>