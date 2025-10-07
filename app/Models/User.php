<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCodeMail;
use App\Notifications\EmailVerificationNotification;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'municipality',
        'phone_number',
        'address',
        'is_active',
        'email_verified_at',
        'two_factor_code',
        'two_factor_expires_at',
        'failed_login_attempts',
        'locked_until',
        'last_login',
        'email_verification_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_code',
        'email_verification_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'two_factor_expires_at' => 'datetime',
            'locked_until' => 'datetime',
            'last_login' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'failed_login_attempts' => 'integer',
        ];
    }

    // Activity logging
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'email', 'role', 'municipality', 'is_active'])
            ->logOnlyDirty();
    }

    // Relationships
    public function reportedIncidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'reported_by');
    }

    public function assignedIncidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'assigned_staff_id');
    }

    public function assignedVehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'assigned_driver_id');
    }

    public function approvedRequests(): HasMany
    {
        return $this->hasMany(Request::class, 'approved_by');
    }

    public function assignedRequests(): HasMany
    {
        return $this->hasMany(Request::class, 'assigned_staff_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeByMunicipality($query, $municipality)
    {
        return $query->where('municipality', $municipality);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getRoleBadgeAttribute()
    {
        return match ($this->role) {
            'admin' => 'badge-error',
            'staff' => 'badge-primary',
            'responder' => 'badge-warning',
            'citizen' => 'badge-neutral',
            default => 'badge-ghost'
        };
    }

    // Methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function isResponder()
    {
        return $this->role === 'responder';
    }

    public function isCitizen()
    {
        return $this->role === 'citizen';
    }

    public function canAccessMunicipality($municipality)
    {
        return $this->isAdmin() || $this->municipality === $municipality;
    }

    public function updateLastLogin()
    {
        $this->update(['last_login' => now()]);
    }

    // =======================
    // SECURITY METHODS
    // =======================

    /**
     * Check if account is temporarily locked due to failed login attempts
     */
    public function isAccountLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Increment failed login attempts and lock account if necessary
     */
    public function incrementFailedLogins(): void
    {
        $this->increment('failed_login_attempts');

        // Lock account after 5 failed attempts for 15 minutes
        if ($this->failed_login_attempts >= 5) {
            $this->update([
                'locked_until' => now()->addMinutes(15)
            ]);

            // Log security event
            activity()
                ->performedOn($this)
                ->withProperties([
                    'failed_attempts' => $this->failed_login_attempts,
                    'locked_until' => $this->locked_until,
                    'ip_address' => request()->ip()
                ])
                ->log('Account locked due to multiple failed login attempts');
        }
    }

    /**
     * Reset failed login attempts and update last login
     */
    public function resetFailedLogins(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_login' => now()
        ]);
    }

    // =======================
    // TWO-FACTOR AUTHENTICATION
    // =======================

    /**
     * Generate a 6-digit OTP code with 5-minute expiration
     */
    public function generateTwoFactorCode(): string
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->update([
            'two_factor_code' => $code,
            'two_factor_expires_at' => now()->addMinutes(5)
        ]);

        // Send 2FA code via email
        try {
            Mail::to($this->email)->send(new TwoFactorCodeMail($code, $this));

            // Optional debug logging (only in debug mode)
            if (config('app.debug')) {
                \Illuminate\Support\Facades\Log::info('2FA code generated', [
                    'user_id' => $this->id,
                    'generated_code' => $code,
                    'stored_code' => $this->two_factor_code,
                    'expires_at' => (string) $this->two_factor_expires_at,
                ]);
            }

            // Log 2FA code generation
            activity()
                ->performedOn($this)
                ->withProperties([
                    'code_expires_at' => $this->two_factor_expires_at,
                    'ip_address' => request()->ip()
                ])
                ->log('Two-factor authentication code generated');

        } catch (\Exception $e) {
            // Log email failure but don't expose error to user
            activity()
                ->performedOn($this)
                ->withProperties([
                    'error' => $e->getMessage(),
                    'ip_address' => request()->ip()
                ])
                ->log('Failed to send 2FA code email');
        }

        return $code;
    }

    /**
     * Validate 2FA code and check expiration
     */
    public function isTwoFactorCodeValid(string $code): bool
    {
        // Normalize input: keep digits only and ensure length
        $inputCode = preg_replace('/[^0-9]/', '', trim($code));

        if (config('app.debug')) {
            \Illuminate\Support\Facades\Log::info('2FA validation attempt', [
                'user_id' => $this->id,
                'submitted_code' => $inputCode,
                'stored_code' => $this->two_factor_code,
                'expires_at' => (string) $this->two_factor_expires_at,
                'now' => (string) now(),
            ]);
        }

        if (!$this->two_factor_code || !$this->two_factor_expires_at) {
            \Illuminate\Support\Facades\Log::warning('2FA code validation failed: No code or expiration set');
            return false;
        }

        // Check if code has expired
        if ($this->two_factor_expires_at->isPast()) {
            \Illuminate\Support\Facades\Log::warning('2FA code validation failed: Code expired');
            $this->clearTwoFactorCode();
            return false;
        }

        // Constant-time comparison to prevent timing attacks and preserve leading zeros
        $isValid = hash_equals((string) $this->two_factor_code, (string) $inputCode);

        if (config('app.debug')) {
            \Illuminate\Support\Facades\Log::info('2FA code validation result: ' . ($isValid ? 'Valid' : 'Invalid'));
        }

        return $isValid;
    }

    /**
     * Clear 2FA code after successful verification or expiration
     */
    public function clearTwoFactorCode(): void
    {
        $this->update([
            'two_factor_code' => null,
            'two_factor_expires_at' => null
        ]);
    }

    // =======================
    // EMAIL VERIFICATION
    // =======================

    /**
     * Send email verification notification
     */
    public function sendEmailVerificationNotification(): void
    {
        $token = bin2hex(random_bytes(32));

        $this->update([
            'email_verification_token' => $token
        ]);

        $this->notify(new EmailVerificationNotification($token));

        // Log verification email sent
        activity()
            ->performedOn($this)
            ->withProperties(['ip_address' => request()->ip()])
            ->log('Email verification notification sent');
    }

    /**
     * Check if user has verified their email
     */
    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Mark email as verified
     */
    public function markEmailAsVerified(): void
    {
        $this->update([
            'email_verified_at' => now(),
            'email_verification_token' => null
        ]);

        // Log email verification
        activity()
            ->performedOn($this)
            ->withProperties(['ip_address' => request()->ip()])
            ->log('Email address verified');
    }
}
