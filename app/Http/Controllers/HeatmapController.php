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
    public function index()
    {
        $user = Auth::user();

        // SuperAdmins see all municipalities, Admins see only their municipality
        // Following SuperAdmin Feature implementation
        $municipality = $user->isSuperAdmin() ? null : $user->municipality;


        // Get incidents with location data
        $incidents = Incident::when($municipality, fn($q) => $q->where('municipality', $municipality))
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['victims', 'assignedStaff', 'assignedVehicle'])
            ->get()
            ->map(function ($incident) {
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

        // Statistics
        $totalIncidents = $incidents->count();
        $monthlyIncidents = Incident::when($municipality, fn($q) => $q->where('municipality', $municipality))
            ->where('created_at', '>=', now()->subMonth())
            ->count();

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

        // Recent incidents for the table (paginated)
        $recentIncidents = Incident::when($municipality, fn($q) => $q->where('municipality', $municipality))
            ->whereNotNull('incident_date')
            ->with(['assignedStaff', 'assignedVehicle'])
            ->latest('incident_date')
            ->paginate(10);

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
