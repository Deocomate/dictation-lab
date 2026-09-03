<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'role',
        'email',
        'password',
        'provider',
        'provider_id',
        'subscription_tier',
        'subscription_expires_at',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
            'subscription_expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // -------------------------------------------------------------------------
    // Role Helper Methods
    // -------------------------------------------------------------------------

    /**
     * Check if user is a superadmin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    /**
     * Check if user is an admin (any admin role).
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['superadmin', 'admin']);
    }

    /**
     * Check if user has exactly the admin role (not superadmin).
     */
    public function isRegularAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user has an active Pro subscription.
     */
    public function isPro(): bool
    {
        return $this->subscription_tier === 'pro'
            && $this->subscription_expires_at
            && $this->subscription_expires_at->isFuture();
    }

    // -------------------------------------------------------------------------
    // Query Scopes
    // -------------------------------------------------------------------------

    /**
     * Scope to get only admin users (excludes superadmin).
     */
    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope to get all admin-type users (admin + superadmin).
     */
    public function scopeAllAdmins(Builder $query): Builder
    {
        return $query->whereIn('role', ['superadmin', 'admin']);
    }

    /**
     * Scope to get only superadmin users.
     */
    public function scopeSuperAdmins(Builder $query): Builder
    {
        return $query->where('role', 'superadmin');
    }

    /**
     * Scope to get client users only.
     */
    public function scopeClients(Builder $query): Builder
    {
        return $query->where('role', 'user');
    }

    /**
     * User's dictation history records.
     */
    public function dictationHistories(): HasMany
    {
        return $this->hasMany(DictationHistory::class);
    }

    /**
     * User's payment transactions.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * User's saved vocabulary entries.
     */
    public function userVocabularies(): HasMany
    {
        return $this->hasMany(UserVocabulary::class);
    }
}
