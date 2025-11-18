# Product Requirements Document
## BukidnonAlert System - Remaining Critical Features

**Document Version:** 1.0
**Date:** November 17, 2025
**Status:** Draft - Ready for Implementation
**Target Completion:** February 28, 2026 (14 weeks)
**Current Project Completion:** 70%
**Target Project Completion:** 95%

---

## 📋 Executive Summary

This PRD outlines the remaining critical features required to bring the BukidnonAlert system to production-ready status. Based on the Comprehensive Objectives Gap Analysis and the Daily Progress Report of November 17, 2025, we have identified **8 critical feature gaps** that must be implemented to achieve the project's core objectives.

### Current State
- **Overall Completion:** 70%
- **Critical Gaps:** 4 features
- **High Priority Gaps:** 4 features
- **Estimated Time to Production-Ready:** 14 weeks

### Success Criteria
Upon completion of this PRD:
- ✅ Project completion reaches 95%
- ✅ All CRITICAL gaps closed
- ✅ System ready for user acceptance testing
- ✅ MVP ready for production deployment

---

## 🎯 Feature Priority Matrix

| Priority | Feature | Objective | Current | Target | Timeline | Effort |
|----------|---------|-----------|---------|--------|----------|--------|
| 🔴 **CRITICAL 1** | Vehicle Utilization System | Obj 3 | 0% | 100% | 3 weeks | High |
| 🔴 **CRITICAL 2** | Analytics Dashboard (Chart.js) | Obj 5 | 0% | 95% | 2 weeks | Medium |
| 🔴 **CRITICAL 3** | WebSocket Real-Time Broadcasting | Obj 2,4 | 30% | 100% | 2 weeks | Medium |
| 🔴 **CRITICAL 4** | Mobile Responder Interface | Obj 1 | 0% | 90% | 3 weeks | High |
| 🟠 **HIGH 1** | Report Generation System | Obj 5 | 0% | 100% | 1 week | Low |
| 🟠 **HIGH 2** | Fuel Consumption Tracking | Obj 3 | 20% | 100% | 1 week | Low |
| 🟠 **HIGH 3** | Response Time Tracking Fix | Obj 4 | 0% | 100% | 3 days | Low |
| 🟠 **HIGH 4** | Maintenance History System | Obj 3 | 0% | 100% | 1 week | Medium |

**Total Estimated Timeline:** 14 weeks (includes buffer)

---

## 🔴 CRITICAL PRIORITY 1: Vehicle Utilization System

### Overview
**Gap Reference:** Objective 3, Section 3.5
**Status:** NOT IMPLEMENTED (0%)
**Business Impact:** CRITICAL - Core PRD requirement for MDRRMO
**Timeline:** 3 weeks
**Dependencies:** None

### Problem Statement
The MDRRMO requires a monthly equipment utilization report to track:
- Vehicle usage per trip
- End-user/victim transport status
- Fuel consumption per trip
- Driver assignments
- Service categorization (Health vs Non-Health)

**Current State:**
- ✅ VehicleUtilization model exists with proper relationships
- ❌ VehicleUtilizationController does NOT exist
- ❌ No integration with victim status updates
- ❌ No monthly report generation
- ❌ Cannot export to Excel

### User Stories

#### US-1: Log Vehicle Utilization on Victim Transport
```
As a staff member
When I update a victim's status to "Discharged" or "Transport to Hospital"
I want to automatically create a vehicle utilization record
So that we can track equipment usage accurately
```

**Acceptance Criteria:**
- [ ] When updating victim status, a vehicle selection modal appears
- [ ] Staff can select vehicle, origin, destination, service type
- [ ] System auto-calculates distance if GPS coordinates provided
- [ ] VehicleUtilization record created with all required fields
- [ ] Vehicle availability updated to "in_use"
- [ ] Trip ticket number auto-generated (format: VU-YYYY-MM-####)
- [ ] Toast notification confirms vehicle utilization logged
- [ ] Activity log entry created

#### US-2: Generate Monthly Equipment Utilization Report
```
As an admin
I want to generate a monthly equipment utilization report
So that I can submit compliance reports to MDRRMO management
```

**Acceptance Criteria:**
- [ ] Admin can select year and month for report
- [ ] Report displays:
  - Total trips per vehicle
  - Service type breakdown (Health/Non-Health)
  - Total distance traveled per vehicle
  - Total fuel consumed per vehicle
  - Driver workload distribution
  - End-user transport history
- [ ] Report includes summary statistics
- [ ] Report can be filtered by vehicle type
- [ ] Report can be filtered by service category
- [ ] Export to Excel functionality available
- [ ] Report includes date range and generated timestamp

#### US-3: View Vehicle Utilization History
```
As a staff member
I want to view the utilization history for a specific vehicle
So that I can track its usage patterns and efficiency
```

**Acceptance Criteria:**
- [ ] Vehicle detail page shows utilization history tab
- [ ] History shows all trips with:
  - Date and time
  - Origin and destination
  - Distance traveled
  - Fuel consumed
  - Service type
  - Driver name
  - Victim/end-user name
- [ ] History can be filtered by date range
- [ ] History can be filtered by service type
- [ ] Pagination for large datasets (20 records per page)
- [ ] Export history to Excel/PDF

### Technical Specifications

#### 1. Database Schema

**Table: vehicle_utilization (already exists)**
```sql
✅ Existing fields:
- id (bigint, primary key)
- vehicle_id (foreign key to vehicles)
- victim_id (foreign key to victims, nullable)
- incident_id (foreign key to incidents, nullable)
- driver_id (foreign key to users)
- service_date (date)
- trip_ticket_number (string, unique)
- origin_address (text)
- destination_address (text)
- origin_latitude (decimal, nullable)
- origin_longitude (decimal, nullable)
- destination_latitude (decimal, nullable)
- destination_longitude (decimal, nullable)
- service_category (enum: 'health', 'non-health')
- service_type (string) // Transport, Referral, etc.
- fuel_consumed (decimal, nullable)
- distance_traveled (decimal, nullable)
- remarks (text, nullable)
- created_at, updated_at

❌ Missing fields to add:
- fuel_level_before (decimal) // Fuel level at trip start
- fuel_level_after (decimal) // Fuel level at trip end
- odometer_before (decimal) // Odometer reading at start
- odometer_after (decimal) // Odometer reading at end
- trip_status (enum: 'pending', 'in-progress', 'completed', 'cancelled')
- completed_at (timestamp, nullable)
```

**Migration Required:**
```php
Schema::table('vehicle_utilization', function (Blueprint $table) {
    $table->decimal('fuel_level_before', 5, 2)->nullable()->after('fuel_consumed');
    $table->decimal('fuel_level_after', 5, 2)->nullable()->after('fuel_level_before');
    $table->decimal('odometer_before', 10, 2)->nullable()->after('fuel_level_after');
    $table->decimal('odometer_after', 10, 2)->nullable()->after('odometer_before');
    $table->enum('trip_status', ['pending', 'in-progress', 'completed', 'cancelled'])
          ->default('pending')->after('odometer_after');
    $table->timestamp('completed_at')->nullable()->after('trip_status');
});
```

#### 2. Controller Structure

**File:** `app/Http/Controllers/VehicleUtilizationController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\VehicleUtilization;
use App\Models\Vehicle;
use App\Models\Victim;
use App\Http\Requests\StoreVehicleUtilizationRequest;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VehicleUtilizationExport;
use Carbon\Carbon;

class VehicleUtilizationController extends Controller
{
    /**
     * Display monthly report
     */
    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        $vehicleType = $request->get('vehicle_type');
        $serviceCategory = $request->get('service_category');

        $query = VehicleUtilization::with(['vehicle', 'driver', 'victim'])
            ->byMonth($year, $month);

        if ($vehicleType) {
            $query->whereHas('vehicle', fn($q) => $q->where('vehicle_type', $vehicleType));
        }

        if ($serviceCategory) {
            $query->byServiceCategory($serviceCategory);
        }

        $utilizations = $query->latest('service_date')->paginate(20);

        // Calculate summary statistics
        $stats = [
            'total_trips' => $query->count(),
            'health_trips' => $query->clone()->where('service_category', 'health')->count(),
            'non_health_trips' => $query->clone()->where('service_category', 'non-health')->count(),
            'total_distance' => $query->clone()->sum('distance_traveled'),
            'total_fuel_consumed' => $query->clone()->sum('fuel_consumed'),
            'unique_vehicles' => $query->clone()->distinct('vehicle_id')->count(),
            'unique_drivers' => $query->clone()->distinct('driver_id')->count(),
        ];

        // Vehicle breakdown
        $vehicleStats = $query->clone()
            ->select('vehicle_id')
            ->selectRaw('COUNT(*) as trips')
            ->selectRaw('SUM(distance_traveled) as distance')
            ->selectRaw('SUM(fuel_consumed) as fuel')
            ->groupBy('vehicle_id')
            ->with('vehicle')
            ->get();

        return view('VehicleUtilization.index', compact(
            'utilizations',
            'stats',
            'vehicleStats',
            'year',
            'month',
            'vehicleType',
            'serviceCategory'
        ));
    }

    /**
     * Show form to create utilization record from victim update
     */
    public function createFromVictim(Victim $victim)
    {
        $availableVehicles = Vehicle::where('status', 'available')
            ->orWhere('id', $victim->incident->assigned_vehicle_id)
            ->get();

        return view('VehicleUtilization.create-from-victim', compact('victim', 'availableVehicles'));
    }

    /**
     * Store vehicle utilization record
     */
    public function store(StoreVehicleUtilizationRequest $request)
    {
        $validated = $request->validated();

        // Auto-generate trip ticket number
        $validated['trip_ticket_number'] = $this->generateTripTicketNumber();

        // Calculate distance if GPS coordinates provided
        if ($this->hasGPSData($validated)) {
            $validated['distance_traveled'] = $this->calculateDistance(
                $validated['origin_latitude'],
                $validated['origin_longitude'],
                $validated['destination_latitude'],
                $validated['destination_longitude']
            );
        }

        // Calculate fuel consumed if odometer readings provided
        if (isset($validated['odometer_before']) && isset($validated['odometer_after'])) {
            $distanceTraveled = $validated['odometer_after'] - $validated['odometer_before'];
            $vehicle = Vehicle::find($validated['vehicle_id']);

            if ($vehicle->fuel_consumption_rate > 0) {
                $validated['fuel_consumed'] = $distanceTraveled / $vehicle->fuel_consumption_rate;
            }
        }

        $utilization = VehicleUtilization::create($validated);

        // Update vehicle status
        Vehicle::find($validated['vehicle_id'])->update(['status' => 'in_use']);

        // Log activity
        activity()
            ->performedOn($utilization)
            ->withProperties($validated)
            ->log('Vehicle utilization record created');

        return response()->json([
            'success' => true,
            'message' => 'Vehicle utilization logged successfully',
            'data' => $utilization->load(['vehicle', 'driver', 'victim'])
        ]);
    }

    /**
     * Complete trip
     */
    public function complete(VehicleUtilization $utilization, Request $request)
    {
        $validated = $request->validate([
            'fuel_level_after' => 'required|numeric|min:0|max:100',
            'odometer_after' => 'required|numeric|min:' . ($utilization->odometer_before ?? 0),
            'remarks' => 'nullable|string|max:500',
        ]);

        // Calculate actual fuel consumed
        if ($utilization->fuel_level_before && $validated['fuel_level_after']) {
            $validated['fuel_consumed'] = $utilization->fuel_level_before - $validated['fuel_level_after'];
        }

        // Calculate actual distance
        if ($utilization->odometer_before && $validated['odometer_after']) {
            $validated['distance_traveled'] = $validated['odometer_after'] - $utilization->odometer_before;
        }

        $validated['trip_status'] = 'completed';
        $validated['completed_at'] = now();

        $utilization->update($validated);

        // Release vehicle
        $utilization->vehicle->update(['status' => 'available']);

        activity()
            ->performedOn($utilization)
            ->log('Trip completed');

        return response()->json([
            'success' => true,
            'message' => 'Trip marked as completed',
            'data' => $utilization->fresh(['vehicle', 'driver'])
        ]);
    }

    /**
     * Generate monthly report
     */
    public function generateMonthlyReport($year, $month)
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $utilizations = VehicleUtilization::with(['vehicle', 'driver', 'victim'])
            ->whereBetween('service_date', [$startDate, $endDate])
            ->orderBy('service_date')
            ->get();

        // Group by vehicle
        $byVehicle = $utilizations->groupBy('vehicle_id')->map(function ($trips, $vehicleId) {
            $vehicle = Vehicle::find($vehicleId);
            return [
                'vehicle' => $vehicle,
                'total_trips' => $trips->count(),
                'health_trips' => $trips->where('service_category', 'health')->count(),
                'non_health_trips' => $trips->where('service_category', 'non-health')->count(),
                'total_distance' => $trips->sum('distance_traveled'),
                'total_fuel' => $trips->sum('fuel_consumed'),
                'avg_fuel_efficiency' => $trips->avg(function ($trip) {
                    return $trip->distance_traveled && $trip->fuel_consumed
                        ? $trip->distance_traveled / $trip->fuel_consumed
                        : 0;
                }),
                'trips' => $trips
            ];
        });

        // Group by driver
        $byDriver = $utilizations->groupBy('driver_id')->map(function ($trips, $driverId) {
            $driver = \App\Models\User::find($driverId);
            return [
                'driver' => $driver,
                'total_trips' => $trips->count(),
                'total_distance' => $trips->sum('distance_traveled'),
            ];
        });

        return view('VehicleUtilization.monthly-report', compact(
            'utilizations',
            'byVehicle',
            'byDriver',
            'year',
            'month',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export to Excel
     */
    public function exportExcel($year, $month)
    {
        $filename = "vehicle-utilization-{$year}-{$month}.xlsx";

        return Excel::download(
            new VehicleUtilizationExport($year, $month),
            $filename
        );
    }

    /**
     * Generate unique trip ticket number
     */
    private function generateTripTicketNumber(): string
    {
        $prefix = 'VU';
        $date = now()->format('Ym');

        // Get last ticket number for this month
        $lastTicket = VehicleUtilization::where('trip_ticket_number', 'like', "{$prefix}-{$date}-%")
            ->orderBy('trip_ticket_number', 'desc')
            ->first();

        if ($lastTicket) {
            $lastNumber = intval(substr($lastTicket->trip_ticket_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $date, $newNumber);
    }

    /**
     * Check if GPS data is complete
     */
    private function hasGPSData(array $data): bool
    {
        return isset($data['origin_latitude'])
            && isset($data['origin_longitude'])
            && isset($data['destination_latitude'])
            && isset($data['destination_longitude']);
    }

    /**
     * Calculate distance between two GPS points (Haversine formula)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371; // km

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2); // Distance in km
    }
}
```

#### 3. Request Validation

**File:** `app/Http/Requests/StoreVehicleUtilizationRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleUtilizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', VehicleUtilization::class);
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => 'required|exists:vehicles,id',
            'victim_id' => 'nullable|exists:victims,id',
            'incident_id' => 'nullable|exists:incidents,id',
            'driver_id' => 'required|exists:users,id',
            'service_date' => 'required|date|before_or_equal:today',
            'origin_address' => 'required|string|max:500',
            'destination_address' => 'required|string|max:500',
            'origin_latitude' => 'nullable|numeric|between:-90,90',
            'origin_longitude' => 'nullable|numeric|between:-180,180',
            'destination_latitude' => 'nullable|numeric|between:-90,90',
            'destination_longitude' => 'nullable|numeric|between:-180,180',
            'service_category' => 'required|in:health,non-health',
            'service_type' => 'required|string|max:100',
            'fuel_level_before' => 'nullable|numeric|min:0|max:100',
            'odometer_before' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'vehicle_id.required' => 'Please select a vehicle',
            'driver_id.required' => 'Please select a driver',
            'service_category.required' => 'Please specify service category (Health/Non-Health)',
            'origin_address.required' => 'Origin address is required',
            'destination_address.required' => 'Destination address is required',
        ];
    }
}
```

#### 4. Model Scopes

**Update:** `app/Models/VehicleUtilization.php`

```php
/**
 * Scope to filter by month
 */
public function scopeByMonth($query, $year, $month)
{
    return $query->whereYear('service_date', $year)
                 ->whereMonth('service_date', $month);
}

/**
 * Scope to filter by service category
 */
public function scopeByServiceCategory($query, $category)
{
    return $query->where('service_category', $category);
}

/**
 * Scope to filter by vehicle
 */
public function scopeByVehicle($query, $vehicleId)
{
    return $query->where('vehicle_id', $vehicleId);
}

/**
 * Scope to filter by date range
 */
public function scopeDateRange($query, $startDate, $endDate)
{
    return $query->whereBetween('service_date', [$startDate, $endDate]);
}

/**
 * Accessor for formatted trip ticket
 */
public function getFormattedTicketAttribute()
{
    return $this->trip_ticket_number;
}

/**
 * Accessor for fuel efficiency
 */
public function getFuelEfficiencyAttribute()
{
    if ($this->distance_traveled && $this->fuel_consumed && $this->fuel_consumed > 0) {
        return round($this->distance_traveled / $this->fuel_consumed, 2);
    }
    return null;
}
```

#### 5. Excel Export

**File:** `app/Exports/VehicleUtilizationExport.php`

```php
<?php

namespace App\Exports;

use App\Models\VehicleUtilization;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class VehicleUtilizationExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle
{
    protected $year;
    protected $month;

    public function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function collection()
    {
        return VehicleUtilization::with(['vehicle', 'driver', 'victim'])
            ->byMonth($this->year, $this->month)
            ->orderBy('service_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Trip Ticket #',
            'Date',
            'Vehicle',
            'License Plate',
            'Vehicle Type',
            'Driver',
            'Service Category',
            'Service Type',
            'Origin',
            'Destination',
            'Distance (km)',
            'Fuel Consumed (L)',
            'Fuel Efficiency (km/L)',
            'Victim/End-User',
            'Status',
            'Remarks'
        ];
    }

    public function map($utilization): array
    {
        return [
            $utilization->trip_ticket_number,
            Carbon::parse($utilization->service_date)->format('Y-m-d'),
            $utilization->vehicle->vehicle_number ?? 'N/A',
            $utilization->vehicle->license_plate ?? 'N/A',
            ucfirst(str_replace('_', ' ', $utilization->vehicle->vehicle_type ?? 'N/A')),
            $utilization->driver->name ?? 'N/A',
            ucfirst($utilization->service_category),
            $utilization->service_type,
            $utilization->origin_address,
            $utilization->destination_address,
            $utilization->distance_traveled ?? 0,
            $utilization->fuel_consumed ?? 0,
            $utilization->fuel_efficiency ?? 'N/A',
            $utilization->victim ? $utilization->victim->full_name : 'N/A',
            ucfirst($utilization->trip_status),
            $utilization->remarks ?? ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return Carbon::create($this->year, $this->month, 1)->format('F Y');
    }
}
```

#### 6. Integration with VictimController

**Update:** `app/Http/Controllers/VictimController.php`

Add to `updateVictimStatus()` method (around line 305):

```php
public function updateVictimStatus(Request $request, Victim $victim)
{
    $validated = $request->validate([
        'medical_status' => 'required|in:Discharge,Transport to Hospital,Hospital Transfer/Referral,Ongoing Care',
        'hospital_name' => 'required_if:medical_status,Transport to Hospital,Hospital Transfer/Referral',
        'referring_hospital' => 'required_if:medical_status,Hospital Transfer/Referral',
        'receiving_hospital' => 'required_if:medical_status,Hospital Transfer/Referral',

        // NEW: Vehicle utilization fields
        'log_vehicle_usage' => 'boolean',
        'vehicle_id' => 'required_if:log_vehicle_usage,true|exists:vehicles,id',
        'origin_address' => 'required_if:log_vehicle_usage,true|string',
        'destination_address' => 'required_if:log_vehicle_usage,true|string',
        'service_type' => 'required_if:log_vehicle_usage,true|string',
    ]);

    // Update victim status
    $victim->update([
        'medical_status' => $validated['medical_status'],
        'hospital_name' => $validated['hospital_name'] ?? null,
        'referring_hospital' => $validated['referring_hospital'] ?? null,
        'receiving_hospital' => $validated['receiving_hospital'] ?? null,
    ]);

    // Log vehicle utilization if requested
    if ($request->boolean('log_vehicle_usage')) {
        $this->logVehicleUtilization($victim, $validated);
    }

    activity()
        ->performedOn($victim)
        ->withProperties($validated)
        ->log('Victim status updated');

    return response()->json([
        'success' => true,
        'message' => 'Victim status updated successfully',
        'victim' => $victim->fresh()
    ]);
}

/**
 * Log vehicle utilization for victim transport
 */
protected function logVehicleUtilization(Victim $victim, array $data)
{
    // Determine service category based on medical status
    $serviceCategory = in_array($victim->medical_status, [
        'Transport to Hospital',
        'Hospital Transfer/Referral',
        'Discharge'
    ]) ? 'health' : 'non-health';

    VehicleUtilization::create([
        'vehicle_id' => $data['vehicle_id'],
        'victim_id' => $victim->id,
        'incident_id' => $victim->incident_id,
        'driver_id' => $victim->incident->assigned_vehicle->assigned_driver_id ?? auth()->id(),
        'service_date' => now()->toDateString(),
        'trip_ticket_number' => $this->generateTripTicketNumber(),
        'origin_address' => $data['origin_address'],
        'destination_address' => $data['destination_address'],
        'service_category' => $serviceCategory,
        'service_type' => $data['service_type'],
        'trip_status' => 'in-progress',
    ]);

    // Update vehicle status
    Vehicle::find($data['vehicle_id'])->update(['status' => 'in_use']);
}
```

#### 7. Views

**Main Index View:** `resources/views/VehicleUtilization/index.blade.php`

```blade
@extends('Layouts.app')

@section('title', 'Vehicle Utilization Reports - MDRRMC')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- Page Header --}}
        <header class="mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-clipboard-list text-primary" aria-hidden="true"></i>
                        <span>Vehicle Utilization Reports</span>
                    </h1>
                    <p class="text-base text-gray-600 mt-1">
                        Monthly equipment utilization and consumption tracking
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <a href="{{ route('vehicle-utilization.create') }}"
                       class="btn btn-primary gap-2 w-full sm:w-auto min-h-[44px]">
                        <i class="fas fa-plus" aria-hidden="true"></i>
                        <span>Log Trip</span>
                    </a>
                    <button type="button"
                            onclick="exportToExcel()"
                            class="btn btn-success gap-2 w-full sm:w-auto min-h-[44px]">
                        <i class="fas fa-file-excel" aria-hidden="true"></i>
                        <span>Export Excel</span>
                    </button>
                </div>
            </div>
        </header>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Total Trips --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <i class="fas fa-route text-4xl"></i>
                    </div>
                    <div class="stat-title text-gray-600">Total Trips</div>
                    <div class="stat-value text-primary">{{ $stats['total_trips'] }}</div>
                    <div class="stat-desc text-sm text-gray-500">
                        This month
                    </div>
                </div>
            </div>

            {{-- Health Services --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-success">
                        <i class="fas fa-heartbeat text-4xl"></i>
                    </div>
                    <div class="stat-title text-gray-600">Health Services</div>
                    <div class="stat-value text-success">{{ $stats['health_trips'] }}</div>
                    <div class="stat-desc text-sm text-gray-500">
                        {{ round(($stats['health_trips'] / max($stats['total_trips'], 1)) * 100, 1) }}% of total
                    </div>
                </div>
            </div>

            {{-- Distance Traveled --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-info">
                        <i class="fas fa-road text-4xl"></i>
                    </div>
                    <div class="stat-title text-gray-600">Distance</div>
                    <div class="stat-value text-info">{{ number_format($stats['total_distance'], 1) }}</div>
                    <div class="stat-desc text-sm text-gray-500">
                        Kilometers
                    </div>
                </div>
            </div>

            {{-- Fuel Consumed --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-warning">
                        <i class="fas fa-gas-pump text-4xl"></i>
                    </div>
                    <div class="stat-title text-gray-600">Fuel Consumed</div>
                    <div class="stat-value text-warning">{{ number_format($stats['total_fuel_consumed'], 1) }}</div>
                    <div class="stat-desc text-sm text-gray-500">
                        Liters
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card bg-white shadow-sm mb-6">
            <div class="card-body">
                <form method="GET" action="{{ route('vehicle-utilization.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {{-- Year --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Year</span>
                            </label>
                            <select name="year" class="select select-bordered">
                                @for($y = now()->year; $y >= now()->year - 5; $y--)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        {{-- Month --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Month</span>
                            </label>
                            <select name="month" class="select select-bordered">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Vehicle Type --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Vehicle Type</span>
                            </label>
                            <select name="vehicle_type" class="select select-bordered">
                                <option value="">All Types</option>
                                <option value="ambulance" {{ $vehicleType == 'ambulance' ? 'selected' : '' }}>Ambulance</option>
                                <option value="fire_truck" {{ $vehicleType == 'fire_truck' ? 'selected' : '' }}>Fire Truck</option>
                                <option value="rescue_vehicle" {{ $vehicleType == 'rescue_vehicle' ? 'selected' : '' }}>Rescue Vehicle</option>
                                <option value="patrol_car" {{ $vehicleType == 'patrol_car' ? 'selected' : '' }}>Patrol Car</option>
                            </select>
                        </div>

                        {{-- Service Category --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Service Category</span>
                            </label>
                            <select name="service_category" class="select select-bordered">
                                <option value="">All Categories</option>
                                <option value="health" {{ $serviceCategory == 'health' ? 'selected' : '' }}>Health</option>
                                <option value="non-health" {{ $serviceCategory == 'non-health' ? 'selected' : '' }}>Non-Health</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i>
                            Apply Filters
                        </button>
                        <a href="{{ route('vehicle-utilization.index') }}" class="btn btn-outline">
                            <i class="fas fa-times"></i>
                            Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Vehicle Breakdown --}}
        <div class="card bg-white shadow-sm mb-6">
            <div class="card-body">
                <h2 class="card-title text-xl mb-4">
                    <i class="fas fa-chart-bar text-info"></i>
                    Vehicle Breakdown
                </h2>
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead class="bg-gray-100">
                            <tr>
                                <th>Vehicle</th>
                                <th>Type</th>
                                <th class="text-center">Trips</th>
                                <th class="text-right">Distance (km)</th>
                                <th class="text-right">Fuel (L)</th>
                                <th class="text-right">Efficiency (km/L)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vehicleStats as $stat)
                                <tr>
                                    <td>
                                        <div class="font-medium">{{ $stat->vehicle->vehicle_number }}</div>
                                        <div class="text-sm text-gray-500">{{ $stat->vehicle->license_plate }}</div>
                                    </td>
                                    <td>
                                        <span class="badge badge-ghost">
                                            {{ ucfirst(str_replace('_', ' ', $stat->vehicle->vehicle_type)) }}
                                        </span>
                                    </td>
                                    <td class="text-center">{{ $stat->trips }}</td>
                                    <td class="text-right">{{ number_format($stat->distance, 1) }}</td>
                                    <td class="text-right">{{ number_format($stat->fuel, 1) }}</td>
                                    <td class="text-right">
                                        @if($stat->fuel > 0)
                                            {{ number_format($stat->distance / $stat->fuel, 2) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-gray-500 py-8">
                                        No vehicle utilization data for this period
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Utilization Records --}}
        <div class="card bg-white shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-xl mb-4">
                    <i class="fas fa-list text-primary"></i>
                    Trip Records
                </h2>
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead class="bg-gray-100">
                            <tr>
                                <th>Ticket #</th>
                                <th>Date</th>
                                <th>Vehicle</th>
                                <th>Driver</th>
                                <th>Route</th>
                                <th>Category</th>
                                <th class="text-right">Distance</th>
                                <th class="text-right">Fuel</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($utilizations as $util)
                                <tr>
                                    <td class="font-mono text-sm">{{ $util->trip_ticket_number }}</td>
                                    <td>{{ \Carbon\Carbon::parse($util->service_date)->format('M d, Y') }}</td>
                                    <td>
                                        <div class="font-medium">{{ $util->vehicle->vehicle_number }}</div>
                                        <div class="text-sm text-gray-500">{{ $util->vehicle->license_plate }}</div>
                                    </td>
                                    <td>{{ $util->driver->name }}</td>
                                    <td class="max-w-xs">
                                        <div class="text-sm">
                                            <i class="fas fa-map-marker-alt text-success"></i>
                                            {{ Str::limit($util->origin_address, 30) }}
                                        </div>
                                        <div class="text-sm">
                                            <i class="fas fa-map-marker-alt text-error"></i>
                                            {{ Str::limit($util->destination_address, 30) }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $util->service_category == 'health' ? 'badge-success' : 'badge-info' }}">
                                            {{ ucfirst($util->service_category) }}
                                        </span>
                                    </td>
                                    <td class="text-right">{{ number_format($util->distance_traveled ?? 0, 1) }} km</td>
                                    <td class="text-right">{{ number_format($util->fuel_consumed ?? 0, 1) }} L</td>
                                    <td>
                                        @php
                                            $statusClass = match($util->trip_status) {
                                                'completed' => 'badge-success',
                                                'in-progress' => 'badge-warning',
                                                'pending' => 'badge-info',
                                                'cancelled' => 'badge-error',
                                                default => 'badge-ghost'
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }}">
                                            {{ ucfirst($util->trip_status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button onclick="viewDetails({{ $util->id }})"
                                                    class="btn btn-sm btn-ghost tooltip"
                                                    data-tip="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($util->trip_status != 'completed')
                                                <button onclick="completeTrip({{ $util->id }})"
                                                        class="btn btn-sm btn-success tooltip"
                                                        data-tip="Complete Trip">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-gray-500 py-8">
                                        No trip records found for this period
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($utilizations->hasPages())
                    <div class="mt-6">
                        {{ $utilizations->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
function exportToExcel() {
    const year = {{ $year }};
    const month = {{ $month }};
    window.location.href = `/vehicle-utilization/export/${year}/${month}`;
}

function viewDetails(id) {
    // Open details modal
    window.location.href = `/vehicle-utilization/${id}`;
}

function completeTrip(id) {
    // Open complete trip modal
    // Implementation details...
}
</script>
@endpush
@endsection
```

### Testing Requirements

#### Unit Tests
```php
// tests/Unit/VehicleUtilizationTest.php

test('generates unique trip ticket numbers', function() {
    $ticket1 = VehicleUtilization::factory()->create()->trip_ticket_number;
    $ticket2 = VehicleUtilization::factory()->create()->trip_ticket_number;

    expect($ticket1)->not->toBe($ticket2);
});

test('calculates distance correctly', function() {
    $utilization = VehicleUtilization::factory()->create([
        'origin_latitude' => 8.1570,
        'origin_longitude' => 124.7143,
        'destination_latitude' => 8.4833,
        'destination_longitude' => 124.6500,
    ]);

    expect($utilization->distance_traveled)->toBeGreaterThan(0);
});

test('calculates fuel efficiency', function() {
    $utilization = VehicleUtilization::factory()->create([
        'distance_traveled' => 100,
        'fuel_consumed' => 10,
    ]);

    expect($utilization->fuel_efficiency)->toBe(10.0);
});
```

#### Feature Tests
```php
// tests/Feature/VehicleUtilizationTest.php

test('staff can create vehicle utilization record', function() {
    $staff = User::factory()->staff()->create();

    $response = $this->actingAs($staff)->post('/vehicle-utilization', [
        'vehicle_id' => Vehicle::factory()->create()->id,
        'driver_id' => User::factory()->staff()->create()->id,
        'service_date' => now()->toDateString(),
        'origin_address' => 'Test Origin',
        'destination_address' => 'Test Destination',
        'service_category' => 'health',
        'service_type' => 'Transport',
    ]);

    $response->assertSuccessful();
    expect(VehicleUtilization::count())->toBe(1);
});

test('admin can generate monthly report', function() {
    $admin = User::factory()->admin()->create();
    VehicleUtilization::factory()->count(10)->create();

    $response = $this->actingAs($admin)->get('/vehicle-utilization/report/2025/11');

    $response->assertSuccessful();
    $response->assertViewHas('utilizations');
    $response->assertViewHas('byVehicle');
});

test('can export to excel', function() {
    $admin = User::factory()->admin()->create();
    VehicleUtilization::factory()->count(5)->create();

    $response = $this->actingAs($admin)->get('/vehicle-utilization/export/2025/11');

    $response->assertSuccessful();
    $response->assertDownload();
});
```

### Routes

**Add to:** `routes/web.php`

```php
// Vehicle Utilization Routes
Route::middleware(['auth', 'staff'])->group(function () {
    Route::resource('vehicle-utilization', VehicleUtilizationController::class);

    Route::get('vehicle-utilization/create-from-victim/{victim}',
        [VehicleUtilizationController::class, 'createFromVictim'])
        ->name('vehicle-utilization.create-from-victim');

    Route::post('vehicle-utilization/{utilization}/complete',
        [VehicleUtilizationController::class, 'complete'])
        ->name('vehicle-utilization.complete');

    Route::get('vehicle-utilization/report/{year}/{month}',
        [VehicleUtilizationController::class, 'generateMonthlyReport'])
        ->name('vehicle-utilization.monthly-report');

    Route::get('vehicle-utilization/export/{year}/{month}',
        [VehicleUtilizationController::class, 'exportExcel'])
        ->name('vehicle-utilization.export');
});
```

### Implementation Checklist

**Week 1: Foundation**
- [ ] Day 1-2: Create migration for additional fields
- [ ] Day 2-3: Create VehicleUtilizationController with all methods
- [ ] Day 3-4: Create StoreVehicleUtilizationRequest validation
- [ ] Day 4-5: Update Model with scopes and accessors

**Week 2: Integration & Views**
- [ ] Day 1-2: Integrate with VictimController
- [ ] Day 2-3: Create index view with filters
- [ ] Day 3-4: Create monthly report view
- [ ] Day 4-5: Create Excel export functionality

**Week 3: Testing & Documentation**
- [ ] Day 1-2: Write unit tests
- [ ] Day 2-3: Write feature tests
- [ ] Day 3-4: Manual testing and bug fixes
- [ ] Day 4-5: Documentation and user guide

---

## 🔴 CRITICAL PRIORITY 2: Analytics Dashboard with Chart.js

### Overview
**Gap Reference:** Objective 5, Section 5.4
**Status:** PLACEHOLDER ONLY (0%)
**Business Impact:** CRITICAL - Cannot visualize trends for planning
**Timeline:** 2 weeks
**Dependencies:** None

### Problem Statement
The Analytics Dashboard exists but contains only placeholder content. The system has backend data preparation methods but no frontend visualization.

**Current State:**
- ✅ `DashboardController::getChartData()` exists with data preparation
- ✅ `DashboardController::getMunicipalityComparison()` exists
- ❌ No Chart.js integration
- ❌ No actual charts rendered
- ❌ Placeholder view with "Hello Dashboard" text

### User Stories

#### US-4: View Incident Trends Chart
```
As an admin
I want to view a line chart showing incident trends over time
So that I can identify patterns and seasonal variations
```

**Acceptance Criteria:**
- [ ] Line chart displays incident count by day/week/month
- [ ] Time range selector (7 days, 30 days, 90 days, 1 year)
- [ ] Chart shows trend line with data points
- [ ] Hover over data points shows exact count and date
- [ ] Legend indicates what line represents
- [ ] Chart is responsive and looks good on mobile
- [ ] Export chart as PNG image
- [ ] Chart animates on load

#### US-5: View Severity Distribution
```
As a staff member
I want to view a pie chart of incident severity distribution
So that I can understand the proportion of critical vs non-critical incidents
```

**Acceptance Criteria:**
- [ ] Pie/Doughnut chart shows severity breakdown
- [ ] Colors: Critical (red), High (orange), Medium (yellow), Low (green)
- [ ] Percentages displayed on chart segments
- [ ] Hover shows exact count for each severity
- [ ] Legend with severity labels
- [ ] Click segment to filter incident list
- [ ] Chart updates when filters applied

#### US-6: View Incident Type Breakdown
```
As an admin
I want to view a bar chart of incidents by type
So that I can allocate resources based on most common incident types
```

**Acceptance Criteria:**
- [ ] Horizontal bar chart shows incident types
- [ ] Bars sorted by count (descending)
- [ ] Each bar shows count label
- [ ] Color-coded by incident type
- [ ] Hover shows percentage of total
- [ ] Click bar to view incidents of that type
- [ ] Chart animates from left to right

### Technical Specifications

#### 1. Install Chart.js

**Package Installation:**
```bash
npm install chart.js
```

**Import in app.js:**
```javascript
import Chart from 'chart.js/auto';
window.Chart = Chart;
```

#### 2. Update DashboardController

**File:** `app/Http/Controllers/DashboardController.php`

Add new method for analytics:

```php
/**
 * Analytics Dashboard
 */
public function analytics(Request $request)
{
    $municipality = auth()->user()->role === 'admin'
        ? $request->get('municipality')
        : auth()->user()->municipality;

    $dateRange = $request->get('date_range', 30);
    $startDate = now()->subDays($dateRange);

    // Chart data
    $chartData = $this->getChartData($municipality, $startDate);

    // Municipality comparison
    $municipalityStats = auth()->user()->role === 'admin'
        ? $this->getMunicipalityComparison()
        : null;

    // KPI calculations
    $kpis = $this->calculateKPIs($municipality, $startDate);

    // Peak times analysis
    $peakTimes = $this->analyzePeakTimes($municipality, $startDate);

    return view('Analytics.Dashboard', compact(
        'chartData',
        'municipalityStats',
        'kpis',
        'peakTimes',
        'dateRange',
        'municipality'
    ));
}

/**
 * Calculate KPIs
 */
private function calculateKPIs($municipality, $startDate)
{
    $query = Incident::when($municipality, fn($q) => $q->where('municipality', $municipality))
        ->where('created_at', '>=', $startDate);

    $total = $query->count();
    $resolved = $query->clone()->whereIn('status', ['resolved', 'closed'])->count();
    $critical = $query->clone()->where('severity_level', 'critical')->count();

    // Calculate average response time
    $avgResponseTime = $query->clone()
        ->whereNotNull('response_time')
        ->avg(DB::raw('EXTRACT(EPOCH FROM (response_time - incident_date))/60'));

    // Previous period for comparison
    $previousStart = $startDate->copy()->subDays($dateRange);
    $previousQuery = Incident::when($municipality, fn($q) => $q->where('municipality', $municipality))
        ->whereBetween('created_at', [$previousStart, $startDate]);

    $previousTotal = $previousQuery->count();

    $changePercent = $previousTotal > 0
        ? round((($total - $previousTotal) / $previousTotal) * 100, 1)
        : 0;

    return [
        'total_incidents' => $total,
        'resolved_incidents' => $resolved,
        'critical_incidents' => $critical,
        'resolution_rate' => $total > 0 ? round(($resolved / $total) * 100, 1) : 0,
        'avg_response_time' => round($avgResponseTime ?? 0, 1),
        'change_percent' => $changePercent,
        'trend' => $changePercent > 0 ? 'up' : ($changePercent < 0 ? 'down' : 'stable'),
    ];
}

/**
 * Analyze peak incident times
 */
private function analyzePeakTimes($municipality, $startDate)
{
    $incidents = Incident::when($municipality, fn($q) => $q->where('municipality', $municipality))
        ->where('created_at', '>=', $startDate)
        ->get();

    // Group by hour of day
    $byHour = $incidents->groupBy(function($incident) {
        return \Carbon\Carbon::parse($incident->incident_date)->format('H');
    })->map->count()->sortKeys();

    // Group by day of week
    $byDayOfWeek = $incidents->groupBy(function($incident) {
        return \Carbon\Carbon::parse($incident->incident_date)->format('l');
    })->map->count();

    return [
        'by_hour' => $byHour,
        'by_day_of_week' => $byDayOfWeek,
        'peak_hour' => $byHour->sortDesc()->keys()->first() ?? 0,
        'peak_day' => $byDayOfWeek->sortDesc()->keys()->first() ?? 'Monday',
    ];
}
```

#### 3. Analytics Dashboard View

**File:** `resources/views/Analytics/Dashboard.blade.php`

```blade
@extends('Layouts.app')

@section('title', 'Analytics Dashboard - MDRRMC')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- Page Header --}}
        <header class="mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-chart-line text-info" aria-hidden="true"></i>
                        <span>Analytics Dashboard</span>
                    </h1>
                    <p class="text-base text-gray-600 mt-1">
                        Incident trends, patterns, and performance metrics
                    </p>
                </div>

                <div class="flex gap-3">
                    {{-- Date Range Selector --}}
                    <select id="dateRangeSelect" class="select select-bordered" onchange="changeDateRange(this.value)">
                        <option value="7" {{ $dateRange == 7 ? 'selected' : '' }}>Last 7 days</option>
                        <option value="30" {{ $dateRange == 30 ? 'selected' : '' }}>Last 30 days</option>
                        <option value="90" {{ $dateRange == 90 ? 'selected' : '' }}>Last 90 days</option>
                        <option value="365" {{ $dateRange == 365 ? 'selected' : '' }}>Last year</option>
                    </select>

                    @if(auth()->user()->role === 'admin')
                        {{-- Municipality Filter --}}
                        <select id="municipalitySelect" class="select select-bordered" onchange="changeMunicipality(this.value)">
                            <option value="">All Municipalities</option>
                            @foreach(config('locations.municipalities') as $muni)
                                <option value="{{ $muni }}" {{ $municipality == $muni ? 'selected' : '' }}>
                                    {{ $muni }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>
        </header>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Total Incidents --}}
            <div class="stats shadow bg-white">
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <i class="fas fa-exclamation-circle text-4xl"></i>
                    </div>
                    <div class="stat-title">Total Incidents</div>
                    <div class="stat-value text-primary">{{ number_format($kpis['total_incidents']) }}</div>
                    <div class="stat-desc flex items-center gap-1">
                        @if($kpis['trend'] == 'up')
                            <i class="fas fa-arrow-up text-error"></i>
                            <span class="text-error">{{ abs($kpis['change_percent']) }}% increase</span>
                        @elseif($kpis['trend'] == 'down')
                            <i class="fas fa-arrow-down text-success"></i>
                            <span class="text-success">{{ abs($kpis['change_percent']) }}% decrease</span>
                        @else
                            <span class="text-gray-500">No change</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Resolution Rate --}}
            <div class="stats shadow bg-white">
                <div class="stat">
                    <div class="stat-figure text-success">
                        <i class="fas fa-check-circle text-4xl"></i>
                    </div>
                    <div class="stat-title">Resolution Rate</div>
                    <div class="stat-value text-success">{{ $kpis['resolution_rate'] }}%</div>
                    <div class="stat-desc">{{ $kpis['resolved_incidents'] }} of {{ $kpis['total_incidents'] }} resolved</div>
                </div>
            </div>

            {{-- Avg Response Time --}}
            <div class="stats shadow bg-white">
                <div class="stat">
                    <div class="stat-figure text-warning">
                        <i class="fas fa-clock text-4xl"></i>
                    </div>
                    <div class="stat-title">Avg Response Time</div>
                    <div class="stat-value text-warning">{{ $kpis['avg_response_time'] }}</div>
                    <div class="stat-desc">minutes</div>
                </div>
            </div>

            {{-- Critical Incidents --}}
            <div class="stats shadow bg-white">
                <div class="stat">
                    <div class="stat-figure text-error">
                        <i class="fas fa-exclamation-triangle text-4xl"></i>
                    </div>
                    <div class="stat-title">Critical Incidents</div>
                    <div class="stat-value text-error">{{ $kpis['critical_incidents'] }}</div>
                    <div class="stat-desc">
                        {{ $kpis['total_incidents'] > 0 ? round(($kpis['critical_incidents'] / $kpis['total_incidents']) * 100, 1) : 0 }}% of total
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Incident Trend Chart --}}
            <div class="card bg-white shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">
                        <i class="fas fa-chart-line text-info"></i>
                        Incident Trend
                    </h2>
                    <canvas id="incidentTrendChart" height="300"></canvas>
                </div>
            </div>

            {{-- Severity Distribution --}}
            <div class="card bg-white shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">
                        <i class="fas fa-chart-pie text-success"></i>
                        Severity Distribution
                    </h2>
                    <canvas id="severityChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Incident Type Breakdown --}}
            <div class="card bg-white shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">
                        <i class="fas fa-chart-bar text-primary"></i>
                        Incident Type Breakdown
                    </h2>
                    <canvas id="typeChart" height="300"></canvas>
                </div>
            </div>

            {{-- Peak Hours Heatmap --}}
            <div class="card bg-white shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">
                        <i class="fas fa-calendar-alt text-warning"></i>
                        Peak Incident Times
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-2">By Hour of Day</h3>
                            <canvas id="hourlyChart" height="150"></canvas>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-2">By Day of Week</h3>
                            <canvas id="dailyChart" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($municipalityStats && auth()->user()->role === 'admin')
            {{-- Municipality Comparison --}}
            <div class="card bg-white shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">
                        <i class="fas fa-map-marked-alt text-accent"></i>
                        Municipality Comparison
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th>Municipality</th>
                                    <th class="text-center">Total Incidents</th>
                                    <th class="text-center">Critical</th>
                                    <th class="text-center">Resolved</th>
                                    <th class="text-center">Resolution Rate</th>
                                    <th class="text-right">Avg Response Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($municipalityStats as $stat)
                                    <tr>
                                        <td class="font-medium">{{ $stat->municipality }}</td>
                                        <td class="text-center">{{ $stat->total_incidents }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-error">{{ $stat->critical_incidents }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-success">{{ $stat->resolved_incidents }}</span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $rate = $stat->total_incidents > 0
                                                    ? round(($stat->resolved_incidents / $stat->total_incidents) * 100, 1)
                                                    : 0;
                                            @endphp
                                            <div class="flex items-center justify-center gap-2">
                                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-success h-2 rounded-full" style="width: {{ $rate }}%"></div>
                                                </div>
                                                <span class="text-sm font-medium">{{ $rate }}%</span>
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            {{ $stat->avg_response_time ? round($stat->avg_response_time, 1) . ' min' : 'N/A' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart.js Configuration
Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
Chart.defaults.color = '#4F5564'; // base-content

// Incident Trend Chart
const trendCtx = document.getElementById('incidentTrendChart');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: @json($chartData['trends']->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))),
        datasets: [{
            label: 'Incidents',
            data: @json($chartData['trends']->pluck('count')),
            borderColor: '#0041E0', // info
            backgroundColor: 'rgba(0, 65, 224, 0.1)',
            tension: 0.4,
            fill: true,
            pointRadius: 4,
            pointHoverRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: { size: 14 },
                bodyFont: { size: 13 },
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Severity Distribution Chart
const severityCtx = document.getElementById('severityChart');
new Chart(severityCtx, {
    type: 'doughnut',
    data: {
        labels: @json($chartData['severity']->pluck('severity_level')->map(fn($s) => ucfirst($s))),
        datasets: [{
            data: @json($chartData['severity']->pluck('count')),
            backgroundColor: [
                '#D6143A', // critical - error
                '#E4AD21', // high - warning
                '#0041E0', // medium - info
                '#00934F', // low - success
            ],
            borderWidth: 2,
            borderColor: '#ffffff',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: { size: 12 }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    }
});

// Incident Type Chart
const typeCtx = document.getElementById('typeChart');
new Chart(typeCtx, {
    type: 'bar',
    data: {
        labels: @json($chartData['types']->pluck('incident_type')->map(fn($t) => ucfirst(str_replace('_', ' ', $t)))),
        datasets: [{
            label: 'Incidents',
            data: @json($chartData['types']->pluck('count')),
            backgroundColor: '#D14E24', // primary
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Hourly Distribution Chart
const hourlyCtx = document.getElementById('hourlyChart');
new Chart(hourlyCtx, {
    type: 'bar',
    data: {
        labels: Array.from({length: 24}, (_, i) => `${i}:00`),
        datasets: [{
            label: 'Incidents',
            data: @json(array_values(array_replace(array_fill(0, 24, 0), $peakTimes['by_hour']->toArray()))),
            backgroundColor: '#3FA09A', // accent
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Daily Distribution Chart
const dailyCtx = document.getElementById('dailyChart');
const daysOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
const dailyData = daysOrder.map(day => @json($peakTimes['by_day_of_week'])[day] || 0);

new Chart(dailyCtx, {
    type: 'bar',
    data: {
        labels: daysOrder.map(d => d.slice(0, 3)),
        datasets: [{
            label: 'Incidents',
            data: dailyData,
            backgroundColor: '#E4AD21', // warning
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Filter functions
function changeDateRange(days) {
    const url = new URL(window.location.href);
    url.searchParams.set('date_range', days);
    window.location.href = url.toString();
}

function changeMunicipality(muni) {
    const url = new URL(window.location.href);
    if (muni) {
        url.searchParams.set('municipality', muni);
    } else {
        url.searchParams.delete('municipality');
    }
    window.location.href = url.toString();
}
</script>
@endpush
@endsection
```

### Implementation Checklist

**Week 1: Setup & Charts**
- [ ] Day 1: Install Chart.js, update package.json
- [ ] Day 2: Update DashboardController with analytics method
- [ ] Day 3: Create KPI calculation methods
- [ ] Day 4: Create peak times analysis
- [ ] Day 5: Build Analytics Dashboard view with KPI cards

**Week 2: Charts & Polish**
- [ ] Day 1: Implement incident trend line chart
- [ ] Day 2: Implement severity doughnut chart
- [ ] Day 3: Implement incident type bar chart
- [ ] Day 4: Implement hourly/daily distribution charts
- [ ] Day 5: Add municipality comparison table, testing, polish

---

## 🔴 CRITICAL PRIORITY 3: WebSocket Real-Time Broadcasting

### Overview
**Gap Reference:** Objectives 2 & 4, Sections 2.7 & 4.5
**Status:** PARTIAL (30% - Toast notifications only)
**Business Impact:** HIGH - No live updates during emergencies
**Timeline:** 2 weeks
**Dependencies:** Pusher account or Laravel Echo Server

### Problem Statement
Staff must manually refresh dashboards to see new incidents or status changes. No automatic notifications when critical incidents are created.

**Current State:**
- ✅ Toast notifications for user actions
- ❌ No WebSocket/Pusher integration
- ❌ No real-time dashboard updates
- ❌ No live incident feed
- ❌ No automatic critical incident alerts

### User Stories

#### US-7: Receive Real-Time Incident Notifications
```
As a staff member
When a new incident is created or status changes
I want to receive an automatic notification
So that I can respond immediately without refreshing
```

**Acceptance Criteria:**
- [ ] Toast notification appears when new incident created
- [ ] Dashboard stats update automatically
- [ ] Incident list refreshes without page reload
- [ ] Audio alert plays for critical incidents
- [ ] Notification shows incident summary
- [ ] Click notification to view incident details
- [ ] Notification persists for 5 seconds
- [ ] Works across multiple browser tabs

#### US-8: View Live Dashboard Updates
```
As an admin
When viewing the dashboard
I want to see live updates as data changes
So that I always have current information
```

**Acceptance Criteria:**
- [ ] Dashboard statistics update in real-time
- [ ] New incidents appear in list automatically
- [ ] Status changes reflect immediately
- [ ] Vehicle assignments update live
- [ ] Charts update with new data
- [ ] Live indicator shows connection status
- [ ] Graceful handling of connection loss

### Technical Specifications

#### 1. Install Laravel Broadcasting

**Install Pusher PHP SDK:**
```bash
composer require pusher/pusher-php-server
```

**Install Laravel Echo & Pusher JS:**
```bash
npm install --save-dev laravel-echo pusher-js
```

#### 2. Configure Broadcasting

**Update `.env`:**
```env
BROADCAST_DRIVER=pusher

PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=ap1
```

**Update `config/broadcasting.php`:**
```php
'connections' => [
    'pusher' => [
        'driver' => 'pusher',
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'app_id' => env('PUSHER_APP_ID'),
        'options' => [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'host' => env('PUSHER_HOST') ?: 'api-'.env('PUSHER_APP_CLUSTER', 'mt1').'.pusher.com',
            'port' => env('PUSHER_PORT', 443),
            'scheme' => env('PUSHER_SCHEME', 'https'),
            'encrypted' => true,
            'useTLS' => env('PUSHER_SCHEME', 'https') === 'https',
        ],
    ],
],
```

#### 3. Create Broadcast Events

**Incident Created Event:**
```php
<?php
// app/Events/IncidentCreated.php

namespace App\Events;

use App\Models\Incident;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IncidentCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $incident;

    public function __construct(Incident $incident)
    {
        $this->incident = $incident->load(['reporter', 'assignedStaff', 'assignedVehicle']);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('incidents'),
            new Channel("municipality.{$this->incident->municipality}"),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'incident.created';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->incident->id,
            'incident_type' => $this->incident->incident_type,
            'severity_level' => $this->incident->severity_level,
            'location' => $this->incident->location,
            'municipality' => $this->incident->municipality,
            'reporter' => $this->incident->reporter?->name,
            'incident_date' => $this->incident->incident_date,
            'created_at' => $this->incident->created_at->toISOString(),
        ];
    }
}
```

**Incident Status Changed Event:**
```php
<?php
// app/Events/IncidentStatusChanged.php

namespace App\Events;

use App\Models\Incident;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IncidentStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $incident;
    public $oldStatus;
    public $newStatus;

    public function __construct(Incident $incident, $oldStatus, $newStatus)
    {
        $this->incident = $incident->load(['assignedStaff', 'assignedVehicle']);
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('incidents'),
            new Channel("municipality.{$this->incident->municipality}"),
            new Channel("incident.{$this->incident->id}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'incident.status-changed';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->incident->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'severity_level' => $this->incident->severity_level,
            'municipality' => $this->incident->municipality,
            'updated_at' => $this->incident->updated_at->toISOString(),
        ];
    }
}
```

**Critical Incident Alert Event:**
```php
<?php
// app/Events/CriticalIncidentAlert.php

namespace App\Events;

use App\Models\Incident;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CriticalIncidentAlert implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $incident;

    public function __construct(Incident $incident)
    {
        $this->incident = $incident->load(['reporter']);
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('critical-alerts'),
            new Channel("municipality.{$this->incident->municipality}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'critical.alert';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->incident->id,
            'incident_type' => $this->incident->incident_type,
            'location' => $this->incident->location,
            'municipality' => $this->incident->municipality,
            'reporter' => $this->incident->reporter?->name,
            'description' => Str::limit($this->incident->description, 100),
            'incident_date' => $this->incident->incident_date,
            'created_at' => $this->incident->created_at->toISOString(),
        ];
    }
}
```

#### 4. Dispatch Events in Controllers

**Update IncidentController:**
```php
use App\Events\IncidentCreated;
use App\Events\IncidentStatusChanged;
use App\Events\CriticalIncidentAlert;

// In store() method
public function store(StoreIncidentRequest $request)
{
    // ... existing code ...

    $incident = $this->incidentService->createIncident($validated);

    // Broadcast incident created event
    broadcast(new IncidentCreated($incident))->toOthers();

    // If critical, send alert
    if ($incident->severity_level === 'critical') {
        broadcast(new CriticalIncidentAlert($incident));
    }

    // ... existing code ...
}

// In updateStatus() method
public function updateStatus(Request $request, Incident $incident)
{
    $oldStatus = $incident->status;

    // ... existing code ...

    $incident->update(['status' => $request->status]);

    // Broadcast status change
    broadcast(new IncidentStatusChanged($incident, $oldStatus, $request->status));

    // ... existing code ...
}
```

#### 5. Frontend Laravel Echo Setup

**Update `resources/js/bootstrap.js`:**
```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    encrypted: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    }
});

// Connection status monitoring
window.Echo.connector.pusher.connection.bind('connected', function() {
    console.log('✅ WebSocket connected');
    updateConnectionStatus(true);
});

window.Echo.connector.pusher.connection.bind('disconnected', function() {
    console.log('❌ WebSocket disconnected');
    updateConnectionStatus(false);
});

window.Echo.connector.pusher.connection.bind('error', function(error) {
    console.error('WebSocket error:', error);
    updateConnectionStatus(false);
});

function updateConnectionStatus(connected) {
    const indicator = document.getElementById('ws-connection-indicator');
    if (indicator) {
        indicator.classList.toggle('connected', connected);
        indicator.classList.toggle('disconnected', !connected);
        indicator.title = connected ? 'Connected' : 'Disconnected';
    }
}
```

#### 6. Dashboard Real-Time Updates

**Add to Dashboard view:**
```blade
{{-- Connection Indicator --}}
<div class="fixed bottom-4 right-4 z-50">
    <div id="ws-connection-indicator" class="flex items-center gap-2 bg-white px-4 py-2 rounded-full shadow-lg border border-gray-200">
        <div class="w-3 h-3 rounded-full bg-success animate-pulse"></div>
        <span class="text-sm font-medium text-gray-700">Live</span>
    </div>
</div>

@push('scripts')
<script>
// Listen for new incidents
window.Echo.channel('incidents')
    .listen('.incident.created', (data) => {
        console.log('New incident:', data);

        // Show toast notification
        showInfoToast(`New ${data.severity_level} incident reported in ${data.municipality}`);

        // Play sound for critical incidents
        if (data.severity_level === 'critical') {
            playAlertSound();
        }

        // Update dashboard stats
        updateDashboardStats();

        // Add to incident list if on incidents page
        if (window.location.pathname.includes('/incidents')) {
            prependIncidentToList(data);
        }
    })
    .listen('.incident.status-changed', (data) => {
        console.log('Incident status changed:', data);

        showSuccessToast(`Incident #${data.id} status updated to ${data.new_status}`);

        // Update incident row if visible
        updateIncidentRow(data.id, data);

        // Update dashboard stats
        updateDashboardStats();
    });

// Listen for critical alerts
window.Echo.channel('critical-alerts')
    .listen('.critical.alert', (data) => {
        console.log('Critical incident alert:', data);

        // Show prominent alert
        showCriticalAlert(data);

        // Play alert sound
        playAlertSound();

        // Desktop notification if permitted
        if (Notification.permission === 'granted') {
            new Notification('🚨 CRITICAL INCIDENT', {
                body: `${data.incident_type} at ${data.location}`,
                icon: '/images/logo.png',
                tag: `critical-${data.id}`,
                requireInteraction: true
            });
        }
    });

// Municipality-specific channel (for non-admin users)
@if(auth()->user()->role !== 'admin')
    window.Echo.channel('municipality.{{ auth()->user()->municipality }}')
        .listen('.incident.created', (data) => {
            // Municipality-specific handling
            console.log('New incident in my municipality:', data);
        });
@endif

// Helper functions
function updateDashboardStats() {
    fetch('/api/dashboard/statistics')
        .then(response => response.json())
        .then(data => {
            // Update stat cards
            document.querySelector('[data-stat="total"]').textContent = data.total_incidents;
            document.querySelector('[data-stat="critical"]').textContent = data.critical_incidents;
            document.querySelector('[data-stat="active"]').textContent = data.active_incidents;
            // ... update other stats
        });
}

function prependIncidentToList(incident) {
    const tableBody = document.querySelector('#incidents-table tbody');
    if (!tableBody) return;

    // Create new row HTML
    const row = createIncidentRow(incident);

    // Prepend to table with animation
    tableBody.insertAdjacentHTML('afterbegin', row);
    const newRow = tableBody.firstElementChild;
    newRow.classList.add('animate-pulse-once', 'bg-success/10');

    setTimeout(() => {
        newRow.classList.remove('animate-pulse-once', 'bg-success/10');
    }, 2000);
}

function updateIncidentRow(incidentId, data) {
    const row = document.querySelector(`tr[data-incident-id="${incidentId}"]`);
    if (!row) return;

    // Update status badge
    const statusBadge = row.querySelector('.status-badge');
    if (statusBadge) {
        statusBadge.className = `badge status-badge badge-${getStatusBadgeClass(data.new_status)}`;
        statusBadge.textContent = data.new_status.charAt(0).toUpperCase() + data.new_status.slice(1);
    }

    // Highlight row briefly
    row.classList.add('bg-info/10');
    setTimeout(() => row.classList.remove('bg-info/10'), 1500);
}

function showCriticalAlert(incident) {
    // Create alert modal or prominent notification
    const alert = `
        <div class="alert alert-error shadow-2xl animate-shake" id="critical-alert-${incident.id}">
            <div>
                <i class="fas fa-exclamation-triangle text-2xl"></i>
                <div>
                    <h3 class="font-bold text-lg">🚨 CRITICAL INCIDENT</h3>
                    <div class="text-sm">${incident.incident_type} at ${incident.location}</div>
                    <div class="text-xs opacity-75 mt-1">${incident.description}</div>
                </div>
            </div>
            <div class="flex-none">
                <a href="/incidents/${incident.id}" class="btn btn-sm btn-primary">View</a>
                <button class="btn btn-sm btn-ghost" onclick="dismissAlert('${incident.id}')">Dismiss</button>
            </div>
        </div>
    `;

    const container = document.getElementById('critical-alerts-container') || createAlertsContainer();
    container.insertAdjacentHTML('beforeend', alert);
}

function createAlertsContainer() {
    const container = document.createElement('div');
    container.id = 'critical-alerts-container';
    container.className = 'fixed top-20 right-4 z-50 space-y-2 max-w-md';
    document.body.appendChild(container);
    return container;
}

function dismissAlert(incidentId) {
    const alert = document.getElementById(`critical-alert-${incidentId}`);
    if (alert) {
        alert.classList.add('animate-fade-out');
        setTimeout(() => alert.remove(), 300);
    }
}

function playAlertSound() {
    // Create audio element
    const audio = new Audio('/sounds/alert.mp3');
    audio.volume = 0.5;
    audio.play().catch(e => console.log('Audio play failed:', e));
}

function getStatusBadgeClass(status) {
    const classes = {
        'pending': 'warning',
        'active': 'info',
        'resolved': 'success',
        'closed': 'ghost'
    };
    return classes[status] || 'ghost';
}

function createIncidentRow(incident) {
    return `
        <tr data-incident-id="${incident.id}" class="hover">
            <td>${incident.id}</td>
            <td>
                <span class="badge badge-${getSeverityBadgeClass(incident.severity_level)}">
                    ${incident.severity_level}
                </span>
            </td>
            <td>${incident.incident_type}</td>
            <td>${incident.location}, ${incident.municipality}</td>
            <td>${new Date(incident.created_at).toLocaleString()}</td>
            <td>
                <span class="badge status-badge badge-warning">Pending</span>
            </td>
            <td>
                <a href="/incidents/${incident.id}" class="btn btn-sm btn-primary">
                    <i class="fas fa-eye"></i> View
                </a>
            </td>
        </tr>
    `;
}

function getSeverityBadgeClass(severity) {
    const classes = {
        'critical': 'error',
        'high': 'warning',
        'medium': 'info',
        'low': 'success'
    };
    return classes[severity] || 'ghost';
}

// Request notification permission on load
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}
</script>

{{-- Add shake animation --}}
<style>
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

.animate-shake {
    animation: shake 0.5s ease-in-out;
}

@keyframes pulse-once {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.animate-pulse-once {
    animation: pulse-once 1s ease-in-out 3;
}

@keyframes fade-out {
    from { opacity: 1; transform: translateY(0); }
    to { opacity: 0; transform: translateY(-20px); }
}

.animate-fade-out {
    animation: fade-out 0.3s ease-in-out forwards;
}

#ws-connection-indicator.connected .w-3 {
    background-color: oklch(52% 0.154 150.069); /* success */
}

#ws-connection-indicator.disconnected .w-3 {
    background-color: oklch(51% 0.222 16.935); /* error */
    animation: none;
}
</style>
@endpush
```

### Implementation Checklist

**Week 1: Backend Setup**
- [ ] Day 1: Install Pusher SDK and configure .env
- [ ] Day 2: Create IncidentCreated event
- [ ] Day 3: Create IncidentStatusChanged event
- [ ] Day 4: Create CriticalIncidentAlert event
- [ ] Day 5: Update IncidentController to dispatch events

**Week 2: Frontend Integration**
- [ ] Day 1: Install Laravel Echo and Pusher JS
- [ ] Day 2: Configure Echo in bootstrap.js
- [ ] Day 3: Add real-time listeners to Dashboard
- [ ] Day 4: Add connection indicator and notifications
- [ ] Day 5: Testing, audio alerts, desktop notifications

---

**Continue to next comment for remaining features...**
