<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Tenant extends Model
{
    use SoftDeletes, Notifiable;

    protected $fillable = ['name', 'email', 'phone'];

    /**
     * Get the buildings this tenant is associated with.
     */
    public function buildings(): BelongsToMany
    {
        return $this->belongsToMany(Building::class, 'building_tenant')
            ->withTimestamps()
            ->withPivot(['start_date', 'end_date']);
    }

    /**
     * Get the flats this tenant is assigned to.
     */
    public function flats(): BelongsToMany
    {
        return $this->belongsToMany(Flat::class, 'flat_tenant')
            ->withTimestamps()
            ->withPivot(['start_date', 'end_date']);
    }

    /**
     * Get all bills for this tenant.
     */
    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    /**
     * Get all payments for this tenant.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'bill_id');
    }

    /**
     * Route notifications for the tenant.
     * This tells Laravel to use the email field for email notifications.
     */
    public function routeNotificationForMail($notification)
    {
        return $this->email;
    }

    /**
     * Get the tenant's display name for notifications.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name;
    }
}