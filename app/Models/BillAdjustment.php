<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillAdjustment extends Model
{
    protected $fillable = [
        'bill_id',
        'type',
        'amount',
        'reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the bill that this adjustment belongs to.
     */
    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    /**
     * Scope adjustments by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get adjustments that increase the bill amount.
     */
    public function scopeIncreases($query)
    {
        return $query->where('type', 'increase');
    }

    /**
     * Get adjustments that decrease the bill amount.
     */
    public function scopeDecreases($query)
    {
        return $query->where('type', 'decrease');
    }

    /**
     * Get the formatted type for display.
     */
    public function getFormattedTypeAttribute()
    {
        return ucfirst($this->type);
    }

    /**
     * Get the signed amount (positive for increases, negative for decreases).
     */
    public function getSignedAmountAttribute()
    {
        return $this->type === 'increase' ? $this->amount : -$this->amount;
    }
}