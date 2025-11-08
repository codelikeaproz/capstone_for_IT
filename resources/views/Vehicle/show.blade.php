@extends("Layouts.app")

@section('title', 'Vehicle Details - ' . $vehicle->vehicle_number)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="{{ $vehicle->vehicle_type_icon }} text-blue-500 mr-3 text-4xl"></i>
                {{ $vehicle->vehicle_number }}
            </h1>
            <p class="text-gray-600 mt-1">{{ ucwords(str_replace('_', ' ', $vehicle->vehicle_type)) }} - {{ $vehicle->license_plate }}</p>
        </div>
        
        <div class="flex space-x-3">
            @if(Auth::user()->role === 'admin' || Auth::user()->municipality === $vehicle->municipality)
                <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-primary">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Vehicle
                </a>
            @endif
            <a href="{{ route('vehicles.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Fleet
            </a>
        </div>
    </div>

    <!-- Status Alert -->
    @if($vehicle->status === 'maintenance' || $vehicle->current_fuel_level < 25)
        <div class="alert {{ $vehicle->status === 'maintenance' ? 'alert-warning' : 'alert-error' }} mb-6">
            <i class="fas {{ $vehicle->status === 'maintenance' ? 'fa-wrench' : 'fa-gas-pump' }}"></i>
            <span class="font-semibold">
                @if($vehicle->status === 'maintenance')
                    Vehicle Under Maintenance
                @else
                    Low Fuel Alert
                @endif
            </span>
            <span>
                @if($vehicle->status === 'maintenance')
                    This vehicle is currently undergoing maintenance and unavailable for assignment.
                @else
                    Vehicle fuel level is at {{ $vehicle->fuel_level_percentage }}%. Refuel recommended.
                @endif
            </span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        Vehicle Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="label font-semibold">Vehicle Number</label>
                            <div class="font-mono text-lg text-blue-600">{{ $vehicle->vehicle_number }}</div>
                        </div>
                        
                        <div>
                            <label class="label font-semibold">License Plate</label>
                            <div class="font-mono text-lg">{{ $vehicle->license_plate }}</div>
                        </div>
                        
                        <div>
                            <label class="label font-semibold">Type</label>
                            <div class="badge badge-primary badge-lg">{{ ucwords(str_replace('_', ' ', $vehicle->vehicle_type)) }}</div>
                        </div>
                        
                        <div>
                            <label class="label font-semibold">Status</label>
                            <div class="badge {{ $vehicle->status_badge }} badge-lg">
                                {{ ucfirst($vehicle->status) }}
                            </div>
                        </div>
                        
                        <div>
                            <label class="label font-semibold">Make & Model</label>
                            <div>{{ $vehicle->make }} {{ $vehicle->model }}</div>
                        </div>
                        
                        <div>
                            <label class="label font-semibold">Year & Color</label>
                            <div>{{ $vehicle->year }} - {{ ucfirst($vehicle->color) }}</div>
                        </div>
                        
                        <div>
                            <label class="label font-semibold">Municipality</label>
                            <div>{{ $vehicle->municipality }}</div>
                        </div>
                        
                        <div>
                            <label class="label font-semibold">Odometer Reading</label>
                            <div>{{ number_format($vehicle->odometer_reading) }} km</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fuel & Performance -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">
                        <i class="fas fa-gas-pump text-green-500"></i>
                        Fuel & Performance
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="font-semibold">Current Fuel Level</span>
                                <span>{{ $vehicle->fuel_level_percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-{{ $vehicle->fuel_level_percentage > 25 ? 'green' : 'red' }}-500 h-4 rounded-full transition-all duration-300" 
                                     style="width: {{ $vehicle->fuel_level_percentage }}%"></div>
                            </div>
                            <div class="text-sm text-gray-600 mt-1">
                                Capacity: {{ $vehicle->fuel_capacity }} liters
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="font-semibold">Fuel Consumption Rate:</span>
                                <span>{{ $vehicle->fuel_consumption_rate ?? 'N/A' }} L/100km</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-semibold">Total Distance:</span>
                                <span>{{ number_format($vehicle->total_distance) }} km</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-semibold">GPS Enabled:</span>
                                <span class="badge {{ $vehicle->gps_enabled ? 'badge-success' : 'badge-error' }} badge-sm">
                                    {{ $vehicle->gps_enabled ? 'Yes' : 'No' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Maintenance Information -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">
                        <i class="fas fa-wrench text-purple-500"></i>
                        Maintenance Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($vehicle->last_maintenance_date)
                            <div>
                                <label class="label font-semibold">Last Maintenance</label>
                                <div>{{ $vehicle->last_maintenance_date->format('M d, Y') }}</div>
                            </div>
                        @endif
                        
                        @if($vehicle->next_maintenance_due)
                            <div>
                                <label class="label font-semibold">Next Maintenance Due</label>
                                <div class="flex items-center space-x-2">
                                    <span>{{ $vehicle->next_maintenance_due->format('M d, Y') }}</span>
                                    @if($vehicle->maintenance_status === 'overdue')
                                        <span class="badge badge-error badge-sm">Overdue</span>
                                    @elseif($vehicle->maintenance_status === 'due_soon')
                                        <span class="badge badge-warning badge-sm">Due Soon</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        @if($vehicle->insurance_policy)
                            <div>
                                <label class="label font-semibold">Insurance Policy</label>
                                <div>{{ $vehicle->insurance_policy }}</div>
                            </div>
                        @endif
                        
                        @if($vehicle->insurance_expiry)
                            <div>
                                <label class="label font-semibold">Insurance Expiry</label>
                                <div>{{ $vehicle->insurance_expiry->format('M d, Y') }}</div>
                            </div>
                        @endif
                        
                        @if($vehicle->registration_expiry)
                            <div>
                                <label class="label font-semibold">Registration Expiry</label>
                                <div>{{ $vehicle->registration_expiry->format('M d, Y') }}</div>
                            </div>
                        @endif
                    </div>
                    
                    @if($vehicle->maintenance_notes)
                        <div class="mt-4">
                            <label class="label font-semibold">Maintenance Notes</label>
                            <div class="bg-gray-100 p-3 rounded">
                                {{ $vehicle->maintenance_notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Equipment List -->
            @if(!empty($vehicle->equipment_list))
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">
                            <i class="fas fa-toolbox text-orange-500"></i>
                            Equipment List
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach($vehicle->equipment_list as $equipment)
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-check text-green-500"></i>
                                    <span>{{ $equipment }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Current Assignment -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h3 class="card-title text-lg mb-4">
                        <i class="fas fa-user text-blue-500"></i>
                        Current Assignment
                    </h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="label font-semibold">Assigned Driver</label>
                            @if($vehicle->assignedDriver)
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-user text-green-500"></i>
                                    <span>{{ $vehicle->assignedDriver->first_name }} {{ $vehicle->assignedDriver->last_name }}</span>
                                </div>
                            @else
                                <span class="text-gray-500">No driver assigned</span>
                            @endif
                        </div>
                        
                        <div>
                            <label class="label font-semibold">Current Incident</label>
                            @if($vehicle->currentIncident)
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-exclamation-triangle text-orange-500"></i>
                                    <a href="{{ route('incidents.show', $vehicle->currentIncident) }}" class="text-blue-600 hover:underline">
                                        {{ $vehicle->currentIncident->incident_number }}
                                    </a>
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ $vehicle->currentIncident->location }}
                                </div>
                            @else
                                <span class="text-gray-500">Not assigned to incident</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- GPS Location -->
            @if($vehicle->gps_enabled && $vehicle->current_latitude && $vehicle->current_longitude)
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h3 class="card-title text-lg mb-4">
                            <i class="fas fa-map-marker-alt text-green-500"></i>
                            Current Location
                        </h3>
                        
                        <div class="space-y-2">
                            <div class="text-sm">
                                <strong>Coordinates:</strong><br>
                                Lat: {{ $vehicle->current_latitude }}<br>
                                Lng: {{ $vehicle->current_longitude }}
                            </div>
                            <a href="https://maps.google.com?q={{ $vehicle->current_latitude }},{{ $vehicle->current_longitude }}" 
                               target="_blank" class="btn btn-outline btn-sm btn-block">
                                <i class="fas fa-external-link-alt mr-2"></i>View on Google Maps
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            @if(Auth::user()->role === 'admin' || Auth::user()->municipality === $vehicle->municipality)
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h3 class="card-title text-lg mb-4">
                            <i class="fas fa-cog text-gray-500"></i>
                            Quick Actions
                        </h3>
                        
                        <div class="space-y-3">
                            @if($vehicle->status === 'available')
                                <form action="{{ route('vehicles.assign', $vehicle) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-plus-circle mr-2"></i>
                                        Assign to Incident
                                    </button>
                                </form>
                            @elseif($vehicle->current_incident_id)
                                <form action="{{ route('vehicles.release', $vehicle) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-block">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Release from Incident
                                    </button>
                                </form>
                            @endif
                            
                            <form action="{{ route('vehicles.maintenance', $vehicle) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-outline btn-block">
                                    <i class="fas fa-wrench mr-2"></i>
                                    Schedule Maintenance
                                </button>
                            </form>
                            
                            @if(Auth::user()->role === 'admin')
                                <button type="button" onclick="confirmDelete()" class="btn btn-error btn-outline btn-block">
                                    <i class="fas fa-trash mr-2"></i>
                                    Remove Vehicle
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Incident History -->
    @if($recentIncidents && $recentIncidents->count() > 0)
        <div class="mt-8">
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">
                        <i class="fas fa-history text-blue-500"></i>
                        Recent Incident History
                    </h2>
                    
                    <div class="overflow-x-auto">
                        <table class="table table-zebra">
                            <thead>
                                <tr>
                                    <th>Incident #</th>
                                    <th>Type</th>
                                    <th>Location</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentIncidents as $incident)
                                    <tr>
                                        <td class="font-mono">{{ $incident->incident_number }}</td>
                                        <td>{{ ucwords(str_replace('_', ' ', $incident->incident_type)) }}</td>
                                        <td class="max-w-xs truncate">{{ $incident->location }}</td>
                                        <td>{{ $incident->incident_date->format('M d, Y') }}</td>
                                        <td>
                                            <div class="badge {{ $incident->status_badge }} badge-sm">
                                                {{ ucfirst($incident->status) }}
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('incidents.show', $incident) }}" class="btn btn-ghost btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@if(Auth::user()->role === 'admin')
    <!-- Delete Confirmation Modal -->
    <dialog id="deleteModal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg text-red-600">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Confirm Vehicle Removal
            </h3>
            <p class="py-4">Are you sure you want to remove this vehicle from the fleet? This action cannot be undone.</p>
            <div class="modal-action">
                <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn" onclick="deleteModal.close()">Cancel</button>
                    <button type="submit" class="btn btn-error">Remove Vehicle</button>
                </form>
            </div>
        </div>
    </dialog>

    <script>
        function confirmDelete() {
            deleteModal.showModal();
        }
    </script>
@endif
@endsection