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
        'birth_date',
        'gender',
        'contact_number',
        'address',
        'medical_status',
        'injury_description',
        'medical_treatment',
        'hospital_referred',
        'transportation_method',
        'hospital_arrival_time',
        'victim_role',
        // Pregnancy-related fields (Maternity cases)
        'is_pregnant',
        'labor_stage',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'hospital_arrival_time' => 'datetime',
        'is_pregnant' => 'boolean',
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

    /**
     * Calculate age from birth_date
     * Returns null if birth_date is not set
     *
     * @return int|null
     */
    public function getAgeAttribute()
    {
        if (!$this->birth_date) {
            return null;
        }

        return \Carbon\Carbon::parse($this->birth_date)->age;
    }

    /**
     * Get formatted birth date
     * Format: Month Day, Year (e.g., September 16, 2003)
     *
     * @return string|null
     */
    public function getFormattedBirthDateAttribute()
    {
        if (!$this->birth_date) {
            return null;
        }

        return \Carbon\Carbon::parse($this->birth_date)->format('F d, Y');
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
