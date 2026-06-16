<?php

namespace App\Domains\Sales\Actions;

use App\Domains\Commissions\Models\CommissionEntry;
use App\Domains\Inventory\DTOs\InventoryMovementData;
use App\Domains\Inventory\Enums\InventoryMovementDirection;
use App\Domains\Inventory\Enums\InventoryMovementType;
use App\Domains\Inventory\Models\InventoryItem;
use App\Domains\Inventory\Services\KardexService;
use App\Domains\Payments\Enums\PaymentStatus;
use App\Domains\Payments\Models\Payment;
use App\Domains\Sales\Enums\SaleStatus;
use App\Domains\Sales\Models\Sale;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConfirmSaleAction
{
    public function __construct(
        private readonly KardexService $kardex,
    ) {
    }

    public function execute(Sale $sale): Sale
    {
        return DB::transaction(function () use ($sale): Sale {
            $sale->loadMissing('lines.productVariant.product', 'lines.inventoryItem', 'seller', 'maker', 'customer');

            if ($sale->status === SaleStatus::Confirmed) {
                throw ValidationException::withMessages([
                    'sale' => 'La venta ya fue confirmada.',
                ]);
            }

            if ($sale->lines->isEmpty()) {
                throw ValidationException::withMessages([
                    'sale' => 'La venta debe tener al menos una linea.',
                ]);
            }

            $subtotal = 0;
            $visibleProfit = 0;
            $hiddenProfit = 0;
            $sellerPaymentAmount = 0;
            $makerPaymentAmount = 0;

            foreach ($sale->lines as $line) {
                /** @var InventoryItem $inventoryItem */
                $inventoryItem = InventoryItem::query()
                    ->lockForUpdate()
                    ->findOrFail($line->inventory_item_id);

                if ((float) $inventoryItem->current_stock < (float) $line->quantity) {
                    throw ValidationException::withMessages([
                        'sale' => "Stock insuficiente para {$inventoryItem->name}.",
                    ]);
                }

                $realUnitCost = (float) $inventoryItem->average_cost;
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
                    $amount = round($line->productVariant->product->commission_amount * (float) $line->quantity, 2);

                    CommissionEntry::query()->create([
                        'sale_id' => $sale->id,
                        'sale_line_id' => $line->id,
                        'person_id' => $sale->seller_id,
                        'type' => 'seller',
                        'amount' => $amount,
                        'earned_at' => $sale->sold_at,
                    ]);

                    $sellerPaymentAmount += $amount;
                }

                if ($sale->maker_id && (float) $line->productVariant->product->maker_payment_amount > 0) {
                    $amount = round($line->productVariant->product->maker_payment_amount * (float) $line->quantity, 2);

                    CommissionEntry::query()->create([
                        'sale_id' => $sale->id,
                        'sale_line_id' => $line->id,
                        'person_id' => $sale->maker_id,
                        'type' => 'maker_payment',
                        'amount' => $amount,
                        'earned_at' => $sale->sold_at,
                    ]);

                    $makerPaymentAmount += $amount;
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

            $this->createPendingPayment($sale, $sale->seller_id, $sellerPaymentAmount, 'Comision por venta');
            $this->createPendingPayment($sale, $sale->maker_id, $makerPaymentAmount, 'Pago elaborador por venta');

            return $sale;
        });
    }

    private function createPendingPayment(Sale $sale, ?int $personId, float $amount, string $concept): void
    {
        if (! $personId || $amount <= 0) {
            return;
        }

        $soldAt = Carbon::parse($sale->sold_at);
        $weekStart = $soldAt->copy()->startOfWeek(Carbon::SUNDAY);
        $weekEnd = $soldAt->copy()->endOfWeek(Carbon::SATURDAY);

        Payment::query()->create([
            'person_id' => $personId,
            'amount' => round($amount, 2),
            'concept' => "{$concept} #{$sale->id}",
            'status' => PaymentStatus::Pending,
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
            'notes' => 'Generado automaticamente al confirmar venta.',
        ]);
    }
}
