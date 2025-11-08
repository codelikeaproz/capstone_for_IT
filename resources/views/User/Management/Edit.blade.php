@extends('Layouts.app')

@section('title', 'Edit User - BukidnonAlert')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <!-- Header with Breadcrumbs -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm breadcrumbs mb-4">
            <ul>
                <li><a href="{{ route('users.index') }}"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="{{ route('users.show', $user) }}">{{ $user->full_name }}</a></li>
                <li><i class="fas fa-edit"></i> Edit</li>
            </ul>
        </div>
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-user-edit text-primary"></i> Edit User
                </h1>
                <p class="text-gray-600 mt-1">Update user information for {{ $user->full_name }}</p>
            </div>
            <a href="{{ route('users.show', $user) }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card bg-white shadow-lg">
        <div class="card-body">
            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
                @csrf
                @method('PUT')

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
                            <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}"
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
                            <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}"
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
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
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
                            <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
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
                                      placeholder="Enter complete address">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Password Section (Optional) -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-key text-primary"></i>
                        Change Password (Optional)
                    </h2>
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle"></i>
                        <span>Leave password fields empty to keep the current password</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- New Password -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">New Password</span>
                            </label>
                            <input type="password" name="password"
                                   class="input input-bordered w-full @error('password') input-error @enderror"
                                   placeholder="Minimum 8 characters">
                            @error('password')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Confirm New Password</span>
                            </label>
                            <input type="password" name="password_confirmation"
                                   class="input input-bordered w-full"
                                   placeholder="Re-enter password">
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
                                    <option value="{{ $role }}" {{ old('role', $user->role) === $role ? 'selected' : '' }}>
                                        {{ ucfirst($role) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
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
                                    <option value="{{ $municipality }}" {{ old('municipality', $user->municipality) === $municipality ? 'selected' : '' }}>
                                        {{ $municipality }}
                                    </option>
                                @endforeach
                            </select>
                            @error('municipality')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Active Status -->
                        <div class="form-control md:col-span-2">
                            <label class="label cursor-pointer justify-start gap-4">
                                <input type="checkbox" name="is_active" value="1" class="checkbox checkbox-primary" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <div>
                                    <span class="label-text font-semibold">Active Account</span>
                                    <p class="text-sm text-gray-500">User can log in and access the system</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Account Status Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div class="alert {{ $user->email_verified_at ? 'alert-success' : 'alert-warning' }}">
                        <i class="fas fa-{{ $user->email_verified_at ? 'check-circle' : 'exclamation-circle' }}"></i>
                        <div>
                            <p class="font-semibold">Email Verification</p>
                            <p class="text-sm">
                                @if($user->email_verified_at)
                                    Verified on {{ $user->email_verified_at->format('M d, Y') }}
                                @else
                                    Not verified yet
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-clock"></i>
                        <div>
                            <p class="font-semibold">Last Login</p>
                            <p class="text-sm">
                                @if($user->last_login)
                                    {{ $user->last_login->format('M d, Y h:i A') }}
                                @else
                                    Never logged in
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                @if($user->isAccountLocked())
                    <div class="alert alert-error mt-4">
                        <i class="fas fa-lock"></i>
                        <div>
                            <p class="font-semibold">Account Locked</p>
                            <p class="text-sm">Due to multiple failed login attempts. Unlocks at {{ $user->locked_until->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row gap-3 pt-6">
                    <button type="submit" class="btn btn-primary flex-1 sm:flex-none">
                        <i class="fas fa-save"></i> Update User
                    </button>
                    <a href="{{ route('users.show', $user) }}" class="btn btn-ghost flex-1 sm:flex-none">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    @if($user->id !== Auth::id())
                        <button type="button" onclick="deleteModal.showModal()" class="btn btn-error flex-1 sm:flex-none">
                            <i class="fas fa-trash"></i> Delete User
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<dialog id="deleteModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg text-error">
            <i class="fas fa-exclamation-triangle"></i> Confirm Deletion
        </h3>
        <p class="py-4">Are you sure you want to delete user <strong>{{ $user->full_name }}</strong>?</p>
        <p class="text-sm text-gray-500">This action cannot be undone.</p>
        <div class="modal-action">
            <form method="POST" action="{{ route('users.destroy', $user) }}">
                @csrf
                @method('DELETE')
                <button type="button" onclick="deleteModal.close()" class="btn btn-ghost">Cancel</button>
                <button type="submit" class="btn btn-error">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

@endsection