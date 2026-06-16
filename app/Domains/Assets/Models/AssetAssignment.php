<?php

namespace App\Domains\Assets\Models;

use App\Domains\People\Models\Person;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetAssignment extends Model
{
    protected $fillable = [
        'business_asset_id',
        'person_id',
        'assigned_at',
        'returned_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'date',
            'returned_at' => 'date',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(BusinessAsset::class, 'business_asset_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
