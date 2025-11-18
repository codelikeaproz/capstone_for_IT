<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HospitalReferral extends Model
{
    use HasFactory;

    protected $fillable = [
        'victim_id',
        'hospital_id',
        'initial_hospital_id',
        'referral_reason',
        'medical_notes',
        'transported_at',
        'status',
    ];

    protected $casts = [
        'transported_at' => 'datetime',
    ];

    // Relationships
    public function victim(): BelongsTo
    {
        return $this->belongsTo(Victim::class);
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class, 'hospital_id');
    }

    public function initialHospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class, 'initial_hospital_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInTransit($query)
    {
        return $query->where('status', 'in_transit');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => 'badge-warning',
            'in_transit' => 'badge-info',
            'completed' => 'badge-success',
            'cancelled' => 'badge-neutral',
            default => 'badge-ghost'
        };
    }

    public function getFormattedTransportedAtAttribute()
    {
        return $this->transported_at ? $this->transported_at->format('M d, Y H:i') : 'Not transported yet';
    }
}
