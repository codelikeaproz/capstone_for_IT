# Database Schema Analysis and Normalization Plan

## Executive Summary

This document maps the **current database schema** to the **target normalized schema** based on `db_schema.png` ERD, following Laravel conventions and SOLID principles.

## Current Schema (Existing)

### 1. Users & Authentication
**Table:** `users`
- **PK:** `id`
- **Columns:** first_name, last_name, email, password, role (enum: admin, staff, responder, citizen, superadmin), municipality, phone_number, address, is_active, last_login, email_verified_at, two_factor_code, two_factor_expires_at, failed_login_attempts, locked_until, email_verification_token
- **Indexes:** [municipality, role], [role, is_active]
- **Issues:** Role stored as enum string, not FK to roles table

### 2. Incidents
**Table:** `incidents`
- **PK:** `id`
- **FKs:** assigned_staff_id → users, reported_by → users, assigned_vehicle_id → vehicles (not constrained)
- **Columns:** incident_number, incident_type, severity_level, status, location, municipality, barangay, latitude, longitude, description, incident_date, weather_condition, road_condition, casualty_count, injury_count, fatality_count, property_damage_estimate, damage_description, vehicle_involved, vehicle_details, photos (json), videos (json), documents (json), response_time, resolved_at, resolution_notes
- **Type-specific fields:** vehicle_count, license_plates, driver_information, medical_emergency_type, patient_symptoms, building_type, fire_spread_level, fire_cause, disaster_type, disaster_description, crime_type, suspect_description
- **Soft Deletes:** Yes
- **Issues:** 
  - No junction table for reports (report many-to-many)
  - assigned_vehicle_id not properly constrained
  - Type-specific fields in single table (acceptable for polymorphic)

### 3. Vehicles
**Table:** `vehicles`
- **PK:** `id`
- **FKs:** assigned_driver_id → users, current_incident_id → incidents (not constrained)
- **Columns:** vehicle_number, license_plate, vehicle_type, status, make, model, year, color, fuel_capacity, current_fuel_level, fuel_consumption_rate, odometer_reading, total_distance, municipality, last_maintenance_date, next_maintenance_due, maintenance_notes, insurance_policy, insurance_expiry, registration_expiry, equipment_list (json), gps_enabled, current_latitude, current_longitude
- **Indexes:** [municipality, status], [vehicle_type, status], [status, assigned_driver_id]
- **Issues:** 
  - No separate vehicle_dispatches/dispatch table
  - No separate fuel_consumption tracking table
  - No dispatched_responders pivot

### 4. Victims
**Table:** `victims`
- **PK:** `id`
- **FKs:** incident_id → incidents (cascade)
- **Columns:** first_name, last_name, birth_date, age (removed, computed), gender, contact_number, address, medical_status, injury_description, medical_treatment, hospital_referred, transportation_method, hospital_arrival_time, helmet_used, seatbelt_used, protective_gear_used, victim_role, vehicle_type_involved, seating_position, emergency_contact_name, emergency_contact_phone, emergency_contact_relationship, insurance_provider, insurance_policy_number, legal_action_required, is_pregnant, labor_stage
- **Issues:** 
  - hospital_referred is string, should be FK to hospitals table
  - No hospital_referrals table for tracking referral chain

### 5. Requests
**Table:** `requests`
- **PK:** `id`
- **FKs:** assigned_staff_id → users, approved_by → users
- **Columns:** request_number, requester_name, requester_email, requester_phone, requester_id_number, requester_address, request_type, urgency_level, request_description, purpose_of_request, incident_case_number, incident_date, incident_location, municipality, status, approved_at, approval_notes, rejection_reason, supporting_documents (json), generated_reports (json), processing_started_at, completed_at, processing_days, email_notifications_enabled, sms_notifications_enabled, internal_notes
- **Issues:**
  - No FK to victim_id (should reference victim if related to victim)
  - No FK to incident_id (uses incident_case_number string)
  - Named `requests` but ERD calls it `report_requests`

### 6. Vehicle Utilizations
**Table:** `vehicle_utilizations`
- **PK:** `id`
- **FKs:** vehicle_id → vehicles, victim_id → victims, incident_id → incidents, driver_id → users
- **Columns:** service_date, trip_ticket_number, origin_address, destination_address, service_category, service_type, fuel_consumed, distance_traveled, status, notes, municipality
- **Issues:** 
  - Partially implements vehicle_dispatched + fuel_consumption
  - Missing separation of dispatch and fuel consumption
  - No dispatched_responders pivot

### 7. Activity Logs
**Table:** `activity_log` (Spatie Laravel-activitylog)
- **PK:** `id`
- **Columns:** log_name, description, subject_type, subject_id, causer_type, causer_id, properties (json), created_at, updated_at
- **Issues:** Using Spatie package, rename to `logs` per ERD if needed

### Missing Tables (from ERD)
1. **account_roles** - Should store role definitions with permissions
2. **hospitals** - Hospital master data
3. **hospital_referrals** - Victim-to-hospital referral chain
4. **reports** - Formal report documents
5. **reports_incident** (or `incident_report`) - Many-to-many pivot between reports and incidents
6. **feedback** - User feedback on requests
7. **vehicle_dispatches** (or `vehicle_dispatched`) - Dispatch assignments
8. **dispatched_responders** - Pivot for dispatch-to-responder assignments
9. **fuel_consumption** - Separate fuel tracking per dispatch
10. **responders** - Separate responder entity (or use users table)

---

## Target Normalized Schema (Based on db_schema.png)

### 1. Users & Roles

#### Table: `account_roles` (or `roles`)
- **PK:** `id`
- **Columns:** role_name (unique), role_description, permissions (json), created_at, updated_at
- **Data:** ['superadmin', 'admin', 'staff', 'responder', 'citizen']

#### Table: `users` (Updated)
- **PK:** `id`
- **FKs:** role_id → account_roles (nullable for backward compat)
- **Columns:** Keep all existing + add `role_id`
- **Migration:** Backfill role_id from role string

### 2. Incidents & Reports

#### Table: `incidents` (Existing, minimal changes)
- Keep all existing columns
- Ensure FK constraints: assigned_staff_id, reported_by, assigned_vehicle_id

#### Table: `reports`
- **PK:** `id`
- **Columns:** report_id, report_title, report_content, created_at, timestamp

#### Table: `reports_incident` (Pivot)
- **PK:** `id` or composite (report_id, incident_id)
- **FKs:** report_id → reports, incident_id → incidents

### 3. Victims & Hospitals

#### Table: `victims` (Existing, update hospital_referred)
- Keep all existing columns
- **Deprecate:** hospital_referred string (keep for now)

#### Table: `hospitals`
- **PK:** `id` (hospital_id in ERD)
- **Columns:** hospital_name, contact_number, address, status, created_at, updated_at

#### Table: `hospital_referrals`
- **PK:** `id` (referral_id in ERD)
- **FKs:** victim_id → victims, hospital_id → hospitals, initial_hospital → hospitals (nullable)
- **Columns:** referral_reason, initial_hospital, medical_notes, transported_at, status, created_at, updated_at

### 4. Requests & Feedback

#### Table: `report_requests` (Rename `requests`)
- **PK:** `id` (request_id in ERD)
- **FKs:** victim_id → victims (nullable), incident_id → incidents (nullable), assigned_staff_id → users
- **Columns:** Keep existing + add victim_id, incident_id FKs

#### Table: `feedback`
- **PK:** `id` (feedback_id in ERD)
- **FKs:** request_id → report_requests
- **Columns:** feedback, rating, submitted_at, created_at, updated_at

### 5. Vehicles, Dispatch, Fuel

#### Table: `vehicles` (Existing, minimal changes)
- Keep all existing
- **Deprecate:** current_incident_id (move to dispatches)

#### Table: `vehicle_dispatches` (or `vehicle_dispatched`)
- **PK:** `id` (dispatch_id in ERD)
- **FKs:** vehicle_id → vehicles, incident_id → incidents, assignment_id → users (nullable)
- **Columns:** dispatched_location, notes, timestamp, created_at, updated_at

#### Table: `dispatched_responders` (Pivot)
- **PK:** composite (dispatch_id, responder_id) or `assignment_id`
- **FKs:** dispatch_id → vehicle_dispatches, responder_id → users (or responders)
- **Columns:** notes, position, created_at

#### Table: `responders` (Optional, or use users with role)
- **Decision:** Use `users` table with `role='responder'` instead of separate table
- **PK:** `id` (responder_id)
- **Columns:** team_unit, notes, lastname, firstname

#### Table: `fuel_consumption`
- **PK:** `id` (consumption_id in ERD)
- **FKs:** dispatch_id → vehicle_dispatches
- **Columns:** starting_odometer, ending_odometer, distance_traveled, fuel_consumed, fuel_price_per_liter, total_cost, fuel_type, timestamp, created_at, updated_at

### 6. Logs
#### Table: `logs` (Rename `activity_log` or keep as is)
- Keep Spatie structure
- **Columns:** log_id (id), user_id (causer_id), description, timestamp, action

---

## Migration Strategy (Non-Destructive)

### Phase 1: Add New Tables
1. Create `account_roles` → backfill from users.role
2. Create `hospitals`
3. Create `hospital_referrals`
4. Create `reports`
5. Create `reports_incident`
6. Create `feedback`
7. Create `vehicle_dispatches`
8. Create `dispatched_responders`
9. Create `fuel_consumption`

### Phase 2: Add FKs to Existing Tables
1. Add `role_id` to `users` → backfill from `role` string
2. Add `victim_id`, `incident_id` FKs to `report_requests` (rename `requests`)
3. Add proper FK constraints to `incidents.assigned_vehicle_id`
4. Add proper FK constraints to `vehicles.current_incident_id` (before deprecating)

### Phase 3: Backfill Data
1. Backfill `hospitals` from `victims.hospital_referred` (unique values)
2. Backfill `hospital_referrals` from victims with hospital data
3. Backfill `vehicle_dispatches` from `vehicle_utilizations` and `vehicles.current_incident_id`
4. Backfill `fuel_consumption` from `vehicle_utilizations.fuel_consumed`
5. Create roles in `account_roles` and assign `role_id` in users

### Phase 4: Update Application Layer
1. Update Eloquent models with new relationships
2. Update controllers to use new relationships
3. Update seeders to use normalized structure
4. Update views to use relationships

### Phase 5: Deprecation (Optional Future)
1. Mark `vehicles.current_incident_id` as deprecated (keep for compatibility)
2. Mark `victims.hospital_referred` as deprecated
3. Mark `users.role` as deprecated (keep `role_id` as primary)
4. Consider soft-deleting `vehicle_utilizations` if fully replaced

---

## Eloquent Relationships Mapping

### User Model
```php
// Existing
hasMany(Incident, 'reported_by')
hasMany(Incident, 'assigned_staff_id')
hasMany(Vehicle, 'assigned_driver_id')
hasMany(Request, 'assigned_staff_id')
hasMany(Request, 'approved_by')

// New
belongsTo(Role, 'role_id')
hasManyThrough(VehicleDispatch, Vehicle, 'assigned_driver_id', 'vehicle_id')
belongsToMany(VehicleDispatch, 'dispatched_responders', 'responder_id', 'dispatch_id')
```

### Incident Model
```php
// Existing
belongsTo(User, 'assigned_staff_id')
belongsTo(User, 'reported_by')
belongsTo(Vehicle, 'assigned_vehicle_id')
hasMany(Victim)

// New
hasMany(VehicleDispatch)
belongsToMany(Report, 'reports_incident')
hasMany(ReportRequest)
```

### Vehicle Model
```php
// Existing
belongsTo(User, 'assigned_driver_id')
belongsTo(Incident, 'current_incident_id') // deprecate
hasMany(Incident, 'assigned_vehicle_id')
hasMany(VehicleUtilization)

// New
hasMany(VehicleDispatch)
hasManyThrough(FuelConsumption, VehicleDispatch)
```

### Victim Model
```php
// Existing
belongsTo(Incident)

// New
hasMany(HospitalReferral)
hasMany(ReportRequest)
```

### VehicleDispatch Model (New)
```php
belongsTo(Vehicle)
belongsTo(Incident)
belongsTo(User, 'assignment_id')
belongsToMany(User, 'dispatched_responders', 'dispatch_id', 'responder_id') // responders
hasMany(FuelConsumption)
```

### Hospital Model (New)
```php
hasMany(HospitalReferral, 'hospital_id')
hasMany(HospitalReferral, 'initial_hospital')
```

### HospitalReferral Model (New)
```php
belongsTo(Victim)
belongsTo(Hospital, 'hospital_id')
belongsTo(Hospital, 'initial_hospital')
```

### Report Model (New)
```php
belongsToMany(Incident, 'reports_incident')
```

### ReportRequest Model (Rename from Request)
```php
// Existing
belongsTo(User, 'assigned_staff_id')
belongsTo(User, 'approved_by')

// New
belongsTo(Victim, 'victim_id')
belongsTo(Incident, 'incident_id')
hasMany(Feedback)
```

### Feedback Model (New)
```php
belongsTo(ReportRequest, 'request_id')
```

### FuelConsumption Model (New)
```php
belongsTo(VehicleDispatch, 'dispatch_id')
```

### Role Model (New)
```php
hasMany(User, 'role_id')
```

---

## Column Mapping Summary

| Current Table.Column | Target Table.Column | Action |
|---------------------|---------------------|--------|
| users.role (enum) | users.role_id → account_roles.id | Add FK, backfill |
| victims.hospital_referred (string) | hospital_referrals.hospital_id → hospitals.id | Extract, create hospitals, create referrals |
| vehicles.current_incident_id | vehicle_dispatches.incident_id | Move to dispatch table |
| vehicle_utilizations.* | vehicle_dispatches.* + fuel_consumption.* | Split table |
| requests | report_requests | Rename table |
| requests.incident_case_number | report_requests.incident_id (FK) | Convert string to FK |

---

## Next Steps

1. ✅ Complete schema analysis (this document)
2. 🔄 Create normalization migrations
3. 🔄 Create/update Eloquent models
4. 🔄 Update seeders
5. 🔄 Update controllers/services
6. 🔄 Update views
7. 🔄 Test and validate
8. 🔄 Document final schema


