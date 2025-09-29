<?php

namespace App\Services\Owner;

use App\Models\Bill;
use App\Models\Flat;
use App\Models\BillCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class BillService
{
    /**
     * Create a monthly bill for a flat.
     */
    public function createMonthlyBill(int $ownerId, int $flatId, int $categoryId, string $monthYmd, float $amount, ?string $notes = null): Bill
    {
        $month = Carbon::parse($monthYmd)->startOfMonth()->toDateString();

        // 1) ensure unique (owner,flat,category,month)
        $exists = Bill::where('owner_id', $ownerId)->where([
            ['flat_id', '=', $flatId],
            ['bill_category_id', '=', $categoryId],
            ['month', '=', $month],
        ])->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'month' => 'Bill already exists for this flat, category & month.'
            ]);
        }

        // 2) scope checks
        $flat = Flat::where('owner_id', $ownerId)->findOrFail($flatId);
        $category = BillCategory::where('owner_id', $ownerId)->findOrFail($categoryId);

        // 3) find snapshot tenant for that month
        $tenant = $flat->tenantForMonth($month);
        $billTo = $tenant ? 'tenant' : 'owner';

        // 4) compute carry-forward (same flat+category, older months)
        $prevBills = Bill::where('owner_id', $ownerId)
            ->where('flat_id', $flatId)
            ->where('bill_category_id', $categoryId)
            ->where('status', 'unpaid')
            ->where('month', '<', $month)
            ->orderBy('month')
            ->get();

        $carry = 0.0;
        foreach ($prevBills as $b) {
            $paid = $b->payments()->sum('amount');
            $due  = max(0, ($b->amount + $b->due_carry_forward) - $paid);
            $carry += $due;
        }

        // 5) create bill
        return Bill::create([
            'owner_id'         => $ownerId,
            'flat_id'          => $flatId,
            'bill_category_id' => $categoryId,
            'tenant_id'        => $tenant?->id,
            'bill_to'          => $billTo,
            'month'            => $month,
            'amount'           => $amount,
            'due_carry_forward'=> $carry,
            'status'           => 'unpaid',
            'notes'            => $notes,
        ]);
    }

    /**
     * Get bills with filters and pagination.
     */
    public function getBillsWithFilters(int $ownerId, array $filters = []): LengthAwarePaginator
    {
        $query = Bill::with(['flat:id,flat_number,building_id', 'flat.building:id,name', 'category:id,name', 'tenant:id,name,email'])
            ->where('owner_id', $ownerId)
            ->when($filters['flat_id'] ?? null, fn($q, $v) => $q->where('flat_id', $v))
            ->when($filters['category_id'] ?? null, fn($q, $v) => $q->where('bill_category_id', $v))
            ->when($filters['status'] ?? null, fn($q, $v) => $q->where('status', $v))
            ->when($filters['bill_to'] ?? null, fn($q, $v) => $q->where('bill_to', $v))
            ->when($filters['month_from'] ?? null, fn($q, $v) => $q->whereDate('month', '>=', date('Y-m-01', strtotime($v.'-01'))))
            ->when($filters['month_to'] ?? null, fn($q, $v) => $q->whereDate('month', '<=', date('Y-m-t', strtotime($v.'-01'))))
            ->when($filters['q'] ?? null, function ($q, $v) {
                $q->whereHas('tenant', fn($t) => $t->where('name', 'like', "%$v%")->orWhere('email', 'like', "%$v%"));
            })
            ->orderByDesc('month')
            ->orderBy('flat_id');

        return $query->paginate(15)->withQueryString();
    }

    /**
     * Calculate bill totals for current filters.
     */
    public function calculateBillTotals(int $ownerId, array $filters = []): array
    {
        $query = Bill::where('owner_id', $ownerId)
            ->when($filters['flat_id'] ?? null, fn($q, $v) => $q->where('flat_id', $v))
            ->when($filters['category_id'] ?? null, fn($q, $v) => $q->where('bill_category_id', $v))
            ->when($filters['status'] ?? null, fn($q, $v) => $q->where('status', $v))
            ->when($filters['bill_to'] ?? null, fn($q, $v) => $q->where('bill_to', $v))
            ->when($filters['month_from'] ?? null, fn($q, $v) => $q->whereDate('month', '>=', date('Y-m-01', strtotime($v.'-01'))))
            ->when($filters['month_to'] ?? null, fn($q, $v) => $q->whereDate('month', '<=', date('Y-m-t', strtotime($v.'-01'))))
            ->when($filters['q'] ?? null, function ($q, $v) {
                $q->whereHas('tenant', fn($t) => $t->where('name', 'like', "%$v%")->orWhere('email', 'like', "%$v%"));
            });

        return $query->get()->reduce(function($carry, $bill) {
            $paid = $bill->payments()->sum('amount');
            $due  = max(0, ($bill->amount + $bill->due_carry_forward) - $paid);
            $carry['amount'] += $bill->amount;
            $carry['carry']  += $bill->due_carry_forward;
            $carry['paid']   += $paid;
            $carry['due']    += $due;
            return $carry;
        }, ['amount' => 0, 'carry' => 0, 'paid' => 0, 'due' => 0]);
    }

    /**
     * Get dropdown data for bill forms.
     */
    public function getDropdownData(int $ownerId): array
    {
        // Only get flats that have active tenant assignments
        $flats = Flat::where('owner_id', $ownerId)
            ->with(['building:id,name', 'tenants' => function ($query) {
                $query->whereNull('flat_tenant.end_date');
            }])
            ->whereHas('tenants', function ($query) {
                $query->whereNull('flat_tenant.end_date');
            })
            ->orderBy('flat_number')
            ->get(['id', 'flat_number', 'building_id']);

        $categories = BillCategory::where('owner_id', $ownerId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return compact('flats', 'categories');
    }

    /**
     * Get all flats for owner (including unassigned ones) for reference.
     */
    public function getAllFlatsForOwner(int $ownerId): \Illuminate\Database\Eloquent\Collection
    {
        return Flat::where('owner_id', $ownerId)
            ->with(['building:id,name', 'tenants' => function ($query) {
                $query->whereNull('flat_tenant.end_date')
                      ->withPivot('start_date', 'end_date');
            }])
            ->orderBy('flat_number')
            ->get(['id', 'flat_number', 'building_id']);
    }

    /**
     * Get flats with tenant assignment status.
     */
    public function getFlatsWithTenantStatus(int $ownerId): array
    {
        $allFlats = $this->getAllFlatsForOwner($ownerId);
        $assignedFlats = $this->getDropdownData($ownerId)['flats'];

        return [
            'all_flats' => $allFlats,
            'assigned_flats' => $assignedFlats,
            'unassigned_flats' => $allFlats->whereNotIn('id', $assignedFlats->pluck('id')),
            'assigned_count' => $assignedFlats->count(),
            'unassigned_count' => $allFlats->count() - $assignedFlats->count(),
        ];
    }

    /**
     * Get bill statistics for dashboard.
     */
    public function getBillStats(int $ownerId): array
    {
        $currentMonth = Carbon::now()->startOfMonth()->toDateString();

        $totalBills = Bill::where('owner_id', $ownerId)->count();
        $currentMonthBills = Bill::where('owner_id', $ownerId)
            ->where('month', $currentMonth)
            ->count();

        $unpaidAmount = Bill::where('owner_id', $ownerId)
            ->where('status', '!=', 'paid')
            ->sum('amount');

        $thisMonthCollection = Bill::where('owner_id', $ownerId)
            ->where('month', $currentMonth)
            ->whereHas('payments', function ($query) use ($currentMonth) {
                $query->whereMonth('paid_at', Carbon::parse($currentMonth)->month)
                      ->whereYear('paid_at', Carbon::parse($currentMonth)->year);
            })
            ->sum('amount');

        return [
            'total_bills' => $totalBills,






}    }        ];            'this_month_collection' => $thisMonthCollection,            'unpaid_amount' => $unpaidAmount,            'current_month_bills' => $currentMonthBills,