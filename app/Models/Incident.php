<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Incident extends Model
{
    use HasFactory;

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
    ];

    protected $casts = [
        'incident_date' => 'datetime',
        'response_time' => 'datetime',
        'resolved_at' => 'datetime',
        'photos' => 'array',
        'videos' => 'array',
        'documents' => 'array',
        'vehicle_involved' => 'boolean',
        'property_damage_estimate' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'casualty_count' => 'integer',
        'injury_count' => 'integer',
        'fatality_count' => 'integer',
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
    public static function generateIncidentNumber()
    {
        $year = now()->year;
        $lastIncident = self::where('incident_number', 'like', "INC-{$year}-%")
                           ->orderBy('id', 'desc')
                           ->first();

        if ($lastIncident) {
            $lastNumber = intval(substr($lastIncident->incident_number, -3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "INC-{$year}-{$newNumber}";
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
