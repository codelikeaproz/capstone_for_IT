<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleDispatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'incident_id',
        'assignment_id',
        'dispatch_location',
        'notes',
        'status',
        'dispatched_at',
        'arrived_at',
        'completed_at',
    ];

    protected $casts = [
        'dispatched_at' => 'datetime',
        'arrived_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignment_id');
    }

    public function responders(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'dispatched_responders', 'dispatch_id', 'responder_id')
            ->withPivot(['team_unit', 'position', 'notes'])
            ->withTimestamps();
    }

    public function fuelConsumptions(): HasMany
    {
        return $this->hasMany(FuelConsumption::class, 'dispatch_id');
    }

    // Scopes
    public function scopeDispatched($query)
    {
        return $query->where('status', 'dispatched');
    }

    public function scopeEnRoute($query)
    {
        return $query->where('status', 'en_route');
    }

    public function scopeArrived($query)
    {
        return $query->where('status', 'arrived');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['dispatched', 'en_route', 'arrived']);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'dispatched' => 'badge-info',
            'en_route' => 'badge-warning',
            'arrived' => 'badge-primary',
            'completed' => 'badge-success',
            'cancelled' => 'badge-neutral',
            default => 'badge-ghost'
        };
    }

    public function getResponseTimeAttribute()
    {
        if ($this->dispatched_at && $this->arrived_at) {
            return $this->dispatched_at->diffInMinutes($this->arrived_at);
        }
        return null;
    }

    public function getTotalDurationAttribute()
    {
        if ($this->dispatched_at && $this->completed_at) {
            return $this->dispatched_at->diffInMinutes($this->completed_at);
        }
        return null;
    }

    public function getTotalFuelConsumedAttribute()
    {
        return $this->fuelConsumptions()->sum('fuel_consumed');
    }

    public function getTotalDistanceTraveledAttribute()
    {
        return $this->fuelConsumptions()->sum('distance_traveled');
    }

    // Methods
    public function markAsEnRoute()
    {
        $this->update(['status' => 'en_route']);
    }

    public function markAsArrived()
    {
        $this->update([
            'status' => 'arrived',
            'arrived_at' => now(),
        ]);
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }
}
