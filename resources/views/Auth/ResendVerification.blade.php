<!DOCTYPE html>
<html lang="en" data-theme="corporate">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resend Email Verification | MDRRMO System</title>
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
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-error mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form class="mt-8 space-y-6 bg-white p-8 rounded-lg shadow-md" action="{{ route('email.verification.resend') }}" method="POST">
                @csrf
                <div class="text-center flex flex-col items-center justify-center">
                    <div class="w-16 h-16 bg-brick-orange rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-envelope text-white text-2xl"></i>
                    </div>
                    <h2 class="mt-4 text-3xl font-extrabold text-gray-900">Resend Email Verification</h2>
                    <p class="mt-2 text-sm text-gray-600">Enter your email address to receive a new verification link</p>
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
                                   placeholder="Enter your email address">
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-brick-orange hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brick-orange transition-colors duration-200 btn-hover">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-paper-plane"></i>
                        </span>
                        Send Verification Email
                    </button>
                </div>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="font-medium text-brick-orange hover:text-orange-700 hover:underline hover:underline-offset-4">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Login
                    </a>
                </div>

                <!-- Information Box -->
                <div class="bg-blue-50 p-4 rounded-md">
                    <h3 class="text-sm font-medium text-blue-700 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>What happens next?
                    </h3>
                    <ul class="text-xs text-blue-600 space-y-1">
                        <li>• A verification email will be sent to your address</li>
                        <li>• Click the verification link in the email</li>
                        <li>• Return to login once verified</li>
                        <li>• Check your spam folder if you don't see the email</li>
                    </ul>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;

            if (!email) {
                e.preventDefault();
                alert('Please enter your email address');
            }
        });
    </script>
</body>
</html>