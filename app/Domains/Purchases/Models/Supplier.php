<?php

namespace App\Domains\Purchases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }
}
