<?php

namespace App\Domains\Payments\Models;

use App\Domains\Payments\Enums\PaymentStatus;
use App\Domains\People\Models\Person;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'person_id',
        'amount',
        'concept',
        'status',
        'week_start',
        'week_end',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => PaymentStatus::class,
            'week_start' => 'date',
            'week_end' => 'date',
            'paid_at' => 'date',
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
