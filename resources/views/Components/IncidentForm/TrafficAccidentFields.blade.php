{{-- Traffic Accident Specific Fields --}}
<div id="traffic-accident-section" class="mb-10" data-incident-type="traffic_accident" style="display: {{ old('incident_type') == 'traffic_accident' ? 'block' : 'none' }}">
    <h2 class="text-lg font-semibold text-base-content mb-1">Traffic Accident Details</h2>
    <p class="text-sm text-base-content/60 mb-6">Vehicle and accident-specific information</p>

    <div class="space-y-4">
        <!-- Vehicle Involved Checkbox -->
        <div class="form-control">
            <label class="label cursor-pointer justify-start gap-3 bg-base-200 p-4 rounded-box">
                <input
                    type="checkbox"
                    name="vehicle_involved"
                    id="vehicle_involved"
                    value="1"
                    class="checkbox checkbox-primary"
                    {{ old('vehicle_involved') ? 'checked' : '' }}
                    onchange="toggleVehicleDetails(this.checked)"
                >
                <span class="label-text font-medium">Vehicle(s) involved in this incident</span>
            </label>
        </div>

        <!-- Vehicle Details Container -->
        <div id="vehicle-details-container" style="display: {{ old('vehicle_involved') ? 'block' : 'none' }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Vehicle Count -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Number of Vehicles <span class="text-error">*</span></span>
                    </label>
                    <input
                        type="number"
                        name="vehicle_count"
                        id="vehicle_count"
                        min="1"
                        max="50"
                        class="input input-bordered w-full focus:outline-primary @error('vehicle_count') input-error @enderror"
                        placeholder="e.g., 2"
                        value="{{ old('vehicle_count') }}"
                    >
                    @error('vehicle_count')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- License Plates -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">License Plate(s)</span>
                        <span class="label-text-alt text-base-content/60">Separate with commas</span>
                    </label>
                    <input
                        type="text"
                        name="license_plates_input"
                        id="license_plates_input"
                        class="input input-bordered w-full focus:outline-primary"
                        placeholder="e.g., ABC-1234, XYZ-5678"
                        value="{{ old('license_plates') ? implode(', ', old('license_plates')) : '' }}"
                    >
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">
                            <i class="fas fa-info-circle mr-1"></i>
                            Enter license plates separated by commas
                        </span>
                    </label>
                </div>

                <!-- Vehicle Details -->
                <div class="form-control md:col-span-2">
                    <label class="label">
                        <span class="label-text font-medium">Vehicle Details <span class="text-error">*</span></span>
                    </label>
                    <textarea
                        name="vehicle_details"
                        rows="3"
                        class="textarea textarea-bordered w-full focus:outline-primary @error('vehicle_details') textarea-error @enderror"
                        placeholder="Describe involved vehicles (make, model, color, condition, etc.)..."
                    >{{ old('vehicle_details') }}</textarea>
                    @error('vehicle_details')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Driver Information -->
                <div class="form-control md:col-span-2">
                    <label class="label">
                        <span class="label-text font-medium">Driver Information</span>
                        <span class="label-text-alt text-base-content/60">If known</span>
                    </label>
                    <textarea
                        name="driver_information"
                        rows="2"
                        class="textarea textarea-bordered w-full focus:outline-primary"
                        placeholder="Driver names, license numbers, contact information (if available)..."
                    >{{ old('driver_information') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Road and Weather Conditions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
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
        </div>

        <!-- Property Damage -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Estimated Damage Cost</span>
                    <span class="label-text-alt text-base-content/60">In Philippine Peso (₱)</span>
                </label>
                <input
                    type="number"
                    step="0.01"
                    min="0"
                    name="property_damage_estimate"
                    class="input input-bordered w-full focus:outline-primary @error('property_damage_estimate') input-error @enderror"
                    placeholder="0.00"
                    value="{{ old('property_damage_estimate') }}"
                >
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
                <textarea
                    name="damage_description"
                    rows="3"
                    class="textarea textarea-bordered w-full focus:outline-primary @error('damage_description') textarea-error @enderror"
                    placeholder="Describe the property damage in detail..."
                >{{ old('damage_description') }}</textarea>
                @error('damage_description')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleVehicleDetails(isChecked) {
    const container = document.getElementById('vehicle-details-container');
    if (container) {
        container.style.display = isChecked ? 'block' : 'none';

        // Make fields required/not required
        const vehicleCount = document.getElementById('vehicle_count');
        const vehicleDetails = document.querySelector('textarea[name="vehicle_details"]');

        if (vehicleCount) vehicleCount.required = isChecked;
        if (vehicleDetails) vehicleDetails.required = isChecked;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const vehicleInvolved = document.getElementById('vehicle_involved');
    if (vehicleInvolved) {
        toggleVehicleDetails(vehicleInvolved.checked);
    }
});
</script>
@endpush

