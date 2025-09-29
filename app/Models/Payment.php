<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model {
  protected $fillable = ['bill_id','amount','paid_at','method','reference','meta'];
  protected $casts = ['paid_at'=>'datetime','meta'=>'array'];
  public function bill()
  {
    return $this->belongsTo(Bill::class);
  }

}