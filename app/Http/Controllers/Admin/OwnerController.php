<?php
// app/Http/Controllers/Admin/OwnerController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOwnerRequest;
use App\Http\Requests\Admin\UpdateOwnerRequest;
use App\Models\User;
use App\Services\Admin\OwnerService;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    public function __construct(private OwnerService $ownerService) {}

    public function index(Request $request)
    {
        $filters = [
            'search' => trim($request->get('q', '')),
        ];

        $owners = $this->ownerService->getOwnersWithFilters($filters);

        return view('admin.owners.index', [
            'owners' => $owners,
            'search' => $filters['search'],
        ]);
    }

    public function create()
    {
        return view('admin.owners.create');
    }

    public function store(StoreOwnerRequest $request)
    {
        try {
            $data = $request->validated();
            $owner = $this->ownerService->createOwner($data);

            return redirect()->route('admin.owners.index')
                ->with('ok', 'Owner created successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create owner: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(User $owner)
    {
        abort_unless($owner->role === 'owner', 404);
        return view('admin.owners.edit', compact('owner'));
    }

    public function update(UpdateOwnerRequest $request, User $owner)
    {
        try {
            $data = $request->validated();
            $this->ownerService->updateOwner($owner, $data);

            return redirect()->route('admin.owners.index')
                ->with('ok', 'Owner updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update owner: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(User $owner)
    {
        abort_unless($owner->role === 'owner', 404);

        try {
            $canDelete = $this->ownerService->canDeleteOwner($owner);

            if (!$canDelete['can_delete']) {
                return back()->with('error', 'Cannot delete owner: ' . implode(', ', $canDelete['reasons']));
            }

            $this->ownerService->deleteOwner($owner);
            return redirect()->route('admin.owners.index')
                ->with('ok', 'Owner deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete owner: ' . $e->getMessage());
        }
    }
}
