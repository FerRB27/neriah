<?php

namespace App\Domains\Commissions\Models;

use App\Domains\Commissions\Enums\CommissionStatus;
use App\Domains\People\Models\Person;
use App\Domains\Sales\Models\Sale;
use App\Domains\Sales\Models\SaleLine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionEntry extends Model
{
    protected $fillable = [
        'sale_id',
        'sale_line_id',
        'person_id',
        'type',
        'amount',
        'status',
        'earned_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => CommissionStatus::class,
            'earned_at' => 'date',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function saleLine(): BelongsTo
    {
        return $this->belongsTo(SaleLine::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
