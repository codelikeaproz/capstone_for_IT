<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        $query = Incident::with(['assignedStaff', 'assignedVehicle', 'reporter']);

        // Filter by municipality if user is not admin
        if (Auth::check() && Auth::user()->role !== 'admin') {
            $query->byMunicipality(Auth::user()->municipality);
        }

        // Apply filters
        if ($request->filled('municipality')) {
            $query->byMunicipality($request->municipality);
        }

        if ($request->filled('severity')) {
            $query->bySeverity($request->severity);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('incident_type')) {
            $query->where('incident_type', $request->incident_type);
        }

        $incidents = $query->latest('incident_date')->paginate(15);

        return view('Incident.index', compact('incidents'));
    }

    public function create()
    {
        $staff = User::where('role', 'staff')
                    ->where('is_active', true)
                    ->when(Auth::user()->role !== 'admin', function ($query) {
                        return $query->where('municipality', Auth::user()->municipality);
                    })
                    ->get();

        $vehicles = Vehicle::where('status', 'available')
                          ->when(Auth::user()->role !== 'admin', function ($query) {
                              return $query->where('municipality', Auth::user()->municipality);
                          })
                          ->get();

        return view('Incident.create', compact('staff', 'vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'incident_type' => 'required|in:traffic_accident,medical_emergency,fire_incident,natural_disaster,criminal_activity,other',
            'severity_level' => 'required|in:critical,high,medium,low',
            'location' => 'required|string|max:255',
            'municipality' => 'required|string|max:255',
            'barangay' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'description' => 'required|string',
            'incident_date' => 'required|date',
            'weather_condition' => 'nullable|in:clear,cloudy,rainy,stormy,foggy',
            'road_condition' => 'nullable|in:dry,wet,slippery,damaged,under_construction',
            'casualty_count' => 'integer|min:0',
            'injury_count' => 'integer|min:0',
            'fatality_count' => 'integer|min:0',
            'property_damage_estimate' => 'nullable|numeric|min:0',
            'damage_description' => 'nullable|string',
            'vehicle_involved' => 'boolean',
            'vehicle_details' => 'nullable|string',
            'assigned_staff_id' => 'nullable|exists:users,id',
            'assigned_vehicle_id' => 'nullable|exists:vehicles,id',
            'photos' => 'required|array|max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Max 5 photos, 2MB each
            'videos' => 'nullable|array|max:2',
            'videos.*' => 'mimes:mp4,webm,mov,quicktime|max:10240', // Max 2 videos, 10MB each
        ], [
            'photos.required' => 'Please upload at least one photo of the incident.',
            'photos.max' => 'You can upload a maximum of 5 photos.',
            'photos.*.image' => 'All photo files must be valid images.',
            'photos.*.mimes' => 'Photos must be in JPG, PNG, or GIF format.',
            'photos.*.max' => 'Each photo must not exceed 2MB in size.',
            'videos.max' => 'You can upload a maximum of 2 videos.',
            'videos.*.mimes' => 'Videos must be in MP4, WebM, or MOV format.',
            'videos.*.max' => 'Each video must not exceed 10MB in size.',
        ]);

        // Handle photo uploads
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('incident_photos', 'public');
                $photoPaths[] = $path;
            }
        }

        // Handle video uploads
        $videoPaths = [];
        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $video) {
                $path = $video->store('incident_videos', 'public');
                $videoPaths[] = $path;
            }
        }

        // Generate incident number and set default values
        $validated['incident_number'] = Incident::generateIncidentNumber();
        $validated['reported_by'] = Auth::id();
        $validated['status'] = 'pending';
        $validated['photos'] = $photoPaths;
        $validated['videos'] = $videoPaths;

        $incident = Incident::create($validated);

        // Log activity
        activity()
            ->performedOn($incident)
            ->withProperties(['attributes' => $validated])
            ->log('Incident created');

        return redirect()->route('incidents.show', $incident)
                        ->with('success', 'Incident reported successfully!');
    }

    public function show(Incident $incident)
    {
        $incident->load(['assignedStaff', 'assignedVehicle', 'reporter', 'victims']);

        // Check access permissions
        if (Auth::user()->role !== 'admin' && Auth::user()->municipality !== $incident->municipality) {
            abort(403, 'You do not have permission to view this incident.');
        }

        return view('Incident.show', compact('incident'));
    }

    public function edit(Incident $incident)
    {
        // Check access permissions
        if (Auth::user()->role !== 'admin' && Auth::user()->municipality !== $incident->municipality) {
            abort(403, 'You do not have permission to edit this incident.');
        }

        $staff = User::where('role', 'staff')
                    ->where('is_active', true)
                    ->when(Auth::user()->role !== 'admin', function ($query) {
                        return $query->where('municipality', Auth::user()->municipality);
                    })
                    ->get();

        $vehicles = Vehicle::where('status', 'available')
                          ->when(Auth::user()->role !== 'admin', function ($query) {
                              return $query->where('municipality', Auth::user()->municipality);
                          })
                          ->get();

        return view('Incident.edit', compact('incident', 'staff', 'vehicles'));
    }

    public function update(Request $request, Incident $incident)
    {
        // Check access permissions
        if (Auth::user()->role !== 'admin' && Auth::user()->municipality !== $incident->municipality) {
            abort(403, 'You do not have permission to update this incident.');
        }

        // If maintain_other_fields is set, only update status and resolution_notes
        if ($request->has('maintain_other_fields')) {
            $validated = $request->validate([
                'status' => 'required|in:pending,active,resolved,closed',
                'resolution_notes' => 'nullable|string',
            ]);

            // Set resolved_at when status changes to resolved
            if ($validated['status'] === 'resolved' && $incident->status !== 'resolved') {
                $validated['resolved_at'] = now();
            }
        } else {
            // Full validation for complete form updates
            $validated = $request->validate([
                'incident_type' => 'required|in:traffic_accident,medical_emergency,fire_incident,natural_disaster,criminal_activity,other',
                'severity_level' => 'required|in:critical,high,medium,low',
                'status' => 'required|in:pending,active,resolved,closed',
                'location' => 'required|string|max:255',
                'municipality' => 'required|string|max:255',
                'barangay' => 'required|string|max:255',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'description' => 'required|string',
                'incident_date' => 'required|date',
                'weather_condition' => 'nullable|in:clear,cloudy,rainy,stormy,foggy',
                'road_condition' => 'nullable|in:dry,wet,slippery,damaged,under_construction',
                'casualty_count' => 'integer|min:0',
                'injury_count' => 'integer|min:0',
                'fatality_count' => 'integer|min:0',
                'property_damage_estimate' => 'nullable|numeric|min:0',
                'damage_description' => 'nullable|string',
                'vehicle_involved' => 'boolean',
                'vehicle_details' => 'nullable|string',
                'assigned_staff_id' => 'nullable|exists:users,id',
                'assigned_vehicle_id' => 'nullable|exists:vehicles,id',
                'resolution_notes' => 'nullable|string',
                'photos' => 'nullable|array|max:5',
                'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Max 5 photos, 2MB each
            ]);

            // Handle photo uploads
            $photoPaths = $incident->photos ?? [];
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('incident_photos', 'public');
                    $photoPaths[] = $path;
                }
            }
            $validated['photos'] = $photoPaths;

            // Set resolved_at when status changes to resolved
            if ($validated['status'] === 'resolved' && $incident->status !== 'resolved') {
                $validated['resolved_at'] = now();
            }
        }

        $oldValues = $incident->toArray();
        $incident->update($validated);

        // Log activity
        activity()
            ->performedOn($incident)
            ->withProperties(['old' => $oldValues, 'attributes' => $validated])
            ->log('Incident updated');

        return redirect()->route('incidents.show', $incident)
                        ->with('success', 'Incident updated successfully!');
    }

    public function destroy(Incident $incident)
    {
        // Only admin can delete incidents
        if (Auth::user()->role !== 'admin') {
            return back()->with('error', 'You do not have permission to delete incidents.');
        }

        $incidentData = $incident->toArray();
        $incident->delete();

        // Log activity
        activity()
            ->withProperties(['attributes' => $incidentData])
            ->log('Incident deleted');

        return redirect()->route('incidents.index')
                        ->with('success', 'Incident deleted successfully!');
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




