<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\OwnerScope;

class BillCategory extends Model
{
    protected $fillable = ['owner_id','name'];

    protected static function booted() {
        static::addGlobalScope(new OwnerScope);
    }

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }
    public function bills() {
        return $this->hasMany(Bill::class, 'bill_category_id');
    }
}