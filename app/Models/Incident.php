<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Incident extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'incident_number',
        'incident_type',
        'severity_level',
        'status',
        'location',
        'municipality',
        'barangay',
        'latitude',
        'longitude',
        'description',
        'incident_date',
        'weather_condition',
        'road_condition',
        'casualty_count',
        'injury_count',
        'fatality_count',
        'property_damage_estimate',
        'damage_description',
        'vehicle_involved',
        'vehicle_details',
        'assigned_staff_id',
        'assigned_vehicle_id',
        'reported_by',
        'photos',
        'videos',
        'documents',
        'response_time',
        'resolved_at',
        'resolution_notes',
        // Traffic Accident specific
        'vehicle_count',
        'license_plates',
        'driver_information',
        // Medical Emergency specific
        'medical_emergency_type',
        'ambulance_requested',
        'patient_count',
        'patient_symptoms',
        // Fire Incident specific
        'building_type',
        'fire_spread_level',
        'evacuation_required',
        'evacuated_count',
        'fire_cause',
        'buildings_affected',
        // Natural Disaster specific
        'disaster_type',
        'affected_area_size',
        'shelter_needed',
        'families_affected',
        'structures_damaged',
        'infrastructure_damage',
        // Criminal Activity specific
        'crime_type',
        'police_notified',
        'case_number',
        'suspect_description',
    ];

    protected $casts = [
        'incident_date' => 'datetime',
        'response_time' => 'datetime',
        'resolved_at' => 'datetime',
        'photos' => 'array',
        'videos' => 'array',
        'documents' => 'array',
        'license_plates' => 'array',
        'vehicle_involved' => 'boolean',
        'property_damage_estimate' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'casualty_count' => 'integer',
        'injury_count' => 'integer',
        'fatality_count' => 'integer',
        'vehicle_count' => 'integer',
        'patient_count' => 'integer',
        'evacuated_count' => 'integer',
        'buildings_affected' => 'integer',
        'families_affected' => 'integer',
        'structures_damaged' => 'integer',
        'affected_area_size' => 'decimal:2',
        'ambulance_requested' => 'boolean',
        'evacuation_required' => 'boolean',
        'shelter_needed' => 'boolean',
        'police_notified' => 'boolean',
    ];

    protected $dates = [
        'incident_date',
        'response_time',
        'resolved_at',
    ];

    // Relationships
    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    public function assignedVehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'assigned_vehicle_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function victims(): HasMany
    {
        return $this->hasMany(Victim::class);
    }

    // Scopes
    public function scopeByMunicipality($query, $municipality)
    {
        return $query->where('municipality', $municipality);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity_level', $severity);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'active']);
    }

    public function scopeResolved($query)
    {
        return $query->whereIn('status', ['resolved', 'closed']);
    }

    // Accessors
    public function getFormattedIncidentDateAttribute()
    {
        return $this->incident_date ? $this->incident_date->format('M d, Y H:i') : null;
    }

    public function getSeverityColorAttribute()
    {
        return match($this->severity_level) {
            'critical' => 'red',
            'high' => 'orange',
            'medium' => 'yellow',
            'low' => 'green',

            default => 'gray'
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => 'badge-warning',
            'active' => 'badge-info',
            'resolved' => 'badge-success',
            'closed' => 'badge-neutral',
            default => 'badge-ghost'
        };
    }

    // Methods
    /**
     * Generate unique incident number with collision prevention
     *
     * Uses multiple strategies to prevent duplicate numbers:
     * 1. Includes soft-deleted records in search
     * 2. Orders by incident_number (not id) for accuracy
     * 3. Uses pessimistic locking to prevent race conditions
     * 4. Validates uniqueness before returning
     *
     * Format: INC-YYYY-NNN (e.g., INC-2025-001)
     *
     * @return string
     * @throws \RuntimeException if unable to generate unique number after retries
     */
    public static function generateIncidentNumber(): string
    {
        $maxRetries = 10;
        $attempt = 0;

        while ($attempt < $maxRetries) {
            try {
                return DB::transaction(function () {
                    $year = now()->year;

                    // CRITICAL FIX 1: Include soft-deleted records using withTrashed()
                    // CRITICAL FIX 2: Order by incident_number, not id
                    // CRITICAL FIX 3: Use lockForUpdate() for pessimistic locking
                    $lastIncident = self::withTrashed()
                        ->where('incident_number', 'like', "INC-{$year}-%")
                        ->orderByRaw("CAST(SUBSTRING(incident_number FROM '\\d+$') AS INTEGER) DESC")
                        ->lockForUpdate() // Locks the row until transaction completes
                        ->first();

                    if ($lastIncident) {
                        // Extract the numeric part (last 3 digits)
                        $lastNumber = intval(substr($lastIncident->incident_number, -3));
                        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
                    } else {
                        $newNumber = '001';
                    }

                    $incidentNumber = "INC-{$year}-{$newNumber}";

                    // CRITICAL FIX 4: Double-check uniqueness (including soft-deleted)
                    $exists = self::withTrashed()
                        ->where('incident_number', $incidentNumber)
                        ->exists();

                    if ($exists) {
                        throw new \RuntimeException("Generated incident number {$incidentNumber} already exists. Retrying...");
                    }

                    return $incidentNumber;
                });
            } catch (\RuntimeException $e) {
                $attempt++;

                if ($attempt >= $maxRetries) {
                    Log::error('Failed to generate unique incident number after ' . $maxRetries . ' attempts', [
                        'year' => $year ?? now()->year,
                        'error' => $e->getMessage()
                    ]);

                    throw new \RuntimeException(
                        'Unable to generate unique incident number. Please try again or contact support.',
                        0,
                        $e
                    );
                }

                // Exponential backoff: wait before retry
                usleep(100000 * pow(2, $attempt)); // 100ms, 200ms, 400ms, etc.
                continue;
            }
        }

        // Should never reach here, but just in case
        throw new \RuntimeException('Unexpected error in incident number generation');
    }

    public function markAsResolved($notes = null)
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolution_notes' => $notes,
        ]);
    }

    public function assignToStaff($staffId)
    {
        $this->update(['assigned_staff_id' => $staffId]);
    }

    public function assignVehicle($vehicleId)
    {
        $this->update(['assigned_vehicle_id' => $vehicleId]);
    }

    public function updateResponseTime()
    {
        if (!$this->response_time) {
            $this->update(['response_time' => now()]);
        }
    }


    

}
