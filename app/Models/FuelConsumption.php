<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelConsumption extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispatch_id',
        'starting_odometer',
        'ending_odometer',
        'distance_traveled',
        'fuel_consumed',
        'fuel_price_per_liter',
        'total_cost',
        'fuel_type',
        'timestamp',
    ];

    protected $casts = [
        'starting_odometer' => 'integer',
        'ending_odometer' => 'integer',
        'distance_traveled' => 'decimal:2',
        'fuel_consumed' => 'decimal:2',
        'fuel_price_per_liter' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'timestamp' => 'datetime',
    ];

    // Relationships
    public function dispatch(): BelongsTo
    {
        return $this->belongsTo(VehicleDispatch::class, 'dispatch_id');
    }

    // Scopes
    public function scopeByFuelType($query, $type)
    {
        return $query->where('fuel_type', $type);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('timestamp', '>=', now()->subDays($days));
    }

    public function scopeByVehicle($query, $vehicleId)
    {
        return $query->whereHas('dispatch', function($q) use ($vehicleId) {
            $q->where('vehicle_id', $vehicleId);
        });
    }

    // Accessors
    public function getFuelEfficiencyAttribute()
    {
        if ($this->distance_traveled && $this->fuel_consumed && $this->fuel_consumed > 0) {
            return round($this->distance_traveled / $this->fuel_consumed, 2);
        }
        return null;
    }

    public function getFormattedTotalCostAttribute()
    {
        return $this->total_cost ? '₱ ' . number_format($this->total_cost, 2) : 'N/A';
    }

    public function getFormattedTimestampAttribute()
    {
        return $this->timestamp ? $this->timestamp->format('M d, Y H:i') : null;
    }

    public function getFuelTypeBadgeAttribute()
    {
        return match($this->fuel_type) {
            'gasoline' => 'badge-warning',
            'diesel' => 'badge-info',
            'lpg' => 'badge-success',
            'electric' => 'badge-primary',
            default => 'badge-neutral'
        };
    }

    // Methods
    public function calculateTotalCost()
    {
        if ($this->fuel_consumed && $this->fuel_price_per_liter) {
            $this->total_cost = $this->fuel_consumed * $this->fuel_price_per_liter;
            $this->save();
        }
    }

    public function calculateDistance()
    {
        if ($this->starting_odometer && $this->ending_odometer) {
            $this->distance_traveled = $this->ending_odometer - $this->starting_odometer;
            $this->save();
        }
    }
}
