@extends("Layouts.app")

@section('title', 'Vehicle Fleet Management')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="mx-auto px-2 sm:px-6 lg:px-6 py-6">

        {{-- Page Header --}}
        <header class="mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-truck text-accent" aria-hidden="true"></i>
                        <span>Vehicle Fleet Management</span>
                    </h1>
                    <p class="text-base text-gray-600 mt-1">Monitor and manage emergency response vehicles</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <a href="{{ route('vehicles.create') }}" class="btn btn-primary gap-2 w-full sm:w-auto min-h-[44px]">
                        <i class="fas fa-plus" aria-hidden="true"></i>
                        <span>Add New Vehicle</span>
                    </a>
                    <button type="button" class="btn btn-success gap-2 w-full sm:w-auto min-h-[44px]" onclick="refreshData()" aria-label="Refresh vehicles">
                        <i class="fas fa-redo" aria-hidden="true"></i>
                        <span>Refresh</span>
                    </button>
                </div>
            </div>
        </header>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6" role="region" aria-label="Vehicle fleet statistics">
            {{-- Total Fleet --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-info">
                        <i class="fas fa-cars text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">Total Fleet</div>
                    <div class="stat-value text-info">{{ number_format($stats['total']) }}</div>
                    <div class="stat-desc text-sm text-gray-500">All vehicles</div>
                </div>
            </div>

            {{-- Available --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-success">
                        <i class="fas fa-check-circle text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">Available</div>
                    <div class="stat-value text-success">{{ number_format($stats['available']) }}</div>
                    <div class="stat-desc text-sm text-gray-500">Ready to deploy</div>
                </div>
            </div>

            {{-- In Use --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-warning">
                        <i class="fas fa-play-circle text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">In Use</div>
                    <div class="stat-value text-warning">{{ number_format($stats['in_use']) }}</div>
                    <div class="stat-desc text-sm text-gray-500">Active deployment</div>
                </div>
            </div>

            {{-- Maintenance --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-accent">
                        <i class="fas fa-wrench text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">Maintenance</div>
                    <div class="stat-value text-accent">{{ number_format($stats['maintenance']) }}</div>
                    <div class="stat-desc text-sm text-gray-500">Under service</div>
                </div>
            </div>

            {{-- Low Fuel --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-error">
                        <i class="fas fa-gas-pump text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">Low Fuel</div>
                    <div class="stat-value text-error">{{ number_format($stats['low_fuel']) }}</div>
                    <div class="stat-desc text-sm text-gray-500">Needs refueling</div>
                </div>
            </div>
        </div>

        {{-- Main Vehicle Card --}}
        <div class="card bg-white shadow-lg">
            <div class="card-body p-0">
                <div class="px-4 py-6 border-b border-gray-200">
                    <div class="flex flex-row justify-between gap-6">
                        <div class="flex-shrink-0">
                            <h2 class="text-xl font-semibold text-gray-800">Vehicle Fleet</h2>
                            <p class="text-sm text-gray-500 mt-2">
                                Showing {{ $vehicles->firstItem() ?? 0 }} to {{ $vehicles->lastItem() ?? 0 }} of {{ number_format($vehicles->total()) }} results
                            </p>
                        </div>
                        <form method="GET" action="{{ route('vehicles.index') }}" class="flex-shrink-0 lg:ml-auto">
                            <div class="flex flex-wrap items-end gap-3">
                                {{-- Search Input --}}
                                <div class="form-control">
                                    <label for="search" class="label">
                                        <span class="label-text font-medium text-gray-700 my-1">Search</span>
                                    </label>
                                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                                           placeholder="Vehicle number, plate, model..."
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
                                        @foreach(config('locations.municipalities') as $municipality => $barangays)
                                            <option value="{{ $municipality }}" {{ request('municipality') === $municipality ? 'selected' : '' }}>
                                                {{ $municipality }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                {{-- Status Filter --}}
                                <div class="form-control">
                                    <label for="status" class="label">
                                        <span class="label-text font-medium text-gray-700 my-1">Status</span>
                                    </label>
                                    <select name="status" id="status" class="select select-bordered w-full focus:outline-primary min-h-[44px] focus:border-primary">
                                        <option value="" {{ request('status') === '' ? 'selected' : '' }}>All Statuses</option>
                                        <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                                        <option value="in_use" {{ request('status') === 'in_use' ? 'selected' : '' }}>In Use</option>
                                        <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="out_of_service" {{ request('status') === 'out_of_service' ? 'selected' : '' }}>Out of Service</option>
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
                                        <a href="{{ route('vehicles.index') }}" class="btn btn-outline gap-2 min-h-[44px]" aria-label="Clear all filters">
                                            <i class="fas fa-times" aria-hidden="true"></i>
                                            <span>Clear</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Active Filters Display --}}
                            @if(request('search') || request('municipality') || request('status'))
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
                                @if(request('status'))
                                    <span class="badge badge-info gap-1">
                                        <span>{{ ucfirst(str_replace('_', ' ', request('status'))) }}</span>
                                    </span>
                                @endif
                            </div>
                            @endif
                        </form>
                    </div>
                </div>

                <div class="p-6">
                    {{-- Vehicle Grid --}}
                    @if($vehicles->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach($vehicles as $vehicle)
                                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="card-body p-4">
                        <!-- Vehicle Header -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <i class="{{ $vehicle->vehicle_type_icon }} text-2xl text-blue-600"></i>
                                <div>
                                    <h3 class="font-bold text-lg">{{ $vehicle->vehicle_number }}</h3>
                                    <p class="text-sm text-gray-500">{{ $vehicle->license_plate }}</p>
                                </div>
                            </div>
                            <div class="badge {{ $vehicle->status_badge }} badge-sm">
                                {{ ucfirst($vehicle->status) }}
                            </div>
                        </div>

                        <!-- Vehicle Details -->
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Type:</span>
                                <span>{{ ucwords(str_replace('_', ' ', $vehicle->vehicle_type)) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Municipality:</span>
                                <span>{{ $vehicle->municipality }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Model:</span>
                                <span>{{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->year }})</span>
                            </div>
                        </div>

                        <!-- Fuel Level -->
                        <div class="mt-3">
                            <div class="flex justify-between text-sm mb-1">
                                <span>Fuel Level</span>
                                <span>{{ $vehicle->fuel_level_percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-{{ $vehicle->fuel_level_percentage > 25 ? 'green' : 'red' }}-500 h-2 rounded-full transition-all duration-300"
                                     style="width: {{ $vehicle->fuel_level_percentage }}%"></div>
                            </div>
                        </div>

                        <!-- Driver Assignment -->
                        @if($vehicle->assignedDriver)
                            <div class="flex items-center space-x-2 mt-2 text-sm">
                                <i class="fas fa-user text-blue-500"></i>
                                <span>{{ $vehicle->assignedDriver->full_name }}</span>
                            </div>
                        @endif

                        <!-- Current Incident -->
                        @if($vehicle->currentIncident)
                            <div class="flex items-center space-x-2 mt-2 text-sm">
                                <i class="fas fa-exclamation-triangle text-orange-500"></i>
                                <span class="truncate">{{ $vehicle->currentIncident->incident_number }}</span>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="card-actions justify-end mt-4">
                            <div class="dropdown dropdown-end">
                                <label tabindex="0" class="btn btn-sm btn-ghost">
                                    <i class="fas fa-ellipsis-v"></i>
                                </label>
                                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                    <li><a href="{{ route('vehicles.show', $vehicle) }}"><i class="fas fa-eye mr-2"></i>View Details</a></li>
                                    @if(Auth::user()->role === 'admin' || Auth::user()->municipality === $vehicle->municipality)
                                        <li><a href="{{ route('vehicles.edit', $vehicle) }}"><i class="fas fa-edit mr-2"></i>Edit</a></li>
                                        @if($vehicle->status === 'available')
                                            <li>
                                                <form action="{{ route('vehicles.assign', $vehicle) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="incident_id" value="">
                                                    <button type="button" onclick="showAssignModal({{ $vehicle->id }})" class="text-blue-600 w-full text-left px-4 py-2 hover:bg-gray-100">
                                                        <i class="fas fa-plus-circle mr-2"></i>Assign to Incident
                                                    </button>
                                                </form>
                                            </li>
                                        @elseif($vehicle->current_incident_id)
                                            <li>
                                                <form action="{{ route('vehicles.release', $vehicle) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 w-full text-left px-4 py-2 hover:bg-gray-100">
                                                        <i class="fas fa-check-circle mr-2"></i>Release from Incident
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        <li>
                                            <button type="button" onclick="showMaintenanceModal({{ $vehicle->id }})" class="text-purple-600 w-full text-left px-4 py-2 hover:bg-gray-100">
                                                <i class="fas fa-wrench mr-2"></i>Maintenance
                                            </button>
                                        </li>
                                    @endif
                                    @if(Auth::user()->role === 'admin')
                                        <li>
                                            <button type="button" onclick="showDeleteModal({{ $vehicle->id }})" class="text-red-600 w-full text-left px-4 py-2 hover:bg-gray-100">
                                                <i class="fas fa-trash mr-2"></i>Remove
                                            </button>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        @if($vehicles->hasPages())
                            <div class="border-t border-gray-200 mt-6 pt-4">
                                {{ $vehicles->links() }}
                            </div>
                        @endif
                    @else
                        {{-- Empty State --}}
                        <div class="text-center py-16 px-4">
                            <i class="fas fa-truck text-6xl text-gray-300 mb-4" aria-hidden="true"></i>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Vehicles Found</h3>
                            <p class="text-gray-500 mb-6">
                                @if(request('search') || request('status'))
                                    No vehicles match your current filters. Try adjusting your search criteria.
                                @else
                                    There are no vehicles to display yet.
                                @endif
                            </p>
                            @if(request('search') || request('status'))
                                <a href="{{ route('vehicles.index') }}" class="btn btn-outline gap-2">
                                    <i class="fas fa-times" aria-hidden="true"></i>
                                    <span>Clear Filters</span>
                                </a>
                            @else
                                <a href="{{ route('vehicles.create') }}" class="btn btn-primary gap-2">
                                    <i class="fas fa-plus" aria-hidden="true"></i>
                                    <span>Add First Vehicle</span>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assignment Modal -->
<dialog id="assignModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg text-blue-600">
            <i class="fas fa-plus-circle mr-2"></i>
            Assign Vehicle to Incident
        </h3>
        <form id="assignForm" method="POST">
            @csrf
            <div class="py-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Select Incident</span>
                    </label>
                    <select name="incident_id" class="select select-bordered" required>
                        <option value="">Choose an incident</option>
                        @foreach($incidents as $incident)
                            <option value="{{ $incident->id }}">
                                {{ $incident->incident_number }} - {{ ucwords(str_replace('_', ' ', $incident->incident_type)) }}
                                ({{ $incident->location }}) - {{ ucfirst($incident->severity_level) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if($incidents->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        No unassigned incidents available for assignment.
                    </div>
                @endif
            </div>
            <div class="modal-action">
                <button type="button" class="btn" onclick="assignModal.close()">Cancel</button>
                <button type="submit" class="btn btn-primary" {{ $incidents->isEmpty() ? 'disabled' : '' }}>
                    Assign Vehicle
                </button>
            </div>
        </form>
    </div>
</dialog>

<!-- Maintenance Modal -->
<dialog id="maintenanceModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg text-purple-600">
            <i class="fas fa-wrench mr-2"></i>
            Update Maintenance
        </h3>
        <form id="maintenanceForm" method="POST">
            @csrf
            <div class="py-4 space-y-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Status</span>
                    </label>
                    <select name="status" class="select select-bordered">
                        <option value="available">Set to Available</option>
                        <option value="maintenance">Set to Maintenance</option>
                        <option value="out_of_service">Mark Out of Service</option>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Next Maintenance Due</span>
                    </label>
                    <input type="date" name="next_maintenance_due" class="input input-bordered">
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Maintenance Notes</span>
                    </label>
                    <textarea name="maintenance_notes" class="textarea textarea-bordered" rows="3" placeholder="Add maintenance notes..."></textarea>
                </div>
            </div>
            <div class="modal-action">
                <button type="button" class="btn" onclick="maintenanceModal.close()">Cancel</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</dialog>

<!-- Delete Confirmation Modal -->
@if(Auth::user()->role === 'admin')
<dialog id="deleteModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg text-red-600">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            Confirm Vehicle Removal
        </h3>
        <p class="py-4">Are you sure you want to remove this vehicle from the fleet? This action cannot be undone.</p>
        <div class="modal-action">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="button" class="btn" onclick="deleteModal.close()">Cancel</button>
                <button type="submit" class="btn btn-error">Remove Vehicle</button>
            </form>
        </div>
    </div>
</dialog>
@endif

<script>
function refreshData() {
    window.location.reload();
}

function showAssignModal(vehicleId) {
    const form = document.getElementById('assignForm');
    form.action = `/vehicles/${vehicleId}/assign`;

    // Check if there are available incidents
    const incidentSelect = form.querySelector('select[name="incident_id"]');
    const submitBtn = form.querySelector('button[type="submit"]');

    if (incidentSelect.options.length <= 1) { // Only has default option
        submitBtn.disabled = true;
        submitBtn.textContent = 'No Incidents Available';
    } else {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Assign Vehicle';
    }

    assignModal.showModal();
}

function showMaintenanceModal(vehicleId) {
    const form = document.getElementById('maintenanceForm');
    form.action = `/vehicles/${vehicleId}/maintenance`;

    // Reset form fields
    form.reset();

    maintenanceModal.showModal();
}

@if(Auth::user()->role === 'admin')
function showDeleteModal(vehicleId) {
    const form = document.getElementById('deleteForm');
    form.action = `/vehicles/${vehicleId}`;
    deleteModal.showModal();
}
@endif

// Handle form submissions with loading states
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Processing...';
            }
        });
    });
});
</script>
@endsection
