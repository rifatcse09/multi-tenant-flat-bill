<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_monthly_cents',
        'price_total_cents',
        'currency',
        'trial_days',
        'billing_period_months',
        'is_free',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_free' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function ownerSubscriptions(): HasMany
    {
        return $this->hasMany(OwnerSubscription::class);
    }

    /**
     * Default “Starter” plan: first active free tier (by sort_order).
     */
    public static function defaultStarter(): ?self
    {
        return static::query()
            ->where('is_active', true)
            ->where('is_free', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();
    }

    public function priceMonthlyFormatted(): string
    {
        return number_format($this->price_monthly_cents / 100, 2).' '.$this->currency;
    }

    public function money(int $cents): string
    {
        $sym = $this->currency === 'USD' ? '$' : $this->currency.' ';

        return $sym.number_format($cents / 100, 2);
    }

    /**
     * Main price line for cards (free / per period / per month).
     */
    public function displayPricePrimary(): string
    {
        if ($this->is_free) {
            return 'Free';
        }

        if ($this->price_total_cents && $this->billing_period_months) {
            return $this->money($this->price_total_cents);
        }

        return $this->money($this->price_monthly_cents);
    }

    public function displayPriceSecondary(): ?string
    {
        if ($this->is_free) {
            return (string) $this->trial_days.' days full access';

        }

        if ($this->billing_period_months === 3) {
            return 'Billed every 3 months';

        }

        if ($this->billing_period_months === 12) {
            return 'Billed once per year';
        }

        return '/ month';
    }

    public function displayPeriodBadge(): string
    {
        if ($this->is_free) {
            return '15-day trial';
        }

        if ($this->billing_period_months === 3) {
            return 'Every 3 months';

        }

        if ($this->billing_period_months === 12) {
            return '1 year';

        }

        return 'Monthly';
    }
}
