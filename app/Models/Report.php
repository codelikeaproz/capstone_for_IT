<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_title',
        'report_content',
        'report_type',
        'generated_by',
        'report_date',
    ];

    protected $casts = [
        'report_date' => 'datetime',
    ];

    // Relationships
    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function incidents(): BelongsToMany
    {
        return $this->belongsToMany(Incident::class, 'incident_report', 'report_id', 'incident_id')
            ->withTimestamps();
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('report_type', $type);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('report_date', '>=', now()->subDays($days));
    }

    // Accessors
    public function getFormattedReportDateAttribute()
    {
        return $this->report_date ? $this->report_date->format('M d, Y H:i') : null;
    }

    public function getIncidentCountAttribute()
    {
        return $this->incidents()->count();
    }
}
