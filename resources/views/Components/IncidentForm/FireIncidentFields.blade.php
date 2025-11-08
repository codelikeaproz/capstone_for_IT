{{-- Fire Incident Specific Fields --}}
<div id="fire-incident-section" class="mb-10" data-incident-type="fire_incident" style="display: {{ old('incident_type') == 'fire_incident' ? 'block' : 'none' }}">
    <h2 class="text-lg font-semibold text-base-content mb-1">Fire Incident Details</h2>
    <p class="text-sm text-base-content/60 mb-6">Fire and evacuation-specific information</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Building Type -->
        <div class="form-control">
            <label class="label">
                <span class="label-text font-medium">Building Type <span class="text-error">*</span></span>
            </label>
            <select
                name="building_type"
                class="select select-bordered w-full focus:outline-primary @error('building_type') select-error @enderror"
            >
                <option value="">Select building type</option>
                <option value="residential" {{ old('building_type') == 'residential' ? 'selected' : '' }}>Residential</option>
                <option value="commercial" {{ old('building_type') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                <option value="industrial" {{ old('building_type') == 'industrial' ? 'selected' : '' }}>Industrial</option>
                <option value="government" {{ old('building_type') == 'government' ? 'selected' : '' }}>Government</option>
                <option value="agricultural" {{ old('building_type') == 'agricultural' ? 'selected' : '' }}>Agricultural</option>
                <option value="other" {{ old('building_type') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('building_type')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>

        <!-- Fire Spread Level -->
        <div class="form-control">
            <label class="label">
                <span class="label-text font-medium">Fire Spread Level <span class="text-error">*</span></span>
            </label>
            <select
                name="fire_spread_level"
                class="select select-bordered w-full focus:outline-primary @error('fire_spread_level') select-error @enderror"
            >
                <option value="">Select fire spread level</option>
                <option value="contained" {{ old('fire_spread_level') == 'contained' ? 'selected' : '' }}>Contained</option>
                <option value="spreading" {{ old('fire_spread_level') == 'spreading' ? 'selected' : '' }}>Spreading</option>
                <option value="widespread" {{ old('fire_spread_level') == 'widespread' ? 'selected' : '' }}>Widespread</option>
                <option value="controlled" {{ old('fire_spread_level') == 'controlled' ? 'selected' : '' }}>Controlled</option>
                <option value="extinguished" {{ old('fire_spread_level') == 'extinguished' ? 'selected' : '' }}>Extinguished</option>
            </select>
            @error('fire_spread_level')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>

        <!-- Buildings Affected -->
        <div class="form-control">
            <label class="label">
                <span class="label-text font-medium">Number of Buildings Affected</span>
            </label>
            <input
                type="number"
                name="buildings_affected"
                min="1"
                class="input input-bordered w-full focus:outline-primary @error('buildings_affected') input-error @enderror"
                placeholder="e.g., 3"
                value="{{ old('buildings_affected') }}"
            >
            @error('buildings_affected')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>

        <!-- Evacuation Required -->
        <div class="form-control">
            <label class="label cursor-pointer justify-start gap-3 bg-base-200 p-4 rounded-box">
                <input
                    type="checkbox"
                    name="evacuation_required"
                    id="evacuation_required"
                    value="1"
                    class="checkbox checkbox-primary"
                    {{ old('evacuation_required') ? 'checked' : '' }}
                    onchange="toggleEvacuationFields(this.checked)"
                >
                <div>
                    <span class="label-text font-medium block">Evacuation Required</span>
                    <span class="label-text-alt text-base-content/60">Check if evacuation was necessary</span>
                </div>
            </label>
        </div>

        <!-- Evacuated Count -->
        <div class="form-control" id="evacuated-count-container" style="display: {{ old('evacuation_required') ? 'block' : 'none' }}">
            <label class="label">
                <span class="label-text font-medium">Number of People Evacuated <span class="text-error">*</span></span>
            </label>
            <input
                type="number"
                name="evacuated_count"
                id="evacuated_count"
                min="0"
                class="input input-bordered w-full focus:outline-primary @error('evacuated_count') input-error @enderror"
                placeholder="e.g., 50"
                value="{{ old('evacuated_count') }}"
            >
            @error('evacuated_count')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>

        <!-- Fire Cause -->
        <div class="form-control md:col-span-2">
            <label class="label">
                <span class="label-text font-medium">Fire Cause</span>
                <span class="label-text-alt text-base-content/60">If known</span>
            </label>
            <textarea
                name="fire_cause"
                rows="2"
                class="textarea textarea-bordered w-full focus:outline-primary @error('fire_cause') textarea-error @enderror"
                placeholder="Describe the suspected or confirmed cause of the fire..."
            >{{ old('fire_cause') }}</textarea>
            @error('fire_cause')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>

        <!-- Property Damage Estimate -->
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

        <!-- Damage Description -->
        <div class="form-control">
            <label class="label">
                <span class="label-text font-medium">Damage Description</span>
            </label>
            <textarea
                name="damage_description"
                rows="3"
                class="textarea textarea-bordered w-full focus:outline-primary @error('damage_description') textarea-error @enderror"
                placeholder="Describe the extent of property damage..."
            >{{ old('damage_description') }}</textarea>
            @error('damage_description')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleEvacuationFields(isChecked) {
    const container = document.getElementById('evacuated-count-container');
    const evacuatedCount = document.getElementById('evacuated_count');

    if (container) {
        container.style.display = isChecked ? 'block' : 'none';
    }

    if (evacuatedCount) {
        evacuatedCount.required = isChecked;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const evacuationRequired = document.getElementById('evacuation_required');
    if (evacuationRequired) {
        toggleEvacuationFields(evacuationRequired.checked);
    }
});
</script>
@endpush

