<?php

namespace App\Models;

use App\Scopes\OwnerScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Flat extends Model
{
    use SoftDeletes;

    protected $fillable = ['building_id','owner_id','flat_number','flat_owner_name','flat_owner_phone'];

    protected static function booted()
    {
        static::addGlobalScope(new OwnerScope);
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function tenants()
    {
        return $this->belongsToMany(Tenant::class, 'flat_tenant')
                    ->withTimestamps()
                    ->withPivot(['start_date','end_date']);
    }

    public function currentTenant(?string $asOfDate = null): ?Tenant
    {
        $asOf = $asOfDate ? date('Y-m-d', strtotime($asOfDate)) : date('Y-m-d');
        return $this->tenants()
            ->where(function($q) use ($asOf) {
                $q->whereNull('flat_tenant.end_date')->orWhere('flat_tenant.end_date','>=',$asOf);
            })
            ->where(function($q) use ($asOf) {
                $q->whereNull('flat_tenant.start_date')->orWhere('flat_tenant.start_date','<=',$asOf);
            })
            ->latest('flat_tenant.start_date')
            ->first();
    }

    /**
     * Find the tenant who was occupying this flat during a specific month.
     *
     * @param string $month Date in 'Y-m-d' format (typically first day of month)
     * @return Tenant|null
     */
    public function tenantForMonth(string $month): ?Tenant
    {
        $monthStart = Carbon::parse($month)->startOfMonth();
        $monthEnd = Carbon::parse($month)->endOfMonth();

        return $this->tenants()
            ->wherePivot('start_date', '<=', $monthEnd->toDateString())
            ->where(function ($query) use ($monthStart) {
                $query->whereNull('flat_tenant.end_date')
                      ->orWhere('flat_tenant.end_date', '>=', $monthStart->toDateString());
            })
            ->orderBy('flat_tenant.start_date', 'desc')
            ->first();
    }

    /**
     * Check if flat is currently occupied.
     */
    public function isOccupied(): bool
    {
        return $this->tenants()
            ->whereNull('flat_tenant.end_date')
            ->exists();
    }

    /**
     * Get current active tenant.
     */
    public function getActiveTenant(): ?Tenant
    {
        return $this->tenants()
            ->whereNull('flat_tenant.end_date')
            ->orderBy('flat_tenant.start_date', 'desc')
            ->first();
    }

    /**
     * Get occupancy history for this flat.
     */
    public function getOccupancyHistory()
    {
        return $this->tenants()
            ->withPivot('start_date', 'end_date', 'id')
            ->orderBy('flat_tenant.start_date', 'desc');
    }

    /**
     * Check if flat was occupied during a date range.
     */
    public function wasOccupiedDuring(string $startDate, string $endDate): bool
    {
        return $this->tenants()
            ->where('flat_tenant.start_date', '<=', $endDate)
            ->where(function ($query) use ($startDate) {
                $query->whereNull('flat_tenant.end_date')
                      ->orWhere('flat_tenant.end_date', '>=', $startDate);
            })
            ->exists();
    }

    /**
     * Get flat display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return "Flat {$this->flat_number}";
    }

    /**
     * Get flat full address including building info.
     */
    public function getFullAddressAttribute(): string
    {
        return "Flat {$this->flat_number}, {$this->building->name}, {$this->building->address}";
    }

    /**
     * Get tenant assignments for a specific period.
     */
    public function getTenantsForPeriod(string $startDate, string $endDate)
    {
        return $this->tenants()
            ->wherePivot('start_date', '<=', $endDate)
            ->where(function ($query) use ($startDate) {
                $query->whereNull('flat_tenant.end_date')
                      ->orWherePivot('end_date', '>=', $startDate);
            })
            ->withPivot('start_date', 'end_date')
            ->orderBy('flat_tenant.start_date');
    }

    /**
     * Get available flats in building (not currently occupied).
     */
    public static function getAvailableInBuilding(int $buildingId)
    {
        return self::where('building_id', $buildingId)
            ->whereDoesntHave('tenants', function($query) {
                $query->whereNull('flat_tenant.end_date');
            })
            ->orderBy('flat_number');
    }

    /**
     * Get occupied flats in building.
     */
    public static function getOccupiedInBuilding(int $buildingId)
    {
        return self::where('building_id', $buildingId)
            ->whereHas('tenants', function($query) {
                $query->whereNull('flat_tenant.end_date');
            })
            ->with(['tenants' => function($query) {
                $query->whereNull('flat_tenant.end_date')
                      ->withPivot('start_date', 'end_date');
            }])
            ->orderBy('flat_number');
    }
}