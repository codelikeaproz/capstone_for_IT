<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LoginAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'ip_address',
        'successful',
        'user_agent',
        'failure_reason',
        'attempted_at',
    ];

    protected $casts = [
        'successful' => 'boolean',
        'attempted_at' => 'datetime',
    ];

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('successful', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('successful', false);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('attempted_at', '>=', now()->subHours($hours));
    }

    public function scopeByIp($query, $ip)
    {
        return $query->where('ip_address', $ip);
    }

    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    // Static methods for security analysis
    public static function getRecentFailedAttempts($email = null, $ip = null, $hours = 1)
    {
        $query = self::failed()->recent($hours);
        
        if ($email) {
            $query->byEmail($email);
        }
        
        if ($ip) {
            $query->byIp($ip);
        }
        
        return $query->count();
    }

    public static function getSuccessRate($hours = 24)
    {
        $total = self::recent($hours)->count();
        $successful = self::successful()->recent($hours)->count();
        
        return $total > 0 ? round(($successful / $total) * 100, 2) : 0;
    }

    public static function getTopFailureReasons($hours = 24)
    {
        return self::failed()
            ->recent($hours)
            ->whereNotNull('failure_reason')
            ->groupBy('failure_reason')
            ->selectRaw('failure_reason, count(*) as count')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
    }

    // Accessors
    public function getFormattedAttemptedAtAttribute()
    {
        return $this->attempted_at->format('M d, Y H:i:s');
    }

    public function getStatusBadgeAttribute()
    {
        return $this->successful ? 'badge-success' : 'badge-error';
    }

    public function getStatusTextAttribute()
    {
        return $this->successful ? 'Success' : 'Failed';
    }
}