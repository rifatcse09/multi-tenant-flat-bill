<?php

// app/Http/Requests/Owner/StoreUpdateBillCategoryRequest.php
namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateBillCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'owner';
    }

    public function rules(): array
    {
        $ownerId = auth()->id();
        $categoryId = $this->route('category')?->id; // on update

        return [
            'name' => [
                'required','string','max:80',
                Rule::unique('bill_categories','name')
                    ->ignore($categoryId)
                    ->where(fn($q) => $q->where('owner_id', $ownerId)),
            ],
        ];
    }

    public function messages(): array
    {
        return ['name.unique' => 'You already have a category with this name.'];
    }
}