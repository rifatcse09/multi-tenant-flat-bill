<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'bill_id',
        'amount',
        'paid_at',
        'method', // Use 'method' instead of 'payment_method'
    ];

    protected $casts = [
        'paid_at' => 'date',
        'amount' => 'decimal:2',
    ];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    /**
     * Scope payments for a specific date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('paid_at', [$startDate, $endDate]);
    }

    /**
     * Scope payments by owner through bill relationship.
     */
    public function scopeForOwner($query, $ownerId)
    {
        return $query->whereHas('bill', function ($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        });
    }

    /**
     * Get the payment method for display.
     */
    public function getPaymentMethodAttribute()
    {
        return ucfirst($this->method ?? 'cash');
    }
}