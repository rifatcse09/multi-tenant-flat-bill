<?php

namespace App\Models;

use App\Models\Tenant;
use App\Scopes\OwnerScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Building extends Model
{

    use SoftDeletes;

    protected $fillable = ['owner_id','name','address'];

    protected static function booted()
    {
        static::addGlobalScope(new OwnerScope);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function tenants()
    {
        // Admin-level approval list (who can live in this building)
        return $this->belongsToMany(Tenant::class, 'building_tenant')
            ->withTimestamps()
            ->withPivot(['start_date','end_date']);
    }

    public function flats() {
        return $this->hasMany(Flat::class);
    }
}