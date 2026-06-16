<?php

namespace Database\Seeders;

use App\Domains\Finance\Enums\FounderCapitalMovementType;
use App\Domains\Finance\Models\FounderCapitalMovement;
use App\Domains\Inventory\Models\InventoryItem;
use App\Domains\Products\Models\Input;
use App\Domains\Products\Models\InputCategory;
use App\Domains\Products\Models\Product;
use App\Domains\Products\Models\ProductCategory;
use App\Domains\Products\Models\ProductVariant;
use App\Domains\Recipes\Models\Recipe;
use App\Domains\Sales\Models\Promotion;
use App\Domains\Sales\Models\SalesChannel;
use Illuminate\Database\Seeder;

class BusinessCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $inputCategories = collect([
            'Base',
            'Aroma',
            'Aceite Esencial',
            'Colorante',
            'Consumible',
            'Empaque',
            'Etiqueta',
        ])->mapWithKeys(fn (string $name) => [
            $name => InputCategory::query()->updateOrCreate(['name' => $name], ['active' => true]),
        ]);

        $inputs = [
            ['category' => 'Base', 'name' => 'Glicerina cristal', 'sku' => 'INS-GLIC-CRISTAL', 'unit' => 'kg', 'minimum' => 2],
            ['category' => 'Aroma', 'name' => 'Aroma melon', 'sku' => 'INS-AROMA-MELON', 'unit' => 'ml', 'minimum' => 100],
            ['category' => 'Aceite Esencial', 'name' => 'Aceite de coco', 'sku' => 'INS-ACEITE-COCO', 'unit' => 'ml', 'minimum' => 100],
            ['category' => 'Colorante', 'name' => 'Colorante verde', 'sku' => 'INS-COLOR-VERDE', 'unit' => 'ml', 'minimum' => 30],
            ['category' => 'Empaque', 'name' => 'Caja para jabon', 'sku' => 'INS-CAJA-JABON', 'unit' => 'unidad', 'minimum' => 25],
            ['category' => 'Etiqueta', 'name' => 'Etiqueta Neriah', 'sku' => 'INS-ETQ-NERIAH', 'unit' => 'unidad', 'minimum' => 25],
        ];

        foreach ($inputs as $inputData) {
            $input = Input::query()->updateOrCreate(
                [
                    'input_category_id' => $inputCategories[$inputData['category']]->id,
                    'name' => $inputData['name'],
                ],
                [
                    'unit' => $inputData['unit'],
                    'minimum_stock' => $inputData['minimum'],
                    'active' => true,
                ],
            );

            InventoryItem::query()->updateOrCreate(
                ['sku' => $inputData['sku']],
                [
                    'input_id' => $input->id,
                    'product_variant_id' => null,
                    'name' => $input->name,
                    'item_type' => 'input',
                    'unit' => $input->unit,
                    'minimum_stock' => $input->minimum_stock,
                    'active' => true,
                ],
            );
        }

        $category = ProductCategory::query()->updateOrCreate(
            ['name' => 'Jabones artesanales'],
            ['active' => true],
        );

        $individual = Product::query()->updateOrCreate(
            ['name' => 'Jabon Individual 85g'],
            [
                'product_category_id' => $category->id,
                'standard_cost' => 1.24,
                'base_price' => 2.50,
                'commission_amount' => 0.25,
                'maker_payment_amount' => 0.35,
                'active' => true,
            ],
        );

        $individualVariant = ProductVariant::query()->updateOrCreate(
            ['sku' => 'JAB-MELON-85G'],
            [
                'product_id' => $individual->id,
                'name' => 'Melon 85g',
                'unit_label' => 'unidad',
                'units_per_variant' => 1,
                'weight_grams' => 85,
                'price' => 2.50,
                'active' => true,
            ],
        );

        InventoryItem::query()->updateOrCreate(
            ['sku' => 'PT-JAB-MELON-85G'],
            [
                'input_id' => null,
                'product_variant_id' => $individualVariant->id,
                'name' => 'Jabon Melon 85g terminado',
                'item_type' => 'finished_good',
                'unit' => 'unidad',
                'minimum_stock' => 10,
                'active' => true,
            ],
        );

        $pack = Product::query()->updateOrCreate(
            ['name' => 'Pack 3 x 85g'],
            [
                'product_category_id' => $category->id,
                'standard_cost' => 3.72,
                'base_price' => 7.00,
                'commission_amount' => 0.60,
                'maker_payment_amount' => 1.05,
                'active' => true,
            ],
        );

        $packVariant = ProductVariant::query()->updateOrCreate(
            ['sku' => 'PACK-3X85G'],
            [
                'product_id' => $pack->id,
                'name' => 'Pack 3 jabones 85g',
                'unit_label' => 'pack',
                'units_per_variant' => 3,
                'weight_grams' => 255,
                'price' => 7.00,
                'active' => true,
            ],
        );

        InventoryItem::query()->updateOrCreate(
            ['sku' => 'PT-PACK-3X85G'],
            [
                'input_id' => null,
                'product_variant_id' => $packVariant->id,
                'name' => 'Pack 3 jabones terminado',
                'item_type' => 'finished_good',
                'unit' => 'pack',
                'minimum_stock' => 5,
                'active' => true,
            ],
        );

        $recipe = Recipe::query()->updateOrCreate(
            ['product_variant_id' => $individualVariant->id, 'name' => 'Formula Melon 85g'],
            ['expected_yield' => 10, 'yield_unit' => 'unidad', 'active' => true],
        );

        foreach ([
            'INS-GLIC-CRISTAL' => ['quantity' => 0.85, 'unit' => 'kg'],
            'INS-AROMA-MELON' => ['quantity' => 20, 'unit' => 'ml'],
            'INS-ACEITE-COCO' => ['quantity' => 15, 'unit' => 'ml'],
            'INS-COLOR-VERDE' => ['quantity' => 5, 'unit' => 'ml'],
            'INS-CAJA-JABON' => ['quantity' => 10, 'unit' => 'unidad'],
            'INS-ETQ-NERIAH' => ['quantity' => 10, 'unit' => 'unidad'],
        ] as $sku => $ingredient) {
            $item = InventoryItem::query()->where('sku', $sku)->firstOrFail();
            $recipe->ingredients()->updateOrCreate(
                ['inventory_item_id' => $item->id],
                ['quantity' => $ingredient['quantity'], 'unit' => $ingredient['unit']],
            );
        }

        foreach (['WhatsApp', 'Tienda', 'Distribuidor', 'Feria'] as $channel) {
            SalesChannel::query()->updateOrCreate(['name' => $channel], ['active' => true]);
        }

        Promotion::query()->updateOrCreate(
            ['name' => 'Promocion inicial Melon 85g'],
            [
                'product_variant_id' => $individualVariant->id,
                'promotional_price' => 2.25,
                'starts_at' => now()->toDateString(),
                'ends_at' => now()->addDays(30)->toDateString(),
                'active' => true,
            ],
        );

        FounderCapitalMovement::query()->updateOrCreate(
            ['concept' => 'Capital inicial del fundador'],
            [
                'type' => FounderCapitalMovementType::Contribution,
                'amount' => 200,
                'movement_date' => now()->toDateString(),
                'notes' => 'Saldo inicial aproximado definido para Neriah ERP V1.0',
            ],
        );
    }
}
