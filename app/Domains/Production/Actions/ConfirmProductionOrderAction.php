<?php

namespace App\Domains\Production\Actions;

use App\Domains\Inventory\DTOs\InventoryMovementData;
use App\Domains\Inventory\Enums\InventoryMovementDirection;
use App\Domains\Inventory\Enums\InventoryMovementType;
use App\Domains\Inventory\Models\InventoryItem;
use App\Domains\Inventory\Services\KardexService;
use App\Domains\Production\Enums\ProductionStatus;
use App\Domains\Production\Models\ProductionConsumption;
use App\Domains\Production\Models\ProductionOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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

            if ($order->status === ProductionStatus::Confirmed) {
                throw ValidationException::withMessages([
                    'production' => 'La orden de produccion ya fue confirmada.',
                ]);
            }

            if ($producedQuantity <= 0) {
                throw ValidationException::withMessages([
                    'produced_quantity' => 'La cantidad producida debe ser mayor a cero.',
                ]);
            }

            if ($order->recipe->ingredients->isEmpty()) {
                throw ValidationException::withMessages([
                    'production' => 'La formula debe tener al menos un ingrediente.',
                ]);
            }

            if (! $order->productVariant->inventoryItem) {
                throw ValidationException::withMessages([
                    'production' => 'El producto terminado no tiene item de inventario.',
                ]);
            }

            $realCostTotal = 0;
            $factor = $producedQuantity / (float) $order->recipe->expected_yield;

            ProductionConsumption::query()
                ->where('production_order_id', $order->id)
                ->delete();

            foreach ($order->recipe->ingredients as $ingredient) {
                /** @var InventoryItem $item */
                $item = InventoryItem::query()
                    ->lockForUpdate()
                    ->findOrFail($ingredient->inventory_item_id);

                $consumedQuantity = round((float) $ingredient->quantity * $factor, 4);

                if ((float) $item->current_stock < $consumedQuantity) {
                    throw ValidationException::withMessages([
                        'production' => "Stock insuficiente para {$item->name}.",
                    ]);
                }

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
