<?php

namespace App\Services\Owner;

use App\Models\Building;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BuildingTenantService
{
    /**
     * Get tenants for a building with their occupancy information.
     */
    public function getTenantsWithOccupancy(Building $building): LengthAwarePaginator
    {
        return $building->tenants()
            ->with(['flats' => function ($query) use ($building) {
                $query->where('building_id', $building->id)
                      ->withPivot('start_date', 'end_date', 'id');
            }])
            ->paginate(15);
    }

    /**
     * Get building tenant statistics.
     */
    public function getBuildingTenantStats(Building $building): array
    {
        $totalTenants = $building->tenants()->count();

        // Active tenants (currently occupying flats)
        $activeTenants = DB::table('flat_tenant as ft')
            ->join('flats as f', 'f.id', '=', 'ft.flat_id')
            ->where('f.building_id', $building->id)
            ->whereNull('ft.end_date')
            ->distinct('ft.tenant_id')
            ->count('ft.tenant_id');

        // Total flats in building
        $totalFlats = $building->flats()->count();

        // Occupied flats
        $occupiedFlats = DB::table('flat_tenant as ft')
            ->join('flats as f', 'f.id', '=', 'ft.flat_id')
            ->where('f.building_id', $building->id)
            ->whereNull('ft.end_date')
            ->distinct('ft.flat_id')
            ->count('ft.flat_id');

        $occupancyRate = $totalFlats > 0 ? round(($occupiedFlats / $totalFlats) * 100, 1) : 0;

        return [
            'total_tenants' => $totalTenants,
            'active_tenants' => $activeTenants,
            'occupied_flats' => $occupiedFlats,
            'total_flats' => $totalFlats,
            'vacant_flats' => $totalFlats - $occupiedFlats,
            'occupancy_rate' => $occupancyRate,
        ];
    }

    /**
     * Get current occupancies for a building.
     */
    public function getCurrentOccupancies(Building $building): Collection
    {
        return collect(DB::table('flat_tenant as ft')
            ->join('flats as f', 'f.id', '=', 'ft.flat_id')
            ->join('tenants as t', 't.id', '=', 'ft.tenant_id')
            ->where('f.building_id', $building->id)
            ->whereNull('ft.end_date')
            ->select(
                'ft.*',
                'f.flat_number',
                't.name as tenant_name',
                't.email as tenant_email',
                't.phone as tenant_phone'
            )
            ->orderBy('f.flat_number')
            ->get());
    }

    /**
     * Get vacant flats in a building.
     */
    public function getVacantFlats(Building $building): Collection
    {
        return collect(DB::select("
            SELECT f.*
            FROM flats f
            WHERE f.building_id = ?
            AND f.id NOT IN (
                SELECT DISTINCT ft.flat_id
                FROM flat_tenant ft
                WHERE ft.end_date IS NULL
            )
            ORDER BY f.flat_number
        ", [$building->id]));
    }

    /**
     * Get occupied flats in a building.
     */
    public function getOccupiedFlats(Building $building): Collection
    {
        return collect(DB::table('flats as f')
            ->join('flat_tenant as ft', 'f.id', '=', 'ft.flat_id')
            ->join('tenants as t', 't.id', '=', 'ft.tenant_id')
            ->where('f.building_id', $building->id)
            ->whereNull('ft.end_date')
            ->select(
                'f.*',
                'ft.start_date',
                't.name as tenant_name',
                't.email as tenant_email'
            )
            ->orderBy('f.flat_number')
            ->get());
    }

    /**
     * Search tenants in a building.
     */
    public function searchTenants(Building $building, string $query): Collection
    {
        if (empty(trim($query))) {
            return collect();
        }

        return $building->tenants()
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('phone', 'LIKE', "%{$query}%");
            })
            ->with(['flats' => function ($q) use ($building) {
                $q->where('building_id', $building->id)
                  ->withPivot('start_date', 'end_date', 'id');
            }])
            ->get();
    }

    /**
     * Get unassigned tenants for a building.
     */
    public function getUnassignedTenants(Building $building): Collection
    {
        return $building->tenants()
            ->whereDoesntHave('flats', function ($query) use ($building) {
                $query->where('building_id', $building->id)
                      ->whereNull('flat_tenant.end_date');
            })
            ->get();
    }

    /**
     * Get occupancy timeline for a building.
     */
    public function getOccupancyTimeline(Building $building, int $days = 30): array
    {
        $timeline = [];
        $current = now()->subDays($days);

        while ($current->lte(now())) {
            $date = $current->toDateString();

            // Get occupancies active on this date
            $occupancies = DB::table('flat_tenant as ft')
                ->join('flats as f', 'f.id', '=', 'ft.flat_id')
                ->where('f.building_id', $building->id)
                ->where('ft.start_date', '<=', $date)
                ->where(function ($query) use ($date) {
                    $query->whereNull('ft.end_date')
                          ->orWhere('ft.end_date', '>=', $date);
                })
                ->count();

            $timeline[] = [
                'date' => $date,
                'occupancies' => $occupancies,
                'formatted_date' => $current->format('M d'),
            ];

            $current->addDay();
        }

        return $timeline;
    }

    /**
     * Get tenant assignment summary.
     */
    public function getTenantAssignmentSummary(Building $building): array
    {
        $allTenants = $building->tenants()->count();
        $assignedTenants = $this->getCurrentOccupancies($building)->count();
        $unassignedTenants = $allTenants - $assignedTenants;

        return [
            'all_tenants' => $allTenants,
            'assigned_tenants' => $assignedTenants,
            'unassigned_tenants' => $unassignedTenants,
            'assignment_rate' => $allTenants > 0 ? round(($assignedTenants / $allTenants) * 100, 1) : 0,
        ];
    }

    /**
     * Get flat occupancy history.
     */
    public function getFlatOccupancyHistory(Building $building): Collection
    {
        return collect(DB::table('flat_tenant as ft')
            ->join('flats as f', 'f.id', '=', 'ft.flat_id')
            ->join('tenants as t', 't.id', '=', 'ft.tenant_id')
            ->where('f.building_id', $building->id)
            ->select(
                'ft.*',
                'f.flat_number',
                't.name as tenant_name',
                't.email as tenant_email'
            )
            ->orderByDesc('ft.start_date')
            ->get());
    }

    /**
     * Get building occupancy metrics.
     */
    public function getBuildingOccupancyMetrics(Building $building): array
    {
        $stats = $this->getBuildingTenantStats($building);
        $averageOccupancyDuration = $this->getAverageOccupancyDuration($building);
        $turnoverRate = $this->getTurnoverRate($building);

        return array_merge($stats, [
            'average_occupancy_duration' => $averageOccupancyDuration,
            'turnover_rate' => $turnoverRate,
        ]);
    }

    /**
     * Get average occupancy duration in days.
     */
    protected function getAverageOccupancyDuration(Building $building): int
    {
        $completedOccupancies = DB::table('flat_tenant as ft')
            ->join('flats as f', 'f.id', '=', 'ft.flat_id')
            ->where('f.building_id', $building->id)
            ->whereNotNull('ft.end_date')
            ->whereNotNull('ft.start_date')
            ->selectRaw('DATEDIFF(ft.end_date, ft.start_date) as duration')
            ->get();

        if ($completedOccupancies->isEmpty()) {
            return 0;
        }

        return (int) $completedOccupancies->avg('duration');
    }

    /**
     * Get tenant turnover rate (completed occupancies in last 12 months).
     */
    protected function getTurnoverRate(Building $building): int
    {
        $oneYearAgo = now()->subYear()->toDateString();

        return DB::table('flat_tenant as ft')
            ->join('flats as f', 'f.id', '=', 'ft.flat_id')
            ->where('f.building_id', $building->id)
            ->whereNotNull('ft.end_date')
            ->where('ft.end_date', '>=', $oneYearAgo)
            ->count();
    }

    /**
     * Get filtered tenants with pagination and search.
     */
    public function getFilteredTenants(Building $building, ?string $search = null): LengthAwarePaginator
    {
        $query = $building->tenants()
            ->with(['flats' => function ($q) use ($building) {
                $q->where('building_id', $building->id)
                  ->withPivot('start_date', 'end_date', 'id');
            }]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        return $query->paginate(15)->appends(['q' => $search]);
    }

    /**
     * Get monthly occupancy report.
     */
    public function getMonthlyOccupancyReport(Building $building, int $months = 12): array
    {
        $report = [];
        $current = now()->subMonths($months)->startOfMonth();

        while ($current->lte(now())) {
            $monthStart = $current->toDateString();
            $monthEnd = $current->endOfMonth()->toDateString();

            $occupancies = DB::table('flat_tenant as ft')
                ->join('flats as f', 'f.id', '=', 'ft.flat_id')
                ->where('f.building_id', $building->id)
                ->where('ft.start_date', '<=', $monthEnd)
                ->where(function ($query) use ($monthStart) {
                    $query->whereNull('ft.end_date')
                          ->orWhere('ft.end_date', '>=', $monthStart);
                })
                ->count();

            $report[] = [
                'month' => $current->format('Y-m'),
                'month_name' => $current->format('M Y'),
                'occupancies' => $occupancies,
            ];

            $current->addMonth()->startOfMonth();
        }

        return $report;
    }

    /**
     * Get tenant activity summary.
     */
    public function getTenantActivitySummary(Building $building): array
    {
        $recentMoveIns = DB::table('flat_tenant as ft')
            ->join('flats as f', 'f.id', '=', 'ft.flat_id')
            ->join('tenants as t', 't.id', '=', 'ft.tenant_id')
            ->where('f.building_id', $building->id)
            ->where('ft.start_date', '>=', now()->subDays(30)->toDateString())
            ->count();

        $recentMoveOuts = DB::table('flat_tenant as ft')
            ->join('flats as f', 'f.id', '=', 'ft.flat_id')
            ->where('f.building_id', $building->id)
            ->whereNotNull('ft.end_date')
            ->where('ft.end_date', '>=', now()->subDays(30)->toDateString())
            ->count();

        return [
            'recent_move_ins' => $recentMoveIns,
            'recent_move_outs' => $recentMoveOuts,
            'net_change' => $recentMoveIns - $recentMoveOuts,
        ];
    }
}