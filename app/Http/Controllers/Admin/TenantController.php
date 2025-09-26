<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q',''));
        $tenants = Tenant::query()
            ->when($q, fn($qry) => $qry->where(function($s) use ($q){
                $s->where('name','like',"%$q%")
                  ->orWhere('email','like',"%$q%")
                  ->orWhere('phone','like',"%$q%");
            }))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.tenants.index', compact('tenants','q'));
    }

    public function create()
    {
        return view('admin.tenants.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => ['required','string','max:100'],
            'email' => ['nullable','email','max:150','unique:tenants,email'],
            'phone' => ['nullable','string','max:30'],
        ]);

        Tenant::create($data);

        return redirect()->route('admin.tenants.index')
            ->with('ok','Tenant created');
    }

    public function edit(Tenant $tenant)
    {
        return view('admin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'name'  => ['required','string','max:100'],
            'email' => ['nullable','email','max:150', Rule::unique('tenants','email')->ignore($tenant->id)],
            'phone' => ['nullable','string','max:30'],
        ]);

        $tenant->update($data);

        return redirect()->route('admin.tenants.index')->with('ok','Tenant updated');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return redirect()->route('admin.tenants.index')->with('ok','Tenant deleted');
    }
}