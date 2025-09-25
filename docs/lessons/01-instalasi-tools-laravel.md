# Pelajaran 1: Instalasi Tools dan Laravel

Selamat datang di pelajaran pertama! Di sini kita akan mempersiapkan environment development dan menginstall Laravel 12 untuk project blog pertama Anda.

## 🏠 Analogi: Membangun Rumah (Website)

Bayangkan Anda ingin membangun sebuah rumah (website). Sebelum mulai membangun, Anda perlu:

**🔨 Peralatan Dasar (Tools Development):**
- **Martil, gergaji, obeng** = PHP, Composer, Node.js (tools dasar untuk coding)
- **Meja kerja yang rapi** = Text editor/IDE (tempat Anda bekerja)
- **Gudang material** = Database (tempat menyimpan data)

**🏗️ Fondasi Rumah (Laravel Framework):**
- **Pondasi beton yang kuat** = Laravel framework (struktur dasar yang kokoh)
- **Kerangka atap** = MVC pattern (cara mengorganisir kode)
- **Sistem listrik & air** = Routes & Database (aliran data dalam aplikasi)

**🎨 Dekorasi & Finishing (Frontend):**
- **Cat & wallpaper** = CSS/Tailwind (tampilan visual)
- **Furniture** = JavaScript components (interaktivitas)

Sama seperti membangun rumah, kita perlu menyiapkan semua peralatan dulu sebelum mulai coding!

## 🎯 Tujuan Pembelajaran

Setelah menyelesaikan pelajaran ini, Anda akan:
- ✅ Memiliki environment development yang siap untuk Laravel
- ✅ Berhasil menginstall Laravel 12
- ✅ Menjalankan server development pertama kali
- ✅ Memahami struktur dasar project Laravel

## 🛠️ Tools yang Dibutuhkan

### 1. PHP 8.2+ 🔧
**Analogi**: PHP adalah seperti **mesin mobil** - tanpa mesin, mobil tidak bisa jalan. Laravel butuh PHP untuk berjalan, sama seperti mobil butuh mesin.

Laravel 12 membutuhkan PHP minimal versi 8.2. Periksa versi PHP Anda:

```bash
# Cek versi PHP yang terinstall di sistem
php --version
```

**Instalasi PHP:**
- **Windows**: Download dari [php.net](https://windows.php.net/download) atau gunakan XAMPP
- **macOS**: Gunakan Homebrew: `brew install php`
- **Ubuntu/Linux**: `sudo apt install php8.2 php8.2-cli php8.2-mbstring php8.2-xml php8.2-curl`

### 2. Composer 📦
**Analogi**: Composer adalah seperti **tukang belanja online** yang pintar. Ketika Anda butuh furniture untuk rumah, Anda tinggal bilang "saya butuh meja dan kursi", lalu tukang belanja ini otomatis pergi ke toko yang tepat, beli barang yang compatible, dan mengaturnya di rumah Anda. Composer melakukan hal yang sama untuk kode - dia download dan atur semua library yang dibutuhkan Laravel.

Composer adalah dependency manager untuk PHP yang wajib untuk Laravel.

```bash
# Verifikasi instalasi Composer
composer --version
```

**Instalasi Composer:**
- Download dari [getcomposer.org](https://getcomposer.org/download/)
- Ikuti petunjuk instalasi sesuai OS Anda

### 3. Node.js dan NPM 🎨
**Analogi**: Node.js adalah seperti **mesin jahit** untuk membuat baju (CSS/JavaScript). NPM adalah **toko kain** yang menyediakan semua bahan (package) yang Anda butuhkan. Sama seperti tukang jahit butuh mesin dan bahan untuk membuat baju yang bagus, developer butuh Node.js dan NPM untuk membuat tampilan website yang indah.

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

### 4. Database (MySQL/PostgreSQL) 🗄️
**Analogi**: Database adalah seperti **lemari arsip raksasa** yang sangat terorganisir. Bayangkan perpustakaan dengan jutaan buku yang tersusun rapi berdasarkan kategori, nomor, dan sistem yang memudahkan pencarian. MySQL adalah "petugas perpustakaan super cepat" yang bisa mencari dan menyimpan data dalam hitungan milidetik.

Untuk tutorial ini, kita akan menggunakan **MySQL** dengan XAMPP untuk pengalaman yang lebih realistis.

### 5. Text Editor/IDE
Rekomendasi:
- **VS Code** (gratis) dengan extension PHP dan Laravel
- **PhpStorm** (berbayar, sangat lengkap)
- **Sublime Text** atau **Atom**

## 🚀 Instalasi Laravel 12

**🏠 Analogi Instalasi**: Membuat project Laravel seperti **memesan rumah pre-fabricated (rumah siap pakai)**. Anda tinggal bilang "saya mau rumah tipe Blog dengan 3 kamar (MVC), lengkap dengan furniture dasar (routing, database, authentication)". Laravel akan kirimkan rumah yang sudah jadi, tinggal Anda custom sesuai selera!

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

**PENTING untuk Laravel 12:** Laravel 12 secara default menggunakan SQLite sebagai database, bukan MySQL seperti versi sebelumnya. Untuk tutorial ini, kita akan mengubahnya ke MySQL agar sesuai dengan setup XAMPP.

Laravel menggunakan file `.env` untuk konfigurasi. Edit file `.env` dan ubah konfigurasi database dari SQLite ke MySQL:

Cari dan ubah konfigurasi database ini di file `.env`:

**UBAH DARI:**
```env
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

**MENJADI:**
```env
# Pengaturan Database MySQL - Laravel butuh tahu cara hubung ke database
DB_CONNECTION=mysql          # Kita pakai MySQL (bukan SQLite)
DB_HOST=127.0.0.1           # Database ada di komputer kita sendiri
DB_PORT=3306                # MySQL biasanya pakai pintu 3306 (bawaan)
DB_DATABASE=laravel_blog    # Nama database yang tadi kita buat di phpMyAdmin
DB_USERNAME=root            # Nama pengguna untuk masuk MySQL (XAMPP bawaannya "root")
DB_PASSWORD=                # Kata sandi kosong karena XAMPP bawaan tanpa kata sandi
```

**PENTING:** Hapus tanda `#` di depan setiap baris DB_ dan pastikan `DB_CONNECTION=mysql`!

### Step 5: Generate Application Key

```bash
# Buat kunci rahasia unik untuk aplikasi kita
# Laravel butuh ini untuk mengamankan data (seperti kata sandi, sesi)
php artisan key:generate
```

### Step 6: Jalankan Migration Default

**⚠️ PENTING:** Pastikan Anda berada di dalam directory project Laravel sebelum menjalankan perintah artisan!

```bash
# Pastikan Anda di dalam folder project Laravel
cd blog-laravel

# Pastikan XAMPP MySQL sudah berjalan sebelum migration
# Buat tabel-tabel bawaan Laravel di database kita
php artisan migrate
```

**FITUR BARU Laravel 12:** Jika database belum ada, Laravel 12 akan menanyakan apakah Anda ingin membuatnya:
```
WARN  The database 'laravel_blog' does not exist on the 'mysql' connection.

Would you like to create it? (yes/no) [yes]
❯ yes
```

Ketik `yes` dan tekan Enter. Laravel akan membuat database dan menjalankan migration:

```
INFO  Preparing database.
Creating migration table ............................ DONE
INFO  Running migrations.
0001_01_01_000000_create_users_table ................ DONE
0001_01_01_000001_create_cache_table ................ DONE
0001_01_01_000002_create_jobs_table ................. DONE
```

**⚠️ PENTING - Troubleshooting Database:**

Jika Anda mendapat error `Unknown database 'laravel_blog'` atau `Connection refused`, ikuti langkah berikut:

1. **Pastikan XAMPP MySQL berjalan:**
   - Buka XAMPP Control Panel
   - Klik "Start" pada Apache dan MySQL
   - Pastikan kedua service berwarna hijau

2. **Alternative: Buat database manual melalui phpMyAdmin**
   - Buka browser dan kunjungi `http://localhost/phpmyadmin`
   - Klik "New" di sidebar kiri
   - Nama database: `laravel_blog`
   - Collation: `utf8mb4_general_ci`
   - Klik "Create"

3. **Jika MySQL command tidak ditemukan:**
   - Ini normal jika MySQL tidak ada di PATH
   - Gunakan fitur auto-create Laravel atau phpMyAdmin
   - Tidak perlu install MySQL CLI secara terpisah

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

## 🧪 Pengujian & Validasi Instalasi

Sebelum melanjutkan ke pelajaran berikutnya, mari kita lakukan pengujian menyeluruh untuk memastikan semua tools dan Laravel telah terinstall dengan benar.

### 🔧 Test 1: Verifikasi Tools Development

**🎯 Tujuan:** Memastikan semua tools yang dibutuhkan sudah terinstall dan berfungsi dengan benar.

**Test Case 1.1 - PHP Installation:**
```bash
# Test versi PHP (minimal 8.2)
php --version

# Test extension PHP yang dibutuhkan
php -m | grep -E "(mbstring|xml|curl|openssl|pdo|tokenizer|json)"

# Test PHP bisa menjalankan script
php -r "echo 'PHP berfungsi dengan baik!'; echo PHP_EOL;"
```

**✅ Expected Results:**
- PHP version 8.2.x atau lebih tinggi
- Semua extension wajib tersedia
- Script PHP berhasil dijalankan

**Test Case 1.2 - Composer Installation:**
```bash
# Test versi Composer
composer --version

# Test Composer bisa download package
composer show --installed | head -5

# Test Composer global config
composer config --list --global
```

**✅ Expected Results:**
- Composer version 2.x
- Menampilkan list package yang terinstall
- Konfigurasi global ditampilkan tanpa error

**Test Case 1.3 - Node.js & NPM Installation:**
```bash
# Test versi Node.js (minimal 18.x)
node --version

# Test versi NPM
npm --version

# Test NPM bisa install package
npm list --depth=0
```

**✅ Expected Results:**
- Node.js version 18.x atau lebih tinggi
- NPM version 9.x atau lebih tinggi
- Package list ditampilkan tanpa error

### 🗄️ Test 2: Verifikasi Database Setup

**🎯 Tujuan:** Memastikan MySQL/database sudah berjalan dan dapat diakses.

**Test Case 2.1 - XAMPP Services:**
```bash
# Windows: Check services melalui Task Manager atau XAMPP Control Panel
# Pastikan Apache dan MySQL berjalan

# Test koneksi MySQL via command line (optional)
mysql -u root -p -e "SHOW DATABASES;"
```

**Test Case 2.2 - Database Connection:**
```bash
# Test koneksi database Laravel
php artisan tinker
```

```php
// Di dalam tinker
DB::connection()->getPdo();
echo "Database connected successfully!";

// Test query sederhana
DB::select('SELECT 1 as test');

// Exit tinker
exit;
```

**✅ Expected Results:**
- XAMPP services (Apache & MySQL) running
- Laravel berhasil connect ke database
- Query test berhasil dijalankan

### 🏗️ Test 3: Verifikasi Laravel Project

**🎯 Tujuan:** Memastikan Laravel project berhasil dibuat dan berfungsi dengan benar.

**Test Case 3.1 - Project Structure:**
```bash
# Test struktur direktori Laravel
ls -la
ls app/
ls config/
ls database/
ls resources/

# Test file penting ada
ls -la .env artisan composer.json package.json
```

**Test Case 3.2 - Laravel Commands:**
```bash
# Test Artisan commands
php artisan --version
php artisan list
php artisan route:list
php artisan config:show app.name

# Test environment configuration
php artisan env
```

**✅ Expected Results:**
- Semua direktori Laravel standard ada
- File konfigurasi (.env, composer.json) ada
- Artisan commands berfungsi tanpa error
- Laravel version 12.x ditampilkan

### 🌐 Test 4: Server Development & Browser

**🎯 Tujuan:** Memastikan server development berjalan dan website dapat diakses.

**Test Case 4.1 - Artisan Serve:**
```bash
# Start development server
php artisan serve

# Server harus berjalan di http://127.0.0.1:8000
# Jangan tutup terminal ini, buka terminal baru untuk test lainnya
```

**Test Case 4.2 - Browser Testing:**

Buka browser dan test URL berikut:

1. **Homepage:** `http://127.0.0.1:8000`
   - ✅ Harus tampil halaman Laravel welcome
   - ✅ Tidak ada error 500 atau 404
   - ✅ Page load dengan cepat

2. **Check Debug Mode:** Lihat source page
   - ✅ Tidak boleh ada error di browser console
   - ✅ Laravel Telescope/debug bar tidak tampil (production setting)

**Test Case 4.3 - Asset Compilation:**
```bash
# Di terminal baru (server tetap jalan di terminal lain)
npm install
npm run dev

# Test hot reload (optional)
npm run build
```

**✅ Expected Results:**
- NPM packages berhasil terinstall
- Asset compilation berhasil tanpa error
- File CSS/JS terbuild di public/ directory

### 🔍 Test 5: Database Migration & Seeding

**🎯 Tujuan:** Memastikan database migration dan seeding berfungsi dengan benar.

**Test Case 5.1 - Migration Testing:**
```bash
# Test migration status
php artisan migrate:status

# Test fresh migration
php artisan migrate:fresh

# Cek tabel yang terbuat
php artisan tinker
```

```php
// Di tinker - cek tabel yang terbuat
Schema::hasTable('users');
Schema::hasTable('password_reset_tokens');
Schema::hasTable('failed_jobs');

// Cek struktur tabel users
Schema::getColumnListing('users');
```

**Test Case 5.2 - Seeding Testing:**
```bash
# Test seeder (jika ada)
php artisan db:seed

# Atau test dengan factory
php artisan tinker
```

```php
// Test User factory
$user = User::factory()->create();
echo $user->name;
echo $user->email;
```

**✅ Expected Results:**
- Migration berhasil tanpa error
- Tabel standard Laravel terbuat
- Factory dan seeder berfungsi

### 🎯 Test 6: Environment Configuration

**🎯 Tujuan:** Memastikan konfigurasi environment sudah benar untuk development.

**Test Case 6.1 - Environment Variables:**
```bash
# Check key environment variables
php artisan tinker
```

```php
// Test environment config
config('app.name');        // Harus: Laravel atau nama project
config('app.env');         // Harus: local
config('app.debug');       // Harus: true
config('app.url');         // Harus: http://localhost
config('database.default'); // Harus: mysql

// Test database config
config('database.connections.mysql.host');     // Harus: 127.0.0.1
config('database.connections.mysql.database'); // Harus: laravel_blog
```

**Test Case 6.2 - Application Key:**
```bash
# Test application key ada dan valid
php artisan key:generate --show

# Test encryption berfungsi
php artisan tinker
```

```php
// Test encryption/decryption
$encrypted = encrypt('Hello Laravel!');
$decrypted = decrypt($encrypted);
echo $decrypted; // Harus: Hello Laravel!
```

**✅ Expected Results:**
- Environment variables sesuai untuk development
- Application key ter-generate dengan benar
- Encryption/decryption berfungsi

## 📋 Checklist Kelulusan Instalasi

Tandai ✅ untuk setiap test yang berhasil:

### 🔧 Tools Development
- [ ] PHP 8.2+ terinstall dengan extension lengkap
- [ ] Composer berfungsi dan bisa download packages
- [ ] Node.js & NPM terinstall dan berfungsi
- [ ] Git terinstall (untuk version control)

### 🗄️ Database Setup
- [ ] XAMPP MySQL service berjalan
- [ ] Database `laravel_blog` sudah dibuat
- [ ] Laravel bisa connect ke database
- [ ] Query database berhasil dijalankan

### 🏗️ Laravel Project
- [ ] Project Laravel berhasil dibuat
- [ ] Struktur direktori lengkap
- [ ] Artisan commands berfungsi
- [ ] .env configuration sudah benar

### 🌐 Server & Browser
- [ ] Development server berjalan di port 8000
- [ ] Homepage Laravel welcome tampil di browser
- [ ] Asset compilation (NPM) berfungsi
- [ ] Tidak ada error di browser console

### 🔍 Database Operations
- [ ] Migration berhasil dijalankan
- [ ] Tabel standard Laravel terbuat
- [ ] Seeder/Factory berfungsi (jika digunakan)
- [ ] Tinker bisa akses database

### 🎯 Configuration
- [ ] Environment variables sudah benar
- [ ] Application key ter-generate
- [ ] Encryption/decryption berfungsi
- [ ] Debug mode aktif untuk development

## 🚨 Troubleshooting Checklist

Jika ada test yang gagal, gunakan panduan ini:

### ❌ PHP Issues
- **"php command not found"** → Install PHP, tambahkan ke PATH
- **Extension missing** → Install php-extensions atau gunakan XAMPP
- **Permission denied** → Run as administrator/sudo

### ❌ Database Issues
- **Connection refused** → Start XAMPP MySQL service
- **Database not found** → Buat database di phpMyAdmin
- **Access denied** → Check username/password di .env

### ❌ Laravel Issues
- **Key not set** → Run `php artisan key:generate`
- **Cache issues** → Run `php artisan config:clear`
- **Permission issues** → Set proper folder permissions

### ❌ NPM Issues
- **Node version old** → Update Node.js ke versi LTS
- **Package install fail** → Clear npm cache, delete node_modules
- **Build errors** → Check package.json syntax

## 🎯 Kesimpulan

Selamat! Anda telah berhasil:
- ✅ Menginstall semua tools yang diperlukan
- ✅ Membuat project Laravel 12 baru
- ✅ Menjalankan server development
- ✅ Memahami struktur dasar project
- ✅ **[BARU] Melakukan pengujian komprehensif instalasi**

Dengan semua test di atas berhasil, environment development Anda sudah siap untuk pembelajaran Laravel. Di pelajaran selanjutnya, kita akan mulai bekerja dengan routing dan membuat halaman pertama kita.

## 📝 Troubleshooting

**Error: "Could not open input file: artisan"**
- Anda tidak berada di dalam directory project Laravel
- Solusi: Pastikan Anda sudah menjalankan `cd blog-laravel` terlebih dahulu

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
- Database belum dibuat di phpMyAdmin atau XAMPP MySQL belum berjalan
- Solusi:
  1. Buka XAMPP Control Panel dan pastikan MySQL berjalan (warna hijau)
  2. Gunakan fitur auto-create Laravel 12: ketik `yes` saat diminta
  3. Alternative: Buat database `laravel_blog` manual di phpMyAdmin

**Error: Migration gagal**
- Periksa koneksi database dan pastikan semua service XAMPP berjalan
- Pastikan tidak ada typo di konfigurasi `.env`
- Pastikan berada di directory yang benar (`cd blog-laravel`)

---

**Selanjutnya:** [Pelajaran 2: Membuat Routes dan Halaman Baru](02-routes-halaman-baru.md)

*Selamat belajar! 🚀*