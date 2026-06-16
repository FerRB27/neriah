<?php

namespace App\Domains\Sales\Models;

use App\Domains\Products\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promotion extends Model
{
    protected $fillable = [
        'product_variant_id',
        'name',
        'promotional_price',
        'starts_at',
        'ends_at',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'promotional_price' => 'decimal:2',
            'starts_at' => 'date',
            'ends_at' => 'date',
            'active' => 'boolean',
        ];
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
