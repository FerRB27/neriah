<?php

namespace App\Domains\People\Models;

use App\Domains\People\Enums\PersonRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonRoleAssignment extends Model
{
    protected $fillable = [
        'person_id',
        'role',
    ];

    protected function casts(): array
    {
        return [
            'role' => PersonRole::class,
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
