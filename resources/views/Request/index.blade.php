@extends("Layouts.app")

@section('title', 'Request Management - MDRRMC')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- Page Header --}}
        <header class="mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-clipboard-list text-primary" aria-hidden="true"></i>
                        <span>Request Management</span>
                    </h1>
                    <p class="text-base text-gray-600 mt-1">Review and process citizen report requests</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <a href="{{ route('requests.create') }}" class="btn btn-primary gap-2 w-full sm:w-auto min-h-[44px]">
                        <i class="fas fa-plus" aria-hidden="true"></i>
                        <span>New Request</span>
                    </a>
                    <button type="button" class="btn btn-outline gap-2 w-full sm:w-auto min-h-[44px]" onclick="window.location.reload()" aria-label="Refresh request list">
                        <i class="fas fa-sync-alt" aria-hidden="true"></i>
                        <span>Refresh</span>
                    </button>
                </div>
            </div>
        </header>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6" role="region" aria-label="Request statistics">
            {{-- Total Requests --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <i class="fas fa-clipboard-list text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">Total Requests</div>
                    <div class="stat-value text-primary">{{ $stats['total'] }}</div>
                    <div class="stat-desc text-sm text-gray-500">All time</div>
                </div>
            </div>

            {{-- Pending --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-warning">
                        <i class="fas fa-clock text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">Pending Review</div>
                    <div class="stat-value text-warning">{{ $stats['pending'] }}</div>
                    <div class="stat-desc text-sm text-gray-500">Awaiting review</div>
                </div>
            </div>

            {{-- Processing --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-info">
                        <i class="fas fa-spinner fa-pulse text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">In Progress</div>
                    <div class="stat-value text-info">{{ $stats['processing'] }}</div>
                    <div class="stat-desc text-sm text-gray-500">Being processed</div>
                </div>
            </div>

            {{-- Completed --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-success">
                        <i class="fas fa-check-circle text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">Completed</div>
                    <div class="stat-value text-success">{{ $stats['completed'] }}</div>
                    <div class="stat-desc text-sm text-gray-500">Approved/Done</div>
                </div>
            </div>

            {{-- Urgent --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-error">
                        <i class="fas fa-exclamation-triangle text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">Urgent</div>
                    <div class="stat-value text-error">{{ $stats['urgent'] }}</div>
                    <div class="stat-desc text-sm text-gray-500">High/Critical priority</div>
                </div>
            </div>
        </div>

        {{-- Requests Table Card --}}
        <div class="card bg-white shadow-lg">
            <div class="card-body p-0">
                <div class="px-4 py-6 border-b border-gray-200">
                    <div class="flex flex-row justify-between gap-6">
                        <div class="flex-shrink-0">
                            <h2 class="text-xl font-semibold text-gray-800">Request Management</h2>
                            <p class="text-sm text-gray-500 mt-2">
                                Showing {{ $requests->firstItem() ?? 0 }} to {{ $requests->lastItem() ?? 0 }} of {{ number_format($requests->total()) }} results
                            </p>
                        </div>
                        <form method="GET" action="{{ route('requests.index') }}" class="flex-shrink-0 lg:ml-auto">
                            <div class="flex flex-wrap items-end gap-3">
                                {{-- Search Input --}}
                                <div class="form-control">
                                    <label for="search" class="label">
                                        <span class="label-text font-medium text-gray-700 my-1">Search</span>
                                    </label>
                                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                                           placeholder="Request #, requester, email..."
                                           class="input input-bordered w-full focus:outline-primary min-h-[44px] focus:border-primary">
                                </div>

                                {{-- Municipality Filter (SuperAdmin Only) --}}
                                @if(Auth::user()->isSuperAdmin())
                                <div class="form-control">
                                    <label for="municipality" class="label">
                                        <span class="label-text font-medium text-gray-700 my-1">Municipality</span>
                                    </label>
                                    <select name="municipality" id="municipality" class="select select-bordered w-full focus:outline-primary min-h-[44px] focus:border-primary">
                                        <option value="" {{ request('municipality') === '' ? 'selected' : '' }}>All Municipalities</option>
                                        @foreach(array_keys(config('locations.municipalities')) as $municipality)
                                            <option value="{{ $municipality }}" {{ request('municipality') === $municipality ? 'selected' : '' }}>
                                                {{ $municipality }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                {{-- Request Type Filter --}}
                                <div class="form-control">
                                    <label for="request_type" class="label">
                                        <span class="label-text font-medium text-gray-700 my-1">Request Type</span>
                                    </label>
                                    <select name="request_type" id="request_type" class="select select-bordered w-full focus:outline-primary min-h-[44px] focus:border-primary">
                                        <option value="" {{ request('request_type') === '' ? 'selected' : '' }}>All Types</option>
                                        <option value="incident_report" {{ request('request_type') === 'incident_report' ? 'selected' : '' }}>Incident Report</option>
                                        <option value="traffic_accident_report" {{ request('request_type') === 'traffic_accident_report' ? 'selected' : '' }}>Traffic Accident</option>
                                        <option value="medical_emergency_report" {{ request('request_type') === 'medical_emergency_report' ? 'selected' : '' }}>Medical Emergency</option>
                                        <option value="fire_incident_report" {{ request('request_type') === 'fire_incident_report' ? 'selected' : '' }}>Fire Incident</option>
                                        <option value="general_emergency_report" {{ request('request_type') === 'general_emergency_report' ? 'selected' : '' }}>General Emergency</option>
                                        <option value="vehicle_accident_report" {{ request('request_type') === 'vehicle_accident_report' ? 'selected' : '' }}>Vehicle Accident</option>
                                    </select>
                                </div>

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
                                        <a href="{{ route('requests.index') }}" class="btn btn-outline gap-2 min-h-[44px]" aria-label="Clear all filters">
                                            <i class="fas fa-times" aria-hidden="true"></i>
                                            <span>Clear</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Active Filters Display --}}
                            @if(request('search') || request('municipality') || request('request_type'))
                            <div class="flex items-center gap-2 flex-wrap mt-3">
                                <span class="text-sm font-medium text-gray-700">Active filters:</span>
                                @if(request('search'))
                                    <span class="badge badge-primary gap-1">
                                        <span>Search: "{{ request('search') }}"</span>
                                    </span>
                                @endif
                                @if(request('municipality'))
                                    <span class="badge badge-secondary gap-1">
                                        <span>{{ request('municipality') }}</span>
                                    </span>
                                @endif
                                @if(request('request_type'))
                                    <span class="badge badge-info gap-1">
                                        <span>{{ str_replace('_report', '', str_replace('_', ' ', ucwords(request('request_type'), '_'))) }}</span>
                                    </span>
                                @endif
                            </div>
                            @endif
                        </form>
                    </div>
                </div>

                {{-- Bulk Actions --}}
                @if($requests->count() > 0)
                <div class="border-b border-gray-200 px-4 py-3">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="selectAll" class="checkbox checkbox-primary" onclick="toggleSelectAll()">
                            <span class="text-sm font-medium">Select All</span>
                        </label>
                        <div class="flex gap-2 flex-wrap">
                            <button type="button" onclick="bulkApprove()" class="btn btn-success btn-sm gap-2" id="bulkApproveBtn" disabled>
                                <i class="fas fa-check"></i>
                                <span>Approve Selected</span>
                            </button>
                            <button type="button" onclick="bulkReject()" class="btn btn-error btn-sm gap-2" id="bulkRejectBtn" disabled>
                                <i class="fas fa-times"></i>
                                <span>Reject Selected</span>
                            </button>
                        </div>
                        <span class="text-sm text-gray-600 ml-auto" id="selectedCount">0 selected</span>
                    </div>
                </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="w-12">
                                    <span class="sr-only">Select</span>
                                </th>
                                <th class="font-semibold text-gray-700">Request #</th>
                                <th class="font-semibold text-gray-700">Requester</th>
                                <th class="font-semibold text-gray-700">Type</th>
                                <th class="font-semibold text-gray-700">Municipality</th>
                                <th class="font-semibold text-gray-700">Urgency</th>
                                <th class="font-semibold text-gray-700">Status</th>
                                <th class="font-semibold text-gray-700">Date</th>
                                <th class="font-semibold text-gray-700 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $request)
                                <tr class="hover {{ $request->urgency_level === 'critical' ? 'bg-red-50' : ($request->urgency_level === 'high' ? 'bg-orange-50' : '') }}">
                                    <td>
                                        @if(in_array($request->status, ['pending', 'processing']))
                                        <input type="checkbox" class="checkbox checkbox-sm request-checkbox" value="{{ $request->id }}" onchange="updateBulkActions()">
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('requests.show', $request) }}" class="font-mono font-bold text-primary hover:underline">
                                            {{ $request->request_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $request->requester_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $request->requester_email }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            @php
                                                $typeIcons = [
                                                    'traffic_accident_report' => ['icon' => 'fa-car-crash', 'color' => 'text-orange-600'],
                                                    'medical_emergency_report' => ['icon' => 'fa-heartbeat', 'color' => 'text-red-600'],
                                                    'fire_incident_report' => ['icon' => 'fa-fire', 'color' => 'text-red-500'],
                                                    'general_emergency_report' => ['icon' => 'fa-exclamation-triangle', 'color' => 'text-yellow-600'],
                                                    'vehicle_accident_report' => ['icon' => 'fa-ambulance', 'color' => 'text-blue-600'],
                                                    'incident_report' => ['icon' => 'fa-clipboard-list', 'color' => 'text-gray-600'],
                                                ];
                                                $typeData = $typeIcons[$request->request_type] ?? ['icon' => 'fa-file-alt', 'color' => 'text-gray-600'];
                                            @endphp
                                            <i class="fas {{ $typeData['icon'] }} {{ $typeData['color'] }}"></i>
                                            <span class="text-sm">{{ str_replace('_report', '', str_replace('_', ' ', ucwords($request->request_type))) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-sm">{{ $request->municipality }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $urgencyBadge = [
                                                'low' => 'badge-success',
                                                'medium' => 'badge-warning',
                                                'high' => 'badge-warning',
                                                'critical' => 'badge-error',
                                            ];
                                        @endphp
                                        <span class="badge {{ $urgencyBadge[$request->urgency_level] ?? 'badge-ghost' }} badge-sm">
                                            {{ ucfirst($request->urgency_level) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $request->status_badge }} badge-sm">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-sm text-gray-600">
                                            {{ $request->created_at->format('M d, Y') }}
                                            <div class="text-xs text-gray-500">{{ $request->created_at->format('g:i A') }}</div>
                                        </div>
                                    </td>
                                    {{-- Actions Dropdown --}}
                                    <td>
                                        <div class="dropdown dropdown-end">
                                            <button type="button"
                                                    tabindex="0"
                                                    class="btn btn-ghost btn-sm min-h-[44px] min-w-[44px]"
                                                    aria-label="Actions for request {{ $request->request_number }}"
                                                    aria-haspopup="true">
                                                <i class="fas fa-ellipsis-v" aria-hidden="true"></i>
                                            </button>
                                            <ul tabindex="0"
                                                class="dropdown-content z-10 menu p-2 shadow-lg bg-white rounded-box w-52 border border-gray-200"
                                                role="menu">
                                                <li role="none">
                                                    <a href="{{ route('requests.show', $request) }}"
                                                       class="flex items-center gap-3 text-gray-700 hover:bg-primary hover:text-white min-h-[44px]"
                                                       role="menuitem">
                                                        <i class="fas fa-eye w-4" aria-hidden="true"></i>
                                                        <span>View Details</span>
                                                    </a>
                                                </li>
                                                @if(in_array($request->status, ['pending', 'processing']))
                                                    <li role="none">
                                                        <a href="{{ route('requests.edit', $request) }}"
                                                           class="flex items-center gap-3 text-gray-700 hover:bg-primary hover:text-white min-h-[44px]"
                                                           role="menuitem">
                                                            <i class="fas fa-edit w-4" aria-hidden="true"></i>
                                                            <span>Edit Request</span>
                                                        </a>
                                                    </li>
                                                @endif
                                                @if(auth()->user()->role === 'admin')
                                                    <div class="divider my-0"></div>
                                                    <li role="none">
                                                        <button type="button"
                                                                onclick="deleteRequest({{ $request->id }}, '{{ $request->request_number }}')"
                                                                class="flex items-center gap-3 text-error hover:bg-error hover:text-white min-h-[44px]"
                                                                role="menuitem">
                                                            <i class="fas fa-trash w-4" aria-hidden="true"></i>
                                                            <span>Delete Request</span>
                                                        </button>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-16">
                                        <div class="flex flex-col items-center gap-4">
                                            <i class="fas fa-inbox text-6xl text-gray-300" aria-hidden="true"></i>
                                            <div>
                                                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Requests Found</h3>
                                                <p class="text-gray-500 mb-6">
                                                    @if(request('search') || request('request_type'))
                                                        No requests match your current filters. Try adjusting your search criteria.
                                                    @else
                                                        There are no requests to display yet.
                                                    @endif
                                                </p>
                                            </div>
                                            @if(request('search') || request('request_type'))
                                                <a href="{{ route('requests.index') }}" class="btn btn-outline gap-2">
                                                    <i class="fas fa-times" aria-hidden="true"></i>
                                                    <span>Clear Filters</span>
                                                </a>
                                            @else
                                                <a href="{{ route('requests.create') }}" class="btn btn-primary gap-2">
                                                    <i class="fas fa-plus" aria-hidden="true"></i>
                                                    <span>Create New Request</span>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($requests->hasPages())
                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $requests->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Bulk Approve Modal --}}
<dialog id="bulkApproveModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg text-success mb-4">
            <i class="fas fa-check-circle mr-2"></i>
            Bulk Approve Requests
        </h3>
        <p class="py-4">Are you sure you want to approve the selected requests?</p>
        <div class="form-control mb-4">
            <label class="label">
                <span class="label-text">Approval Notes (Optional)</span>
            </label>
            <textarea id="bulkApprovalNotes" class="textarea textarea-bordered" rows="3" placeholder="Enter any notes for the approval..."></textarea>
        </div>
        <div class="modal-action">
            <form method="dialog">
                <button class="btn btn-outline">Cancel</button>
            </form>
            <button onclick="confirmBulkApprove()" class="btn btn-success">
                <i class="fas fa-check"></i>
                Approve Selected
            </button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

{{-- Bulk Reject Modal --}}
<dialog id="bulkRejectModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg text-error mb-4">
            <i class="fas fa-times-circle mr-2"></i>
            Bulk Reject Requests
        </h3>
        <p class="py-4">Are you sure you want to reject the selected requests?</p>
        <div class="form-control mb-4">
            <label class="label">
                <span class="label-text">Rejection Reason <span class="text-error">*</span></span>
            </label>
            <textarea id="bulkRejectionReason" class="textarea textarea-bordered" rows="3" placeholder="Enter the reason for rejection..." required></textarea>
        </div>
        <div class="modal-action">
            <form method="dialog">
                <button class="btn btn-outline">Cancel</button>
            </form>
            <button onclick="confirmBulkReject()" class="btn btn-error">
                <i class="fas fa-times"></i>
                Reject Selected
            </button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

@endsection

@push('scripts')
<script>
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.request-checkbox');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateBulkActions();
    }

    function updateBulkActions() {
        const checkboxes = document.querySelectorAll('.request-checkbox:checked');
        const count = checkboxes.length;
        const approveBtn = document.getElementById('bulkApproveBtn');
        const rejectBtn = document.getElementById('bulkRejectBtn');
        const countSpan = document.getElementById('selectedCount');

        countSpan.textContent = `${count} selected`;
        approveBtn.disabled = count === 0;
        rejectBtn.disabled = count === 0;
    }

    function bulkApprove() {
        document.getElementById('bulkApproveModal').showModal();
    }

    function bulkReject() {
        document.getElementById('bulkRejectModal').showModal();
    }

    function confirmBulkApprove() {
        const checkboxes = document.querySelectorAll('.request-checkbox:checked');
        const requestIds = Array.from(checkboxes).map(cb => cb.value);
        const notes = document.getElementById('bulkApprovalNotes').value;

        fetch('{{ route("requests.bulk-approve") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                request_ids: requestIds,
                approval_notes: notes
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function confirmBulkReject() {
        const reason = document.getElementById('bulkRejectionReason').value.trim();
        if (!reason) {
            alert('Please provide a rejection reason');
            return;
        }

        const checkboxes = document.querySelectorAll('.request-checkbox:checked');
        const requestIds = Array.from(checkboxes).map(cb => cb.value);

        fetch('{{ route("requests.bulk-reject") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                request_ids: requestIds,
                rejection_reason: reason
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function deleteRequest(requestId, requestNumber) {
        if (!confirm(`Are you sure you want to delete request ${requestNumber}? This action cannot be undone.`)) {
            return;
        }

        fetch(`/requests/${requestId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-HTTP-Method-Override': 'DELETE'
            },
            body: JSON.stringify({
                _method: 'DELETE'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast(data.message || 'Request deleted successfully!');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showErrorToast(data.error || 'Failed to delete request');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('An error occurred while deleting the request');
        });
    }
</script>
@endpush






