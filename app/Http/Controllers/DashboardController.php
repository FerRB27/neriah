<?php

namespace App\Http\Controllers;

use App\Domains\Finance\Enums\FounderCapitalMovementType;
use App\Domains\Finance\Models\FounderCapitalMovement;
use App\Domains\Inventory\Models\InventoryItem;
use App\Domains\Payments\Enums\PaymentStatus;
use App\Domains\Payments\Models\Payment;
use App\Domains\Production\Enums\ProductionStatus;
use App\Domains\Production\Models\ProductionOrder;
use App\Domains\Sales\Enums\SaleStatus;
use App\Domains\Sales\Models\Sale;
use App\Domains\SocialFund\Enums\SocialFundMovementType;
use App\Domains\SocialFund\Models\SocialFundMovement;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = Carbon::today();
        $weekStart = $today->copy()->startOfWeek(Carbon::SUNDAY);
        $weekEnd = $today->copy()->endOfWeek(Carbon::SATURDAY);

        $confirmedSales = Sale::query()->where('status', SaleStatus::Confirmed->value);

        $weeklySales = (clone $confirmedSales)
            ->whereBetween('sold_at', [$weekStart, $weekEnd])
            ->sum('total_amount');

        $monthlySales = (clone $confirmedSales)
            ->whereYear('sold_at', $today->year)
            ->whereMonth('sold_at', $today->month)
            ->sum('total_amount');

        $yearlySales = (clone $confirmedSales)
            ->whereYear('sold_at', $today->year)
            ->sum('total_amount');

        $visibleProfit = (clone $confirmedSales)->sum('visible_profit');
        $hiddenProfit = (clone $confirmedSales)->sum('hidden_profit');

        $founderContributions = FounderCapitalMovement::query()
            ->where('type', FounderCapitalMovementType::Contribution->value)
            ->sum('amount');

        $founderReimbursements = FounderCapitalMovement::query()
            ->where('type', FounderCapitalMovementType::Reimbursement->value)
            ->sum('amount');

        $socialFundIn = SocialFundMovement::query()
            ->whereIn('type', [SocialFundMovementType::Allocation->value, SocialFundMovementType::Adjustment->value])
            ->sum('amount');

        $socialFundOut = SocialFundMovement::query()
            ->where('type', SocialFundMovementType::Donation->value)
            ->sum('amount');

        return view('dashboard.index', [
            'metrics' => [
                'weeklySales' => $weeklySales,
                'monthlySales' => $monthlySales,
                'yearlySales' => $yearlySales,
                'visibleProfit' => $visibleProfit,
                'hiddenProfit' => $hiddenProfit,
                'totalProfit' => $visibleProfit + $hiddenProfit,
                'productionConfirmed' => ProductionOrder::query()->where('status', ProductionStatus::Confirmed->value)->count(),
                'criticalInventory' => InventoryItem::query()->whereColumn('current_stock', '<=', 'minimum_stock')->count(),
                'pendingPayments' => Payment::query()->where('status', PaymentStatus::Pending->value)->sum('amount'),
                'founderCapitalPending' => max(0, $founderContributions - $founderReimbursements),
                'socialFundBalance' => $socialFundIn - $socialFundOut,
            ],
            'weekRange' => [$weekStart, $weekEnd],
        ]);
    }
}
