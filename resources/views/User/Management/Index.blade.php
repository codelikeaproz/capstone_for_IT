@extends('Layouts.app')

@section('title', 'User Management - BukidnonAlert')

@section('content')
<div class="container max-w-full px-6 py-6">
    <!-- Header with Breadcrumbs -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm breadcrumbs mb-4">
                <ul>
                    <li><a href="{{ route('dashboard') }}"><i class="fas fa-home "></i> Home</a></li>
                    <li>Users</li>
                </ul>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-users text-primary"></i> User Management
            </h1>
            <p class="text-gray-600 mt-1">Manage system users, roles, and permissions</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New User
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="card bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm">Total Users</p>
                        <p class="text-3xl font-bold">{{ $stats['total'] }}</p>
                    </div>
                    <i class="fas fa-users text-4xl text-blue-200"></i>
                </div>
            </div>
        </div>

        <div class="card bg-gradient-to-r from-green-500 to-green-600 text-white shadow-lg">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm">Active Users</p>
                        <p class="text-3xl font-bold">{{ $stats['active'] }}</p>
                    </div>
                    <i class="fas fa-user-check text-4xl text-green-200"></i>
                </div>
            </div>
        </div>

        <div class="card bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm">Administrators</p>
                        <p class="text-3xl font-bold">{{ $stats['admins'] }}</p>
                    </div>
                    <i class="fas fa-user-shield text-4xl text-red-200"></i>
                </div>
            </div>
        </div>

        <div class="card bg-gradient-to-r from-yellow-500 to-yellow-600 text-white shadow-lg">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-sm">Inactive Users</p>
                        <p class="text-3xl font-bold">{{ $stats['inactive'] }}</p>
                    </div>
                    <i class="fas fa-user-times text-4xl text-yellow-200"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card bg-white shadow-lg mb-6">
        <div class="card-body">
            <h2 class="card-title mb-4">
                <i class="fas fa-filter text-blue-500"></i>
                Filters
            </h2>
            <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Search -->
                <div class="form-control">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Name, email, phone..."
                           class="input input-bordered w-full input-sm">
                </div>

                <!-- Role Filter -->
                <div class="form-control">
                    <select name="role" class="select select-bordered select-sm">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="responder" {{ request('role') === 'responder' ? 'selected' : '' }}>Responder</option>
                        <option value="citizen" {{ request('role') === 'citizen' ? 'selected' : '' }}>Citizen</option>
                    </select>
                </div>

                <!-- Municipality Filter -->
                <div class="form-control">
                    <select name="municipality" class="select select-bordered select-sm">
                        <option value="">All Municipalities</option>
                        @foreach(\App\Services\LocationService::getMunicipalitiesForSelect() as $municipality)
                            <option value="{{ $municipality }}" {{ request('municipality') === $municipality ? 'selected' : '' }}>
                                {{ $municipality }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="form-control">
                    <select name="status" class="select select-bordered select-sm">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-1">
                        <i class="fas fa-filter"></i> Apply
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline btn-sm">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card bg-white shadow-lg">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-base-200">
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Municipality</th>
                            <th>Status</th>
                            <th>Email Verified</th>
                            <th>Last Login</th>
                            <th class="text-center">Actions</th>
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
                                <td onclick="event.stopPropagation()">
                                    <div class="dropdown dropdown-end">
                                        <label tabindex="0" class="btn btn-ghost btn-sm btn-circle">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </label>
                                        <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                            <li>
                                                <a href="{{ route('users.show', $user) }}">
                                                    <i class="fas fa-eye mr-2"></i>View Details
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('users.edit', $user) }}">
                                                    <i class="fas fa-edit mr-2"></i>Edit
                                                </a>
                                            </li>
                                            @if($user->id !== Auth::id())
                                                <li>
                                                    <button type="button" onclick="event.stopPropagation(); deleteUser({{ $user->id }}, '{{ $user->full_name }}')" class="text-red-600">
                                                        <i class="fas fa-trash mr-2"></i>Delete
                                                    </button>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-12">
                                    <div class="text-gray-400">
                                        <i class="fas fa-users text-6xl mb-4"></i>
                                        <h3 class="text-xl font-semibold text-gray-600 mb-2">No users found</h3>
                                        <p class="text-gray-500 mb-4">No users match your current filters.</p>
                                        <a href="{{ route('users.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus mr-2"></i>Add First User
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="p-4 border-t bg-white">
                    {{ $users->links() }}
                </div>
            @endif
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
</script>
@endpush
