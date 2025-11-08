<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateIncidentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $incident = $this->route('incident');

        // Admin can update any incident
        if (Auth::user()->role === 'admin') {
            return true;
        }

        // Staff can only update incidents in their municipality
        return
         Auth::user()->municipality === $incident->municipality;

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // If only updating status/resolution (quick update)
        if ($this->has('maintain_other_fields')) {
            return [
                'status' => 'required|in:pending,active,resolved,closed',
                'resolution_notes' => 'nullable|string|max:1000',
            ];
        }

        // Full update validation
        $rules = [
            // Basic Information
            'incident_type' => 'required|in:traffic_accident,medical_emergency,fire_incident,natural_disaster,criminal_activity,other',
            'severity_level' => 'required|in:critical,high,medium,low',
            'status' => 'required|in:pending,active,resolved,closed',
            'incident_date' => 'required|date|before_or_equal:now',
            'location' => 'required|string|max:500',
            'municipality' => 'required|string|max:255',
            'barangay' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'description' => 'required|string|min:20',

            // Environmental Conditions
            'weather_condition' => 'nullable|in:clear,cloudy,rainy,stormy,foggy',
            'road_condition' => 'nullable|in:dry,wet,slippery,damaged,under_construction',

            // Casualty Information
            'casualty_count' => 'nullable|integer|min:0',
            'injury_count' => 'nullable|integer|min:0',
            'fatality_count' => 'nullable|integer|min:0',

            // Property Damage
            'property_damage_estimate' => 'nullable|numeric|min:0',
            'damage_description' => 'nullable|string',

            // Media (optional for updates)
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'videos' => 'nullable|array|max:2',
            'videos.*' => 'mimetypes:video/mp4,video/webm,video/quicktime|max:10240',

            // Assignment
            'assigned_staff_id' => 'nullable|exists:users,id',
            'assigned_vehicle_id' => 'nullable|exists:vehicles,id',

            // Resolution
            'resolution_notes' => 'nullable|string|max:1000',

            // Vehicle Information
            'vehicle_involved' => 'nullable|boolean',
            'vehicle_details' => 'nullable|string',
        ];

        // Add incident type specific rules
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
                'vehicle_count' => 'nullable|integer|min:1|max:50',
                'license_plates_input' => 'nullable|string',
                'driver_information' => 'nullable|string',
            ],
            'medical_emergency' => [
                'medical_emergency_type' => 'nullable|in:heart_attack,stroke,trauma,respiratory,allergic_reaction,seizure,poisoning,other',
                'ambulance_requested' => 'nullable|boolean',
                'patient_count' => 'nullable|integer|min:1|max:100',
                'patient_symptoms' => 'nullable|string',
            ],
            'fire_incident' => [
                'building_type' => 'nullable|in:residential,commercial,industrial,government,agricultural,other',
                'fire_spread_level' => 'nullable|in:contained,spreading,widespread,controlled,extinguished',
                'evacuation_required' => 'nullable|boolean',
                'evacuated_count' => 'nullable|integer|min:0',
                'fire_cause' => 'nullable|string',
                'buildings_affected' => 'nullable|integer|min:1',
            ],
            'natural_disaster' => [
                'disaster_type' => 'nullable|in:flood,earthquake,landslide,typhoon,drought,volcanic,tsunami,other',
                'affected_area_size' => 'nullable|numeric|min:0',
                'shelter_needed' => 'nullable|boolean',
                'families_affected' => 'nullable|integer|min:0',
                'structures_damaged' => 'nullable|integer|min:0',
                'infrastructure_damage' => 'nullable|string',
            ],
            'criminal_activity' => [
                'crime_type' => 'nullable|in:assault,theft,vandalism,domestic_violence,other',
                'police_notified' => 'nullable|boolean',
                'case_number' => 'nullable|string|max:50',
                'suspect_description' => 'nullable|string',
            ],
            default => [],
        };
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
            'status.required' => 'Please select a status.',
            'incident_date.required' => 'Incident date is required.',
            'incident_date.before_or_equal' => 'Incident date cannot be in the future.',
            'location.required' => 'Please provide the incident location.',
            'municipality.required' => 'Please select a municipality.',
            'barangay.required' => 'Please select a barangay.',
            'description.required' => 'Please provide a detailed description.',
            'description.min' => 'Description must be at least 20 characters.',
            'photos.max' => 'You can upload a maximum of 5 photos.',
            'photos.*.image' => 'All photo files must be valid images.',
            'photos.*.mimes' => 'Photos must be in JPG, PNG, or GIF format.',
            'photos.*.max' => 'Each photo must not exceed 2MB in size.',
            'videos.max' => 'You can upload a maximum of 2 videos.',
            'videos.*.mimetypes' => 'Videos must be in MP4, WebM, or MOV format.',
            'videos.*.max' => 'Each video must not exceed 10MB in size.',
            'assigned_staff_id.exists' => 'The selected staff member does not exist.',
            'assigned_vehicle_id.exists' => 'The selected vehicle does not exist.',
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
            'resolution_notes' => 'resolution notes',
        ];
    }
}
