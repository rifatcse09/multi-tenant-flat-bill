<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\OwnerScope;

class Flat extends Model
{
    protected $fillable = ['building_id','owner_id','flat_number','flat_owner_name','flat_owner_phone'];

    protected static function booted() {
        static::addGlobalScope(new OwnerScope);
    }

    public function building() { return $this->belongsTo(Building::class); }
    public function bills() { return $this->hasMany(Bill::class); }
    public function tenants() {
        return $this->belongsToMany(Tenant::class, 'flat_tenant')
                    ->withTimestamps()
                    ->withPivot(['start_date','end_date']);
    }

    public function currentTenant(?string $asOfDate = null): ?Tenant {
        $asOf = $asOfDate ? date('Y-m-d', strtotime($asOfDate)) : date('Y-m-d');
        return $this->tenants()
            ->where(function($q) use ($asOf) {
                $q->whereNull('flat_tenant.end_date')->orWhere('flat_tenant.end_date','>=',$asOf);
            })
            ->where(function($q) use ($asOf) {
                $q->whereNull('flat_tenant.start_date')->orWhere('flat_tenant.start_date','<=',$asOf);
            })
            ->latest('flat_tenant.start_date')
            ->first();
    }
}