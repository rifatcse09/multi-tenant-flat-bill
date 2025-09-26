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
    protected OwnerService $ownerService;

    public function __construct(OwnerService $ownerService)
    {
        $this->ownerService = $ownerService;
    }

    /**
     * Display a listing of owners with pagination and search.
     */
    public function index(Request $request)
    {
        $search = trim($request->get('q', ''));
        $owners = $this->ownerService->paginateOwners($search);
        $stats = $this->ownerService->getAdminOwnerStats();

        return view('admin.owners.index', compact('owners', 'search', 'stats'));
    }

    /**
     * Show the form for creating a new owner.
     */
    public function create()
    {
        return view('admin.owners.create');
    }

    /**
     * Store a newly created owner.
     */
    public function store(StoreOwnerRequest $request)
    {
        $owner = $this->ownerService->createOwner($request->validated());

        return redirect()
            ->route('admin.owners.index')
            ->with('ok', "Owner '{$owner->name}' created successfully");
    }

    /**
     * Display the specified owner.
     */
    public function show(User $owner)
    {
        abort_unless($owner->role === 'owner', 404);

        $stats = $this->ownerService->getOwnerStats($owner);
        $buildings = $owner->buildings()->withCount(['flats', 'tenants'])->get();

        return view('admin.owners.show', compact('owner', 'stats', 'buildings'));
    }

    /**
     * Show the form for editing the owner.
     */
    public function edit(User $owner)
    {
        abort_unless($owner->role === 'owner', 404);
        return view('admin.owners.edit', compact('owner'));
    }

    /**
     * Update the specified owner.
     */
    public function update(UpdateOwnerRequest $request, User $owner)
    {
        abort_unless($owner->role === 'owner', 404);

        $this->ownerService->updateOwner($owner, $request->validated());

        return redirect()
            ->route('admin.owners.index')
            ->with('ok', 'Owner updated successfully');
    }

    /**
     * Remove the specified owner.
     */
    public function destroy(User $owner)
    {
        abort_unless($owner->role === 'owner', 404);

        try {
            $this->ownerService->deleteOwner($owner);
            return redirect()
                ->route('admin.owners.index')
                ->with('ok', 'Owner deleted successfully');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show owners with building information.
     */
    public function withBuildings()
    {
        $owners = $this->ownerService->getOwnersWithBuildingCounts();
        return view('admin.owners.with-buildings', compact('owners'));
    }

    /**
     * Search owners.
     */
    public function search(Request $request)
    {
        $query = trim($request->get('q', ''));

        if ($query) {
            $owners = $this->ownerService->searchOwners($query);
            return view('admin.owners.search', compact('owners', 'query'));
        }

        return redirect()->route('admin.owners.index');
    }
}