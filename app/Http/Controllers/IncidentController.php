<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\LocationService;
use App\Services\IncidentService;
use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        $query = Incident::with(['assignedStaff', 'assignedVehicle', 'reporter']);

        // Filter by municipality if user is not superadmin
        // SuperAdmin sees all, Admin/Staff/Responder see only their municipality
        if (Auth::check() && !Auth::user()->isSuperAdmin()) {
            $query->byMunicipality(Auth::user()->municipality);
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('incident_number', 'LIKE', "%{$search}%")
                  ->orWhere('location', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('incident_type', 'LIKE', "%{$search}%");
            });
        }

        // SuperAdmin can filter by municipality
        if ($request->filled('municipality') && Auth::user()->isSuperAdmin()) {
            $query->byMunicipality($request->municipality);
        }

        if ($request->filled('incident_type')) {
            $query->where('incident_type', $request->incident_type);
        }

        $incidents = $query->latest('incident_date')->paginate(15)->withQueryString();

        return view('Incident.index', compact('incidents'));
    }

    public function create()
    {
        $staff = User::where('role', 'staff')
            ->where('is_active', true)
            ->when(!Auth::user()->isSuperAdmin(), function ($query) {
                return $query->where('municipality', Auth::user()->municipality);
            })
            ->get();

        $vehicles = Vehicle::where('status', 'available')
            ->when(!Auth::user()->isSuperAdmin(), function ($query) {
                return $query->where('municipality', Auth::user()->municipality);
            })
            ->get();

        return view('Incident.create', compact('staff', 'vehicles'));
    }

    public function store(StoreIncidentRequest $request, IncidentService $incidentService)
    {
        // DEBUG: Log that we reached the controller
        Log::info('=== INCIDENT STORE REACHED ===');
        Log::info('User authenticated: ' . (auth()->check() ? 'YES' : 'NO'));
        Log::info('User ID: ' . (auth()->check() ? auth()->id() : 'N/A'));
        Log::info('Request method: ' . $request->method());
        Log::info('Request data keys: ' . implode(', ', array_keys($request->all())));

        try {
            // Process license plates input (comma-separated to array)
            $validated = $request->validated();

            // DEBUG: Log successful validation
            Log::info('Validation passed successfully');
            Log::info('Validated data keys: ' . implode(', ', array_keys($validated)));

            if (isset($validated['license_plates_input']) && !empty($validated['license_plates_input'])) {
                $validated['license_plates'] = array_map('trim', explode(',', $validated['license_plates_input']));
                unset($validated['license_plates_input']);
            }

            // Create incident using service
            $incident = $incidentService->createIncident($validated);

            return redirect()
                ->route('incidents.index')
                ->with('success', "Incident {$incident->incident_number} reported successfully!");

        } catch (\Exception $e) {
            Log::error('Failed to create incident: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to create incident. Please check all fields and try again. Error: ' . $e->getMessage());
        }
    }

    public function show(Incident $incident)
    {
        $incident->load(['assignedStaff', 'assignedVehicle', 'reporter', 'victims']);

        // Check access permissions - superadmin can view all, others only their municipality
        if (!Auth::user()->canAccessMunicipality($incident->municipality)) {
            abort(403, 'You do not have permission to view this incident.');
        }

        return view('Incident.show', compact('incident'));
    }

    public function edit(Incident $incident)
    {
        // Check access permissions - superadmin can edit all, others only their municipality
        if (!Auth::user()->canAccessMunicipality($incident->municipality)) {
            abort(403, 'You do not have permission to edit this incident.');
        }

        $staff = User::where('role', 'staff')
            ->where('is_active', true)
            ->when(!Auth::user()->isSuperAdmin(), function ($query) {
                return $query->where('municipality', Auth::user()->municipality);
            })
            ->get();

        $vehicles = Vehicle::where('status', 'available')
            ->when(!Auth::user()->isSuperAdmin(), function ($query) {
                return $query->where('municipality', Auth::user()->municipality);
            })
            ->get();

        // Get municipalities from LocationService
        $municipalities = \App\Services\LocationService::getMunicipalities();

        // Get barangays for the incident's current municipality
        $barangays = [];
        if ($incident->municipality) {
            $barangays = \App\Services\LocationService::getBarangays($incident->municipality);
        }

        return view('Incident.edit', compact('incident', 'staff', 'vehicles', 'municipalities', 'barangays'));
    }

    public function update(UpdateIncidentRequest $request, Incident $incident, IncidentService $incidentService)
    {
        try {
            // Get validated data
            $validated = $request->validated();

            // If this is a quick status update
            if ($request->has('maintain_other_fields')) {
                // Set resolved_at when status changes to resolved
                if ($validated['status'] === 'resolved' && $incident->status !== 'resolved') {
                    $validated['resolved_at'] = now();
                }

                $incident->update($validated);

                // Log activity
                activity()
                    ->performedOn($incident)
                    ->withProperties(['status' => $validated['status']])
                    ->log('Incident status updated');

                return redirect()
                    ->route('incidents.show', $incident)
                    ->with('success', 'Incident status updated successfully!');
            }

            // Full update - process license plates
            if (isset($validated['license_plates_input']) && !empty($validated['license_plates_input'])) {
                $validated['license_plates'] = array_map('trim', explode(',', $validated['license_plates_input']));
                unset($validated['license_plates_input']);
            }

            // Set resolved_at when status changes to resolved
            if (isset($validated['status']) && $validated['status'] === 'resolved' && $incident->status !== 'resolved') {
                $validated['resolved_at'] = now();
            }

            // Update incident using service
            $incident = $incidentService->updateIncident($incident, $validated);

            return redirect()
                ->route('incidents.show', $incident)
                ->with('success', 'Incident updated successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to update incident: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to update incident. Please check all fields and try again. Error: ' . $e->getMessage());
        }
    }

    public function destroy(Incident $incident, IncidentService $incidentService)
    {
        // Check if incident is already soft deleted
        if ($incident->trashed()) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This incident has already been deleted.'
                ], 410); // 410 Gone - resource permanently deleted
            }

            return redirect()
                ->route('incidents.index')
                ->with('warning', 'This incident has already been deleted.');
        }

        // Only superadmin and admin can delete incidents
        if (!Auth::user()->hasAdminPrivileges()) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete incidents.'
                ], 403);
            }
            abort(403, 'You do not have permission to delete incidents.');
        }

        try {
            $incidentNumber = $incident->incident_number;
            $incidentId = $incident->id;

            // Delete incident using service
            $incidentService->deleteIncident($incident);

            // Return JSON for AJAX requests
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Incident {$incidentNumber} has been deleted successfully!",
                    'incident_id' => $incidentId
                ]);
            }

            // Regular redirect for non-AJAX requests
            return redirect()
                ->route('incidents.index')
                ->with('success', "Incident {$incidentNumber} has been deleted successfully!");

        } catch (\Exception $e) {
            Log::error('Failed to delete incident: ' . $e->getMessage(), [
                'incident_id' => $incident->id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete incident. Please try again.',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

            return back()
                ->with('error', 'Failed to delete incident. Please try again.');
        }
    }

    // API Methods for mobile and AJAX
    public function apiIndex(Request $request)
    {
        $query = Incident::with(['assignedStaff', 'assignedVehicle'])
            ->when($request->municipality, function ($q, $municipality) {
                return $q->byMunicipality($municipality);
            })
            ->when($request->severity, function ($q, $severity) {
                return $q->bySeverity($severity);
            })
            ->when($request->status, function ($q, $status) {
                return $q->byStatus($status);
            });

        $incidents = $query->latest('incident_date')->get();

        return response()->json($incidents);
    }

    public function updateStatus(Request $request, Incident $incident)
    {
        $request->validate([
            'status' => 'required|in:pending,active,resolved,closed',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $incident->status;
        $incident->update([
            'status' => $request->status,
            'resolution_notes' => $request->notes,
            'resolved_at' => $request->status === 'resolved' ? now() : null,
        ]);

        // Log activity
        activity()
            ->performedOn($incident)
            ->withProperties([
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'notes' => $request->notes
            ])
            ->log('Incident status updated');

        // Return JSON response with toast notification message
        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully!',
            'incident' => $incident->fresh()
        ]);
    }

    /**
     * Get the list of municipalities for use in views.
     *
     * @deprecated Use LocationService::getMunicipalities() instead
     * @return array
     */
    public static function municipalities()
    {
        return LocationService::getMunicipalitiesForSelect();
    }

    /**
     * Get barangays for a specific municipality (API endpoint).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBarangays(Request $request)
    {
        $request->validate([
            'municipality' => 'required|string',
        ]);

        $municipality = $request->input('municipality');
        $barangays = LocationService::getBarangays($municipality);

        return response()->json([
            'success' => true,
            'municipality' => $municipality,
            'barangays' => $barangays,
        ]);
    }

    /**
     * Get all municipalities (API endpoint).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMunicipalities()
    {
        $municipalities = LocationService::getMunicipalities();

        return response()->json([
            'success' => true,
            'municipalities' => $municipalities,
        ]);
    }
}




