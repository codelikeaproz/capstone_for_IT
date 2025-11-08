{{-- Natural Disaster Specific Fields --}}
<div id="natural-disaster-section" class="mb-10" data-incident-type="natural_disaster" style="display: {{ old('incident_type') == 'natural_disaster' ? 'block' : 'none' }}">
    <h2 class="text-lg font-semibold text-base-content mb-1">Natural Disaster Details</h2>
    <p class="text-sm text-base-content/60 mb-6">Disaster impact and affected area information</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Disaster Type -->
        <div class="form-control">
            <label class="label">
                <span class="label-text font-medium">Disaster Type <span class="text-error">*</span></span>
            </label>
            <select
                name="disaster_type"
                class="select select-bordered w-full focus:outline-primary @error('disaster_type') select-error @enderror"
            >
                <option value="">Select disaster type</option>
                <option value="flood" {{ old('disaster_type') == 'flood' ? 'selected' : '' }}>Flood</option>
                <option value="earthquake" {{ old('disaster_type') == 'earthquake' ? 'selected' : '' }}>Earthquake</option>
                <option value="landslide" {{ old('disaster_type') == 'landslide' ? 'selected' : '' }}>Landslide</option>
                <option value="typhoon" {{ old('disaster_type') == 'typhoon' ? 'selected' : '' }}>Typhoon</option>
                <option value="drought" {{ old('disaster_type') == 'drought' ? 'selected' : '' }}>Drought</option>
                <option value="volcanic" {{ old('disaster_type') == 'volcanic' ? 'selected' : '' }}>Volcanic Activity</option>
                <option value="tsunami" {{ old('disaster_type') == 'tsunami' ? 'selected' : '' }}>Tsunami</option>
                <option value="other" {{ old('disaster_type') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('disaster_type')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>

        <!-- Affected Area Size -->
        <div class="form-control">
            <label class="label">
                <span class="label-text font-medium">Affected Area Size</span>
                <span class="label-text-alt text-base-content/60">In square kilometers</span>
            </label>
            <input
                type="number"
                step="0.01"
                min="0"
                name="affected_area_size"
                class="input input-bordered w-full focus:outline-primary @error('affected_area_size') input-error @enderror"
                placeholder="e.g., 5.5"
                value="{{ old('affected_area_size') }}"
            >
            @error('affected_area_size')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>

        <!-- Families Affected -->
        <div class="form-control">
            <label class="label">
                <span class="label-text font-medium">Number of Families Affected <span class="text-error">*</span></span>
            </label>
            <input
                type="number"
                name="families_affected"
                min="0"
                class="input input-bordered w-full focus:outline-primary @error('families_affected') input-error @enderror"
                placeholder="e.g., 150"
                value="{{ old('families_affected') }}"
            >
            @error('families_affected')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>

        <!-- Structures Damaged -->
        <div class="form-control">
            <label class="label">
                <span class="label-text font-medium">Number of Structures Damaged</span>
            </label>
            <input
                type="number"
                name="structures_damaged"
                min="0"
                class="input input-bordered w-full focus:outline-primary @error('structures_damaged') input-error @enderror"
                placeholder="e.g., 50"
                value="{{ old('structures_damaged') }}"
            >
            @error('structures_damaged')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>

        <!-- Shelter Needed -->
        <div class="form-control">
            <label class="label cursor-pointer justify-start gap-3 bg-base-200 p-4 rounded-box">
                <input
                    type="checkbox"
                    name="shelter_needed"
                    value="1"
                    class="checkbox checkbox-primary"
                    {{ old('shelter_needed') ? 'checked' : '' }}
                >
                <div>
                    <span class="label-text font-medium block">Shelter Needed</span>
                    <span class="label-text-alt text-base-content/60">Check if evacuation shelter is required</span>
                </div>
            </label>
        </div>

        <!-- Infrastructure Damage -->
        <div class="form-control md:col-span-2">
            <label class="label">
                <span class="label-text font-medium">Infrastructure Damage Description</span>
            </label>
            <textarea
                name="infrastructure_damage"
                rows="3"
                class="textarea textarea-bordered w-full focus:outline-primary @error('infrastructure_damage') textarea-error @enderror"
                placeholder="Describe damage to roads, bridges, power lines, water systems, and other infrastructure..."
            >{{ old('infrastructure_damage') }}</textarea>
            @error('infrastructure_damage')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>

        <!-- Property Damage Estimate -->
        <div class="form-control">
            <label class="label">
                <span class="label-text font-medium">Estimated Total Damage Cost</span>
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

