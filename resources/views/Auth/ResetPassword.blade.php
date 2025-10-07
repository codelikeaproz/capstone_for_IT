<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password | MDRRMO Account Security</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

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

        .password-strength {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md space-y-8">
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
                Set New Password
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                MDRRMO Account Security
            </p>
        </div>

        <div class="bg-white py-8 px-6 shadow-lg rounded-lg">


            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" id="email"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brick-orange focus:border-brick-orange outline-none transition"
                    placeholder="mddrmomaramag.@gmail.com" required>
            </div>
            @csrf
            <form>
                <div class="mb-4">
                    <label for="new-password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <div>
                        <input type="password" id="new-password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brick-orange focus:border-brick-orange outline-none transition"
                            placeholder="Minimum 8 characters" minlength="8" required>
                    </div>
                    <div class="mt-2 flex space-x-1">
                        <div id="strength-1" class="password-strength w-1/4 bg-gray-200"></div>
                        <div id="strength-2" class="password-strength w-1/4 bg-gray-200"></div>
                        <div id="strength-3" class="password-strength w-1/4 bg-gray-200"></div>
                        <div id="strength-4" class="password-strength w-1/4 bg-gray-200"></div>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="confirm-password" class="block text-sm font-medium text-gray-700 mb-2">Confirm New
                        Password</label>
                    <div>
                        <input type="password" id="confirm-password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brick-orange focus:border-brick-orange outline-none transition"
                            placeholder="Re-enter your new password" minlength="8" required>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-brick-orange hover:bg-orange-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200">
                    Reset Password
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}"
                    class="text-brick-orange hover:text-orange-700 font-medium flex items-center justify-center hover:underline hover:underline-offset-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Login
                </a>

                <div class="mt-8 text-center text-sm text-gray-500">
                    <p>Having trouble? <a href="#"
                            class="text-brick-orange hover:text-orange-700 hover:underline hover:underline-offset-4">Contact
                            support</a></p>
                </div>
            </div>
        </div>
    </div>
    </div>



    <script>
        // Password strength indicator
        document.getElementById('new-password').addEventListener('input', function(e) {
            const password = e.target.value;
            const strengthBars = [
                document.getElementById('strength-1'),
                document.getElementById('strength-2'),
                document.getElementById('strength-3'),
                document.getElementById('strength-4')
            ];

            // Reset all bars
            strengthBars.forEach(bar => {
                bar.classList.remove('bg-red-500', 'bg-yellow-400', 'bg-green-500');
                bar.classList.add('bg-gray-200');
            });

            if (password.length === 0) return;

            // Very weak (less than 4 chars)
            if (password.length < 4) {
                strengthBars[0].classList.remove('bg-gray-200');
                strengthBars[0].classList.add('bg-red-500');
                return;
            }

            // Weak (4-6 chars)
            if (password.length <= 6) {
                strengthBars[0].classList.remove('bg-gray-200');
                strengthBars[1].classList.remove('bg-gray-200');
                strengthBars[0].classList.add('bg-red-500');
                strengthBars[1].classList.add('bg-red-500');
                return;
            }

            // Medium (7-9 chars)
            if (password.length <= 9) {
                strengthBars[0].classList.remove('bg-gray-200');
                strengthBars[1].classList.remove('bg-gray-200');
                strengthBars[2].classList.remove('bg-gray-200');
                strengthBars[0].classList.add('bg-yellow-400');
                strengthBars[1].classList.add('bg-yellow-400');
                strengthBars[2].classList.add('bg-yellow-400');
                return;
            }

            // Strong (10+ chars)
            strengthBars.forEach(bar => {
                bar.classList.remove('bg-gray-200');
                bar.classList.add('bg-green-500');
            });
        });

        // Password match validation
        document.getElementById('confirm-password').addEventListener('input', function() {
            const newPassword = document.getElementById('new-password').value;
            const confirmPassword = this.value;
            const matchText = document.getElementById('password-match');

            if (confirmPassword.length === 0) {
                matchText.classList.add('hidden');
                return;
            }

            if (newPassword === confirmPassword) {
                matchText.textContent = 'Passwords match!';
                matchText.classList.remove('hidden', 'text-red-500');
                matchText.classList.add('text-green-500');
            } else {
                matchText.textContent = 'Passwords do not match';
                matchText.classList.remove('hidden', 'text-green-500');
                matchText.classList.add('text-red-500');
            }
        });
    </script>
</body>

</html>
