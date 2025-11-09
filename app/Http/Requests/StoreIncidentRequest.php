<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $isAuthorized = auth()->check();

        // DEBUG: Log authorization check
        \Log::info('StoreIncidentRequest Authorization Check', [
            'authorized' => $isAuthorized,
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role ?? 'N/A',
        ]);

        return $isAuthorized;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            // Step 1: Basic Information
            'incident_type' => 'required|in:traffic_accident,medical_emergency,fire_incident,natural_disaster,criminal_activity,other',
            'severity_level' => 'required|in:critical,high,medium,low',
            'incident_date' => 'required|date|before_or_equal:now',
            'location' => 'required|string|max:500',
            'municipality' => 'required|string|max:255',
            'barangay' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'description' => 'required|string|min:20',

            // Step 2: Environmental Conditions
            'weather_condition' => 'nullable|in:clear,cloudy,rainy,stormy,foggy',
            'road_condition' => 'nullable|in:dry,wet,slippery,damaged,under_construction',

            // Step 3: Property Damage
            'property_damage_estimate' => 'nullable|numeric|min:0',
            'damage_description' => 'nullable|string',

            // Step 4: Media (Photos required) - OPTIMIZED with stricter validation
            'photos' => 'required|array|min:1|max:' . config('media.photos.max_count', 5),
            'photos.*' => 'image|mimes:' . config('media.photos.validation.mimes', 'jpeg,png,jpg,gif,webp') . '|max:' . config('media.photos.validation.max_size', 3072),
            'videos' => 'nullable|array|max:' . config('media.videos.max_count', 2),
            'videos.*' => 'mimetypes:video/mp4,video/webm,video/quicktime|max:' . config('media.videos.validation.max_size', 20480),

            // Step 5: Assignment
            'assigned_staff_id' => 'nullable|exists:users,id',
            'assigned_vehicle_id' => 'nullable|exists:vehicles,id',

            // Casualty Information
            'casualty_count' => 'nullable|integer|min:0',
            'injury_count' => 'nullable|integer|min:0',
            'fatality_count' => 'nullable|integer|min:0',
        ];

        // Conditional rules based on incident type
        $rules = array_merge($rules, $this->getIncidentTypeSpecificRules());

        return $rules;
    }

    /**
     * Get validation rules specific to incident type
     *
     * @return array
     */
    private function getIncidentTypeSpecificRules(): array
    {
        $incidentType = $this->input('incident_type');

        return match ($incidentType) {
            'traffic_accident' => [
                'vehicle_involved' => 'required|boolean',
                'vehicle_count' => 'required_if:vehicle_involved,true|nullable|integer|min:1|max:50',
                'vehicle_details' => 'required_if:vehicle_involved,true|nullable|string',
                'license_plates' => 'nullable|array',
                'license_plates.*' => 'string|max:20',
                'driver_information' => 'nullable|string',
            ],
            'medical_emergency' => [
                'medical_emergency_type' => 'required|in:heart_attack,stroke,trauma,respiratory,allergic_reaction,seizure,poisoning,other',
                'ambulance_requested' => 'required|boolean',
                'patient_count' => 'required|integer|min:1|max:100',
                'patient_symptoms' => 'nullable|string',
            ],
            'fire_incident' => [
                'building_type' => 'required|in:residential,commercial,industrial,government,agricultural,other',
                'fire_spread_level' => 'required|in:contained,spreading,widespread,controlled,extinguished',
                'evacuation_required' => 'required|boolean',
                'evacuated_count' => 'required_if:evacuation_required,true|nullable|integer|min:0',
                'fire_cause' => 'nullable|string',
                'buildings_affected' => 'nullable|integer|min:1',
            ],
            'natural_disaster' => [
                'disaster_type' => 'required|in:flood,earthquake,landslide,typhoon,drought,volcanic,tsunami,other',
                'affected_area_size' => 'nullable|numeric|min:0',
                'shelter_needed' => 'required|boolean',
                'families_affected' => 'required|integer|min:0',
                'structures_damaged' => 'nullable|integer|min:0',
                'infrastructure_damage' => 'nullable|string',
            ],
            'criminal_activity' => [
                'crime_type' => 'required|in:assault,theft,vandalism,domestic_violence,other',
                'police_notified' => 'required|boolean',
                'case_number' => 'nullable|string|max:50',
                'suspect_description' => 'nullable|string',
            ],
            default => [],
        };
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // DEBUG: Log validation failures
        \Log::error('StoreIncidentRequest Validation Failed', [
            'errors' => $validator->errors()->toArray(),
            'input' => $this->except(['photos', 'videos']), // Exclude files from log
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization()
    {
        // DEBUG: Log authorization failures
        \Log::error('StoreIncidentRequest Authorization Failed', [
            'user_id' => auth()->id() ?? 'Not authenticated',
            'url' => $this->url(),
        ]);

        parent::failedAuthorization();
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'incident_type.required' => 'Please select an incident type.',
            'severity_level.required' => 'Please select a severity level.',
            'incident_date.required' => 'Incident date is required.',
            'incident_date.before_or_equal' => 'Incident date cannot be in the future.',
            'location.required' => 'Please provide the incident location.',
            'municipality.required' => 'Please select a municipality.',
            'barangay.required' => 'Please select a barangay.',
            'description.required' => 'Please provide a detailed description.',
            'description.min' => 'Description must be at least 20 characters.',
            'photos.required' => 'Please upload at least one photo of the incident.',
            'photos.min' => 'Please upload at least one photo.',
            'photos.max' => 'You can upload a maximum of ' . config('media.photos.max_count', 5) . ' photos.',
            'photos.*.image' => 'All photo files must be valid images.',
            'photos.*.mimes' => 'Photos must be in JPG, PNG, GIF, or WebP format.',
            'photos.*.max' => 'Each photo must not exceed ' . (config('media.photos.validation.max_size', 3072) / 1024) . 'MB in size.',
            'videos.max' => 'You can upload a maximum of ' . config('media.videos.max_count', 2) . ' videos.',
            'videos.*.mimetypes' => 'Videos must be in MP4, WebM, or MOV format.',
            'videos.*.max' => 'Each video must not exceed ' . (config('media.videos.validation.max_size', 20480) / 1024) . 'MB in size.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'incident_type' => 'incident type',
            'severity_level' => 'severity level',
            'incident_date' => 'incident date',
            'assigned_staff_id' => 'assigned staff',
            'assigned_vehicle_id' => 'assigned vehicle',
        ];
    }
}
