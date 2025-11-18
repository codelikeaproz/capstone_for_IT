@extends('Layouts.app')

@section('title', 'User Management - BukidnonAlert')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- Page Header --}}
        <header class="mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-users text-primary" aria-hidden="true"></i>
                        <span>User Management</span>
                    </h1>
                    <p class="text-base text-gray-600 mt-1">Manage system users, roles, and permissions</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <a href="{{ route('users.create') }}" class="btn btn-primary gap-2 w-full sm:w-auto min-h-[44px]">
                        <i class="fas fa-plus" aria-hidden="true"></i>
                        <span>Add New User</span>
                    </a>
                </div>
            </div>
        </header>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6" role="region" aria-label="User statistics">
            {{-- Total Users --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <i class="fas fa-users text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">Total Users</div>
                    <div class="stat-value text-primary">{{ number_format($stats['total']) }}</div>
                    <div class="stat-desc text-sm text-gray-500">All registered users</div>
                </div>
            </div>

            {{-- Active Users --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-success">
                        <i class="fas fa-user-check text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">Active Users</div>
                    <div class="stat-value text-success">{{ number_format($stats['active']) }}</div>
                    <div class="stat-desc text-sm text-gray-500">Currently active</div>
                </div>
            </div>

            {{-- Administrators --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-error">
                        <i class="fas fa-user-shield text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">Administrators</div>
                    <div class="stat-value text-error">{{ number_format($stats['admins']) }}</div>
                    <div class="stat-desc text-sm text-gray-500">Admin role users</div>
                </div>
            </div>

            {{-- Inactive Users --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-warning">
                        <i class="fas fa-user-times text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">Inactive Users</div>
                    <div class="stat-value text-warning">{{ number_format($stats['inactive']) }}</div>
                    <div class="stat-desc text-sm text-gray-500">Deactivated accounts</div>
                </div>
            </div>
        </div>

        {{-- Main Table Card --}}
        <div class="card bg-white shadow-lg">
            <div class="card-body p-0">
                <div class="px-4 py-6 border-b border-gray-200">
                    <div class="flex flex-row justify-between gap-6">
                        <div class="flex-shrink-0">
                            <h2 class="text-xl font-semibold text-gray-800">User Management</h2>
                            <p class="text-sm text-gray-500 mt-2">
                                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ number_format($users->total()) }} results
                            </p>
                        </div>
                        <form method="GET" action="{{ route('users.index') }}" class="flex-shrink-0 lg:ml-auto">
                            <div class="flex flex-wrap items-end gap-3">
                                {{-- Search Input --}}
                                <div class="form-control">
                                    <label for="search" class="label">
                                        <span class="label-text font-medium text-gray-700 my-1">Search</span>
                                    </label>
                                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                                           placeholder="Name, email, phone..."
                                           class="input input-bordered w-full focus:outline-primary min-h-[44px] focus:border-primary">
                                </div>

                                {{-- Role Filter --}}
                                <div class="form-control">
                                    <label for="role" class="label">
                                        <span class="label-text font-medium text-gray-700 my-1">Role</span>
                                    </label>
                                    <select name="role" id="role" class="select select-bordered w-full focus:outline-primary min-h-[44px] focus:border-primary">
                                        <option value="" {{ request('role') === '' ? 'selected' : '' }}>All Roles</option>
                                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                                        <option value="responder" {{ request('role') === 'responder' ? 'selected' : '' }}>Responder</option>
                                        <option value="citizen" {{ request('role') === 'citizen' ? 'selected' : '' }}>Citizen</option>
                                    </select>
                                </div>

                                {{-- Municipality Filter (SuperAdmin Only) --}}
                                @if(Auth::user()->isSuperAdmin())
                                <div class="form-control">
                                    <label for="municipality" class="label">
                                        <span class="label-text font-medium text-gray-700 my-1">Municipality</span>
                                    </label>
                                    <select name="municipality" id="municipality" class="select select-bordered w-full focus:outline-primary min-h-[44px] focus:border-primary">
                                        <option value="" {{ request('municipality') === '' ? 'selected' : '' }}>All Municipalities</option>
                                        @foreach(\App\Services\LocationService::getMunicipalitiesForSelect() as $municipality)
                                            <option value="{{ $municipality }}" {{ request('municipality') === $municipality ? 'selected' : '' }}>
                                                {{ $municipality }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                {{-- Filter Actions --}}
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-medium text-gray-700 opacity-0">Actions</span>
                                    </label>
                                    <div class="flex gap-2">
                                        <button type="submit" class="btn btn-primary gap-2 min-h-[44px] px-6">
                                            <i class="fas fa-search" aria-hidden="true"></i>
                                            <span>Apply</span>
                                        </button>
                                        <a href="{{ route('users.index') }}" class="btn btn-outline gap-2 min-h-[44px]" aria-label="Clear all filters">
                                            <i class="fas fa-times" aria-hidden="true"></i>
                                            <span>Clear</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Active Filters Display --}}
                            @if(request('search') || request('role') || request('municipality'))
                            <div class="flex items-center gap-2 flex-wrap mt-3">
                                <span class="text-sm font-medium text-gray-700">Active filters:</span>
                                @if(request('search'))
                                    <span class="badge badge-primary gap-1">
                                        <span>Search: "{{ request('search') }}"</span>
                                    </span>
                                @endif
                                @if(request('role'))
                                    <span class="badge badge-info gap-1">
                                        <span>{{ ucfirst(request('role')) }}</span>
                                    </span>
                                @endif
                                @if(request('municipality'))
                                    <span class="badge badge-secondary gap-1">
                                        <span>{{ request('municipality') }}</span>
                                    </span>
                                @endif
                            </div>
                            @endif
                        </form>
                    </div>
                </div>
                <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="font-semibold text-gray-700">User</th>
                            <th class="font-semibold text-gray-700">Email</th>
                            <th class="font-semibold text-gray-700">Role</th>
                            <th class="font-semibold text-gray-700">Municipality</th>
                            <th class="font-semibold text-gray-700">Status</th>
                            <th class="font-semibold text-gray-700">Email Verified</th>
                            <th class="font-semibold text-gray-700">Last Login</th>
                            <th class="font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr class="hover">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar placeholder">
                                            <div class="bg-primary text-primary-content rounded-full w-10">
                                                <span class="text-sm">{{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-bold">{{ $user->full_name }}</div>
                                            @if($user->phone_number)
                                                <div class="text-sm text-gray-500">
                                                    <i class="fas fa-phone"></i> {{ $user->phone_number }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                        {{ $user->email }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $user->role_badge }} badge-sm">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-map-marker-alt text-gray-400"></i>
                                        {{ $user->municipality }}
                                    </div>
                                </td>
                                <td>
                                    <button onclick="toggleUserStatus({{ $user->id }}, {{ $user->is_active ? 'true' : 'false' }})"
                                            class="badge {{ $user->is_active ? 'badge-success' : 'badge-error' }} cursor-pointer hover:opacity-80">
                                        <i class="fas fa-{{ $user->is_active ? 'check' : 'times' }} mr-1"></i>
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>
                                <td>
                                    @if($user->email_verified_at)
                                        <span class="badge badge-success badge-sm">
                                            <i class="fas fa-check-circle mr-1"></i> Verified
                                        </span>
                                    @else
                                        <span class="badge badge-warning badge-sm">
                                            <i class="fas fa-exclamation-circle mr-1"></i> Unverified
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->last_login)
                                        <span class="text-sm" title="{{ $user->last_login->format('F d, Y h:i A') }}">
                                            {{ $user->last_login->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-sm">Never</span>
                                    @endif
                                </td>
                                {{-- Actions Dropdown --}}
                                <td>
                                    <div class="dropdown dropdown-end">
                                        <button type="button"
                                                tabindex="0"
                                                class="btn btn-ghost btn-sm min-h-[44px] min-w-[44px]"
                                                aria-label="Actions for {{ $user->full_name }}"
                                                aria-haspopup="true">
                                            <i class="fas fa-ellipsis-v" aria-hidden="true"></i>
                                        </button>
                                        <ul tabindex="0"
                                            class="dropdown-content z-10 menu p-2 shadow-lg bg-white rounded-box w-52 border border-gray-200"
                                            role="menu">
                                            <li role="none">
                                                <a href="{{ route('users.show', $user) }}"
                                                   class="flex items-center gap-3 text-gray-700 hover:bg-primary hover:text-white min-h-[44px]"
                                                   role="menuitem">
                                                    <i class="fas fa-eye w-4" aria-hidden="true"></i>
                                                    <span>View Details</span>
                                                </a>
                                            </li>
                                            <li role="none">
                                                <a href="{{ route('users.edit', $user) }}"
                                                   class="flex items-center gap-3 text-gray-700 hover:bg-primary hover:text-white min-h-[44px]"
                                                   role="menuitem">
                                                    <i class="fas fa-edit w-4" aria-hidden="true"></i>
                                                    <span>Edit User</span>
                                                </a>
                                            </li>
                                            @if(!$user->email_verified_at)
                                                <li role="none">
                                                    <button type="button"
                                                            onclick="resendVerificationEmail({{ $user->id }}, '{{ $user->email }}')"
                                                            class="flex items-center gap-3 text-gray-700 hover:bg-primary hover:text-white min-h-[44px]"
                                                            role="menuitem">
                                                        <i class="fas fa-envelope w-4" aria-hidden="true"></i>
                                                        <span>Resend Verification Email</span>
                                                    </button>
                                                </li>
                                            @endif
                                            @if($user->id !== Auth::id())
                                                <div class="divider my-0"></div>
                                                <li role="none">
                                                    <button type="button"
                                                            onclick="deleteUser({{ $user->id }}, '{{ $user->full_name }}')"
                                                            class="flex items-center gap-3 text-error hover:bg-error hover:text-white min-h-[44px]"
                                                            role="menuitem">
                                                        <i class="fas fa-trash w-4" aria-hidden="true"></i>
                                                        <span>Delete User</span>
                                                    </button>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                @if($users->hasPages())
                    <div class="border-t border-gray-200 px-6 py-4">
                        {{ $users->links() }}
                    </div>
                @endif

                {{-- Empty State --}}
                @if($users->count() === 0)
                    <div class="text-center py-16 px-4">
                        <i class="fas fa-users text-6xl text-gray-300 mb-4" aria-hidden="true"></i>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No Users Found</h3>
                        <p class="text-gray-500 mb-6">
                            @if(request('search') || request('role') || request('municipality'))
                                No users match your current filters. Try adjusting your search criteria.
                            @else
                                There are no users to display. Start by adding a new user.
                            @endif
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            @if(request('search') || request('role') || request('municipality'))
                                <a href="{{ route('users.index') }}" class="btn btn-outline gap-2">
                                    <i class="fas fa-times" aria-hidden="true"></i>
                                    <span>Clear Filters</span>
                                </a>
                            @else
                                <a href="{{ route('users.create') }}" class="btn btn-primary gap-2">
                                    <i class="fas fa-plus" aria-hidden="true"></i>
                                    <span>Add First User</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    </div>
</div>
@endsection

<!-- Delete Confirmation Modal -->
<dialog id="deleteModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg text-error">
            <i class="fas fa-exclamation-triangle"></i> Confirm Deletion
        </h3>
        <p class="py-4">Are you sure you want to delete user <strong id="deleteUserName"></strong>?</p>
        <p class="text-sm text-gray-500">This action cannot be undone.</p>
        <div class="modal-action">
            <form id="deleteForm" method="POST">
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

@push('scripts')
<script>
    function deleteUser(userId, userName) {
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deleteForm');
        const nameSpan = document.getElementById('deleteUserName');

        nameSpan.textContent = userName;
        form.action = `/users/${userId}`;
        modal.showModal();
    }

    // Handle delete form submission with AJAX
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForm = document.getElementById('deleteForm');
        if (deleteForm) {
            let isDeleting = false;

            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();

                if (isDeleting) {
                    console.log('Delete already in progress...');
                    return;
                }

                isDeleting = true;

                const submitBtn = this.querySelector('button[type="submit"]');
                const cancelBtn = this.querySelector('button[type="button"]');
                const originalText = submitBtn.innerHTML;

                // Disable buttons and show loading
                submitBtn.disabled = true;
                cancelBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';

                const formData = new FormData(this);
                const action = this.action;

                fetch(action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || `HTTP ${response.status}: ${response.statusText}`);
                        }).catch(() => {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        console.log('Delete successful, showing toast...');

                        // Close modal
                        setTimeout(() => {
                            deleteModal.close();
                        }, 100);

                        // Show success toast
                        setTimeout(() => {
                            showSuccessToast(data.message || 'User deleted successfully!');
                        }, 200);

                        // Redirect after toast is visible
                        setTimeout(() => {
                            window.location.href = '{{ route('users.index') }}';
                        }, 2000);
                    } else {
                        submitBtn.disabled = false;
                        cancelBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                        isDeleting = false;
                        deleteModal.close();

                        // Show error toast
                        setTimeout(() => {
                            showErrorToast(data.message || 'Failed to delete user.');
                        }, 200);
                    }
                })
                .catch(error => {
                    submitBtn.disabled = false;
                    cancelBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    isDeleting = false;
                    deleteModal.close();

                    // Show error toast
                    setTimeout(() => {
                        showErrorToast(error.message || 'An error occurred while deleting the user.');
                    }, 200);
                    console.error('Delete error:', error);
                });
            });
        }
    });

    function toggleUserStatus(userId, currentStatus) {
        if (!confirm(`Are you sure you want to ${currentStatus ? 'deactivate' : 'activate'} this user?`)) {
            return;
        }

        fetch(`/users/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast(data.message);
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showErrorToast(data.error || 'Failed to update user status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('An error occurred while updating user status');
        });
    }

    function resendVerificationEmail(userId, userEmail) {
        if (!confirm(`Send verification email to ${userEmail}?`)) {
            return;
        }

        fetch(`/users/${userId}/resend-verification`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast(data.message || 'Verification email sent successfully!');
            } else {
                showErrorToast(data.error || 'Failed to send verification email');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('An error occurred while sending verification email');
        });
    }
</script>
@endpush
