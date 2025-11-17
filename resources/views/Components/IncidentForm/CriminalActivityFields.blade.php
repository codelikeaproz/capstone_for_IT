{{-- Criminal Activity Specific Fields --}}
<div id="criminal-activity-section" class="mb-10" data-incident-type="criminal_activity" style="display: {{ old('incident_type') == 'criminal_activity' ? 'block' : 'none' }}">
    <h2 class="text-lg font-semibold text-base-content mb-1">Criminal Activity Details</h2>
    <p class="text-sm text-base-content/60 mb-6">Crime and police notification information</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Crime Type -->
        <div class="form-control">
            <label class="label">
                <span class="label-text font-medium">Crime Type <span class="text-error">*</span></span>
            </label>
            <select
                name="crime_type"
                class="select select-bordered w-full focus:outline-primary @error('crime_type') select-error @enderror"
            >
                <option value="">Select crime type</option>
                <option value="assault" {{ old('crime_type') == 'assault' ? 'selected' : '' }}>Assault</option>
                <option value="theft" {{ old('crime_type') == 'theft' ? 'selected' : '' }}>Theft/Robbery</option>
                <option value="vandalism" {{ old('crime_type') == 'vandalism' ? 'selected' : '' }}>Vandalism</option>
                <option value="domestic_violence" {{ old('crime_type') == 'domestic_violence' ? 'selected' : '' }}>Domestic Violence</option>
                <option value="other" {{ old('crime_type') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('crime_type')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>

        <!-- Suspect Description -->
        <div class="form-control md:col-span-2">
            <label class="label">
                <span class="label-text font-medium">Suspect Description</span>
                <span class="label-text-alt text-base-content/60">If known or witnessed</span>
            </label>
            <textarea
                name="suspect_description"
                rows="3"
                class="textarea textarea-bordered w-full focus:outline-primary @error('suspect_description') textarea-error @enderror"
                placeholder="Describe the suspect(s): appearance, clothing, vehicle, direction of escape, etc..."
            >{{ old('suspect_description') }}</textarea>
            @error('suspect_description')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>

        <!-- Property Damage Estimate (if applicable) -->
        <div class="form-control">
            <label class="label">
                <span class="label-text font-medium">Estimated Property Loss/Damage</span>
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
                <span class="label-text font-medium">Damage/Loss Description</span>
            </label>
            <textarea
                name="damage_description"
                rows="3"
                class="textarea textarea-bordered w-full focus:outline-primary @error('damage_description') textarea-error @enderror"
                placeholder="Describe property damage or stolen items..."
            >{{ old('damage_description') }}</textarea>
            @error('damage_description')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>
    </div>

    <!-- Warning Alert -->
    <div class="alert alert-warning mt-6">
        <i class="fas fa-exclamation-triangle"></i>
        <div>
            <h3 class="font-bold">Important Notice</h3>
            <div class="text-xs">If the police have not been notified yet, please coordinate with local law enforcement as soon as possible. This system is for MDRRMO coordination purposes and does not replace official police reports.</div>
        </div>
    </div>
</div>


