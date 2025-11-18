<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedback';

    protected $fillable = [
        'request_id',
        'feedback',
        'rating',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'rating' => 'integer',
    ];

    // Relationships
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class, 'request_id');
    }

    // Scopes
    public function scopePositive($query)
    {
        return $query->where('rating', '>=', 4);
    }

    public function scopeNegative($query)
    {
        return $query->where('rating', '<=', 2);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('submitted_at', '>=', now()->subDays($days));
    }

    // Accessors
    public function getRatingBadgeAttribute()
    {
        if (!$this->rating) return 'badge-neutral';

        return match(true) {
            $this->rating >= 4 => 'badge-success',
            $this->rating === 3 => 'badge-warning',
            $this->rating <= 2 => 'badge-error',
            default => 'badge-neutral'
        };
    }

    public function getFormattedSubmittedAtAttribute()
    {
        return $this->submitted_at ? $this->submitted_at->format('M d, Y H:i') : 'Not submitted';
    }

    public function getRatingStarsAttribute()
    {
        return $this->rating ? str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating) : 'No rating';
    }
}
