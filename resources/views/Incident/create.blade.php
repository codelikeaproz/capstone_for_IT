@extends("Layouts.app")

@section('title', 'Report New Incident')

@section('content')
<div class="container mx-auto px-0 py-0">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                Report New Incident
            </h1>
            <a href="{{ route('incidents.index') }}" class="btn btn-outline btn-sm">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>

        <form action="{{ route('incidents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

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
                            <select name="incident_type" class="select select-bordered  @error('incident_type')   select-error @enderror" required>
                                <option value="">Select incident type</option>
                                <option value="traffic_accident" {{ old('incident_type') == 'traffic_accident' ? 'selected' : '' }}>Traffic Accident</option>
                                <option value="medical_emergency" {{ old('incident_type') == 'medical_emergency' ? 'selected' : '' }}>Medical Emergency</option>
                                <option value="fire_incident" {{ old('incident_type') == 'fire_incident' ? 'selected' : '' }}>Fire Incident</option>
                                <option value="natural_disaster" {{ old('incident_type') == 'natural_disaster' ? 'selected' : '' }}>Natural Disaster</option>
                                <option value="criminal_activity" {{ old('incident_type') == 'criminal_activity' ? 'selected' : '' }}>Criminal Activity</option>
                                <option value="other" {{ old('incident_type') == 'other' ? 'selected' : '' }}>Other</option>
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
                                <option value="low" {{ old('severity_level') == 'low' ? 'selected' : '' }}>
                                    🟢 Low
                                </option>
                                <option value="medium" {{ old('severity_level') == 'medium' ? 'selected' : '' }}>
                                    🟡 Medium
                                </option>
                                <option value="high" {{ old('severity_level') == 'high' ? 'selected' : '' }}>
                                    🟠 High
                                </option>
                                <option value="critical" {{ old('severity_level') == 'critical' ? 'selected' : '' }}>
                                    🔴 Critical
                                </option>
                            </select>
                            @error('severity_level')
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
                                   value="{{ old('incident_date', now()->format('Y-m-d\TH:i')) }}" required>
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
                                      placeholder="Detailed location description..." required>{{ old('location') }}</textarea>
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
                                <option value="Valencia City" {{ old('municipality') == 'Valencia City' ? 'selected' : '' }}>Valencia City</option>
                                <option value="Malaybalay City" {{ old('municipality') == 'Malaybalay City' ? 'selected' : '' }}>Malaybalay City</option>
                                <option value="Don Carlos" {{ old('municipality') == 'Don Carlos' ? 'selected' : '' }}>Don Carlos</option>
                                <option value="Quezon" {{ old('municipality') == 'Quezon' ? 'selected' : '' }}>Quezon</option>
                                <option value="Manolo Fortich" {{ old('municipality') == 'Manolo Fortich' ? 'selected' : '' }}>Manolo Fortich</option>
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
                                <button type="button" class="btn btn-xs btn-primary" onclick="getLocation()">
                                    <i class="fas fa-location-arrow"></i> Get Location
                                </button>
                            </label>
                            <input type="number" step="any" name="latitude"
                                   class="input input-bordered @error('latitude') input-error @enderror"
                                   placeholder="e.g. 8.1234567" value="{{ old('latitude') }}">
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
                                   placeholder="e.g. 125.1234567" value="{{ old('longitude') }}">
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
                                  placeholder="Provide a detailed description of the incident..." required>{{ old('description') }}</textarea>
                        @error('description')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Photos Upload -->
            <div class="card bg-base-100 shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">
                        <i class="fas fa-camera text-green-500"></i>
                        Incident Photos
                    </h2>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Upload Photos <span class="text-red-500">*</span></span>
                            <span class="label-text-alt">You can upload up to 5 photos (max 2MB each)</span>
                        </label>
                        <input
                            type="file"
                            name="photos[]"
                            id="photo-upload"
                            class="file-input file-input-bordered @error('photos') file-input-error @enderror"
                            accept="image/*"
                            multiple
                            required
                            onchange="previewImages(event)"
                        >
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

                        <!-- Image Skeleton Preview -->
                        <div id="photo-preview" class="flex flex-wrap gap-2 mt-3">
                            <!-- JS will inject image skeletons here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Videos Upload -->
            <div class="card bg-base-100 shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">
                        <i class="fas fa-video text-blue-500"></i>
                        Incident Videos
                    </h2>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Upload Videos</span>
                            <span class="label-text-alt">You can upload up to 2 videos (max 10MB each)</span>
                        </label>
                        <input
                            type="file"
                            name="videos[]"
                            id="video-upload"
                            class="file-input file-input-bordered @error('videos') file-input-error @enderror"
                            accept="video/*"
                            multiple
                            onchange="previewVideos(event)"
                        >
                        <div class="text-sm text-gray-500 mt-1">
                            Supported formats: MP4, WebM, MOV. Maximum file size: 10MB per video.
                        </div>
                        @error('videos')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                        @error('videos.*')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror

                        <!-- Video Skeleton Preview -->
                        <div id="video-preview" class="flex flex-wrap gap-2 mt-3">
                            <!-- JS will inject video skeletons here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image & Video Preview Scripts -->
            <script>
                function previewImages(event) {
                    const preview = document.getElementById('photo-preview');
                    preview.innerHTML = '';
                    const files = event.target.files;
                    for (let i = 0; i < files.length && i < 5; i++) {
                        const file = files[i];
                        const reader = new FileReader();
                        const skeleton = document.createElement('div');
                        skeleton.className = 'w-24 h-24 bg-gray-200 animate-pulse rounded flex items-center justify-center overflow-hidden';
                        preview.appendChild(skeleton);

                        reader.onload = function(e) {
                            skeleton.classList.remove('animate-pulse', 'bg-gray-200');
                            skeleton.innerHTML = `<img src="${e.target.result}" class="object-cover w-full h-full" alt="Photo Preview">`;
                        };
                        reader.readAsDataURL(file);
                    }
                }

                function previewVideos(event) {
                    const preview = document.getElementById('video-preview');
                    preview.innerHTML = '';
                    const files = event.target.files;
                    for (let i = 0; i < files.length && i < 2; i++) {
                        const file = files[i];
                        const reader = new FileReader();
                        // Add a skeleton similar to the image skeleton
                        const skeleton = document.createElement('div');
                        skeleton.className = 'w-32 h-24 bg-gray-200 animate-pulse rounded flex items-center justify-center overflow-hidden';
                        // Add a video icon as a placeholder while loading
                        skeleton.innerHTML = `<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 19h8a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>`;
                        preview.appendChild(skeleton);

                        reader.onload = function(e) {
                            skeleton.classList.remove('animate-pulse', 'bg-gray-200');
                            skeleton.innerHTML = `<video src="${e.target.result}" class="object-cover w-full h-full" controls></video>`;
                        };
                        reader.readAsDataURL(file);
                    }
                }
            </script>

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
                                <option value="clear" {{ old('weather_condition') == 'clear' ? 'selected' : '' }}>☀️ Clear</option>
                                <option value="cloudy" {{ old('weather_condition') == 'cloudy' ? 'selected' : '' }}>☁️ Cloudy</option>
                                <option value="rainy" {{ old('weather_condition') == 'rainy' ? 'selected' : '' }}>🌧️ Rainy</option>
                                <option value="stormy" {{ old('weather_condition') == 'stormy' ? 'selected' : '' }}>⛈️ Stormy</option>
                                <option value="foggy" {{ old('weather_condition') == 'foggy' ? 'selected' : '' }}>🌫️ Foggy</option>
                            </select>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Road Condition</span>
                            </label>
                            <select name="road_condition" class="select select-bordered">
                                <option value="">Select road condition</option>
                                <option value="dry" {{ old('road_condition') == 'dry' ? 'selected' : '' }}>Dry</option>
                                <option value="wet" {{ old('road_condition') == 'wet' ? 'selected' : '' }}>Wet</option>
                                <option value="slippery" {{ old('road_condition') == 'slippery' ? 'selected' : '' }}>Slippery</option>
                                <option value="damaged" {{ old('road_condition') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                                <option value="under_construction" {{ old('road_condition') == 'under_construction' ? 'selected' : '' }}>Under Construction</option>
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
                                   value="{{ old('casualty_count', 0) }}">
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
                                   value="{{ old('injury_count', 0) }}">
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
                                   value="{{ old('fatality_count', 0) }}">
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
                                   {{ old('vehicle_involved') ? 'checked' : '' }}>
                            <span class="label-text font-medium">Vehicle(s) involved in incident</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Vehicle Details</span>
                        </label>
                        <textarea name="vehicle_details"
                                  class="textarea textarea-bordered @error('vehicle_details') textarea-error @enderror"
                                  placeholder="Describe involved vehicles (make, model, license plate, etc.)">{{ old('vehicle_details') }}</textarea>
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
                                   placeholder="0.00" value="{{ old('property_damage_estimate') }}">
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
                                      placeholder="Describe property damage...">{{ old('damage_description') }}</textarea>
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
                                    <option value="{{ $member->id }}" {{ old('assigned_staff_id') == $member->id ? 'selected' : '' }}>
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
                                    <option value="{{ $vehicle->id }}" {{ old('assigned_vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->vehicle_number }} - {{ ucwords(str_replace('_', ' ', $vehicle->vehicle_type)) }} ({{ $vehicle->municipality }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6">
                <a href="{{ route('incidents.index') }}" class="btn btn-outline">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i>Report Incident
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.querySelector('input[name="latitude"]').value = position.coords.latitude;
            document.querySelector('input[name="longitude"]').value = position.coords.longitude;

            // Show success message
            const toast = document.createElement('div');
            toast.className = 'toast toast-top toast-end';
            toast.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>Location captured successfully!</span>
                </div>
            `;
            document.body.appendChild(toast);

            setTimeout(() => {
                document.body.removeChild(toast);
            }, 3000);
        }, function(error) {
            // Show error message
            const toast = document.createElement('div');
            toast.className = 'toast toast-top toast-end';
            toast.innerHTML = `
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Failed to get location: ${error.message}</span>
                </div>
            `;
            document.body.appendChild(toast);

            setTimeout(() => {
                document.body.removeChild(toast);
            }, 3000);
        });
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}
</script>
@endsection
