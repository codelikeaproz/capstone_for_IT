<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $table = 'account_roles';

    protected $fillable = [
        'role_name',
        'role_description',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereHas('users', function($q) {
            $q->where('is_active', true);
        });
    }

    // Accessors
    public function getUserCountAttribute()
    {
        return $this->users()->count();
    }
}
