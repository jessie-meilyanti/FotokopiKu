<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Kertas & Print',
            'Alat Tulis',
            'ATK Kantor',
            'Jasa Laminating',
            'Jasa Jilid',
        ];

        foreach ($categories as $cat) {
            $category = Category::create([
                'name' => $cat,
                'slug' => Str::slug($cat),
                'description' => $cat,
            ]);

            Product::factory()
                ->count(4)
                ->create([
                    'category_id' => $category->id,
                    'is_service' => str_contains($cat, 'Jasa'),
                ]);
        }
    }
}

