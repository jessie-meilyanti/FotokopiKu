# FotokopiKu Backup Script (Windows PowerShell)
# Membuat backup database + file penting agar data tidak hilang
# Hasil backup akan disimpan ke folder ./backups

# ------------------------------
# Konfigurasi & Utilitas
# ------------------------------
$ErrorActionPreference = 'Stop'

function Read-Env($path) {
    $envMap = @{}
    if (-Not (Test-Path $path)) { return $envMap }
    Get-Content -Path $path | ForEach-Object {
        $line = $_.Trim()
        if ($line -eq '' -or $line.StartsWith('#')) { return }
        $kv = $line -split '=', 2
        if ($kv.Length -eq 2) { $envMap[$kv[0]] = $kv[1] }
    }
    return $envMap
}

function Find-MySqlDump() {
    # 1) Coba di PATH
    $cmd = Get-Command mysqldump -ErrorAction SilentlyContinue
    if ($cmd) { return $cmd.Path }

    # 2) Cari di Laragon (default instalasi)
    $laragonBin = 'C:\laragon\bin\mysql'
    if (Test-Path $laragonBin) {
        $dump = Get-ChildItem -Path $laragonBin -Recurse -Filter mysqldump.exe -ErrorAction SilentlyContinue | Select-Object -First 1
        if ($dump) { return $dump.FullName }
    }

    throw "mysqldump tidak ditemukan. Tambahkan ke PATH atau install Laragon/XAMPP."
}

# ------------------------------
# Persiapan
# ------------------------------
$root = Split-Path -Parent $MyInvocation.MyCommand.Path
$projectRoot = Split-Path -Parent $root  # ../ (scripts berada di dalam project)
Set-Location $projectRoot

$env = Read-Env "$projectRoot/.env"
if (-Not $env.ContainsKey('DB_DATABASE')) { Write-Warning 'File .env belum diset atau DB_* belum lengkap. Lanjut, tapi backup DB akan dilewati.' }

$DB_HOST = $env['DB_HOST']
$DB_PORT = $env['DB_PORT']
$DB_NAME = $env['DB_DATABASE']
$DB_USER = $env['DB_USERNAME']
$DB_PASS = $env['DB_PASSWORD']

$timestamp = Get-Date -Format 'yyyyMMdd-HHmmss'
$backupDir = Join-Path $projectRoot 'backups'
if (-Not (Test-Path $backupDir)) { New-Item -ItemType Directory -Path $backupDir | Out-Null }

# ------------------------------
# Backup Database (mysqldump)
# ------------------------------
$dbBackupPath = Join-Path $backupDir ("db-$timestamp.sql")
try {
    if ($DB_NAME -and $DB_USER) {
        $mysqldump = Find-MySqlDump
        Write-Host "➜ Menjalankan mysqldump untuk database '$DB_NAME'..." -ForegroundColor Cyan
        & $mysqldump -h $DB_HOST -P $DB_PORT -u $DB_USER --password=$DB_PASS --routines --triggers --single-transaction --databases $DB_NAME | Out-File -FilePath $dbBackupPath -Encoding utf8
        Write-Host "✔ Backup DB tersimpan: $dbBackupPath" -ForegroundColor Green
    } else {
        Write-Warning 'Lewati backup DB: DB_DATABASE/DB_USERNAME tidak ditemukan di .env'
    }
} catch {
    Write-Warning "Gagal backup DB: $($_.Exception.Message)"
}

# ------------------------------
# Backup File Penting (images, storage)
# ------------------------------
$zipBackupPath = Join-Path $backupDir ("files-$timestamp.zip")
$itemsToZip = @()

$publicImages = Join-Path $projectRoot 'public/images'
if (Test-Path $publicImages) { $itemsToZip += $publicImages }

$storagePublic = Join-Path $projectRoot 'storage/app/public'
if (Test-Path $storagePublic) { $itemsToZip += $storagePublic }

$extraFiles = @(
    (Join-Path $projectRoot 'composer.json'),
    (Join-Path $projectRoot 'package.json'),
    (Join-Path $projectRoot 'vite.config.js'),
    (Join-Path $projectRoot 'tailwind.config.js'),
    (Join-Path $projectRoot 'postcss.config.js')
)
foreach ($f in $extraFiles) { if (Test-Path $f) { $itemsToZip += $f } }

if ($itemsToZip.Count -gt 0) {
    Write-Host "➜ Mengarsipkan file penting ke ZIP..." -ForegroundColor Cyan
    Compress-Archive -Path $itemsToZip -DestinationPath $zipBackupPath -Force
    Write-Host "✔ Backup file tersimpan: $zipBackupPath" -ForegroundColor Green
} else {
    Write-Warning 'Tidak ada folder/file untuk di-zip (cek public/images dan storage/app/public)'
}

# ------------------------------
# Ringkasan
# ------------------------------
Write-Host "\nSelesai. Backup berada di: $backupDir" -ForegroundColor Yellow
Write-Host "- Database: $dbBackupPath" -ForegroundColor DarkGray
Write-Host "- File:     $zipBackupPath" -ForegroundColor DarkGray

# Cara Restore (ringkas):
# 1) Import DB: mysql -h HOST -P PORT -u USER -p < db-YYYYMMDD-HHMMSS.sql
# 2) Extract ZIP: unzip files-YYYYMMDD-HHMMSS.zip ke project root
# 3) Pastikan symlink storage: php artisan storage:link
