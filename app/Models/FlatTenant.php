<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlatTenant extends Model
{

    protected $table = 'flat_tenant';
    protected $fillable = ['flat_id','tenant_id','start_date','end_date'];
    protected $dates = ['start_date','end_date'];

    public function tenants()
    {
        return $this->belongsToMany(Tenant::class, 'flat_tenant')
            ->using(FlatTenant::class)
            ->withPivot(['start_date','end_date'])
            ->withTimestamps();
    }

}