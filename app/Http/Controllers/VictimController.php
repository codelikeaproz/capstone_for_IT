<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Victim;
use App\Models\Incident;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class VictimController extends Controller
{
    public function index(Request $request)
    {
        $query = Victim::with('incident');

        // Filter by municipality if user is not admin
        if (Auth::check() && Auth::user()->role !== 'admin') {
            $query->whereHas('incident', function ($q) {
                $q->where('municipality', Auth::user()->municipality);
            });
        }

        // Apply filters
        if ($request->filled('medical_status')) {
            $query->byMedicalStatus($request->medical_status);
        }

        if ($request->filled('incident_id')) {
            $query->where('incident_id', $request->incident_id);
        }

        $victims = $query->latest()->paginate(15);

        // Get incidents for filter dropdown
        $incidents = Incident::when(Auth::user()->role !== 'admin', function ($q) {
                        return $q->where('municipality', Auth::user()->municipality);
                     })
                     ->latest()
                     ->take(50)
                     ->get();

        return view('Victim.index', compact('victims', 'incidents'));
    }

    public function create(Request $request)
    {
        $incidentId = $request->get('incident_id');
        $incident = null;

        if ($incidentId) {
            $incident = Incident::find($incidentId);

            // Check access permissions
            if ($incident && Auth::user()->role !== 'admin' && Auth::user()->municipality !== $incident->municipality) {
                abort(403, 'You do not have permission to add victims to this incident.');
            }
        }

        $incidents = Incident::when(Auth::user()->role !== 'admin', function ($q) {
                        return $q->where('municipality', Auth::user()->municipality);
                     })
                     ->whereIn('status', ['pending', 'active'])
                     ->latest()
                     ->get();

        return view('Victim.create', compact('incidents', 'incident'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'incident_id' => 'required|exists:incidents,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:0|max:150',
            'gender' => 'nullable|in:male,female,other',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'id_number' => 'nullable|string|max:50',
            'medical_status' => 'required|in:uninjured,minor_injury,major_injury,critical,deceased',
            'injury_description' => 'nullable|string',
            'medical_treatment' => 'nullable|string',
            'hospital_referred' => 'nullable|string|max:255',
            'transportation_method' => 'nullable|in:ambulance,private_vehicle,helicopter,on_foot,other',
            'hospital_arrival_time' => 'nullable|date',
            'victim_role' => 'nullable|in:driver,passenger,pedestrian,cyclist,bystander,other',
            'vehicle_type_involved' => 'nullable|string|max:100',
            'seating_position' => 'nullable|string|max:100',
            'helmet_used' => 'boolean',
            'seatbelt_used' => 'boolean',
            'protective_gear_used' => 'boolean',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'insurance_provider' => 'nullable|string|max:255',
            'insurance_policy_number' => 'nullable|string|max:100',
            'legal_action_required' => 'boolean',
        ]);

        // Check access permissions
        $incident = Incident::find($validated['incident_id']);
        if (Auth::user()->role !== 'admin' && Auth::user()->municipality !== $incident->municipality) {
            abort(403, 'You do not have permission to add victims to this incident.');
        }

        $victim = Victim::create($validated);

        // Update incident casualty count
        $incident->increment('casualty_count');
        if (in_array($validated['medical_status'], ['minor_injury', 'major_injury', 'critical'])) {
            $incident->increment('injury_count');
        }
        if ($validated['medical_status'] === 'deceased') {
            $incident->increment('fatality_count');
        }

        // Log activity
        activity()
            ->performedOn($victim)
            ->withProperties(['incident_number' => $incident->incident_number])
            ->log('Victim record created');

        return redirect()->route('victims.show', $victim)
                        ->with('success', 'Victim record created successfully.');
    }

    public function show(Victim $victim)
    {
        $victim->load('incident');

        // Check access permissions
        if (Auth::user()->role !== 'admin' && Auth::user()->municipality !== $victim->incident->municipality) {
            abort(403, 'You do not have permission to view this victim record.');
        }

        return view('victims.how', compact('victim'));
    }

    public function edit(Victim $victim)
    {
        $victim->load('incident');

        // Check access permissions
        if (Auth::user()->role !== 'admin' && Auth::user()->municipality !== $victim->incident->municipality) {
            abort(403, 'You do not have permission to edit this victim record.');
        }

        $incidents = Incident::when(Auth::user()->role !== 'admin', function ($q) {
                        return $q->where('municipality', Auth::user()->municipality);
                     })
                     ->latest()
                     ->get();

        return view('victims.edit', compact('victim', 'incidents'));
    }

    public function update(Request $request, Victim $victim)
    {
        $victim->load('incident');

        // Check access permissions
        if (Auth::user()->role !== 'admin' && Auth::user()->municipality !== $victim->incident->municipality) {
            abort(403, 'You do not have permission to update this victim record.');
        }

        $validated = $request->validate([
            'incident_id' => 'required|exists:incidents,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:0|max:150',
            'gender' => 'nullable|in:male,female,other',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'id_number' => 'nullable|string|max:50',
            'medical_status' => 'required|in:uninjured,minor_injury,major_injury,critical,deceased',
            'injury_description' => 'nullable|string',
            'medical_treatment' => 'nullable|string',
            'hospital_referred' => 'nullable|string|max:255',
            'transportation_method' => 'nullable|in:ambulance,private_vehicle,helicopter,on_foot,other',
            'hospital_arrival_time' => 'nullable|date',
            'victim_role' => 'nullable|in:driver,passenger,pedestrian,cyclist,bystander,other',
            'vehicle_type_involved' => 'nullable|string|max:100',
            'seating_position' => 'nullable|string|max:100',
            'helmet_used' => 'boolean',
            'seatbelt_used' => 'boolean',
            'protective_gear_used' => 'boolean',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'insurance_provider' => 'nullable|string|max:255',
            'insurance_policy_number' => 'nullable|string|max:100',
            'legal_action_required' => 'boolean',
        ]);

        $oldValues = $victim->toArray();
        $victim->update($validated);

        // Log activity
        activity()
            ->performedOn($victim)
            ->withProperties(['old' => $oldValues, 'attributes' => $validated])
            ->log('Victim record updated');

        return redirect()->route('victims.show', $victim)
                        ->with('success', 'Victim record updated successfully.');
    }

    public function destroy(Victim $victim)
    {
        $victim->load('incident');

        // Only admin can delete victim records
        if (Auth::user()->role !== 'admin') {
            abort(403, 'You do not have permission to delete victim records.');
        }

        $incident = $victim->incident;
        $victimData = $victim->toArray();

        $victim->delete();

        // Update incident casualty counts
        $incident->decrement('casualty_count');
        if (in_array($victimData['medical_status'], ['minor_injury', 'major_injury', 'critical'])) {
            $incident->decrement('injury_count');
        }
        if ($victimData['medical_status'] === 'deceased') {
            $incident->decrement('fatality_count');
        }

        // Log activity
        activity()
            ->withProperties(['victim_data' => $victimData, 'incident_number' => $incident->incident_number])
            ->log('Victim record deleted');

        return redirect()->route('victims.index')
                        ->with('success', 'Victim record deleted successfully.');
    }

    // API Methods
    public function apiIndex(Request $request)
    {
        $query = Victim::with('incident')
                      ->when($request->incident_id, function ($q, $incidentId) {
                          return $q->where('incident_id', $incidentId);
                      })
                      ->when($request->medical_status, function ($q, $status) {
                          return $q->byMedicalStatus($status);
                      });

        $victims = $query->get();

        return response()->json($victims);
    }

    public function updateMedicalStatus(Request $request, Victim $victim)
    {
        $request->validate([
            'medical_status' => 'required|in:uninjured,minor_injury,major_injury,critical,deceased',
            'medical_treatment' => 'nullable|string',
            'hospital_referred' => 'nullable|string',
        ]);

        $oldStatus = $victim->medical_status;
        $victim->update($request->only(['medical_status', 'medical_treatment', 'hospital_referred']));

        // Update incident casualty counts if status changed
        if ($oldStatus !== $request->medical_status) {
            $incident = $victim->incident;

            // Remove old status count
            if (in_array($oldStatus, ['minor_injury', 'major_injury', 'critical'])) {
                $incident->decrement('injury_count');
            }
            if ($oldStatus === 'deceased') {
                $incident->decrement('fatality_count');
            }

            // Add new status count
            if (in_array($request->medical_status, ['minor_injury', 'major_injury', 'critical'])) {
                $incident->increment('injury_count');
            }
            if ($request->medical_status === 'deceased') {
                $incident->increment('fatality_count');
            }
        }

        // Log activity
        activity()
            ->performedOn($victim)
            ->withProperties(['old_status' => $oldStatus, 'new_status' => $request->medical_status])
            ->log('Victim medical status updated');

        return response()->json([
            'success' => true,
            'message' => 'Medical status updated successfully',
            'victim' => $victim->fresh()
        ]);
    }



    // update victim status in victim table and update incident casualty counts
    public function updateVictimStatus(Request $request, Victim $victim){

        $request -> Validate([
            'status' => 'required|in:uninjured,minor_injury,major_injury,critical,deceased',
            'medical_treatment' => 'nullable|string',
            'hospital_referred' => 'nullable|string',
        ]);
        $oldStatus = $victim->medical_status;
        $victim->update($request->only(['medical_status', 'medical_treatment', 'hospital_referred']));
        if ($oldStatus !== $request->medical_status) {
            $incident = $victim->incident;
            if (in_array($oldStatus, ['minor_injury', 'major_injury', 'critical'])) {
                $incident->decrement('injury_count');
            }
            if ($oldStatus === 'deceased') {
                $incident->decrement('fatality_count');
            }
            if (in_array($request->medical_status, ['minor_injury', 'major_injury', 'critical'])) {
                $incident->increment('injury_count');
            }
            if ($request->medical_status === 'deceased') {
                $incident->increment('fatality_count');
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'Victim status updated successfully',
            'victim' => $victim->fresh()
        ]);

    }



}
