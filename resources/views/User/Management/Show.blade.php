@extends('Layouts.app')

@section('title', $user->full_name . ' - User Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header with Breadcrumbs -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm breadcrumbs mb-4">
            <ul>
                <li><a href="{{ route('users.index') }}"><i class="fas fa-users"></i> Users</a></li>
                <li>{{ $user->full_name }}</li>
            </ul>
        </div>
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                    <div class="avatar placeholder">
                        <div class="bg-primary text-primary-content rounded-full w-12">
                            <span class="text-xl">{{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}</span>
                        </div>
                    </div>
                    {{ $user->full_name }}
                </h1>
                <p class="text-gray-600 mt-1 ml-16">
                    <span class="badge {{ $user->role_badge }} badge-sm mr-2">{{ ucfirst($user->role) }}</span>
                    <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-error' }} badge-sm">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit User
                </a>
                @if($user->id !== Auth::id())
                    <button onclick="deleteModal.showModal()" class="btn btn-error">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - User Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="card bg-white shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">
                        <i class="fas fa-user text-primary"></i>
                        Personal Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">First Name</p>
                            <p class="font-semibold">{{ $user->first_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Last Name</p>
                            <p class="font-semibold">{{ $user->last_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Email Address</p>
                            <p class="font-semibold flex items-center gap-2">
                                <i class="fas fa-envelope text-gray-400"></i>
                                {{ $user->email }}
                                @if($user->email_verified_at)
                                    <span class="badge badge-success badge-xs">Verified</span>
                                @else
                                    <span class="badge badge-warning badge-xs">Unverified</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Phone Number</p>
                            <p class="font-semibold">
                                @if($user->phone_number)
                                    <i class="fas fa-phone text-gray-400"></i> {{ $user->phone_number }}
                                @else
                                    <span class="text-gray-400">Not provided</span>
                                @endif
                            </p>
                        </div>
                        @if($user->address)
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-500 mb-1">Address</p>
                                <p class="font-semibold">
                                    <i class="fas fa-map-marker-alt text-gray-400"></i> {{ $user->address }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="card bg-white shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">
                        <i class="fas fa-user-shield text-primary"></i>
                        Account Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Role</p>
                            <p class="font-semibold">
                                <span class="badge {{ $user->role_badge }}">{{ ucfirst($user->role) }}</span>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Municipality</p>
                            <p class="font-semibold">
                                <i class="fas fa-map-marker-alt text-gray-400"></i> {{ $user->municipality }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Account Status</p>
                            <p class="font-semibold">
                                <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-error' }}">
                                    <i class="fas fa-{{ $user->is_active ? 'check' : 'times' }} mr-1"></i>
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Email Verification</p>
                            <p class="font-semibold">
                                @if($user->email_verified_at)
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle mr-1"></i> Verified
                                    </span>
                                    <span class="text-xs text-gray-500 ml-2">
                                        {{ $user->email_verified_at->format('M d, Y') }}
                                    </span>
                                @else
                                    <span class="badge badge-warning">
                                        <i class="fas fa-exclamation-circle mr-1"></i> Unverified
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Last Login</p>
                            <p class="font-semibold">
                                @if($user->last_login)
                                    <i class="fas fa-clock text-gray-400"></i>
                                    {{ $user->last_login->format('M d, Y h:i A') }}
                                    <span class="text-xs text-gray-500">({{ $user->last_login->diffForHumans() }})</span>
                                @else
                                    <span class="text-gray-400">Never logged in</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Account Created</p>
                            <p class="font-semibold">
                                <i class="fas fa-calendar text-gray-400"></i>
                                {{ $user->created_at->format('M d, Y') }}
                                <span class="text-xs text-gray-500">({{ $user->created_at->diffForHumans() }})</span>
                            </p>
                        </div>
                    </div>

                    @if($user->isAccountLocked())
                        <div class="alert alert-error mt-4">
                            <i class="fas fa-lock"></i>
                            <div>
                                <p class="font-semibold">Account Locked</p>
                                <p class="text-sm">Due to {{ $user->failed_login_attempts }} failed login attempts. Unlocks at {{ $user->locked_until->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Activity Statistics -->
            <div class="card bg-white shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">
                        <i class="fas fa-chart-bar text-primary"></i>
                        Activity Statistics
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="stat bg-base-200 rounded-lg p-4 text-center">
                            <div class="stat-title text-xs">Incidents Reported</div>
                            <div class="stat-value text-2xl text-primary">{{ $user->reportedIncidents->count() }}</div>
                        </div>
                        <div class="stat bg-base-200 rounded-lg p-4 text-center">
                            <div class="stat-title text-xs">Incidents Assigned</div>
                            <div class="stat-value text-2xl text-secondary">{{ $user->assignedIncidents->count() }}</div>
                        </div>
                        <div class="stat bg-base-200 rounded-lg p-4 text-center">
                            <div class="stat-title text-xs">Vehicles Assigned</div>
                            <div class="stat-value text-2xl text-accent">{{ $user->assignedVehicles->count() }}</div>
                        </div>
                        <div class="stat bg-base-200 rounded-lg p-4 text-center">
                            <div class="stat-title text-xs">Requests Handled</div>
                            <div class="stat-value text-2xl text-info">{{ $user->assignedRequests->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card bg-white shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">
                        <i class="fas fa-history text-primary"></i>
                        Recent Activity
                    </h2>
                    @if($activities->count() > 0)
                        <div class="space-y-3">
                            @foreach($activities as $activity)
                                <div class="flex items-start gap-3 p-3 bg-base-200 rounded-lg">
                                    <div class="text-primary mt-1">
                                        <i class="fas fa-circle text-xs"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold">{{ $activity->description }}</p>
                                        <p class="text-sm text-gray-500">
                                            <i class="fas fa-clock mt-1"></i>
                                            {{ $activity->created_at->format('M d, Y h:i A') }}
                                            ({{ $activity->created_at->diffForHumans() }})
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-400">
                            <i class="fas fa-history text-4xl mb-2"></i>
                            <p>No recent activity</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Quick Actions -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="card bg-white shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">
                        <i class="fas fa-bolt text-primary"></i>
                        Quick Actions
                    </h2>
                    <div class="space-y-3">
                        <button onclick="toggleStatus()" class="btn btn-block {{ $user->is_active ? 'btn-warning' : 'btn-success' }}">
                            <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                            {{ $user->is_active ? 'Deactivate' : 'Activate' }} Account
                        </button>

                        @if(!$user->email_verified_at)
                            <button onclick="verifyEmail()" class="btn btn-block btn-info">
                                <i class="fas fa-check-circle"></i>
                                Verify Email
                            </button>
                        @endif

                        @if($user->isAccountLocked())
                            <button onclick="unlockAccount()" class="btn btn-block btn-warning">
                                <i class="fas fa-unlock"></i>
                                Unlock Account
                            </button>
                        @endif

                        <button onclick="resetPasswordModal.showModal()" class="btn btn-block btn-secondary">
                            <i class="fas fa-key"></i>
                            Reset Password
                        </button>

                        <button onclick="changeRoleModal.showModal()" class="btn btn-block btn-primary">
                            <i class="fas fa-user-tag"></i>
                            Change Role
                        </button>

                        <button onclick="changeMunicipalityModal.showModal()" class="btn btn-block btn-accent">
                            <i class="fas fa-map-marker-alt"></i>
                            Change Municipality
                        </button>
                    </div>
                </div>
            </div>

            <!-- Role Information -->
            <div class="card bg-white shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">
                        <i class="fas fa-info-circle text-primary"></i>
                        Role Permissions
                    </h2>
                    <div class="space-y-2 text-sm">
                        @if($user->isAdmin())
                            <p><i class="fas fa-check text-success"></i> Full system access</p>
                            <p><i class="fas fa-check text-success"></i> Manage all users</p>
                            <p><i class="fas fa-check text-success"></i> View all municipalities</p>
                            <p><i class="fas fa-check text-success"></i> System configuration</p>
                        @elseif($user->isStaff())
                            <p><i class="fas fa-check text-success"></i> Manage incidents</p>
                            <p><i class="fas fa-check text-success"></i> Manage vehicles</p>
                            <p><i class="fas fa-check text-success"></i> Handle requests</p>
                            <p><i class="fas fa-check text-success"></i> View reports</p>
                        @elseif($user->isResponder())
                            <p><i class="fas fa-check text-success"></i> Respond to incidents</p>
                            <p><i class="fas fa-check text-success"></i> Update incident status</p>
                            <p><i class="fas fa-check text-success"></i> Mobile access</p>
                        @else
                            <p><i class="fas fa-check text-success"></i> Report incidents</p>
                            <p><i class="fas fa-check text-success"></i> Check request status</p>
                            <p><i class="fas fa-check text-success"></i> View public information</p>
                        @endif
                    </div>
                </div>
            </div>
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

<!-- Reset Password Modal -->
<dialog id="resetPasswordModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg">
            <i class="fas fa-key"></i> Reset Password
        </h3>
        <form id="resetPasswordForm" class="py-4 space-y-4">
            <div class="form-control">
                <label class="label">
                    <span class="label-text">New Password</span>
                </label>
                <input type="password" name="password" class="input input-bordered" required minlength="8">
            </div>
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Confirm Password</span>
                </label>
                <input type="password" name="password_confirmation" class="input input-bordered" required>
            </div>
        </form>
        <div class="modal-action">
            <button type="button" onclick="resetPasswordModal.close()" class="btn btn-ghost">Cancel</button>
            <button type="button" onclick="submitResetPassword()" class="btn btn-primary">Reset Password</button>
        </div>
    </div>
</dialog>

<!-- Change Role Modal -->
<dialog id="changeRoleModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg">
            <i class="fas fa-user-tag"></i> Change Role
        </h3>
        <form id="changeRoleForm" class="py-4">
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Select New Role</span>
                </label>
                <select name="role" class="select select-bordered" required>
                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>Staff</option>
                    <option value="responder" {{ $user->role === 'responder' ? 'selected' : '' }}>Responder</option>
                    <option value="citizen" {{ $user->role === 'citizen' ? 'selected' : '' }}>Citizen</option>
                </select>
            </div>
        </form>
        <div class="modal-action">
            <button type="button" onclick="changeRoleModal.close()" class="btn btn-ghost">Cancel</button>
            <button type="button" onclick="submitChangeRole()" class="btn btn-primary">Change Role</button>
        </div>
    </div>
</dialog>

<!-- Change Municipality Modal -->
<dialog id="changeMunicipalityModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg">
            <i class="fas fa-map-marker-alt"></i> Change Municipality
        </h3>
        <form id="changeMunicipalityForm" class="py-4">
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Select Municipality</span>
                </label>
                <select name="municipality" class="select select-bordered" required>
                    @foreach(\App\Services\LocationService::getMunicipalitiesForSelect() as $municipality)
                        <option value="{{ $municipality }}" {{ $user->municipality === $municipality ? 'selected' : '' }}>
                            {{ $municipality }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
        <div class="modal-action">
            <button type="button" onclick="changeMunicipalityModal.close()" class="btn btn-ghost">Cancel</button>
            <button type="button" onclick="submitChangeMunicipality()" class="btn btn-primary">Change Municipality</button>
        </div>
    </div>
</dialog>

@endsection

@push('scripts')
<script>
    const userId = {{ $user->id }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function toggleStatus() {
        if (!confirm('Are you sure you want to {{ $user->is_active ? "deactivate" : "activate" }} this user?')) {
            return;
        }

        fetch(`/users/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast(data.message);
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showErrorToast(data.error || 'Failed to update status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('An error occurred');
        });
    }

    function verifyEmail() {
        if (!confirm('Manually verify this user\'s email?')) {
            return;
        }

        fetch(`/users/${userId}/verify-email`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast(data.message);
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showErrorToast(data.error || 'Failed to verify email');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('An error occurred');
        });
    }

    function unlockAccount() {
        if (!confirm('Unlock this user\'s account?')) {
            return;
        }

        fetch(`/users/${userId}/unlock`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast(data.message);
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showErrorToast(data.error || 'Failed to unlock account');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('An error occurred');
        });
    }

    function submitResetPassword() {
        const form = document.getElementById('resetPasswordForm');
        const formData = new FormData(form);

        if (formData.get('password') !== formData.get('password_confirmation')) {
            showErrorToast('Passwords do not match');
            return;
        }

        fetch(`/users/${userId}/reset-password`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                password: formData.get('password'),
                password_confirmation: formData.get('password_confirmation')
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast(data.message);
                resetPasswordModal.close();
                form.reset();
            } else {
                showErrorToast(data.error || 'Failed to reset password');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('An error occurred');
        });
    }

    function submitChangeRole() {
        const form = document.getElementById('changeRoleForm');
        const formData = new FormData(form);

        fetch(`/users/${userId}/assign-role`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                role: formData.get('role')
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast(data.message);
                changeRoleModal.close();
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showErrorToast(data.error || 'Failed to change role');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('An error occurred');
        });
    }

    function submitChangeMunicipality() {
        const form = document.getElementById('changeMunicipalityForm');
        const formData = new FormData(form);

        fetch(`/users/${userId}/assign-municipality`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                municipality: formData.get('municipality')
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast(data.message);
                changeMunicipalityModal.close();
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showErrorToast(data.error || 'Failed to change municipality');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('An error occurred');
        });
    }
</script>
@endpush