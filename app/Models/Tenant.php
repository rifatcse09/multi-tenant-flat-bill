<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = ['name','email','phone'];

    public function flats() {
        return $this->belongsToMany(Flat::class, 'flat_tenant')
                    ->withTimestamps()
                    ->withPivot(['start_date','end_date']);
    }
    public function bills() { return $this->hasMany(Bill::class); }
}