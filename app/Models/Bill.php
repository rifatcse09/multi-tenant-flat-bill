<?php

namespace App\Models;

use App\Scopes\OwnerScope;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'owner_id','flat_id','bill_category_id','tenant_id','bill_to',
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
}