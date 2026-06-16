<?php

namespace App\Http\Controllers;

use App\Domains\Customers\Models\Customer;
use App\Domains\Finance\Actions\AllocateSaleProfitAction;
use App\Domains\People\Models\Person;
use App\Domains\Products\Models\ProductVariant;
use App\Domains\Sales\Actions\ConfirmSaleAction;
use App\Domains\Sales\Enums\SaleStatus;
use App\Domains\Sales\Models\Sale;
use App\Domains\Sales\Models\SalesChannel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $sales = Sale::query()
            ->with(['customer', 'seller', 'maker', 'salesChannel'])
            ->when($request->string('status')->toString(), fn ($query, string $status) => $query->where('status', $status))
            ->latest('sold_at')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('sales.index', [
            'sales' => $sales,
            'status' => $request->string('status')->toString(),
        ]);
    }

    public function create(): View
    {
        return view('sales.create', $this->formData(new Sale([
            'sold_at' => now()->toDateString(),
            'status' => SaleStatus::Draft,
            'discount_total' => 0,
        ])));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);

        $sale = DB::transaction(function () use ($validated): Sale {
            $sale = Sale::query()->create($validated['sale']);
            $this->syncLines($sale, $validated['lines']);

            return $sale;
        });

        return redirect()->route('sales.show', $sale)->with('status', 'Venta creada como borrador.');
    }

    public function show(Sale $sale): View
    {
        return view('sales.show', [
            'sale' => $sale->load([
                'customer',
                'seller',
                'maker',
                'salesChannel',
                'lines.productVariant.product',
                'lines.inventoryItem',
                'inventoryMovements.inventoryItem',
                'commissionEntries.person',
                'profitAllocation',
            ]),
        ]);
    }

    public function edit(Sale $sale): View
    {
        abort_if($sale->status === SaleStatus::Confirmed, 403);

        return view('sales.edit', $this->formData($sale->load('lines')));
    }

    public function update(Request $request, Sale $sale): RedirectResponse
    {
        abort_if($sale->status === SaleStatus::Confirmed, 403);

        $validated = $this->validatedData($request);

        DB::transaction(function () use ($sale, $validated): void {
            $sale->update($validated['sale']);
            $this->syncLines($sale, $validated['lines']);
        });

        return redirect()->route('sales.show', $sale)->with('status', 'Venta actualizada.');
    }

    public function confirm(Sale $sale, ConfirmSaleAction $confirmSale, AllocateSaleProfitAction $allocateProfit): RedirectResponse
    {
        $confirmSale->execute($sale);
        $sale->refresh();
        $allocateProfit->execute($sale);

        return redirect()->route('sales.show', $sale)->with('status', 'Venta confirmada. Kardex, utilidades, comisiones y pagos actualizados.');
    }

    private function formData(Sale $sale): array
    {
        return [
            'sale' => $sale,
            'customers' => Customer::query()->orderBy('name')->get(),
            'sellers' => Person::query()
                ->where('active', true)
                ->whereHas('roleAssignments', fn ($query) => $query->where('role', 'seller'))
                ->orderBy('name')
                ->get(),
            'makers' => Person::query()
                ->where('active', true)
                ->whereHas('roleAssignments', fn ($query) => $query->where('role', 'maker'))
                ->orderBy('name')
                ->get(),
            'channels' => SalesChannel::query()->where('active', true)->orderBy('name')->get(),
            'variants' => ProductVariant::query()
                ->with(['product', 'inventoryItem'])
                ->where('active', true)
                ->whereHas('inventoryItem')
                ->orderBy('name')
                ->get(),
        ];
    }

    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'seller_id' => ['nullable', 'exists:people,id'],
            'maker_id' => ['nullable', 'exists:people,id'],
            'sales_channel_id' => ['nullable', 'exists:sales_channels,id'],
            'sold_at' => ['required', 'date'],
            'discount_total' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'lines' => ['array'],
            'lines.*.product_variant_id' => ['nullable', 'exists:product_variants,id'],
            'lines.*.quantity' => ['nullable', 'numeric', 'min:0.0001', 'max:999999.9999'],
            'lines.*.unit_price' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
        ]);

        $lines = collect($validated['lines'] ?? [])
            ->filter(fn (array $line): bool => filled($line['product_variant_id'] ?? null))
            ->map(function (array $line): array {
                $variant = ProductVariant::query()
                    ->with('inventoryItem')
                    ->findOrFail($line['product_variant_id']);

                if (! $variant->inventoryItem) {
                    throw ValidationException::withMessages([
                        'lines' => "La variante {$variant->name} no tiene item de inventario.",
                    ]);
                }

                $quantity = round((float) ($line['quantity'] ?? 0), 4);
                $unitPrice = round((float) ($line['unit_price'] ?? $variant->price), 2);

                return [
                    'product_variant_id' => $variant->id,
                    'inventory_item_id' => $variant->inventoryItem->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => round($quantity * $unitPrice, 2),
                ];
            })
            ->filter(fn (array $line): bool => $line['quantity'] > 0)
            ->values();

        if ($lines->isEmpty()) {
            throw ValidationException::withMessages([
                'lines' => 'La venta debe tener al menos una linea valida.',
            ]);
        }

        $subtotal = $lines->sum('line_total');
        $discount = round((float) ($validated['discount_total'] ?? 0), 2);

        return [
            'sale' => [
                'customer_id' => $validated['customer_id'],
                'seller_id' => $validated['seller_id'] ?? null,
                'maker_id' => $validated['maker_id'] ?? null,
                'sales_channel_id' => $validated['sales_channel_id'] ?? null,
                'sold_at' => $validated['sold_at'],
                'status' => SaleStatus::Draft,
                'subtotal' => $subtotal,
                'discount_total' => $discount,
                'total_amount' => max(0, $subtotal - $discount),
                'visible_profit' => 0,
                'hidden_profit' => 0,
                'notes' => $validated['notes'] ?? null,
            ],
            'lines' => $lines->all(),
        ];
    }

    private function syncLines(Sale $sale, array $lines): void
    {
        $sale->lines()->delete();

        foreach ($lines as $line) {
            $sale->lines()->create($line + [
                'standard_unit_cost' => 0,
                'real_unit_cost' => 0,
                'visible_profit' => 0,
                'hidden_profit' => 0,
            ]);
        }
    }
}
