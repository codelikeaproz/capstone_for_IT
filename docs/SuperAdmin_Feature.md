# SuperAdmin Feature Implementation

## Overview

The SuperAdmin feature introduces a hierarchical role system that fixes data isolation issues and provides system-wide administrative access. This implementation ensures that:

- **SuperAdmins** have full system access across all municipalities
- **Admins** are restricted to their assigned municipality only
- **Staff, Responders, and Citizens** maintain their existing access levels

## Role Hierarchy

```
┌─────────────────────────────────────────────────────────┐
│ SUPERADMIN (System-wide access)                         │
│ • Full access to all municipalities                     │
│ • Manage all users, incidents, vehicles, victims        │
│ • Create other superadmin users                         │
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│ ADMIN (Municipality-level access)                       │
│ • Access only to their assigned municipality            │
│ • Manage users, incidents, vehicles within municipality │
│ • Cannot create superadmin users                        │
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│ STAFF (MDRRMO Staff)                                    │
│ • Incident management                                   │
│ • Field operations                                      │
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│ RESPONDER (Mobile Field Responder)                      │
│ • Mobile app access                                     │
│ • Field incident reporting                              │
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│ CITIZEN (Public User)                                   │
│ • Submit incident requests                              │
│ • Track request status                                  │
└─────────────────────────────────────────────────────────┘
```

## Problem Solved

### Before (Data Isolation Issue)
Admins could see and manage data from ALL municipalities, creating security and privacy concerns:
- Admin in Municipality A could view incidents from Municipality B
- Admins could manage vehicles from other municipalities
- User management showed all users regardless of municipality

### After (Fixed with SuperAdmin)
- **SuperAdmins**: See all data across all municipalities (intentional)
- **Admins**: See ONLY data from their assigned municipality
- Data is properly filtered by municipality in all controllers
- Clear separation between system-wide and municipality-level access

## Technical Implementation

### 1. Database Migration

**File**: `database/migrations/2025_11_10_104817_add_superadmin_role_support.php`

Adds 'superadmin' role to the users table using PostgreSQL-compatible syntax:

```php
// Drops existing CHECK constraint on role column
// Adds new CHECK constraint including 'superadmin'
DB::statement(
    "ALTER TABLE users ADD CONSTRAINT users_role_check
     CHECK (role::text = ANY (ARRAY['superadmin'::character varying, ...]))"
);
```

**Run Migration**:
```bash
php artisan migrate --force
```

### 2. User Model Updates

**File**: `app/Models/User.php`

New methods added:

```php
// Check if user is SuperAdmin
public function isSuperAdmin(): bool
{
    return $this->role === 'superadmin';
}

// Check if user has admin-level privileges (superadmin or admin)
public function hasAdminPrivileges(): bool
{
    return $this->isSuperAdmin() || $this->isAdmin();
}

// Check if user can access specific municipality data
public function canAccessMunicipality(string $municipality): bool
{
    if ($this->isSuperAdmin()) {
        return true; // SuperAdmins can access all municipalities
    }
    return $this->municipality === $municipality;
}
```

### 3. Middleware

#### SuperAdminMiddleware
**File**: `app/Http/Middleware/SuperAdminMiddleware.php`

Protects routes requiring system-wide access:

```php
if (!Auth::user()->isSuperAdmin()) {
    abort(403, 'Only system administrators can access this page.');
}
```

**Usage**:
```php
Route::middleware(['auth', 'superadmin'])->group(function () {
    // Routes only accessible to SuperAdmins
});
```

#### AdminMiddleware (Updated)
**File**: `app/Http/Middleware/AdminMiddleware.php`

Now allows both superadmin and admin roles:

```php
if (!Auth::user()->hasAdminPrivileges()) {
    abort(403, 'Only administrators can access this page.');
}
```

### 4. Controller Updates (Data Isolation Fixed)

All controllers updated to properly filter data by municipality for admin users:

#### IncidentController
**File**: `app/Http/Controllers/IncidentController.php`

```php
// Index - List incidents
if (Auth::check() && !Auth::user()->isSuperAdmin()) {
    $query->byMunicipality(Auth::user()->municipality);
}

// Create/Edit - Filter staff by municipality
$staff = User::where('role', 'staff')
    ->when(!Auth::user()->isSuperAdmin(), function ($query) {
        return $query->where('municipality', Auth::user()->municipality);
    })
    ->get();

// Show/Edit - Check access permission
if (!Auth::user()->canAccessMunicipality($incident->municipality)) {
    abort(403, 'You do not have permission to view this incident.');
}
```

#### VehicleController
**File**: `app/Http/Controllers/VehicleController.php`

- Index: Filters vehicles by municipality for admins
- Create/Edit: Only shows municipality-specific data
- All CRUD operations check `isSuperAdmin()` for filtering

#### VictimController
**File**: `app/Http/Controllers/VictimController.php`

- Replaced all `Auth::user()->role !== 'admin'` with `!Auth::user()->isSuperAdmin()`
- Ensures admins only see victims from their municipality

#### UserController
**File**: `app/Http/Controllers/UserController.php`

```php
// Stats include superadmins
'superadmins' => User::where('role', 'superadmin')->count(),
'admins' => User::where('role', 'admin')->count(),

// Only superadmin can create/assign superadmin role
$roles = Auth::user()->isSuperAdmin()
    ? ['superadmin', 'admin', 'staff', 'responder', 'citizen']
    : ['admin', 'staff', 'responder', 'citizen'];

// Validation restricts role selection
'role' => [
    'required',
    Rule::in(Auth::user()->isSuperAdmin()
        ? ['superadmin', 'admin', 'staff', 'responder', 'citizen']
        : ['admin', 'staff', 'responder', 'citizen']
    )
],

// Last admin protection updated
if ($user->hasAdminPrivileges() && User::whereIn('role', ['superadmin', 'admin'])->count() <= 1) {
    // Prevent deletion of last admin
}
```

### 5. Artisan Command for User Promotion

**File**: `app/Console/Commands/PromoteToSuperAdmin.php`

Command to promote existing users to SuperAdmin role.

**Usage**:
```bash
# With email argument
php artisan user:promote-superadmin admin@example.com

# Interactive mode (will ask for email)
php artisan user:promote-superadmin
```

**Features**:
- Interactive email input if not provided
- Displays user information before promotion
- Requires confirmation
- Logs activity for audit trail
- Prevents promoting already-promoted users
- Shows summary of role change

**Example Output**:
```
===========================================
  SuperAdmin Promotion Tool
===========================================

User Found:
+--------------+---------------------------+
| Field        | Value                     |
+--------------+---------------------------+
| Name         | John Doe                  |
| Email        | john@example.com          |
| Current Role | ADMIN                     |
| Municipality | Taguig                    |
| Status       | Active                    |
+--------------+---------------------------+

IMPORTANT: SuperAdmin Role grants FULL SYSTEM ACCESS to ALL municipalities!

Are you sure you want to promote this user to SuperAdmin? (yes/no) [no]:
> yes

✅ Successfully promoted user to SuperAdmin!

Role Change Summary:
+--------+------------+
| Before | After      |
+--------+------------+
| ADMIN  | SUPERADMIN |
+--------+------------+

🔑 This user now has:
  • Full system access
  • View all municipalities
  • Manage all users, incidents, vehicles
  • Create other superadmin users
```

## UI/UX Changes (Following MDRRMC Design System)

### Role Badges

**File**: `resources/views/User/index.blade.php` and related views

Role badges follow the design system color scheme:

```php
@if($user->role === 'superadmin')
    <span class="badge badge-error badge-lg">SuperAdmin</span>
@elseif($user->role === 'admin')
    <span class="badge badge-warning">Admin</span>
@elseif($user->role === 'staff')
    <span class="badge badge-primary">Staff</span>
@elseif($user->role === 'responder')
    <span class="badge badge-info">Responder</span>
@else
    <span class="badge badge-neutral">Citizen</span>
@endif
```

**Color Scheme**:
- **SuperAdmin**: Red (`badge-error`) - Large badge to emphasize system-wide access
- **Admin**: Orange/Yellow (`badge-warning`) - Municipality-level admin
- **Staff**: Blue (`badge-primary`) - Primary operational role
- **Responder**: Light Blue (`badge-info`) - Field responder
- **Citizen**: Gray (`badge-neutral`) - Public user

## Security Considerations

### Access Control

1. **Middleware Protection**: Routes are protected by appropriate middleware
2. **Role-based Authorization**: Controllers check user roles before operations
3. **Municipality Filtering**: Data queries automatically filter by municipality for admins
4. **Permission Checks**: `canAccessMunicipality()` validates access rights

### Audit Logging

All SuperAdmin promotions are logged using `spatie/laravel-activitylog`:

```php
activity()
    ->performedOn($user)
    ->withProperties([
        'old_role' => $oldRole,
        'new_role' => 'superadmin',
        'promoted_via' => 'artisan_command'
    ])
    ->log('User promoted to SuperAdmin via console command');
```

### Last Admin Protection

System prevents deletion of the last admin/superadmin:

```php
if ($user->hasAdminPrivileges() && User::whereIn('role', ['superadmin', 'admin'])->count() <= 1) {
    return redirect()->back()->with('error', 'Cannot delete the last administrator.');
}
```

## Testing Checklist

### SuperAdmin Access
- [ ] Can view incidents from all municipalities
- [ ] Can manage vehicles from any municipality
- [ ] Can view and edit all users
- [ ] Can create other superadmin users
- [ ] Can access all dashboard statistics

### Admin Access (Data Isolation)
- [ ] Can ONLY view incidents from their municipality
- [ ] Can ONLY manage vehicles from their municipality
- [ ] Can ONLY see users from their municipality
- [ ] CANNOT create superadmin users
- [ ] Dashboard shows only municipality-specific data

### User Promotion
- [ ] `php artisan user:promote-superadmin` works correctly
- [ ] Promotion is logged in activity log
- [ ] User role badge updates to SuperAdmin
- [ ] User gains system-wide access immediately

### UI/UX
- [ ] Role badges display correct colors per design system
- [ ] SuperAdmin badge is larger and more prominent
- [ ] Forms only show allowed roles based on user permissions
- [ ] Municipality filter hidden for SuperAdmins, shown for Admins

## Files Modified

### Core Implementation
- `database/migrations/2025_11_10_104817_add_superadmin_role_support.php` - Migration
- `app/Models/User.php` - Role methods and logic
- `app/Http/Middleware/SuperAdminMiddleware.php` - New middleware
- `app/Http/Middleware/AdminMiddleware.php` - Updated for dual roles
- `app/Console/Commands/PromoteToSuperAdmin.php` - Promotion command

### Controllers (Data Isolation)
- `app/Http/Controllers/IncidentController.php` - Municipality filtering
- `app/Http/Controllers/VehicleController.php` - Municipality filtering
- `app/Http/Controllers/VictimController.php` - Municipality filtering
- `app/Http/Controllers/UserController.php` - Role management updates

### Configuration
- `app/Http/Kernel.php` - Middleware registration (if needed)

### Views
- `resources/views/User/index.blade.php` - Role badge updates
- Other views displaying user roles

## Usage Examples

### Creating First SuperAdmin

```bash
# Promote an existing admin to superadmin
php artisan user:promote-superadmin admin@mdrrmc.gov

# Or use interactive mode
php artisan user:promote-superadmin
```

### Checking User Permissions in Code

```php
// Check if user is SuperAdmin
if (Auth::user()->isSuperAdmin()) {
    // System-wide operations
}

// Check if user has admin privileges
if (Auth::user()->hasAdminPrivileges()) {
    // Admin-level operations
}

// Check municipality access
if (Auth::user()->canAccessMunicipality($incident->municipality)) {
    // Access granted
}
```

### Filtering Queries by Municipality

```php
// Automatic filtering in controllers
$incidents = Incident::query()
    ->when(!Auth::user()->isSuperAdmin(), function ($query) {
        return $query->byMunicipality(Auth::user()->municipality);
    })
    ->get();
```

## Future Enhancements

1. **Web UI for Promotion**: Add interface for SuperAdmins to promote users
2. **Audit Dashboard**: SuperAdmin-only page showing all system activities
3. **Role Permissions Matrix**: Detailed permission management per role
4. **Municipality Management**: SuperAdmin interface to manage municipalities
5. **System Configuration**: SuperAdmin-only settings panel

## Troubleshooting

### Migration Fails

If migration fails with PostgreSQL syntax error:

```bash
# Check if migration file is using raw SQL (not ->change())
# The current migration uses PostgreSQL-compatible syntax

# Force run migration
php artisan migrate --force
```

### User Cannot See Other Municipalities

**For SuperAdmins**: Verify role is exactly 'superadmin' in database:
```sql
SELECT id, email, role FROM users WHERE email = 'admin@example.com';
```

**For Admins**: This is expected behavior - admins should only see their municipality.

### Role Badge Not Showing Correctly

Clear view cache:
```bash
php artisan view:clear
php artisan cache:clear
```

## Related Documentation

- `PRD.md` - Product Requirements Document
- `MDRRMC_DESIGN_SYSTEM.md` - UI/UX Design Guidelines
- `MEDIA_SYSTEM_OPTIMIZATION.md` - Media Upload System

## Support

For issues or questions:
1. Check the troubleshooting section above
2. Review the testing checklist
3. Verify middleware registration
4. Check activity logs for permission errors

---

**Version**: 1.0
**Last Updated**: 2025-11-10
**Status**: ✅ Implemented - Pending Migration Run
