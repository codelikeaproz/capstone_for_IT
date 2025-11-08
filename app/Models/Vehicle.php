<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
// use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Log;
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

    public function utilizations(): HasMany
    {
        return $this->hasMany(VehicleUtilization::class);
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
        return match ($this->status) {
            'available' => 'badge-success',
            'in_use' => 'badge-warning',
            'maintenance' => 'badge-info',
            'out_of_service' => 'badge-error',
            default => 'badge-ghost'
        };
    }

    public function getVehicleTypeIconAttribute()
    {
        return match ($this->vehicle_type) {
            'ambulance' => 'fas fa-ambulance',
            'fire_truck' => 'fas fa-fire-extinguisher',
            'rescue_vehicle' => 'fas fa-truck-pickup',
            'patrol_car' => 'fas fa-car',
            'support_vehicle' => 'fas fa-truck',
            'traviz' => 'fas fa-van-shuttle',
            'pick_up' => 'fas fa-truck-pickup',
            default => 'fas fa-car'
        };
    }

    public function getVehicleTypeFormattedAttribute()
    {
        return match ($this->vehicle_type) {
            'ambulance' => 'Ambulance',
            'fire_truck' => 'Fire Truck',
            'rescue_vehicle' => 'Rescue Vehicle',
            'patrol_car' => 'Patrol Car',
            'support_vehicle' => 'Support Vehicle',
            'traviz' => 'TRAVIZ',
            'pick_up' => 'Pick-Up',
            default => ucwords(str_replace('_', ' ', $this->vehicle_type))
        };
    }

    /**
     * Accessor to ensure equipment_list is ALWAYS an array
     * Handles edge cases: null, string JSON, double-encoded JSON, already array
     *
     * This prevents count() errors in views and ensures type safety
     */
    public function getEquipmentListAttribute($value)
    {
        // If null, return empty array
        if (is_null($value)) {
            return [];
        }

        // If already an array, return as-is
        if (is_array($value)) {
            return $value;
        }

        // If it's a string, try to decode it as JSON
        if (is_string($value)) {
            // Empty string edge case
            if (trim($value) === '') {
                return [];
            }

            // First decode attempt
            $decoded = json_decode($value, true);

            // If first decode returned an array, we're done
            if (is_array($decoded)) {
                return $decoded;
            }

            // If first decode returned a string, it might be double-encoded
            // Try decoding again (handles PostgreSQL JSON quirks)
            if (is_string($decoded)) {
                $secondDecode = json_decode($decoded, true);
                if (is_array($secondDecode)) {
                    return $secondDecode;
                }
            }

            // If both decode attempts failed, log and return empty array
            \Log::warning('Failed to decode equipment_list JSON for vehicle', [
                'vehicle_id' => $this->id ?? 'unknown',
                'value' => substr($value, 0, 100), // Log first 100 chars only
                'first_decode_type' => gettype($decoded),
                'error' => json_last_error_msg()
            ]);
            return [];
        }

        // Fallback: return empty array for any other unexpected type
        return [];
    }

    /**
     * Mutator to ensure equipment_list is properly stored as JSON
     */
    public function setEquipmentListAttribute($value)
    {
        // If null or empty, store as empty JSON array
        if (is_null($value) || (is_array($value) && empty($value))) {
            $this->attributes['equipment_list'] = json_encode([]);
            return;
        }

        // If it's already a JSON string, store as-is
        if (is_string($value)) {
            // Validate it's valid JSON
            json_decode($value);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->attributes['equipment_list'] = $value;
                return;
            }
        }

        // If it's an array, encode it
        if (is_array($value)) {
            $this->attributes['equipment_list'] = json_encode(array_values($value));
            return;
        }

        // Fallback: store as empty array
        $this->attributes['equipment_list'] = json_encode([]);
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
