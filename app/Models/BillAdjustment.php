<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BillAdjustment extends Model {
  protected $fillable = ['bill_id','amount','reason','type'];
  public function bill()
  {
    return $this->belongsTo(Bill::class);
  }
}