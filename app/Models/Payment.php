<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['bill_id','amount','paid_at','method','ref'];
    protected $casts = [
        'paid_at' => 'datetime'
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

}