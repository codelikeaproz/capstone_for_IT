<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * SeverityLevel Model
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property int $priority_level
 * @property int|null $response_time_minutes
 * @property string $color
 * @property string|null $badge_class
 * @property string|null $icon
 * @property bool $requires_immediate_notification
 * @property bool $requires_supervisor_approval
 * @property bool $is_active
 * @property int $sort_order
 */
class SeverityLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'description', 'priority_level', 'response_time_minutes',
        'color', 'badge_class', 'icon', 'requires_immediate_notification',
        'requires_supervisor_approval', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'priority_level' => 'integer',
        'response_time_minutes' => 'integer',
        'requires_immediate_notification' => 'boolean',
        'requires_supervisor_approval' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = ['badge_color', 'icon_html', 'urgency_label'];

    // Relationships
    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'severity_level_id');
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('priority_level');
    }

    public function scopeByCode(Builder $query, string $code): Builder
    {
        return $query->where('code', $code);
    }

    public function scopeCritical(Builder $query): Builder
    {
        return $query->where('priority_level', 1);
    }

    public function scopeRequiresNotification(Builder $query): Builder
    {
        return $query->where('requires_immediate_notification', true);
    }

    // Accessors
    public function getBadgeColorAttribute(): string
    {
        return $this->badge_class ?? match($this->color) {
            'red' => 'badge-error',
            'orange', 'yellow' => 'badge-warning',
            'green' => 'badge-success',
            default => 'badge-neutral',
        };
    }

    public function getIconHtmlAttribute(): string
    {
        return $this->icon ? "<i class=\"{$this->icon}\"></i>" : '<i class="fas fa-exclamation-triangle"></i>';
    }

    public function getTextColorAttribute(): string
    {
        return "text-{$this->color}-600";
    }

    public function getUrgencyLabelAttribute(): string
    {
        return match($this->priority_level) {
            1 => 'URGENT',
            2 => 'High Priority',
            3 => 'Normal',
            4 => 'Low Priority',
            default => 'Unknown',
        };
    }

    public function getFormattedResponseTimeAttribute(): ?string
    {
        if (!$this->response_time_minutes) {
            return null;
        }

        if ($this->response_time_minutes < 60) {
            return "{$this->response_time_minutes} minutes";
        }

        $hours = floor($this->response_time_minutes / 60);
        $mins = $this->response_time_minutes % 60;

        return $mins > 0 ? "{$hours}h {$mins}m" : "{$hours} hour" . ($hours > 1 ? 's' : '');
    }

    // Helper Methods
    public function getStatistics(): array
    {
        return [
            'total_incidents' => $this->incidents()->count(),
            'active_incidents' => $this->incidents()->active()->count(),
            'resolved_incidents' => $this->incidents()->resolved()->count(),
        ];
    }

    public function isCritical(): bool
    {
        return $this->priority_level === 1;
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
