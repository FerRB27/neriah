<?php

namespace App\Http\Controllers;

use App\Domains\People\Enums\PersonRole;
use App\Domains\People\Models\Person;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PeopleController extends Controller
{
    public function index(Request $request): View
    {
        $people = Person::query()
            ->with('roleAssignments')
            ->when($request->string('q')->toString(), function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('people.index', [
            'people' => $people,
            'search' => $request->string('q')->toString(),
        ]);
    }

    public function create(): View
    {
        return view('people.create', [
            'person' => new Person(['active' => true]),
            'roles' => PersonRole::cases(),
            'selectedRoles' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $person = Person::query()->create($this->validatedData($request));
        $this->syncRoles($person, $request->input('roles', []));

        return redirect()->route('people.index')->with('status', 'Persona creada.');
    }

    public function edit(Person $person): View
    {
        return view('people.edit', [
            'person' => $person->load('roleAssignments'),
            'roles' => PersonRole::cases(),
            'selectedRoles' => $person->roleAssignments->pluck('role.value')->all(),
        ]);
    }

    public function update(Request $request, Person $person): RedirectResponse
    {
        $person->update($this->validatedData($request));
        $this->syncRoles($person, $request->input('roles', []));

        return redirect()->route('people.index')->with('status', 'Persona actualizada.');
    }

    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'active' => ['nullable', 'boolean'],
            'roles' => ['array'],
            'roles.*' => [Rule::enum(PersonRole::class)],
        ]);

        unset($validated['roles']);

        return $validated + ['active' => false];
    }

    private function syncRoles(Person $person, array $roles): void
    {
        $roleValues = collect($roles)
            ->filter(fn (string $role): bool => PersonRole::tryFrom($role) !== null)
            ->unique()
            ->values();

        $person->roleAssignments()
            ->whereNotIn('role', $roleValues->all())
            ->delete();

        $roleValues->each(fn (string $role): mixed => $person->roleAssignments()->updateOrCreate(['role' => $role]));
    }
}
