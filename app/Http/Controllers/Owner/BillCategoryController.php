<?php


// app/Http/Controllers/Owner/BillCategoryController.php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreUpdateBillCategoryRequest as CategoryRequest;
use App\Models\BillCategory;
use App\Services\Owner\BillCategoryService;
use Illuminate\Http\Request;

class BillCategoryController extends Controller
{
    public function __construct(private BillCategoryService $service) {}

    public function index(Request $request)
    {
        $q = trim($request->get('q',''));
        $categories = $this->service->paginateForOwner(auth()->id(), $q);
        return view('owner.categories.index', compact('categories','q'));
    }

    public function create()
    {
        return view('owner.categories.create');
    }

    public function store(CategoryRequest $request)
    {
        $this->service->createForOwner(auth()->id(), $request->validated());
        return redirect()->route('owner.categories.index')->with('ok','Category created');
    }

    public function edit(BillCategory $category)
    {
        // OwnerScope ensures only their own category is resolved
        return view('owner.categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, BillCategory $category)
    {
        $this->service->update($category, $request->validated());
        return redirect()->route('owner.categories.index')->with('ok','Category updated');
    }

    public function destroy(BillCategory $category)
    {
        $this->service->delete($category);
        return redirect()->route('owner.categories.index')->with('ok','Category deleted');
    }
}