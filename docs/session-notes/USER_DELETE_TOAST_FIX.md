# User Delete Toast Notification Fix

## Problem Identified

The delete toast notification was not appearing in the User Management Index page, while it worked perfectly in the Incident Index page.

### Root Cause Analysis

**User Management Index (Before Fix):**
❌ Delete form used regular form submission (no AJAX)
❌ Controller returned redirect responses only
❌ No toast notifications on delete
❌ User only saw flash messages after page reload

**Incident Index (Working Reference):**
✅ Delete form uses AJAX submission
✅ Controller returns JSON responses for AJAX
✅ Toast notifications shown before redirect
✅ Better user experience with visual feedback

## Solution Implemented

### 1. **Frontend Changes** (`resources/views/User/Management/Index.blade.php`)

#### Added AJAX Delete Handler
```javascript
// Handle delete form submission with AJAX
document.addEventListener('DOMContentLoaded', function() {
    const deleteForm = document.getElementById('deleteForm');
    if (deleteForm) {
        let isDeleting = false;

        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (isDeleting) {
                console.log('Delete already in progress...');
                return;
            }

            isDeleting = true;

            // Disable buttons and show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const cancelBtn = this.querySelector('button[type="button"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            cancelBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';

            // Submit via AJAX
            const formData = new FormData(this);
            const action = this.action;

            fetch(action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || `HTTP ${response.status}: ${response.statusText}`);
                    }).catch(() => {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Close modal
                    setTimeout(() => {
                        deleteModal.close();
                    }, 100);

                    // Show success toast AFTER modal closes
                    setTimeout(() => {
                        showSuccessToast(data.message || 'User deleted successfully!');
                    }, 200);

                    // Redirect after toast is visible
                    setTimeout(() => {
                        window.location.href = '{{ route('users.index') }}';
                    }, 2000);
                } else {
                    // Re-enable form and show error
                    submitBtn.disabled = false;
                    cancelBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    isDeleting = false;
                    deleteModal.close();

                    setTimeout(() => {
                        showErrorToast(data.message || 'Failed to delete user.');
                    }, 200);
                }
            })
            .catch(error => {
                // Handle errors
                submitBtn.disabled = false;
                cancelBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                isDeleting = false;
                deleteModal.close();

                setTimeout(() => {
                    showErrorToast(error.message || 'An error occurred while deleting the user.');
                }, 200);
                console.error('Delete error:', error);
            });
        });
    }
});
```

**Key Features:**
- ✅ Prevents double-submission with `isDeleting` flag
- ✅ Shows loading state with spinner
- ✅ Disables buttons during operation
- ✅ Closes modal before showing toast (proper z-index management)
- ✅ Shows toast notification with 200ms delay for smooth transition
- ✅ Redirects after 2 seconds (gives user time to see the toast)
- ✅ Comprehensive error handling

### 2. **Backend Changes** (`app/Http/Controllers/UserController.php`)

#### Updated `destroy()` Method to Support AJAX

**Before:**
```php
public function destroy(User $user)
{
    // ... validation ...
    
    $user->delete();
    
    // Always returns redirect
    return redirect()
        ->route('users.index')
        ->with('success', "User {$userName} deleted successfully!");
}
```

**After:**
```php
public function destroy(Request $request, User $user)
{
    // Check if user is admin
    if (!Auth::user()->isAdmin()) {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        abort(403, 'Only administrators can delete users.');
    }

    // Prevent deleting own account
    if ($user->id === Auth::id()) {
        $errorMessage = 'You cannot delete your own account.';
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => $errorMessage], 400);
        }
        return back()->with('error', $errorMessage);
    }

    // Prevent deleting last admin
    if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
        $errorMessage = 'Cannot delete the last administrator account.';
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => $errorMessage], 400);
        }
        return back()->with('error', $errorMessage);
    }

    $userName = $user->full_name;
    $userId = $user->id;
    $userData = $user->toArray();

    try {
        $user->delete();

        // Log activity
        activity()
            ->withProperties(['attributes' => $userData])
            ->log('User deleted by admin');

        $successMessage = "User {$userName} deleted successfully!";

        // Return JSON response for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'user_id' => $userId
            ]);
        }

        // Return redirect for regular requests (backwards compatible)
        return redirect()
            ->route('users.index')
            ->with('success', $successMessage);
    } catch (\Exception $e) {
        Log::error('User deletion failed', [
            'user_id' => $userId,
            'error' => $e->getMessage()
        ]);

        $errorMessage = 'An error occurred while deleting the user.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }

        return back()->with('error', $errorMessage);
    }
}
```

**Key Improvements:**
- ✅ Detects AJAX requests using `$request->wantsJson()` or `$request->ajax()`
- ✅ Returns JSON responses for AJAX requests
- ✅ Returns redirect responses for regular form submissions (backwards compatible)
- ✅ All error conditions handled for both AJAX and regular requests
- ✅ Comprehensive try-catch block with logging
- ✅ Returns `user_id` in success response for UI updates

## How It Works

### Delete Flow (Step-by-Step)

1. **User clicks Delete button** → Modal opens with user name
2. **User confirms deletion** → Form submit event triggered
3. **JavaScript intercepts submit** → `e.preventDefault()` stops normal submission
4. **AJAX request sent** → FormData with CSRF token to `/users/{id}`
5. **Controller receives request** → Detects AJAX via headers
6. **Validation checks** → Own account, last admin, permissions
7. **User deleted** → Database operation with activity logging
8. **JSON response sent** → `{ success: true, message: "...", user_id: 123 }`
9. **Modal closes** → 100ms delay
10. **Toast appears** → 200ms delay (after modal animation)
11. **Success message displayed** → Green toast with checkmark
12. **Page redirects** → 2000ms delay (2 seconds to see toast)
13. **Fresh list loaded** → User sees updated user list

### Error Handling

**If deletion fails:**
- Modal closes
- Error toast appears (red with error icon)
- Form buttons re-enabled
- No page redirect
- Error logged to console

**Validation errors handled:**
- "You cannot delete your own account"
- "Cannot delete the last administrator account"
- "An error occurred while deleting the user" (for exceptions)

## User Experience Improvements

### Before Fix:
1. Click Delete → Modal opens
2. Confirm → Page reloads
3. See flash message at top
4. **Total time: ~2-3 seconds, no visual feedback**

### After Fix:
1. Click Delete → Modal opens
2. Confirm → Button shows spinner "Deleting..."
3. Modal smoothly closes
4. Toast appears with success message
5. User sees confirmation for 2 seconds
6. Page refreshes with updated list
7. **Total time: ~2-3 seconds, but with clear visual feedback at each step**

## Toast Functions Used

The fix uses the global toast functions from `app.blade.php`:

```javascript
showSuccessToast(message)  // Green toast with checkmark
showErrorToast(message)    // Red toast with error icon
```

These are already defined in the layout and available globally.

## Testing Checklist

- [x] Delete user shows success toast
- [x] Delete own account shows error toast
- [x] Delete last admin shows error toast
- [x] Modal closes smoothly before toast
- [x] Loading spinner appears during deletion
- [x] Buttons disabled during operation
- [x] No double-submission possible
- [x] Page redirects after toast display
- [x] Works for both AJAX and non-AJAX requests
- [x] Activity logging still works
- [x] No linter errors

## Files Modified

1. `resources/views/User/Management/Index.blade.php`
   - Added AJAX delete handler
   - Improved UX with loading states
   - Toast notifications on success/error

2. `app/Http/Controllers/UserController.php`
   - Updated `destroy()` method
   - Added AJAX detection
   - JSON responses for AJAX requests
   - Backwards compatible with regular form submissions

## Comparison with Incident Index

Both now follow the same pattern:
- ✅ AJAX-based deletion
- ✅ Toast notifications
- ✅ Modal management
- ✅ Loading states
- ✅ Error handling
- ✅ Activity logging
- ✅ Smooth animations

The User Management delete functionality now matches the Incident Management implementation in terms of user experience and code quality.

## Notes for Future Development

1. This pattern can be applied to other delete operations
2. Toast timing (200ms for modal, 2000ms for redirect) is optimized for readability
3. The `isDeleting` flag prevents race conditions
4. All error cases return meaningful messages
5. Activity logging preserved for audit trail

---

**Status:** ✅ Complete  
**Date:** October 22, 2025  
**Impact:** High - Improves user experience across admin interface

