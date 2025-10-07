<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MDRRMO Account Recovery</title>
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

        .btn-primary {
            background: linear-gradient(to right, #c14a09, #a53e07);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .input-field:focus {
            border-color: #c14a09;
            box-shadow: 0 0 0 3px rgba(193, 74, 9, 0.2);
        }

        .logo-float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="flex justify-center mb-6">
                <div class="logo-float">
                    <div class="bg-white p-4 rounded-full shadow-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-brick-orange" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                </div>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Reset Password
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                MDRRMO Account Recovery
            </p>
        </div>

        <div class="bg-white py-8 px-6 shadow-lg rounded-lg">
            <div class="mb-6 text-center">
                <p class="text-gray-600">
                    Enter your MDRRMO email address and we'll send you a link to reset your password.
                </p>
            </div>

            <form class="mt-8 space-y-6" id="recoveryForm" action="">
                <div class="rounded-md shadow-sm space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required
                                class="input-field py-3 px-4 pl-10 block w-full border border-gray-300 rounded-md focus:outline-none focus:ring-brick-orange focus:border-brick-orange transition duration-150 ease-in-out"
                                placeholder="your.email@mdrrmo-maramag.gov.ph">
                        </div>
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="btn-primary group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brick-orange">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-paper-plane text-orange-200"></i>
                        </span>
                        Send Reset Link
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}"
                    class="text-brick-orange hover:text-orange-700 text-sm font-medium flex items-center justify-center hover:underline hover:underline-offset-4">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Login
                </a>
                <div class="mt-8 text-center text-sm text-gray-500">
                    <p>
                        Having trouble? Contact
                        <a href="mailto:support@mdrrmo-maramag.gov.ph" class="text-brick-orange hover:text-orange-700">
                            MDRRMO Support
                        </a>
                    </p>
                </div>
            </div>
        </div>


    </div>

    <script>
        document.getElementById('recoveryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('email').value;

            // Simple validation
            if (!email.includes('@mdrrmo-maramag.gov.ph')) {
                alert('Please enter a valid MDRRMO email address');
                return;
            }

            // Show loading state
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Sending...';
            btn.disabled = true;

            // Simulate API call
            setTimeout(() => {
                // Reset button
                btn.innerHTML = originalText;
                btn.disabled = false;

                // Show success message
                alert(`Reset link sent to ${email}. Please check your inbox.`);

                // You would typically redirect to a confirmation page here
                // window.location.href = '/reset-confirmation';
            }, 1500);
        });
    </script>
</body>

</html>
