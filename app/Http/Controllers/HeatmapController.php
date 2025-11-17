<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;
use App\Models\Vehicle;
use App\Models\Victim;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HeatmapController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // SuperAdmins see all municipalities, Admins see only their municipality
        // Following SuperAdmin Feature implementation
        $baseMunicipality = $user->isSuperAdmin() ? null : $user->municipality;

        // Build base query for incidents with location data
        $query = Incident::query()
            ->when($baseMunicipality, fn($q) => $q->where('municipality', $baseMunicipality))
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['victims', 'assignedStaff', 'assignedVehicle']);

        // Apply filters from request
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('incident_number', 'LIKE', "%{$search}%")
                  ->orWhere('location', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Incident type filter
        if ($request->filled('incident_type')) {
            $query->where('incident_type', $request->incident_type);
        }

        // Municipality filter (SuperAdmin only)
        if ($user->isSuperAdmin() && $request->filled('municipality')) {
            $query->where('municipality', $request->municipality);
        }

        // Get all incidents for map (with filters applied)
        $incidents = $query->get()->map(function ($incident) {
            // Handle photo paths properly
            $photos = [];
            if (!empty($incident->photos) && is_array($incident->photos)) {
                foreach ($incident->photos as $photo) {
                    // Ensure we have the correct path format
                    if (is_string($photo)) {
                        $photos[] = $photo;
                    }
                }
            }

            return [
                'id' => $incident->id,
                'incident_number' => $incident->incident_number,
                'incident_type' => $incident->incident_type,
                'severity_level' => $incident->severity_level,
                'location' => $incident->location,
                'latitude' => (float) $incident->latitude,
                'longitude' => (float) $incident->longitude,
                'incident_date' => $incident->incident_date ? $incident->incident_date->format('Y-m-d') : null,
                'incident_datetime' => $incident->incident_date ? $incident->incident_date->toISOString() : null,
                'photos' => $photos,
                'victims' => $incident->victims,
                'status' => $incident->status,
            ];
        });

        // Statistics (respect filters)
        $totalIncidents = $incidents->count();

        // Monthly incidents with same filter logic
        $monthlyQuery = Incident::when($baseMunicipality, fn($q) => $q->where('municipality', $baseMunicipality))
            ->where('created_at', '>=', now()->subMonth());

        if ($request->filled('search')) {
            $search = $request->search;
            $monthlyQuery->where(function($q) use ($search) {
                $q->where('incident_number', 'LIKE', "%{$search}%")
                  ->orWhere('location', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('incident_type')) {
            $monthlyQuery->where('incident_type', $request->incident_type);
        }

        if ($user->isSuperAdmin() && $request->filled('municipality')) {
            $monthlyQuery->where('municipality', $request->municipality);
        }

        $monthlyIncidents = $monthlyQuery->count();

        // Hotspots calculation
        $hotspots = $incidents->filter(function ($incident) {
            // Filter out incidents without proper coordinates
            return isset($incident['latitude']) && isset($incident['longitude']) &&
                $incident['latitude'] !== null && $incident['longitude'] !== null;
        })->groupBy(function ($incident) {
            // Group by rounded coordinates to identify hotspots
            $lat = round($incident['latitude'], 3);
            $lng = round($incident['longitude'], 3);
            return "{$lat},{$lng}";
        })->filter(function ($group) {
            return $group->count() > 1; // Hotspot if more than 1 incident
        })->count();

        $mappedIncidents = $totalIncidents;

        // Recent incidents for the table (paginated with same filters)
        $tableQuery = Incident::when($baseMunicipality, fn($q) => $q->where('municipality', $baseMunicipality))
            ->whereNotNull('incident_date')
            ->with(['assignedStaff', 'assignedVehicle']);

        if ($request->filled('search')) {
            $search = $request->search;
            $tableQuery->where(function($q) use ($search) {
                $q->where('incident_number', 'LIKE', "%{$search}%")
                  ->orWhere('location', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('incident_type')) {
            $tableQuery->where('incident_type', $request->incident_type);
        }

        if ($user->isSuperAdmin() && $request->filled('municipality')) {
            $tableQuery->where('municipality', $request->municipality);
        }

        $recentIncidents = $tableQuery->latest('incident_date')->paginate(10)->withQueryString();

        return view('HeatMaps.Heatmaps', compact(
            'incidents',
            'totalIncidents',
            'monthlyIncidents',
            'hotspots',
            'mappedIncidents',
            'recentIncidents'
        ));
    }
}
