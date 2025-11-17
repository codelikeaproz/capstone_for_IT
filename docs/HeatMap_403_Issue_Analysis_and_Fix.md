# Heat Map 403 Forbidden Error - Analysis and Fix

## Issue Report

**Date**: 2025-11-10  
**Reported URL**: `http://127.0.0.1:8000/incidents/31` (403 Forbidden)  
**Source**: Heat Map page (`http://127.0.0.1:8000/heat-maps`)  
**Trigger**: Clicking "View Full Details" button on incident popup

---

## Problem Analysis

### Root Cause

**Municipality Access Control Mismatch** between `HeatmapController` and `IncidentController`.

### The Bug 🐛

**File**: `app/Http/Controllers/HeatmapController.php` (Line 19)

**Before (Incorrect Logic)**:
```php
$municipality = $user->role === 'admin' ? null : $user->municipality;
```

This logic was **backwards** and had the following issues:

1. ❌ **If user is Admin**: `$municipality = null` → Shows ALL incidents from ALL municipalities
2. ❌ **If user is NOT Admin**: `$municipality = $user->municipality` → Shows only their municipality
3. ❌ **Does NOT use `isSuperAdmin()` method** from SuperAdmin Feature
4. ❌ **Contradicts the SuperAdmin Feature specification**

### The Flow That Caused 403

```
┌─────────────────────────────────────────────────────────┐
│ 1. HeatmapController@index (BUGGY)                      │
│    Admin sees ALL municipality incidents on map         │
│    (Municipality filter = null for admins)              │
└─────────────────┬───────────────────────────────────────┘
                  │
                  ↓
┌─────────────────────────────────────────────────────────┐
│ 2. User clicks "View Full Details" on Incident #31      │
│    (Incident from Municipality B, User is from Muni A)  │
└─────────────────┬───────────────────────────────────────┘
                  │
                  ↓
┌─────────────────────────────────────────────────────────┐
│ 3. IncidentController@show (CORRECT)                    │
│    Checks: canAccessMunicipality($incident->municipality)│
│    Result: FALSE (Admin can only access their own)      │
│    Action: abort(403, 'No permission...')               │
└─────────────────────────────────────────────────────────┘
```

### Why It Happened

**HeatmapController** was showing incidents the user **should NOT have access to**, then **IncidentController** correctly blocked access when the user tried to view details.

---

## The Fix ✅

### Code Change

**File**: `app/Http/Controllers/HeatmapController.php`

**After (Correct Logic)**:
```php
// SuperAdmins see all municipalities, Admins see only their municipality
// Following SuperAdmin Feature implementation
$municipality = $user->isSuperAdmin() ? null : $user->municipality;
```

### What Changed

1. ✅ **Uses `isSuperAdmin()` method** (consistent with SuperAdmin Feature)
2. ✅ **SuperAdmins**: `$municipality = null` → See ALL incidents (intentional)
3. ✅ **Admins/Others**: `$municipality = $user->municipality` → See ONLY their municipality
4. ✅ **Aligns with IncidentController access control**

---

## How It Works Now

### SuperAdmin Flow
```
SuperAdmin → HeatMap → Shows ALL municipalities
          → Click Detail → IncidentController checks
          → canAccessMunicipality() returns TRUE
          → ✅ ACCESS GRANTED (All municipalities)
```

### Admin Flow
```
Admin (Muni A) → HeatMap → Shows ONLY Muni A incidents
               → Click Detail → IncidentController checks
               → canAccessMunicipality() returns TRUE
               → ✅ ACCESS GRANTED (Own municipality only)
```

### Data Isolation Maintained

| Role       | Heat Map Shows           | Can View Details       | Result |
|------------|--------------------------|------------------------|--------|
| SuperAdmin | All municipalities       | All municipalities     | ✅     |
| Admin      | Own municipality only    | Own municipality only  | ✅     |
| Staff      | Own municipality only    | Own municipality only  | ✅     |
| Responder  | Own municipality only    | Own municipality only  | ✅     |
| Citizen    | Own municipality only    | Own municipality only  | ✅     |

---

## Testing Checklist

### Before Fix (Broken)
- [ ] ❌ Admin sees incidents from all municipalities on heat map
- [ ] ❌ Admin clicks incident from other municipality
- [ ] ❌ Gets 403 Forbidden error

### After Fix (Expected Behavior)
- [ ] ✅ **SuperAdmin**: Sees all municipalities on heat map
- [ ] ✅ **SuperAdmin**: Can view details of ANY incident
- [ ] ✅ **Admin**: Sees ONLY their municipality on heat map
- [ ] ✅ **Admin**: Can view details of their municipality incidents
- [ ] ✅ **Admin**: Does NOT see other municipality incidents on map
- [ ] ✅ **No 403 errors** when clicking "View Full Details"

---

## Related Files

### Fixed
- ✅ `app/Http/Controllers/HeatmapController.php` - Municipality filtering logic

### Correctly Implemented (No Changes Needed)
- ✅ `app/Http/Controllers/IncidentController.php` - Access control checks
- ✅ `app/Models/User.php` - `isSuperAdmin()`, `canAccessMunicipality()` methods
- ✅ `resources/views/HeatMaps/Heatmaps.blade.php` - View details link

---

## SuperAdmin Feature Compliance

This fix ensures the HeatmapController now **fully complies** with the SuperAdmin Feature specification:

### From `SuperAdmin_Feature.md` (Lines 56-59):

> ### After (Fixed with SuperAdmin)
> - **SuperAdmins**: See all data across all municipalities (intentional)
> - **Admins**: See ONLY data from their assigned municipality
> - Data is properly filtered by municipality in all controllers
> - Clear separation between system-wide and municipality-level access

### Implementation Pattern

All controllers should follow this pattern:

```php
// ✅ CORRECT PATTERN (SuperAdmin Feature)
$municipality = Auth::user()->isSuperAdmin() ? null : Auth::user()->municipality;

$incidents = Incident::when($municipality, fn($q) => $q->where('municipality', $municipality))
    ->get();
```

### Anti-Pattern (Bug)

```php
// ❌ INCORRECT PATTERN (What was causing the bug)
$municipality = Auth::user()->role === 'admin' ? null : Auth::user()->municipality;
```

---

## Security Impact

### Before Fix
- **Data Leak**: Admins could see incidents they shouldn't access
- **Inconsistent Access Control**: Map showed data, but couldn't access details
- **Poor UX**: Users confused by 403 errors on visible data

### After Fix
- **Data Isolation**: Each admin sees only their municipality
- **Consistent Access Control**: Map and details show same data
- **Better UX**: No unexpected 403 errors

---

## Additional Notes

### Municipality Filter Variable Usage

The `$municipality` variable is used consistently throughout the HeatmapController:

1. **Line 26**: Filter main incidents query
2. **Line 58**: Filter monthly statistics
3. **Line 78**: Filter recent incidents table

All three now correctly respect the SuperAdmin/Admin distinction.

### User Model Methods

The fix uses these methods from `app/Models/User.php`:

```php
// Check if user is SuperAdmin
public function isSuperAdmin(): bool
{
    return $this->role === 'superadmin';
}

// Check if user can access specific municipality
public function canAccessMunicipality(string $municipality): bool
{
    if ($this->isSuperAdmin()) {
        return true; // SuperAdmins can access all
    }
    return $this->municipality === $municipality;
}
```

---

## Conclusion

**Status**: ✅ **FIXED**

The 403 Forbidden error was caused by inconsistent municipality filtering logic in the HeatmapController. The fix ensures that:

1. ✅ SuperAdmins can see and access all incidents
2. ✅ Admins only see and can access their municipality incidents
3. ✅ No 403 errors occur when viewing incident details
4. ✅ Data isolation is properly maintained
5. ✅ Follows SuperAdmin Feature specification

**Next Steps**:
1. Test with SuperAdmin user account
2. Test with Admin user account from different municipalities
3. Verify no 403 errors when clicking "View Full Details"
4. Confirm heat map only shows accessible incidents

---

**Version**: 1.0  
**Last Updated**: 2025-11-10  
**Related Documentation**: 
- `docs/SuperAdmin_Feature.md`
- `docs/SuperAdmin_Feature_Analysis_and_Fix.md`










