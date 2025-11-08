<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as HttpRequest;
use App\Models\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RequestController extends Controller
{
    public function index(HttpRequest $request)
    {
        $query = Request::with(['assignedStaff', 'approvedBy']);

        // Filter by municipality if user is not admin
        if (Auth::check() && Auth::user()->role !== 'admin') {
            $query->byMunicipality(Auth::user()->municipality);
        }

        // Apply filters
        if ($request->filled('municipality')) {
            $query->byMunicipality($request->municipality);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('urgency_level')) {
            $query->byUrgency($request->urgency_level);
        }

        if ($request->filled('request_type')) {
            $query->where('request_type', $request->request_type);
        }

        $requests = $query->latest()->paginate(15);

        // Statistics
        $stats = [
            'total' => $requests->total(),
            'pending' => Request::pending()->count(),
            'processing' => Request::processing()->count(),
            'completed' => Request::completed()->count(),
            'urgent' => Request::whereIn('urgency_level', ['high', 'critical'])->count(),
        ];

        return view('Request.index', compact('requests', 'stats'));
    }

    public function create()
    {
        // Public form for citizens to submit requests
        return view('Request.create');
    }

    public function store(HttpRequest $request)
    {
        $validated = $request->validate([
            'requester_name' => 'required|string|max:255',
            'requester_email' => 'required|email|max:255',
            'requester_phone' => 'required|string|max:20',
            'requester_id_number' => 'nullable|string|max:50',
            'requester_address' => 'required|string',
            'request_type' => 'required|in:incident_report,traffic_accident_report,medical_emergency_report,fire_incident_report,general_emergency_report,vehicle_accident_report',
            'urgency_level' => 'required|in:low,medium,high,critical',
            'request_description' => 'required|string',
            'purpose_of_request' => 'nullable|string',
            'incident_case_number' => 'nullable|string|max:100',
            'incident_date' => 'nullable|date',
            'incident_location' => 'nullable|string|max:255',
            'municipality' => 'required|string|max:255',
            'email_notifications_enabled' => 'boolean',
            'sms_notifications_enabled' => 'boolean',
        ]);

        // Generate request number
        $validated['request_number'] = Request::generateRequestNumber();
        $validated['status'] = 'pending';

        $citizenRequest = Request::create($validated);

        // Log activity
        activity()
            ->performedOn($citizenRequest)
            ->withProperties(['requester' => $validated['requester_name']])
            ->log('Citizen request submitted');

        return redirect()->route('requests.status', $citizenRequest->request_number)
                        ->with('success', 'Your request has been submitted successfully. Request ID: ' . $citizenRequest->request_number);
    }

    public function show(Request $request)
    {
        $request->load(['assignedStaff', 'approvedBy']);

        // Check access permissions
        if (Auth::check() && Auth::user()->role !== 'admin' && Auth::user()->municipality !== $request->municipality) {
            abort(403, 'You do not have permission to view this request.');
        }

        return view('Request.show', compact('request'));
    }

    public function edit(Request $request)
    {
        $request->load(['assignedStaff', 'approvedBy']);

        // Check access permissions
        if (Auth::check() && Auth::user()->role !== 'admin' && Auth::user()->municipality !== $request->municipality) {
            abort(403, 'You do not have permission to edit this request.');
        }

        $staff = User::where('role', 'staff')
                    ->where('is_active', true)
                    ->when(Auth::user()->role !== 'admin', function ($query) {
                        return $query->where('municipality', Auth::user()->municipality);
                    })
                    ->get();

        return view('Request.edit', compact('request', 'staff'));
    }

    public function update(HttpRequest $httpRequest, Request $request)
    {
        // Check access permissions
        if (Auth::user()->role !== 'admin' && Auth::user()->municipality !== $request->municipality) {
            abort(403, 'You do not have permission to update this request.');
        }

        $validated = $httpRequest->validate([
            'status' => 'required|in:pending,processing,approved,rejected,completed',
            'assigned_staff_id' => 'nullable|exists:users,id',
            'urgency_level' => 'required|in:low,medium,high,critical',
            'approval_notes' => 'nullable|string',
            'rejection_reason' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        $oldValues = $request->toArray();

        // Handle status changes
        if ($validated['status'] === 'approved' && $request->status !== 'approved') {
            $request->approve(Auth::id(), $validated['approval_notes'] ?? null);

            // Auto-create incident if checkbox is checked
            if ($httpRequest->has('auto_create_incident') && $httpRequest->auto_create_incident) {
                $incident = $request->createIncidentFromRequest();

                // Log incident creation
                activity()
                    ->performedOn($incident)
                    ->withProperties(['request_number' => $request->request_number])
                    ->log('Incident auto-created from approved request');
            }
        } elseif ($validated['status'] === 'rejected' && $request->status !== 'rejected') {
            $request->reject(Auth::id(), $validated['rejection_reason'] ?? 'No reason provided');
        } elseif ($validated['status'] === 'completed' && $request->status !== 'completed') {
            $request->markAsCompleted();
        } else {
            $request->update($validated);
        }

        // Assign staff if provided
        if ($validated['assigned_staff_id'] && $request->assigned_staff_id !== $validated['assigned_staff_id']) {
            $request->assignToStaff($validated['assigned_staff_id']);
        }

        // Log activity
        activity()
            ->performedOn($request)
            ->withProperties(['old' => $oldValues, 'attributes' => $validated])
            ->log('Request updated');

        return redirect()->route('requests.show', $request)
                        ->with('success', 'Request updated successfully.');
    }

    public function destroy(Request $request)
    {
        // Only admin can delete requests
        if (Auth::user()->role !== 'admin') {
            abort(403, 'You do not have permission to delete requests.');
        }

        $requestData = $request->toArray();
        $request->delete();

        // Log activity
        activity()
            ->withProperties(['request_data' => $requestData])
            ->log('Request deleted');

        return redirect()->route('requests.index')
                        ->with('success', 'Request deleted successfully.');
    }

    // Public status checking
    public function checkStatus(HttpRequest $request, $requestNumber = null)
    {
        if (!$requestNumber) {
            return view('Request.status-check');
        }

        $citizenRequest = Request::where('request_number', $requestNumber)->first();

        if (!$citizenRequest) {
            return view('Request.status-check', ['error' => 'Request not found.']);
        }

        return view('Request.status', compact('citizenRequest'));
    }

    // Staff assignment
    public function assign(HttpRequest $request, Request $citizenRequest)
    {
        $request->validate([
            'staff_id' => 'required|exists:users,id',
        ]);

        $citizenRequest->assignToStaff($request->staff_id);

        // Log activity
        activity()
            ->performedOn($citizenRequest)
            ->withProperties(['assigned_to' => $request->staff_id])
            ->log('Request assigned to staff');

        return response()->json([
            'success' => true,
            'message' => 'Request assigned successfully',
            'request' => $citizenRequest->fresh()
        ]);
    }

    // Bulk operations
    public function bulkApprove(HttpRequest $request)
    {
        $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:requests,id',
            'approval_notes' => 'nullable|string',
        ]);

        $requests = Request::whereIn('id', $request->request_ids)->get();

        foreach ($requests as $citizenRequest) {
            if ($citizenRequest->status === 'pending' || $citizenRequest->status === 'processing') {
                $citizenRequest->approve(Auth::id(), $request->approval_notes);
            }
        }

        // Log activity
        activity()
            ->withProperties(['approved_count' => count($request->request_ids)])
            ->log('Bulk request approval');

        return response()->json([
            'success' => true,
            'message' => count($request->request_ids) . ' requests approved successfully'
        ]);
    }

    public function bulkReject(HttpRequest $request)
    {
        $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:requests,id',
            'rejection_reason' => 'required|string',
        ]);

        $requests = Request::whereIn('id', $request->request_ids)->get();

        foreach ($requests as $citizenRequest) {
            if ($citizenRequest->status === 'pending' || $citizenRequest->status === 'processing') {
                $citizenRequest->reject(Auth::id(), $request->rejection_reason);
            }
        }

        // Log activity
        activity()
            ->withProperties(['rejected_count' => count($request->request_ids)])
            ->log('Bulk request rejection');

        return response()->json([
            'success' => true,
            'message' => count($request->request_ids) . ' requests rejected successfully'
        ]);
    }

    // API Methods
    public function apiIndex(HttpRequest $request)
    {
        $query = Request::with(['assignedStaff', 'approvedBy'])
                       ->when($request->municipality, function ($q, $municipality) {
                           return $q->byMunicipality($municipality);
                       })
                       ->when($request->status, function ($q, $status) {
                           return $q->byStatus($status);
                       })
                       ->when($request->urgency_level, function ($q, $urgency) {
                           return $q->byUrgency($urgency);
                       });

        $requests = $query->latest()->get();

        return response()->json($requests);
    }

    // Search incidents by victim name for request verification
    public function searchIncidentsByVictim(HttpRequest $request)
    {
        $request->validate([
            'victim_name' => 'required|string|min:2',
        ]);

        $victimName = $request->victim_name;

        // Search for victims matching the name
        $query = \App\Models\Victim::with(['incident' => function ($q) {
            $q->select('id', 'incident_number', 'incident_type', 'incident_date', 'location', 'municipality', 'severity_level', 'status');
        }])
        ->where(function ($q) use ($victimName) {
            $q->where('first_name', 'ILIKE', "%{$victimName}%")
              ->orWhere('last_name', 'ILIKE', "%{$victimName}%")
              ->orWhereRaw("CONCAT(first_name, ' ', last_name) ILIKE ?", ["%{$victimName}%"]);
        })
        ->whereHas('incident'); // Only victims with incidents

        // Filter by municipality if user is not admin
        if (Auth::check() && Auth::user()->role !== 'admin') {
            $query->whereHas('incident', function ($q) {
                $q->where('municipality', Auth::user()->municipality);
            });
        }

        $victims = $query->limit(20)->get();

        // Group by incident to avoid duplicates
        $incidents = $victims->groupBy('incident_id')->map(function ($victimsGroup) {
            $incident = $victimsGroup->first()->incident;
            return [
                'incident_id' => $incident->id,
                'incident_number' => $incident->incident_number,
                'incident_type' => $incident->incident_type,
                'incident_date' => $incident->incident_date->format('Y-m-d H:i'),
                'location' => $incident->location,
                'municipality' => $incident->municipality,
                'severity_level' => $incident->severity_level,
                'status' => $incident->status,
                'victims' => $victimsGroup->map(function ($victim) {
                    return [
                        'id' => $victim->id,
                        'name' => $victim->full_name,
                        'age' => $victim->age,
                        'gender' => $victim->gender,
                        'medical_status' => $victim->medical_status,
                    ];
                })->values(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'count' => $incidents->count(),
            'incidents' => $incidents,
        ]);
    }
}
