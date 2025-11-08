{{-- Basic Incident Information Section --}}
<div class="mb-10">
    <h2 class="text-lg font-semibold text-base-content mb-1">Basic Information</h2>
    <p class="text-sm text-base-content/60 mb-6">Essential incident details</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Incident Type -->
        <div class="form-control">
            <label class="label">
                <span class="label-text font-medium">Incident Type <span class="text-error">*</span></span>
            </label>
            <select
                name="incident_type"
                id="incident_type"
                class="select select-bordered w-full focus:outline-primary @error('incident_type') select-error @enderror"
                required
                onchange="handleIncidentTypeChange(this.value)"
            >
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
            <input
                type="datetime-local"
                name="incident_date"
                class="input input-bordered w-full focus:outline-primary @error('incident_date') input-error @enderror"
                value="{{ old('incident_date', now()->format('Y-m-d\TH:i')) }}"
                max="{{ now()->format('Y-m-d\TH:i') }}"
                required
            >
            @error('incident_date')
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

        <!-- Location Details -->
        <div class="form-control md:col-span-2">
            <label class="label">
                <span class="label-text font-medium">Location Details <span class="text-error">*</span></span>
            </label>
            <textarea
                name="location"
                rows="3"
                class="textarea textarea-bordered w-full focus:outline-primary @error('location') textarea-error @enderror"
                placeholder="Enter detailed location description (e.g., street name, landmarks)..."
                required
            >{{ old('location') }}</textarea>
            @error('location')
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
            <input
                type="number"
                step="any"
                name="latitude"
                class="input input-bordered w-full focus:outline-primary @error('latitude') input-error @enderror"
                placeholder="e.g. 8.1234567"
                value="{{ old('latitude') }}"
            >
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
            <input
                type="number"
                step="any"
                name="longitude"
                class="input input-bordered w-full focus:outline-primary @error('longitude') input-error @enderror"
                placeholder="e.g. 125.1234567"
                value="{{ old('longitude') }}"
            >
            @error('longitude')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>

        <!-- Description -->
        <div class="form-control md:col-span-2">
            <label class="label">
                <span class="label-text font-medium">Detailed Description <span class="text-error">*</span></span>
                <span class="label-text-alt text-base-content/60">Be as specific as possible</span>
            </label>
            <textarea
                name="description"
                rows="5"
                class="textarea textarea-bordered w-full focus:outline-primary @error('description') textarea-error @enderror"
                placeholder="Provide a comprehensive description of the incident, including what happened, when, and any other relevant details..."
                required
            >{{ old('description') }}</textarea>
            @error('description')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>
    </div>
</div>

