<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of users with filters and search
     */
    public function index(Request $request)
    {
        // Check if user has admin privileges (superadmin or admin)
        if (!Auth::user()->hasAdminPrivileges()) {
            abort(403, 'Only administrators can access user management.');
        }

        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by municipality
        if ($request->filled('municipality')) {
            $query->where('municipality', $request->municipality);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by email verification
        if ($request->filled('email_verified')) {
            if ($request->email_verified === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->email_verified === 'unverified') {
                $query->whereNull('email_verified_at');
            }
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(15)->withQueryString();

        // Get statistics
        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'superadmins' => User::where('role', 'superadmin')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'staff' => User::where('role', 'staff')->count(),
            'responders' => User::where('role', 'responder')->count(),
            'citizens' => User::where('role', 'citizen')->count(),
        ];

        return view('User.Management.Index', compact('users', 'stats'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        // Check if user has admin privileges (superadmin or admin)
        if (!Auth::user()->hasAdminPrivileges()) {
            abort(403, 'Only administrators can create users.');
        }

        $municipalities = LocationService::getMunicipalitiesForSelect();
        // Only superadmin can create/assign superadmin role
        $roles = Auth::user()->isSuperAdmin()
            ? ['superadmin', 'admin', 'staff', 'responder', 'citizen']
            : ['admin', 'staff', 'responder', 'citizen'];

        return view('User.Management.Create', compact('municipalities', 'roles'));
    }

    /**
     * Store a newly created user in storage
     */
    public function store(Request $request)
    {
        // Check if user has admin privileges (superadmin or admin)
        if (!Auth::user()->hasAdminPrivileges()) {
            abort(403, 'Only administrators can create users.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => [
                'required',
                Rule::in(Auth::user()->isSuperAdmin()
                    ? ['superadmin', 'admin', 'staff', 'responder', 'citizen']
                    : ['admin', 'staff', 'responder', 'citizen']
                )
            ],
            'municipality' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        // Hash password
        $validated['password'] = Hash::make($validated['password']);

        // Set default active status if not provided
        $validated['is_active'] = $request->has('is_active') ? true : false;

        // Create user
        $user = User::create($validated);

        // Log activity
        activity()
            ->performedOn($user)
            ->withProperties(['attributes' => $validated])
            ->log('User created by admin');

        return redirect()
            ->route('users.index')
            ->with('success', "User {$user->full_name} created successfully!");
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        // Check if user has admin privileges (superadmin or admin)
        if (!Auth::user()->hasAdminPrivileges()) {
            abort(403, 'Only administrators can view user details.');
        }

        // Load relationships
        $user->load([
            'reportedIncidents',
            'assignedIncidents',
            'assignedVehicles',
            'approvedRequests',
            'assignedRequests'
        ]);

        // Get activity logs
        $activities = $user->activities()
            ->latest()
            ->take(20)
            ->get();

        return view('User.Management.Show', compact('user', 'activities'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        // Check if user has admin privileges (superadmin or admin)
        if (!Auth::user()->hasAdminPrivileges()) {
            abort(403, 'Only administrators can edit users.');
        }

        $municipalities = LocationService::getMunicipalitiesForSelect();
        // Only superadmin can create/assign superadmin role
        $roles = Auth::user()->isSuperAdmin()
            ? ['superadmin', 'admin', 'staff', 'responder', 'citizen']
            : ['admin', 'staff', 'responder', 'citizen'];

        return view('User.Management.Edit', compact('user', 'municipalities', 'roles'));
    }

    /**
     * Update the specified user in storage
     */
    public function update(Request $request, User $user)
    {
        // Check if user has admin privileges (superadmin or admin)
        if (!Auth::user()->hasAdminPrivileges()) {
            abort(403, 'Only administrators can update users.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => [
                'required',
                Rule::in(Auth::user()->isSuperAdmin()
                    ? ['superadmin', 'admin', 'staff', 'responder', 'citizen']
                    : ['admin', 'staff', 'responder', 'citizen']
                )
            ],
            'municipality' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        // Only update password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Handle is_active checkbox
        $validated['is_active'] = $request->has('is_active');

        $oldValues = $user->toArray();
        $user->update($validated);

        // Log activity
        activity()
            ->performedOn($user)
            ->withProperties(['old' => $oldValues, 'attributes' => $validated])
            ->log('User updated by admin');

        return redirect()
            ->route('users.show', $user)
            ->with('success', "User {$user->full_name} updated successfully!");
    }

    /**
     * Remove the specified user from storage
     */
    public function destroy(Request $request, User $user)
    {
        // Check if user has admin privileges (superadmin or admin)
        if (!Auth::user()->hasAdminPrivileges()) {
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

        // Prevent deleting last admin (superadmin or admin)
        if ($user->hasAdminPrivileges() && User::whereIn('role', ['superadmin', 'admin'])->count() <= 1) {
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

            // Return redirect for regular requests
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

    /**
     * Assign role to user
     */
    public function assignRole(Request $request, User $user)
    {
        // Check if user has admin privileges (superadmin or admin)
        if (!Auth::user()->hasAdminPrivileges()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'role' => [
                'required',
                Rule::in(Auth::user()->isSuperAdmin()
                    ? ['superadmin', 'admin', 'staff', 'responder', 'citizen']
                    : ['admin', 'staff', 'responder', 'citizen']
                )
            ],
        ]);

        $oldRole = $user->role;
        $user->update(['role' => $validated['role']]);

        // Log activity
        activity()
            ->performedOn($user)
            ->withProperties([
                'old_role' => $oldRole,
                'new_role' => $validated['role']
            ])
            ->log('User role changed by admin');

        return response()->json([
            'success' => true,
            'message' => "Role updated to {$validated['role']} successfully!",
            'user' => $user->fresh()
        ]);
    }

    /**
     * Assign municipality to user
     */
    public function assignMunicipality(Request $request, User $user)
    {
        // Check if user has admin privileges (superadmin or admin)
        if (!Auth::user()->hasAdminPrivileges()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'municipality' => 'required|string|max:255',
        ]);

        $oldMunicipality = $user->municipality;
        $user->update(['municipality' => $validated['municipality']]);

        // Log activity
        activity()
            ->performedOn($user)
            ->withProperties([
                'old_municipality' => $oldMunicipality,
                'new_municipality' => $validated['municipality']
            ])
            ->log('User municipality changed by admin');

        return response()->json([
            'success' => true,
            'message' => "Municipality updated to {$validated['municipality']} successfully!",
            'user' => $user->fresh()
        ]);
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        // Check if user has admin privileges (superadmin or admin)
        if (!Auth::user()->hasAdminPrivileges()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Prevent deactivating own account
        if ($user->id === Auth::id()) {
            return response()->json(['error' => 'You cannot deactivate your own account.'], 400);
        }

        // Prevent deactivating last active admin (superadmin or admin)
        if ($user->hasAdminPrivileges() && $user->is_active && User::whereIn('role', ['superadmin', 'admin'])->where('is_active', true)->count() <= 1) {
            return response()->json(['error' => 'Cannot deactivate the last active administrator.'], 400);
        }

        $newStatus = !$user->is_active;
        $user->update(['is_active' => $newStatus]);

        // Log activity
        activity()
            ->performedOn($user)
            ->withProperties([
                'old_status' => !$newStatus,
                'new_status' => $newStatus
            ])
            ->log('User status changed by admin');

        return response()->json([
            'success' => true,
            'message' => $newStatus ? 'User activated successfully!' : 'User deactivated successfully!',
            'is_active' => $newStatus,
            'user' => $user->fresh()
        ]);
    }

    /**
     * Reset user password (admin function)
     */
    public function resetPassword(Request $request, User $user)
    {
        // Check if user has admin privileges (superadmin or admin)
        if (!Auth::user()->hasAdminPrivileges()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);

        // Log activity
        activity()
            ->performedOn($user)
            ->log('Password reset by admin');

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully!',
        ]);
    }

    /**
     * Unlock user account
     */
    public function unlockAccount(User $user)
    {
        // Check if user has admin privileges (superadmin or admin)
        if (!Auth::user()->hasAdminPrivileges()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);

        // Log activity
        activity()
            ->performedOn($user)
            ->log('Account unlocked by admin');

        return response()->json([
            'success' => true,
            'message' => 'Account unlocked successfully!',
        ]);
    }

    /**
     * Verify user email (admin function)
     */
    public function verifyEmail(User $user)
    {
        // Check if user has admin privileges (superadmin or admin)
        if (!Auth::user()->hasAdminPrivileges()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['error' => 'Email already verified.'], 400);
        }

        $user->markEmailAsVerified();

        // Log activity
        activity()
            ->performedOn($user)
            ->log('Email verified by admin');

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully!',
        ]);
    }
}
