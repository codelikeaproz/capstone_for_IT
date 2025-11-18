<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hospital extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospital_name',
        'contact_number',
        'address',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Relationships
    public function referrals(): HasMany
    {
        return $this->hasMany(HospitalReferral::class, 'hospital_id');
    }

    public function initialReferrals(): HasMany
    {
        return $this->hasMany(HospitalReferral::class, 'initial_hospital_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'active' => 'badge-success',
            'inactive' => 'badge-neutral',
            default => 'badge-ghost'
        };
    }

    public function getReferralCountAttribute()
    {
        return $this->referrals()->count();
    }
}
