<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTenantRequest;
use App\Http\Requests\Admin\UpdateTenantRequest;
use App\Models\Tenant;
use App\Services\Admin\TenantService;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function __construct(private TenantService $tenantService) {}

    public function index(Request $request)
    {
        $filters = [
            'search' => trim($request->get('q', '')),
        ];

        $tenants = $this->tenantService->getTenantsWithFilters($filters);
        $stats = $this->tenantService->getTenantStats();

        return view('admin.tenants.index', [
            'tenants' => $tenants,
            'search' => $filters['search'],
            'stats' => $stats,
        ]);
    }

    public function create()
    {
        return view('admin.tenants.create');
    }

    public function store(StoreTenantRequest $request)
    {
        try {
            $data = $request->validated();
            $this->tenantService->createTenant($data);

            return redirect()->route('admin.tenants.index')
                ->with('ok', 'Tenant created successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create tenant: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Tenant $tenant)
    {
        $tenant = $this->tenantService->getTenantWithDetails($tenant->id);
        $assignmentHistory = $this->tenantService->getTenantAssignmentHistory($tenant->id);

        return view('admin.tenants.show', compact('tenant', 'assignmentHistory'));
    }

    public function edit(Tenant $tenant)
    {
        return view('admin.tenants.edit', compact('tenant'));
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant)
    {
        try {
            $data = $request->validated();
            $this->tenantService->updateTenant($tenant, $data);

            return redirect()->route('admin.tenants.index')
                ->with('ok', 'Tenant updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update tenant: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Tenant $tenant)
    {
        try {
            $canDelete = $this->tenantService->canDeleteTenant($tenant);

            if (!$canDelete['can_delete']) {
                return back()->with('error', 'Cannot delete tenant: ' . implode(', ', $canDelete['reasons']));
            }

            $this->tenantService->deleteTenant($tenant);
            return redirect()->route('admin.tenants.index')
                ->with('ok', 'Tenant deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete tenant: ' . $e->getMessage());
        }
    }

    /**
     * Search tenants for AJAX requests (for assignment forms).
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');
        $tenants = $this->tenantService->searchTenantsForAssignment($search);

        return response()->json($tenants);
    }

    /**
     * Export tenants to CSV.
     */
    public function export(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
        ];

        $tenants = $this->tenantService->exportTenants($filters);

        $filename = 'tenants-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($tenants) {
            $file = fopen('php://output', 'w');

            // Header row - removed payment columns
            fputcsv($file, [
                'ID', 'Name', 'Email', 'Phone', 'Bills Count',
                'Created', 'Updated'
            ]);

            foreach ($tenants as $tenant) {
                fputcsv($file, [
                    $tenant->id,
                    $tenant->name,
                    $tenant->email ?? 'N/A',
                    $tenant->phone ?? 'N/A',
                    $tenant->bills_count ?? 0,
                    $tenant->created_at->format('Y-m-d H:i:s'),
                    $tenant->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}