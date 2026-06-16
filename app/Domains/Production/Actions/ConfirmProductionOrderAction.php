<?php

namespace App\Domains\Production\Actions;

use App\Domains\Inventory\DTOs\InventoryMovementData;
use App\Domains\Inventory\Enums\InventoryMovementDirection;
use App\Domains\Inventory\Enums\InventoryMovementType;
use App\Domains\Inventory\Services\KardexService;
use App\Domains\Production\Enums\ProductionStatus;
use App\Domains\Production\Models\ProductionConsumption;
use App\Domains\Production\Models\ProductionOrder;
use Illuminate\Support\Facades\DB;

class ConfirmProductionOrderAction
{
    public function __construct(
        private readonly KardexService $kardex,
    ) {
    }

    public function execute(ProductionOrder $order, float $producedQuantity): ProductionOrder
    {
        return DB::transaction(function () use ($order, $producedQuantity): ProductionOrder {
            $order->loadMissing('recipe.ingredients.inventoryItem', 'productVariant.inventoryItem');

            $realCostTotal = 0;

            foreach ($order->recipe->ingredients as $ingredient) {
                $item = $ingredient->inventoryItem;
                $factor = $producedQuantity / (float) $order->recipe->expected_yield;
                $consumedQuantity = round((float) $ingredient->quantity * $factor, 4);
                $unitCost = (float) $item->average_cost;
                $totalCost = round($consumedQuantity * $unitCost, 2);

                ProductionConsumption::query()->create([
                    'production_order_id' => $order->id,
                    'inventory_item_id' => $item->id,
                    'planned_quantity' => $ingredient->quantity,
                    'consumed_quantity' => $consumedQuantity,
                    'unit_cost' => $unitCost,
                    'total_cost' => $totalCost,
                ]);

                $this->kardex->record(new InventoryMovementData(
                    inventoryItemId: $item->id,
                    type: InventoryMovementType::ProductionConsumption,
                    direction: InventoryMovementDirection::Out,
                    quantity: $consumedQuantity,
                    unitCost: $unitCost,
                    movementDate: $order->produced_at,
                    productionOrderId: $order->id,
                    notes: 'Consumo de insumo por produccion',
                ));

                $realCostTotal += $totalCost;
            }

            $outputItem = $order->productVariant->inventoryItem;
            $outputUnitCost = $producedQuantity > 0 ? $realCostTotal / $producedQuantity : 0;

            $this->kardex->record(new InventoryMovementData(
                inventoryItemId: $outputItem->id,
                type: InventoryMovementType::ProductionOutput,
                direction: InventoryMovementDirection::In,
                quantity: $producedQuantity,
                unitCost: $outputUnitCost,
                movementDate: $order->produced_at,
                productionOrderId: $order->id,
                notes: 'Producto terminado generado por produccion',
            ));

            $order->forceFill([
                'status' => ProductionStatus::Confirmed,
                'produced_quantity' => $producedQuantity,
                'real_cost_total' => $realCostTotal,
                'confirmed_at' => now(),
            ])->save();

            return $order;
        });
    }
}
