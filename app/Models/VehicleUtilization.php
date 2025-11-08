<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleUtilization extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'victim_id',
        'incident_id',
        'driver_id',
        'service_date',
        'trip_ticket_number',
        'origin_address',
        'destination_address',
        'service_category',
        'service_type',
        'fuel_consumed',
        'distance_traveled',
        'status',
        'notes',
        'municipality',
    ];

    protected $casts = [
        'service_date' => 'date',
        'fuel_consumed' => 'decimal:2',
        'distance_traveled' => 'decimal:2',
    ];

    // Relationships
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function victim(): BelongsTo
    {
        return $this->belongsTo(Victim::class);
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    // Scopes
    public function scopeByMonth($query, $year, $month)
    {
        return $query->whereYear('service_date', $year)
                     ->whereMonth('service_date', $month);
    }

    public function scopeByServiceCategory($query, $category)
    {
        return $query->where('service_category', $category);
    }

    public function scopeByVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Accessors
    public function getServiceTypeFormattedAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->service_type));
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'scheduled' => 'badge-info',
            'in_progress' => 'badge-warning',
            'completed' => 'badge-success',
            'cancelled' => 'badge-error',
            default => 'badge-ghost'
        };
    }
}
