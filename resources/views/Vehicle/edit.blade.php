@extends("Layouts.app")

@section('title', 'Edit Vehicle - ' . $vehicle->vehicle_number)
@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-edit text-blue-500 mr-3"></i>
                Edit Vehicle {{ $vehicle->vehicle_number }}
            </h1>
            <p class="text-gray-600 mt-1">Update vehicle information and status</p>
        </div>
        
        <div class="flex space-x-3">
            <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-outline">
                <i class="fas fa-eye mr-2"></i>
                View Details
            </a>
            <a href="{{ route('vehicles.index') }}" class="btn btn-ghost">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Fleet
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <form action="{{ route('vehicles.update', $vehicle) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            Basic Information
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Vehicle Number <span class="text-red-500">*</span></span>
                                </label>
                                <input type="text" name="vehicle_number" value="{{ old('vehicle_number', $vehicle->vehicle_number) }}" 
                                       placeholder="Enter vehicle number"
                                       class="input input-bordered @error('vehicle_number') input-error @enderror" required>
                                @error('vehicle_number')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">License Plate <span class="text-red-500">*</span></span>
                                </label>
                                <input type="text" name="license_plate" value="{{ old('license_plate', $vehicle->license_plate) }}" 
                                       placeholder="Enter license plate"
                                       class="input input-bordered @error('license_plate') input-error @enderror" required>
                                @error('license_plate')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Vehicle Type <span class="text-red-500">*</span></span>
                                </label>
                                <select name="vehicle_type" class="select select-bordered @error('vehicle_type') select-error @enderror" required>
                                    <option value="">Select vehicle type</option>
                                    <option value="ambulance" {{ $vehicle->vehicle_type === 'ambulance' ? 'selected' : '' }}>🚑 Ambulance</option>
                                    <option value="fire_truck" {{ $vehicle->vehicle_type === 'fire_truck' ? 'selected' : '' }}>🚒 Fire Truck</option>
                                    <option value="rescue_vehicle" {{ $vehicle->vehicle_type === 'rescue_vehicle' ? 'selected' : '' }}>🚐 Rescue Vehicle</option>
                                    <option value="patrol_car" {{ $vehicle->vehicle_type === 'patrol_car' ? 'selected' : '' }}>🚔 Patrol Car</option>
                                    <option value="support_vehicle" {{ $vehicle->vehicle_type === 'support_vehicle' ? 'selected' : '' }}>🚚 Support Vehicle</option>
                                </select>
                                @error('vehicle_type')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Status <span class="text-red-500">*</span></span>
                                </label>
                                <select name="status" class="select select-bordered @error('status') select-error @enderror" required>
                                    <option value="available" {{ $vehicle->status === 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="in_use" {{ $vehicle->status === 'in_use' ? 'selected' : '' }}>In Use</option>
                                    <option value="maintenance" {{ $vehicle->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    <option value="out_of_service" {{ $vehicle->status === 'out_of_service' ? 'selected' : '' }}>Out of Service</option>
                                </select>
                                @error('status')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Details -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">
                            <i class="fas fa-car text-green-500"></i>
                            Vehicle Details
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Make <span class="text-red-500">*</span></span>
                                </label>
                                <input type="text" name="make" value="{{ old('make', $vehicle->make) }}" 
                                       placeholder="e.g., Toyota, Ford"
                                       class="input input-bordered @error('make') input-error @enderror" required>
                                @error('make')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Model <span class="text-red-500">*</span></span>
                                </label>
                                <input type="text" name="model" value="{{ old('model', $vehicle->model) }}" 
                                       placeholder="e.g., Hilux, Ranger"
                                       class="input input-bordered @error('model') input-error @enderror" required>
                                @error('model')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Year <span class="text-red-500">*</span></span>
                                </label>
                                <input type="number" name="year" value="{{ old('year', $vehicle->year) }}" 
                                       min="1990" max="{{ date('Y') + 1 }}"
                                       class="input input-bordered @error('year') input-error @enderror" required>
                                @error('year')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Color <span class="text-red-500">*</span></span>
                                </label>
                                <input type="text" name="color" value="{{ old('color', $vehicle->color) }}" 
                                       placeholder="e.g., White, Red"
                                       class="input input-bordered @error('color') input-error @enderror" required>
                                @error('color')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Municipality <span class="text-red-500">*</span></span>
                                </label>
                                <select name="municipality" class="select select-bordered @error('municipality') select-error @enderror" required>
                                    <option value="">Select municipality</option>
                                    <option value="Valencia City" {{ $vehicle->municipality === 'Valencia City' ? 'selected' : '' }}>Valencia City</option>
                                    <option value="Malaybalay City" {{ $vehicle->municipality === 'Malaybalay City' ? 'selected' : '' }}>Malaybalay City</option>
                                    <option value="Don Carlos" {{ $vehicle->municipality === 'Don Carlos' ? 'selected' : '' }}>Don Carlos</option>
                                    <option value="Quezon" {{ $vehicle->municipality === 'Quezon' ? 'selected' : '' }}>Quezon</option>
                                    <option value="Manolo Fortich" {{ $vehicle->municipality === 'Manolo Fortich' ? 'selected' : '' }}>Manolo Fortich</option>
                                </select>
                                @error('municipality')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fuel & Performance -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">
                            <i class="fas fa-gas-pump text-orange-500"></i>
                            Fuel & Performance
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Fuel Capacity (Liters) <span class="text-red-500">*</span></span>
                                </label>
                                <input type="number" name="fuel_capacity" value="{{ old('fuel_capacity', $vehicle->fuel_capacity) }}" 
                                       min="1" step="0.1"
                                       class="input input-bordered @error('fuel_capacity') input-error @enderror" required>
                                @error('fuel_capacity')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Current Fuel Level (%) <span class="text-red-500">*</span></span>
                                </label>
                                <input type="number" name="current_fuel_level" value="{{ old('current_fuel_level', $vehicle->current_fuel_level) }}" 
                                       min="0" max="100" step="0.1"
                                       class="input input-bordered @error('current_fuel_level') input-error @enderror" required>
                                @error('current_fuel_level')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Fuel Consumption Rate (L/100km)</span>
                                </label>
                                <input type="number" name="fuel_consumption_rate" value="{{ old('fuel_consumption_rate', $vehicle->fuel_consumption_rate) }}" 
                                       min="0" step="0.1"
                                       class="input input-bordered @error('fuel_consumption_rate') input-error @enderror">
                                @error('fuel_consumption_rate')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Odometer Reading (km) <span class="text-red-500">*</span></span>
                                </label>
                                <input type="number" name="odometer_reading" value="{{ old('odometer_reading', $vehicle->odometer_reading) }}" 
                                       min="0"
                                       class="input input-bordered @error('odometer_reading') input-error @enderror" required>
                                @error('odometer_reading')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Maintenance & Insurance -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">
                            <i class="fas fa-wrench text-purple-500"></i>
                            Maintenance & Insurance
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Insurance Policy Number</span>
                                </label>
                                <input type="text" name="insurance_policy" value="{{ old('insurance_policy', $vehicle->insurance_policy) }}" 
                                       placeholder="Enter policy number"
                                       class="input input-bordered @error('insurance_policy') input-error @enderror">
                                @error('insurance_policy')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Insurance Expiry Date</span>
                                </label>
                                <input type="date" name="insurance_expiry" 
                                       value="{{ old('insurance_expiry', $vehicle->insurance_expiry ? $vehicle->insurance_expiry->format('Y-m-d') : '') }}"
                                       class="input input-bordered @error('insurance_expiry') input-error @enderror">
                                @error('insurance_expiry')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Registration Expiry Date</span>
                                </label>
                                <input type="date" name="registration_expiry" 
                                       value="{{ old('registration_expiry', $vehicle->registration_expiry ? $vehicle->registration_expiry->format('Y-m-d') : '') }}"
                                       class="input input-bordered @error('registration_expiry') input-error @enderror">
                                @error('registration_expiry')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-control mt-4">
                            <label class="label">
                                <span class="label-text font-semibold">Maintenance Notes</span>
                            </label>
                            <textarea name="maintenance_notes" rows="3" placeholder="Add maintenance notes..."
                                      class="textarea textarea-bordered @error('maintenance_notes') textarea-error @enderror">{{ old('maintenance_notes', $vehicle->maintenance_notes) }}</textarea>
                            @error('maintenance_notes')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Equipment List -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">
                            <i class="fas fa-toolbox text-gray-500"></i>
                            Equipment List
                        </h2>
                        
                        <div id="equipment-list">
                            @if($vehicle->equipment_list && count($vehicle->equipment_list) > 0)
                                @foreach($vehicle->equipment_list as $index => $equipment)
                                    <div class="flex space-x-2 mb-2 equipment-item">
                                        <input type="text" name="equipment_list[]" value="{{ $equipment }}" 
                                               placeholder="Equipment item"
                                               class="input input-bordered flex-1">
                                        <button type="button" onclick="removeEquipment(this)" class="btn btn-outline btn-sm">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="flex space-x-2 mb-2 equipment-item">
                                    <input type="text" name="equipment_list[]" placeholder="Equipment item"
                                           class="input input-bordered flex-1">
                                    <button type="button" onclick="removeEquipment(this)" class="btn btn-outline btn-sm">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                        
                        <button type="button" onclick="addEquipment()" class="btn btn-outline btn-sm">
                            <i class="fas fa-plus mr-2"></i>Add Equipment
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Assignment -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h3 class="card-title text-lg mb-4">
                            <i class="fas fa-user text-blue-500"></i>
                            Driver Assignment
                        </h3>
                        
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Assigned Driver</span>
                            </label>
                            <select name="assigned_driver_id" class="select select-bordered @error('assigned_driver_id') select-error @enderror">
                                <option value="">No driver assigned</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}" {{ $vehicle->assigned_driver_id == $driver->id ? 'selected' : '' }}>
                                        {{ $driver->first_name }} {{ $driver->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_driver_id')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Current Status -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h3 class="card-title text-lg mb-4">
                            <i class="fas fa-info-circle text-green-500"></i>
                            Current Status
                        </h3>
                        
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>Status:</span>
                                <span class="badge {{ $vehicle->status_badge }} badge-sm">{{ ucfirst($vehicle->status) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Fuel Level:</span>
                                <span>{{ $vehicle->fuel_level_percentage }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Total Distance:</span>
                                <span>{{ number_format($vehicle->total_distance) }} km</span>
                            </div>
                            @if($vehicle->currentIncident)
                                <div class="flex justify-between">
                                    <span>Current Incident:</span>
                                    <span class="text-blue-600">{{ $vehicle->currentIncident->incident_number }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h3 class="card-title text-lg mb-4">
                            <i class="fas fa-cog text-gray-500"></i>
                            Actions
                        </h3>
                        
                        <div class="space-y-3">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-save mr-2"></i>
                                Update Vehicle
                            </button>
                            
                            <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-outline btn-block">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </a>
                            
                            @if(Auth::user()->role === 'admin')
                                <button type="button" onclick="confirmDelete()" class="btn btn-error btn-outline btn-block">
                                    <i class="fas fa-trash mr-2"></i>
                                    Remove Vehicle
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
@endif

<script>
function addEquipment() {
    const equipmentList = document.getElementById('equipment-list');
    const newItem = document.createElement('div');
    newItem.className = 'flex space-x-2 mb-2 equipment-item';
    newItem.innerHTML = `
        <input type="text" name="equipment_list[]" placeholder="Equipment item" class="input input-bordered flex-1">
        <button type="button" onclick="removeEquipment(this)" class="btn btn-outline btn-sm">
            <i class="fas fa-times"></i>
        </button>
    `;
    equipmentList.appendChild(newItem);
}

function removeEquipment(button) {
    const equipmentList = document.getElementById('equipment-list');
    if (equipmentList.children.length > 1) {
        button.parentElement.remove();
    }
}

@if(Auth::user()->role === 'admin')
function confirmDelete() {
    deleteModal.showModal();
}
@endif
</script>
@endsection