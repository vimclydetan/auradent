<?= $this->extend('layouts/dashboard_layout') ?>
<?= $this->section('content') ?>
<style>
    @layer utilities {

        /* Modal animations */
        .modal-toggle:checked~.modal-backdrop,
        .modal-toggle:checked+.modal-container .modal-content {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-toggle:checked+.modal-container .modal-content {
            transform: scale(1);
        }

        .modal-backdrop {
            transition: opacity 0.3s ease;
        }

        .modal-content {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Subtle shake animation for errors */
        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-4px);
            }

            75% {
                transform: translateX(4px);
            }
        }

        .animate-shake {
            animation: shake 0.3s ease-in-out;
        }
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
</style>
<div class="flex justify-between items-center mb-6">
    <h3 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
        <i class="fas fa-user-shield text-blue-600"></i>
        User Login Logs
    </h3>

    <label for="export_password_modal"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 shadow-lg font-bold transition-all active:scale-95 flex items-center cursor-pointer">
        <i class="fas fa-file-export mr-2"></i>
        Export Logs
    </label>
</div>

<!-- FILTER SECTION -->
<div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5 mb-6">
    <form action="<?= base_url('admin/user-logs') ?>" method="GET"
        class="grid grid-cols-1 md:grid-cols-5 gap-4">

        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Username</label>
            <input type="text" name="username"
                value="<?= esc($filters['username']) ?>"
                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                placeholder="Search username">
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
            <select name="status"
                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">All Status</option>
                <?php foreach ($status_options as $opt): ?>
                    <option value="<?= $opt ?>" <?= $filters['status'] == $opt ? 'selected' : '' ?>>
                        <?= ucfirst(str_replace('_', ' ', $opt)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Date From</label>
            <input type="date" name="date_from"
                value="<?= esc($filters['date_from']) ?>"
                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Date To</label>
            <input type="date" name="date_to"
                value="<?= esc($filters['date_to']) ?>"
                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div class="flex items-end gap-2">
            <button type="submit"
                class="w-full px-4 py-2 text-xs font-semibold text-white uppercase bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                Filter
            </button>

            <a href="<?= base_url('admin/user-logs') ?>"
                class="w-full px-4 py-2 text-xs font-semibold text-center uppercase border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- TABLE -->
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
            <tr>
                <th class="px-5 py-3 text-left">ID</th>
                <th class="px-5 py-3 text-left">User</th>
                <th class="px-5 py-3 text-left">IP Address</th>
                <th class="px-5 py-3 text-center">Status</th>
                <th class="px-5 py-3 text-left">Date & Time</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
            <?php if (!empty($logs)): ?>
                <?php foreach ($logs as $log): ?>
                    <?php
                    $status = $log['status'];
                    $badgeClass = match ($status) {
                        'success' => 'bg-green-100 text-green-700',
                        'wrong_password' => 'bg-amber-100 text-amber-700',
                        'not_found' => 'bg-slate-100 text-slate-700',
                        'deactivated' => 'bg-red-100 text-red-700',
                        default => 'bg-slate-100 text-slate-700'
                    };
                    ?>
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-4 text-slate-400 font-mono">
                            #<?= $log['id'] ?>
                        </td>

                        <td class="px-5 py-4">
                            <div class="font-medium text-slate-700">
                                <?= esc($log['username']) ?>
                            </div>
                            <div class="text-xs text-slate-500">
                                <?= $log['role'] ? strtoupper($log['role']) : 'GUEST / UNKNOWN' ?>
                            </div>
                        </td>

                        <td class="px-5 py-4">
                            <span class="px-2 py-1 text-xs bg-slate-100 border border-slate-200 rounded">
                                <?= esc($log['ip_address_masked']) ?>
                            </span>
                        </td>

                        <td class="px-5 py-4 text-center">
                            <span class="px-2 py-1 text-xs font-semibold uppercase rounded <?= $badgeClass ?>">
                                <?= str_replace('_', ' ', $status) ?>
                            </span>
                        </td>

                        <td class="px-5 py-4">
                            <div class="text-slate-700 font-medium">
                                <?= date('M d, Y', strtotime($log['created_at'])) ?>
                            </div>
                            <div class="text-xs text-slate-500">
                                <?= date('h:i A', strtotime($log['created_at'])) ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="px-5 py-10 text-center text-slate-400">
                        No login records found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- PAGINATION -->
<?php if ($pager['total'] > 1): ?>
    <div class="flex items-center justify-between mt-6 p-4 bg-white border border-slate-200 rounded-xl shadow-sm">
        <div class="text-xs text-slate-500">
            Page <span class="font-semibold"><?= $pager['current'] ?></span> of
            <span class="font-semibold"><?= $pager['total'] ?></span> |
            Total: <span class="font-semibold"><?= number_format($pager['count']) ?></span>
        </div>

        <div class="flex gap-2">
            <?php if ($pager['current'] > 1): ?>
                <a href="<?= base_url('admin/user-logs?' . http_build_query(array_merge($filters, ['page' => $pager['current'] - 1]))) ?>"
                    class="px-3 py-2 text-xs font-medium border rounded-lg text-slate-600 hover:bg-slate-50">
                    Previous
                </a>
            <?php endif; ?>

            <?php if ($pager['current'] < $pager['total']): ?>
                <a href="<?= base_url('admin/user-logs?' . http_build_query(array_merge($filters, ['page' => $pager['current'] + 1]))) ?>"
                    class="px-3 py-2 text-xs font-medium border rounded-lg text-blue-600 hover:bg-blue-50">
                    Next
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<!-- EXPORT PASSWORD MODAL (DaisyUI) -->
<input type="checkbox" id="export_password_modal" class="modal-toggle" />

<label class="modal modal-bottom sm:modal-middle" for="export_password_modal" role="dialog" aria-modal="true">
    <label class="modal-backdrop" for="export_password_modal"></label>

    <div class="modal-box max-w-md p-0 overflow-hidden shadow-2xl">
        <!-- Header with Gradient -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                <i class="fas fa-lock text-white text-xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-white text-lg">Verify Identity</h3>
                <p class="text-blue-100 text-sm mt-0.5">Enter admin password to export login logs</p>
            </div>
        </div>

        <!-- Form Content -->
        <form id="export_verify_form" class="p-6 space-y-5">
            <?= csrf_field() ?>

            <div class="form-control">
                <label class="label pb-2" for="export_password">
                    <span class="label-text font-medium text-slate-700">Admin Password</span>
                </label>
                <div class="relative">
                    <input type="password"
                        name="password"
                        id="export_password"
                        placeholder="Enter your password"
                        class="input input-bordered w-full pl-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        required
                        autocomplete="current-password" />
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <i class="fas fa-key text-sm"></i>
                    </div>
                </div>
            </div>

            <!-- Error Message (DaisyUI Alert) -->
            <div id="export_error" class="alert alert-error py-3 px-4 hidden animate__animated animate__shakeX">
                <i class="fas fa-exclamation-circle"></i>
                <span id="error_message">Incorrect password. Please try again.</span>
            </div>

            <!-- Security Note (DaisyUI Alert Info) -->
            <div class="alert alert-info py-3 px-4 bg-blue-50 border-blue-200 text-blue-800">
                <i class="fas fa-info-circle"></i>
                <span class="text-sm">Exported file contains sensitive login data. Ensure you're on a secure connection.</span>
            </div>

            <!-- Actions -->
            <div class="modal-action justify-end gap-3 pt-2">
                <label for="export_password_modal" class="btn btn-ghost btn-sm text-slate-600 hover:text-slate-800">
                    Cancel
                </label>
                <button type="submit" class="btn btn-sm bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white border-none px-6 shadow-lg shadow-blue-500/30">
                    <i class="fas fa-download mr-1.5"></i>
                    Export Logs
                </button>
            </div>
        </form>
    </div>
</label>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('export_verify_form');
        const passwordInput = document.getElementById('export_password');
        const errorBox = document.getElementById('export_error');
        const errorMessage = document.getElementById('error_message');
        const modalToggle = document.getElementById('export_password_modal');

        // Focus password input when modal opens
        modalToggle.addEventListener('change', function() {
            if (this.checked) {
                setTimeout(() => passwordInput?.focus(), 100);
            } else {
                // Reset form when modal closes manually
                form?.reset();
                errorBox?.classList.add('hidden');
            }
        });

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const password = passwordInput.value.trim();
            if (!password) {
                showError('Please enter your password');
                return;
            }

            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i> Verifying...';

            try {
                const response = await fetch('<?= base_url("admin/user-logs/verify-export") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        password: password,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    })
                });

                const result = await response.json();

                if (result.success) {
                    // ✅ Show success toast BEFORE redirect
                    showSuccessToast('Preparing your export file...');

                    // Close modal programmatically
                    modalToggle.checked = false;

                    // Small delay for toast to appear, then redirect
                    setTimeout(() => {
                        window.location.href = '<?= base_url("admin/user-logs/export") ?>';
                    }, 800);

                } else {
                    showError(result.message || 'Incorrect password');
                    passwordInput.focus();
                    passwordInput.select();
                }
            } catch (error) {
                console.error('Export verification error:', error);
                showError('Connection error. Please try again.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });

        function showError(message) {
            errorMessage.textContent = message;
            errorBox.classList.remove('hidden');

            // Re-trigger shake animation
            errorBox.classList.remove('animate-shake');
            void errorBox.offsetWidth; // Trigger reflow
            errorBox.classList.add('animate-shake');

            setTimeout(() => {
                errorBox.classList.add('hidden');
            }, 4000);
        }

        // ✅ Success Toast Notification
        function showSuccessToast(message) {
            // Remove existing toast if any
            const existingToast = document.getElementById('export-success-toast');
            if (existingToast) existingToast.remove();

            // Create toast element
            const toast = document.createElement('div');
            toast.id = 'export-success-toast';
            toast.className = 'toast toast-top toast-end z-[100]';
            toast.innerHTML = `
            <div class="alert alert-success shadow-lg py-3 px-4 animate-[slideInRight_0.3s_ease-out]">
                <i class="fas fa-check-circle text-lg"></i>
                <span class="text-sm font-medium">${message}</span>
            </div>
        `;

            document.body.appendChild(toast);

            // Auto-remove after 2 seconds
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.2s ease';
                setTimeout(() => toast.remove(), 200);
            }, 2000);
        }

        // Allow Enter key to submit
        passwordInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                form.requestSubmit();
            }
        });
    });
</script>
<?= $this->endSection() ?>