# Pelajaran 1: Instalasi Tools dan Laravel

Selamat datang di pelajaran pertama! Di sini kita akan mempersiapkan environment development dan menginstall Laravel 12 untuk project blog pertama Anda.

## 🎯 Tujuan Pembelajaran

Setelah menyelesaikan pelajaran ini, Anda akan:
- ✅ Memiliki environment development yang siap untuk Laravel
- ✅ Berhasil menginstall Laravel 12
- ✅ Menjalankan server development pertama kali
- ✅ Memahami struktur dasar project Laravel

## 🛠️ Tools yang Dibutuhkan

### 1. PHP 8.2+
Laravel 12 membutuhkan PHP minimal versi 8.2. Periksa versi PHP Anda:

```bash
# Cek versi PHP yang terinstall di sistem
php --version
```

**Instalasi PHP:**
- **Windows**: Download dari [php.net](https://windows.php.net/download) atau gunakan XAMPP
- **macOS**: Gunakan Homebrew: `brew install php`
- **Ubuntu/Linux**: `sudo apt install php8.2 php8.2-cli php8.2-mbstring php8.2-xml php8.2-curl`

### 2. Composer
Composer adalah dependency manager untuk PHP yang wajib untuk Laravel.

```bash
# Verifikasi instalasi Composer
composer --version
```

**Instalasi Composer:**
- Download dari [getcomposer.org](https://getcomposer.org/download/)
- Ikuti petunjuk instalasi sesuai OS Anda

### 3. Node.js dan NPM
Diperlukan untuk asset compilation (CSS, JavaScript).

```bash
# Cek versi Node.js
node --version

# Cek versi NPM (Node Package Manager)
npm --version
```

**Instalasi Node.js:**
- Download dari [nodejs.org](https://nodejs.org/)
- NPM akan terinstall otomatis bersama Node.js

### 4. Database (MySQL/PostgreSQL)
Untuk tutorial ini, kita akan menggunakan **MySQL** dengan XAMPP untuk pengalaman yang lebih realistis.

### 5. Text Editor/IDE
Rekomendasi:
- **VS Code** (gratis) dengan extension PHP dan Laravel
- **PhpStorm** (berbayar, sangat lengkap)
- **Sublime Text** atau **Atom**

## 🚀 Instalasi Laravel 12

### Step 1: Membuat Project Baru

Buka terminal/command prompt dan jalankan:

```bash
# Buat proyek Laravel baru bernama "blog-laravel"
# Composer = alat untuk unduh pustaka PHP
# Ini akan unduh semua berkas Laravel yang kita butuhkan
composer create-project laravel/laravel blog-laravel
```

Proses ini akan:
- Download Laravel 12 terbaru
- Install semua dependencies
- Setup struktur project

### Step 2: Masuk ke Direktori Project

```bash
# Masuk ke folder proyek Laravel kita
# cd = ganti direktori (pindah folder)
cd blog-laravel
```

### Step 3: Setup Database dengan XAMPP

Sebelum konfigurasi Laravel, kita perlu menyiapkan database MySQL:

1. **Install dan jalankan XAMPP**
   - Download XAMPP dari [apachefriends.org](https://www.apachefriends.org/)
   - Install dan jalankan Apache + MySQL

2. **Buat database melalui phpMyAdmin**
   - Buka browser dan akses `http://localhost/phpmyadmin`
   - Klik "New" untuk membuat database baru
   - Nama database: `laravel_blog`
   - Collation: `utf8mb4_general_ci`
   - Klik "Create"

### Step 4: Konfigurasi Environment

Laravel menggunakan file `.env` untuk konfigurasi. Edit file `.env`:

```env
# Pengaturan Database MySQL - Laravel butuh tahu cara hubung ke database
DB_CONNECTION=mysql          # Kita pakai MySQL (bukan PostgreSQL atau SQLite)
DB_HOST=127.0.0.1           # Database ada di komputer kita sendiri
DB_PORT=3306                # MySQL biasanya pakai pintu 3306 (bawaan)
DB_DATABASE=laravel_blog    # Nama database yang tadi kita buat di phpMyAdmin
DB_USERNAME=root            # Nama pengguna untuk masuk MySQL (XAMPP bawaannya "root")
DB_PASSWORD=                # Kata sandi kosong karena XAMPP bawaan tanpa kata sandi
```

**Pastikan menghapus tanda # (uncomment) pada semua baris DB_* di atas!**

### Step 5: Generate Application Key

```bash
# Buat kunci rahasia unik untuk aplikasi kita
# Laravel butuh ini untuk mengamankan data (seperti kata sandi, sesi)
php artisan key:generate
```

### Step 6: Jalankan Migration Default

**Pastikan XAMPP MySQL sudah berjalan sebelum menjalankan migration!**

Laravel sudah menyediakan beberapa migration default. Jalankan:

```bash
# Buat tabel-tabel bawaan Laravel di database kita
# Laravel sudah sediakan tabel pengguna (untuk masuk) dan tabel lainnya
php artisan migrate
```

Anda akan melihat output seperti ini:
```
Migration table created successfully.
Migrating: 2014_10_12_000000_create_users_table
Migrated:  2014_10_12_000000_create_users_table (25.67ms)
...
```

## 🏃‍♂️ Menjalankan Server Development

Sekarang mari kita test instalasi dengan menjalankan server:

```bash
# Nyalakan server website Laravel kita
# Setelah ini, website bisa dibuka di browser
php artisan serve
```

Output akan menunjukkan:
```
Starting Laravel development server: http://127.0.0.1:8000
```

Buka browser dan akses `http://127.0.0.1:8000`. Anda akan melihat halaman welcome Laravel! 🎉

## 📁 Memahami Struktur Project

Mari kita eksplorasi struktur direktori Laravel:

```
blog-laravel/
├── app/                 # Core aplikasi (Models, Controllers)
│   ├── Http/Controllers/
│   ├── Models/
│   └── Providers/
├── bootstrap/           # File bootstrap dan cache
├── config/              # File konfigurasi
├── database/            # Migrations, seeds, factories
│   └── migrations/
├── public/              # File publik (index.php, assets)
├── resources/           # Views, raw assets
│   ├── views/
│   └── css/
├── routes/              # Route definitions
│   └── web.php
├── storage/             # File storage, logs, cache
├── tests/               # Unit dan feature tests
├── .env                 # Environment configuration
├── artisan              # Command line tool Laravel
├── composer.json        # Dependencies PHP
└── package.json         # Dependencies Node.js
```

### Direktori Penting untuk Dipahami:

**`app/`**: Inti aplikasi Anda
- `Models/`: Eloquent models untuk database
- `Http/Controllers/`: Logic aplikasi

**`resources/views/`**: Template Blade untuk UI

**`routes/web.php`**: Definisi route untuk web

**`database/migrations/`**: Schema database

## ✅ Verifikasi Instalasi

Pastikan semuanya berjalan dengan baik:

1. **Server berjalan**: `php artisan serve` tanpa error
2. **Database terkoneksi**: Migration berhasil
3. **Halaman welcome** terbuka di browser
4. **Artisan commands**: `php artisan --version` menampilkan Laravel 12.x

## 🚀 Perintah Artisan Berguna

Laravel menyediakan CLI tool bernama Artisan. Beberapa perintah berguna:

```bash
# Melihat semua perintah
php artisan list

# Membuat controller
php artisan make:controller

# Membuat model
php artisan make:model

# Melihat routes
php artisan route:list

# Clear cache
php artisan cache:clear
```

## 🎯 Kesimpulan

Selamat! Anda telah berhasil:
- ✅ Menginstall semua tools yang diperlukan
- ✅ Membuat project Laravel 12 baru
- ✅ Menjalankan server development
- ✅ Memahami struktur dasar project

Di pelajaran selanjutnya, kita akan mulai bekerja dengan routing dan membuat halaman pertama kita.

## 📝 Troubleshooting

**Error: "php command not found"**
- PHP belum terinstall atau tidak ada di PATH
- Solusi: Install PHP dan tambahkan ke system PATH

**Error: "composer command not found"**
- Composer belum terinstall
- Solusi: Install Composer dari getcomposer.org

**Error: Database connection failed**
- XAMPP MySQL belum berjalan atau database belum dibuat
- Solusi: 
  1. Pastikan XAMPP MySQL service aktif
  2. Database `laravel_blog` sudah dibuat di phpMyAdmin
  3. Konfigurasi `.env` sudah benar (uncomment semua baris DB_*)

**Error: "SQLSTATE[HY000] [1049] Unknown database"**
- Database belum dibuat di phpMyAdmin
- Solusi: Buat database `laravel_blog` melalui phpMyAdmin

**Error: Migration gagal**
- Periksa koneksi database dan pastikan semua service XAMPP berjalan
- Pastikan tidak ada typo di konfigurasi `.env`

---

**Selanjutnya:** [Pelajaran 2: Membuat Routes dan Halaman Baru](02-routes-halaman-baru.md)

*Selamat belajar! 🚀*