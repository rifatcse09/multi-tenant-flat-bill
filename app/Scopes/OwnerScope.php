<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class OwnerScope implements Scope
{
    public function apply(Builder $query, Model $model)
    {
        $user = auth()->user();
        if ($user && $user->role === 'owner') {
            $query->where($model->getTable().'.owner_id', $user->id);
        }
    }
}