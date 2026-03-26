<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Building;
use App\Models\Flat;
use App\Models\OwnerSubscription;
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

            $adminAnalytics = $this->buildAdminAnalytics();
        } else {
            $totalBuildings = Building::count();
            $totalFlats = Flat::count();
            $unpaidBills = Bill::whereIn('status', ['unpaid', 'partial'])->count();
            $paymentsThisMonth = Payment::whereHas('bill', fn ($q) => $q->where('owner_id', $user->id))
                ->whereMonth('paid_at', Carbon::now()->month)
                ->whereYear('paid_at', Carbon::now()->year)
                ->sum('amount');
            $adminAnalytics = null;
        }

        return view('dashboard', [
            'totalBuildings' => $totalBuildings,
            'totalFlats' => $totalFlats,
            'unpaidBills' => $unpaidBills,
            'paymentsThisMonth' => $paymentsThisMonth,
            'adminAnalytics' => $adminAnalytics,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildAdminAnalytics(): array
    {
        $paymentTrendLabels = [];
        $paymentTrendAmounts = [];
        for ($i = 11; $i >= 0; $i--) {
            $start = Carbon::now()->subMonths($i)->startOfMonth();
            $end = $start->copy()->endOfMonth();
            $paymentTrendLabels[] = $start->format('M Y');
            $paymentTrendAmounts[] = round((float) Payment::query()
                ->whereBetween('paid_at', [$start->toDateString(), $end->toDateString()])
                ->sum('amount'), 2);
        }

        $paymentsLast12Months = array_sum($paymentTrendAmounts);

        $outstandingBills = (float) Bill::withoutGlobalScopes()
            ->whereIn('status', ['unpaid', 'partial'])
            ->with(['payments', 'adjustments'])
            ->get()
            ->sum(fn (Bill $bill) => $bill->due);

        $netPosition = round($paymentsLast12Months - $outstandingBills, 2);

        $subscriptionByPlan = OwnerSubscription::query()
            ->join('subscription_plans', 'owner_subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
            ->selectRaw('subscription_plans.name as plan_name, COUNT(*) as c')
            ->groupBy('subscription_plans.id', 'subscription_plans.name')
            ->orderByDesc('c')
            ->get();

        $subscriptionByStatus = OwnerSubscription::query()
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        $estimatedSubscriptionMrr = (float) OwnerSubscription::query()
            ->join('subscription_plans', 'owner_subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
            ->where('owner_subscriptions.status', OwnerSubscription::STATUS_ACTIVE)
            ->where('subscription_plans.is_free', false)
            ->sum('subscription_plans.price_monthly_cents') / 100;

        $trialingCount = OwnerSubscription::where('status', OwnerSubscription::STATUS_TRIALING)->count();
        $activePaidCount = OwnerSubscription::query()
            ->join('subscription_plans', 'owner_subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
            ->where('owner_subscriptions.status', OwnerSubscription::STATUS_ACTIVE)
            ->where('subscription_plans.is_free', false)
            ->count();

        return [
            'paymentTrend' => [
                'labels' => $paymentTrendLabels,
                'amounts' => $paymentTrendAmounts,
            ],
            'subscriptionByPlan' => [
                'labels' => $subscriptionByPlan->pluck('plan_name')->values()->all(),
                'counts' => $subscriptionByPlan->pluck('c')->map(fn ($v) => (int) $v)->values()->all(),
            ],
            'subscriptionByStatus' => [
                'labels' => $subscriptionByStatus->pluck('status')->map(fn ($s) => ucfirst(str_replace('_', ' ', (string) $s)))->values()->all(),
                'counts' => $subscriptionByStatus->pluck('c')->map(fn ($v) => (int) $v)->values()->all(),
            ],
            'profitLoss' => [
                'labels' => ['Collected (12 mo)', 'Outstanding bills', 'Net position'],
                'values' => [
                    round($paymentsLast12Months, 2),
                    round($outstandingBills, 2),
                    $netPosition,
                ],
            ],
            'kpi' => [
                'paymentsLast12Months' => round($paymentsLast12Months, 2),
                'outstandingBills' => round($outstandingBills, 2),
                'netPosition' => $netPosition,
                'estimatedSubscriptionMrr' => round($estimatedSubscriptionMrr, 2),
                'trialingSubscriptions' => $trialingCount,
                'activePaidSubscriptions' => $activePaidCount,
            ],
        ];
    }
}
