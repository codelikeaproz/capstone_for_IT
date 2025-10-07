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
    <!-- Navbar -->
    {{-- <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <img src="{{ asset('img/logo.png') }}" alt="MDRRMO Logo" class="w-8 h-8 mr-2" loading="lazy">
                        <span class="text-xl font-bold text-gray-800">MDRRMO</span>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-center space-x-4">
                        <a href="#" class="text-gray-700 hover:text-brick-orange px-3 py-2 text-sm font-medium">Home</a>
                        <a href="#" class="text-gray-700 hover:text-brick-orange px-3 py-2 text-sm font-medium">About</a>
                        <a href="#" class="text-gray-700 hover:text-brick-orange px-3 py-2 text-sm font-medium">Contact</a>
                    </div>
                </div>
                <div class="-mr-2 flex md:hidden">
                    <button type="button" id="mobile-menu-button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-brick-orange focus:outline-none">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div> --}}

        <!-- Mobile menu -->
        {{-- <div class="md:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="#" class="text-gray-700 hover:text-brick-orange block px-3 py-2 text-base font-medium">Home</a>
                <a href="#" class="text-gray-700 hover:text-brick-orange block px-3 py-2 text-base font-medium">About</a>
                <a href="#" class="text-gray-700 hover:text-brick-orange block px-3 py-2 text-base font-medium">Contact</a>
            </div>
        </div>
    </nav> --}}

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center p-4">
        <div class="w-full max-w-md space-y-8">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success mb-4">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-error mb-4">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form class="mt-8 space-y-6 bg-white p-8 rounded-lg shadow-md" action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="text-center flex flex-col items-center justify-center">
                    <img src="{{ asset('img/logo.png') }}" alt="BukidnonAlert Logo" class="w-17 h-17 mx-auto" loading="lazy">
                    <h2 class="mt-4 text-3xl font-extrabold text-gray-900">BukidnonAlert</h2>
                    <p class="mt-2 text-sm text-gray-600">Emergency Response Management System</p>
                </div>
                
                <div class="rounded-md shadow-sm space-y-4">
                    <div>
                        <label for="email" class="sr-only">Email address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                                   class="appearance-none block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-brick-orange focus:border-brick-orange sm:text-sm hover:border-brick-orange transition-colors duration-200 input-field @error('email') border-red-500 @enderror"
                                   placeholder="admin@bukidnonalert.gov.ph">
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                   class="appearance-none block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-brick-orange focus:border-brick-orange sm:text-sm hover:border-brick-orange transition-colors duration-200 input-field @error('password') border-red-500 @enderror"
                                   placeholder="Enter your password">
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                               class="h-4 w-4 text-brick-orange focus:ring-brick-orange border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                    </div>

                    <div class="text-sm">
                        <a href="{{ route('forgot-password') }}" class="font-medium text-brick-orange hover:text-orange-700 hover:underline hover:underline-offset-4">Forgot password?</a>
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-brick-orange hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brick-orange transition-colors duration-200 btn-hover">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt"></i>
                        </span>
                        Sign In
                    </button>
                </div>

                <!-- Demo Credentials -->
                <div class="bg-gray-50 p-4 rounded-md">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Demo Credentials:</h3>
                    <div class="text-xs text-gray-600 space-y-1">
                       
                    </div>
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
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (!email || !password) {
                e.preventDefault();
                alert('Please fill in all fields');
            }
            // You can add more validation here if needed
        });
    </script>
</body>
</html>
