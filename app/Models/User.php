<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'slug',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is an owner.
     */
    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    /**
     * Get all buildings owned by this user.
     */
    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class, 'owner_id');
    }

    /**
     * Get all bill categories created by this user.
     */
    public function billCategories(): HasMany
    {
        return $this->hasMany(BillCategory::class, 'owner_id');
    }

    /**
     * Get all bills created by this user.
     */
    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class, 'owner_id');
    }

    /**
     * Get all payments recorded by this user.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'owner_id');
    }
}