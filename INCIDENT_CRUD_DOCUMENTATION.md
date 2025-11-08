# Incident Reporting CRUD Implementation

## Overview
This document provides a comprehensive guide to the full CRUD (Create, Read, Update, Delete) implementation for the Incident Reporting System. The implementation follows Laravel best practices with a clean separation of concerns using Controllers, Services, Form Requests, and Models.

## Architecture

### Layer Structure
```
┌─────────────────────────────────────────────────────┐
│                    Routes (web.php)                  │
│              RESTful Resource Routes                 │
└──────────────────────┬──────────────────────────────┘
                       │
┌──────────────────────▼──────────────────────────────┐
│            IncidentController                        │
│         (HTTP Request Handling)                      │
│   - index()    - create()   - store()               │
│   - show()     - edit()     - update()              │
│   - destroy()                                        │
└──────────────────────┬──────────────────────────────┘
                       │
        ┌──────────────┼──────────────┐
        │              │              │
┌───────▼───────┐ ┌───▼────────┐ ┌──▼──────────────┐
│ Form Requests │ │  Service   │ │     Model       │
│               │ │            │ │                 │
│ - Store       │ │ Incident   │ │   Incident      │
│ - Update      │ │ Service    │ │   (Eloquent)    │
└───────────────┘ └────────────┘ └─────────────────┘
```

### Key Components

1. **IncidentController** (`app/Http/Controllers/IncidentController.php`)
   - Handles HTTP requests and responses
   - Minimal business logic
   - Delegates complex operations to IncidentService

2. **Form Requests**
   - `StoreIncidentRequest.php` - Validates new incident creation
   - `UpdateIncidentRequest.php` - Validates incident updates with authorization

3. **IncidentService** (`app/Services/IncidentService.php`)
   - Business logic layer
   - Database transactions
   - Media file handling
   - Victim management
   - Vehicle assignment

4. **Incident Model** (`app/Models/Incident.php`)
   - Eloquent ORM model
   - Relationships, scopes, accessors
   - Soft deletes enabled

---

## CRUD Operations

### 1. CREATE

#### Route
```php
POST /incidents
```

#### Controller Method
```php
public function store(StoreIncidentRequest $request, IncidentService $incidentService)
```

#### Process Flow
1. User fills out the incident creation form
2. `StoreIncidentRequest` validates all input data
3. License plates are processed from comma-separated string to array
4. `IncidentService::createIncident()` is called within a database transaction
5. Photos and videos are uploaded to storage
6. Incident record is created with auto-generated incident number
7. Victims are created and associated with the incident
8. Vehicle is assigned if provided
9. Activity log is recorded
10. User is redirected to the incident show page

#### Key Features
- **Auto-generated incident number**: Format `INC-{YEAR}-{SEQUENCE}`
- **Transaction safety**: All or nothing - if any step fails, everything rolls back
- **Media handling**: Photos (max 5, 2MB each) and videos (max 2, 10MB each)
- **Victim creation**: Multiple victims can be added during incident creation
- **Casualty counting**: Automatically updates injury and fatality counts
- **Activity logging**: Complete audit trail

#### Validation Rules
- **Required fields**: incident_type, severity_level, incident_date, location, municipality, barangay, description
- **Conditional validation**: Based on incident type (traffic accident, medical emergency, fire, natural disaster, criminal activity)
- **Media validation**: File type, size, and count restrictions
- **Photos**: Required, minimum 1, maximum 5

---

### 2. READ

#### Routes
```php
GET /incidents              // List all incidents (index)
GET /incidents/{incident}   // Show single incident
```

#### Controller Methods

##### Index (List)
```php
public function index(Request $request)
```

**Features:**
- Pagination (15 items per page)
- Municipality filtering (automatic for non-admin users)
- Filter by: severity, status, incident_type
- Eager loading: assignedStaff, assignedVehicle, reporter
- Latest incidents first

##### Show (Details)
```php
public function show(Incident $incident)
```

**Features:**
- Permission checking (municipality-based access control)
- Eager loading: assignedStaff, assignedVehicle, reporter, victims
- Full incident details including all type-specific fields
- Media display (photos and videos)

---

### 3. UPDATE

#### Route
```php
PUT /incidents/{incident}
```

#### Controller Method
```php
public function update(UpdateIncidentRequest $request, Incident $incident, IncidentService $incidentService)
```

#### Process Flow
1. `UpdateIncidentRequest` validates and authorizes the request
2. Two update modes are supported:
   - **Quick Update**: Status and resolution notes only (maintain_other_fields flag)
   - **Full Update**: All incident fields
3. License plates are processed if provided
4. `IncidentService::updateIncident()` handles the update within a transaction
5. New photos/videos are merged with existing media
6. Vehicle assignment changes are handled (release old, assign new)
7. Activity log is recorded
8. User is redirected to incident show page

#### Key Features
- **Authorization**: Built into `UpdateIncidentRequest`
  - Admin: Can update any incident
  - Staff: Can only update incidents in their municipality
- **Partial updates**: Supports both quick status updates and full edits
- **Media preservation**: Existing media is preserved, new media is added
- **Resolved timestamp**: Automatically set when status changes to 'resolved'
- **Transaction safety**: All changes are atomic

#### Validation
- Similar to StoreIncidentRequest but with nullable photos (not required for updates)
- Authorization check happens automatically via FormRequest
- Conditional validation based on incident type

---

### 4. DELETE

#### Route
```php
DELETE /incidents/{incident}
```

#### Controller Method
```php
public function destroy(Incident $incident, IncidentService $incidentService)
```

#### Process Flow
1. Check authorization (Admin only)
2. `IncidentService::deleteIncident()` is called within a transaction
3. All photos are deleted from storage
4. All videos are deleted from storage
5. All documents are deleted from storage
6. Assigned vehicle is released (status set to 'available')
7. Activity log is recorded
8. Incident is soft-deleted (not permanently removed)
9. User is redirected to incidents index

#### Key Features
- **Admin-only**: Only administrators can delete incidents
- **Soft deletes**: Incidents are marked as deleted but remain in database
- **Media cleanup**: All associated files are removed from storage
- **Vehicle release**: Automatically frees up assigned vehicles
- **Transaction safety**: All cleanup happens atomically
- **Activity logging**: Deletion is recorded with incident details

#### Additional Service Methods

##### Restore Deleted Incident
```php
public function restoreIncident(Incident $incident): bool
```
Restores a soft-deleted incident with activity logging.

##### Force Delete (Permanent)
```php
public function forceDeleteIncident(Incident $incident): bool
```
Permanently removes incident from database (irreversible).

---

## Best Practices Implemented

### 1. **Separation of Concerns**
- **Controllers**: Handle HTTP only, delegate to services
- **Services**: Contain all business logic and complex operations
- **Form Requests**: Handle validation and authorization
- **Models**: Define data structure, relationships, and simple accessors

### 2. **Database Transactions**
All multi-step operations (create, update, delete) are wrapped in transactions:
```php
return DB::transaction(function () use ($data) {
    // Multiple database operations
    // If any fail, all are rolled back
});
```

### 3. **Validation Layer**
- Custom Form Request classes with detailed validation rules
- Type-specific conditional validation
- Custom error messages for better UX
- Authorization built into Form Requests

### 4. **Error Handling**
```php
try {
    // Operation
} catch (\Exception $e) {
    Log::error('Operation failed: ' . $e->getMessage());
    return back()->withInput()->with('error', 'User-friendly message');
}
```

### 5. **Activity Logging**
Every significant action is logged:
```php
activity()
    ->performedOn($incident)
    ->withProperties(['key' => 'value'])
    ->log('Action description');
```

### 6. **Soft Deletes**
- Incidents are never permanently deleted by default
- Maintains data integrity and audit trail
- Can be restored if needed
- Force delete available for permanent removal

### 7. **Eager Loading**
Prevents N+1 query problems:
```php
$incident->load(['assignedStaff', 'assignedVehicle', 'reporter', 'victims']);
```

### 8. **Type Safety**
- Method return types declared
- Parameter types declared
- PHP 8.1+ match expressions for cleaner conditionals

### 9. **Consistent Naming**
- RESTful route names
- Clear method names describing actions
- Consistent variable naming

### 10. **Media Management**
- Organized storage structure (`incident_photos/`, `incident_videos/`)
- File validation (type, size)
- Automatic cleanup on deletion
- Array storage in database for multiple files

---

## Database Structure

### Incidents Table
```sql
- id (primary key)
- incident_number (unique, auto-generated)
- incident_type (enum)
- severity_level (enum)
- status (enum: pending, active, resolved, closed)
- location, municipality, barangay
- latitude, longitude
- description
- incident_date
- weather_condition, road_condition
- casualty_count, injury_count, fatality_count
- property_damage_estimate, damage_description
- vehicle_involved, vehicle_details
- assigned_staff_id (foreign key)
- assigned_vehicle_id (foreign key)
- reported_by (foreign key)
- photos (JSON array)
- videos (JSON array)
- documents (JSON array)
- response_time, resolved_at, resolution_notes

# Type-specific fields (Traffic Accident)
- vehicle_count, license_plates, driver_information

# Type-specific fields (Medical Emergency)
- medical_emergency_type, ambulance_requested
- patient_count, patient_symptoms

# Type-specific fields (Fire Incident)
- building_type, fire_spread_level
- evacuation_required, evacuated_count
- fire_cause, buildings_affected

# Type-specific fields (Natural Disaster)
- disaster_type, affected_area_size
- shelter_needed, families_affected
- structures_damaged, infrastructure_damage

# Type-specific fields (Criminal Activity)
- crime_type, police_notified
- case_number, suspect_description

- created_at, updated_at, deleted_at (soft delete)
```

---

## API Endpoints Summary

| Method | URI | Name | Action |
|--------|-----|------|--------|
| GET | /incidents | incidents.index | List all incidents |
| GET | /incidents/create | incidents.create | Show creation form |
| POST | /incidents | incidents.store | Store new incident |
| GET | /incidents/{incident} | incidents.show | Show incident details |
| GET | /incidents/{incident}/edit | incidents.edit | Show edit form |
| PUT/PATCH | /incidents/{incident} | incidents.update | Update incident |
| DELETE | /incidents/{incident} | incidents.destroy | Delete incident |

### Additional API Routes
- `GET /api/municipalities` - Get all municipalities
- `GET /api/barangays?municipality={name}` - Get barangays for municipality

---

## Authorization Matrix

| Role | Create | Read Own | Read All | Update Own | Update All | Delete |
|------|--------|----------|----------|------------|------------|--------|
| Admin | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Staff | ✓ | ✓ | Municipality Only | ✓ | Municipality Only | ✗ |
| Responder | ✓ | ✓ | Municipality Only | ✓ | Municipality Only | ✗ |

---

## Usage Examples

### Creating an Incident
```php
// In create.blade.php form submits to:
POST /incidents

// Request handled by:
IncidentController@store(StoreIncidentRequest $request, IncidentService $incidentService)

// Service processes:
$incident = $incidentService->createIncident($validatedData);

// Response:
redirect()->route('incidents.show', $incident)
    ->with('success', 'Incident INC-2025-001 reported successfully!');
```

### Updating an Incident
```php
// Quick status update (from show page):
PUT /incidents/123
{
    "maintain_other_fields": true,
    "status": "resolved",
    "resolution_notes": "Issue resolved successfully"
}

// Full update (from edit form):
PUT /incidents/123
{
    "incident_type": "traffic_accident",
    "severity_level": "high",
    // ... all other fields
}
```

### Deleting an Incident
```php
// Admin only:
DELETE /incidents/123

// Soft deletes the incident:
$incidentService->deleteIncident($incident);

// Can be restored later:
$incidentService->restoreIncident($incident);
```

---

## Testing Checklist

### Create
- [ ] Can create incident with all required fields
- [ ] Validates required fields
- [ ] Validates incident type-specific fields
- [ ] Uploads and stores photos correctly
- [ ] Uploads and stores videos correctly
- [ ] Creates victims correctly
- [ ] Assigns vehicle correctly
- [ ] Generates unique incident number
- [ ] Redirects to show page after creation
- [ ] Shows success message

### Read
- [ ] Can view list of incidents
- [ ] Pagination works correctly
- [ ] Filters work (municipality, severity, status, type)
- [ ] Non-admin users only see their municipality
- [ ] Can view individual incident details
- [ ] All relationships are loaded correctly
- [ ] Photos and videos display correctly
- [ ] Access control works (403 for wrong municipality)

### Update
- [ ] Can update incident with all fields
- [ ] Quick status update works
- [ ] Full form update works
- [ ] Validates updated data
- [ ] Authorization works correctly (own municipality only)
- [ ] New photos are added to existing ones
- [ ] Vehicle reassignment works correctly
- [ ] Resolved timestamp is set correctly
- [ ] Shows success message
- [ ] Activity log is recorded

### Delete
- [ ] Only admin can delete
- [ ] Non-admin gets 403 error
- [ ] All photos are deleted from storage
- [ ] All videos are deleted from storage
- [ ] Assigned vehicle is released
- [ ] Incident is soft-deleted (remains in database)
- [ ] Can be restored
- [ ] Force delete removes permanently
- [ ] Activity log is recorded
- [ ] Shows success message

---

## Migration Required

Run this migration to add soft deletes support:

```bash
php artisan migrate
```

This will add the `deleted_at` column to the incidents table.

---

## Future Enhancements

1. **Restore Interface**: Add UI for admins to view and restore deleted incidents
2. **Bulk Operations**: Bulk status updates, bulk assignments
3. **Export Functionality**: Export incidents to PDF, Excel
4. **Advanced Filters**: Date ranges, multiple municipalities, combined filters
5. **Real-time Updates**: WebSocket notifications for new incidents
6. **Mobile API**: RESTful API for mobile app integration
7. **File Management**: UI to remove individual photos/videos from existing incidents
8. **Audit Trail UI**: View complete activity log for each incident
9. **Incident Timeline**: Visual timeline of incident status changes
10. **Map Integration**: Interactive map for incident locations

---

## Troubleshooting

### Common Issues

1. **Photos not uploading**
   - Check storage is linked: `php artisan storage:link`
   - Verify file permissions on `storage/app/public`
   - Check max upload size in `php.ini`

2. **Validation errors**
   - Check browser console for JavaScript errors
   - Verify all required fields are filled
   - Check file sizes don't exceed limits

3. **403 Forbidden on update/delete**
   - Verify user role and municipality
   - Check incident municipality matches user municipality
   - Ensure user is logged in

4. **Transaction rollback**
   - Check Laravel logs: `storage/logs/laravel.log`
   - Look for database constraint violations
   - Verify foreign key relationships exist

---

## Code Quality Metrics

- **Controller Methods**: Clean, focused, delegating to services
- **Service Methods**: Well-documented, transaction-safe
- **Validation**: Comprehensive, type-specific
- **Error Handling**: Try-catch blocks with logging
- **Type Safety**: Return types and parameter types declared
- **Comments**: PHPDoc blocks for all public methods
- **Naming**: Clear, descriptive, following Laravel conventions

---

## Conclusion

This implementation provides a robust, maintainable, and scalable CRUD system for incident management. It follows Laravel best practices and industry standards, making it easy to understand, test, and extend.

For questions or improvements, please refer to the codebase or contact the development team.
