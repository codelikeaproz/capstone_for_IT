<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;
use App\Models\Vehicle;
use App\Models\Victim;
use App\Models\Request as CitizenRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $municipality = $user->role === 'admin' ? null : $user->municipality;

        // Get date range
        $dateRange = $request->get('date_range', '30'); // default 30 days
        $startDate = now()->subDays($dateRange);

        // Core Statistics
        $stats = $this->getCoreStatistics($municipality, $startDate);

        // Chart Data
        $chartData = $this->getChartData($municipality, $startDate);

        // Recent Activities
        $recentIncidents = $this->getRecentIncidents($municipality);
        $recentRequests = $this->getRecentRequests($municipality);

        // Emergency Alerts
        $alerts = $this->getEmergencyAlerts($municipality);

        // Municipality data for admin
        $municipalityStats = $user->role === 'admin' ? $this->getMunicipalityComparison() : null;

        return view('Dashboard.index', compact(
            'stats', 'chartData', 'recentIncidents', 'recentRequests',
            'alerts', 'municipalityStats', 'dateRange'
        ));
    }

    public function adminDashboard()
    {
        $stats = $this->getAdminStatistics();
        $systemHealth = $this->getSystemHealth();
        $municipalityPerformance = $this->getMunicipalityPerformance();
        $userActivity = $this->getUserActivity();

        return view('User.Admin.AdminDashboard', compact(
            'stats', 'systemHealth', 'municipalityPerformance', 'userActivity'
        ));
    }

    public function staffDashboard()
    {
        $user = Auth::user();
        $municipality = $user->municipality;

        $stats = $this->getStaffStatistics($municipality);
        $myIncidents = $this->getMyIncidents($user->id);
        $myRequests = $this->getMyRequests($user->id);
        $teamActivity = $this->getTeamActivity($municipality);

        return view('User.Staff.StaffDashBoard', compact(
            'stats', 'myIncidents', 'myRequests', 'teamActivity'
        ));
    }

    public function responderDashboard(Request $request)
    {
        $user = Auth::user();
        $municipality = $user->municipality;

        $stats = $this->getResponderStatistics($municipality);
        $activeIncidents = $this->getActiveIncidents($municipality);
        $myVehicle = $this->getMyVehicle($user->id);
        $nearbyIncidents = $this->getNearbyIncidents($municipality);

        // Check if request is from mobile device
        if ($this->isMobileDevice($request)) {
            return view('MobileView.responder-dashboard', compact(
                'stats', 'activeIncidents', 'myVehicle', 'nearbyIncidents'
            ));
        }

        return view('User.Responder.RespondersDashBoard', compact(
            'stats', 'activeIncidents', 'myVehicle', 'nearbyIncidents'
        ));
    }

    // Analytics Methods
    private function getCoreStatistics($municipality = null, $startDate = null)
    {
        $incidentQuery = Incident::when($municipality, fn($q) => $q->where('municipality', $municipality))
                                ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate));

        $vehicleQuery = Vehicle::when($municipality, fn($q) => $q->where('municipality', $municipality));

        $requestQuery = CitizenRequest::when($municipality, fn($q) => $q->where('municipality', $municipality))
                                     ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate));

        return [
            'incidents' => [
                'total' => $incidentQuery->count(),
                'active' => $incidentQuery->whereIn('status', ['pending', 'active'])->count(),
                'critical' => $incidentQuery->where('severity_level', 'critical')->count(),
                'resolved_today' => $incidentQuery->where('status', 'resolved')
                                                 ->whereDate('resolved_at', today())
                                                 ->count(),
            ],
            'vehicles' => [
                'total' => $vehicleQuery->count(),
                'available' => $vehicleQuery->where('status', 'available')->count(),
                'in_use' => $vehicleQuery->where('status', 'in_use')->count(),
                'maintenance' => $vehicleQuery->where('status', 'maintenance')->count(),
                'low_fuel' => $vehicleQuery->where('current_fuel_level', '<', 25)->count(),
            ],
            'requests' => [
                'total' => $requestQuery->count(),
                'pending' => $requestQuery->where('status', 'pending')->count(),
                'processing' => $requestQuery->where('status', 'processing')->count(),
                'completed' => $requestQuery->whereIn('status', ['approved', 'completed'])->count(),
            ],
            'victims' => [
                'total' => Victim::whereHas('incident', function($q) use ($municipality, $startDate) {
                    $q->when($municipality, fn($q) => $q->where('municipality', $municipality))
                      ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate));
                })->count(),
                'injured' => Victim::injured()
                                  ->whereHas('incident', function($q) use ($municipality, $startDate) {
                                      $q->when($municipality, fn($q) => $q->where('municipality', $municipality))
                                        ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate));
                                  })->count(),
                'critical' => Victim::critical()
                                   ->whereHas('incident', function($q) use ($municipality, $startDate) {
                                       $q->when($municipality, fn($q) => $q->where('municipality', $municipality))
                                         ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate));
                                   })->count(),
            ],
        ];
    }

    private function getChartData($municipality = null, $startDate = null)
    {
        // Incident trends by day
        $incidentTrends = Incident::when($municipality, fn($q) => $q->where('municipality', $municipality))
                                 ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
                                 ->selectRaw('DATE(incident_date) as date, COUNT(*) as count')
                                 ->groupBy('date')
                                 ->orderBy('date')
                                 ->get();

        // Severity distribution
        $severityData = Incident::when($municipality, fn($q) => $q->where('municipality', $municipality))
                               ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
                               ->selectRaw('severity_level, COUNT(*) as count')
                               ->groupBy('severity_level')
                               ->get();

        // Incident types
        $typeData = Incident::when($municipality, fn($q) => $q->where('municipality', $municipality))
                           ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
                           ->selectRaw('incident_type, COUNT(*) as count')
                           ->groupBy('incident_type')
                           ->get();

        // Response time analysis
        $responseTimeData = Incident::when($municipality, fn($q) => $q->where('municipality', $municipality))
                                   ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
                                   ->whereNotNull('response_time')
                                   ->selectRaw('AVG(EXTRACT(EPOCH FROM (response_time - incident_date))/60) as avg_response_time')
                                   ->selectRaw('DATE(incident_date) as date')
                                   ->groupBy('date')
                                   ->orderBy('date')
                                   ->get();

        return [
            'trends' => $incidentTrends,
            'severity' => $severityData,
            'types' => $typeData,
            'response_times' => $responseTimeData,
        ];
    }

    private function getRecentIncidents($municipality = null)
    {
        return Incident::with(['assignedStaff', 'assignedVehicle'])
                      ->when($municipality, fn($q) => $q->where('municipality', $municipality))
                      ->latest('incident_date')
                      ->take(10)
                      ->get();
    }

    private function getRecentRequests($municipality = null)
    {
        return CitizenRequest::with('assignedStaff')
                            ->when($municipality, fn($q) => $q->where('municipality', $municipality))
                            ->latest()
                            ->take(10)
                            ->get();
    }

    private function getEmergencyAlerts($municipality = null)
    {
        $alerts = [];

        // Critical incidents
        $criticalIncidents = Incident::where('severity_level', 'critical')
                                   ->whereIn('status', ['pending', 'active'])
                                   ->when($municipality, fn($q) => $q->where('municipality', $municipality))
                                   ->count();

        if ($criticalIncidents > 0) {
            $alerts[] = [
                'type' => 'critical',
                'message' => "{$criticalIncidents} critical incident(s) require immediate attention",
                'count' => $criticalIncidents,
            ];
        }

        // Low fuel vehicles
        $lowFuelVehicles = Vehicle::where('current_fuel_level', '<', 25)
                                 ->where('status', '!=', 'out_of_service')
                                 ->when($municipality, fn($q) => $q->where('municipality', $municipality))
                                 ->count();

        if ($lowFuelVehicles > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$lowFuelVehicles} vehicle(s) have low fuel levels",
                'count' => $lowFuelVehicles,
            ];
        }

        // Overdue maintenance
        $overdueVehicles = Vehicle::where('next_maintenance_due', '<', now())
                                 ->where('status', '!=', 'maintenance')
                                 ->when($municipality, fn($q) => $q->where('municipality', $municipality))
                                 ->count();

        if ($overdueVehicles > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$overdueVehicles} vehicle(s) have overdue maintenance",
                'count' => $overdueVehicles,
            ];
        }

        return $alerts;
    }

    private function getMunicipalityComparison()
    {
        return DB::table('incidents')
                ->select('municipality')
                ->selectRaw('COUNT(*) as total_incidents')
                ->selectRaw('SUM(CASE WHEN severity_level = \'critical\' THEN 1 ELSE 0 END) as critical_incidents')
                ->selectRaw('SUM(CASE WHEN status IN (\'resolved\', \'closed\') THEN 1 ELSE 0 END) as resolved_incidents')
                ->selectRaw('AVG(CASE WHEN response_time IS NOT NULL THEN EXTRACT(EPOCH FROM (response_time - incident_date))/60 END) as avg_response_time')
                ->groupBy('municipality')
                ->orderBy('total_incidents', 'desc')
                ->get();
    }

    // API Methods for real-time updates
    public function getStatistics(Request $request)
    {
        $municipality = $request->get('municipality');
        $dateRange = $request->get('date_range', 30);
        $startDate = now()->subDays($dateRange);

        $stats = $this->getCoreStatistics($municipality, $startDate);

        return response()->json($stats);
    }

    public function getHeatmapData(Request $request)
    {
        $municipality = $request->get('municipality');

        $incidents = Incident::when($municipality, fn($q) => $q->where('municipality', $municipality))
                            ->whereNotNull('latitude')
                            ->whereNotNull('longitude')
                            ->where('created_at', '>=', now()->subMonths(3))
                            ->select('latitude', 'longitude', 'severity_level', 'incident_type')
                            ->get()
                            ->map(function ($incident) {
                                return [
                                    'lat' => (float) $incident->latitude,
                                    'lng' => (float) $incident->longitude,
                                    'intensity' => $this->getSeverityWeight($incident->severity_level),
                                    'type' => $incident->incident_type,
                                ];
                            });

        return response()->json($incidents);
    }

    private function getSeverityWeight($severity)
    {
        return match($severity) {
            'critical' => 1.0,
            'high' => 0.7,
            'medium' => 0.4,
            'low' => 0.2,
            default => 0.1
        };
    }

    // Additional helper methods for specific dashboards
    private function getAdminStatistics()
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_municipalities' => User::distinct('municipality')->count('municipality'),
            'system_incidents' => Incident::count(),
            'system_vehicles' => Vehicle::count(),
            'pending_requests' => CitizenRequest::where('status', 'pending')->count(),
        ];
    }

    private function getMyIncidents($userId)
    {
        return Incident::where('assigned_staff_id', $userId)
                      ->whereIn('status', ['pending', 'active'])
                      ->latest('incident_date')
                      ->take(5)
                      ->get();
    }

    private function getMyRequests($userId)
    {
        return CitizenRequest::where('assigned_staff_id', $userId)
                            ->whereIn('status', ['pending', 'processing'])
                            ->latest()
                            ->take(5)
                            ->get();
    }

    private function getMyVehicle($userId)
    {
        return Vehicle::where('assigned_driver_id', $userId)->first();
    }

    private function getActiveIncidents($municipality)
    {
        return Incident::where('municipality', $municipality)
                      ->whereIn('status', ['pending', 'active'])
                      ->latest('incident_date')
                      ->take(10)
                      ->get();
    }

    private function getNearbyIncidents($municipality)
    {
        return Incident::where('municipality', $municipality)
                      ->whereIn('status', ['pending', 'active'])
                      ->whereNotNull('latitude')
                      ->whereNotNull('longitude')
                      ->get();
    }

    private function getStaffStatistics($municipality)
    {
        return $this->getCoreStatistics($municipality, now()->subDays(30));
    }

    private function getResponderStatistics($municipality)
    {
        return $this->getCoreStatistics($municipality, now()->subDays(7));
    }

    private function getSystemHealth()
    {
        return [
            'database_status' => 'online',
            'last_backup' => now()->subHours(2),
            'active_sessions' => DB::table('sessions')->count(),
            'failed_logins_today' => DB::table('login_attempts')
                                      ->where('successful', false)
                                      ->whereDate('attempted_at', today())
                                      ->count(),
        ];
    }

    private function getMunicipalityPerformance()
    {
        return $this->getMunicipalityComparison();
    }

    private function getUserActivity()
    {
        return DB::table('activity_log')
                ->join('users', 'activity_log.causer_id', '=', 'users.id')
                ->select('users.first_name', 'users.last_name', 'activity_log.description', 'activity_log.created_at')
                ->latest('activity_log.created_at')
                ->take(15)
                ->get();
    }

    private function getTeamActivity($municipality)
    {
        // Get recent incidents assigned to the same municipality
        $incidents = Incident::where('municipality', $municipality)
                           ->whereIn('status', ['pending', 'active'])
                           ->with(['assignedStaff', 'assignedVehicle'])
                           ->latest('incident_date')
                           ->take(5)
                           ->get();

        // Get recent requests assigned to the same municipality
        $requests = CitizenRequest::where('municipality', $municipality)
                                 ->whereIn('status', ['pending', 'processing'])
                                 ->with('assignedStaff')
                                 ->latest()
                                 ->take(5)
                                 ->get();

        // Get recent activity logs for the municipality
        $activityLogs = DB::table('activity_log')
                         ->join('users', 'activity_log.causer_id', '=', 'users.id')
                         ->where('users.municipality', $municipality)
                         ->select('activity_log.description', 'users.first_name', 'users.last_name', 'activity_log.created_at')
                         ->latest('activity_log.created_at')
                         ->take(10)
                         ->get();

        return [
            'recent_incidents' => $incidents,
            'recent_requests' => $requests,
            'recent_activity' => $activityLogs,
        ];
    }



    // Helper method to detect mobile devices
    private function isMobileDevice($request)
    {
        $userAgent = $request->header('User-Agent');

        return preg_match('/Mobile|Android|iPhone|iPad|iPod|BlackBerry|Windows Phone/i', $userAgent);
    }

    // Advanced Analytics Dashboard
    public function analytics(Request $request)
    {
        $user = Auth::user();
        $municipality = $request->get('municipality', $user->role === 'admin' ? null : $user->municipality);

        // Get filter parameters
        $dateRange = $request->get('date_range', 30);
        $incidentType = $request->get('incident_type', '');
        $severityLevel = $request->get('severity', '');

        // Date range
        $startDate = now()->subDays($dateRange);
        $endDate = now();

        // Get filter options
        $incidentTypes = Incident::distinct()->pluck('incident_type')->toArray();
        $severityLevels = Incident::distinct()->pluck('severity_level')->toArray();
        $municipalities = $user->role === 'admin' ? User::distinct()->whereNotNull('municipality')->pluck('municipality')->toArray() : [];

        // Build base query with filters
        $baseQuery = Incident::when($municipality, fn($q) => $q->where('municipality', $municipality))
                            ->when($incidentType, fn($q) => $q->where('incident_type', $incidentType))
                            ->when($severityLevel, fn($q) => $q->where('severity_level', $severityLevel))
                            ->whereBetween('incident_date', [$startDate, $endDate]);

        // Month-over-Month Comparison
        $monthComparison = $this->getMonthComparison($municipality, $incidentType, $severityLevel);

        // Chart Data
        $chartData = $this->getAnalyticsChartData($municipality, $startDate, $endDate, $incidentType, $severityLevel);

        // Time-based Heatmap
        $timeHeatmap = $this->getTimeHeatmap($municipality, $startDate, $endDate, $incidentType, $severityLevel);

        // Response Metrics (Admin only)
        $responseMetrics = $user->role === 'admin' ? $this->getResponseMetrics($startDate, $endDate, $incidentType, $severityLevel) : ['response_times' => [], 'resolution_rates' => []];

        // Municipality Stats (Admin only)
        $municipalityStats = $user->role === 'admin' ? $this->getDetailedMunicipalityStats($startDate, $endDate, $incidentType, $severityLevel) : null;

        return view('Analytics.Dashboard', compact(
            'dateRange',
            'incidentTypes',
            'incidentType',
            'severityLevels',
            'severityLevel',
            'municipalities',
            'municipality',
            'monthComparison',
            'chartData',
            'timeHeatmap',
            'responseMetrics',
            'municipalityStats'
        ));
    }

    private function getMonthComparison($municipality, $incidentType, $severityLevel)
    {
        // Current month
        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();

        // Previous month
        $previousMonthStart = now()->subMonth()->startOfMonth();
        $previousMonthEnd = now()->subMonth()->endOfMonth();

        $currentStats = DB::table('incidents')
            ->when($municipality, fn($q) => $q->where('municipality', $municipality))
            ->when($incidentType, fn($q) => $q->where('incident_type', $incidentType))
            ->when($severityLevel, fn($q) => $q->where('severity_level', $severityLevel))
            ->whereBetween('incident_date', [$currentMonthStart, $currentMonthEnd])
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN severity_level = \'critical\' THEN 1 ELSE 0 END) as critical')
            ->selectRaw('SUM(CASE WHEN status IN (\'resolved\', \'closed\') THEN 1 ELSE 0 END) as resolved')
            ->first();

        $previousStats = DB::table('incidents')
            ->when($municipality, fn($q) => $q->where('municipality', $municipality))
            ->when($incidentType, fn($q) => $q->where('incident_type', $incidentType))
            ->when($severityLevel, fn($q) => $q->where('severity_level', $severityLevel))
            ->whereBetween('incident_date', [$previousMonthStart, $previousMonthEnd])
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN severity_level = \'critical\' THEN 1 ELSE 0 END) as critical')
            ->selectRaw('SUM(CASE WHEN status IN (\'resolved\', \'closed\') THEN 1 ELSE 0 END) as resolved')
            ->first();

        // Calculate percentage changes
        $changes = [
            'total' => $previousStats->total > 0
                ? round((($currentStats->total - $previousStats->total) / $previousStats->total) * 100)
                : 0,
            'critical' => $previousStats->critical > 0
                ? round((($currentStats->critical - $previousStats->critical) / $previousStats->critical) * 100)
                : 0,
            'resolved' => $previousStats->resolved > 0
                ? round((($currentStats->resolved - $previousStats->resolved) / $previousStats->resolved) * 100)
                : 0,
        ];

        return [
            'current' => $currentStats,
            'previous' => $previousStats,
            'changes' => $changes,
        ];
    }

    private function getAnalyticsChartData($municipality, $startDate, $endDate, $incidentType, $severityLevel)
    {
        // Incident trends by day
        $trends = Incident::when($municipality, fn($q) => $q->where('municipality', $municipality))
                        ->when($incidentType, fn($q) => $q->where('incident_type', $incidentType))
                        ->when($severityLevel, fn($q) => $q->where('severity_level', $severityLevel))
                        ->whereBetween('incident_date', [$startDate, $endDate])
                        ->selectRaw('DATE(incident_date) as date, COUNT(*) as count')
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get();

        // Severity distribution
        $severity = Incident::when($municipality, fn($q) => $q->where('municipality', $municipality))
                          ->when($incidentType, fn($q) => $q->where('incident_type', $incidentType))
                          ->when($severityLevel, fn($q) => $q->where('severity_level', $severityLevel))
                          ->whereBetween('incident_date', [$startDate, $endDate])
                          ->selectRaw('severity_level, COUNT(*) as count')
                          ->groupBy('severity_level')
                          ->get();

        // Incident types
        $types = Incident::when($municipality, fn($q) => $q->where('municipality', $municipality))
                       ->when($incidentType, fn($q) => $q->where('incident_type', $incidentType))
                       ->when($severityLevel, fn($q) => $q->where('severity_level', $severityLevel))
                       ->whereBetween('incident_date', [$startDate, $endDate])
                       ->selectRaw('incident_type, COUNT(*) as count')
                       ->groupBy('incident_type')
                       ->get();

        // Response times
        $responseTimes = Incident::when($municipality, fn($q) => $q->where('municipality', $municipality))
                               ->when($incidentType, fn($q) => $q->where('incident_type', $incidentType))
                               ->when($severityLevel, fn($q) => $q->where('severity_level', $severityLevel))
                               ->whereBetween('incident_date', [$startDate, $endDate])
                               ->whereNotNull('response_time')
                               ->selectRaw('DATE(incident_date) as date')
                               ->selectRaw('AVG(EXTRACT(EPOCH FROM (response_time - incident_date))/60) as avg_response_time')
                               ->groupBy('date')
                               ->orderBy('date')
                               ->get();

        return [
            'trends' => $trends,
            'severity' => $severity,
            'types' => $types,
            'response_times' => $responseTimes,
        ];
    }

    private function getTimeHeatmap($municipality, $startDate, $endDate, $incidentType, $severityLevel)
    {
        $incidents = Incident::when($municipality, fn($q) => $q->where('municipality', $municipality))
                           ->when($incidentType, fn($q) => $q->where('incident_type', $incidentType))
                           ->when($severityLevel, fn($q) => $q->where('severity_level', $severityLevel))
                           ->whereBetween('incident_date', [$startDate, $endDate])
                           ->selectRaw('EXTRACT(HOUR FROM incident_date) as hour')
                           ->selectRaw('EXTRACT(DOW FROM incident_date) as day_of_week')
                           ->get();

        // Initialize heatmap array
        $heatmap = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $heatmap[$hour] = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0];
        }

        // Populate heatmap
        foreach ($incidents as $incident) {
            $hour = (int) $incident->hour;
            $day = (int) $incident->day_of_week;
            // PostgreSQL DOW: Sunday = 0, convert to 1-7 where Sunday = 7
            $day = $day == 0 ? 7 : $day;
            $heatmap[$hour][$day]++;
        }

        return $heatmap;
    }

    private function getResponseMetrics($startDate, $endDate, $incidentType, $severityLevel)
    {
        // Average response time by municipality
        $responseTimes = DB::table('incidents')
            ->when($incidentType, fn($q) => $q->where('incident_type', $incidentType))
            ->when($severityLevel, fn($q) => $q->where('severity_level', $severityLevel))
            ->whereBetween('incident_date', [$startDate, $endDate])
            ->whereNotNull('response_time')
            ->select('municipality')
            ->selectRaw('AVG(EXTRACT(EPOCH FROM (response_time - incident_date))/60) as avg_response_time')
            ->groupBy('municipality')
            ->orderBy('avg_response_time')
            ->get();

        // Resolution rate by municipality
        $resolutionRates = DB::table('incidents')
            ->when($incidentType, fn($q) => $q->where('incident_type', $incidentType))
            ->when($severityLevel, fn($q) => $q->where('severity_level', $severityLevel))
            ->whereBetween('incident_date', [$startDate, $endDate])
            ->select('municipality')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status IN (\'resolved\', \'closed\') THEN 1 ELSE 0 END) as resolved')
            ->groupBy('municipality')
            ->get()
            ->map(function($item) {
                $item->resolution_rate = $item->total > 0 ? round(($item->resolved / $item->total) * 100, 1) : 0;
                return $item;
            });

        return [
            'response_times' => $responseTimes,
            'resolution_rates' => $resolutionRates,
        ];
    }

    private function getDetailedMunicipalityStats($startDate, $endDate, $incidentType, $severityLevel)
    {
        return DB::table('incidents')
            ->when($incidentType, fn($q) => $q->where('incident_type', $incidentType))
            ->when($severityLevel, fn($q) => $q->where('severity_level', $severityLevel))
            ->whereBetween('incident_date', [$startDate, $endDate])
            ->select('municipality')
            ->selectRaw('COUNT(*) as total_incidents')
            ->selectRaw('SUM(CASE WHEN severity_level = \'critical\' THEN 1 ELSE 0 END) as critical_incidents')
            ->selectRaw('SUM(CASE WHEN status IN (\'resolved\', \'closed\') THEN 1 ELSE 0 END) as resolved_incidents')
            ->selectRaw('AVG(CASE WHEN response_time IS NOT NULL THEN EXTRACT(EPOCH FROM (response_time - incident_date))/60 END) as avg_response_time')
            ->groupBy('municipality')
            ->orderBy('total_incidents', 'desc')
            ->get();
    }
}
