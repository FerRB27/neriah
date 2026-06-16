<?php

namespace App\Domains\Products\Models;

use App\Domains\Inventory\Models\InventoryItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Input extends Model
{
    protected $fillable = [
        'input_category_id',
        'name',
        'unit',
        'minimum_stock',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'minimum_stock' => 'decimal:4',
            'active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(InputCategory::class, 'input_category_id');
    }

    public function inventoryItem(): HasOne
    {
        return $this->hasOne(InventoryItem::class);
    }
}
