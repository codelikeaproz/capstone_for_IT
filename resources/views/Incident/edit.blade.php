@extends("Layouts.app")

@section('title', 'Edit Incident - ' . $incident->incident_number)

@section('content')
<div class="container mx-auto px-4 py-0">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-edit text-blue-500 mr-2"></i>
                Edit Incident {{ $incident->incident_number }}
            </h1>
            <a href="{{ route('incidents.show', $incident) }}" class="btn btn-outline btn-sm">
                <i class="fas fa-arrow-left mr-2"></i>Back to Incident
            </a>
        </div>

        <form action="{{ route('incidents.update', $incident) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        Basic Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Incident Type -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Incident Type <span class="text-red-500">*</span></span>
                            </label>
                            <select name="incident_type" class="select select-bordered @error('incident_type') select-error @enderror" required>
                                <option value="">Select incident type</option>
                                <option value="traffic_accident" {{ $incident->incident_type == 'traffic_accident' ? 'selected' : '' }}>Traffic Accident</option>
                                <option value="medical_emergency" {{ $incident->incident_type == 'medical_emergency' ? 'selected' : '' }}>Medical Emergency</option>
                                <option value="fire_incident" {{ $incident->incident_type == 'fire_incident' ? 'selected' : '' }}>Fire Incident</option>
                                <option value="natural_disaster" {{ $incident->incident_type == 'natural_disaster' ? 'selected' : '' }}>Natural Disaster</option>
                                <option value="criminal_activity" {{ $incident->incident_type == 'criminal_activity' ? 'selected' : '' }}>Criminal Activity</option>
                                <option value="other" {{ $incident->incident_type == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('incident_type')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Severity Level -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Severity Level <span class="text-red-500">*</span></span>
                            </label>
                            <select name="severity_level" class="select select-bordered @error('severity_level') select-error @enderror" required>
                                <option value="">Select severity</option>
                                <option value="low" {{ $incident->severity_level == 'low' ? 'selected' : '' }}>
                                    🟢 Low
                                </option>
                                <option value="medium" {{ $incident->severity_level == 'medium' ? 'selected' : '' }}>
                                    🟡 Medium
                                </option>
                                <option value="high" {{ $incident->severity_level == 'high' ? 'selected' : '' }}>
                                    🟠 High
                                </option>
                                <option value="critical" {{ $incident->severity_level == 'critical' ? 'selected' : '' }}>
                                    🔴 Critical
                                </option>
                            </select>
                            @error('severity_level')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Status <span class="text-red-500">*</span></span>
                            </label>
                            <select name="status" class="select select-bordered @error('status') select-error @enderror" required>
                                <option value="pending" {{ $incident->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="active" {{ $incident->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="resolved" {{ $incident->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $incident->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                            @error('status')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Incident Date -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Incident Date & Time <span class="text-red-500">*</span></span>
                            </label>
                            <input type="datetime-local" name="incident_date" 
                                   class="input input-bordered @error('incident_date') input-error @enderror" 
                                   value="{{ old('incident_date', $incident->incident_date->format('Y-m-d\TH:i')) }}" required>
                            @error('incident_date')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Information -->
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">
                        <i class="fas fa-map-marker-alt text-green-500"></i>
                        Location Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Location -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Location <span class="text-red-500">*</span></span>
                            </label>
                            <textarea name="location" 
                                      class="textarea textarea-bordered @error('location') textarea-error @enderror" 
                                      placeholder="Detailed location description..." required>{{ old('location', $incident->location) }}</textarea>
                            @error('location')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Municipality -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Municipality <span class="text-red-500">*</span></span>
                            </label>
                            <select name="municipality" class="select select-bordered @error('municipality') select-error @enderror" required>
                                <option value="">Select municipality</option>
                                <option value="Valencia City" {{ $incident->municipality == 'Valencia City' ? 'selected' : '' }}>Valencia City</option>
                                <option value="Malaybalay City" {{ $incident->municipality == 'Malaybalay City' ? 'selected' : '' }}>Malaybalay City</option>
                                <option value="Don Carlos" {{ $incident->municipality == 'Don Carlos' ? 'selected' : '' }}>Don Carlos</option>
                                <option value="Quezon" {{ $incident->municipality == 'Quezon' ? 'selected' : '' }}>Quezon</option>
                                <option value="Manolo Fortich" {{ $incident->municipality == 'Manolo Fortich' ? 'selected' : '' }}>Manolo Fortich</option>
                                <!-- Add more municipalities as needed -->
                            </select>
                            @error('municipality')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- GPS Coordinates -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Latitude</span>
                            </label>
                            <input type="number" step="any" name="latitude" 
                                   class="input input-bordered @error('latitude') input-error @enderror" 
                                   placeholder="e.g. 8.1234567" value="{{ old('latitude', $incident->latitude) }}">
                            @error('latitude')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Longitude</span>
                            </label>
                            <input type="number" step="any" name="longitude" 
                                   class="input input-bordered @error('longitude') input-error @enderror" 
                                   placeholder="e.g. 125.1234567" value="{{ old('longitude', $incident->longitude) }}">
                            @error('longitude')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Incident Description -->
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">
                        <i class="fas fa-file-alt text-purple-500"></i>
                        Incident Description
                    </h2>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Detailed Description <span class="text-red-500">*</span></span>
                        </label>
                        <textarea name="description" 
                                  class="textarea textarea-bordered h-32 @error('description') textarea-error @enderror" 
                                  placeholder="Provide a detailed description of the incident..." required>{{ old('description', $incident->description) }}</textarea>
                        @error('description')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Photos Gallery -->
            @if($incident->photos && count($incident->photos) > 0)
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title text-lg mb-4">
                            <i class="fas fa-images text-green-500"></i>
                            Current Incident Photos
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                            @foreach($incident->photos as $photo)
                                <div class="aspect-square overflow-hidden rounded-lg border">
                                    <img src="{{ asset('storage/' . $photo) }}" 
                                         alt="Incident photo" 
                                         class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-info-circle"></i> The photos above are already attached to this incident.
                        </div>
                    </div>
                </div>
            @endif

            <!-- Add More Photos -->
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">
                        <i class="fas fa-plus-circle text-green-500"></i>
                        Add More Photos
                    </h2>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Upload Additional Photos</span>
                            <span class="label-text-alt">You can upload up to 5 photos (max 2MB each)</span>
                        </label>
                        <input type="file" name="photos[]" 
                               class="file-input file-input-bordered @error('photos') file-input-error @enderror" 
                               accept="image/*" multiple>
                        <div class="text-sm text-gray-500 mt-1">
                            Supported formats: JPG, PNG, GIF. Maximum file size: 2MB per photo.
                        </div>
                        @error('photos')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                        @error('photos.*')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Environmental Conditions -->
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">
                        <i class="fas fa-cloud-rain text-blue-500"></i>
                        Environmental Conditions
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Weather Condition</span>
                            </label>
                            <select name="weather_condition" class="select select-bordered">
                                <option value="">Select weather</option>
                                <option value="clear" {{ $incident->weather_condition == 'clear' ? 'selected' : '' }}>☀️ Clear</option>
                                <option value="cloudy" {{ $incident->weather_condition == 'cloudy' ? 'selected' : '' }}>☁️ Cloudy</option>
                                <option value="rainy" {{ $incident->weather_condition == 'rainy' ? 'selected' : '' }}>🌧️ Rainy</option>
                                <option value="stormy" {{ $incident->weather_condition == 'stormy' ? 'selected' : '' }}>⛈️ Stormy</option>
                                <option value="foggy" {{ $incident->weather_condition == 'foggy' ? 'selected' : '' }}>🌫️ Foggy</option>
                            </select>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Road Condition</span>
                            </label>
                            <select name="road_condition" class="select select-bordered">
                                <option value="">Select road condition</option>
                                <option value="dry" {{ $incident->road_condition == 'dry' ? 'selected' : '' }}>Dry</option>
                                <option value="wet" {{ $incident->road_condition == 'wet' ? 'selected' : '' }}>Wet</option>
                                <option value="slippery" {{ $incident->road_condition == 'slippery' ? 'selected' : '' }}>Slippery</option>
                                <option value="damaged" {{ $incident->road_condition == 'damaged' ? 'selected' : '' }}>Damaged</option>
                                <option value="under_construction" {{ $incident->road_condition == 'under_construction' ? 'selected' : '' }}>Under Construction</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Casualty Information -->
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">
                        <i class="fas fa-user-injured text-red-500"></i>
                        Casualty Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Total Casualties</span>
                            </label>
                            <input type="number" min="0" name="casualty_count" 
                                   class="input input-bordered @error('casualty_count') input-error @enderror" 
                                   value="{{ old('casualty_count', $incident->casualty_count) }}">
                            @error('casualty_count')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Injuries</span>
                            </label>
                            <input type="number" min="0" name="injury_count" 
                                   class="input input-bordered @error('injury_count') input-error @enderror" 
                                   value="{{ old('injury_count', $incident->injury_count) }}">
                            @error('injury_count')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Fatalities</span>
                            </label>
                            <input type="number" min="0" name="fatality_count" 
                                   class="input input-bordered @error('fatality_count') input-error @enderror" 
                                   value="{{ old('fatality_count', $incident->fatality_count) }}">
                            @error('fatality_count')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehicle Information -->
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">
                        <i class="fas fa-car text-blue-500"></i>
                        Vehicle Information
                    </h2>
                    
                    <div class="form-control mb-4">
                        <label class="label cursor-pointer justify-start">
                            <input type="checkbox" name="vehicle_involved" value="1" 
                                   class="checkbox checkbox-primary mr-3" 
                                   {{ old('vehicle_involved', $incident->vehicle_involved) ? 'checked' : '' }}>
                            <span class="label-text font-medium">Vehicle(s) involved in incident</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Vehicle Details</span>
                        </label>
                        <textarea name="vehicle_details" 
                                  class="textarea textarea-bordered @error('vehicle_details') textarea-error @enderror" 
                                  placeholder="Describe involved vehicles (make, model, license plate, etc.)">{{ old('vehicle_details', $incident->vehicle_details) }}</textarea>
                        @error('vehicle_details')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Property Damage -->
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">
                        <i class="fas fa-hammer text-orange-500"></i>
                        Property Damage
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Estimated Damage Cost (₱)</span>
                            </label>
                            <input type="number" step="0.01" min="0" name="property_damage_estimate" 
                                   class="input input-bordered @error('property_damage_estimate') input-error @enderror" 
                                   placeholder="0.00" value="{{ old('property_damage_estimate', $incident->property_damage_estimate) }}">
                            @error('property_damage_estimate')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Damage Description</span>
                            </label>
                            <textarea name="damage_description" 
                                      class="textarea textarea-bordered @error('damage_description') textarea-error @enderror" 
                                      placeholder="Describe property damage...">{{ old('damage_description', $incident->damage_description) }}</textarea>
                            @error('damage_description')
                                <label class="label">
                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Response Assignment -->
            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'staff')
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">
                        <i class="fas fa-users text-indigo-500"></i>
                        Response Assignment
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Assign Staff</span>
                            </label>
                            <select name="assigned_staff_id" class="select select-bordered">
                                <option value="">Select staff member</option>
                                @foreach($staff as $member)
                                    <option value="{{ $member->id }}" {{ $incident->assigned_staff_id == $member->id ? 'selected' : '' }}>
                                        {{ $member->first_name }} {{ $member->last_name }} ({{ $member->municipality }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Assign Vehicle</span>
                            </label>
                            <select name="assigned_vehicle_id" class="select select-bordered">
                                <option value="">Select vehicle</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ $incident->assigned_vehicle_id == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->vehicle_number }} - {{ ucwords(str_replace('_', ' ', $vehicle->vehicle_type)) }} ({{ $vehicle->municipality }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Resolution Notes (Only show when status is resolved or closed) -->
            @if(in_array($incident->status, ['resolved', 'closed']))
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">
                        <i class="fas fa-check-circle text-green-500"></i>
                        Resolution Notes
                    </h2>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Resolution Notes</span>
                        </label>
                        <textarea name="resolution_notes" 
                                  class="textarea textarea-bordered @error('resolution_notes') textarea-error @enderror" 
                                  placeholder="Add notes about how this incident was resolved...">{{ old('resolution_notes', $incident->resolution_notes) }}</textarea>
                        @error('resolution_notes')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>
            </div>
            @endif

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6">
                <a href="{{ route('incidents.show', $incident) }}" class="btn btn-outline">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i>Update Incident
                </button>
            </div>
        </form>
    </div>
</div>
@endsection