<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * MedicalStatus Model
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property int $severity_level
 * @property bool $requires_hospitalization
 * @property bool $requires_ambulance
 * @property bool $requires_immediate_care
 * @property string $color
 * @property string|null $badge_class
 * @property string|null $icon
 * @property bool $is_fatality
 * @property bool $counts_as_injury
 * @property bool $is_active
 * @property int $sort_order
 */
class MedicalStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'description', 'severity_level',
        'requires_hospitalization', 'requires_ambulance', 'requires_immediate_care',
        'color', 'badge_class', 'icon', 'is_fatality', 'counts_as_injury',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'severity_level' => 'integer',
        'requires_hospitalization' => 'boolean',
        'requires_ambulance' => 'boolean',
        'requires_immediate_care' => 'boolean',
        'is_fatality' => 'boolean',
        'counts_as_injury' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = ['badge_color', 'icon_html', 'care_level'];

    // Relationships
    public function victims(): HasMany
    {
        return $this->hasMany(Victim::class, 'medical_status_id');
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('severity_level');
    }

    public function scopeByCode(Builder $query, string $code): Builder
    {
        return $query->where('code', $code);
    }

    public function scopeFatalities(Builder $query): Builder
    {
        return $query->where('is_fatality', true);
    }

    public function scopeInjuries(Builder $query): Builder
    {
        return $query->where('counts_as_injury', true);
    }

    public function scopeRequiresHospitalization(Builder $query): Builder
    {
        return $query->where('requires_hospitalization', true);
    }

    public function scopeCriticalCare(Builder $query): Builder
    {
        return $query->where('requires_immediate_care', true);
    }

    // Accessors
    public function getBadgeColorAttribute(): string
    {
        return $this->badge_class ?? match($this->color) {
            'red' => 'badge-error',
            'orange' => 'badge-error',
            'yellow' => 'badge-warning',
            'green' => 'badge-success',
            'gray' => 'badge-neutral',
            default => 'badge-neutral',
        };
    }

    public function getIconHtmlAttribute(): string
    {
        return $this->icon ? "<i class=\"{$this->icon}\"></i>" : '<i class="fas fa-user-injured"></i>';
    }

    public function getTextColorAttribute(): string
    {
        return "text-{$this->color}-600";
    }

    public function getCareLevelAttribute(): string
    {
        if ($this->is_fatality) {
            return 'Deceased';
        }

        if ($this->requires_immediate_care) {
            return 'Critical Care';
        }

        if ($this->requires_hospitalization) {
            return 'Hospital Care';
        }

        if ($this->counts_as_injury) {
            return 'First Aid';
        }

        return 'No Care Needed';
    }

    // Helper Methods
    public function getStatistics(): array
    {
        return [
            'total_victims' => $this->victims()->count(),
            'recent_victims' => $this->victims()->where('created_at', '>=', now()->subDays(30))->count(),
        ];
    }

    public function isFatal(): bool
    {
        return $this->is_fatality;
    }

    public function requiresEmergencyResponse(): bool
    {
        return $this->requires_ambulance || $this->requires_immediate_care;
    }

    public static function getForSelect(): array
    {
        return static::active()->ordered()->pluck('name', 'id')->toArray();
    }

    public static function findByCode(string $code): ?static
    {
        return static::byCode($code)->first();
    }
}
