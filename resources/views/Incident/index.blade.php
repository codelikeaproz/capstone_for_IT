@extends("Layouts.app")

@section('title', 'Incident Management')

@section('content')
<div class="mx-auto px-4 py-0">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                Incident Management
            </h1>
            <p class="text-gray-600 mt-1">Monitor and manage emergency incidents</p>
        </div>

        <div class="flex space-x-3">
            <a href="{{ route('incidents.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Report New Incident
            </a>
            <button class="btn btn-outline" onclick="refreshData()">
                <i class="fas fa-sync-alt mr-2"></i>
                Refresh
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="card bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm">Critical Incidents</p>
                        <p class="text-3xl font-bold">{{ $incidents->where('severity_level', 'critical')->count() }}</p>
                    </div>
                    <i class="fas fa-exclamation-circle text-4xl text-red-200"></i>
                </div>
            </div>
        </div>

        <div class="card bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm">High Priority</p>
                        <p class="text-3xl font-bold">{{ $incidents->where('severity_level', 'high')->count() }}</p>
                    </div>
                    <i class="fas fa-fire text-4xl text-orange-200"></i>
                </div>
            </div>
        </div>

        <div class="card bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm">Active Incidents</p>
                        <p class="text-3xl font-bold">{{ $incidents->whereIn('status', ['pending', 'active'])->count() }}</p>
                    </div>
                    <i class="fas fa-play-circle text-4xl text-blue-200"></i>
                </div>
            </div>
        </div>

        <div class="card bg-gradient-to-r from-green-500 to-green-600 text-white shadow-lg">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm">Resolved Today</p>
                        <p class="text-3xl font-bold">{{ $incidents->where('status', 'resolved')->where('resolved_at', '>=', now()->startOfDay())->count() }}</p>
                    </div>
                    <i class="fas fa-check-circle text-4xl text-green-200"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card bg-base-100 shadow-sm mb-6">
        <div class="card-body">
            <h2 class="card-title mb-4">
                <i class="fas fa-filter text-blue-500"></i>
                Filters
            </h2>

            <form method="GET" action="{{ route('incidents.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="form-control">
                    <select name="municipality" class="select select-bordered select-sm">
                        <option value="">All Municipalities</option>
                        <option value="Valencia City" {{ request('municipality') == 'Valencia City' ? 'selected' : '' }}>Valencia City</option>
                        <option value="Malaybalay City" {{ request('municipality') == 'Malaybalay City' ? 'selected' : '' }}>Malaybalay City</option>
                        <option value="Don Carlos" {{ request('municipality') == 'Don Carlos' ? 'selected' : '' }}>Don Carlos</option>
                        <option value="Quezon" {{ request('municipality') == 'Quezon' ? 'selected' : '' }}>Quezon</option>
                        <option value="Manolo Fortich" {{ request('municipality') == 'Manolo Fortich' ? 'selected' : '' }}>Manolo Fortich</option>
                    </select>
                </div>

                <div class="form-control">
                    <select name="severity" class="select select-bordered select-sm">
                        <option value="">All Severities</option>
                        <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>🔴 Critical</option>
                        <option value="high" {{ request('severity') == 'high' ? 'selected' : '' }}>🟠 High</option>
                        <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>🟡 Medium</option>
                        <option value="low" {{ request('severity') == 'low' ? 'selected' : '' }}>🟢 Low</option>
                    </select>
                </div>

                <div class="form-control">
                    <select name="status" class="select select-bordered select-sm">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                <div class="form-control">
                    <select name="incident_type" class="select select-bordered select-sm">
                        <option value="">All Types</option>
                        <option value="traffic_accident" {{ request('incident_type') == 'traffic_accident' ? 'selected' : '' }}>Traffic Accident</option>
                        <option value="medical_emergency" {{ request('incident_type') == 'medical_emergency' ? 'selected' : '' }}>Medical Emergency</option>
                        <option value="fire_incident" {{ request('incident_type') == 'fire_incident' ? 'selected' : '' }}>Fire Incident</option>
                        <option value="natural_disaster" {{ request('incident_type') == 'natural_disaster' ? 'selected' : '' }}>Natural Disaster</option>
                        <option value="criminal_activity" {{ request('incident_type') == 'criminal_activity' ? 'selected' : '' }}>Criminal Activity</option>
                        <option value="other" {{ request('incident_type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="flex space-x-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-1">
                        <i class="fas fa-search mr-1"></i> Filter
                    </button>
                    <a href="{{ route('incidents.index') }}" class="btn btn-outline btn-sm">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Incidents Table -->
    <div class="card bg-base-100 shadow-lg">
        <div class="card-body">
            <div class="overflow-x-auto">
                @if($incidents->count() > 0)
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Incident #</th>
                                <th>Type</th>
                                <th>Severity</th>
                                <th>Status</th>
                                <th>Location</th>
                                <th>Municipality</th>
                                <th>Date</th>
                                <th>Assigned Staff</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($incidents as $incident)
                                <tr class="hover">
                                    <td class="font-mono font-bold text-blue-600">
                                        <a href="{{ route('incidents.show', $incident) }}" class="hover:underline">
                                            {{ $incident->incident_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="flex items-center space-x-2">
                                            @switch($incident->incident_type)
                                                @case('traffic_accident')
                                                    <i class="fas fa-car text-orange-500"></i>
                                                    @break
                                                @case('medical_emergency')
                                                    <i class="fas fa-heartbeat text-red-500"></i>
                                                    @break
                                                @case('fire_incident')
                                                    <i class="fas fa-fire text-red-600"></i>
                                                    @break
                                                @case('natural_disaster')
                                                    <i class="fas fa-cloud-bolt text-gray-600"></i>
                                                    @break
                                                @case('criminal_activity')
                                                    <i class="fas fa-shield-alt text-purple-600"></i>
                                                    @break
                                                @default
                                                    <i class="fas fa-exclamation-circle text-gray-500"></i>
                                            @endswitch
                                            <span class="text-sm">{{ ucwords(str_replace('_', ' ', $incident->incident_type)) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="badge {{ $incident->severity_level === 'critical' ? 'badge-error' : ($incident->severity_level === 'high' ? 'badge-warning' : ($incident->severity_level === 'medium' ? 'badge-info' : 'badge-success')) }}">
                                            {{ ucfirst($incident->severity_level) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="badge {{ $incident->status_badge }}">
                                            {{ ucfirst($incident->status) }}
                                        </div>
                                    </td>
                                    <td class="text-sm max-w-xs truncate" title="{{ $incident->location }}">{{ $incident->location }}</td>
                                    <td class="text-sm">{{ $incident->municipality }}</td>
                                    <td class="text-sm">{{ $incident->formatted_incident_date }}</td>
                                    <td class="text-sm">
                                        @if($incident->assignedStaff)
                                            <div class="flex items-center space-x-1">
                                                <i class="fas fa-user text-blue-500"></i>
                                                <span>{{ $incident->assignedStaff->first_name }} {{ $incident->assignedStaff->last_name }}</span>
                                            </div>
                                        @else
                                            <span class="text-gray-400">Unassigned</span>
                                        @endif
                                    </td>
                                    <td onclick="event.stopPropagation()">
                                        <div class="dropdown dropdown-end">
                                            <label tabindex="0" class="btn btn-ghost btn-sm btn-circle">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </label>
                                            <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                                <li><a href="{{ route('incidents.show', $incident) }}"><i class="fas fa-eye mr-2"></i>View Details</a></li>
                                                @if(Auth::user()->role === 'admin' || Auth::user()->municipality === $incident->municipality)
                                                    <li><a href="{{ route('incidents.edit', $incident) }}"><i class="fas fa-edit mr-2"></i>Edit</a></li>
                                                @endif
                                                @if(Auth::user()->role === 'admin')
                                                    <li>
                                                        <button type="button" onclick="event.stopPropagation(); showDeleteModal({{ $incident->id }})" class="text-red-600 w-full text-left px-4 py-2 hover:bg-gray-100">
                                                            <i class="fas fa-trash mr-2"></i>Delete
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
                    <!-- Pagination -->
                    @if($incidents->hasPages())
                        <div class="flex justify-center mt-6">
                            {{ $incidents->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">No incidents found</h3>
                        <p class="text-gray-500 mb-4">No incidents match your current filters.</p>
                        <a href="{{ route('incidents.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-2"></i>Report First Incident
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@if(Auth::user()->role === 'admin')
<dialog id="deleteModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg text-red-600">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            Confirm Deletion
        </h3>
        <p class="py-4">Are you sure you want to delete this incident? This action cannot be undone and will also remove all associated data.</p>
        <div class="modal-action">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="button" class="btn" onclick="deleteModal.close()">Cancel</button>
                <button type="submit" class="btn btn-error">Delete Incident</button>
            </form>
        </div>
    </div>
</dialog>
@endif

<script>
function refreshData() {
    window.location.reload();
}

@if(Auth::user()->role === 'admin')
function showDeleteModal(incidentId) {
    const form = document.getElementById('deleteForm');
    form.action = `/incidents/${incidentId}`;
    deleteModal.showModal();
}
@endif

// Auto-refresh every 30 seconds for active incidents
setTimeout(function() {
    const hasActiveIncidents = {{ $incidents->whereIn('status', ['pending', 'active'])->count() > 0 ? 'true' : 'false' }};
    if (hasActiveIncidents) {
        window.location.reload();
    }
}, 30000);

// Handle delete form submission with toast notification
document.addEventListener('DOMContentLoaded', function() {
    const deleteForm = document.getElementById('deleteForm');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();

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
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessToast(data.message || 'Incident deleted successfully!');
                    setTimeout(() => {
                        window.location.href = '{{ route('incidents.index') }}';
                    }, 1500);
                } else {
                    showErrorToast(data.message || 'Failed to delete incident.');
                }
            })
            .catch(error => {
                showErrorToast('An error occurred while deleting the incident.');
                console.error('Error:', error);
            });
        });
    }
});
</script>
@endsection
