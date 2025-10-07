<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern User Registration</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#c14a09',
                        primaryLight: '#e05e1a',
                        primaryDark: '#9a3b07'
                    }
                }
            }
        }
    </script>
    <style>
        .form-input:focus {
            box-shadow: 0 0 0 3px rgba(193, 74, 9, 0.2);
            border-color: #c14a09;
        }
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-xl shadow-2xl overflow-hidden">
        <!-- Header with decorative elements -->
        <div class="relative bg-gradient-to-r from-primaryDark via-primary to-primaryLight h-24 flex items-center justify-center">
            <div class="absolute -bottom-6 left-1/2 transform -translate-x-1/2 bg-white rounded-full p-2 shadow-lg">
                <div class="bg-primary rounded-full p-4 text-white">
                    <i class="fas fa-user-plus text-2xl"></i>
                </div>
            </div>
            <div class="absolute top-4 left-4 w-8 h-8 rounded-full bg-white opacity-20 animate-float"></div>
            <div class="absolute top-8 right-6 w-6 h-6 rounded-full bg-white opacity-20 animate-float" style="animation-delay: 0.5s;"></div>
        </div>

        <!-- Form content -->
        <div class="px-8 pt-16 pb-8">
            <h1 class="text-2xl font-bold text-center text-gray-800 mb-2">Create New Account</h1>
            <p class="text-center text-gray-600 mb-6">Fill in the details below to register a new user</p>

            <form class="space-y-5">
                <!-- First Name Field -->
                <div>
                    <label for="first-name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text" id="first-name" class="form-input pl-10 block w-full rounded-lg border-gray-300 focus:border-primary" placeholder="John" required>
                    </div>
                </div>

                <!-- Last Name Field -->
                <div>
                    <label for="last-name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text" id="last-name" class="form-input pl-10 block w-full rounded-lg border-gray-300 focus:border-primary" placeholder="Doe" required>
                    </div>
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" class="form-input pl-10 block w-full rounded-lg border-gray-300 focus:border-primary" placeholder="john@example.com" required>
                    </div>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" class="form-input pl-10 block w-full rounded-lg border-gray-300 focus:border-primary" placeholder="••••••••" required>
                    </div>
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <label for="confirm-password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="confirm-password" class="form-input pl-10 block w-full rounded-lg border-gray-300 focus:border-primary" placeholder="••••••••" required>
                    </div>
                </div>

                <!-- Role Dropdown -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">User Role</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user-tag text-gray-400"></i>
                        </div>
                        <select id="role" class="form-select pl-10 block w-full rounded-lg border-gray-300 focus:border-primary text-gray-700">
                            <option value="" disabled selected>Select a role</option>
                            <option value="admin">Administrator</option>
                            <option value="editor">Editor</option>
                            <option value="viewer">Viewer</option>
                            <option value="guest">Guest</option>
                        </select>
                    </div>
                </div>

                <!-- Terms Checkbox -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="terms" name="terms" type="checkbox" class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded" required>
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="terms" class="font-medium text-gray-700">I agree to the <a href="#" class="text-primary hover:text-primaryDark">Terms and Conditions</a></label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primaryLight focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primaryDark transition-colors duration-300">
                        Register User
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-8 py-4 text-center">
            <p class="text-xs text-gray-500">Already have an account? <a href="#" class="font-medium text-primary hover:text-primaryDark">Sign in</a></p>
        </div>
    </div>
</body>
</html>
