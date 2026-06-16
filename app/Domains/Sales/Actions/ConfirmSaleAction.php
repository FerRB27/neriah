<?php

namespace App\Domains\Sales\Actions;

use App\Domains\Commissions\Models\CommissionEntry;
use App\Domains\Inventory\DTOs\InventoryMovementData;
use App\Domains\Inventory\Enums\InventoryMovementDirection;
use App\Domains\Inventory\Enums\InventoryMovementType;
use App\Domains\Inventory\Services\KardexService;
use App\Domains\Sales\Enums\SaleStatus;
use App\Domains\Sales\Models\Sale;
use Illuminate\Support\Facades\DB;

class ConfirmSaleAction
{
    public function __construct(
        private readonly KardexService $kardex,
    ) {
    }

    public function execute(Sale $sale): Sale
    {
        return DB::transaction(function () use ($sale): Sale {
            $sale->loadMissing('lines.productVariant.product', 'seller', 'maker', 'customer');

            $subtotal = 0;
            $visibleProfit = 0;
            $hiddenProfit = 0;

            foreach ($sale->lines as $line) {
                $realUnitCost = (float) $line->inventoryItem->average_cost;
                $standardUnitCost = (float) $line->productVariant->product->standard_cost;
                $lineTotal = (float) $line->quantity * (float) $line->unit_price;
                $lineVisibleProfit = $lineTotal - ((float) $line->quantity * $standardUnitCost);
                $lineHiddenProfit = ((float) $line->quantity * $standardUnitCost) - ((float) $line->quantity * $realUnitCost);

                $line->forceFill([
                    'real_unit_cost' => $realUnitCost,
                    'standard_unit_cost' => $standardUnitCost,
                    'line_total' => round($lineTotal, 2),
                    'visible_profit' => round($lineVisibleProfit, 2),
                    'hidden_profit' => round($lineHiddenProfit, 2),
                ])->save();

                $this->kardex->record(new InventoryMovementData(
                    inventoryItemId: $line->inventory_item_id,
                    type: InventoryMovementType::Sale,
                    direction: InventoryMovementDirection::Out,
                    quantity: (float) $line->quantity,
                    unitCost: $realUnitCost,
                    movementDate: $sale->sold_at,
                    saleId: $sale->id,
                    saleLineId: $line->id,
                    notes: 'Venta confirmada',
                ));

                if ($sale->seller_id && (float) $line->productVariant->product->commission_amount > 0) {
                    CommissionEntry::query()->create([
                        'sale_id' => $sale->id,
                        'sale_line_id' => $line->id,
                        'person_id' => $sale->seller_id,
                        'type' => 'seller',
                        'amount' => $line->productVariant->product->commission_amount * (float) $line->quantity,
                        'earned_at' => $sale->sold_at,
                    ]);
                }

                if ($sale->maker_id && (float) $line->productVariant->product->maker_payment_amount > 0) {
                    CommissionEntry::query()->create([
                        'sale_id' => $sale->id,
                        'sale_line_id' => $line->id,
                        'person_id' => $sale->maker_id,
                        'type' => 'maker_payment',
                        'amount' => $line->productVariant->product->maker_payment_amount * (float) $line->quantity,
                        'earned_at' => $sale->sold_at,
                    ]);
                }

                $subtotal += $lineTotal;
                $visibleProfit += $lineVisibleProfit;
                $hiddenProfit += $lineHiddenProfit;
            }

            $sale->forceFill([
                'status' => SaleStatus::Confirmed,
                'subtotal' => round($subtotal, 2),
                'total_amount' => round($subtotal - (float) $sale->discount_total, 2),
                'visible_profit' => round($visibleProfit, 2),
                'hidden_profit' => round($hiddenProfit, 2),
                'confirmed_at' => now(),
            ])->save();

            $sale->customer->forceFill([
                'first_purchase_date' => $sale->customer->first_purchase_date ?? $sale->sold_at,
                'last_purchase_date' => $sale->sold_at,
                'total_purchased' => (float) $sale->customer->total_purchased + (float) $sale->total_amount,
                'orders_count' => $sale->customer->orders_count + 1,
            ])->save();

            return $sale;
        });
    }
}
