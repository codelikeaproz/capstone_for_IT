# Database Normalization Plan
## BukidnonAlert Emergency Response System

**Branch:** `feature/normalize`
**Created:** November 19, 2025
**Status:** Planning Phase
**Priority:** HIGH - Major Refactoring

---

## 📋 Table of Contents

1. [Executive Summary](#executive-summary)
2. [Current vs Target Schema Analysis](#current-vs-target-schema-analysis)
3. [Naming Convention Strategy](#naming-convention-strategy)
4. [Database Normalization Goals](#database-normalization-goals)
5. [Gap Analysis](#gap-analysis)
6. [Implementation Strategy](#implementation-strategy)
7. [Migration Plan](#migration-plan)
8. [Model Relationship Updates](#model-relationship-updates)
9. [Risk Assessment](#risk-assessment)
10. [Testing Strategy](#testing-strategy)
11. [Rollback Plan](#rollback-plan)

---

## 📊 Executive Summary

### Objective
Normalize the database structure to match the approved schema design (`public/global/db_schema.png`), ensuring proper relationships, naming conventions, and Laravel best practices.

### Scope
- **17 new tables** to be created
- **5 existing tables** to be refactored
- **All models** to be updated with proper relationships
- **All migrations** to follow consistent naming conventions
- **Zero data loss** - migration with data preservation

### Timeline
- **Phase 1:** Planning & Analysis (Current)
- **Phase 2:** Create New Tables (1-2 days)
- **Phase 3:** Add Relationships (1 day)
- **Phase 4:** Model Updates (1 day)
- **Phase 5:** Testing & Validation (1-2 days)
- **Total:** 4-6 days

---

## 🔍 Current vs Target Schema Analysis

### Current Database Structure (Existing)

**Core Tables:**
1. ✅ `users` (id, role enum, municipality string)
2. ✅ `incidents` (id, multiple type-specific columns)
3. ✅ `vehicles` (id)
4. ✅ `victims` (id, incident_id FK)
5. ✅ `requests` (id, incident_case_number string)
6. ✅ `activity_logs` (Spatie package)
7. ✅ `login_attempts`
8. ✅ `vehicle_utilizations` (partial implementation)

**Issues:**
- ❌ No `account_roles` table (roles hardcoded as enum)
- ❌ No `hospitals` table
- ❌ No `hospital_referrals` table
- ❌ No `reports` table
- ❌ No `reports_incident` pivot table
- ❌ No `vehicle_dispatched` table
- ❌ No `fuel_consumption` table
- ❌ No `dispatched_responders` table
- ❌ No `responders` table
- ❌ No `report_requests` table
- ❌ No `feedback` table
- ❌ Municipality stored as string (not normalized)
- ❌ Primary keys named `id` (Laravel convention) vs `{table}_id` (schema convention)
- ❌ Inconsistent foreign key naming

---

### Target Database Structure (From Schema)

**Tables to Create:**

| Table Name | Primary Key | Purpose |
|------------|-------------|---------|
| `account_roles` | `role_id` | User role definitions |
| `logs` | `log_id` | System activity logs |
| `hospitals` | `hospital_id` | Hospital directory |
| `hospital_referrals` | `referral_id` | Victim hospital referrals |
| `reports` | `report_id` | Report documents |
| `reports_incident` | `report_incident_id` | Report-Incident pivot |
| `vehicle_dispatched` | `dispatch_id` | Vehicle dispatch records |
| `fuel_consumption` | `consumption_id` | Fuel tracking |
| `dispatched_responders` | `assignment_id` | Responder assignments |
| `responders` | `responder_id` | Responder directory |
| `report_requests` | `request_id` | Report requests from citizens |
| `feedback` | `feedback_id` | Feedback on requests |

**Tables to Refactor:**

| Table Name | Changes Needed |
|------------|----------------|
| `users` | Add `role_id` FK, remove `role` enum |
| `incidents` | Rename to `incident_list` (optional), add proper relationships |
| `requests` | Add `incident_id` FK instead of string reference |
| `victims` | Add relationships to `hospital_referrals` |
| `vehicles` | Remove circular FK `current_incident_id` |

---

## 🎯 Naming Convention Strategy

### Decision: **Hybrid Approach** (Best of Both Worlds)

#### Primary Keys
**Keep Laravel Convention:**
- Use `id` as primary key name (Laravel standard)
- Use `bigIncrements('id')` or `id()` in migrations
- Maintains compatibility with Laravel's ecosystem

**Rationale:**
- Laravel Eloquent expects `id` by default
- Third-party packages expect `id`
- Less configuration needed in models
- Industry standard for Laravel applications

#### Foreign Keys
**Use Descriptive Names:**
- Format: `{related_table_singular}_id`
- Examples: `user_id`, `incident_id`, `vehicle_id`, `role_id`

#### Table Names
**Keep Laravel Convention:**
- Use plural, snake_case table names
- Examples: `users`, `incidents`, `hospital_referrals`

#### Column Names
**Use snake_case:**
- Examples: `first_name`, `incident_type`, `severity_level`

---

## 📐 Database Normalization Goals

### 1. First Normal Form (1NF) ✅
**Current Status:** Mostly compliant

**Issues to Fix:**
- ❌ JSON columns (`photos`, `videos`, `documents`, `license_plates`, `equipment_list`)
- ✅ Solution: Keep JSON for flexibility, but add dedicated tables where needed

### 2. Second Normal Form (2NF) ⚠️
**Current Status:** Partially compliant

**Issues to Fix:**
- ❌ `municipality` stored as string in multiple tables
- ❌ `role` enum instead of separate table
- ❌ Incident type-specific fields mixed in single table

**Solution:**
- ✅ Create `account_roles` table
- ✅ Create `municipalities` table (optional, for future)
- ✅ Keep incident type fields (acceptable for Laravel polymorphism)

### 3. Third Normal Form (3NF) ⚠️
**Current Status:** Needs improvement

**Issues to Fix:**
- ❌ `requests` table references incidents by string
- ❌ No proper report management system
- ❌ Vehicle dispatch not tracked properly

**Solution:**
- ✅ Add proper foreign keys
- ✅ Create missing tables for relationships

---

## 🔎 Gap Analysis

### Missing Tables (CRITICAL)

#### 1. `account_roles` Table
**Purpose:** Normalize user roles

**Schema:**
```sql
CREATE TABLE account_roles (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description TEXT,
    permissions JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Data to Seed:**
- superadmin (System Administrator)
- admin (Municipality Administrator)
- staff (Staff Member)
- responder (Emergency Responder)
- citizen (Citizen User)

---

#### 2. `hospitals` Table
**Purpose:** Hospital directory for referrals

**Schema:**
```sql
CREATE TABLE hospitals (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    hospital_name VARCHAR(255) NOT NULL,
    hospital_type ENUM('government', 'private', 'rural_health_unit', 'barangay_health_station'),
    contact_number VARCHAR(20),
    email VARCHAR(255),
    address TEXT NOT NULL,
    municipality VARCHAR(100),
    barangay VARCHAR(100),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    bed_capacity INT,
    emergency_room BOOLEAN DEFAULT true,
    trauma_center BOOLEAN DEFAULT false,
    operating_hours VARCHAR(100),
    services_offered JSON,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

#### 3. `hospital_referrals` Table
**Purpose:** Track victim hospital referrals

**Schema:**
```sql
CREATE TABLE hospital_referrals (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    victim_id BIGINT NOT NULL,
    hospital_id BIGINT NOT NULL,
    referral_type ENUM('initial', 'transfer', 'follow_up') DEFAULT 'initial',
    referral_reason TEXT,
    referral_date DATETIME NOT NULL,
    arrival_time DATETIME,
    admission_status ENUM('admitted', 'treated_released', 'transferred', 'pending'),
    medical_notes TEXT,
    referred_by BIGINT, -- user_id of referring personnel
    transportation_method ENUM('ambulance', 'private_vehicle', 'helicopter', 'other'),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (victim_id) REFERENCES victims(id) ON DELETE CASCADE,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE RESTRICT,
    FOREIGN KEY (referred_by) REFERENCES users(id) ON DELETE SET NULL
);
```

---

#### 4. `reports` Table
**Purpose:** Store generated report documents

**Schema:**
```sql
CREATE TABLE reports (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    report_title VARCHAR(255) NOT NULL,
    report_type ENUM('incident_report', 'monthly_summary', 'vehicle_utilization', 'custom'),
    report_content LONGTEXT,
    file_path VARCHAR(500),
    generated_by BIGINT,
    generated_at DATETIME NOT NULL,
    date_range_start DATE,
    date_range_end DATE,
    municipality VARCHAR(100),
    status ENUM('draft', 'final', 'archived') DEFAULT 'draft',
    metadata JSON, -- Additional report parameters
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (generated_by) REFERENCES users(id) ON DELETE SET NULL
);
```

---

#### 5. `reports_incident` Table
**Purpose:** Pivot table for report-incident relationships

**Schema:**
```sql
CREATE TABLE reports_incident (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    report_id BIGINT NOT NULL,
    incident_id BIGINT NOT NULL,
    created_at TIMESTAMP,
    FOREIGN KEY (report_id) REFERENCES reports(id) ON DELETE CASCADE,
    FOREIGN KEY (incident_id) REFERENCES incidents(id) ON DELETE CASCADE,
    UNIQUE KEY unique_report_incident (report_id, incident_id)
);
```

---

#### 6. `vehicle_dispatched` Table
**Purpose:** Track vehicle dispatch history

**Schema:**
```sql
CREATE TABLE vehicle_dispatched (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    vehicle_id BIGINT NOT NULL,
    incident_id BIGINT NOT NULL,
    dispatch_time DATETIME NOT NULL,
    arrival_time DATETIME,
    departure_time DATETIME,
    return_time DATETIME,
    odometer_start INT,
    odometer_end INT,
    distance_traveled DECIMAL(10,2),
    fuel_used DECIMAL(8,2),
    dispatch_notes TEXT,
    status ENUM('dispatched', 'arrived', 'in_progress', 'returning', 'completed') DEFAULT 'dispatched',
    dispatched_by BIGINT, -- user_id
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE RESTRICT,
    FOREIGN KEY (incident_id) REFERENCES incidents(id) ON DELETE CASCADE,
    FOREIGN KEY (dispatched_by) REFERENCES users(id) ON DELETE SET NULL
);
```

---

#### 7. `fuel_consumption` Table
**Purpose:** Track detailed fuel consumption

**Schema:**
```sql
CREATE TABLE fuel_consumption (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    dispatch_id BIGINT NOT NULL,
    vehicle_id BIGINT NOT NULL,
    starting_odometer INT NOT NULL,
    ending_odometer INT NOT NULL,
    distance_traveled DECIMAL(10,2) NOT NULL,
    fuel_consumed DECIMAL(8,2) NOT NULL,
    fuel_price_per_liter DECIMAL(8,2),
    total_cost DECIMAL(10,2),
    fuel_type ENUM('gasoline', 'diesel', 'electric', 'hybrid'),
    recorded_by BIGINT,
    recorded_at DATETIME NOT NULL,
    notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (dispatch_id) REFERENCES vehicle_dispatched(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE RESTRICT,
    FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL
);
```

---

#### 8. `responders` Table
**Purpose:** Emergency responder directory

**Schema:**
```sql
CREATE TABLE responders (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT, -- Optional link to user account
    responder_number VARCHAR(50) UNIQUE NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    email VARCHAR(255),
    position VARCHAR(100), -- Paramedic, Firefighter, etc.
    team_unit VARCHAR(100), -- Team/Unit assignment
    specialization VARCHAR(100), -- Medical, Fire, Rescue, etc.
    certification_number VARCHAR(100),
    certification_expiry DATE,
    municipality VARCHAR(100) NOT NULL,
    status ENUM('active', 'inactive', 'on_leave') DEFAULT 'active',
    emergency_contact_name VARCHAR(100),
    emergency_contact_number VARCHAR(20),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

---

#### 9. `dispatched_responders` Table
**Purpose:** Track responder assignments to incidents

**Schema:**
```sql
CREATE TABLE dispatched_responders (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    dispatch_id BIGINT NOT NULL,
    responder_id BIGINT NOT NULL,
    assigned_role VARCHAR(100), -- Team Leader, Medic, Driver, etc.
    dispatch_time DATETIME NOT NULL,
    arrival_time DATETIME,
    departure_time DATETIME,
    status ENUM('dispatched', 'arrived', 'in_progress', 'completed') DEFAULT 'dispatched',
    notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (dispatch_id) REFERENCES vehicle_dispatched(id) ON DELETE CASCADE,
    FOREIGN KEY (responder_id) REFERENCES responders(id) ON DELETE RESTRICT
);
```

---

#### 10. `report_requests` Table
**Purpose:** Citizen requests for incident reports

**Schema:**
```sql
CREATE TABLE report_requests (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    request_number VARCHAR(50) UNIQUE NOT NULL,
    victim_id BIGINT,
    incident_id BIGINT,
    requester_name VARCHAR(100) NOT NULL,
    requester_email VARCHAR(255),
    requester_phone VARCHAR(20) NOT NULL,
    requester_id_number VARCHAR(50),
    purpose TEXT NOT NULL,
    request_type ENUM('incident_report', 'medical_certificate', 'police_report', 'other'),
    status ENUM('pending', 'processing', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    requested_at DATETIME NOT NULL,
    processed_by BIGINT,
    processed_at DATETIME,
    completed_at DATETIME,
    rejection_reason TEXT,
    notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (victim_id) REFERENCES victims(id) ON DELETE SET NULL,
    FOREIGN KEY (incident_id) REFERENCES incidents(id) ON DELETE CASCADE,
    FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL
);
```

---

#### 11. `feedback` Table
**Purpose:** Feedback on report requests

**Schema:**
```sql
CREATE TABLE feedback (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    request_id BIGINT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    feedback_text TEXT,
    submitted_at DATETIME NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES report_requests(id) ON DELETE CASCADE
);
```

---

### Existing Tables to Refactor

#### 1. `users` Table
**Changes:**
- ✅ Add `role_id` foreign key column
- ✅ Keep `role` enum temporarily for backwards compatibility
- ✅ Add migration to populate `role_id` from `role` enum
- ⚠️ Deprecate `role` enum in future version

**Migration:**
```php
Schema::table('users', function (Blueprint $table) {
    $table->foreignId('role_id')->nullable()->after('role')->constrained('account_roles');
    // Keep role enum for now (backwards compatibility)
});
```

---

#### 2. `incidents` Table
**Changes:**
- ✅ Remove circular FK `current_incident_id` from vehicles table
- ✅ Add proper relationships via `vehicle_dispatched`
- ✅ Optionally rename to `incident_list` (schema name)

**Decision:** Keep as `incidents` (Laravel convention)

---

#### 3. `requests` Table
**Changes:**
- ✅ Add `incident_id` foreign key
- ✅ Keep `incident_case_number` for backwards compatibility
- ✅ Clarify purpose: This is for administrative requests (different from `report_requests`)

**Migration:**
```php
Schema::table('requests', function (Blueprint $table) {
    $table->foreignId('incident_id')->nullable()->after('incident_case_number')->constrained('incidents');
});
```

---

#### 4. `victims` Table
**Changes:**
- ✅ Update `hospital_referred` (string) to use `hospital_referrals` table
- ✅ Keep existing column for backwards compatibility
- ✅ Add relationships

**No migration needed** - just add relationships in model

---

#### 5. `vehicles` Table
**Changes:**
- ✅ Remove `current_incident_id` foreign key (circular dependency)
- ✅ Use `vehicle_dispatched` table instead
- ✅ Add relationship to `dispatched_responders`

**Migration:**
```php
Schema::table('vehicles', function (Blueprint $table) {
    $table->dropForeign(['current_incident_id']);
    $table->dropColumn('current_incident_id');
});
```

---

## 🚀 Implementation Strategy

### Phase 1: Create Lookup Tables (Low Risk)
**Priority:** HIGH
**Estimated Time:** 2-3 hours

**Tables:**
1. ✅ `account_roles`
2. ✅ `hospitals`

**Actions:**
- Create migrations
- Create models
- Seed initial data
- Add relationships to existing models (optional FKs)

**Risk Level:** 🟢 LOW - New tables, no impact on existing functionality

---

### Phase 2: Create Tracking Tables (Medium Risk)
**Priority:** HIGH
**Estimated Time:** 3-4 hours

**Tables:**
1. ✅ `vehicle_dispatched`
2. ✅ `fuel_consumption`
3. ✅ `responders`
4. ✅ `dispatched_responders`

**Actions:**
- Create migrations with proper foreign keys
- Create models with relationships
- Update Vehicle model to use dispatched table

**Risk Level:** 🟡 MEDIUM - Affects vehicle tracking logic

---

### Phase 3: Create Report System (Medium Risk)
**Priority:** MEDIUM
**Estimated Time:** 2-3 hours

**Tables:**
1. ✅ `reports`
2. ✅ `reports_incident`
3. ✅ `hospital_referrals`

**Actions:**
- Create migrations
- Create models
- Add many-to-many relationships

**Risk Level:** 🟡 MEDIUM - New feature area

---

### Phase 4: Create Request System (Low Risk)
**Priority:** MEDIUM
**Estimated Time:** 2-3 hours

**Tables:**
1. ✅ `report_requests`
2. ✅ `feedback`

**Actions:**
- Create migrations
- Create models
- Differentiate from existing `requests` table

**Risk Level:** 🟢 LOW - New feature, separate from existing requests

---

### Phase 5: Refactor Existing Tables (HIGH RISK)
**Priority:** HIGH
**Estimated Time:** 4-5 hours

**Tables:**
1. ⚠️ `users` - Add `role_id`
2. ⚠️ `requests` - Add `incident_id`
3. ⚠️ `vehicles` - Remove `current_incident_id`

**Actions:**
- Create migrations to add/remove columns
- Populate new foreign keys from existing data
- Update models
- Update controllers (if needed)
- Test thoroughly

**Risk Level:** 🔴 HIGH - Modifies core tables with existing data

---

## 📝 Migration Plan

### Migration Naming Convention

**Format:** `YYYY_MM_DD_HHMMSS_action_table_name.php`

**Examples:**
- ✅ `2025_11_19_100000_create_account_roles_table.php`
- ✅ `2025_11_19_100100_create_hospitals_table.php`
- ✅ `2025_11_19_100200_create_hospital_referrals_table.php`
- ✅ `2025_11_19_103000_add_role_id_to_users_table.php`

---

### Migration Order (CRITICAL)

**Must follow dependency order to avoid foreign key errors:**

```
1. account_roles (no dependencies)
2. hospitals (no dependencies)
3. responders (depends on users)
4. hospital_referrals (depends on victims, hospitals, users)
5. reports (depends on users)
6. reports_incident (depends on reports, incidents)
7. vehicle_dispatched (depends on vehicles, incidents, users)
8. fuel_consumption (depends on vehicle_dispatched, vehicles, users)
9. dispatched_responders (depends on vehicle_dispatched, responders)
10. report_requests (depends on victims, incidents, users)
11. feedback (depends on report_requests)

-- REFACTORING MIGRATIONS --
12. add_role_id_to_users_table (depends on account_roles)
13. add_incident_id_to_requests_table (depends on incidents)
14. remove_current_incident_id_from_vehicles_table
```

---

### Sample Migration Template

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('table_name', function (Blueprint $table) {
            $table->id(); // Laravel convention

            // Foreign keys
            $table->foreignId('related_table_id')
                  ->constrained('related_table')
                  ->onDelete('cascade'); // or 'set null', 'restrict'

            // Columns
            $table->string('column_name');

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['column1', 'column2']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_name');
    }
};
```

---

## 🔗 Model Relationship Updates

### 1. User Model

**New Relationships to Add:**

```php
// app/Models/User.php

// Belongs To
public function role(): BelongsTo
{
    return $this->belongsTo(AccountRole::class, 'role_id');
}

// Has Many
public function generatedReports(): HasMany
{
    return $this->hasMany(Report::class, 'generated_by');
}

public function processedReportRequests(): HasMany
{
    return $this->hasMany(ReportRequest::class, 'processed_by');
}

public function dispatchedVehicles(): HasMany
{
    return $this->hasMany(VehicleDispatched::class, 'dispatched_by');
}
```

---

### 2. Incident Model

**New Relationships to Add:**

```php
// app/Models/Incident.php

// Belongs To Many (via pivot)
public function reports(): BelongsToMany
{
    return $this->belongsToMany(Report::class, 'reports_incident', 'incident_id', 'report_id')
                ->withTimestamps();
}

// Has Many
public function vehicleDispatches(): HasMany
{
    return $this->hasMany(VehicleDispatched::class);
}

public function reportRequests(): HasMany
{
    return $this->hasMany(ReportRequest::class);
}
```

---

### 3. Vehicle Model

**Remove:**
```php
// Remove this relationship (circular dependency)
public function currentIncident(): BelongsTo
{
    return $this->belongsTo(Incident::class, 'current_incident_id');
}
```

**Add:**
```php
// app/Models/Vehicle.php

public function dispatches(): HasMany
{
    return $this->hasMany(VehicleDispatched::class);
}

public function currentDispatch(): HasOne
{
    return $this->hasOne(VehicleDispatched::class)
                ->whereIn('status', ['dispatched', 'arrived', 'in_progress'])
                ->latest();
}

public function fuelConsumptions(): HasMany
{
    return $this->hasMany(FuelConsumption::class);
}
```

---

### 4. Victim Model

**New Relationships to Add:**

```php
// app/Models/Victim.php

public function hospitalReferrals(): HasMany
{
    return $this->hasMany(HospitalReferral::class);
}

public function reportRequests(): HasMany
{
    return $this->hasMany(ReportRequest::class);
}

public function primaryHospital(): HasOneThrough
{
    return $this->hasOneThrough(
        Hospital::class,
        HospitalReferral::class,
        'victim_id', // FK on hospital_referrals
        'id', // FK on hospitals
        'id', // Local key on victims
        'hospital_id' // Local key on hospital_referrals
    )->where('referral_type', 'initial');
}
```

---

### 5. Request Model

**Update:**

```php
// app/Models/Request.php

public function incident(): BelongsTo
{
    return $this->belongsTo(Incident::class);
}
```

---

## ⚠️ Risk Assessment

### HIGH RISK Areas

**1. Vehicles `current_incident_id` Removal**
- **Risk:** Breaking active dispatch tracking
- **Mitigation:**
  - Implement `vehicle_dispatched` table first
  - Migrate existing `current_incident_id` data to new table
  - Update all controllers that reference this field
  - Add backwards compatibility layer

**2. Users `role_id` Addition**
- **Risk:** Breaking authentication/authorization
- **Mitigation:**
  - Keep `role` enum during transition
  - Populate `role_id` automatically on login
  - Add fallback logic: check `role_id` first, then `role` enum
  - Gradual migration over 2-3 versions

**3. Requests `incident_id` Addition**
- **Risk:** Breaking existing request workflow
- **Mitigation:**
  - Keep `incident_case_number` field
  - Add data migration to populate `incident_id` from case numbers
  - Update forms to use FK instead of string

---

### MEDIUM RISK Areas

**1. New Vehicle Dispatch System**
- **Risk:** Complex tracking logic changes
- **Mitigation:**
  - Implement alongside existing system
  - Gradual rollout by municipality
  - Extensive testing with real scenarios

**2. Hospital Referrals**
- **Risk:** Data entry burden increase
- **Mitigation:**
  - Auto-populate from existing `victims.hospital_referred`
  - Make fields optional initially
  - Training for staff

---

### LOW RISK Areas

**1. New Report System**
- **Risk:** Minimal (new feature)
- **Mitigation:** Phase implementation, start with basic reporting

**2. Responder Management**
- **Risk:** Minimal (new feature)
- **Mitigation:** Optional initially, can be added gradually

---

## 🧪 Testing Strategy

### 1. Migration Testing

**Pre-Migration Checks:**
```bash
# Check current database state
php artisan migrate:status

# Create backup
php artisan db:backup

# Test migrations on copy of database
php artisan migrate --pretend
```

**Migration Validation:**
```bash
# Run migrations
php artisan migrate

# Verify table structure
php artisan db:show

# Check relationships
php artisan tinker
>>> User::with('role')->first()
>>> Incident::with('vehicleDispatches')->first()
```

---

### 2. Model Relationship Testing

**Create Test Cases:**
```php
// tests/Feature/DatabaseRelationshipsTest.php

public function test_user_has_role_relationship()
{
    $user = User::factory()->create(['role_id' => 1]);
    $this->assertInstanceOf(AccountRole::class, $user->role);
}

public function test_incident_has_many_vehicle_dispatches()
{
    $incident = Incident::factory()->create();
    $dispatch = VehicleDispatched::factory()->create(['incident_id' => $incident->id]);

    $this->assertTrue($incident->vehicleDispatches->contains($dispatch));
}

public function test_victim_has_hospital_referrals()
{
    $victim = Victim::factory()->create();
    $referral = HospitalReferral::factory()->create(['victim_id' => $victim->id]);

    $this->assertCount(1, $victim->hospitalReferrals);
}
```

---

### 3. Integration Testing

**Test Critical Workflows:**
- ✅ Create incident → Dispatch vehicle → Assign responders
- ✅ Victim referral → Hospital → Follow-up
- ✅ Request submission → Processing → Report generation
- ✅ User role change → Permission update

---

## 🔄 Rollback Plan

### If Migrations Fail

**Step 1: Identify Failed Migration**
```bash
php artisan migrate:status
```

**Step 2: Rollback to Previous State**
```bash
# Rollback last batch
php artisan migrate:rollback

# Rollback specific steps
php artisan migrate:rollback --step=5

# Rollback all normalization migrations
php artisan migrate:rollback --path=/database/migrations/2025_11_19*
```

**Step 3: Restore Database Backup**
```bash
# If rollback fails, restore from backup
mysql -u user -p database_name < backup_2025_11_19.sql
```

---

### If Application Breaks

**Emergency Rollback Procedure:**

1. **Revert Git Branch**
   ```bash
   git checkout stable-main
   ```

2. **Rollback Database**
   ```bash
   php artisan migrate:rollback --batch=X
   ```

3. **Clear Caches**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

4. **Notify Team**
   - Document what failed
   - Schedule fix for later
   - Resume from `stable-main`

---

## 📅 Implementation Timeline

### Week 1: Planning & Setup (Current)
- ✅ Day 1: Analyze schema and create plan
- ⏳ Day 2: Review plan with team
- ⏳ Day 3: Create backup and prepare environment

### Week 2: Phase 1 Implementation
- ⏳ Day 4: Create lookup tables (roles, hospitals)
- ⏳ Day 5: Create tracking tables (vehicle dispatch, responders)

### Week 3: Phase 2 Implementation
- ⏳ Day 6: Create report system tables
- ⏳ Day 7: Create request system tables

### Week 4: Phase 3 Refactoring
- ⏳ Day 8: Refactor users table
- ⏳ Day 9: Refactor vehicles table
- ⏳ Day 10: Update all models

### Week 5: Testing & Validation
- ⏳ Day 11-12: Integration testing
- ⏳ Day 13: Bug fixes
- ⏳ Day 14: Final review and merge

---

## ✅ Success Criteria

### Database Structure
- ✅ All 17 new tables created
- ✅ All foreign keys properly defined
- ✅ All indexes created for performance
- ✅ Zero migration errors

### Model Relationships
- ✅ All models have correct relationships
- ✅ Eager loading works correctly
- ✅ No N+1 query issues

### Application Functionality
- ✅ All existing features still work
- ✅ No broken views
- ✅ No broken controllers
- ✅ Authentication/authorization works

### Performance
- ✅ Query performance maintained or improved
- ✅ No significant slowdowns
- ✅ Proper indexing verified

### Documentation
- ✅ All new tables documented
- ✅ ERD updated
- ✅ API documentation updated (if applicable)
- ✅ Code comments added

---

## 📚 References

**Related Documents:**
- `DAILY_PROGRESS_NOVEMBER_17_2025.md` - Recent UI/UX improvements
- `public/global/db_schema.png` - Target database schema
- Laravel Documentation: [Database Migrations](https://laravel.com/docs/migrations)
- Laravel Documentation: [Eloquent Relationships](https://laravel.com/docs/eloquent-relationships)

**Schema Design Principles:**
- Database Normalization (1NF, 2NF, 3NF)
- Laravel Best Practices
- SOLID Principles
- DRY (Don't Repeat Yourself)

---

## 📝 Notes

### Important Decisions

1. **Primary Key Naming:** Using `id` (Laravel convention) instead of `{table}_id`
2. **Table Naming:** Keeping plural names (Laravel convention)
3. **Backwards Compatibility:** Maintaining old columns during transition
4. **Data Migration:** Populating new FKs from existing string references

### Future Enhancements

1. **Municipality Normalization:** Create `municipalities` table
2. **Barangay Normalization:** Create `barangays` table
3. **Equipment Tracking:** Normalize `equipment_list` JSON
4. **Media Management:** Create `incident_media` table for photos/videos
5. **Audit Trail:** Enhanced tracking with `model_has_changes` table

---

**Plan Status:** ✅ READY FOR REVIEW
**Next Step:** Get approval and begin Phase 1 implementation
**Estimated Completion:** 2 weeks from start

---

*Document prepared by: AI Development Assistant*
*Review Date: November 19, 2025*
*Version: 1.0*
