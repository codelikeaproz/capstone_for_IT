@extends("Layouts.app")

@section('title', 'Add New Vehicle')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-plus text-green-500 mr-3"></i>
                Add New Vehicle
            </h1>
            <p class="text-gray-600 mt-1">Register a new vehicle to the emergency fleet</p>
        </div>
        
        <div class="flex space-x-3">
            <a href="{{ route('vehicles.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Fleet
            </a>
        </div>
    </div>

    <!-- Create Form -->
    <form action="{{ route('vehicles.store') }}" method="POST" class="space-y-6">
        @csrf
        
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
                                <input type="text" name="vehicle_number" value="{{ old('vehicle_number') }}" 
                                       placeholder="Enter vehicle number (e.g., V-001)"
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
                                <input type="text" name="license_plate" value="{{ old('license_plate') }}" 
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
                                    <option value="ambulance" {{ old('vehicle_type') === 'ambulance' ? 'selected' : '' }}>🚑 Ambulance</option>
                                    <option value="fire_truck" {{ old('vehicle_type') === 'fire_truck' ? 'selected' : '' }}>🚒 Fire Truck</option>
                                    <option value="rescue_vehicle" {{ old('vehicle_type') === 'rescue_vehicle' ? 'selected' : '' }}>🚐 Rescue Vehicle</option>
                                    <option value="patrol_car" {{ old('vehicle_type') === 'patrol_car' ? 'selected' : '' }}>🚔 Patrol Car</option>
                                    <option value="support_vehicle" {{ old('vehicle_type') === 'support_vehicle' ? 'selected' : '' }}>🚚 Support Vehicle</option>
                                </select>
                                @error('vehicle_type')
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
                                    <option value="Valencia City" {{ old('municipality') === 'Valencia City' ? 'selected' : '' }}>Valencia City</option>
                                    <option value="Malaybalay City" {{ old('municipality') === 'Malaybalay City' ? 'selected' : '' }}>Malaybalay City</option>
                                    <option value="Don Carlos" {{ old('municipality') === 'Don Carlos' ? 'selected' : '' }}>Don Carlos</option>
                                    <option value="Quezon" {{ old('municipality') === 'Quezon' ? 'selected' : '' }}>Quezon</option>
                                    <option value="Manolo Fortich" {{ old('municipality') === 'Manolo Fortich' ? 'selected' : '' }}>Manolo Fortich</option>
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
                                <input type="text" name="make" value="{{ old('make') }}" 
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
                                <input type="text" name="model" value="{{ old('model') }}" 
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
                                <input type="number" name="year" value="{{ old('year') }}" 
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
                                <input type="text" name="color" value="{{ old('color') }}" 
                                       placeholder="e.g., White, Red"
                                       class="input input-bordered @error('color') input-error @enderror" required>
                                @error('color')
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
                                <input type="number" name="fuel_capacity" value="{{ old('fuel_capacity') }}" 
                                       min="1" step="0.1" placeholder="e.g., 60"
                                       class="input input-bordered @error('fuel_capacity') input-error @enderror" required>
                                @error('fuel_capacity')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Fuel Consumption Rate (L/100km)</span>
                                </label>
                                <input type="number" name="fuel_consumption_rate" value="{{ old('fuel_consumption_rate') }}" 
                                       min="0" step="0.1" placeholder="e.g., 12.5"
                                       class="input input-bordered @error('fuel_consumption_rate') input-error @enderror">
                                @error('fuel_consumption_rate')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Insurance & Registration -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">
                            <i class="fas fa-shield-alt text-purple-500"></i>
                            Insurance & Registration
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Insurance Policy Number</span>
                                </label>
                                <input type="text" name="insurance_policy" value="{{ old('insurance_policy') }}" 
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
                                <input type="date" name="insurance_expiry" value="{{ old('insurance_expiry') }}"
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
                                <input type="date" name="registration_expiry" value="{{ old('registration_expiry') }}"
                                       class="input input-bordered @error('registration_expiry') input-error @enderror">
                                @error('registration_expiry')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
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
                            <div class="flex space-x-2 mb-2 equipment-item">
                                <input type="text" name="equipment_list[]" placeholder="Equipment item (e.g., First Aid Kit)"
                                       class="input input-bordered flex-1">
                                <button type="button" onclick="removeEquipment(this)" class="btn btn-outline btn-sm">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        
                        <button type="button" onclick="addEquipment()" class="btn btn-outline btn-sm">
                            <i class="fas fa-plus mr-2"></i>Add Equipment
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Driver Assignment -->
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
                                    <option value="{{ $driver->id }}" {{ old('assigned_driver_id') == $driver->id ? 'selected' : '' }}>
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

                <!-- Fuel Information -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h3 class="card-title text-lg mb-4">
                            <i class="fas fa-gas-pump text-green-500"></i>
                            Fuel Information
                        </h3>
                        
                        <div class="space-y-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Fuel Capacity (Liters) <span class="text-red-500">*</span></span>
                                </label>
                                <input type="number" name="fuel_capacity" value="{{ old('fuel_capacity') }}" 
                                       min="1" step="0.1" placeholder="e.g., 60"
                                       class="input input-bordered @error('fuel_capacity') input-error @enderror" required>
                                @error('fuel_capacity')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Fuel Consumption Rate (L/100km)</span>
                                </label>
                                <input type="number" name="fuel_consumption_rate" value="{{ old('fuel_consumption_rate') }}" 
                                       min="0" step="0.1" placeholder="e.g., 12.5"
                                       class="input input-bordered @error('fuel_consumption_rate') input-error @enderror">
                                @error('fuel_consumption_rate')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle"></i>
                            <div class="text-sm">
                                <strong>Note:</strong> Vehicle will be added with 100% fuel level and available status.
                            </div>
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
                                <i class="fas fa-plus mr-2"></i>
                                Add Vehicle to Fleet
                            </button>
                            
                            <a href="{{ route('vehicles.index') }}" class="btn btn-outline btn-block">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

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
</script>
@endsection