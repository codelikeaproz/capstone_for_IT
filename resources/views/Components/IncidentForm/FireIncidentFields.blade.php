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
            </select>
            @error('fire_spread_level')
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
    </div>
</div>
