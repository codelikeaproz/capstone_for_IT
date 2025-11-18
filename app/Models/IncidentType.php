<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * IncidentType Model
 *
 * Represents types of incidents that can be reported in the MDRRMC system.
 * Provides rich metadata for each incident type including icons, colors, and response requirements.
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property string $default_severity
 * @property string|null $icon
 * @property string $color
 * @property bool $requires_vehicle
 * @property bool $requires_medical_response
 * @property int $priority_level
 * @property bool $is_active
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class IncidentType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'default_severity',
        'icon',
        'color',
        'requires_vehicle',
        'requires_medical_response',
        'priority_level',
        'is_active',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'requires_vehicle' => 'boolean',
        'requires_medical_response' => 'boolean',
        'priority_level' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['badge_color', 'icon_html'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get all incidents of this type.
     */
    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'incident_type_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope a query to only include active incident types.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope a query to only include high priority types.
     */
    public function scopeHighPriority(Builder $query): Builder
    {
        return $query->where('priority_level', '<=', 2);
    }

    /**
     * Scope a query to only include types that require vehicles.
     */
    public function scopeRequiresVehicle(Builder $query): Builder
    {
        return $query->where('requires_vehicle', true);
    }

    /**
     * Scope a query to only include types that require medical response.
     */
    public function scopeRequiresMedicalResponse(Builder $query): Builder
    {
        return $query->where('requires_medical_response', true);
    }

    /**
     * Scope a query by specific code.
     */
    public function scopeByCode(Builder $query, string $code): Builder
    {
        return $query->where('code', $code);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    /**
     * Get the badge color class for UI display.
     */
    public function getBadgeColorAttribute(): string
    {
        return match($this->color) {
            'red' => 'badge-error',
            'orange' => 'badge-warning',
            'yellow' => 'badge-warning',
            'green' => 'badge-success',
            'blue' => 'badge-info',
            'purple', 'indigo' => 'badge-primary',
            default => 'badge-neutral',
        };
    }

    /**
     * Get the icon HTML with FontAwesome classes.
     */
    public function getIconHtmlAttribute(): string
    {
        if (!$this->icon) {
            return '<i class="fas fa-circle-exclamation"></i>';
        }

        return "<i class=\"{$this->icon}\"></i>";
    }

    /**
     * Get the text color class based on the color.
     */
    public function getTextColorAttribute(): string
    {
        return match($this->color) {
            'red' => 'text-red-600',
            'orange' => 'text-orange-600',
            'yellow' => 'text-yellow-600',
            'green' => 'text-green-600',
            'blue' => 'text-blue-600',
            'purple' => 'text-purple-600',
            'indigo' => 'text-indigo-600',
            default => 'text-gray-600',
        };
    }

    /**
     * Get the background color class for badges.
     */
    public function getBgColorAttribute(): string
    {
        return match($this->color) {
            'red' => 'bg-red-100',
            'orange' => 'bg-orange-100',
            'yellow' => 'bg-yellow-100',
            'green' => 'bg-green-100',
            'blue' => 'bg-blue-100',
            'purple' => 'bg-purple-100',
            'indigo' => 'bg-indigo-100',
            default => 'bg-gray-100',
        };
    }

    /**
     * Get formatted priority level.
     */
    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority_level) {
            1 => 'Critical',
            2 => 'High',
            3 => 'Medium',
            4 => 'Low',
            5 => 'Minimal',
            default => 'Unknown',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get statistics for this incident type.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return [
            'total_incidents' => $this->incidents()->count(),
            'active_incidents' => $this->incidents()->active()->count(),
            'resolved_incidents' => $this->incidents()->resolved()->count(),
            'critical_incidents' => $this->incidents()->where('severity_level', 'critical')->count(),
        ];
    }

    /**
     * Check if this type requires emergency response.
     *
     * @return bool
     */
    public function isEmergency(): bool
    {
        return $this->priority_level <= 2;
    }

    /**
     * Get recommended vehicle types for this incident type.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecommendedVehicles()
    {
        if (!$this->requires_vehicle) {
            return collect([]);
        }

        return VehicleType::active()
            ->where(function($query) {
                $query->whereJsonContains('response_types', $this->code)
                      ->orWhere('priority_level', '<=', $this->priority_level);
            })
            ->ordered()
            ->get();
    }

    /**
     * Get for select dropdown.
     *
     * @return array
     */
    public static function getForSelect(): array
    {
        return static::active()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * Get by code or fail.
     *
     * @param string $code
     * @return static
     */
    public static function findByCode(string $code): ?static
    {
        return static::byCode($code)->first();
    }
}
