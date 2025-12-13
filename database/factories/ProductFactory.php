<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->words(3, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name . '-' . $this->faker->unique()->numberBetween(1, 9999)),
            'description' => $this->faker->sentence(),
            'thumbnail' => 'https://via.placeholder.com/600x400?text=Produk',
            'price' => $this->faker->randomFloat(2, 5000, 150000),
            'stock' => $this->faker->numberBetween(0, 200),
            'is_service' => false,
        ];
    }
}

