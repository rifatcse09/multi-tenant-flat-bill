<?php

namespace App\Models;

use App\Scopes\OwnerScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'owner_id',
        'flat_id',
        'bill_category_id',
        'tenant_id',
        'month',
        'amount',
        'due_carry_forward',
        'status',
        'notes',
    ];

    protected $casts = [
        'month' => 'date',
        'amount' => 'decimal:2',
        'due_carry_forward' => 'decimal:2',
    ];

    protected static function booted() {
        static::addGlobalScope(new OwnerScope);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function flat(): BelongsTo
    {
        return $this->belongsTo(Flat::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BillCategory::class, 'bill_category_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope bills for a specific month.
     */
    public function scopeForMonth($query, $month)
    {
        return $query->whereMonth('month', $month);
    }

    /**
     * Scope bills by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get total amount due (including carry forward).
     */
    public function getTotalDueAttribute(): float
    {
        return $this->amount + $this->due_carry_forward;
    }

    /**
     * Get total paid amount.
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->payments()->sum('amount');
    }

    /**
     * Get remaining amount.
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->total_due - $this->total_paid);
    }
}