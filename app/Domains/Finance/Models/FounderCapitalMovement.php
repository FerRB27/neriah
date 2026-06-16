<?php

namespace App\Domains\Finance\Models;

use App\Domains\Finance\Enums\FounderCapitalMovementType;
use Illuminate\Database\Eloquent\Model;

class FounderCapitalMovement extends Model
{
    protected $fillable = [
        'type',
        'amount',
        'movement_date',
        'concept',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => FounderCapitalMovementType::class,
            'amount' => 'decimal:2',
            'movement_date' => 'date',
        ];
    }
}
