<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\Incident;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::with(['assignedDriver', 'currentIncident']);
        
        // Filter by municipality if user is not admin
        $userMunicipality = Auth::user()->municipality;
        if (Auth::check() && Auth::user()->role !== 'admin') {
            $query->byMunicipality($userMunicipality);
        }
        
        // Apply filters
        if ($request->filled('municipality')) {
            $query->byMunicipality($request->municipality);
        }
        
        if ($request->filled('vehicle_type')) {
            $query->byType($request->vehicle_type);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $vehicles = $query->latest()->paginate(12);
        
        // Statistics for dashboard - calculate based on current user's access
        $statsQuery = Vehicle::query();
        if (Auth::user()->role !== 'admin') {
            $statsQuery->byMunicipality($userMunicipality);
        }
        
        $stats = [
            'total' => $statsQuery->count(),
            'available' => (clone $statsQuery)->where('status', 'available')->count(),
            'in_use' => (clone $statsQuery)->where('status', 'in_use')->count(),
            'maintenance' => (clone $statsQuery)->where('status', 'maintenance')->count(),
            'low_fuel' => (clone $statsQuery)->where('current_fuel_level', '<', 25)->count(),
        ];
        
        // Get available incidents for vehicle assignment
        $incidentsQuery = Incident::whereIn('status', ['pending', 'active'])
                                ->whereNull('assigned_vehicle_id');
        
        if (Auth::user()->role !== 'admin') {
            $incidentsQuery->byMunicipality($userMunicipality);
        }
        
        $incidents = $incidentsQuery->orderBy('severity_level', 'desc')
                                  ->orderBy('incident_date', 'desc')
                                  ->get();
        
        return view('Vehicle.index', compact('vehicles', 'stats', 'incidents'));
    }
    
    public function create()
    {
        $drivers = User::where('role', 'responder')
                      ->where('is_active', true)
                      ->when(Auth::user()->role !== 'admin', function ($query) {
                          return $query->where('municipality', Auth::user()->municipality);
                      })
                      ->get();
        
        return view('Vehicle.create', compact('drivers'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_number' => 'required|string|unique:vehicles,vehicle_number',
            'license_plate' => 'required|string|unique:vehicles,license_plate',
            'vehicle_type' => 'required|in:ambulance,fire_truck,rescue_vehicle,patrol_car,support_vehicle',
            'make' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'color' => 'required|string|max:50',
            'fuel_capacity' => 'required|numeric|min:1',
            'municipality' => 'required|string|max:255',
            'assigned_driver_id' => 'nullable|exists:users,id',
            'equipment_list' => 'nullable|array',
            'insurance_policy' => 'nullable|string',
            'insurance_expiry' => 'nullable|date|after:today',
            'registration_expiry' => 'nullable|date|after:today',
        ]);
        
        // Set default values
        $validated['status'] = 'available';
        $validated['current_fuel_level'] = 100;
        $validated['gps_enabled'] = true;
        $validated['odometer_reading'] = 0;
        $validated['total_distance'] = 0;
        
        $vehicle = Vehicle::create($validated);
        
        // Log activity
        activity()
            ->performedOn($vehicle)
            ->withProperties(['attributes' => $validated])
            ->log('Vehicle added to fleet');
        
        return redirect()->route('vehicles.show', $vehicle)
                        ->with('success', 'Vehicle added to fleet successfully.');
    }
    
    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['assignedDriver', 'currentIncident', 'assignedIncidents']);
        
        // Check access permissions
        if (Auth::user()->role !== 'admin' && Auth::user()->municipality !== $vehicle->municipality) {
            abort(403, 'You do not have permission to view this vehicle.');
        }
        
        // Get recent incidents for this vehicle
        $recentIncidents = $vehicle->assignedIncidents()
                                  ->with(['assignedStaff', 'reporter'])
                                  ->latest('incident_date')
                                  ->take(10)
                                  ->get();
        
        return view('Vehicle.show', compact('vehicle', 'recentIncidents'));
    }
    
    public function edit(Vehicle $vehicle)
    {
        // Check access permissions
        if (Auth::user()->role !== 'admin' && Auth::user()->municipality !== $vehicle->municipality) {
            abort(403, 'You do not have permission to edit this vehicle.');
        }
        
        $drivers = User::where('role', 'responder')
                      ->where('is_active', true)
                      ->when(Auth::user()->role !== 'admin', function ($query) {
                          return $query->where('municipality', Auth::user()->municipality);
                      })
                      ->get();
        
        return view('Vehicle.edit', compact('vehicle', 'drivers'));
    }
    
    public function update(Request $request, Vehicle $vehicle)
    {
        // Check access permissions
        if (Auth::user()->role !== 'admin' && Auth::user()->municipality !== $vehicle->municipality) {
            abort(403, 'You do not have permission to update this vehicle.');
        }
        
        $validated = $request->validate([
            'vehicle_number' => ['required', 'string', Rule::unique('vehicles', 'vehicle_number')->ignore($vehicle->id)],
            'license_plate' => ['required', 'string', Rule::unique('vehicles', 'license_plate')->ignore($vehicle->id)],
            'vehicle_type' => 'required|in:ambulance,fire_truck,rescue_vehicle,patrol_car,support_vehicle',
            'status' => 'required|in:available,in_use,maintenance,out_of_service',
            'make' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'color' => 'required|string|max:50',
            'fuel_capacity' => 'required|numeric|min:1',
            'current_fuel_level' => 'required|numeric|min:0|max:100',
            'odometer_reading' => 'required|integer|min:0',
            'municipality' => 'required|string|max:255',
            'assigned_driver_id' => 'nullable|exists:users,id',
            'equipment_list' => 'nullable|array',
            'insurance_policy' => 'nullable|string',
            'insurance_expiry' => 'nullable|date',
            'registration_expiry' => 'nullable|date',
            'maintenance_notes' => 'nullable|string',
        ]);
        
        $oldValues = $vehicle->toArray();
        $vehicle->update($validated);
        
        // Log activity
        activity()
            ->performedOn($vehicle)
            ->withProperties(['old' => $oldValues, 'attributes' => $validated])
            ->log('Vehicle updated');
        
        return redirect()->route('vehicles.show', $vehicle)
                        ->with('success', 'Vehicle updated successfully.');
    }
    
    public function destroy(Vehicle $vehicle)
    {
        // Only admin can delete vehicles
        if (Auth::user()->role !== 'admin') {
            abort(403, 'You do not have permission to delete vehicles.');
        }
        
        // Check if vehicle is currently assigned
        if ($vehicle->current_incident_id) {
            return back()->with('error', 'Cannot delete vehicle that is currently assigned to an incident.');
        }
        
        $vehicleData = $vehicle->toArray();
        $vehicle->delete();
        
        // Log activity
        activity()
            ->withProperties(['attributes' => $vehicleData])
            ->log('Vehicle removed from fleet');
        
        return redirect()->route('vehicles.index')
                        ->with('success', 'Vehicle removed from fleet successfully.');
    }
    
    // Vehicle Assignment Methods
    public function assignToIncident(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'incident_id' => 'required|exists:incidents,id',
        ]);
        
        if ($vehicle->status !== 'available') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehicle is not available for assignment'
                ], 400);
            }
            return back()->with('error', 'Vehicle is not available for assignment.');
        }
        
        $vehicle->assignToIncident($request->incident_id);
        
        // Log activity
        activity()
            ->performedOn($vehicle)
            ->withProperties(['incident_id' => $request->incident_id])
            ->log('Vehicle assigned to incident');
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Vehicle assigned successfully',
                'vehicle' => $vehicle->fresh()
            ]);
        }
        
        return back()->with('success', 'Vehicle assigned to incident successfully.');
    }
    
    public function releaseFromIncident(Vehicle $vehicle)
    {
        $oldIncidentId = $vehicle->current_incident_id;
        
        if (!$oldIncidentId) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehicle is not assigned to any incident'
                ], 400);
            }
            return back()->with('error', 'Vehicle is not assigned to any incident.');
        }
        
        $vehicle->releaseFromIncident();
        
        // Log activity
        activity()
            ->performedOn($vehicle)
            ->withProperties(['previous_incident_id' => $oldIncidentId])
            ->log('Vehicle released from incident');
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Vehicle released successfully',
                'vehicle' => $vehicle->fresh()
            ]);
        }
        
        return back()->with('success', 'Vehicle released from incident successfully.');
    }
    
    // Maintenance Methods
    public function updateMaintenance(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'maintenance_notes' => 'nullable|string',
            'next_maintenance_due' => 'nullable|date|after:today',
            'status' => 'nullable|in:available,maintenance',
        ]);
        
        $updateData = [];
        
        if ($request->filled('maintenance_notes')) {
            $updateData['maintenance_notes'] = $request->maintenance_notes;
        }
        
        if ($request->filled('next_maintenance_due')) {
            $updateData['next_maintenance_due'] = $request->next_maintenance_due;
        }
        
        if ($request->filled('status')) {
            $updateData['status'] = $request->status;
        }
        
        // Set last maintenance date if status changed to maintenance
        if ($request->status === 'maintenance') {
            $updateData['last_maintenance_date'] = now();
        }
        
        $vehicle->update($updateData);
        
        // Log activity
        activity()
            ->performedOn($vehicle)
            ->withProperties($updateData)
            ->log('Vehicle maintenance updated');
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Maintenance information updated',
                'vehicle' => $vehicle->fresh()
            ]);
        }
        
        return back()->with('success', 'Maintenance information updated successfully.');
    }
    
    // Location and Status Updates (for mobile API)
    public function updateLocation(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);
        
        $vehicle->updateLocation($request->latitude, $request->longitude);
        
        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully'
        ]);
    }
    
    public function updateFuel(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'fuel_level' => 'required|numeric|between:0,100',
        ]);
        
        $vehicle->updateFuelLevel($request->fuel_level);
        
        // Log activity for low fuel
        if ($request->fuel_level < 25) {
            activity()
                ->performedOn($vehicle)
                ->withProperties(['fuel_level' => $request->fuel_level])
                ->log('Low fuel alert');
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Fuel level updated successfully'
        ]);
    }
    
    // API Methods
    public function apiIndex(Request $request)
    {
        $query = Vehicle::with(['assignedDriver', 'currentIncident'])
                        ->when($request->municipality, function ($q, $municipality) {
                            return $q->byMunicipality($municipality);
                        })
                        ->when($request->status, function ($q, $status) {
                            return $q->where('status', $status);
                        })
                        ->when($request->vehicle_type, function ($q, $type) {
                            return $q->byType($type);
                        });
        
        $vehicles = $query->get();
        
        return response()->json($vehicles);
    }
    
    public function getAvailableVehicles(Request $request)
    {
        $vehicles = Vehicle::available()
                          ->when($request->municipality, function ($q, $municipality) {
                              return $q->byMunicipality($municipality);
                          })
                          ->when($request->vehicle_type, function ($q, $type) {
                              return $q->byType($type);
                          })
                          ->get();
        
        return response()->json($vehicles);
    }
}
