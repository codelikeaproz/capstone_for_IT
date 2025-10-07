<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Verification | MDRRMO Security</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
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

        .code-input {
            letter-spacing: 0.5em;
            font-size: 2rem;
            padding-left: 0.5em;
        }

        .digit-box {
            width: 3.5rem;
            height: 4.5rem;
            font-size: 2rem;
            text-align: center;
            margin: 0 0.25rem;
            border-radius: 0.5rem;
            border: 2px solid #e2e8f0;
            transition: all 0.2s;
        }

        .digit-box:focus {
            border-color: #c14a09;
            box-shadow: 0 0 0 3px rgba(193, 74, 9, 0.2);
            outline: none;
        }

        .timer {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                opacity: 1;
            }
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
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
                Two-Factor Authentication
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                MDRRMO Security Verification
            </p>
        </div>

        <div class="bg-white py-8 px-6 shadow-lg rounded-lg">
            <div class="mb-6 text-center">
                <p class="text-sm text-gray-600 mb-2">We've sent a 6-digit verification code to:</p>
                <p class="font-medium text-gray-800">{{ $userEmail }}</p>
            </div>

            <div class="flex justify-between items-center mb-6">
                <span class="text-sm text-gray-600">Code expires in:</span>
                <span class="font-medium text-red-500 timer" id="countdown-timer">
                    05:00
                </span>
            </div>

            <form method="POST" action="{{ route('2fa.verify.post') }}" id="twoFactorForm">
                @csrf
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Enter Verification Code</label>
                    <div class="flex justify-center space-x-2">
                        <input type="text" maxlength="1" class="digit-box" name="digit1" autofocus>
                        <input type="text" maxlength="1" class="digit-box" name="digit2">
                        <input type="text" maxlength="1" class="digit-box" name="digit3">
                        <input type="text" maxlength="1" class="digit-box" name="digit4">
                        <input type="text" maxlength="1" class="digit-box" name="digit5">
                        <input type="text" maxlength="1" class="digit-box" name="digit6">
                    </div>
                    <input type="hidden" name="code" id="verification-code">
                </div>

                <button type="submit" id="verify-btn"
                    class="w-full bg-brick-orange hover:bg-orange-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 mb-4">
                    <span class="btn-text">Verify Code</span>
                    <span class="loading hidden"><i class="fas fa-spinner fa-spin mr-2"></i>Verifying...</span>
                </button>
            </form>

            <div class="text-center">
                <p class="text-sm text-gray-600 mb-4">Didn't receive the code?
                    <a href="#" id="resend-code" class="text-brick-orange hover:text-orange-700 font-medium">Resend Code</a>
                </p>
                <a href="{{ route('login') }}"
                    class="text-sm text-gray-600 hover:text-gray-800 font-medium flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Login
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.digit-box');
            const form = document.getElementById('twoFactorForm');
            const codeInput = document.getElementById('verification-code');
            const verifyBtn = document.getElementById('verify-btn');
            const resendLink = document.getElementById('resend-code');
            const timerElement = document.getElementById('countdown-timer');

            // Timer variables
            let totalSeconds = 300; // 5 minutes default
            let timerId = null;

            // Initialize timer function
            function startTimer(seconds = 300) {
                // Clear existing timer if any
                if (timerId) {
                    clearInterval(timerId);
                }

                totalSeconds = Math.max(0, seconds);
                
                function updateTimer() {
                    if (totalSeconds <= 0) {
                        // Timer expired
                        timerElement.textContent = 'Expired';
                        timerElement.classList.add('text-red-600');
                        timerElement.classList.remove('timer');
                        
                        // Show expired message
                        if (!document.querySelector('.timer-expired-message')) {
                            const expiredMessage = document.createElement('div');
                            expiredMessage.className = 'timer-expired-message text-sm text-orange-600 text-center mt-2';
                            expiredMessage.innerHTML = '<i class="fas fa-clock mr-1"></i>Code expired. You can still try entering it or request a new one.';
                            timerElement.parentNode.appendChild(expiredMessage);
                        }
                        
                        // Clear the timer
                        if (timerId) {
                            clearInterval(timerId);
                            timerId = null;
                        }
                        return;
                    }

                    const minutes = Math.floor(totalSeconds / 60);
                    const seconds = totalSeconds % 60;
                    
                    // Format with leading zeros
                    const minutesDisplay = minutes.toString().padStart(2, '0');
                    const secondsDisplay = seconds.toString().padStart(2, '0');
                    
                    timerElement.textContent = `${minutesDisplay}:${secondsDisplay}`;
                    totalSeconds--;
                }

                // Update immediately, then every second
                updateTimer();
                timerId = setInterval(updateTimer, 1000);
            }

            // Start initial timer
            startTimer();

            // Initialize input fields as enabled
            inputs.forEach(input => {
                input.disabled = false;
                input.readOnly = false;
            });

            // Auto-focus and move between inputs
            inputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    // Only allow digits
                    const value = e.target.value.replace(/[^0-9]/g, '');
                    e.target.value = value;

                    // Move to next field if digit entered
                    if (value.length === 1 && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }

                    updateVerificationCode();
                });

                // Handle backspace
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && input.value === '' && index > 0) {
                        inputs[index - 1].focus();
                    }
                });

                // Handle paste
                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '');
                    
                    // Fill inputs with pasted digits
                    for (let i = 0; i < Math.min(pastedData.length, inputs.length - index); i++) {
                        if (inputs[index + i]) {
                            inputs[index + i].value = pastedData[i];
                        }
                    }
                    
                    // Focus next empty field or last field
                    const nextIndex = Math.min(index + pastedData.length, inputs.length - 1);
                    inputs[nextIndex].focus();
                    
                    updateVerificationCode();
                });
            });

            // Update hidden verification code
            function updateVerificationCode() {
                const code = Array.from(inputs).map(input => input.value).join('');
                codeInput.value = code;
            }

            // Form submission
            form.addEventListener('submit', function(e) {
                updateVerificationCode();

                if (codeInput.value.length !== 6) {
                    e.preventDefault();
                    showMessage('Please enter all 6 digits of the verification code.', 'error');
                    return;
                }

                // Show loading state
                const btnText = verifyBtn.querySelector('.btn-text');
                const loading = verifyBtn.querySelector('.loading');
                btnText.classList.add('hidden');
                loading.classList.remove('hidden');
                verifyBtn.disabled = true;

                // Allow form to submit normally - remove preventDefault
                // The server will handle the verification and redirect
            });

            // Resend code functionality
            resendLink.addEventListener('click', function(e) {
                e.preventDefault();

                // Disable resend link temporarily
                const originalText = resendLink.textContent;
                resendLink.style.pointerEvents = 'none';
                resendLink.textContent = 'Sending...';

                // Make actual AJAX request to resend endpoint
                fetch('{{ route("2fa.resend") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reset timer with new duration from server
                        const remainingSeconds = (data.remaining_minutes * 60) + data.remaining_seconds;
                        startTimer(remainingSeconds);
                        
                        // Clear inputs
                        inputs.forEach(input => {
                            input.value = '';
                        });
                        inputs[0].focus();

                        // Reset timer styling
                        timerElement.classList.remove('text-red-600');
                        timerElement.classList.add('timer');

                        // Remove expired message if exists
                        const expiredMessage = document.querySelector('.timer-expired-message');
                        if (expiredMessage) {
                            expiredMessage.remove();
                        }

                        // Re-enable verify button
                        verifyBtn.disabled = false;

                        // Show success message
                        showMessage(data.message, 'success');
                    } else {
                        showMessage(data.message || 'Failed to resend code. Please try again.', 'error');
                        
                        if (data.redirect) {
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 2000);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('Network error. Please check your connection and try again.', 'error');
                })
                .finally(() => {
                    // Re-enable resend link
                    resendLink.style.pointerEvents = 'auto';
                    resendLink.textContent = originalText;
                });
            });

            // Helper function to show messages
            function showMessage(message, type) {
                // Remove existing messages
                const existingAlerts = document.querySelectorAll('.alert');
                existingAlerts.forEach(alert => alert.remove());

                const alertDiv = document.createElement('div');
                alertDiv.className = `alert p-3 border rounded mb-4 ${
                    type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'
                }`;
                alertDiv.innerHTML = `<i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle mr-2"></i>${message}`;

                const container = document.querySelector('.bg-white.py-8');
                container.insertBefore(alertDiv, container.firstChild);

                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 5000);
            }

            // Initialize code field
            updateVerificationCode();
        });
    </script>
</body>

</html>