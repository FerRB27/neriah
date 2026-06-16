<?php

namespace App\Domains\Customers\Models;

use App\Domains\Sales\Models\Sale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'city',
        'address',
        'notes',
        'first_purchase_date',
        'last_purchase_date',
        'total_purchased',
        'orders_count',
    ];

    protected function casts(): array
    {
        return [
            'first_purchase_date' => 'date',
            'last_purchase_date' => 'date',
            'total_purchased' => 'decimal:2',
            'orders_count' => 'integer',
        ];
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
