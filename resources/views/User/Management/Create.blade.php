@extends('Layouts.app')

@section('title', 'Create New User - BukidnonAlert')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <!-- Header with Breadcrumbs -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm breadcrumbs mb-4">
            <ul>
                <li><a href="{{ route('users.index') }}"><i class="fas fa-users"></i> Users</a></li>
                <li><i class="fas fa-plus"></i> Create New User</li>
            </ul>
        </div>
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-user-plus text-primary"></i> Create New User
                </h1>
                <p class="text-gray-600 mt-1">Add a new user to the system</p>
            </div>
            <a href="{{ route('users.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card bg-white shadow-lg">
        <div class="card-body">
            <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
                @csrf

                <!-- Personal Information Section -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-user text-primary"></i>
                        Personal Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- First Name -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">
                                    First Name <span class="text-error">*</span>
                                </span>
                            </label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}"
                                   class="input input-bordered w-full @error('first_name') input-error @enderror"
                                   placeholder="Enter first name" required>
                            @error('first_name')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">
                                    Last Name <span class="text-error">*</span>
                                </span>
                            </label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}"
                                   class="input input-bordered w-full @error('last_name') input-error @enderror"
                                   placeholder="Enter last name" required>
                            @error('last_name')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">
                                    Email Address <span class="text-error">*</span>
                                </span>
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="input input-bordered w-full @error('email') input-error @enderror"
                                   placeholder="user@example.com" required>
                            @error('email')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Phone Number -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Phone Number</span>
                            </label>
                            <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                                   class="input input-bordered w-full @error('phone_number') input-error @enderror"
                                   placeholder="+63 912 345 6789">
                            @error('phone_number')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="form-control md:col-span-2">
                            <label class="label">
                                <span class="label-text font-semibold">Address</span>
                            </label>
                            <textarea name="address" rows="2"
                                      class="textarea textarea-bordered w-full @error('address') textarea-error @enderror"
                                      placeholder="Enter complete address">{{ old('address') }}</textarea>
                            @error('address')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Account Information Section -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-key text-primary"></i>
                        Account Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Password -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">
                                    Password <span class="text-error">*</span>
                                </span>
                            </label>
                            <input type="password" name="password"
                                   class="input input-bordered w-full @error('password') input-error @enderror"
                                   placeholder="Minimum 8 characters" required>
                            @error('password')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                            <label class="label">
                                <span class="label-text-alt text-gray-500">
                                    <i class="fas fa-info-circle"></i> Minimum 8 characters
                                </span>
                            </label>
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">
                                    Confirm Password <span class="text-error">*</span>
                                </span>
                            </label>
                            <input type="password" name="password_confirmation"
                                   class="input input-bordered w-full"
                                   placeholder="Re-enter password" required>
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Role & Access Section -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-user-shield text-primary"></i>
                        Role & Access
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Role -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">
                                    Role <span class="text-error">*</span>
                                </span>
                            </label>
                            <select name="role" class="select select-bordered w-full @error('role') select-error @enderror" required>
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>
                                        {{ ucfirst($role) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                            <label class="label">
                                <span class="label-text-alt text-gray-500">
                                    <i class="fas fa-info-circle"></i> Determines user permissions
                                </span>
                            </label>
                        </div>

                        <!-- Municipality -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">
                                    Municipality <span class="text-error">*</span>
                                </span>
                            </label>
                            <select name="municipality" class="select select-bordered w-full @error('municipality') select-error @enderror" required>
                                <option value="">Select Municipality</option>
                                @foreach($municipalities as $municipality)
                                    <option value="{{ $municipality }}" {{ old('municipality') === $municipality ? 'selected' : '' }}>
                                        {{ $municipality }}
                                    </option>
                                @endforeach
                            </select>
                            @error('municipality')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                            <label class="label">
                                <span class="label-text-alt text-gray-500">
                                    <i class="fas fa-info-circle"></i> User's assigned municipality
                                </span>
                            </label>
                        </div>

                        <!-- Active Status -->
                        <div class="form-control md:col-span-2">
                            <label class="label cursor-pointer justify-start gap-4">
                                <input type="checkbox" name="is_active" value="1" class="checkbox checkbox-primary" {{ old('is_active', true) ? 'checked' : '' }}>
                                <div>
                                    <span class="label-text font-semibold">Active Account</span>
                                    <p class="text-sm text-gray-500">User can log in and access the system</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Role Descriptions -->
                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle text-xl"></i>
                    <div class="text-sm">
                        <p class="font-semibold mb-2">Role Descriptions:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li><strong>Admin:</strong> Full system access, can manage all users and settings</li>
                            <li><strong>Staff:</strong> Can manage incidents, vehicles, and requests</li>
                            <li><strong>Responder:</strong> Can respond to incidents and update status</li>
                            <li><strong>Citizen:</strong> Can report incidents and check request status</li>
                        </ul>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row gap-3 pt-6">
                    <button type="submit" class="btn btn-primary flex-1 sm:flex-none">
                        <i class="fas fa-save"></i> Create User
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-ghost flex-1 sm:flex-none">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-fill email domain suggestion
    document.querySelector('input[name="email"]').addEventListener('blur', function(e) {
        const email = e.target.value;
        if (email && !email.includes('@')) {
            e.target.value = email + '@example.com';
        }
    });

    // Password strength indicator
    document.querySelector('input[name="password"]').addEventListener('input', function(e) {
        const password = e.target.value;
        const strength = calculatePasswordStrength(password);
        // You can add visual feedback here
    });

    function calculatePasswordStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]+/)) strength++;
        if (password.match(/[A-Z]+/)) strength++;
        if (password.match(/[0-9]+/)) strength++;
        if (password.match(/[$@#&!]+/)) strength++;
        return strength;
    }
</script>
@endpush