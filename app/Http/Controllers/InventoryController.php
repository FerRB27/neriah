<?php

namespace App\Http\Controllers;

use App\Domains\Inventory\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $items = InventoryItem::query()
            ->withCount('movements')
            ->when($request->string('q')->toString(), function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($request->string('type')->toString(), fn ($query, string $type) => $query->where('item_type', $type))
            ->when($request->boolean('critical'), fn ($query) => $query->whereColumn('current_stock', '<=', 'minimum_stock'))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('inventory.index', [
            'items' => $items,
            'search' => $request->string('q')->toString(),
            'type' => $request->string('type')->toString(),
            'critical' => $request->boolean('critical'),
            'summary' => [
                'items' => InventoryItem::query()->count(),
                'critical' => InventoryItem::query()->whereColumn('current_stock', '<=', 'minimum_stock')->count(),
                'inputs' => InventoryItem::query()->where('item_type', 'input')->count(),
                'finishedGoods' => InventoryItem::query()->where('item_type', 'finished_good')->count(),
                'value' => InventoryItem::query()->selectRaw('COALESCE(SUM(current_stock * average_cost), 0) as value')->value('value'),
            ],
        ]);
    }

    public function show(InventoryItem $inventoryItem): View
    {
        $inventoryItem->load(['input.category', 'productVariant.product']);

        $movements = $inventoryItem->movements()
            ->with(['purchase.supplier', 'productionOrder', 'sale.customer'])
            ->latest('movement_date')
            ->latest()
            ->paginate(20);

        return view('inventory.show', [
            'item' => $inventoryItem,
            'movements' => $movements,
        ]);
    }
}
