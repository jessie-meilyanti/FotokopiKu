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
        // Data statis agar foto produk konsisten sesuai nama produk
        // Menggunakan path lokal untuk memastikan foto selalu muncul seperti hero image
        $catalog = [
            'Kertas & Print' => [
                [
                    'name' => 'Kertas HVS A4 80gr (500 lembar)',
                    'price' => 52000,
                    'description' => 'Kertas HVS putih bersih untuk print hitam/warna, isi 1 rim.',
                    'thumbnail' => '/images/kertasHVSA480gr(500lembar).jpg',
                ],
                [
                    'name' => 'Kertas Art Paper A4 150gr (100 lembar)',
                    'price' => 75000,
                    'description' => 'Cocok untuk brosur, pamflet, dan undangan warna berkualitas.',
                    'thumbnail' => '/images/kertasArtPaperA4150gr(100lembar).jpg',
                ],
                [
                    'name' => 'Isi Ulang Tinta Printer Canon PG-47 Hitam',
                    'price' => 125000,
                    'description' => 'Memberikan hasil cetak yang pekat, cepat kering, dan tahan lama tanpa menyumbat kepala cetak (printhead).',
                    'thumbnail' => '/images/isiUlangTintaPrinterCanonPG-47(Black).png',
                ],
                [
                    'name' => 'Toner Printer LaserJet HP 85A (CF285A) Original Hitam',
                    'price' => 1500000,
                    'description' => 'Menghasilkan cetakan dokumen yang sangat tajam, tanpa noda, dan tahan terhadap air dan luntur.',
                    'thumbnail' => '/images/tonerPrinterLaserJetHP85A(CF285A)Original-Hitam.jpg',
                ],
            ],
            'Alat Tulis' => [
                [
                    'name' => 'Pulpen Gel 0.5mm Hitam',
                    'price' => 6000,
                    'description' => 'Pulpen gel halus untuk menulis harian dan tanda tangan.',
                    'thumbnail' => '/images/pulpenGel0.5mmHitam.jpg',
                ],
                [
                    'name' => 'Pensil 2B + Penghapus',
                    'price' => 4000,
                    'description' => 'Pensil 2B untuk ujian dan sketsa dilengkapi penghapus.',
                    'thumbnail' => '/images/pensilpenghapus.jpg',
                ],
                [
                    'name' => 'Spidol Whiteboard Hitam',
                    'price' => 9000,
                    'description' => 'Spidol whiteboard low odor, tinta pekat, mudah dihapus.',
                    'thumbnail' => '/images/spidolWhiteboardHitam.png',
                ],
                [
                    'name' => 'Penggaris 30cm Anti Slip',
                    'price' => 7000,
                    'description' => 'Penggaris akrilik transparan dengan grip anti slip.',
                    'thumbnail' => '/images/penggaris30cmAntiSlip.jpg',
                ],
            ],
            'ATK Kantor' => [
                [
                    'name' => 'Map Snelhecter Folio',
                    'price' => 3500,
                    'description' => 'Map snelhecter plastik tebal untuk menyimpan dokumen folio.',
                    'thumbnail' => '/images/mapSnelhecterFolio.jpg',
                ],
                [
                    'name' => 'Ordner Folio 7cm',
                    'price' => 24000,
                    'description' => 'Ordner besar dengan tuas besi kuat, muat ratusan lembar.',
                    'thumbnail' => '/images/ordnerFolio7cm.jpg',
                ],
                [
                    'name' => 'Cutter Besar + Refill',
                    'price' => 12000,
                    'description' => 'Cutter ukuran besar dengan isi cadangan.',
                    'thumbnail' => '/images/cutterBesarRefill.jpg',
                ],
                [
                    'name' => 'Isi Staples No.10 (1000 pcs)',
                    'price' => 8000,
                    'description' => 'Isi staples ukuran No.10, kemasan 1000 pcs.',
                    'thumbnail' => '/images/isiStaplesNo.10(1000pcs).jpg',
                ],
            ],
            'Jasa Laminating' => [
                [
                    'name' => 'Laminating Kartu',
                    'price' => 5000,
                    'description' => 'Laminating kartu nama dan tiket.',
                    'thumbnail' => '/images/laminatingKartu.jpg',
                ],
                [
                    'name' => 'Laminating A4',
                    'price' => 8000,
                    'description' => 'Laminating dokumen ukuran A4, hasil rapi anti air.',
                    'thumbnail' => '/images/laminatingA4.jpg',
                ],
                [
                    'name' => 'Laminating A3',
                    'price' => 12000,
                    'description' => 'Laminating ukuran A3 untuk poster atau sertifikat besar.',
                    'thumbnail' => '/images/laminatingA3.jpg',
                ],
                [
                    'name' => 'Laminating Sertifikat Premium',
                    'price' => 15000,
                    'description' => 'Laminating sertifikat dengan plastik tebal anti-kusut.',
                    'thumbnail' => '/images/laminatingSertif.jpg',
                ],
            ],
            'Jasa Jilid' => [
                [
                    'name' => 'Jilid Spiral A4',
                    'price' => 12000,
                    'description' => 'Jilid spiral plastik untuk laporan dan makalah (sampai 200 lembar).',
                    'thumbnail' => '/images/jilidSpiralA4.jpg',
                ],
                [
                    'name' => 'Jilid Lakban A4',
                    'price' => 8000,
                    'description' => 'Jilid lakban sederhana, rapi untuk tugas kuliah.',
                    'thumbnail' => '/images/jilidLakbanA4.jpg',
                ],
                [
                    'name' => 'Jilid Hardcover Skripsi',
                    'price' => 48000,
                    'description' => 'Jilid hardcover khusus skripsi/tesis, termasuk emboss tulisan emas.',
                    'thumbnail' => '/images/jilidHardcover.jpg',
                ],
                [
                    'name' => 'Jilid Klip Cepat',
                    'price' => 6000,
                    'description' => 'Jilid klip tanpa perforasi, cocok untuk dokumen sementara.',
                    'thumbnail' => '/images/jilidKlip.jpg',
                ],
            ],
        ];

        foreach ($catalog as $categoryName => $products) {
            $category = Category::updateOrCreate(
                ['slug' => Str::slug($categoryName)],
                [
                    'name' => $categoryName,
                    'description' => $categoryName,
                ]
            );

            foreach ($products as $product) {
                Product::updateOrCreate(
                    ['slug' => Str::slug($product['name'])],
                    [
                        'name' => $product['name'],
                        'description' => $product['description'],
                        'thumbnail' => $product['thumbnail'],
                        'price' => $product['price'],
                        'stock' => 100,
                        'is_service' => str_contains($categoryName, 'Jasa'),
                        'category_id' => $category->id,
                    ]
                );
            }
        }
    }
}

