<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;
use App\Models\Vehicle;
use App\Models\Victim;
use App\Models\Request as CitizenRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $municipalities = $this->getMunicipalities();
        
        return view('Reports.Index', compact('municipalities'));
    }
    
    public function generate(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'report_type' => 'required|in:incident_summary,incident_detailed,vehicle_usage,request_analysis,victim_statistics,municipality_comparison',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'municipality' => 'nullable|string',
            'format' => 'required|in:html,pdf,excel',
        ]);
        
        $reportData = $this->generateReportData($validated);
        
        if ($validated['format'] === 'pdf') {
            return $this->generatePdf($reportData, $validated);
        } elseif ($validated['format'] === 'excel') {
            return $this->generateExcel($reportData, $validated);
        }
        
        // Check if the view exists, if not, return a simple response
        if (view()->exists('Reports.Generated')) {
            return view('Reports.Generated', compact('reportData', 'validated'));
        } else {
            return response()->json([
                'message' => 'Report generated successfully',
                'report_data' => $reportData,
                'parameters' => $validated
            ]);
        }
    }
    
    private function generateReportData($params)
    {
        $query = Incident::query();
        
        // Apply municipality filter
        if (!empty($params['municipality'])) {
            $query->where('municipality', $params['municipality']);
        } elseif (Auth::user()->role !== 'admin') {
            $query->where('municipality', Auth::user()->municipality);
        }
        
        // Apply date range
        $query->whereBetween('incident_date', [$params['date_from'], $params['date_to']]);
        
        $incidents = $query->get();
        
        switch ($params['report_type']) {
            case 'incident_summary':
                return $this->generateIncidentSummary($incidents, $params);
            case 'incident_detailed':
                return $this->generateIncidentDetailed($incidents, $params);
            case 'vehicle_usage':
                return $this->generateVehicleUsage($incidents, $params);
            case 'request_analysis':
                return $this->generateRequestAnalysis($params);
            case 'victim_statistics':
                return $this->generateVictimStatistics($incidents, $params);
            case 'municipality_comparison':
                return $this->generateMunicipalityComparison($params);
            default:
                return [];
        }
    }
    
    private function generateIncidentSummary($incidents, $params)
    {
        $totalIncidents = $incidents->count();
        $bySeverity = $incidents->groupBy('severity_level')->map->count();
        $byType = $incidents->groupBy('incident_type')->map->count();
        $byStatus = $incidents->groupBy('status')->map->count();
        
        $criticalIncidents = $incidents->where('severity_level', 'critical')->count();
        $resolvedIncidents = $incidents->where('status', 'resolved')->count();
        
        // Calculate average response time
        $avgResponseTime = $incidents->filter(function ($incident) {
            return $incident->response_time && $incident->incident_date;
        })->avg(function ($incident) {
            return $incident->incident_date->diffInMinutes($incident->response_time);
        });
        
        return [
            'title' => 'Incident Summary Report',
            'period' => "{$params['date_from']} to {$params['date_to']}",
            'municipality' => $params['municipality'] ?? 'All Municipalities',
            'summary' => [
                'total_incidents' => $totalIncidents,
                'critical_incidents' => $criticalIncidents,
                'resolved_incidents' => $resolvedIncidents,
                'avg_response_time' => $avgResponseTime ? round($avgResponseTime, 2) : 'N/A',
            ],
            'by_severity' => $bySeverity,
            'by_type' => $byType,
            'by_status' => $byStatus,
        ];
    }
    
    private function generateIncidentDetailed($incidents, $params)
    {
        $incidentsWithDetails = $incidents->load(['assignedStaff', 'assignedVehicle', 'victims']);
        
        return [
            'title' => 'Detailed Incident Report',
            'period' => "{$params['date_from']} to {$params['date_to']}",
            'municipality' => $params['municipality'] ?? 'All Municipalities',
            'incidents' => $incidentsWithDetails,
        ];
    }
    
    private function generateVehicleUsage($incidents, $params)
    {
        $vehicles = Vehicle::when(!empty($params['municipality']), function ($query) use ($params) {
            return $query->where('municipality', $params['municipality']);
        })->when(Auth::user()->role !== 'admin', function ($query) {
            return $query->where('municipality', Auth::user()->municipality);
        })->get();
        
        $assignedVehicles = $incidents->pluck('assigned_vehicle_id')->filter()->unique();
        $vehicleUsage = $vehicles->map(function ($vehicle) use ($assignedVehicles) {
            return [
                'vehicle' => $vehicle,
                'times_used' => $assignedVehicles->contains($vehicle->id) ? 1 : 0,
            ];
        });
        
        return [
            'title' => 'Vehicle Usage Report',
            'period' => "{$params['date_from']} to {$params['date_to']}",
            'municipality' => $params['municipality'] ?? 'All Municipalities',
            'vehicles' => $vehicleUsage,
        ];
    }
    
    private function generateRequestAnalysis($params)
    {
        $requests = CitizenRequest::when(!empty($params['municipality']), function ($query) use ($params) {
            return $query->where('municipality', $params['municipality']);
        })->when(Auth::user()->role !== 'admin', function ($query) {
            return $query->where('municipality', Auth::user()->municipality);
        })->whereBetween('created_at', [$params['date_from'], $params['date_to']])
        ->get();
        
        $byStatus = $requests->groupBy('status')->map->count();
        $byType = $requests->groupBy('request_type')->map->count();
        
        return [
            'title' => 'Request Analysis Report',
            'period' => "{$params['date_from']} to {$params['date_to']}",
            'municipality' => $params['municipality'] ?? 'All Municipalities',
            'summary' => [
                'total_requests' => $requests->count(),
                'pending' => $requests->where('status', 'pending')->count(),
                'approved' => $requests->where('status', 'approved')->count(),
                'rejected' => $requests->where('status', 'rejected')->count(),
            ],
            'by_status' => $byStatus,
            'by_type' => $byType,
        ];
    }
    
    private function generateVictimStatistics($incidents, $params)
    {
        $victims = Victim::whereIn('incident_id', $incidents->pluck('id'))->get();
        
        $bySeverity = $victims->groupBy('severity')->map->count();
        $byAgeGroup = $victims->groupBy(function ($victim) {
            if ($victim->age < 18) return 'Minor (0-17)';
            if ($victim->age < 65) return 'Adult (18-64)';
            return 'Senior (65+)';
        })->map->count();
        
        return [
            'title' => 'Victim Statistics Report',
            'period' => "{$params['date_from']} to {$params['date_to']}",
            'municipality' => $params['municipality'] ?? 'All Municipalities',
            'summary' => [
                'total_victims' => $victims->count(),
                'injured' => $victims->where('injury_status', 'injured')->count(),
                'deceased' => $victims->where('injury_status', 'deceased')->count(),
                'critical' => $victims->where('severity', 'critical')->count(),
            ],
            'by_severity' => $bySeverity,
            'by_age_group' => $byAgeGroup,
        ];
    }
    
    private function generateMunicipalityComparison($params)
    {
        $municipalities = DB::table('incidents')
            ->select('municipality')
            ->selectRaw('COUNT(*) as total_incidents')
            ->selectRaw('SUM(CASE WHEN severity_level = "critical" THEN 1 ELSE 0 END) as critical_incidents')
            ->selectRaw('SUM(CASE WHEN status IN ("resolved", "closed") THEN 1 ELSE 0 END) as resolved_incidents')
            ->selectRaw('AVG(CASE WHEN response_time IS NOT NULL THEN EXTRACT(EPOCH FROM (response_time - incident_date))/60 END) as avg_response_time')
            ->whereBetween('incident_date', [$params['date_from'], $params['date_to']])
            ->groupBy('municipality')
            ->orderBy('total_incidents', 'desc')
            ->get();
        
        return [
            'title' => 'Municipality Comparison Report',
            'period' => "{$params['date_from']} to {$params['date_to']}",
            'municipalities' => $municipalities,
        ];
    }
    
    private function generatePdf($reportData, $params)
    {
        // For now, we'll return a simple PDF generation message
        // In a real implementation, you would use a PDF library like dompdf or tcpdf
        return response()->json([
            'message' => 'PDF generation would be implemented here',
            'report_data' => $reportData
        ]);
    }
    
    private function generateExcel($reportData, $params)
    {
        // For now, we'll return a simple Excel generation message
        // In a real implementation, you would use a library like PhpSpreadsheet
        return response()->json([
            'message' => 'Excel generation would be implemented here',
            'report_data' => $reportData
        ]);
    }
    
    private function getMunicipalities()
    {
        return Incident::distinct('municipality')
            ->pluck('municipality')
            ->filter()
            ->values();
    }
}