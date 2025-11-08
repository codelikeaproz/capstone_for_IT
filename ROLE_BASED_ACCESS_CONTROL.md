# Role-Based Access Control (RBAC) Implementation

## Overview
This document outlines the role-based access control system implemented in the MDRRMC Incident Management System. The system supports 4 user roles with different permission levels.

## User Roles

### 1. **Admin Role**
- **Full System Access**: Complete control over all system features
- **Municipality**: Can access ALL municipalities
- **User Management**: Full CRUD operations on all users

#### Admin Permissions:
✅ **Incident Management**
- View all incidents across all municipalities
- Create, edit, and delete incidents
- Assign incidents to any staff member
- Access soft-deleted incidents

✅ **Vehicle Management**
- View all vehicles across all municipalities
- Create, edit, and delete vehicles
- Assign vehicles to incidents
- Manage vehicle maintenance

✅ **Victim Management**
- View all victims across all municipalities
- Create, edit, and delete victim records
- Update victim status

✅ **User Management** (Admin Exclusive)
- Create new users
- Edit user information
- Assign roles and municipalities
- Toggle user active/inactive status
- Reset passwords
- Unlock accounts
- Verify user emails
- View user activity logs

✅ **Analytics & Reports**
- System-wide analytics
- Municipality comparison reports
- Performance metrics
- Activity logs

✅ **System Administration**
- Access system logs
- View system health
- Configure system settings

---

### 2. **Staff Role**
- **Municipality-Restricted Access**: Can only access their assigned municipality
- **Operational Tasks**: Handle incidents, vehicles, and victims in their municipality
- **No User Management**: Cannot manage users

#### Staff Permissions:
✅ **Incident Management**
- View incidents in their municipality only
- Create new incidents
- Edit incidents in their municipality
- Delete incidents in their municipality (soft delete)
- Assign incidents to staff in same municipality

✅ **Vehicle Management**
- View vehicles in their municipality only
- Create new vehicles
- Edit vehicles in their municipality
- Assign/release vehicles to incidents
- Update vehicle maintenance
- ❌ Cannot delete vehicles (Admin only)

✅ **Victim Management**
- View victims in their municipality only
- Create new victim records
- Edit victim information
- Update victim status
- Delete victim records

✅ **Request Management**
- View requests in their municipality
- Create and process requests
- Assign requests to staff in same municipality

✅ **Dashboard Access**
- Staff-specific dashboard
- Municipality-level statistics
- Personal assigned tasks
- Team activity in same municipality

❌ **Restrictions:**
- Cannot access other municipalities' data
- Cannot manage users
- Cannot access system-wide settings
- Cannot delete vehicles
- Cannot access system logs

---

### 3. **Responder Role**
- **Field Operations**: Mobile-optimized interface for emergency response
- **Real-time Updates**: GPS tracking and incident updates

#### Responder Permissions:
✅ Active incident viewing in their municipality
✅ Update incident status
✅ Update victim status
✅ Vehicle assignment tracking
✅ Mobile-optimized dashboard

---

### 4. **Citizen Role**
- **Public Access**: Request assistance and check status
- **Limited View**: Only their own requests

#### Citizen Permissions:
✅ Submit assistance requests
✅ Check request status
✅ View public incident information

---

## Implementation Details

### Middleware Protection

#### Files Created:
1. `app/Http/Middleware/RoleMiddleware.php` - Generic role checker
2. `app/Http/Middleware/AdminMiddleware.php` - Admin-only access
3. `app/Http/Middleware/StaffMiddleware.php` - Staff & Admin access

#### Middleware Registration:
```php
// bootstrap/app.php
$middleware->alias([
    'role' => \App\Http\Middleware\RoleMiddleware::class,
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
    'staff' => \App\Http\Middleware\StaffMiddleware::class,
]);
```

### Route Protection

#### Admin-Only Routes:
```php
Route::middleware('admin')->group(function () {
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/assign-role', ...);
    Route::post('/users/{user}/toggle-status', ...);
    // ... all user management routes
});
```

#### Staff & Admin Routes:
```php
Route::middleware('staff')->group(function () {
    // Incidents
    Route::get('/incidents', [IncidentController::class, 'index']);
    Route::post('/incidents', [IncidentController::class, 'store']);

    // Vehicles
    Route::get('/vehicles', [VehicleController::class, 'index']);
    Route::post('/vehicles', [VehicleController::class, 'store']);

    // Victims
    Route::get('/victims', [VictimController::class, 'index']);
    Route::post('/victims', [VictimController::class, 'store']);
});
```

### Controller-Level Municipality Filtering

Staff users automatically have their queries filtered by municipality:

```php
// Example from IncidentController
if (Auth::check() && Auth::user()->role !== 'admin') {
    $query->byMunicipality(Auth::user()->municipality);
}
```

This pattern is applied in:
- `IncidentController` (routes/web.php:24)
- `VehicleController`
- `VictimController`
- `RequestController`
- `DashboardController` (routes/web.php:20)

---

## User Model Helper Methods

Located in `app/Models/User.php` (lines 145-163):

```php
public function isAdmin()
{
    return $this->role === 'admin';
}

public function isStaff()
{
    return $this->role === 'staff';
}

public function isResponder()
{
    return $this->role === 'responder';
}

public function isCitizen()
{
    return $this->role === 'citizen';
}

public function canAccessMunicipality($municipality)
{
    return $this->isAdmin() || $this->municipality === $municipality;
}
```

---

## Dashboard Routing

Each role has a dedicated dashboard:

| Role | Route | Middleware | View |
|------|-------|------------|------|
| Admin | `/admin-dashboard` | `admin` | `User.Admin.AdminDashboard` |
| Staff | `/staff-dashboard` | `staff` | `User.Staff.StaffDashBoard` |
| Responder | `/responder-dashboard` | `role:responder,admin` | `User.Responder.RespondersDashBoard` |
| General | `/dashboard` | `auth` | `Dashboard.index` |

---

## Permission Comparison Table

| Feature | Admin | Staff | Responder | Citizen |
|---------|-------|-------|-----------|---------|
| **Incident Management** |
| View All Municipalities | ✅ | ❌ | ❌ | ❌ |
| View Own Municipality | ✅ | ✅ | ✅ | ❌ |
| Create Incidents | ✅ | ✅ | ✅ | ❌ |
| Edit Incidents | ✅ | ✅ (own muni) | ✅ (status only) | ❌ |
| Delete Incidents | ✅ | ✅ (own muni) | ❌ | ❌ |
| **Vehicle Management** |
| View Vehicles | ✅ | ✅ (own muni) | ✅ (assigned) | ❌ |
| Create Vehicles | ✅ | ✅ | ❌ | ❌ |
| Edit Vehicles | ✅ | ✅ (own muni) | ❌ | ❌ |
| Delete Vehicles | ✅ | ❌ | ❌ | ❌ |
| Assign Vehicles | ✅ | ✅ | ❌ | ❌ |
| **Victim Management** |
| View Victims | ✅ | ✅ (own muni) | ✅ (own muni) | ❌ |
| Create Victims | ✅ | ✅ | ✅ | ❌ |
| Edit Victims | ✅ | ✅ (own muni) | ✅ (status) | ❌ |
| Delete Victims | ✅ | ✅ (own muni) | ❌ | ❌ |
| **User Management** |
| View Users | ✅ | ❌ | ❌ | ❌ |
| Create Users | ✅ | ❌ | ❌ | ❌ |
| Edit Users | ✅ | ❌ | ❌ | ❌ |
| Delete Users | ✅ | ❌ | ❌ | ❌ |
| Assign Roles | ✅ | ❌ | ❌ | ❌ |
| **System Features** |
| System Logs | ✅ | ❌ | ❌ | ❌ |
| Analytics (All) | ✅ | ❌ | ❌ | ❌ |
| Analytics (Own Muni) | ✅ | ✅ | ❌ | ❌ |
| Reports | ✅ | ✅ (own muni) | ❌ | ❌ |

---

## Testing Role-Based Access

### Test Admin Access:
1. Login as admin user
2. Navigate to `/users` - Should display user management
3. Access any municipality's incidents
4. View system logs at `/system-logs`

### Test Staff Access:
1. Login as staff user
2. Navigate to `/users` - Should see 403 Forbidden
3. View incidents - Only shows your municipality
4. Try accessing another municipality's data - Should be filtered out
5. Attempt to delete a vehicle - Should see 403 Forbidden

### Test Differences:
```bash
# Login as Admin
- Can access: http://localhost/users
- Can see: All municipalities in dropdowns
- Dashboard: System-wide statistics

# Login as Staff
- Cannot access: http://localhost/users (403 Forbidden)
- Can see: Only own municipality
- Dashboard: Municipality-specific statistics
```

---

## Security Considerations

### 1. **Double-Layer Protection**
- Middleware at route level
- Additional checks in controllers

### 2. **Municipality Filtering**
- Staff users automatically filtered by municipality
- Cannot bypass using query parameters

### 3. **Activity Logging**
- All actions logged with `spatie/laravel-activitylog`
- Includes user role and IP address

### 4. **Soft Deletes**
- Incidents use soft deletes for audit trail
- Only admins can permanently delete

---

## Migration Path

### Existing Users:
- All existing users retain their current roles
- Municipality assignments remain unchanged
- No data migration required

### New Users:
- Created through admin panel only
- Default role: 'staff'
- Must assign municipality during creation

---

## Future Enhancements

### Potential Additions:
1. **Permission-based system** - More granular control beyond roles
2. **Multi-municipality staff** - Allow staff to access multiple municipalities
3. **Role hierarchy** - Define inheritance between roles
4. **Custom role creation** - Allow admins to create custom roles
5. **API token permissions** - Role-based API access

---

## Related Files

### Controllers:
- `app/Http/Controllers/UserController.php` - User management (Admin only)
- `app/Http/Controllers/IncidentController.php` - Incident CRUD (Staff + Admin)
- `app/Http/Controllers/VehicleController.php` - Vehicle CRUD (Staff + Admin)
- `app/Http/Controllers/VictimController.php` - Victim CRUD (Staff + Admin)
- `app/Http/Controllers/DashboardController.php` - Role-specific dashboards

### Models:
- `app/Models/User.php` - Role helper methods

### Views:
- `resources/views/User/Admin/AdminDashboard.blade.php`
- `resources/views/User/Staff/StaffDashBoard.blade.php`
- `resources/views/User/Responder/RespondersDashBoard.blade.php`

### Middleware:
- `app/Http/Middleware/RoleMiddleware.php`
- `app/Http/Middleware/AdminMiddleware.php`
- `app/Http/Middleware/StaffMiddleware.php`

### Routes:
- `routes/web.php` - All route definitions with middleware

### Database:
- `database/migrations/0001_01_01_000000_create_users_table.php` (line 21)

---

## Support

For questions or issues regarding role-based access control, please refer to:
- PRD Document: `prompt/PRD.md` (line 454)
- User Management Documentation: `prompt/USER_MANAGEMENT_IMPLEMENTATION_COMPLETE.md`

---

**Last Updated:** October 24, 2025
**Implementation Status:** ✅ Complete
