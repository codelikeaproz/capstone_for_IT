# Database Normalization Implementation Guide

**Project:** MDRRMC Emergency Response System
**Branch:** `claude/database-normalization-01CrWmK9wBKD57H4GPXBTsSF`
**Created:** November 18, 2025
**Status:** Phase 1 - Lookup Tables Complete ✅

---

## 📋 Table of Contents

1. [Executive Summary](#executive-summary)
2. [Current Issues Identified](#current-issues-identified)
3. [Normalization Strategy](#normalization-strategy)
4. [Implementation](#implementation)
5. [Testing & Validation](#testing--validation)
6. [Rollback Plan](#rollback-plan)
7. [Next Steps](#next-steps)

---

## 1. Executive Summary

### **Problem Statement**

The current database design has **normalization issues** that lead to:
- ❌ Hardcoded ENUM values in 20+ locations
- ❌ Municipality strings duplicated across 4 tables
- ❌ No centralized reference data management
- ❌ Difficulty adding new types without migrations
- ❌ No metadata support (icons, colors, descriptions)

### **Solution**

Implement **lookup tables** to normalize ENUMs and reference data:

| Lookup Table | Replaces | Benefit |
|--------------|----------|---------|
| `municipalities` | String field in 4 tables | Single source of truth, metadata support |
| `incident_types` | ENUM with 6 values | Rich metadata, easy to extend |
| `vehicle_types` | ENUM with 7 values | Equipment lists, response mapping |
| `severity_levels` | ENUM with 4 values | Response time targets, priorities |
| `medical_statuses` | ENUM with 5 values | Injury counting logic, care requirements |

### **Benefits**

✅ **Flexibility:** Add new types via seeder, not migration
✅ **Rich Metadata:** Icons, colors, descriptions, priorities
✅ **Consistency:** Single source of truth
✅ **Localization:** Easy to translate
✅ **Backward Compatible:** ENUM columns remain during transition

---

## 2. Current Issues Identified

### **Issue 1: ENUM Proliferation**

**Location:** 5 tables with 20+ ENUM columns

```php
// incidents table
enum('incident_type', [...6 values])
enum('severity_level', [...4 values])
enum('status', [...4 values])
enum('weather_condition', [...5 values])
enum('road_condition', [...5 values])

// vehicles table
enum('vehicle_type', [...7 values])
enum('status', [...4 values])

// victims table
enum('medical_status', [...5 values])
enum('gender', [...3 values])
// ...and 5 more ENUMs

// requests table
enum('request_type', [...6 values])
enum('urgency_level', [...4 values])
enum('status', [...5 values])

// users table
enum('role', [...4 values])
```

**Impact:**
- Cannot add "earthquake" to incident types without migration
- No descriptions or help text for incident types
- Hardcoded in controllers (DashboardController.php:76-78)
- No icon/color metadata for UI

---

### **Issue 2: Municipality Duplication**

**Location:** String field in 4 tables

```sql
incidents.municipality      -- VARCHAR
vehicles.municipality       -- VARCHAR
requests.municipality       -- VARCHAR
users.municipality          -- VARCHAR
```

**Impact:**
- Typo-prone: "Valencia" vs "valencia" vs "Valencia City"
- No centralized list
- No coordinates, population, contact info
- No validation

---

### **Issue 3: Derived Data Redundancy**

**Location:** incidents table

```php
'casualty_count' => stored in column
'injury_count' => stored in column
'fatality_count' => stored in column

// But these can be calculated from victims table:
COUNT(*) WHERE incident_id = X
COUNT(*) WHERE medical_status IN ('minor_injury', 'major_injury', 'critical')
COUNT(*) WHERE medical_status = 'deceased'
```

**Impact:**
- Data sync issues (count doesn't match victims)
- Violates 3NF (Third Normal Form)

---

## 3. Normalization Strategy

### **Phase 1: Lookup Tables (IMPLEMENTED ✅)**

**Goal:** Create normalized reference tables

**Migrations Created:**
1. ✅ `2025_11_19_001744_create_municipalities_table.php`
2. ✅ `2025_11_19_001748_create_incident_types_table.php`
3. ✅ `2025_11_19_001752_create_vehicle_types_table.php`
4. ✅ `2025_11_19_001756_create_severity_levels_table.php`
5. ✅ `2025_11_19_001800_create_medical_statuses_table.php`

**Seeders Created:**
1. ✅ `MunicipalitySeeder.php` (13 municipalities in Bukidnon)
2. ✅ `IncidentTypeSeeder.php` (6 types with metadata)
3. ✅ `VehicleTypeSeeder.php` (7 types with equipment lists)
4. ✅ `SeverityLevelSeeder.php` (4 levels with response times)
5. ✅ `MedicalStatusSeeder.php` (5 statuses with care requirements)

---

### **Phase 2: Foreign Key Columns (PENDING)**

**Goal:** Add new columns alongside existing ENUMs

**Example Migration:**
```php
Schema::table('incidents', function (Blueprint $table) {
    // Add foreign key columns (nullable for safety)
    $table->foreignId('incident_type_id')->nullable()
          ->constrained('incident_types')
          ->onDelete('restrict');

    $table->foreignId('severity_level_id')->nullable()
          ->constrained('severity_levels')
          ->onDelete('restrict');

    $table->foreignId('municipality_id')->nullable()
          ->constrained('municipalities')
          ->onDelete('restrict');
});
```

**Strategy:**
- Add alongside existing ENUM (don't drop yet)
- Make nullable initially
- Create indexes for performance

---

### **Phase 3: Data Backfill (PENDING)**

**Goal:** Populate foreign keys from existing ENUM values

**Example Backfill Migration:**
```php
public function up(): void
{
    DB::transaction(function () {
        // Map incident_type ENUM to incident_type_id
        $types = DB::table('incident_types')->get();
        foreach ($types as $type) {
            DB::table('incidents')
              ->where('incident_type', $type->code)
              ->update(['incident_type_id' => $type->id]);
        }

        // Validate: check for unmapped records
        $unmapped = DB::table('incidents')
                      ->whereNull('incident_type_id')
                      ->count();

        if ($unmapped > 0) {
            throw new \Exception("Found {$unmapped} incidents with unmapped types");
        }
    });
}
```

---

### **Phase 4: Model Updates (PENDING)**

**Goal:** Add relationships and backward-compatible accessors

**Example (Incident Model):**
```php
// app/Models/Incident.php

// NEW: Add relationship
public function incidentType(): BelongsTo
{
    return $this->belongsTo(IncidentType::class);
}

public function severityLevel(): BelongsTo
{
    return $this->belongsTo(SeverityLevel::class);
}

public function municipalityRelation(): BelongsTo
{
    return $this->belongsTo(Municipality::class, 'municipality_id');
}

// BACKWARD COMPATIBLE: Accessor for old ENUM column
public function getIncidentTypeAttribute($value)
{
    // If new relationship exists, use it
    if ($this->relationLoaded('incidentType') && $this->incidentType) {
        return $this->incidentType->code;
    }

    // Otherwise, return old ENUM value
    return $value;
}

// NEW: Rich metadata accessor
public function getIncidentTypeDataAttribute()
{
    return $this->incidentType; // Returns full object with icon, color, description
}
```

**Create New Models:**
```bash
php artisan make:model Municipality
php artisan make:model IncidentType
php artisan make:model VehicleType
php artisan make:model SeverityLevel
php artisan make:model MedicalStatus
```

---

### **Phase 5: Controller Updates (PENDING)**

**Goal:** Use lookup tables instead of hardcoded arrays

**Before (DashboardController.php:76-78):**
```php
$incidentTypes = ['traffic_accident', 'medical_emergency', 'fire_incident', 'natural_disaster', 'criminal_activity', 'other'];
$severityLevels = ['critical', 'high', 'medium', 'low'];
$vehicleTypes = ['ambulance', 'fire_truck', 'rescue_vehicle', 'patrol_car', 'support_vehicle'];
```

**After:**
```php
$incidentTypes = IncidentType::active()->orderBy('sort_order')->get();
$severityLevels = SeverityLevel::active()->orderBy('priority_level')->get();
$vehicleTypes = VehicleType::active()->orderBy('sort_order')->get();
```

**Benefits:**
- Dynamic lists (add new types without code changes)
- Access to metadata (icons, colors, descriptions)
- Can filter by `is_active` status

---

## 4. Implementation

### **Step 1: Run Migrations & Seeders**

```bash
# Run lookup table migrations
php artisan migrate

# Run seeders
php artisan db:seed --class=MunicipalitySeeder
php artisan db:seed --class=IncidentTypeSeeder
php artisan db:seed --class=VehicleTypeSeeder
php artisan db:seed --class=SeverityLevelSeeder
php artisan db:seed --class=MedicalStatusSeeder
```

**Expected Output:**
```
✅ municipalities: 13 records inserted
✅ incident_types: 6 records inserted
✅ vehicle_types: 7 records inserted
✅ severity_levels: 4 records inserted
✅ medical_statuses: 5 records inserted
```

---

### **Step 2: Verify Data**

```sql
-- Check municipalities
SELECT id, name, province, zip_code, population FROM municipalities ORDER BY name;

-- Check incident types with metadata
SELECT code, name, icon, color, requires_vehicle FROM incident_types ORDER BY sort_order;

-- Check vehicle types with equipment
SELECT code, name, typical_capacity, typical_fuel_capacity FROM vehicle_types ORDER BY sort_order;

-- Check severity levels with response times
SELECT code, name, priority_level, response_time_minutes, color FROM severity_levels ORDER BY priority_level;

-- Check medical statuses
SELECT code, name, requires_hospitalization, is_fatality FROM medical_statuses ORDER BY severity_level;
```

---

### **Step 3: Create Foreign Key Migration**

```bash
php artisan make:migration add_lookup_foreign_keys_to_existing_tables
```

**Migration Content:**
```php
public function up(): void
{
    // Add foreign keys to incidents table
    Schema::table('incidents', function (Blueprint $table) {
        $table->foreignId('incident_type_id')->nullable()
              ->after('incident_type')
              ->constrained('incident_types')->onDelete('restrict');

        $table->foreignId('severity_level_id')->nullable()
              ->after('severity_level')
              ->constrained('severity_levels')->onDelete('restrict');

        $table->foreignId('municipality_id')->nullable()
              ->after('municipality')
              ->constrained('municipalities')->onDelete('restrict');
    });

    // Add foreign keys to vehicles table
    Schema::table('vehicles', function (Blueprint $table) {
        $table->foreignId('vehicle_type_id')->nullable()
              ->after('vehicle_type')
              ->constrained('vehicle_types')->onDelete('restrict');

        $table->foreignId('municipality_id')->nullable()
              ->after('municipality')
              ->constrained('municipalities')->onDelete('restrict');
    });

    // Add foreign keys to victims table
    Schema::table('victims', function (Blueprint $table) {
        $table->foreignId('medical_status_id')->nullable()
              ->after('medical_status')
              ->constrained('medical_statuses')->onDelete('restrict');
    });

    // Add foreign keys to requests table
    Schema::table('requests', function (Blueprint $table) {
        $table->foreignId('municipality_id')->nullable()
              ->after('municipality')
              ->constrained('municipalities')->onDelete('restrict');
    });

    // Add foreign keys to users table
    Schema::table('users', function (Blueprint $table) {
        $table->foreignId('municipality_id')->nullable()
              ->after('municipality')
              ->constrained('municipalities')->onDelete('restrict');
    });
}
```

---

### **Step 4: Create Backfill Migration**

```bash
php artisan make:migration backfill_lookup_foreign_keys
```

**Migration Content:**
```php
public function up(): void
{
    DB::transaction(function () {
        // Backfill incident_type_id
        $this->backfillColumn(
            'incidents',
            'incident_type',
            'incident_type_id',
            'incident_types',
            'code'
        );

        // Backfill severity_level_id
        $this->backfillColumn(
            'incidents',
            'severity_level',
            'severity_level_id',
            'severity_levels',
            'code'
        );

        // Backfill municipalities
        $this->backfillMunicipalities();

        // Validate
        $this->validateBackfill();
    });
}

private function backfillColumn($table, $sourceColumn, $targetColumn, $lookupTable, $lookupColumn)
{
    $lookupData = DB::table($lookupTable)->get()->keyBy($lookupColumn);

    DB::table($table)->orderBy('id')->chunk(100, function ($records) use ($sourceColumn, $targetColumn, $lookupData) {
        foreach ($records as $record) {
            $sourceValue = $record->$sourceColumn;
            if ($sourceValue && isset($lookupData[$sourceValue])) {
                DB::table($this->table)
                  ->where('id', $record->id)
                  ->update([$targetColumn => $lookupData[$sourceValue]->id]);
            }
        }
    });
}

private function validateBackfill()
{
    $issues = [];

    // Check for unmapped incidents
    $unmappedIncidents = DB::table('incidents')
                           ->whereNotNull('incident_type')
                           ->whereNull('incident_type_id')
                           ->count();
    if ($unmappedIncidents > 0) {
        $issues[] = "Found {$unmappedIncidents} incidents with unmapped incident_type";
    }

    if (!empty($issues)) {
        throw new \Exception("Backfill validation failed:\n" . implode("\n", $issues));
    }
}
```

---

## 5. Testing & Validation

### **Test Checklist**

```bash
# 1. Verify lookup data
php artisan tinker
>>> App\Models\IncidentType::count(); // Should be 6
>>> App\Models\Municipality::count(); // Should be 13

# 2. Test relationships
>>> $incident = App\Models\Incident::with('incidentType', 'severityLevel')->first();
>>> $incident->incidentType->name; // "Traffic Accident"
>>> $incident->incidentType->icon; // "fas fa-car-crash"

# 3. Test backward compatibility
>>> $incident->incident_type; // Still returns "traffic_accident" (old ENUM)

# 4. Test metadata access
>>> $incident->incidentTypeData; // Full object with metadata

# 5. Validate data integrity
SELECT
    COUNT(*) as total,
    COUNT(incident_type_id) as mapped,
    COUNT(*) - COUNT(incident_type_id) as unmapped
FROM incidents;
```

---

## 6. Rollback Plan

### **If Something Goes Wrong:**

```bash
# 1. Rollback last migration
php artisan migrate:rollback

# 2. Rollback specific batch
php artisan migrate:rollback --batch=X

# 3. Check migration status
php artisan migrate:status

# 4. Emergency: Reset all lookup tables (DESTRUCTIVE!)
php artisan migrate:reset --path=/database/migrations/2025_11_19_*.php
```

### **Safe Rollback (Keep ENUM columns)**

The ENUM columns are NOT dropped during transition, so you can always:
1. Remove foreign key columns
2. Continue using old ENUM values
3. Keep lookup tables for future use

---

## 7. Next Steps

### **Immediate (This Session)**

- [ ] Create Models for lookup tables
- [ ] Add foreign key migration
- [ ] Create backfill migration
- [ ] Update Incident model with relationships

### **Near Future (Next Session)**

- [ ] Update all models (Vehicle, Victim, Request, User)
- [ ] Update controllers to use lookup tables
- [ ] Update views to display metadata (icons, colors)
- [ ] Create admin panel to manage lookup tables

### **Long Term**

- [ ] Add barangays lookup table
- [ ] Add hospitals lookup table
- [ ] File management normalization (polymorphic `files` table)
- [ ] Remove derived data columns (casualty_count, etc.)
- [ ] Drop old ENUM columns (after 3 months of successful operation)

---

## 📊 Progress Summary

| Task | Status | Files Changed |
|------|--------|---------------|
| Database audit | ✅ Complete | audit_database.php, database_audit.sql |
| Lookup migrations | ✅ Complete | 5 migration files |
| Lookup seeders | ✅ Complete | 5 seeder files |
| Documentation | ✅ Complete | This file |
| Foreign keys | ⏳ Pending | - |
| Backfill | ⏳ Pending | - |
| Models | ⏳ Pending | - |
| Controllers | ⏳ Pending | - |

---

## 🎯 Expected Outcomes

**Before Normalization:**
```php
// Hardcoded array
$types = ['traffic_accident', 'medical_emergency', ...];

// No metadata
<option value="traffic_accident">Traffic Accident</option>
```

**After Normalization:**
```php
// Dynamic from database
$types = IncidentType::active()->get();

// Rich metadata
@foreach($types as $type)
    <option value="{{ $type->id }}" data-icon="{{ $type->icon }}" data-color="{{ $type->color }}">
        <i class="{{ $type->icon }}"></i>
        {{ $type->name }}
        <span class="text-gray-500">{{ $type->description }}</span>
    </option>
@endforeach
```

**Impact:**
- ✅ Add new incident type via seeder (no migration)
- ✅ Display icons in dropdowns
- ✅ Show descriptions as tooltips
- ✅ Filter by active status
- ✅ Localize by adding `name_local` column

---

**End of Guide** | **Branch:** `claude/database-normalization-01CrWmK9wBKD57H4GPXBTsSF`
