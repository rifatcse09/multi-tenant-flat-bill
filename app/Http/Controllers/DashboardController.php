<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Flat;
use App\Models\Bill;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $totalBuildings = Building::withoutGlobalScopes()->count();
            $totalFlats = Flat::withoutGlobalScopes()->count();
            $unpaidBills = Bill::withoutGlobalScopes()
                ->whereIn('status', ['unpaid', 'partial'])
                ->count();
            $paymentsThisMonth = Payment::whereMonth('paid_at', Carbon::now()->month)
                ->whereYear('paid_at', Carbon::now()->year)
                ->sum('amount');
        } else {
            $totalBuildings = Building::count();
            $totalFlats = Flat::count();
            $unpaidBills = Bill::whereIn('status', ['unpaid', 'partial'])->count();
            $paymentsThisMonth = Payment::whereHas('bill', fn ($q) => $q->where('owner_id', $user->id))
                ->whereMonth('paid_at', Carbon::now()->month)
                ->whereYear('paid_at', Carbon::now()->year)
                ->sum('amount');
        }

        return view('dashboard', [
            'totalBuildings' => $totalBuildings,
            'totalFlats' => $totalFlats,
            'unpaidBills' => $unpaidBills,
            'paymentsThisMonth' => $paymentsThisMonth,
        ]);
    }
}
