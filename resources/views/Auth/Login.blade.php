<!DOCTYPE html>
<html lang="en" data-theme="corporate">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MDRRMO System Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .bg-brick-orange {
            background-color: #c14a09;
        }
        .text-brick-orange {
            color: #c14a09;
        }
        .border-brick-orange {
            border-color: #c14a09;
        }
        .focus-ring-brick-orange:focus {
            ring-color: #c14a09;
        }
        .btn-hover:hover {
            background-color: #a53e07;
        }
        .input-field:focus {
            border-color: #c14a09 !important;
            box-shadow: 0 0 0 3px rgba(193, 74, 9, 0.2) !important;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center p-4">
        <div class="w-full max-w-md space-y-8">
            <form class="mt-8 space-y-6 bg-white p-8 rounded-lg shadow-md w-full" action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="text-center flex flex-col items-center justify-center mb-8">
                    <img src="{{ asset('img/logo.png') }}" alt="BukidnonAlert Logo" class="w-20 h-20 mx-auto mb-4" loading="lazy">
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-3">BukidnonAlert</h2>
                    <p class="text-sm text-gray-600">Emergency Response Management System</p>
                </div>

                <div class="w-full space-y-5">
                    {{-- Email/Username Input field with icon and validator --}}
                    <div class="w-full">
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            Email or Username
                        </label>
                        <label class="input validator input-primary w-full">
                            <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none" stroke="currentColor">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </g>
                            </svg>
                            <input
                                id="username"
                                name="username"
                                type="text"
                                required
                                placeholder="Enter your email or username"
                                pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}|[A-Za-z][A-Za-z0-9\-]*"
                                minlength="3"
                                title="Enter a valid email address or username"
                                value="{{ old('username') }}"
                                class="w-full"
                            />
                        </label>
                        <p class="validator-hint hidden text-xs text-gray-500 mt-1">
                            Enter a valid email address (e.g., user@example.com) or username (3-30 characters)
                        </p>
                        @error('username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password Input field with icon and validator --}}
                    <div class="w-full">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <label class="input validator input-primary w-full">
                            <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none" stroke="currentColor">
                                    <path d="M2.586 17.414A2 2 0 0 0 2 18.828V21a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h1a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h.172a2 2 0 0 0 1.414-.586l.814-.814a6.5 6.5 0 1 0-4-4z"></path>
                                    <circle cx="16.5" cy="7.5" r=".5" fill="currentColor"></circle>
                                </g>
                            </svg>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                placeholder="Enter your password"
                                minlength="6"
                                pattern="[A-Za-z0-9@#$%^&+=!]{6,}"
                                title="Must be at least 6 characters containing letters, numbers, or special characters"
                                class="w-full"
                            />
                        </label>
                        <p class="validator-hint hidden text-xs text-gray-500 mt-1">
                            Must be at least 6 characters containing letters, numbers, or special characters
                        </p>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                               class="h-4 w-4 text-brick-orange focus:ring-brick-orange border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                    </div>

                    <div class="text-sm">
                        <a href="{{ route('forgot-password') }}" class="font-medium text-brick-orange hover:text-orange-700 hover:underline hover:underline-offset-4">Forgot password?</a>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-brick-orange hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brick-orange transition-colors duration-200 btn-hover">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt"></i>
                        </span>
                        Sign In
                    </button>
                </div>

                <!-- Contact Admin -->
                <div class="bg-gray-50 p-4 rounded-md text-center">
                    <p class="text-sm text-gray-600">
                        Don't have an account?
                        <span class="font-medium text-gray-700">Contact your Administrator</span>
                    </p>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer -->
    {{-- <footer class="bg-white py-4 shadow-inner">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-500 text-center md:text-left">
                    &copy; 2023 MDRRMO Maramag. All rights reserved.
                </p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="text-gray-500 hover:text-brick-orange">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-brick-orange">
                        <i class="fas fa-globe"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-brick-orange">
                        <i class="fas fa-phone-alt"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer> --}}

    <script>
        // Toast notification functions
        function showSuccessToast(message) {
            const toast = document.createElement('div');
            toast.className = 'toast toast-end z-[9999]';
            toast.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(toast);

            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 3000);
        }

        function showErrorToast(message) {
            const toast = document.createElement('div');
            toast.className = 'toast toast-end z-[9999]';
            toast.innerHTML = `
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(toast);

            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 3000);
        }

        // Auto-display session messages as toasts
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showSuccessToast("{{ session('success') }}");
            @endif

            @if($errors->any())
                @foreach($errors->all() as $error)
                    showErrorToast("{{ $error }}");
                @endforeach
            @endif
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            if (!username || !password) {
                e.preventDefault();
                showErrorToast('Please fill in all fields');
            }
        });
    </script>
</body>
</html>
