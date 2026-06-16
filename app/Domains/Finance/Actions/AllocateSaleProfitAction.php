<?php

namespace App\Domains\Finance\Actions;

use App\Domains\Finance\Models\FinancialProfitAllocation;
use App\Domains\Sales\Models\Sale;
use App\Domains\SocialFund\Enums\SocialFundMovementType;
use App\Domains\SocialFund\Models\SocialFundMovement;
use Illuminate\Support\Facades\DB;

class AllocateSaleProfitAction
{
    public function execute(Sale $sale): FinancialProfitAllocation
    {
        return DB::transaction(function () use ($sale): FinancialProfitAllocation {
            $profit = max(0, (float) $sale->visible_profit + (float) $sale->hidden_profit);

            $allocation = FinancialProfitAllocation::query()->updateOrCreate(
                ['sale_id' => $sale->id],
                [
                    'profit_amount' => round($profit, 2),
                    'social_fund_amount' => round($profit * 0.10, 2),
                    'reinvestment_amount' => round($profit * 0.40, 2),
                    'founder_recovery_amount' => round($profit * 0.25, 2),
                    'reserve_amount' => round($profit * 0.25, 2),
                    'allocated_at' => $sale->sold_at,
                ],
            );

            SocialFundMovement::query()->updateOrCreate(
                ['financial_profit_allocation_id' => $allocation->id, 'type' => SocialFundMovementType::Allocation],
                [
                    'amount' => $allocation->social_fund_amount,
                    'movement_date' => $allocation->allocated_at,
                    'notes' => 'Asignacion automatica del 10% de utilidad al fondo social.',
                ],
            );

            return $allocation;
        });
    }
}
