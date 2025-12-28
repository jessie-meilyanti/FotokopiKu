# 📋 FotokopiKu - Panduan Setup Lengkap

> **Website fotokopi online yang aesthetic dan user-friendly banget!** ✨

Halo bestie! 👋 Welcome to FotokopiKu - platform fotokopi digital yang bikin hidup mahasiswa jadi lebih mudah. Gaskan setup projectnya dengan mengikuti step-by-step guide ini ya!

---

## 🎯 Apa Itu FotokopiKu?

FotokopiKu adalah website e-commerce modern untuk layanan fotokopi yang dibangun dengan tech stack kekinian:
- **Laravel 8.1** - Backend framework yang powerful buat handle semua logic bisnis
- **Tailwind CSS** - Styling yang aesthetic dan responsive tanpa ribet
- **Alpine.js** - Interaktivitas JavaScript yang ringan dan smooth
- **Vite** - Build tool super cepat buat compile asset
- **MySQL** - Database yang reliable buat nyimpen semua data

### ✨ Fitur Unggulan
- 🛒 Shopping cart dengan real-time quantity control
- 💳 Pembayaran COD & QRIS dengan upload bukti transfer
- 📦 Tracking pesanan real-time
- 👥 Role management (User, Staff, Admin, Owner, Courier)
- 🚚 Pengiriman dengan calculate ongkir Jabodetabek
- 📊 Dashboard admin untuk manage orders & staff
- 📱 Fully responsive design

---

## 🚀 Persiapan Sebelum Mulai

Sebelum lanjut, pastikan laptop/PC kamu udah ada software-software ini ya:

### 1️⃣ **Laragon Full** (Windows) atau XAMPP/MAMP
📥 **Download:** [laragon.org/download](https://laragon.org/download/)
- **Versi:** Laragon Full 6.0 atau yang lebih baru
- **Kenapa?** Laragon udah include semua yang kita butuhin: PHP, MySQL, Apache, dan terminal yang mudah dipakai
- **Include:** PHP 8.1+, MySQL 5.7+, Apache, Composer

**Alternatif untuk Mac/Linux:**
- XAMPP: [apachefriends.org](https://www.apachefriends.org/)
- MAMP: [mamp.info](https://www.mamp.info/)

### 2️⃣ **PHP** (Jika belum ada di Laragon)
📌 **Versi minimum:** PHP 8.0 atau lebih tinggi
- **Kenapa PHP 8.0+?** Laravel 8.1 butuh minimal PHP 7.3, tapi PHP 8+ lebih stabil dan punya fitur modern
- **Cek versi:** Buka terminal, ketik `php -v`

**Extensions PHP yang dibutuhkan** (biasanya udah include di Laragon):
- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath

### 3️⃣ **Composer**
📥 **Download:** [getcomposer.org](https://getcomposer.org/)
- **Versi:** Composer 2.0+
- **Kenapa?** Composer adalah package manager buat PHP, basically npm-nya PHP world
- **Cek versi:** Ketik `composer -v` di terminal

### 4️⃣ **Node.js & npm**
📥 **Download:** [nodejs.org](https://nodejs.org/)
- **Versi:** Node.js 16+ (LTS recommended)
- **Kenapa?** Buat compile asset frontend (CSS & JS) pakai Vite
- **Cek versi:** Ketik `node -v` dan `npm -v` di terminal

### 5️⃣ **Git** (Optional tapi recommended)
📥 **Download:** [git-scm.com](https://git-scm.com/)
- **Kenapa?** Buat version control dan clone project kalo dari repository
- **Cek versi:** Ketik `git --version`

---

## 📦 Step 1: Download & Extract Project

### Cara 1: Download ZIP
1. Download file `fotokopi1.zip` (pastiin udah dapet dari dosen/source)
2. Extract ke folder `laragon/www/` atau `htdocs/` (kalo pake XAMPP)
   ```
   C:\laragon\www\fotokopi1
   ```
# 📦 FotokopiKu — Panduan Lengkap (Bahasa Indonesia)

Website layanan fotokopi modern berbasis Laravel. Dokumen ini menjelaskan cara men-setup di komputer lain, versi software yang dibutuhkan, serta cara menjaga agar data tidak hilang.

## Ringkasan Versi Proyek

- Framework: Laravel 10.x (`laravel/framework` ^10.10)
- PHP: 8.1+
- Composer: 2.x
- Database: MySQL 5.7+ atau MariaDB 10.3+
- Frontend Build: Vite ^5.0.0
- Tailwind CSS: ^3.4.19
- Alpine.js: ^3.x
- Axios: ^1.6.4
- PDF: `barryvdh/laravel-dompdf` ^3.1

Rekomendasi Windows: Laragon Full 6.x (sudah termasuk PHP, MySQL, Composer). Alternatif: XAMPP.

## Persyaratan Sistem

- PHP 8.1 atau lebih baru
- Composer 2.x
- Node.js 18.x LTS atau lebih baru (untuk Vite 5)
- MySQL 5.7+ atau MariaDB 10.3+
- Git (opsional)

## Instalasi di Komputer Baru

1. Ekstrak folder `fotokopi1` ke `C:\laragon\www\fotokopi1` (atau htdocs jika XAMPP).
2. Buka terminal di folder proyek, jalankan:
   - `composer install`
   - `npm install`
3. Buat file `.env` dari template dan generate app key:
   - Salin: `copy .env.example .env`
   - `php artisan key:generate`
4. Edit `.env` dan sesuaikan konfigurasi database:
   ```env
   APP_NAME=FotokopiKu
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://localhost

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=fotokopi_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```
5. Buat database baru `fotokopi_db` via phpMyAdmin atau CLI.
6. Migrasi tabel dan (opsional) isi data dummy:
   - `php artisan migrate`
   - `php artisan db:seed` (opsional, hanya untuk development)
7. Buat symlink storage agar gambar bisa diakses:
   - `php artisan storage:link`
8. Jalankan server dan builder aset:
   - Terminal 1: `php artisan serve`
   - Terminal 2: `npm run dev`

## Akun Default (Jika Seeding)

- Admin: `admin@fotokopi.com` / `password`
- User: `user@fotokopi.com` / `password`
- Semua akun contoh menggunakan password: `password`

## Menjaga Data Agar Tidak Hilang

Data yang perlu dijaga:
- Database (tabel users, products, orders, dll.)
- File upload di `storage/app/public` (bukti transfer, dll.)
- Gambar produk di `public/images`

Hal yang harus dihindari pada lingkungan yang menyimpan data asli:
- Jangan menggunakan `php artisan migrate:fresh` atau `db:wipe` (ini menghapus semua tabel)
- Gunakan `php artisan migrate` biasa untuk upgrade skema database
- Jalankan seeder hanya di development. Seeder tidak menghapus data, namun menambah data dummy.

### Backup Otomatis (Windows PowerShell)

Kami menyertakan script backup siap pakai: `scripts/backup.ps1`.

Apa yang dilakukan script:
- Mengekspor database ke file SQL (`backups/db-YYYYMMDD-HHMMSS.sql`)
- Mengarsipkan folder penting ke ZIP (`backups/files-YYYYMMDD-HHMMSS.zip`), termasuk `public/images` dan `storage/app/public`

Cara menjalankan:

```powershell
# Dari folder proyek
.\n+.\n+.
PS> .\scripts\backup.ps1
```

Catatan:
- Script mencoba mendeteksi `mysqldump` dari PATH atau Laragon (`C:\laragon\bin\mysql\...\bin\mysqldump.exe`).
- Pastikan `.env` berisi `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

### Restore Singkat

1. Import file SQL ke MySQL:
   ```bash
   mysql -h HOST -P PORT -u USER -p < backups/db-YYYYMMDD-HHMMSS.sql
   ```
2. Extract ZIP ke folder proyek, pastikan struktur `public/images` dan `storage/app/public` kembali.
3. Jalankan `php artisan storage:link` bila perlu.

## Kompilasi Aset

- Development: `npm run dev` (hot reload)
- Production: `npm run build` (minified, siap deploy)

## Troubleshooting Cepat

- CSS/JS tidak tampil: jalankan `npm run dev` atau `npm run build`, lalu `php artisan view:clear`
- Gambar upload tidak muncul: `php artisan storage:link`
- Error kunci aplikasi: `php artisan key:generate`
- Masalah koneksi DB: cek kredensial di `.env` dan status MySQL

## Catatan Versi & Ketergantungan

Mengacu pada `composer.json` dan `package.json`:
- `laravel/framework` ^10.10 (Laravel 10)
- `barryvdh/laravel-dompdf` ^3.1
- `laravel/sanctum` ^3.3
- `phpunit/phpunit` ^10.1
- `vite` ^5.0.0
- `tailwindcss` ^3.4.19
- `alpinejs` ^3.4.2
- `axios` ^1.6.4

Pastikan Node.js 18+ agar kompatibel dengan Vite 5.

## Rekomendasi Workflow Aman (Agar Data Tetap Tersusun)

- Selalu backup sebelum update dependency atau menjalankan migrasi besar
- Hindari perintah destruktif di lingkungan berdata asli (`migrate:fresh`, `db:wipe`)
- Simpan gambar produk di `public/images` dan file upload di `storage/app/public`
- Gunakan seeder untuk development saja
- Dokumentasikan perubahan skema/migrasi di tim

## Kontak & Dukungan

- Dokumentasi Laravel 10: https://laravel.com/docs/10.x
- Tailwind CSS: https://tailwindcss.com/docs
- Vite: https://vitejs.dev/guide/

Selamat menggunakan FotokopiKu! Dengan backup rutin dan konfigurasi `.env` yang benar, data Anda akan aman dan tetap rapi seperti sekarang. 🙌
Website FotokopiKu dirancang berdasarkan observasi langsung di toko fotokopi nyata untuk mengatasi masalah berikut:

**Problem Customer:**
1. **Antrian Panjang** – Saat ramai, customer harus menunggu lama hanya untuk pesan print/fotokopi.
2. **Tidak Tahu Harga Pasti** – Harga sering tidak jelas, terutama untuk jasa laminating/jilid.
3. **Pengiriman Manual** – Customer harus datang ambil sendiri atau nego ongkir tidak standar.

**Problem Owner:**
1. **Order Manual** – Catat pesanan di buku/kertas, rawan salah atau hilang.
2. **Stok Tidak Terpantau** – Stok ATK sering kosong mendadak karena tidak tercatat real-time.
3. **Pembayaran Ribet** – Sering customer lupa bayar atau tidak bawa uang pas.

**Solusi FotokopiKu:**
- ✅ **Order Online** – Customer pesan kapan saja, tanpa antri. Owner bisa siapkan pesanan sebelum customer datang.
- ✅ **Harga Transparan** – Semua produk/jasa tampil harga jelas di website.
- ✅ **Ongkir Otomatis** – Sistem hitung ongkir berdasarkan kota, standar dan adil.
- ✅ **Payment Fleksibel** – COD untuk yang mau bayar di tempat, QRIS untuk transfer langsung.
- ✅ **Tracking Pesanan** – Customer tahu status pesanan real-time (processing, shipped, delivered).
- ✅ **Admin Panel** – Owner kelola stok, pesanan, dan penjualan dengan mudah. Validasi bukti bayar QRIS otomatis.

**Catatan:** Minimal 10 item untuk kirim-antar agar ongkir sebanding dengan biaya operasional. Owner bisa adjust logic ini di `CheckoutController.php` jika perlu.

---

**Terima kasih telah menggunakan FotokopiKu! Semoga panduan ini membantu. Selamat menjalankan website! 🎉**
