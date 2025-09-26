<?php
// app/Http/Controllers/Admin/OwnerController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class OwnerController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $owners = User::query()
            ->where('role', 'owner')
            ->when($q, fn($qry) => $qry->where(function($s) use ($q){
                $s->where('name','like',"%$q%")
                  ->orWhere('email','like',"%$q%")
                  ->orWhere('slug','like',"%$q%");
            }))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.owners.index', compact('owners','q'));
    }

    public function create()
    {
        return view('admin.owners.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => ['required','string','max:100'],
            'email' => ['required','email','max:150','unique:users,email'],
            'slug'  => ['nullable','alpha_dash','max:80','unique:users,slug'],
            'password' => ['nullable','string','min:6'], // optional; default if empty
        ]);

        $owner = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'slug'     => $data['slug'] ?? null,
            'role'     => 'owner',
            'password' => Hash::make($data['password'] ?? 'password'),
        ]);

        return redirect()->route('admin.owners.index')
            ->with('ok', "Owner '{$owner->name}' created");
    }

    public function edit(User $owner)
    {
        abort_unless($owner->role === 'owner', 404);
        return view('admin.owners.edit', compact('owner'));
    }

    public function update(Request $request, User $owner)
    {
        abort_unless($owner->role === 'owner', 404);

        $data = $request->validate([
            'name'  => ['required','string','max:100'],
            'email' => ['required','email','max:150', Rule::unique('users','email')->ignore($owner->id)],
            'slug'  => ['nullable','alpha_dash','max:80', Rule::unique('users','slug')->ignore($owner->id)],
            'password' => ['nullable','string','min:6'],
        ]);

        $owner->update([
            'name'  => $data['name'],
            'email' => $data['email'],
            'slug'  => $data['slug'] ?? null,
            'password' => isset($data['password']) ? Hash::make($data['password']) : $owner->password,
        ]);

        return redirect()->route('admin.owners.index')->with('ok', 'Owner updated');
    }

    public function destroy(User $owner)
    {
        abort_unless($owner->role === 'owner', 404);
        $owner->delete();
        return redirect()->route('admin.owners.index')->with('ok', 'Owner deleted');
    }
}