<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Vehicle extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'vehicle_number',
        'license_plate',
        'vehicle_type',
        'status',
        'make',
        'model',
        'year',
        'color',
        'fuel_capacity',
        'current_fuel_level',
        'fuel_consumption_rate',
        'odometer_reading',
        'total_distance',
        'municipality',
        'assigned_driver_id',
        'current_incident_id',
        'last_maintenance_date',
        'next_maintenance_due',
        'maintenance_notes',
        'insurance_policy',
        'insurance_expiry',
        'registration_expiry',
        'equipment_list',
        'gps_enabled',
        'current_latitude',
        'current_longitude',
    ];

    protected $casts = [
        'fuel_capacity' => 'decimal:2',
        'current_fuel_level' => 'decimal:2',
        'fuel_consumption_rate' => 'decimal:2',
        'odometer_reading' => 'integer',
        'total_distance' => 'integer',
        'last_maintenance_date' => 'date',
        'next_maintenance_due' => 'date',
        'insurance_expiry' => 'date',
        'registration_expiry' => 'date',
        'equipment_list' => 'array',
        'gps_enabled' => 'boolean',
        'current_latitude' => 'decimal:8',
        'current_longitude' => 'decimal:8',
        'year' => 'integer',
    ];

    // Activity logging
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['vehicle_number', 'status', 'assigned_driver_id', 'current_incident_id', 'current_fuel_level'])
            ->logOnlyDirty();
    }

    // Relationships
    public function assignedDriver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_driver_id');
    }

    public function currentIncident(): BelongsTo
    {
        return $this->belongsTo(Incident::class, 'current_incident_id');
    }

    public function assignedIncidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'assigned_vehicle_id');
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeInUse($query)
    {
        return $query->where('status', 'in_use');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('vehicle_type', $type);
    }

    public function scopeByMunicipality($query, $municipality)
    {
        return $query->where('municipality', $municipality);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'available' => 'badge-success',
            'in_use' => 'badge-warning',
            'maintenance' => 'badge-info',
            'out_of_service' => 'badge-error',
            default => 'badge-ghost'
        };
    }

    public function getVehicleTypeIconAttribute()
    {
        return match($this->vehicle_type) {
            'ambulance' => 'fas fa-ambulance',
            'fire_truck' => 'fas fa-fire-extinguisher',
            'rescue_vehicle' => 'fas fa-truck-pickup',
            'patrol_car' => 'fas fa-car',
            'support_vehicle' => 'fas fa-truck',
            default => 'fas fa-car'
        };
    }

    public function getFuelLevelPercentageAttribute()
    {
        return min(100, max(0, $this->current_fuel_level));
    }

    public function getMaintenanceStatusAttribute()
    {
        if (!$this->next_maintenance_due) {
            return 'unknown';
        }
        
        $daysUntilMaintenance = now()->diffInDays($this->next_maintenance_due, false);
        
        if ($daysUntilMaintenance < 0) {
            return 'overdue';
        } elseif ($daysUntilMaintenance <= 7) {
            return 'due_soon';
        } else {
            return 'good';
        }
    }

    // Methods
    public function assignToIncident($incidentId)
    {
        $this->update([
            'current_incident_id' => $incidentId,
            'status' => 'in_use'
        ]);
    }

    public function releaseFromIncident()
    {
        $this->update([
            'current_incident_id' => null,
            'status' => 'available'
        ]);
    }

    public function updateFuelLevel($level)
    {
        $this->update(['current_fuel_level' => max(0, min(100, $level))]);
    }

    public function updateLocation($latitude, $longitude)
    {
        $this->update([
            'current_latitude' => $latitude,
            'current_longitude' => $longitude
        ]);
    }

    public function addDistance($kilometers)
    {
        $this->increment('odometer_reading', $kilometers);
        $this->increment('total_distance', $kilometers);
    }

    public function scheduleMaintenanceAlert($days = 30)
    {
        $this->update([
            'next_maintenance_due' => now()->addDays($days)
        ]);
    }

    public function isAvailableForAssignment()
    {
        return $this->status === 'available' && $this->current_fuel_level > 10;
    }
}
