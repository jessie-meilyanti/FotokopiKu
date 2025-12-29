# 🚀 Panduan Deploy FotokopiKu

## ✅ Checklist Sebelum Upload

- [ ] Export database dari phpMyAdmin (fotokopi_db)
- [ ] Backup folder `vendor/` (zip dulu)
- [ ] Test website di localhost (pastikan jalan)
- [ ] Catat semua kredensial database lokal

---

## 📦 File Yang Perlu Di-Upload

### ✅ WAJIB Upload:
```
✅ app/
✅ bootstrap/
✅ config/
✅ database/ (migrations, seeders, factories)
✅ public/
✅ resources/
✅ routes/
✅ storage/ (framework & app folders, kosongkan logs)
✅ .env (EDIT dulu sesuai hosting)
✅ artisan
✅ composer.json
✅ composer.lock
✅ .htaccess (root)
```

### ❌ JANGAN Upload:
```
❌ vendor/ (terlalu besar, install via composer di server)
❌ node_modules/ (tidak perlu)
❌ .git/ (tidak perlu)
❌ storage/logs/*.log (hapus log file)
❌ .env.example (gunakan .env saja)
❌ tests/ (optional, tidak perlu di production)
```

---

## 🗄️ Cara Export Database

**Via phpMyAdmin:**
1. Buka: `http://localhost/phpmyadmin`
2. Pilih database: `fotokopi_db`
3. Tab **Export**
4. Method: **Quick** / **Custom**
5. Format: **SQL**
6. ✅ Centang: "Add CREATE DATABASE"
7. Klik **Export**
8. Save file: `fotokopi_db.sql`

---

## 🌐 Deploy ke 000webhost

### Step 1: Setup Akun
1. Daftar: https://www.000webhost.com/
2. Verifikasi email
3. Create New Site (nama: fotokopiku)

### Step 2: Upload Files
**Opsi A: Via File Manager**
1. Login → File Manager
2. Masuk folder `public_html`
3. Hapus semua file default
4. Upload semua file Laravel (kecuali vendor/, node_modules/, .git/)
5. Pindahkan isi folder `public/` ke `public_html/`

**Opsi B: Via FTP**
1. Download FileZilla Client
2. Host: `files.000webhost.com`
3. Username & Password: dari 000webhost
4. Port: 21
5. Upload semua file

### Step 3: Setup Database
1. Dashboard → Database Manager
2. Create New Database
3. Database Name: `fotokopi_db`
4. **CATAT**: 
   - Database Name (biasanya: id12345_fotokopi_db)
   - Username (biasanya: id12345_dbuser)
   - Password
   - Host (biasanya: localhost)

### Step 4: Import Database
1. Database Manager → Manage → phpMyAdmin
2. Pilih database yang baru dibuat
3. Tab **Import**
4. Choose file: `fotokopi_db.sql`
5. Klik **Import**
6. Wait... Done! ✅

### Step 5: Update .env
Edit file `.env` di server:
```env
APP_NAME="FotokopiKu"
APP_ENV=production
APP_KEY=base64:... (generate via php artisan key:generate)
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
APP_URL=http://fotokopiku.000webhostapp.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=id12345_fotokopi_db
DB_USERNAME=id12345_dbuser
DB_PASSWORD=your_password_here

SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### Step 6: Fix Laravel Paths
Edit `public_html/index.php`:
```php
<?php
define('LARAVEL_START', microtime(true));

// Require autoloader (naik 1 level karena public dipindah)
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
```

### Step 7: Install Composer (Via SSH jika tersedia)
```bash
cd public_html
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Jika TIDAK ada SSH:**
1. Zip folder `vendor/` dari localhost
2. Upload via FTP
3. Extract di server

### Step 8: Set Permissions
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

Via File Manager: Klik kanan folder → Change Permission → 775

---

## 🎉 Testing

1. Buka: `http://fotokopiku.000webhostapp.com`
2. Test halaman:
   - ✅ Homepage
   - ✅ Login/Register
   - ✅ Katalog produk
   - ✅ Keranjang
   - ✅ Checkout
   - ✅ Admin dashboard

---

## 🔧 Troubleshooting

### Error: 500 Internal Server Error
**Solusi:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```
Cek file `.env` sudah benar semua

### Error: Connection Refused / Database Error
**Solusi:**
- Cek DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD di `.env`
- Pastikan database sudah di-import
- Test koneksi database via phpMyAdmin

### Error: Class Not Found
**Solusi:**
```bash
composer dump-autoload
php artisan clear-compiled
```

### Error: Storage Link Broken
**Solusi:**
```bash
php artisan storage:link
```

### Error: Permission Denied
**Solusi:**
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage
```

---

## 📱 Custom Domain (Optional)

**Gratis di Freenom:**
1. Buka: https://www.freenom.com/
2. Cari domain gratis (.tk, .ml, .ga, .cf, .gq)
3. Register & checkout (gratis 12 bulan)
4. Di 000webhost: Settings → Domain → Add Custom Domain
5. Update nameservers di Freenom ke 000webhost

---

## 🔒 Security Tips

1. **Selalu set** `APP_DEBUG=false` di production
2. **Jangan expose** `.env` file
3. **Gunakan HTTPS** jika memungkinkan
4. **Update Laravel** secara berkala
5. **Backup database** rutin

---

## 📞 Support

**000webhost:**
- Forum: https://forum.000webhost.com/
- Ticket: Login → Support

**Laravel:**
- Docs: https://laravel.com/docs/10.x
- Forum: https://laracasts.com/discuss

---

✅ **Deployment Complete!** Website live di: `http://fotokopiku.000webhostapp.com`
