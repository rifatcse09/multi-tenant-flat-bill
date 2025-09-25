<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\OwnerScope;

class Building extends Model
{
    protected $fillable = ['owner_id','name','address'];

    protected static function booted() {
        static::addGlobalScope(new OwnerScope);
    }

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }
    public function flats() {
        return $this->hasMany(Flat::class);
    }
}