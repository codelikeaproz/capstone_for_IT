@extends("Layouts.app")

@section('title', 'Incident Management - MDRRMC')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- Page Header --}}
        <header class="mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-exclamation-triangle text-error" aria-hidden="true"></i>
                        <span>Incident Management</span>
                    </h1>
                    <p class="text-base text-gray-600 mt-1">Monitor and manage emergency incidents across Bukidnon</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <a href="{{ route('incidents.create') }}" class="btn btn-primary gap-2 w-full sm:w-auto min-h-[44px]">
                        <i class="fas fa-plus" aria-hidden="true"></i>
                        <span>Report New Incident</span>
                    </a>
                    <button type="button" class="btn btn-outline gap-2 w-full sm:w-auto min-h-[44px]" onclick="refreshData()" aria-label="Refresh incident list">
                        <i class="fas fa-sync-alt" aria-hidden="true"></i>
                        <span>Refresh</span>
                    </button>
                </div>
            </div>
        </header>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6" role="region" aria-label="Incident statistics">
            {{-- Critical Incidents --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-error">
                        <i class="fas fa-exclamation-circle text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">Critical Incidents</div>
                    <div class="stat-value text-error">{{ $incidents->where('severity_level', 'critical')->count() }}</div>
                    <div class="stat-desc text-sm text-gray-500">Requires immediate attention</div>
                </div>
            </div>

            {{-- High Priority --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-warning">
                        <i class="fas fa-fire text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">High Priority</div>
                    <div class="stat-value text-warning">{{ $incidents->where('severity_level', 'high')->count() }}</div>
                    <div class="stat-desc text-sm text-gray-500">Urgent response needed</div>
                </div>
            </div>

            {{-- Active Incidents --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-info">
                        <i class="fas fa-spinner fa-pulse text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">Active Incidents</div>
                    <div class="stat-value text-info">{{ $incidents->whereIn('status', ['pending', 'active'])->count() }}</div>
                    <div class="stat-desc text-sm text-gray-500">Currently in progress</div>
                </div>
            </div>

            {{-- Resolved Today --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-success">
                        <i class="fas fa-check-circle text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">Resolved Today</div>
                    <div class="stat-value text-success">{{ $incidents->where('status', 'resolved')->where('resolved_at', '>=', now()->startOfDay())->count() }}</div>
                    <div class="stat-desc text-sm text-gray-500">Since {{ now()->format('h:i A') }}</div>
                </div>
            </div>
        </div>

        {{-- Filters Card --}}
        <div class="card bg-white shadow-sm mb-6">
            <div class="card-body p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-filter text-primary" aria-hidden="true"></i>
                    <span>Filter Incidents</span>
                </h2>

                <form method="GET" action="{{ route('incidents.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        {{-- Municipality Filter - Dynamic from config --}}
                        <div class="form-control">
                            <label for="filter-municipality" class="label">
                                <span class="label-text font-medium text-gray-700">Municipality</span>
                            </label>
                            <select name="municipality" id="filter-municipality" class="select select-bordered w-full focus:outline-primary min-h-[44px]">
                                <option value="">All Municipalities</option>
                                @foreach(array_keys(config('locations.municipalities')) as $municipality)
                                    <option value="{{ $municipality }}" {{ request('municipality') == $municipality ? 'selected' : '' }}>
                                        {{ $municipality }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Severity Filter --}}
                        <div class="form-control">
                            <label for="filter-severity" class="label">
                                <span class="label-text font-medium text-gray-700">Severity Level</span>
                            </label>
                            <select name="severity" id="filter-severity" class="select select-bordered w-full focus:outline-primary">
                                <option value="">All Severities</option>
                                <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Critical</option>
                                <option value="high" {{ request('severity') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="low" {{ request('severity') == 'low' ? 'selected' : '' }}>Low</option>
                            </select>
                        </div>

                        {{-- Status Filter --}}
                        <div class="form-control">
                            <label for="filter-status" class="label">
                                <span class="label-text font-medium text-gray-700">Status</span>
                            </label>
                            <select name="status" id="filter-status" class="select select-bordered w-full focus:outline-primary">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>

                        {{-- Incident Type Filter --}}
                        <div class="form-control">
                            <label for="filter-type" class="label">
                                <span class="label-text font-medium text-gray-700">Incident Type</span>
                            </label>
                            <select name="incident_type" id="filter-type" class="select select-bordered w-full focus:outline-primary">
                                <option value="">All Types</option>
                                <option value="traffic_accident" {{ request('incident_type') == 'traffic_accident' ? 'selected' : '' }}>Traffic Accident</option>
                                <option value="medical_emergency" {{ request('incident_type') == 'medical_emergency' ? 'selected' : '' }}>Medical Emergency</option>
                                <option value="fire_incident" {{ request('incident_type') == 'fire_incident' ? 'selected' : '' }}>Fire Incident</option>
                                <option value="natural_disaster" {{ request('incident_type') == 'natural_disaster' ? 'selected' : '' }}>Natural Disaster</option>
                                <option value="criminal_activity" {{ request('incident_type') == 'criminal_activity' ? 'selected' : '' }}>Criminal Activity</option>
                                <option value="other" {{ request('incident_type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        {{-- Filter Actions --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium text-gray-700 opacity-0">Actions</span>
                            </label>
                            <div class="flex gap-2">
                                <button type="submit" class="btn btn-primary gap-2 flex-1 min-h-[44px]">
                                    <i class="fas fa-search" aria-hidden="true"></i>
                                    <span>Apply</span>
                                </button>
                                <a href="{{ route('incidents.index') }}" class="btn btn-outline min-h-[44px] min-w-[44px]" aria-label="Clear all filters">
                                    <i class="fas fa-times" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Active Filters Display --}}
                    @if(request()->hasAny(['municipality', 'severity', 'status', 'incident_type']))
                    <div class="flex items-center gap-2 flex-wrap pt-2 border-t border-gray-200">
                        <span class="text-sm font-medium text-gray-700">Active filters:</span>
                        @if(request('municipality'))
                            <span class="badge badge-primary gap-1">
                                <span>{{ request('municipality') }}</span>
                            </span>
                        @endif
                        @if(request('severity'))
                            <span class="badge badge-warning gap-1">
                                <span>{{ ucfirst(request('severity')) }} Severity</span>
                            </span>
                        @endif
                        @if(request('status'))
                            <span class="badge badge-info gap-1">
                                <span>{{ ucfirst(request('status')) }}</span>
                            </span>
                        @endif
                        @if(request('incident_type'))
                            <span class="badge badge-neutral gap-1">
                                <span>{{ ucwords(str_replace('_', ' ', request('incident_type'))) }}</span>
                            </span>
                        @endif
                    </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- Incidents Table Card --}}
        <div class="card bg-white shadow-lg">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    @if($incidents->count() > 0)
                        <table class="table table-zebra w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="font-semibold text-gray-700">Incident #</th>
                                    <th class="font-semibold text-gray-700">Type</th>
                                    <th class="font-semibold text-gray-700">Severity</th>
                                    <th class="font-semibold text-gray-700">Status</th>
                                    <th class="font-semibold text-gray-700">Location</th>
                                    <th class="font-semibold text-gray-700">Municipality</th>
                                    <th class="font-semibold text-gray-700">Date</th>
                                    <th class="font-semibold text-gray-700">Assigned Staff</th>
                                    <th class="font-semibold text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($incidents as $incident)
                                    <tr class="hover" data-incident-id="{{ $incident->id }}">
                                        {{-- Incident Number --}}
                                        <td>
                                            <a href="{{ route('incidents.show', $incident) }}" class="font-mono font-bold text-primary hover:underline focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded">
                                                {{ $incident->incident_number }}
                                            </a>
                                        </td>

                                        {{-- Incident Type with Icon --}}
                                        <td>
                                            <div class="flex items-center gap-2">
                                                @switch($incident->incident_type)
                                                    @case('traffic_accident')
                                                        <i class="fas fa-car-crash text-warning text-lg" aria-hidden="true"></i>
                                                        @break
                                                    @case('medical_emergency')
                                                        <i class="fas fa-heartbeat text-error text-lg" aria-hidden="true"></i>
                                                        @break
                                                    @case('fire_incident')
                                                        <i class="fas fa-fire text-error text-lg" aria-hidden="true"></i>
                                                        @break
                                                    @case('natural_disaster')
                                                        <i class="fas fa-cloud-bolt text-gray-600 text-lg" aria-hidden="true"></i>
                                                        @break
                                                    @case('criminal_activity')
                                                        <i class="fas fa-shield-alt text-lg" style="color: #7C3AED;" aria-hidden="true"></i>
                                                        @break
                                                    @default
                                                        <i class="fas fa-exclamation-circle text-gray-500 text-lg" aria-hidden="true"></i>
                                                @endswitch
                                                <span class="text-sm text-gray-700">{{ ucwords(str_replace('_', ' ', $incident->incident_type)) }}</span>
                                            </div>
                                        </td>

                                        {{-- Severity Badge --}}
                                        <td>
                                            <span class="badge {{ $incident->severity_level === 'critical' ? 'badge-error' : ($incident->severity_level === 'high' ? 'badge-warning' : ($incident->severity_level === 'medium' ? 'badge-info' : 'badge-success')) }} badge-lg">
                                                {{ ucfirst($incident->severity_level) }}
                                            </span>
                                        </td>

                                        {{-- Status Badge --}}
                                        <td>
                                            <div class="flex items-center gap-2">
                                                @if($incident->status === 'active')
                                                    <i class="fas fa-spinner fa-pulse text-info" aria-hidden="true"></i>
                                                @elseif($incident->status === 'resolved')
                                                    <i class="fas fa-check-circle text-success" aria-hidden="true"></i>
                                                @elseif($incident->status === 'pending')
                                                    <i class="fas fa-clock text-warning" aria-hidden="true"></i>
                                                @else
                                                    <i class="fas fa-archive text-gray-500" aria-hidden="true"></i>
                                                @endif
                                                <span class="badge {{ $incident->status_badge }}">
                                                    {{ ucfirst($incident->status) }}
                                                </span>
                                            </div>
                                        </td>

                                        {{-- Location --}}
                                        <td class="text-sm text-gray-700">{{ Str::limit($incident->location, 30) }}</td>

                                        {{-- Municipality --}}
                                        <td>
                                            <span class="text-sm font-medium text-gray-700">{{ $incident->municipality }}</span>
                                        </td>

                                        {{-- Date --}}
                                        <td>
                                            <div class="text-sm text-gray-700">
                                                <div class="font-medium">{{ $incident->incident_date->format('M d, Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ $incident->incident_date->format('h:i A') }}</div>
                                            </div>
                                        </td>

                                        {{-- Assigned Staff --}}
                                        <td>
                                            @if($incident->assignedStaff)
                                                <span class="text-sm font-medium text-gray-700">{{ $incident->assignedStaff->last_name}}</span>
                                            @else
                                                <span class="text-sm text-gray-500 italic">Unassigned</span>
                                            @endif
                                        </td>

                                        {{-- Actions Dropdown --}}
                                        <td>
                                            <div class="dropdown dropdown-end">
                                                <button type="button"
                                                        tabindex="0"
                                                        class="btn btn-ghost btn-sm min-h-[44px] min-w-[44px]"
                                                        aria-label="Actions for incident {{ $incident->incident_number }}"
                                                        aria-haspopup="true">
                                                    <i class="fas fa-ellipsis-v" aria-hidden="true"></i>
                                                </button>
                                                <ul tabindex="0"
                                                    class="dropdown-content z-10 menu p-2 shadow-lg bg-white rounded-box w-52 border border-gray-200"
                                                    role="menu">
                                                    <li role="none">
                                                        <a href="{{ route('incidents.show', $incident) }}"
                                                           class="flex items-center gap-3 text-gray-700 hover:bg-primary hover:text-white min-h-[44px]"
                                                           role="menuitem">
                                                            <i class="fas fa-eye w-4" aria-hidden="true"></i>
                                                            <span>View Details</span>
                                                        </a>
                                                    </li>
                                                    <li role="none">
                                                        <a href="{{ route('incidents.edit', $incident) }}"
                                                           class="flex items-center gap-3 text-gray-700 hover:bg-primary hover:text-white min-h-[44px]"
                                                           role="menuitem">
                                                            <i class="fas fa-edit w-4" aria-hidden="true"></i>
                                                            <span>Edit Incident</span>
                                                        </a>
                                                    </li>
                                                    @if($incident->latitude && $incident->longitude)
                                                        <li role="none">
                                                            <a href="https://maps.google.com?q={{ $incident->latitude }},{{ $incident->longitude }}"
                                                               target="_blank"
                                                               rel="noopener noreferrer"
                                                               class="flex items-center gap-3 text-gray-700 hover:bg-primary hover:text-white min-h-[44px]"
                                                               role="menuitem">
                                                                <i class="fas fa-map-marker-alt w-4" aria-hidden="true"></i>
                                                                <span>View on Map</span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <div class="divider my-0"></div>
                                                    @if(Auth::user()->role === 'admin')
                                                        <li role="none">
                                                            <button type="button"
                                                                    onclick="showDeleteModal({{ $incident->id }})"
                                                                    class="flex items-center gap-3 text-error hover:bg-error hover:text-white min-h-[44px]"
                                                                    role="menuitem">
                                                                <i class="fas fa-trash w-4" aria-hidden="true"></i>
                                                                <span>Delete Incident</span>
                                                            </button>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        @if($incidents->hasPages())
                            <div class="border-t border-gray-200 px-6 py-4">
                                {{ $incidents->links() }}
                            </div>
                        @endif
                    @else
                        {{-- Empty State --}}
                        <div class="text-center py-16 px-4">
                            <i class="fas fa-inbox text-6xl text-gray-300 mb-4" aria-hidden="true"></i>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Incidents Found</h3>
                            <p class="text-gray-500 mb-6">
                                @if(request()->hasAny(['municipality', 'severity', 'status', 'incident_type']))
                                    No incidents match your current filters. Try adjusting your search criteria.
                                @else
                                    There are no incidents to display. Start by reporting a new incident.
                                @endif
                            </p>
                            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                @if(request()->hasAny(['municipality', 'severity', 'status', 'incident_type']))
                                    <a href="{{ route('incidents.index') }}" class="btn btn-outline gap-2">
                                        <i class="fas fa-times" aria-hidden="true"></i>
                                        <span>Clear Filters</span>
                                    </a>
                                @endif
                                <a href="{{ route('incidents.create') }}" class="btn btn-primary gap-2">
                                    <i class="fas fa-plus" aria-hidden="true"></i>
                                    <span>Report New Incident</span>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Delete Confirmation Modal --}}
@if(Auth::user()->role === 'admin')
<dialog id="deleteModal" class="modal">
    <div class="modal-box max-w-md">
        <h3 class="font-bold text-lg text-error mb-4">
            <i class="fas fa-exclamation-triangle mr-2" aria-hidden="true"></i>
            Confirm Deletion
        </h3>
        <p class="py-4 text-gray-700">
            Are you sure you want to delete this incident? This action cannot be undone and will also remove all associated data including:
        </p>
        <ul class="list-disc list-inside text-sm text-gray-600 mb-4 space-y-1">
            <li>Incident photos and videos</li>
            <li>Victim information</li>
            <li>Activity logs</li>
        </ul>
        <div class="modal-action">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex gap-2">
                    <button type="button" class="btn btn-outline" onclick="deleteModal.close()">Cancel</button>
                    <button type="submit" class="btn btn-error gap-2">
                        <i class="fas fa-trash" aria-hidden="true"></i>
                        <span>Delete Incident</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>
@endif

@push('scripts')
<script>
// Refresh data
function refreshData() {
    window.location.reload();
}

@if(Auth::user()->role === 'admin')
// Show delete modal
function showDeleteModal(incidentId) {
    const form = document.getElementById('deleteForm');
    form.action = `/incidents/${incidentId}`;
    deleteModal.showModal();
}

// Handle delete form submission with proper error handling
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
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Deleting...';

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

                    // Optimistic UI update FIRST (while modal still open)
                    if (data.incident_id) {
                        const row = document.querySelector(`tr[data-incident-id="${data.incident_id}"]`);
                        if (row) {
                            row.style.opacity = '0.3';
                            row.style.pointerEvents = 'none';
                            row.style.transition = 'opacity 0.3s';
                        }
                    }

                    // Close modal with delay to ensure smooth transition
                    setTimeout(() => {
                        deleteModal.close();
                    }, 100);

                    // Show toast AFTER modal starts closing
                    setTimeout(() => {
                        showSuccessToast(data.message || 'Incident deleted successfully!');
                    }, 200);

                    // Redirect after toast is visible (increased to 3 seconds for emergency responders)
                    setTimeout(() => {
                        window.location.href = '{{ route('incidents.index') }}';
                    }, 3000);
                } else {
                    submitBtn.disabled = false;
                    cancelBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    isDeleting = false;
                    deleteModal.close();

                    // Show error toast after modal closes
                    setTimeout(() => {
                        showErrorToast(data.message || 'Failed to delete incident.');
                    }, 200);
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                cancelBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                isDeleting = false;
                deleteModal.close();

                // Show error toast after modal closes
                setTimeout(() => {
                    showErrorToast(error.message || 'An error occurred while deleting the incident.');
                }, 200);
                console.error('Delete error:', error);
            });
        });
    }
});
@endif

// Auto-refresh every 30 seconds for active incidents
setTimeout(function() {
    const hasActiveIncidents = {{ $incidents->whereIn('status', ['pending', 'active'])->count() > 0 ? 'true' : 'false' }};
    if (hasActiveIncidents) {
        window.location.reload();
    }
}, 30000);
</script>
@endpush
