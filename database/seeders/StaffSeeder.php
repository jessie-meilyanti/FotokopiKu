<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staff = [
            [
                'staff_id' => '10240015',
                'name' => 'Jessie Meilyanti',
                'email' => 'jessie@fotokopi.com',
                'role' => 'owner',
                'position' => 'Owner & Manager',
                'salary' => 0,
                'phone' => '085810101576',
                'address' => 'Jl. Boulevard Ahmad Yani, Summarecon Bekasi, Bekasi',
                'photo' => '/images/user-placeholder.svg'
            ],
            [
                'staff_id' => '10240080',
                'name' => 'Tasya Azkya Zahra',
                'email' => 'tasya@fotokopi.com',
                'role' => 'admin',
                'position' => 'Admin & Customer Service',
                'salary' => 0,
                'phone' => '081287906751',
                'address' => 'Jl. Bulevard Raya No. 45, Kelapa Gading, Jakarta Utara',
                'photo' => '/images/user-placeholder.svg'
            ],
            [
                'staff_id' => '10240050',
                'name' => 'Nadela Ayu Putri',
                'email' => 'nadela@fotokopi.com',
                'role' => 'staff',
                'position' => 'Staff Operasional',
                'salary' => 0,
                'phone' => '081315658371',
                'address' => 'Jl. Margonda Raya No. 88, Depok, Jawa Barat',
                'photo' => '/images/user-placeholder.svg'
            ],
            [
                'staff_id' => '10240066',
                'name' => 'Sri Sulistyowati',
                'email' => 'sulis@fotokopi.com',
                'role' => 'staff',
                'position' => 'Staff Produksi',
                'salary' => 0,
                'phone' => '08139423463',
                'address' => 'Jl. Bintaro Utama No. 23, Tangerang Selatan, Banten',
                'photo' => '/images/user-placeholder.svg'
            ],
            [
                'staff_id' => '10240001',
                'name' => 'Eka Putra Effendi',
                'email' => 'eka@fotokopi.com',
                'role' => 'courier',
                'position' => 'Kurir Pengiriman',
                'salary' => 0,
                'phone' => '081310430641',
                'address' => 'Jl. Raya Pajajaran No. 67, Bogor, Jawa Barat',
                'photo' => '/images/user-placeholder.svg'
            ],
        ];

        foreach ($staff as $s) {
            $user = \App\Models\User::create([
                'name' => $s['name'],
                'email' => $s['email'],
                'password' => bcrypt('password'),
            ]);

            \App\Models\Staff::create([
                'user_id' => $user->id,
                'staff_id' => $s['staff_id'],
                'role' => $s['role'],
                'position' => $s['position'],
                'salary' => $s['salary'],
                'phone' => $s['phone'],
                'photo' => $s['photo'],
                'notes' => $s['address'] ?? null,
            ]);
        }
    }
}
