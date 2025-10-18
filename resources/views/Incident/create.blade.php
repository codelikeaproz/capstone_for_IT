@extends("Layouts.app")

@section('title', 'Report New Incident')

@section('content')
<div class="min-h-screen bg-base-200 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-base-content">Report New Incident</h1>
                    <p class="mt-1 text-sm text-base-content/60">
                        Complete the form below to report a new incident. Fields marked with <span class="text-error font-semibold">*</span> are required.
                    </p>
                </div>
                <a href="{{ route('incidents.index') }}" class="btn btn-ghost btn-sm gap-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>



        <form action="{{ route('incidents.store') }}" method="POST" enctype="multipart/form-data" class="bg-base-100 rounded-box shadow-sm p-8 space-y-10">
            @csrf

              <!-- Media Upload -->
              <div>
                <h2 class="text-lg font-semibold text-base-content mb-1">Incident Media</h2>
                <p class="text-sm text-base-content/60 mb-6">Upload photos and videos of the incident</p>

                <div class="space-y-6">
                    <!-- Photos Upload Section -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Photos <span class="text-error">*</span></span>
                            <span class="label-text-alt text-base-content/60">Max 5 photos, 2MB each</span>
                        </label>

                        <input
                            type="file"
                            name="photos[]"
                            id="photo-input"
                            class="file-input file-input-bordered w-full focus:outline-primary @error('photos') file-input-error @enderror"
                            accept="image/jpeg,image/png,image/jpg,image/gif"
                            multiple
                            required
                            onchange="handlePhotoUpload(this)"
                        >

                        <div class="label">
                            <span class="label-text-alt text-base-content/60">
                                <i class="fas fa-info-circle mr-1"></i>
                                Supported: JPG, PNG, GIF
                            </span>
                            <span id="photo-count-display" class="label-text-alt text-primary font-medium"></span>
                        </div>

                        @error('photos')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                        @error('photos.*')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror

                        <!-- Photo Preview Section -->
                        <div id="photo-preview-container" class="mt-4 hidden">
                            <div class="bg-base-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-sm font-semibold text-base-content">
                                        Uploaded Images
                                    </h3>
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs text-base-content/60">
                                            <span id="photo-count">0</span>/5 photos
                                        </span>
                                        <button
                                            type="button"
                                            onclick="clearAllPhotos()"
                                            class="btn btn-ghost btn-xs text-error gap-1"
                                        >
                                            <i class="fas fa-trash"></i>
                                            <span>Clear All</span>
                                        </button>
                                    </div>
                                </div>
                                <div id="photo-preview-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                                    <!-- Previews will be inserted here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Videos Upload Section -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Videos (Optional)</span>
                            <span class="label-text-alt text-base-content/60">Max 2 videos, 10MB each</span>
                        </label>

                        <input
                            type="file"
                            name="videos[]"
                            id="video-input"
                            class="file-input file-input-bordered w-full focus:outline-primary @error('videos') file-input-error @enderror"
                            accept="video/mp4,video/webm,video/quicktime"
                            multiple
                            onchange="handleVideoUpload(this)"
                        >

                        <div class="label">
                            <span class="label-text-alt text-base-content/60">
                                <i class="fas fa-info-circle mr-1"></i>
                                Supported: MP4, WebM, MOV
                            </span>
                            <span id="video-count-display" class="label-text-alt text-secondary font-medium"></span>
                        </div>

                        @error('videos')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                        @error('videos.*')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror

                        <!-- Video Preview Section -->
                        <div id="video-preview-container" class="mt-4 hidden">
                            <div class="bg-base-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-sm font-semibold text-base-content">
                                        Uploaded Videos
                                    </h3>
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs text-base-content/60">
                                            <span id="video-count">0</span>/2 videos
                                        </span>
                                        <button
                                            type="button"
                                            onclick="clearAllVideos()"
                                            class="btn btn-ghost btn-xs text-error gap-1"
                                        >
                                            <i class="fas fa-trash"></i>
                                            <span>Clear All</span>
                                        </button>
                                    </div>
                                </div>
                                <div id="video-preview-grid" class="space-y-3">
                                    <!-- Video previews will be inserted here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Basic Information -->
            <div>
                <h2 class="text-lg font-semibold text-base-content mb-1">Basic Information</h2>
                <p class="text-sm text-base-content/60 mb-6">Essential incident details</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Incident Type -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Incident Type <span class="text-error">*</span></span>
                        </label>
                        <select name="incident_type" class="select select-bordered w-full focus:outline-primary @error('incident_type') select-error @enderror" required>
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
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>




                    <!-- Severity Level -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Severity Level <span class="text-error">*</span></span>
                        </label>
                        <select name="severity_level" class="select select-bordered w-full focus:outline-primary @error('severity_level') select-error @enderror" required>
                            <option value="">Select severity</option>
                            <option value="low" {{ old('severity_level') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('severity_level') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('severity_level') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="critical" {{ old('severity_level') == 'critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                        @error('severity_level')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Incident Date -->
                    <div class="form-control md:col-span-2">
                        <label class="label">
                            <span class="label-text font-medium">Incident Date & Time <span class="text-error">*</span></span>
                        </label>
                        <input type="datetime-local" name="incident_date"
                               class="input input-bordered w-full focus:outline-primary @error('incident_date') input-error @enderror"
                               value="{{ old('incident_date', now()->format('Y-m-d\TH:i')) }}" required>
                        @error('incident_date')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Location Information -->
            <div>
                <h2 class="text-lg font-semibold text-base-content mb-1">Location Information</h2>
                <p class="text-sm text-base-content/60 mb-6">Incident location and coordinates</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Location -->
                    <div class="form-control md:col-span-2">
                        <label class="label">
                            <span class="label-text font-medium">Location <span class="text-error">*</span></span>
                        </label>
                        <textarea name="location" rows="3"
                                  class="textarea textarea-bordered w-full focus:outline-primary @error('location') textarea-error @enderror"
                                  placeholder="Enter detailed location description..." required>{{ old('location') }}</textarea>
                        @error('location')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Municipality -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Municipality <span class="text-error">*</span></span>
                        </label>
                        <select name="municipality" id="municipality-select" class="select select-bordered w-full focus:outline-primary @error('municipality') select-error @enderror" required>
                            <option value="">Select municipality</option>
                            @foreach(\App\Services\LocationService::getMunicipalities() as $municipality)
                                <option value="{{ $municipality }}" {{ old('municipality') == $municipality ? 'selected' : '' }}>
                                    {{ $municipality }}
                                </option>
                            @endforeach
                        </select>
                        @error('municipality')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Barangay -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Barangay <span class="text-error">*</span></span>
                        </label>
                        <select name="barangay" id="barangay-select" class="select select-bordered w-full focus:outline-primary @error('barangay') select-error @enderror" required disabled>
                            <option value="">Select municipality first</option>
                        </select>
                        @error('barangay')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- GPS Coordinates -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Latitude</span>
                            <button type="button" class="btn btn-xs btn-primary gap-1" onclick="getLocation()">
                                <i class="fas fa-location-arrow"></i>
                                <span>Get Location</span>
                            </button>
                        </label>
                        <input type="number" step="any" name="latitude"
                               class="input input-bordered w-full focus:outline-primary @error('latitude') input-error @enderror"
                               placeholder="e.g. 8.1234567" value="{{ old('latitude') }}">
                        @error('latitude')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Longitude</span>
                        </label>
                        <input type="number" step="any" name="longitude"
                               class="input input-bordered w-full focus:outline-primary @error('longitude') input-error @enderror"
                               placeholder="e.g. 125.1234567" value="{{ old('longitude') }}">
                        @error('longitude')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Incident Description -->
            <div>
                <h2 class="text-lg font-semibold text-base-content mb-1">Incident Description</h2>
                <p class="text-sm text-base-content/60 mb-6">Detailed narrative of the incident</p>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Detailed Description <span class="text-error">*</span></span>
                        <span class="label-text-alt text-base-content/60">Be as specific as possible</span>
                    </label>
                    <textarea name="description" rows="5"
                              class="textarea textarea-bordered w-full focus:outline-primary @error('description') textarea-error @enderror"
                              placeholder="Provide a comprehensive description of the incident, including what happened, when, and any other relevant details..." required>{{ old('description') }}</textarea>
                    @error('description')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
            </div>



            <!-- Environmental Conditions -->
            <div>
                <h2 class="text-lg font-semibold text-base-content mb-1">Environmental Conditions</h2>
                <p class="text-sm text-base-content/60 mb-6">Weather and road conditions at the time of incident</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Weather Condition</span>
                        </label>
                        <select name="weather_condition" class="select select-bordered w-full focus:outline-primary">
                            <option value="">Select weather condition</option>
                            <option value="clear" {{ old('weather_condition') == 'clear' ? 'selected' : '' }}>Clear</option>
                            <option value="cloudy" {{ old('weather_condition') == 'cloudy' ? 'selected' : '' }}>Cloudy</option>
                            <option value="rainy" {{ old('weather_condition') == 'rainy' ? 'selected' : '' }}>Rainy</option>
                            <option value="stormy" {{ old('weather_condition') == 'stormy' ? 'selected' : '' }}>Stormy</option>
                            <option value="foggy" {{ old('weather_condition') == 'foggy' ? 'selected' : '' }}>Foggy</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Road Condition</span>
                        </label>
                        <select name="road_condition" class="select select-bordered w-full focus:outline-primary">
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

            <!-- Casualty Information -->
            <div>
                <h2 class="text-lg font-semibold text-base-content mb-1">Casualty Information</h2>
                <p class="text-sm text-base-content/60 mb-6">Number of people affected by the incident</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Total Casualties</span>
                        </label>
                        <input type="number" min="0" name="casualty_count"
                               class="input input-bordered w-full focus:outline-primary @error('casualty_count') input-error @enderror"
                               placeholder="0" value="{{ old('casualty_count', 0) }}">
                        @error('casualty_count')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Injuries</span>
                        </label>
                        <input type="number" min="0" name="injury_count"
                               class="input input-bordered w-full focus:outline-primary @error('injury_count') input-error @enderror"
                               placeholder="0" value="{{ old('injury_count', 0) }}">
                        @error('injury_count')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Fatalities</span>
                        </label>
                        <input type="number" min="0" name="fatality_count"
                               class="input input-bordered w-full focus:outline-primary @error('fatality_count') input-error @enderror"
                               placeholder="0" value="{{ old('fatality_count', 0) }}">
                        @error('fatality_count')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Vehicle Information -->
            <div>
                <h2 class="text-lg font-semibold text-base-content mb-1">Vehicle Information</h2>
                <p class="text-sm text-base-content/60 mb-6">Details about vehicles involved in the incident</p>

                <div class="space-y-4">
                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3 bg-base-200 p-4 rounded-box">
                            <input type="checkbox" name="vehicle_involved" value="1"
                                   class="checkbox checkbox-primary"
                                   {{ old('vehicle_involved') ? 'checked' : '' }}>
                            <span class="label-text font-medium">Vehicle(s) involved in this incident</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Vehicle Details</span>
                            <span class="label-text-alt text-base-content/60">If applicable</span>
                        </label>
                        <textarea name="vehicle_details" rows="3"
                                  class="textarea textarea-bordered w-full focus:outline-primary @error('vehicle_details') textarea-error @enderror"
                                  placeholder="Describe involved vehicles (make, model, license plate, color, etc.)">{{ old('vehicle_details') }}</textarea>
                        @error('vehicle_details')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Property Damage -->
            <div>
                <h2 class="text-lg font-semibold text-base-content mb-1">Property Damage</h2>
                <p class="text-sm text-base-content/60 mb-6">Estimated damage and description</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Estimated Damage Cost</span>
                            <span class="label-text-alt text-base-content/60">In Philippine Peso (₱)</span>
                        </label>
                        <input type="number" step="0.01" min="0" name="property_damage_estimate"
                               class="input input-bordered w-full focus:outline-primary @error('property_damage_estimate') input-error @enderror"
                               placeholder="0.00" value="{{ old('property_damage_estimate') }}">
                        @error('property_damage_estimate')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Damage Description</span>
                        </label>
                        <textarea name="damage_description" rows="3"
                                  class="textarea textarea-bordered w-full focus:outline-primary @error('damage_description') textarea-error @enderror"
                                  placeholder="Describe the property damage in detail...">{{ old('damage_description') }}</textarea>
                        @error('damage_description')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Response Assignment -->
            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'staff')
            <div>
                <h2 class="text-lg font-semibold text-base-content mb-1">Response Assignment</h2>
                <p class="text-sm text-base-content/60 mb-6">Assign staff and resources to this incident</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Assign Staff Member</span>
                            <span class="label-text-alt text-base-content/60">Optional</span>
                        </label>
                        <select name="assigned_staff_id" class="select select-bordered w-full focus:outline-primary">
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
                            <span class="label-text-alt text-base-content/60">Optional</span>
                        </label>
                        <select name="assigned_vehicle_id" class="select select-bordered w-full focus:outline-primary">
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
            @endif

            <!-- Form Actions -->
            <div class="border-t border-base-300 pt-6">
                <div class="flex flex-col sm:flex-row justify-end items-center gap-3">
                    <a href="{{ route('incidents.index') }}" class="btn btn-outline w-full sm:w-auto gap-2">
                        <i class="fas fa-times"></i>
                        <span>Cancel</span>
                    </a>
                    <button type="submit" class="btn btn-primary w-full sm:w-auto gap-2">
                        <i class="fas fa-paper-plane"></i>
                        <span>Submit Incident Report</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// ============================================
// LOCATION FUNCTIONALITY
// ============================================
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.querySelector('input[name="latitude"]').value = position.coords.latitude;
            document.querySelector('input[name="longitude"]').value = position.coords.longitude;
            showSuccessToast('Location captured successfully!');
        }, function(error) {
            showErrorToast('Failed to get location: ' + error.message);
        });
    } else {
        showErrorToast('Geolocation is not supported by this browser.');
    }
}

// ============================================
// FILE UPLOAD WITH VALIDATION & PREVIEW
// ============================================
const MAX_PHOTOS = 5;
const MAX_VIDEOS = 2;
const MAX_PHOTO_SIZE = 2 * 1024 * 1024; // 2MB
const MAX_VIDEO_SIZE = 10 * 1024 * 1024; // 10MB

// Store files in arrays for manipulation
let photoFiles = [];
let videoFiles = [];

// Format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
}

// Update file input with current files
function updatePhotoInput() {
    const input = document.getElementById('photo-input');
    const dataTransfer = new DataTransfer();

    photoFiles.forEach(file => {
        dataTransfer.items.add(file);
    });

    input.files = dataTransfer.files;
}

function updateVideoInput() {
    const input = document.getElementById('video-input');
    const dataTransfer = new DataTransfer();

    videoFiles.forEach(file => {
        dataTransfer.items.add(file);
    });

    input.files = dataTransfer.files;
}

// Handle photo upload with validation
function handlePhotoUpload(input) {
    const files = Array.from(input.files);

    console.log('Files selected:', files.length);

    // Validate file count
    if (files.length > MAX_PHOTOS) {
        showErrorToast(`Maximum ${MAX_PHOTOS} photos allowed. You selected ${files.length}.`);
        input.value = '';
        return;
    }

    // Validate each file
    photoFiles = [];
    let hasErrors = false;

    for (let file of files) {
        console.log('Processing file:', file.name, 'Type:', file.type, 'Size:', file.size);

        // Check size
        if (file.size > MAX_PHOTO_SIZE) {
            showErrorToast(`${file.name} exceeds 2MB limit (${formatFileSize(file.size)})`);
            hasErrors = true;
            continue;
        }

        // Check type
        if (!file.type.startsWith('image/')) {
            showErrorToast(`${file.name} is not a valid image file`);
            hasErrors = true;
            continue;
        }

        photoFiles.push(file);
    }

    console.log('Valid files:', photoFiles.length);

    if (hasErrors && photoFiles.length === 0) {
        input.value = '';
        hidePhotoPreview();
        return;
    }

    // Update input with valid files
    updatePhotoInput();

    // Render previews
    renderPhotoPreview();
}

// Render photo preview with proper error handling
function renderPhotoPreview() {
    const container = document.getElementById('photo-preview-container');
    const grid = document.getElementById('photo-preview-grid');
    const countDisplay = document.getElementById('photo-count');
    const textDisplay = document.getElementById('photo-count-display');

    console.log('Rendering preview for', photoFiles.length, 'files');

    if (photoFiles.length === 0) {
        hidePhotoPreview();
        return;
    }

    // Show count
    countDisplay.textContent = photoFiles.length;
    textDisplay.textContent = `${photoFiles.length} file${photoFiles.length > 1 ? 's' : ''} selected`;

    // Clear previous previews
    grid.innerHTML = '';

    // Show container
    container.classList.remove('hidden');

    // Generate previews
    photoFiles.forEach((file, index) => {
        console.log(`Reading file ${index + 1}:`, file.name);
        const reader = new FileReader();

        reader.onload = function(e) {
            const imageUrl = e.target.result;
            console.log(`File ${file.name} loaded, URL length:`, imageUrl ? imageUrl.length : 0);

            // Create preview card
            const previewCard = document.createElement('div');
            previewCard.className = 'relative bg-base-100 rounded-lg overflow-hidden shadow-sm border border-base-300 group';

            // Build the HTML structure
            previewCard.innerHTML = `
                <div class="relative bg-base-200" style="padding-top: 100%; position: relative;">
                    <img
                        src="${imageUrl}"
                        alt="${file.name}"
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"
                        onload="console.log('Image loaded:', '${file.name}')"
                        onerror="console.error('Image failed to load:', '${file.name}')"
                    >
                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0); transition: background-color 0.3s; display: flex; align-items: center; justify-content: center;" class="group-hover:bg-opacity-40 group-hover:bg-black">
                        <button
                            type="button"
                            onclick="removePhoto(${index})"
                            class="btn btn-circle btn-sm btn-error opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                            title="Remove photo"
                            style="z-index: 10;"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="p-2">
                    <p class="text-xs text-base-content truncate" title="${file.name}">${file.name}</p>
                    <p class="text-xs text-base-content/60">${formatFileSize(file.size)}</p>
                </div>
            `;

            grid.appendChild(previewCard);
            console.log(`Preview card added for ${file.name}`);
        };

        reader.onerror = function(error) {
            console.error('Error reading file:', file.name, error);
            showErrorToast(`Failed to read ${file.name}`);
        };

        // Start reading the file
        reader.readAsDataURL(file);
    });
}

// Hide photo preview
function hidePhotoPreview() {
    const container = document.getElementById('photo-preview-container');
    const textDisplay = document.getElementById('photo-count-display');
    container.classList.add('hidden');
    textDisplay.textContent = '';
}

// Remove individual photo
function removePhoto(index) {
    photoFiles.splice(index, 1);
    updatePhotoInput();
    renderPhotoPreview();
    showSuccessToast('Photo removed successfully');
}

// Clear all photos
function clearAllPhotos() {
    if (confirm('Are you sure you want to remove all photos?')) {
        photoFiles = [];
        document.getElementById('photo-input').value = '';
        hidePhotoPreview();
        showSuccessToast('All photos cleared');
    }
}

// Handle video upload with validation
function handleVideoUpload(input) {
    const files = Array.from(input.files);

    // Validate file count
    if (files.length > MAX_VIDEOS) {
        showErrorToast(`Maximum ${MAX_VIDEOS} videos allowed. You selected ${files.length}.`);
        input.value = '';
        return;
    }

    // Validate each file
    videoFiles = [];
    let hasErrors = false;

    for (let file of files) {
        // Check size
        if (file.size > MAX_VIDEO_SIZE) {
            showErrorToast(`${file.name} exceeds 10MB limit (${formatFileSize(file.size)})`);
            hasErrors = true;
            continue;
        }

        // Check type
        const validVideoTypes = ['video/mp4', 'video/webm', 'video/quicktime'];
        if (!validVideoTypes.includes(file.type.toLowerCase())) {
            showErrorToast(`${file.name} is not a valid video file`);
            hasErrors = true;
            continue;
        }

        videoFiles.push(file);
    }

    if (hasErrors && videoFiles.length === 0) {
        input.value = '';
        hideVideoPreview();
        return;
    }

    // Update input with valid files
    updateVideoInput();

    // Render previews
    renderVideoPreview();
}

// Render video preview
function renderVideoPreview() {
    const container = document.getElementById('video-preview-container');
    const grid = document.getElementById('video-preview-grid');
    const countDisplay = document.getElementById('video-count');
    const textDisplay = document.getElementById('video-count-display');

    if (videoFiles.length === 0) {
        hideVideoPreview();
        return;
    }

    // Show count
    countDisplay.textContent = videoFiles.length;
    textDisplay.textContent = `${videoFiles.length} file${videoFiles.length > 1 ? 's' : ''} selected`;

    // Clear previous previews
    grid.innerHTML = '';

    // Generate video previews
    videoFiles.forEach((file, index) => {
        const videoCard = document.createElement('div');
        videoCard.className = 'relative bg-base-100 rounded-lg overflow-hidden shadow-sm border border-base-300 p-3';
        videoCard.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    <i class="fas fa-video text-3xl text-secondary"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-base-content truncate font-medium" title="${file.name}">${file.name}</p>
                    <p class="text-xs text-base-content/60">${formatFileSize(file.size)}</p>
                </div>
                <button
                    type="button"
                    onclick="removeVideo(${index})"
                    class="btn btn-circle btn-sm btn-error flex-shrink-0"
                    title="Remove video"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        grid.appendChild(videoCard);
    });

    container.classList.remove('hidden');
}

// Hide video preview
function hideVideoPreview() {
    const container = document.getElementById('video-preview-container');
    const textDisplay = document.getElementById('video-count-display');
    container.classList.add('hidden');
    textDisplay.textContent = '';
}

// Remove individual video
function removeVideo(index) {
    videoFiles.splice(index, 1);
    updateVideoInput();
    renderVideoPreview();
    showSuccessToast('Video removed successfully');
}

// Clear all videos
function clearAllVideos() {
    if (confirm('Are you sure you want to remove all videos?')) {
        videoFiles = [];
        document.getElementById('video-input').value = '';
        hideVideoPreview();
        showSuccessToast('All videos cleared');
    }
}

// Handle video upload with validation
function handleVideoUpload(input) {
    const files = Array.from(input.files);

    // Validate file count
    if (files.length > MAX_VIDEOS) {
        showErrorToast(`Maximum ${MAX_VIDEOS} videos allowed. You selected ${files.length}.`);
        input.value = ''; // Clear selection
        return;
    }

    // Validate each file
    videoFiles = []; // Reset
    let hasErrors = false;

    for (let file of files) {
        // Check size
        if (file.size > MAX_VIDEO_SIZE) {
            showErrorToast(`${file.name} exceeds 10MB limit (${formatFileSize(file.size)})`);
            hasErrors = true;
            continue;
        }

        // Check type
        if (!file.type.match('video.*')) {
            showErrorToast(`${file.name} is not a valid video file`);
            hasErrors = true;
            continue;
        }

        videoFiles.push(file);
    }

    if (hasErrors && videoFiles.length === 0) {
        input.value = ''; // Clear all if none valid
        hideVideoPreview();
        return;
    }

    // Update input with valid files
    updateVideoInput();

    // Render previews
    renderVideoPreview();
}

// Render video preview
function renderVideoPreview() {
    const container = document.getElementById('video-preview-container');
    const grid = document.getElementById('video-preview-grid');
    const countDisplay = document.getElementById('video-count');
    const textDisplay = document.getElementById('video-count-display');

    if (videoFiles.length === 0) {
        hideVideoPreview();
        return;
    }

    // Show count
    countDisplay.textContent = videoFiles.length;
    textDisplay.textContent = `${videoFiles.length} file${videoFiles.length > 1 ? 's' : ''} selected`;

    // Clear previous previews
    grid.innerHTML = '';

    // Generate video previews
    videoFiles.forEach((file, index) => {
        const videoCard = document.createElement('div');
        videoCard.className = 'relative bg-base-100 rounded-lg overflow-hidden shadow-sm border border-base-300 p-3';
        videoCard.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    <i class="fas fa-video text-3xl text-secondary"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-base-content truncate font-medium" title="${file.name}">${file.name}</p>
                    <p class="text-xs text-base-content/60">${formatFileSize(file.size)}</p>
                </div>
                <button
                    type="button"
                    onclick="removeVideo(${index})"
                    class="btn btn-circle btn-sm btn-error flex-shrink-0"
                    title="Remove video"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        grid.appendChild(videoCard);
    });

    container.classList.remove('hidden');
}

// Hide video preview
function hideVideoPreview() {
    const container = document.getElementById('video-preview-container');
    const textDisplay = document.getElementById('video-count-display');
    container.classList.add('hidden');
    textDisplay.textContent = '';
}

// Remove individual video
function removeVideo(index) {
    videoFiles.splice(index, 1);
    updateVideoInput();
    renderVideoPreview();
    showSuccessToast('Video removed successfully');
}

// Clear all videos
function clearAllVideos() {
    if (confirm('Are you sure you want to remove all videos?')) {
        videoFiles = [];
        document.getElementById('video-input').value = '';
        hideVideoPreview();
        showSuccessToast('All videos cleared');
    }
}

// ============================================
// DYNAMIC BARANGAY LOADING
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const municipalitySelect = document.getElementById('municipality-select');
    const barangaySelect = document.getElementById('barangay-select');

    // Load barangays when municipality changes
    municipalitySelect.addEventListener('change', function() {
        const municipality = this.value;

        // Reset barangay select
        barangaySelect.innerHTML = '<option value="">Loading barangays...</option>';
        barangaySelect.disabled = true;

        if (!municipality) {
            barangaySelect.innerHTML = '<option value="">Select municipality first</option>';
            return;
        }

        // Fetch barangays for selected municipality
        fetch(`{{ route('api.barangays') }}?municipality=${encodeURIComponent(municipality)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.barangays) {
                    // Clear and populate barangay select
                    barangaySelect.innerHTML = '<option value="">Select barangay</option>';

                    data.barangays.forEach(barangay => {
                        const option = document.createElement('option');
                        option.value = barangay;
                        option.textContent = barangay;

                        // Restore old value if exists
                        if ('{{ old("barangay") }}' === barangay) {
                            option.selected = true;
                        }

                        barangaySelect.appendChild(option);
                    });

                    barangaySelect.disabled = false;
                } else {
                    barangaySelect.innerHTML = '<option value="">No barangays found</option>';
                }
            })
            .catch(error => {
                console.error('Error loading barangays:', error);
                barangaySelect.innerHTML = '<option value="">Error loading barangays</option>';
                showErrorToast('Failed to load barangays. Please try again.');
            });
    });

    // Trigger change event if municipality is pre-selected (for old values)
    if (municipalitySelect.value) {
        municipalitySelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection
