<?php

namespace App\Http\Controllers;

use App\Domains\Finance\Enums\FounderCapitalMovementType;
use App\Domains\Finance\Models\FounderCapitalMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FounderCapitalController extends Controller
{
    public function index(): View
    {
        $movements = FounderCapitalMovement::query()
            ->latest('movement_date')
            ->latest()
            ->paginate(15);

        $contributions = FounderCapitalMovement::query()
            ->where('type', FounderCapitalMovementType::Contribution->value)
            ->sum('amount');

        $reimbursements = FounderCapitalMovement::query()
            ->where('type', FounderCapitalMovementType::Reimbursement->value)
            ->sum('amount');

        $adjustments = FounderCapitalMovement::query()
            ->where('type', FounderCapitalMovementType::Adjustment->value)
            ->sum('amount');

        return view('finance.founder-capital.index', [
            'movements' => $movements,
            'types' => FounderCapitalMovementType::cases(),
            'summary' => [
                'contributions' => $contributions,
                'reimbursements' => $reimbursements,
                'adjustments' => $adjustments,
                'pending' => max(0, $contributions + $adjustments - $reimbursements),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        FounderCapitalMovement::query()->create($request->validate([
            'type' => ['required', 'string', 'in:contribution,reimbursement,adjustment'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'movement_date' => ['required', 'date'],
            'concept' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]));

        return redirect()->route('finance.founder-capital.index')->with('status', 'Movimiento registrado.');
    }
}
