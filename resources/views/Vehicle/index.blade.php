@extends("Layouts.app")

@section('title', 'Vehicle Fleet Management')

@section('content')
<div class="mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-truck text-blue-500 mr-3"></i>
                Vehicle Fleet Management
            </h1>
            <p class="text-gray-600 mt-1">Monitor and manage emergency response vehicles</p>
        </div>
        
        <div class="flex space-x-3">
            <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Add New Vehicle
            </a>
            <button class="btn btn-outline" onclick="refreshData()">
                <i class="fas fa-sync-alt mr-2"></i>
                Refresh
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="card bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg">
            <div class="card-body py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm">Total Fleet</p>
                        <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
                    </div>
                    <i class="fas fa-cars text-2xl text-blue-200"></i>
                </div>
            </div>
        </div>

        <div class="card bg-gradient-to-r from-green-500 to-green-600 text-white shadow-lg">
            <div class="card-body py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm">Available</p>
                        <p class="text-2xl font-bold">{{ $stats['available'] }}</p>
                    </div>
                    <i class="fas fa-check-circle text-2xl text-green-200"></i>
                </div>
            </div>
        </div>

        <div class="card bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg">
            <div class="card-body py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm">In Use</p>
                        <p class="text-2xl font-bold">{{ $stats['in_use'] }}</p>
                    </div>
                    <i class="fas fa-play-circle text-2xl text-orange-200"></i>
                </div>
            </div>
        </div>

        <div class="card bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-lg">
            <div class="card-body py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm">Maintenance</p>
                        <p class="text-2xl font-bold">{{ $stats['maintenance'] }}</p>
                    </div>
                    <i class="fas fa-wrench text-2xl text-purple-200"></i>
                </div>
            </div>
        </div>

        <div class="card bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg">
            <div class="card-body py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm">Low Fuel</p>
                        <p class="text-2xl font-bold">{{ $stats['low_fuel'] }}</p>
                    </div>
                    <i class="fas fa-gas-pump text-2xl text-red-200"></i>
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
            
            <form method="GET" action="{{ route('vehicles.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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
                    <select name="vehicle_type" class="select select-bordered select-sm">
                        <option value="">All Types</option>
                        <option value="ambulance" {{ request('vehicle_type') == 'ambulance' ? 'selected' : '' }}>🚑 Ambulance</option>
                        <option value="fire_truck" {{ request('vehicle_type') == 'fire_truck' ? 'selected' : '' }}>🚒 Fire Truck</option>
                        <option value="rescue_vehicle" {{ request('vehicle_type') == 'rescue_vehicle' ? 'selected' : '' }}>🚐 Rescue Vehicle</option>
                        <option value="patrol_car" {{ request('vehicle_type') == 'patrol_car' ? 'selected' : '' }}>🚔 Patrol Car</option>
                        <option value="support_vehicle" {{ request('vehicle_type') == 'support_vehicle' ? 'selected' : '' }}>🚚 Support Vehicle</option>
                    </select>
                </div>
                
                <div class="form-control">
                    <select name="status" class="select select-bordered select-sm">
                        <option value="">All Statuses</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="in_use" {{ request('status') == 'in_use' ? 'selected' : '' }}>In Use</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="out_of_service" {{ request('status') == 'out_of_service' ? 'selected' : '' }}>Out of Service</option>
                    </select>
                </div>
                
                <div class="flex space-x-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-1">
                        <i class="fas fa-search mr-1"></i> Filter
                    </button>
                    <a href="{{ route('vehicles.index') }}" class="btn btn-outline btn-sm">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Vehicle Grid -->
    @if($vehicles->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-6">
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

        <!-- Pagination -->
        @if($vehicles->hasPages())
            <div class="flex justify-center mt-6">
                {{ $vehicles->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <i class="fas fa-truck text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">No vehicles found</h3>
            <p class="text-gray-500 mb-4">No vehicles match your current filters.</p>
            <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>Add First Vehicle
            </a>
        </div>
    @endif
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