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

        <!-- Disaster Description -->
        <div class="form-control md:col-span-2">
            <label class="label">
                <span class="label-text font-medium">Disaster Description</span>
                <span class="label-text-alt text-base-content/60">Provide detailed information about the disaster</span>
            </label>
            <textarea
                name="disaster_description"
                rows="4"
                class="textarea textarea-bordered w-full focus:outline-primary @error('disaster_description') textarea-error @enderror"
                placeholder="Describe the disaster impact, affected areas, damage to infrastructure, evacuation status, and any other relevant information..."
            >{{ old('disaster_description') }}</textarea>
            @error('disaster_description')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>
    </div>
</div>
