{{-- Medical Emergency Specific Fields --}}
<div id="medical-emergency-section" class="mb-10" data-incident-type="medical_emergency" style="display: {{ old('incident_type') == 'medical_emergency' ? 'block' : 'none' }}">
    <h2 class="text-lg font-semibold text-base-content mb-1">Medical Emergency Details</h2>
    <p class="text-sm text-base-content/60 mb-6">Patient and medical-specific information</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Medical Emergency Type -->
        <div class="form-control">
            <label class="label">
                <span class="label-text font-medium">Emergency Type <span class="text-error">*</span></span>
            </label>
            <select
                name="medical_emergency_type"
                class="select select-bordered w-full focus:outline-primary @error('medical_emergency_type') select-error @enderror"
            >
                <option value="">Select emergency type</option>
                <option value="heart_attack" {{ old('medical_emergency_type') == 'heart_attack' ? 'selected' : '' }}>Heart Attack</option>
                <option value="stroke" {{ old('medical_emergency_type') == 'stroke' ? 'selected' : '' }}>Stroke</option>
                <option value="trauma" {{ old('medical_emergency_type') == 'trauma' ? 'selected' : '' }}>Trauma/Injury</option>
                <option value="respiratory" {{ old('medical_emergency_type') == 'respiratory' ? 'selected' : '' }}>Respiratory Distress</option>
                <option value="allergic_reaction" {{ old('medical_emergency_type') == 'allergic_reaction' ? 'selected' : '' }}>Allergic Reaction</option>
                <option value="seizure" {{ old('medical_emergency_type') == 'seizure' ? 'selected' : '' }}>Seizure</option>
                <option value="poisoning" {{ old('medical_emergency_type') == 'poisoning' ? 'selected' : '' }}>Poisoning</option>
                <option value="other" {{ old('medical_emergency_type') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('medical_emergency_type')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>

        <!-- Patient Count -->
        <div class="form-control">
            <label class="label">
                <span class="label-text font-medium">Number of Patients <span class="text-error">*</span></span>
            </label>
            <input
                type="number"
                name="patient_count"
                min="1"
                max="100"
                class="input input-bordered w-full focus:outline-primary @error('patient_count') input-error @enderror"
                placeholder="e.g., 1"
                value="{{ old('patient_count', 1) }}"
            >
            @error('patient_count')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>

        <!-- Ambulance Requested -->
        <div class="form-control">
            <label class="label cursor-pointer justify-start gap-3 bg-base-200 p-4 rounded-box">
                <input
                    type="checkbox"
                    name="ambulance_requested"
                    value="1"
                    class="checkbox checkbox-primary"
                    {{ old('ambulance_requested') ? 'checked' : '' }}
                >
                <div>
                    <span class="label-text font-medium block">Ambulance Requested</span>
                    <span class="label-text-alt text-base-content/60">Check if ambulance is needed</span>
                </div>
            </label>
        </div>

        <!-- Patient Symptoms -->
        <div class="form-control md:col-span-2">
            <label class="label">
                <span class="label-text font-medium">Patient Symptoms</span>
                <span class="label-text-alt text-base-content/60">Describe observed symptoms</span>
            </label>
            <textarea
                name="patient_symptoms"
                rows="3"
                class="textarea textarea-bordered w-full focus:outline-primary @error('patient_symptoms') textarea-error @enderror"
                placeholder="Describe the symptoms observed (e.g., chest pain, difficulty breathing, bleeding, etc.)..."
            >{{ old('patient_symptoms') }}</textarea>
            @error('patient_symptoms')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>
    </div>

    <!-- Alert Box for Adding Victims -->
    <div class="alert alert-info mt-6">
        <i class="fas fa-info-circle"></i>
        <div>
            <h3 class="font-bold">Patient/Victim Information</h3>
            <div class="text-xs">You will be able to add detailed patient information including vitals, medical history, and pregnancy status (for females) in the Victims/Patients section below.</div>
        </div>
    </div>
</div>

