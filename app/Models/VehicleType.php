<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * VehicleType Model
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property string|null $icon
 * @property string $color
 * @property array|null $typical_equipment
 * @property int|null $typical_capacity
 * @property float|null $typical_fuel_capacity
 * @property array|null $response_types
 * @property int $priority_level
 * @property bool $is_active
 * @property int $sort_order
 */
class VehicleType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'description', 'icon', 'color',
        'typical_equipment', 'typical_capacity', 'typical_fuel_capacity',
        'response_types', 'priority_level', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'typical_equipment' => 'array',
        'typical_capacity' => 'integer',
        'typical_fuel_capacity' => 'decimal:2',
        'response_types' => 'array',
        'priority_level' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = ['badge_color', 'icon_html'];

    // Relationships
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'vehicle_type_id');
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeByCode(Builder $query, string $code): Builder
    {
        return $query->where('code', $code);
    }

    public function scopeForIncidentType(Builder $query, string $incidentTypeCode): Builder
    {
        return $query->whereJsonContains('response_types', $incidentTypeCode);
    }

    // Accessors
    public function getBadgeColorAttribute(): string
    {
        return match($this->color) {
            'red' => 'badge-error',
            'orange', 'yellow' => 'badge-warning',
            'green' => 'badge-success',
            'blue', 'teal' => 'badge-info',
            default => 'badge-neutral',
        };
    }

    public function getIconHtmlAttribute(): string
    {
        return $this->icon ? "<i class=\"{$this->icon}\"></i>" : '<i class="fas fa-truck"></i>';
    }

    public function getTextColorAttribute(): string
    {
        return "text-{$this->color}-600";
    }

    // Helper Methods
    public function getStatistics(): array
    {
        return [
            'total_vehicles' => $this->vehicles()->count(),
            'available' => $this->vehicles()->available()->count(),
            'in_use' => $this->vehicles()->inUse()->count(),
            'maintenance' => $this->vehicles()->where('status', 'maintenance')->count(),
        ];
    }

    public function canRespondTo(string $incidentTypeCode): bool
    {
        return in_array($incidentTypeCode, $this->response_types ?? []);
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
