# Staff Role Testing Guide

## Quick Testing Checklist

This guide helps you test and verify the Staff role implementation and compare it with Admin role access.

---

## Prerequisites

### 1. Create Test Users

Run these commands to create test users:

```bash
php artisan tinker
```

```php
// Create Admin Test User
\App\Models\User::create([
    'first_name' => 'Admin',
    'last_name' => 'Test',
    'email' => 'admin@test.com',
    'password' => bcrypt('password123'),
    'role' => 'admin',
    'municipality' => 'Malaybalay',
    'is_active' => true,
    'email_verified_at' => now(),
]);

// Create Staff Test User - Malaybalay
\App\Models\User::create([
    'first_name' => 'Staff',
    'last_name' => 'Malaybalay',
    'email' => 'staff.malaybalay@test.com',
    'password' => bcrypt('password123'),
    'role' => 'staff',
    'municipality' => 'Malaybalay',
    'is_active' => true,
    'email_verified_at' => now(),
]);

// Create Staff Test User - Valencia
\App\Models\User::create([
    'first_name' => 'Staff',
    'last_name' => 'Valencia',
    'email' => 'staff.valencia@test.com',
    'password' => bcrypt('password123'),
    'role' => 'staff',
    'municipality' => 'Valencia',
    'is_active' => true,
    'email_verified_at' => now(),
]);
```

---

## Test Scenarios

### Test 1: Dashboard Access

#### Admin Dashboard
1. Login as `admin@test.com` / `password123`
2. Navigate to `/admin-dashboard`
3. ✅ Should display system-wide statistics
4. ✅ Should show all municipalities
5. ✅ Should display user activity logs

#### Staff Dashboard
1. Login as `staff.malaybalay@test.com` / `password123`
2. Navigate to `/staff-dashboard`
3. ✅ Should display "Malaybalay Municipality" header
4. ✅ Should show only Malaybalay statistics
5. ✅ Should display "My Assigned Incidents"
6. ✅ Should show team activity (same municipality)

---

### Test 2: User Management Access

#### Admin Access
1. Login as Admin
2. Navigate to `/users`
3. ✅ Should display user management page
4. ✅ Should see "Create New User" button
5. ✅ Should list all users
6. Click on a user
7. ✅ Should access user detail page
8. ✅ Should see edit/delete options

#### Staff Access (Should Fail)
1. Login as Staff
2. Navigate to `/users`
3. ✅ Should see **403 Forbidden** error
4. Try accessing `/users/create`
5. ✅ Should see **403 Forbidden** error
6. Check sidebar
7. ✅ "User Management" section should **NOT be visible**

---

### Test 3: Incident Management

#### Admin Access (All Municipalities)
1. Login as Admin
2. Navigate to `/incidents`
3. ✅ Should see incidents from **all municipalities**
4. Check municipality filter dropdown
5. ✅ Should list all municipalities
6. Create new incident
7. ✅ Can select any municipality

#### Staff Access (Own Municipality Only)
1. Login as Staff (Malaybalay)
2. Navigate to `/incidents`
3. ✅ Should see **only Malaybalay** incidents
4. Check municipality filter
5. ✅ Should be **locked to Malaybalay**
6. Create new incident
7. ✅ Municipality should be **pre-selected** as Malaybalay
8. ✅ Cannot change municipality

#### Cross-Municipality Test
1. Login as Staff (Malaybalay)
2. Note an incident ID from Valencia (as admin first)
3. Login as Staff (Malaybalay)
4. Try to access `/incidents/{valencia-incident-id}`
5. ✅ Should be **blocked or show 403**

---

### Test 4: Vehicle Management

#### Admin Access
1. Login as Admin
2. Navigate to `/vehicles`
3. ✅ See all vehicles from all municipalities
4. Create a new vehicle
5. ✅ Can assign to any municipality
6. Try to delete a vehicle
7. ✅ Should **succeed** with confirmation

#### Staff Access
1. Login as Staff (Malaybalay)
2. Navigate to `/vehicles`
3. ✅ See only Malaybalay vehicles
4. Create a new vehicle
5. ✅ Municipality pre-selected as Malaybalay
6. Try to delete a vehicle
7. ✅ Should see **403 Forbidden** (Admin only)
8. Edit a vehicle
9. ✅ Should **succeed** (can edit, not delete)

---

### Test 5: Victim Management

#### Staff Access
1. Login as Staff (Malaybalay)
2. Navigate to `/victims`
3. ✅ See victims linked to Malaybalay incidents
4. Create new victim
5. ✅ Should succeed
6. Edit victim
7. ✅ Should succeed for own municipality
8. Delete victim
9. ✅ Should succeed for own municipality

---

### Test 6: Sidebar Navigation

#### Admin Sidebar
1. Login as Admin
2. Check sidebar menu
3. ✅ Should see:
   - Dashboard
   - Incidents
   - Vehicles
   - Victims
   - Requests
   - **User Management** ⭐
   - Analytics
   - Reports
   - System Logs ⭐

#### Staff Sidebar
1. Login as Staff
2. Check sidebar menu
3. ✅ Should see:
   - Dashboard
   - Incidents
   - Vehicles
   - Victims
   - Requests
   - Analytics (limited)
   - Reports (limited)
4. ✅ Should **NOT** see:
   - ❌ User Management
   - ❌ System Logs

---

### Test 7: Assignment Restrictions

#### Staff Can Only Assign Within Municipality
1. Login as Staff (Malaybalay)
2. Create or edit an incident
3. Check "Assigned Staff" dropdown
4. ✅ Should **only show staff** from Malaybalay
5. ✅ Should **NOT show** staff from other municipalities
6. Check "Assigned Vehicle" dropdown
7. ✅ Should **only show vehicles** from Malaybalay

---

### Test 8: Analytics & Reports

#### Admin Access
1. Login as Admin
2. Navigate to `/reports`
3. ✅ Can generate reports for **all municipalities**
4. ✅ Can compare municipalities
5. Navigate to `/system-logs`
6. ✅ Can view system activity logs

#### Staff Access
1. Login as Staff
2. Navigate to `/reports`
3. ✅ Can generate reports for **own municipality only**
4. Navigate to `/system-logs`
5. ✅ Should see **403 Forbidden** or no access

---

## Expected Behavior Summary

### ✅ Staff CAN:
- View/Create/Edit/Delete incidents in their municipality
- View/Create/Edit vehicles in their municipality
- Assign vehicles to incidents
- View/Create/Edit/Delete victims in their municipality
- Access staff dashboard
- View analytics for their municipality
- Generate reports for their municipality

### ❌ Staff CANNOT:
- Access user management
- View/modify data from other municipalities
- Delete vehicles (Admin only)
- Access system logs
- View system-wide analytics
- Change their assigned municipality
- Create users or modify roles

---

## Automated Testing Script

Create this test file: `tests/Feature/RoleBasedAccessTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleBasedAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_access_user_management()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/users');

        $response->assertStatus(200);
    }

    /** @test */
    public function staff_cannot_access_user_management()
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)->get('/users');

        $response->assertStatus(403);
    }

    /** @test */
    public function staff_can_only_see_own_municipality_incidents()
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'municipality' => 'Malaybalay'
        ]);

        $response = $this->actingAs($staff)->get('/incidents');

        $response->assertStatus(200);
        // Add assertions to check filtered data
    }

    /** @test */
    public function staff_cannot_delete_vehicles()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $vehicle = \App\Models\Vehicle::factory()->create();

        $response = $this->actingAs($staff)->delete("/vehicles/{$vehicle->id}");

        $response->assertStatus(403);
    }
}
```

Run tests:
```bash
php artisan test --filter=RoleBasedAccessTest
```

---

## Troubleshooting

### Issue: Staff can see other municipalities
**Solution:** Check that controllers have municipality filtering:
```php
if (Auth::user()->role !== 'admin') {
    $query->where('municipality', Auth::user()->municipality);
}
```

### Issue: Middleware not working
**Solution:** Clear route cache:
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

### Issue: 403 errors for valid access
**Solution:** Check middleware registration in `bootstrap/app.php`

---

## Success Criteria

### ✅ Implementation Complete When:
- [ ] Admin can access all features
- [ ] Staff can access municipality-scoped features
- [ ] Staff blocked from user management
- [ ] Staff blocked from other municipalities
- [ ] Staff cannot delete vehicles
- [ ] Proper dashboards for each role
- [ ] Sidebar shows correct menu items per role
- [ ] All tests pass

---

## Production Checklist

Before deploying to production:

- [ ] Remove test users from database
- [ ] Ensure default passwords are changed
- [ ] Verify all existing users have correct roles
- [ ] Test with real data
- [ ] Confirm activity logging works
- [ ] Check error pages display correctly
- [ ] Verify mobile responsiveness
- [ ] Test logout functionality
- [ ] Confirm session expiry works
- [ ] Review all console errors

---

**Testing Date:** _______________
**Tested By:** _______________
**Status:** ⬜ Pass / ⬜ Fail
**Notes:** _______________________________________________

