<?php

namespace App\Models;

use App\Models\Building;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = ['name','email','phone'];

    public function buildings()
    {
        return $this->belongsToMany(Building::class, 'building_tenant')
            ->withTimestamps()
            ->withPivot(['start_date','end_date']);
    }

    public function flats()
    {
        return $this->belongsToMany(Flat::class, 'flat_tenant')
                    ->withTimestamps()
                    ->withPivot(['start_date','end_date']);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
}