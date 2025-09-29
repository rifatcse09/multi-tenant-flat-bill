<?php

namespace App\Models;

use App\Scopes\OwnerScope;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'owner_id','flat_id','bill_category_id','tenant_id',
        'month','amount','status','notes','due_carry_forward'
    ];

    protected $casts = [
        'month' => 'date:Y-m-d',
        'amount' => 'decimal:2',
        'due_carry_forward' => 'decimal:2',
    ];

    protected static function booted() {
        static::addGlobalScope(new OwnerScope);
    }

    public function owner() {
        return $this->belongsTo(User::class,'owner_id');
    }
    public function flat() {
        return $this->belongsTo(Flat::class);
    }
    public function category() {
        return $this->belongsTo(BillCategory::class,'bill_category_id');
    }
    public function tenant() {
        return $this->belongsTo(Tenant::class);
    }
    public function payments() {
        return $this->hasMany(Payment::class);
    }

    public function getTotalDueAttribute() {
        $paid = $this->payments()->sum('amount');
        return max(0, ($this->amount + $this->due_carry_forward) - $paid);
    }

    public function adjustments()
    {
        return $this->hasMany(BillAdjustment::class);
    }

    public function getPaidAttribute(): float
    {
        return (float)$this->payments()->sum('amount');
    }

    public function getAdjustmentsTotalAttribute(): float
    {
        return (float)$this->adjustments()->sum('amount');
    }

    public function getGrossAttribute(): float
    {
        return (float)$this->amount + (float)$this->due_carry_forward + (float)$this->adjustments_total;
    }

    public function getDueAttribute(): float
    {
        return max(0.0, $this->gross - $this->paid);
    }
}