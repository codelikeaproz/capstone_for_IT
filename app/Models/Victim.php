<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Victim extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_id',
        'first_name',
        'last_name',
        'age',
        'gender',
        'contact_number',
        'address',
        'id_number',
        'medical_status',
        'injury_description',
        'medical_treatment',
        'hospital_referred',
        'transportation_method',
        'hospital_arrival_time',
        'helmet_used',
        'seatbelt_used',
        'protective_gear_used',
        'victim_role',
        'vehicle_type_involved',
        'seating_position',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'insurance_provider',
        'insurance_policy_number',
        'legal_action_required',
    ];

    protected $casts = [
        'age' => 'integer',
        'hospital_arrival_time' => 'datetime',
        'helmet_used' => 'boolean',
        'seatbelt_used' => 'boolean',
        'protective_gear_used' => 'boolean',
        'legal_action_required' => 'boolean',
    ];

    // Relationships
    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getMedicalStatusBadgeAttribute()
    {
        return match($this->medical_status) {
            'uninjured' => 'badge-success',
            'minor_injury' => 'badge-warning',
            'major_injury' => 'badge-error',
            'critical' => 'badge-error',
            'deceased' => 'badge-neutral',
            default => 'badge-ghost'
        };
    }

    // Scopes
    public function scopeByMedicalStatus($query, $status)
    {
        return $query->where('medical_status', $status);
    }

    public function scopeInjured($query)
    {
        return $query->whereIn('medical_status', ['minor_injury', 'major_injury', 'critical']);
    }

    public function scopeCritical($query)
    {
        return $query->whereIn('medical_status', ['critical', 'deceased']);
    }
}
