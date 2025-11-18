<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Municipality Model
 *
 * Represents municipalities in Bukidnon Province for the MDRRMC system.
 * Provides centralized municipality data with geographic and administrative information.
 *
 * @property int $id
 * @property string $name
 * @property string $province
 * @property string $region
 * @property string|null $zip_code
 * @property float|null $latitude
 * @property float|null $longitude
 * @property int|null $population
 * @property float|null $land_area_sqkm
 * @property string|null $contact_number
 * @property string|null $email
 * @property string|null $address
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Municipality extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'province',
        'region',
        'zip_code',
        'latitude',
        'longitude',
        'population',
        'land_area_sqkm',
        'contact_number',
        'email',
        'address',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'population' => 'integer',
        'land_area_sqkm' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get all incidents for this municipality.
     */
    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'municipality_id');
    }

    /**
     * Get all vehicles assigned to this municipality.
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'municipality_id');
    }

    /**
     * Get all requests from this municipality.
     */
    public function requests(): HasMany
    {
        return $this->hasMany(Request::class, 'municipality_id');
    }

    /**
     * Get all users assigned to this municipality.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'municipality_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope a query to only include active municipalities.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include municipalities in a specific province.
     */
    public function scopeInProvince(Builder $query, string $province): Builder
    {
        return $query->where('province', $province);
    }

    /**
     * Scope a query to only include municipalities in a specific region.
     */
    public function scopeInRegion(Builder $query, string $region): Builder
    {
        return $query->where('region', $region);
    }

    /**
     * Scope a query to order municipalities by population.
     */
    public function scopeOrderByPopulation(Builder $query, string $direction = 'desc'): Builder
    {
        return $query->orderBy('population', $direction);
    }

    /**
     * Scope a query to only include municipalities with coordinates.
     */
    public function scopeHasCoordinates(Builder $query): Builder
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    /**
     * Get the formatted display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->name}, {$this->province}";
    }

    /**
     * Get the full address with province and region.
     */
    public function getFullAddressAttribute(): string
    {
        return trim("{$this->address}, {$this->name}, {$this->province}, {$this->region}");
    }

    /**
     * Check if municipality has geographic coordinates.
     */
    public function getHasCoordinatesAttribute(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    /**
     * Get formatted population with commas.
     */
    public function getFormattedPopulationAttribute(): ?string
    {
        return $this->population ? number_format($this->population) : null;
    }

    /**
     * Get formatted land area.
     */
    public function getFormattedLandAreaAttribute(): ?string
    {
        return $this->land_area_sqkm ? number_format($this->land_area_sqkm, 2) . ' km²' : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get statistics for this municipality.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return [
            'total_incidents' => $this->incidents()->count(),
            'active_incidents' => $this->incidents()->active()->count(),
            'total_vehicles' => $this->vehicles()->count(),
            'available_vehicles' => $this->vehicles()->available()->count(),
            'pending_requests' => $this->requests()->pending()->count(),
            'total_staff' => $this->users()->where('role', 'staff')->count(),
        ];
    }

    /**
     * Calculate distance to another municipality in kilometers.
     *
     * @param Municipality $other
     * @return float|null
     */
    public function distanceTo(Municipality $other): ?float
    {
        if (!$this->has_coordinates || !$other->has_coordinates) {
            return null;
        }

        // Haversine formula
        $earthRadius = 6371; // kilometers

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($other->latitude);
        $lonTo = deg2rad($other->longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Get coordinates as array for mapping.
     *
     * @return array|null
     */
    public function getCoordinatesArray(): ?array
    {
        if (!$this->has_coordinates) {
            return null;
        }

        return [
            'lat' => (float) $this->latitude,
            'lng' => (float) $this->longitude,
        ];
    }
}
