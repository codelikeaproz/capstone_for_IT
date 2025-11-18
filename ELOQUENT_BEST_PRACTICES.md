# Eloquent ORM Best Practices - Implementation Guide

**Project:** MDRRMC Emergency Response System
**Branch:** `claude/database-normalization-01CrWmK9wBKD57H4GPXBTsSF`
**Created:** November 18, 2025
**Models:** Municipality, IncidentType, VehicleType, SeverityLevel, MedicalStatus

---

## 📖 Table of Contents

1. [Overview](#overview)
2. [Model Architecture](#model-architecture)
3. [Best Practices Implemented](#best-practices-implemented)
4. [Code Examples](#code-examples)
5. [Usage Patterns](#usage-patterns)
6. [Performance Optimization](#performance-optimization)

---

## 1. Overview

### **Why Eloquent?**

Eloquent is Laravel's powerful ActiveRecord ORM implementation. These models follow **Laravel 12 best practices** for:

- ✅ Type safety with PHP 8.2+ features
- ✅ Clean, expressive syntax
- ✅ Relationship management
- ✅ Query optimization
- ✅ Business logic encapsulation

### **Models Created**

| Model | Purpose | Lines | Relationships |
|-------|---------|-------|---------------|
| **Municipality** | Geographic data, distance calculations | 277 | 4 hasMany |
| **IncidentType** | Incident classification with metadata | 299 | 1 hasMany |
| **VehicleType** | Vehicle classification, equipment | 123 | 1 hasMany |
| **SeverityLevel** | Priority levels, response times | 155 | 1 hasMany |
| **MedicalStatus** | Medical care requirements | 167 | 1 hasMany |

---

## 2. Model Architecture

### **Standard Structure**

Every model follows this consistent structure:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * PHPDoc Block
 *
 * @property int $id
 * @property string $name
 * ...
 */
class ModelName extends Model
{
    use HasFactory;

    // 1. Mass Assignment Protection
    protected $fillable = [...];

    // 2. Type Casting
    protected $casts = [...];

    // 3. Appended Accessors
    protected $appends = [...];

    // 4. Relationships
    public function relatedModel(): HasMany { ... }

    // 5. Query Scopes
    public function scopeActive(Builder $query): Builder { ... }

    // 6. Accessors & Mutators
    public function getFormattedAttribute(): string { ... }

    // 7. Helper Methods
    public function getStatistics(): array { ... }
}
```

---

## 3. Best Practices Implemented

### **3.1 PHPDoc Type Hints**

**Why:** IDE autocomplete, static analysis, better DX

```php
/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon $created_at
 */
class IncidentType extends Model
```

**Benefits:**
- PHPStan/Psalm static analysis
- IDE autocomplete in VS Code/PhpStorm
- Self-documenting code

---

### **3.2 Explicit Fillable Properties**

**Why:** Mass assignment protection, security

```php
protected $fillable = [
    'code',
    'name',
    'description',
    'icon',
    'color',
    'is_active',
];
```

**Security:**
```php
// SAFE: Only fillable attributes are set
IncidentType::create($request->all());

// PROTECTED: Cannot mass-assign 'id' or other non-fillable fields
```

---

### **3.3 Type Casting**

**Why:** Data integrity, type safety, automatic JSON handling

```php
protected $casts = [
    'requires_vehicle' => 'boolean',
    'priority_level' => 'integer',
    'typical_equipment' => 'array',  // Auto JSON encode/decode
    'latitude' => 'decimal:8',
    'is_active' => 'boolean',
];
```

**Automatic Conversions:**
```php
$type = IncidentType::find(1);

// Database: "1" (string) → PHP: true (boolean)
$type->requires_vehicle; // true

// Database: JSON string → PHP: array
$vehicleType->typical_equipment; // ['Stretcher', 'First Aid Kit']
```

---

### **3.4 Query Scopes**

**Why:** Reusable query logic, cleaner controllers

```php
// Scope definition
public function scopeActive(Builder $query): Builder
{
    return $query->where('is_active', true);
}

public function scopeOrdered(Builder $query): Builder
{
    return $query->orderBy('sort_order')->orderBy('name');
}
```

**Usage:**
```php
// Chainable, readable queries
$types = IncidentType::active()->ordered()->get();

// Combine multiple scopes
$critical = IncidentType::active()
    ->highPriority()
    ->requiresMedicalResponse()
    ->get();
```

**All Scopes Implemented:**
- `active()` - Only active records
- `ordered()` - Sort by sort_order/name
- `byCode($code)` - Find by code
- `highPriority()` - Priority level ≤ 2
- `requiresVehicle()` - Needs vehicle response
- `fatalities()` - Medical status fatalities
- `injuries()` - Medical status injuries

---

### **3.5 Relationships with Type Hints**

**Why:** Strong typing, eager loading, query optimization

```php
// HasMany relationship
public function incidents(): HasMany
{
    return $this->hasMany(Incident::class, 'incident_type_id');
}

// BelongsTo relationship (in Incident model)
public function incidentType(): BelongsTo
{
    return $this->belongsTo(IncidentType::class);
}
```

**Usage:**
```php
// Lazy loading (N+1 query problem)
foreach ($incidents as $incident) {
    echo $incident->incidentType->name; // Query per incident!
}

// Eager loading (optimized)
$incidents = Incident::with('incidentType')->get();
foreach ($incidents as $incident) {
    echo $incident->incidentType->name; // No extra query!
}
```

---

### **3.6 Accessors for Computed Properties**

**Why:** Clean presentation logic, encapsulation

```php
// Accessor definition
public function getBadgeColorAttribute(): string
{
    return match($this->color) {
        'red' => 'badge-error',
        'orange' => 'badge-warning',
        'green' => 'badge-success',
        default => 'badge-neutral',
    };
}

// Appended to array/JSON
protected $appends = ['badge_color', 'icon_html'];
```

**Usage:**
```php
$type = IncidentType::find(1);

// Accessor called automatically
$type->badge_color; // "badge-error"

// In JSON API responses
return response()->json($type);
// { "id": 1, "name": "...", "badge_color": "badge-error" }
```

**Accessors Implemented:**
- `badge_color` - DaisyUI badge class
- `icon_html` - FontAwesome icon HTML
- `text_color` - Tailwind text color class
- `formatted_population` - Number format with commas
- `has_coordinates` - Boolean check for lat/lng
- `care_level` - Medical care requirement level

---

### **3.7 Helper Methods for Business Logic**

**Why:** Keep controllers thin, encapsulate domain logic

```php
// Statistics aggregation
public function getStatistics(): array
{
    return [
        'total_incidents' => $this->incidents()->count(),
        'active_incidents' => $this->incidents()->active()->count(),
        'resolved_incidents' => $this->incidents()->resolved()->count(),
    ];
}

// Business logic checks
public function isEmergency(): bool
{
    return $this->priority_level <= 2;
}

// Complex calculations
public function distanceTo(Municipality $other): ?float
{
    // Haversine formula implementation
    // ...
    return $distance;
}
```

**Usage:**
```php
$type = IncidentType::find(1);

// Clean, expressive
if ($type->isEmergency()) {
    // Send notifications
}

// Get stats
$stats = $type->getStatistics();
// ['total_incidents' => 45, 'active_incidents' => 3, ...]
```

---

### **3.8 Static Factory Methods**

**Why:** Cleaner code, common patterns

```php
// For dropdowns
public static function getForSelect(): array
{
    return static::active()
        ->ordered()
        ->pluck('name', 'id')
        ->toArray();
}

// Find by code instead of ID
public static function findByCode(string $code): ?static
{
    return static::byCode($code)->first();
}
```

**Usage:**
```php
// In controller
public function create()
{
    return view('incidents.create', [
        'types' => IncidentType::getForSelect(),
        // [1 => 'Traffic Accident', 2 => 'Medical Emergency', ...]
    ]);
}

// In Blade
<select name="incident_type_id">
    @foreach($types as $id => $name)
        <option value="{{ $id }}">{{ $name }}</option>
    @endforeach
</select>

// Find by code
$type = IncidentType::findByCode('traffic_accident');
```

---

## 4. Code Examples

### **Example 1: Municipality with Distance Calculation**

```php
$valencia = Municipality::findByCode('Valencia City');
$malaybalay = Municipality::findByCode('Malaybalay City');

// Haversine distance calculation
$distance = $valencia->distanceTo($malaybalay);
echo "Distance: {$distance} km"; // Distance: 32.5 km

// Get coordinates for mapping
$coords = $valencia->getCoordinatesArray();
// ['lat' => 7.9089, 'lng' => 125.0942]

// Statistics
$stats = $valencia->getStatistics();
// [
//     'total_incidents' => 123,
//     'active_incidents' => 5,
//     'total_vehicles' => 12,
//     'available_vehicles' => 8,
//     ...
// ]
```

---

### **Example 2: IncidentType with Recommendations**

```php
$type = IncidentType::findByCode('medical_emergency');

// Check requirements
if ($type->requires_medical_response) {
    $recommendedVehicles = $type->getRecommendedVehicles();
    // Returns: Ambulance, TRAVIZ (from response_types JSON)
}

// UI metadata
echo $type->icon_html; // <i class="fas fa-heartbeat"></i>
echo $type->badge_color; // "badge-error"
echo $type->text_color; // "text-red-600"

// Statistics
$stats = $type->getStatistics();
// [
//     'total_incidents' => 45,
//     'active_incidents' => 3,
//     'critical_incidents' => 2,
// ]
```

---

### **Example 3: VehicleType with Equipment Lists**

```php
$ambulance = VehicleType::findByCode('ambulance');

// Access JSON casted array
foreach ($ambulance->typical_equipment as $equipment) {
    echo "- {$equipment}\n";
}
// - Stretcher
// - First Aid Kit
// - Oxygen Tank
// - Defibrillator

// Check if can respond to incident type
if ($ambulance->canRespondTo('medical_emergency')) {
    // Dispatch ambulance
}

// Get all ambulances in fleet
$vehicles = $ambulance->vehicles()
    ->available()
    ->where('municipality', 'Valencia City')
    ->get();
```

---

### **Example 4: SeverityLevel with Response Times**

```php
$critical = SeverityLevel::findByCode('critical');

echo $critical->formatted_response_time; // "5 minutes"
echo $critical->urgency_label; // "URGENT"

if ($critical->requires_immediate_notification) {
    // Send SMS/Email to supervisors
}

if ($critical->requires_supervisor_approval) {
    // Require approval before closing
}

// Get all critical incidents
$incidents = $critical->incidents()
    ->active()
    ->with('municipality', 'incidentType')
    ->get();
```

---

### **Example 5: MedicalStatus with Care Requirements**

```php
$status = MedicalStatus::findByCode('major_injury');

echo $status->care_level; // "Hospital Care"

if ($status->requires_ambulance) {
    // Dispatch ambulance
}

if ($status->requires_hospitalization) {
    $hospitals = Hospital::nearestTo($incident->municipality)->get();
}

// Get injury count (excluding deceased and uninjured)
$injuryCount = MedicalStatus::injuries()
    ->get()
    ->sum(fn($status) => $status->victims()->count());

// Get fatality count
$fatalityCount = MedicalStatus::fatalities()
    ->first()
    ->victims()
    ->count();
```

---

## 5. Usage Patterns

### **Pattern 1: Controller Method**

```php
// app/Http/Controllers/IncidentController.php

public function create()
{
    return view('incidents.create', [
        'types' => IncidentType::getForSelect(),
        'severities' => SeverityLevel::getForSelect(),
        'municipalities' => Municipality::active()->ordered()->get(),
    ]);
}

public function store(Request $request)
{
    $validated = $request->validate([
        'incident_type_id' => 'required|exists:incident_types,id',
        'severity_level_id' => 'required|exists:severity_levels,id',
        'municipality_id' => 'required|exists:municipalities,id',
        // ...
    ]);

    $incident = Incident::create($validated);

    // Check if notification needed
    if ($incident->severityLevel->requires_immediate_notification) {
        NotificationService::sendEmergencyAlert($incident);
    }

    return redirect()->route('incidents.show', $incident);
}
```

---

### **Pattern 2: Blade View**

```blade
{{-- resources/views/incidents/create.blade.php --}}

<select name="incident_type_id" class="select select-bordered">
    @foreach($types as $id => $name)
        <option value="{{ $id }}">{{ $name }}</option>
    @endforeach
</select>

{{-- With metadata --}}
@foreach(IncidentType::active()->ordered()->get() as $type)
    <div class="badge {{ $type->badge_color }}">
        {!! $type->icon_html !!}
        {{ $type->name }}
    </div>
@endforeach

{{-- Municipality with stats --}}
@foreach($municipalities as $municipality)
    <div>
        <h3>{{ $municipality->display_name }}</h3>
        <p>Population: {{ $municipality->formatted_population }}</p>
        <p>Active Incidents: {{ $municipality->incidents()->active()->count() }}</p>
    </div>
@endforeach
```

---

### **Pattern 3: API Resource**

```php
// app/Http/Resources/IncidentResource.php

public function toArray($request)
{
    return [
        'id' => $this->id,
        'incident_number' => $this->incident_number,
        'type' => [
            'id' => $this->incidentType->id,
            'name' => $this->incidentType->name,
            'icon' => $this->incidentType->icon,
            'color' => $this->incidentType->color,
            'badge_class' => $this->incidentType->badge_color,
        ],
        'severity' => [
            'id' => $this->severityLevel->id,
            'name' => $this->severityLevel->name,
            'urgency' => $this->severityLevel->urgency_label,
            'response_time' => $this->severityLevel->formatted_response_time,
        ],
        'municipality' => [
            'id' => $this->municipality->id,
            'name' => $this->municipality->name,
            'coordinates' => $this->municipality->coordinates_array,
        ],
        // ...
    ];
}
```

---

## 6. Performance Optimization

### **6.1 Eager Loading**

```php
// BAD: N+1 Query Problem
$incidents = Incident::all();
foreach ($incidents as $incident) {
    echo $incident->incidentType->name; // Query per incident!
    echo $incident->municipality->name; // Query per incident!
}

// GOOD: Eager Loading
$incidents = Incident::with([
    'incidentType',
    'severityLevel',
    'municipality',
])->get();
foreach ($incidents as $incident) {
    echo $incident->incidentType->name; // No extra query
    echo $incident->municipality->name; // No extra query
}
```

---

### **6.2 Selective Loading**

```php
// Only load what you need
$types = IncidentType::select('id', 'name', 'icon', 'color')
    ->active()
    ->ordered()
    ->get();

// Instead of:
$types = IncidentType::all(); // Loads ALL columns
```

---

### **6.3 Caching**

```php
// Cache dropdown options
$types = Cache::remember('incident_types_select', 3600, function () {
    return IncidentType::getForSelect();
});

// Cache statistics
$stats = Cache::remember("municipality_{$id}_stats", 600, function () use ($municipality) {
    return $municipality->getStatistics();
});
```

---

## 📊 Summary

### **Benefits Achieved:**

| Benefit | Implementation |
|---------|----------------|
| **Type Safety** | PHPDoc + casts + return types |
| **Code Reuse** | Scopes, accessors, helper methods |
| **Clean Controllers** | Business logic in models |
| **Better UX** | Rich metadata (icons, colors) |
| **Performance** | Eager loading, caching patterns |
| **Maintainability** | Consistent structure, self-documenting |

### **Next Steps:**

1. Update existing `Incident`, `Vehicle`, `Victim`, `Request` models
2. Add relationships to lookup tables
3. Create backward-compatible accessors
4. Update controllers to use scopes
5. Update views to use rich metadata

---

**Branch:** `claude/database-normalization-01CrWmK9wBKD57H4GPXBTsSF`
**Commits:** 2 (Migrations + Models)
**Models:** 5 comprehensive Eloquent models
**Status:** ✅ Ready for Phase 2 (Foreign Keys + Backfill)
