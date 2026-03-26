<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OwnerSubscription extends Model
{
    public const STATUS_TRIALING = 'trialing';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_PAST_DUE = 'past_due';

    public const STATUS_CANCELED = 'canceled';

    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'status',
        'trial_ends_at',
        'current_period_start',
        'current_period_end',
        'canceled_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'current_period_start' => 'datetime',
            'current_period_end' => 'datetime',
            'canceled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    /**
     * Whether the owner may use the application (trial or paid period).
     */
    public function grantsAccess(): bool
    {
        if ($this->status === self::STATUS_CANCELED) {
            return false;
        }

        if ($this->status === self::STATUS_ACTIVE
            && $this->current_period_end
            && now()->lte($this->current_period_end)) {
            return true;
        }

        if ($this->status === self::STATUS_TRIALING
            && $this->trial_ends_at
            && now()->lte($this->trial_ends_at)) {
            return true;
        }

        return false;
    }

    public function isTrialing(): bool
    {
        return $this->status === self::STATUS_TRIALING
            && $this->trial_ends_at
            && now()->lte($this->trial_ends_at);
    }

    public function trialDaysRemaining(): ?int
    {
        if (! $this->isTrialing() || ! $this->trial_ends_at) {
            return null;
        }

        return max(0, (int) now()->diffInDays($this->trial_ends_at, false));
    }
}
