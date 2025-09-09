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
php --version
```

**Instalasi PHP:**
- **Windows**: Download dari [php.net](https://windows.php.net/download) atau gunakan XAMPP
- **macOS**: Gunakan Homebrew: `brew install php`
- **Ubuntu/Linux**: `sudo apt install php8.2 php8.2-cli php8.2-mbstring php8.2-xml php8.2-curl`

### 2. Composer
Composer adalah dependency manager untuk PHP yang wajib untuk Laravel.

```bash
composer --version
```

**Instalasi Composer:**
- Download dari [getcomposer.org](https://getcomposer.org/download/)
- Ikuti petunjuk instalasi sesuai OS Anda

### 3. Node.js dan NPM
Diperlukan untuk asset compilation (CSS, JavaScript).

```bash
node --version
npm --version
```

**Instalasi Node.js:**
- Download dari [nodejs.org](https://nodejs.org/)
- NPM akan terinstall otomatis bersama Node.js

### 4. Database (MySQL/PostgreSQL)
Untuk tutorial ini, kita akan menggunakan **SQLite** yang sudah built-in di Laravel untuk kemudahan.

### 5. Text Editor/IDE
Rekomendasi:
- **VS Code** (gratis) dengan extension PHP dan Laravel
- **PhpStorm** (berbayar, sangat lengkap)
- **Sublime Text** atau **Atom**

## 🚀 Instalasi Laravel 12

### Step 1: Membuat Project Baru

Buka terminal/command prompt dan jalankan:

```bash
composer create-project laravel/laravel blog-laravel
```

Proses ini akan:
- Download Laravel 12 terbaru
- Install semua dependencies
- Setup struktur project

### Step 2: Masuk ke Direktori Project

```bash
cd blog-laravel
```

### Step 3: Konfigurasi Environment

Laravel menggunakan file `.env` untuk konfigurasi. File ini sudah dibuat otomatis, mari kita periksa:

```bash
# Windows
type .env

# macOS/Linux
cat .env
```

Untuk tutorial ini, kita akan menggunakan SQLite. Edit file `.env`:

```env
DB_CONNECTION=sqlite
# Hapus atau comment line berikut:
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

### Step 4: Membuat Database SQLite

```bash
# Windows
type nul > database/database.sqlite

# macOS/Linux
touch database/database.sqlite
```

### Step 5: Generate Application Key

```bash
php artisan key:generate
```

### Step 6: Jalankan Migration Default

Laravel sudah menyediakan beberapa migration default. Jalankan:

```bash
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
│   ├── migrations/
│   └── database.sqlite
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
- File SQLite belum dibuat atau permission error
- Solusi: Pastikan file `database/database.sqlite` exists dan writable

---

**Selanjutnya:** [Pelajaran 2: Membuat Routes dan Halaman Baru](02-routes-halaman-baru.md)

*Selamat belajar! 🚀*