<?php

namespace App\Http\Controllers;

use App\Domains\Inventory\Models\InventoryItem;
use App\Domains\Purchases\Actions\ConfirmPurchaseAction;
use App\Domains\Purchases\Models\Purchase;
use App\Domains\Purchases\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(Request $request): View
    {
        $purchases = Purchase::query()
            ->with('supplier')
            ->when($request->string('status')->toString(), fn ($query, string $status) => $query->where('status', $status))
            ->latest('purchased_at')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('purchases.index', [
            'purchases' => $purchases,
            'status' => $request->string('status')->toString(),
        ]);
    }

    public function create(): View
    {
        return view('purchases.create', $this->formData(new Purchase([
            'purchased_at' => now()->toDateString(),
            'status' => 'draft',
        ])));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);

        $purchase = DB::transaction(function () use ($validated): Purchase {
            $purchase = Purchase::query()->create($validated['purchase']);
            $this->syncLines($purchase, $validated['lines']);

            return $purchase;
        });

        return redirect()->route('purchases.show', $purchase)->with('status', 'Compra creada como borrador.');
    }

    public function show(Purchase $purchase): View
    {
        return view('purchases.show', [
            'purchase' => $purchase->load(['supplier', 'lines.inventoryItem', 'inventoryMovements.inventoryItem']),
        ]);
    }

    public function edit(Purchase $purchase): View
    {
        abort_if($purchase->status === 'confirmed', 403);

        return view('purchases.edit', $this->formData($purchase->load('lines')));
    }

    public function update(Request $request, Purchase $purchase): RedirectResponse
    {
        abort_if($purchase->status === 'confirmed', 403);

        $validated = $this->validatedData($request);

        DB::transaction(function () use ($purchase, $validated): void {
            $purchase->update($validated['purchase']);
            $this->syncLines($purchase, $validated['lines']);
        });

        return redirect()->route('purchases.show', $purchase)->with('status', 'Compra actualizada.');
    }

    public function confirm(Purchase $purchase, ConfirmPurchaseAction $action): RedirectResponse
    {
        $action->execute($purchase);

        return redirect()->route('purchases.show', $purchase)->with('status', 'Compra confirmada. Kardex y costo promedio actualizados.');
    }

    private function formData(Purchase $purchase): array
    {
        return [
            'purchase' => $purchase,
            'suppliers' => Supplier::query()->where('active', true)->orderBy('name')->get(),
            'inventoryItems' => InventoryItem::query()->where('active', true)->orderBy('name')->get(),
        ];
    }

    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'purchased_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'lines' => ['array'],
            'lines.*.inventory_item_id' => ['nullable', 'exists:inventory_items,id'],
            'lines.*.quantity' => ['nullable', 'numeric', 'min:0.0001', 'max:999999.9999'],
            'lines.*.unit_cost' => ['nullable', 'numeric', 'min:0', 'max:999999.999999'],
        ]);

        $lines = collect($validated['lines'] ?? [])
            ->filter(fn (array $line): bool => filled($line['inventory_item_id'] ?? null))
            ->map(fn (array $line): array => [
                'inventory_item_id' => (int) $line['inventory_item_id'],
                'quantity' => round((float) ($line['quantity'] ?? 0), 4),
                'unit_cost' => round((float) ($line['unit_cost'] ?? 0), 6),
            ])
            ->filter(fn (array $line): bool => $line['quantity'] > 0)
            ->values();

        if ($lines->isEmpty()) {
            throw ValidationException::withMessages([
                'lines' => 'La compra debe tener al menos una linea valida.',
            ]);
        }

        return [
            'purchase' => [
                'supplier_id' => $validated['supplier_id'] ?? null,
                'purchased_at' => $validated['purchased_at'],
                'status' => 'draft',
                'notes' => $validated['notes'] ?? null,
                'total_amount' => $lines->sum(fn (array $line): float => round($line['quantity'] * $line['unit_cost'], 2)),
            ],
            'lines' => $lines->all(),
        ];
    }

    private function syncLines(Purchase $purchase, array $lines): void
    {
        $purchase->lines()->delete();

        foreach ($lines as $line) {
            $purchase->lines()->create($line + [
                'total_cost' => round($line['quantity'] * $line['unit_cost'], 2),
            ]);
        }
    }
}
