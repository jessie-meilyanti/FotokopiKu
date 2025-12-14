<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@fotokopi.com',
            'password' => 'password',
            'is_admin' => true,
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Pelanggan',
            'email' => 'user@fotokopi.com',
            'password' => 'password',
        ]);

        $this->call(CatalogSeeder::class);
        $this->call(\Database\Seeders\OrderSeeder::class);
    }
}
