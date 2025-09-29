<?php

namespace App\Models;

use App\Scopes\OwnerScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'owner_id',
        'tenant_id',
        'bill_id',
        'amount',
        'paid_at',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'date',
        'amount' => 'decimal:2',
    ];

    protected static function booted() {
        static::addGlobalScope(new OwnerScope);
    }

    public function bill()
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
}