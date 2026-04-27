<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | AuraDent SmartClinic</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/tailwind.css') ?>">
    <link rel="stylesheet" href="<?= base_url('font-awesome/css/all.min.css') ?>">
    <style>
        /* Chrome, Edge, Safari */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none;
        }

        input[type="password"]::-webkit-credentials-auto-fill-button,
        input[type="password"]::-webkit-textfield-decoration-container {
            display: none !important;
        }

        /* Newer Chrome (important) */
        input[type="password"]::-webkit-password-toggle-button {
            display: none;
        }
    </style>

</head>


<body class="bg-slate-50 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-sm border border-slate-200">

        <!-- Branding -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-600 rounded-lg mb-3">
                <i class="fas fa-tooth text-white text-xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">AuraDent</h1>
            <p class="text-slate-500 text-sm">SmartClinic System</p>
        </div>

        <!-- Error Message -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="<?= base_url('/login') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-medium text-slate-700 mb-1">Username</label>
                <div class="relative">
                    <input
                        type="text"
                        name="username"
                        id="username"
                        class="w-full px-4 py-2.5 pl-10 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Enter username"
                        value="<?= old('username') ?>"
                        required>
                    <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                </div>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <div class="relative">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="w-full px-4 py-2.5 pl-10 pr-10 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Enter password"
                        required>
                    <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                <label for="remember" class="ml-2 text-sm text-slate-600">Remember me</label>
            </div>

            <!-- Login Button -->
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition-colors">
                Sign In
            </button>
        </form>

        <!-- Footer -->
        <div class="mt-6 pt-4 border-t border-slate-200 text-center">
            <p class="text-xs text-slate-400">&copy; <?= date('Y') ?> AuraDent</p>
        </div>
    </div>

    <!-- Minimal JS for password toggle -->
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>

</html>