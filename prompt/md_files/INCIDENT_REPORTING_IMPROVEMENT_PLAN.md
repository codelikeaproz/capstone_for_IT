# BukidnonAlert: Incident Reporting System Improvement Plan

## Executive Summary

This document outlines the comprehensive restructuring of the Incident Reporting system to meet PRD requirements with Laravel best practices, minimal JavaScript, and clean MVC architecture.

---

## Current Issues Identified

### 1. **Form Structure Problems**
- ❌ Flat, single-page form with 500+ lines
- ❌ No conditional field display based on incident type
- ❌ Victim management is separate (should be integrated)
- ❌ No dynamic vehicle-involved sections
- ❌ Missing medical emergency specific fields
- ❌ Heavy JavaScript dependency (1000+ lines)

### 2. **Missing Database Fields**
```sql
-- victims table needs:
- is_pregnant (boolean)
- trimester (enum: 'first', 'second', 'third')
- pregnancy_complications (text)
- age_category (computed: child, teen, adult, elderly)
- requires_special_care (boolean)
- special_care_notes (text)
```

### 3. **Missing Conditional Logic**
- Traffic Accident → Vehicle details, license plates, driver info
- Medical Emergency → Patient vitals, medical history, pregnancy status
- Fire Incident → Building type, fire spread, evacuation status
- Natural Disaster → Disaster type, affected area size, shelter needs

---

## Proposed Solution Architecture

### **Multi-Step Session-Based Form (Server-Side State Management)**

```
Step 1: Basic Incident Information
├── Incident Type Selection (triggers conditional logic)
├── Date/Time
├── Location (Municipality → Barangay)
└── Severity Level

Step 2: Incident-Type Specific Details
├── IF traffic_accident:
│   ├── Vehicle count
│   ├── Vehicle details (dynamic)
│   └── Road/Weather conditions
├── IF medical_emergency:
│   ├── Patient count
│   ├── Medical emergency type
│   └── Ambulance required?
├── IF fire_incident:
│   ├── Building type
│   ├── Fire spread level
│   └── Casualties estimate
└── [Other incident types...]

Step 3: Victim/Patient Management
├── Add multiple victims (inline)
├── Personal information
├── Medical status
├── IF female + medical_emergency:
│   ├── Is pregnant? (checkbox)
│   └── IF yes: Trimester, complications
└── Emergency contacts

Step 4: Media Upload
├── Photos (max 5, required)
├── Videos (max 2, optional)
└── Documents (optional)

Step 5: Assignment & Review
├── Assign staff (admin/staff only)
├── Assign vehicle (admin/staff only)
├── Review all information
└── Submit
```

---

## Implementation Plan

### **Phase 1A: Database Enhancements**

#### 1.1 Create Migration for Victim Medical Fields
```php
// database/migrations/2025_01_XX_add_medical_fields_to_victims_table.php
public function up(): void
{
    Schema::table('victims', function (Blueprint $table) {
        // Pregnancy-related fields
        $table->boolean('is_pregnant')->default(false)->after('gender');
        $table->enum('pregnancy_trimester', ['first', 'second', 'third'])->nullable()->after('is_pregnant');
        $table->text('pregnancy_complications')->nullable()->after('pregnancy_trimester');
        
        // Age-based care
        $table->string('age_category', 20)->nullable()->after('age'); // child, teen, adult, elderly
        $table->boolean('requires_special_care')->default(false)->after('age_category');
        $table->text('special_care_notes')->nullable()->after('requires_special_care');
        
        // Vitals (for medical emergencies)
        $table->string('blood_pressure')->nullable()->after('medical_status');
        $table->integer('heart_rate')->nullable()->after('blood_pressure');
        $table->decimal('temperature', 4, 1)->nullable()->after('heart_rate'); // Celsius
        $table->integer('respiratory_rate')->nullable()->after('temperature');
        $table->string('consciousness_level')->nullable()->after('respiratory_rate'); // Alert, Verbal, Pain, Unresponsive
    });
}
```

#### 1.2 Add Incident Type-Specific Fields
```php
// database/migrations/2025_01_XX_add_incident_type_fields_to_incidents_table.php
public function up(): void
{
    Schema::table('incidents', function (Blueprint $table) {
        // Traffic Accident specific
        $table->integer('vehicle_count')->nullable()->after('vehicle_involved');
        $table->json('license_plates')->nullable()->after('vehicle_count');
        
        // Medical Emergency specific
        $table->string('medical_emergency_type')->nullable()->after('incident_type');
        $table->boolean('ambulance_requested')->default(false)->after('medical_emergency_type');
        $table->integer('patient_count')->nullable()->after('ambulance_requested');
        
        // Fire Incident specific
        $table->string('building_type')->nullable()->after('incident_type');
        $table->enum('fire_spread_level', ['contained', 'spreading', 'widespread', 'controlled'])->nullable();
        $table->boolean('evacuation_required')->default(false);
        $table->integer('evacuated_count')->nullable();
        
        // Natural Disaster specific
        $table->string('disaster_type')->nullable()->after('incident_type');
        $table->decimal('affected_area_size', 10, 2)->nullable(); // in square kilometers
        $table->boolean('shelter_needed')->default(false);
        $table->integer('families_affected')->nullable();
    });
}
```

---

### **Phase 1B: Service Layer Implementation**

#### 2.1 Create IncidentService
```php
// app/Services/IncidentService.php
<?php

namespace App\Services;

use App\Models\Incident;
use App\Models\Victim;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class IncidentService
{
    /**
     * Create incident with all related data (victims, media, assignments)
     * Uses database transaction for data integrity
     */
    public function createIncident(array $data): Incident
    {
        return DB::transaction(function () use ($data) {
            // Extract and process media
            $photoPaths = $this->processPhotos($data['photos'] ?? []);
            $videoPaths = $this->processVideos($data['videos'] ?? []);
            
            // Extract victims data
            $victimsData = $data['victims'] ?? [];
            unset($data['victims']);
            
            // Create incident
            $incident = Incident::create([
                ...$data,
                'incident_number' => Incident::generateIncidentNumber(),
                'reported_by' => auth()->id(),
                'status' => 'pending',
                'photos' => $photoPaths,
                'videos' => $videoPaths,
            ]);
            
            // Create victims if any
            foreach ($victimsData as $victimData) {
                $this->createVictimForIncident($incident, $victimData);
            }
            
            // Update vehicle status if assigned
            if (!empty($data['assigned_vehicle_id'])) {
                $this->assignVehicle($incident, $data['assigned_vehicle_id']);
            }
            
            // Log activity
            activity()
                ->performedOn($incident)
                ->withProperties(['data' => $data])
                ->log('Incident created with all details');
            
            return $incident->load(['victims', 'assignedStaff', 'assignedVehicle']);
        });
    }
    
    /**
     * Process and store photo uploads
     */
    private function processPhotos(array $photos): array
    {
        $paths = [];
        foreach ($photos as $photo) {
            $path = $photo->store('incident_photos', 'public');
            $paths[] = $path;
        }
        return $paths;
    }
    
    /**
     * Process and store video uploads
     */
    private function processVideos(array $videos): array
    {
        $paths = [];
        foreach ($videos as $video) {
            $path = $video->store('incident_videos', 'public');
            $paths[] = $path;
        }
        return $paths;
    }
    
    /**
     * Create victim with automatic age category calculation
     */
    private function createVictimForIncident(Incident $incident, array $victimData): Victim
    {
        // Auto-calculate age category
        if (isset($victimData['age'])) {
            $victimData['age_category'] = $this->calculateAgeCategory($victimData['age']);
        }
        
        $victim = $incident->victims()->create($victimData);
        
        // Update incident casualty counts
        $this->updateIncidentCounts($incident, $victimData['medical_status'] ?? 'uninjured');
        
        return $victim;
    }
    
    /**
     * Calculate age category for special care determination
     */
    private function calculateAgeCategory(int $age): string
    {
        return match (true) {
            $age < 13 => 'child',
            $age < 18 => 'teen',
            $age < 60 => 'adult',
            default => 'elderly'
        };
    }
    
    /**
     * Update incident casualty counts
     */
    private function updateIncidentCounts(Incident $incident, string $medicalStatus): void
    {
        $incident->increment('casualty_count');
        
        if (in_array($medicalStatus, ['minor_injury', 'major_injury', 'critical'])) {
            $incident->increment('injury_count');
        }
        
        if ($medicalStatus === 'deceased') {
            $incident->increment('fatality_count');
        }
    }
    
    /**
     * Assign vehicle and update its status
     */
    private function assignVehicle(Incident $incident, int $vehicleId): void
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        $vehicle->update(['status' => 'in_use']);
        
        activity()
            ->performedOn($vehicle)
            ->withProperties([
                'incident_id' => $incident->id,
                'incident_number' => $incident->incident_number
            ])
            ->log('Vehicle assigned to incident');
    }
}
```

---

### **Phase 1C: Form Request Validation**

#### 3.1 Create StoreIncidentRequest
```php
// app/Http/Requests/StoreIncidentRequest.php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }
    
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
            
            // Step 4: Media (Photos required)
            'photos' => 'required|array|min:1|max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'videos' => 'nullable|array|max:2',
            'videos.*' => 'mimetypes:video/mp4,video/webm,video/quicktime|max:10240',
            
            // Step 5: Assignment
            'assigned_staff_id' => 'nullable|exists:users,id',
            'assigned_vehicle_id' => 'nullable|exists:vehicles,id',
        ];
        
        // Conditional rules based on incident type
        $rules = array_merge($rules, $this->getIncidentTypeSpecificRules());
        
        return $rules;
    }
    
    private function getIncidentTypeSpecificRules(): array
    {
        $incidentType = $this->input('incident_type');
        
        return match ($incidentType) {
            'traffic_accident' => [
                'vehicle_involved' => 'required|boolean',
                'vehicle_count' => 'required_if:vehicle_involved,true|nullable|integer|min:1',
                'vehicle_details' => 'required_if:vehicle_involved,true|nullable|string',
                'license_plates' => 'nullable|array',
                'license_plates.*' => 'string|max:20',
            ],
            'medical_emergency' => [
                'medical_emergency_type' => 'required|in:heart_attack,stroke,trauma,respiratory,other',
                'ambulance_requested' => 'required|boolean',
                'patient_count' => 'required|integer|min:1',
            ],
            'fire_incident' => [
                'building_type' => 'required|in:residential,commercial,industrial,government,other',
                'fire_spread_level' => 'required|in:contained,spreading,widespread,controlled',
                'evacuation_required' => 'required|boolean',
                'evacuated_count' => 'required_if:evacuation_required,true|nullable|integer|min:0',
            ],
            'natural_disaster' => [
                'disaster_type' => 'required|in:flood,earthquake,landslide,typhoon,drought,other',
                'affected_area_size' => 'nullable|numeric|min:0',
                'shelter_needed' => 'required|boolean',
                'families_affected' => 'required|integer|min:0',
            ],
            default => [],
        };
    }
    
    public function messages(): array
    {
        return [
            'photos.required' => 'Please upload at least one photo of the incident.',
            'photos.*.max' => 'Each photo must not exceed 2MB.',
            'videos.*.max' => 'Each video must not exceed 10MB.',
            'incident_date.before_or_equal' => 'Incident date cannot be in the future.',
            'description.min' => 'Please provide a detailed description (at least 20 characters).',
        ];
    }
}
```

---

### **Phase 1D: Controller Refactoring**

#### 4.1 Update IncidentController
```php
// app/Http/Controllers/IncidentController.php
public function store(StoreIncidentRequest $request, IncidentService $incidentService)
{
    try {
        $incident = $incidentService->createIncident($request->validated());
        
        return redirect()
            ->route('incidents.show', $incident)
            ->with('success', "Incident {$incident->incident_number} reported successfully!");
    } catch (\Exception $e) {
        return back()
            ->withInput()
            ->with('error', 'Failed to create incident: ' . $e->getMessage());
    }
}
```

---

### **Phase 1E: View Restructuring (Blade Components)**

#### 5.1 Create Blade Component Structure
```
resources/views/
├── Components/
│   └── IncidentForm/
│       ├── BasicInformation.blade.php
│       ├── TrafficAccidentFields.blade.php
│       ├── MedicalEmergencyFields.blade.php
│       ├── FireIncidentFields.blade.php
│       ├── NaturalDisasterFields.blade.php
│       ├── VictimInlineForm.blade.php
│       ├── MediaUpload.blade.php
│       └── AssignmentFields.blade.php
└── Incident/
    ├── create.blade.php (main form)
    ├── show.blade.php (improved)
    └── edit.blade.php (conditional)
```

---

## Conditional Display Strategy (Minimal JavaScript)

### **Server-Side Approach (Recommended)**
```blade
{{-- create.blade.php --}}
<form method="POST" action="{{ route('incidents.store') }}" enctype="multipart/form-data">
    @csrf
    
    {{-- Step 1: Basic Info --}}
    @include('Components.IncidentForm.BasicInformation')
    
    {{-- Step 2: Conditional Fields (Server-side rendered) --}}
    @if(old('incident_type') == 'traffic_accident' || $errors->has('vehicle_*'))
        @include('Components.IncidentForm.TrafficAccidentFields')
    @endif
    
    @if(old('incident_type') == 'medical_emergency')
        @include('Components.IncidentForm.MedicalEmergencyFields')
    @endif
    
    {{-- Step 3: Victims (Dynamic) --}}
    <div id="victims-section">
        @include('Components.IncidentForm.VictimInlineForm')
    </div>
    
    {{-- Step 4: Media --}}
    @include('Components.IncidentForm.MediaUpload')
    
    {{-- Step 5: Assignment --}}
    @include('Components.IncidentForm.AssignmentFields')
    
    <button type="submit" class="btn btn-primary">Submit Incident Report</button>
</form>
```

### **Minimal JavaScript for Dynamic Sections**
```javascript
// Only for incident type change (progressive enhancement)
document.getElementById('incident_type').addEventListener('change', function() {
    // Show/hide relevant sections
    document.querySelectorAll('[data-incident-type]').forEach(section => {
        section.style.display = 'none';
    });
    
    const selectedType = this.value;
    const relevantSection = document.querySelector(`[data-incident-type="${selectedType}"]`);
    if (relevantSection) {
        relevantSection.style.display = 'block';
    }
});
```

---

## Benefits of This Approach

### ✅ **Laravel Best Practices**
1. Service Layer for business logic
2. Form Requests for validation
3. Database Transactions for data integrity
4. Blade Components for reusability
5. Minimal JavaScript (progressive enhancement)

### ✅ **Clean Architecture**
1. Separation of Concerns (MVC)
2. Single Responsibility Principle
3. DRY (Don't Repeat Yourself)
4. Testable code structure

### ✅ **User Experience**
1. Conditional fields based on incident type
2. Integrated victim management
3. Real-time validation feedback
4. Clear step-by-step process

### ✅ **Maintainability**
1. Easy to add new incident types
2. Reusable components
3. Centralized validation rules
4. Clean codebase

---

## Implementation Timeline

### **Week 1: Database & Service Layer**
- Day 1-2: Database migrations
- Day 3-4: IncidentService implementation
- Day 5: Form Request classes

### **Week 2: View Restructuring**
- Day 1-2: Blade components
- Day 3-4: Conditional display logic
- Day 5: Testing & refinement

### **Week 3: Integration & Testing**
- Day 1-2: Controller updates
- Day 3: End-to-end testing
- Day 4: Bug fixes
- Day 5: Documentation

---

## Next Steps

1. ✅ Create database migrations
2. ✅ Implement IncidentService
3. ✅ Create Form Request classes
4. ✅ Build Blade components
5. ✅ Update IncidentController
6. ✅ Test complete flow
7. ✅ Refine UI/UX

---

**Document Version**: 1.0  
**Created**: January 2025  
**Status**: Ready for Implementation

