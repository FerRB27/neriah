<?php

namespace App\Http\Controllers;

use App\Domains\Purchases\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $suppliers = Supplier::query()
            ->when($request->string('q')->toString(), function ($query, string $search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('suppliers.index', [
            'suppliers' => $suppliers,
            'search' => $request->string('q')->toString(),
        ]);
    }

    public function create(): View
    {
        return view('suppliers.create', [
            'supplier' => new Supplier(['active' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Supplier::query()->create($this->validatedData($request));

        return redirect()->route('suppliers.index')->with('status', 'Proveedor creado.');
    }

    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', [
            'supplier' => $supplier,
        ]);
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($this->validatedData($request));

        return redirect()->route('suppliers.index')->with('status', 'Proveedor actualizado.');
    }

    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'active' => ['nullable', 'boolean'],
        ]);

        return $validated + ['active' => false];
    }
}
