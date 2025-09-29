<?php

namespace App\Models;

use App\Models\Building;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use SoftDeletes;

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

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    // Removed payments() relationship - no longer needed
}