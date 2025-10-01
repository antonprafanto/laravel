# Bab 04: Project Laravel Pertama 🎉

[⬅️ Bab 03: Instalasi Environment](03-instalasi-environment.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 05: Hello World ➡️](05-hello-world.md)

---

## 🎯 Learning Objectives

Setelah mempelajari bab ini, kamu akan:
- ✅ Bisa membuat project Laravel menggunakan Composer
- ✅ Memahami setiap output yang muncul saat instalasi
- ✅ Bisa membuka project di VS Code
- ✅ Memahami struktur folder Laravel
- ✅ Mengerti flow request di Laravel (mental model)
- ✅ Berhasil menjalankan server Laravel dan lihat welcome page

---

## 🎯 Analogi Sederhana: Membuat Project seperti Bangun Rumah Makan

Bayangkan kamu mau buka rumah makan:

**Tanpa Framework (PHP Murni):**
```
❌ Bangun gedung dari nol
❌ Desain dapur sendiri
❌ Buat meja-kursi sendiri
❌ Rancang sistem pelayanan
❌ Buat sistem kasir
→ Butuh waktu BERBULAN-BULAN! 😫
```

**Dengan Laravel (Composer create-project):**
```
✅ Gedung sudah jadi (struktur folder)
✅ Dapur lengkap (backend system)
✅ Meja-kursi tersedia (view templates)
✅ SOP pelayanan ada (routing & controllers)
✅ Sistem kasir siap (authentication)
→ Dalam 5 MENIT sudah siap! 🎉
```

**Composer** adalah "kontraktor" yang membangun semua itu untukmu!

---

## 📦 Bagian 1: Membuat Project Laravel

### Step 1: Buka Terminal

**Cara 1 - Dari Laragon:**
1. Buka Laragon
2. Klik **Menu** → **Terminal**

**Cara 2 - Dari VS Code:**
1. Buka VS Code
2. Tekan `Ctrl + ~` (atau `Cmd + ~` di Mac)

**Cara 3 - Manual:**
- Windows: Buka PowerShell atau CMD
- Mac: Buka Terminal

---

### Step 2: Navigasi ke Folder Projects

```bash
# Pindah ke folder Documents
cd Documents

# Buat folder untuk semua project Laravel
mkdir laravel-projects

# Masuk ke folder tersebut
cd laravel-projects
```

**Cek lokasi saat ini:**
```bash
pwd
# Output: C:\Users\NamaKamu\Documents\laravel-projects
```

---

### Step 3: Buat Project Laravel dengan Composer

Ketik command ini (copy-paste boleh):

```bash
composer create-project laravel/laravel blog-app
```

**Penjelasan command:**
- `composer` = Panggil Composer
- `create-project` = Perintah buat project baru
- `laravel/laravel` = Package Laravel dari Packagist
- `blog-app` = Nama folder project kita

**Tekan Enter dan tunggu...**

---

### 🔄 Apa yang Terjadi Saat Instalasi?

Kamu akan lihat output seperti ini:

```
Creating a "laravel/laravel" project at "./blog-app"
Installing laravel/laravel (v11.x.x)
  - Installing laravel/laravel (v11.x.x): Extracting archive
Created project in C:\Users\...\blog-app
> @php -r "file_exists('.env') || copy('.env.example', '.env');"
Loading composer repositories with package information
Updating dependencies
...
Package operations: 107 installs, 0 updates, 0 removals
...
Generating optimized autoload files
> @php artisan key:generate --ansi
Application key set successfully.
```

**Mari kita pahami apa yang terjadi:**

#### 1️⃣ **Download Laravel**
```
Installing laravel/laravel (v11.x.x): Extracting archive
```
→ Composer download Laravel dan extract ke folder `blog-app`

#### 2️⃣ **Copy File .env**
```
@php -r "file_exists('.env') || copy('.env.example', '.env');"
```
→ Copy file konfigurasi `.env.example` jadi `.env`
→ **Analogi:** Copy template formulir kosong untuk diisi

#### 3️⃣ **Install Dependencies**
```
Package operations: 107 installs
```
→ Install 100+ package yang dibutuhkan Laravel
→ **Analogi:** Panggil supplier untuk antar bahan makanan ke dapur

#### 4️⃣ **Generate Application Key**
```
Application key set successfully.
```
→ Buat kunci rahasia untuk enkripsi
→ **Analogi:** Buat kunci gembok untuk brankas

**Total waktu:** 2-5 menit (tergantung internet)

---

### ✅ Verifikasi Project Berhasil Dibuat

```bash
# Masuk ke folder project
cd blog-app

# Lihat isi folder
dir      # Windows
ls       # Mac/Linux
```

**Kamu akan lihat folder & file Laravel:**
```
app/
bootstrap/
config/
database/
public/
resources/
routes/
storage/
tests/
vendor/
.env
.env.example
artisan
composer.json
composer.lock
package.json
...
```

**Lihat folder-folder itu?** Project berhasil dibuat! 🎉

---

## 📂 Bagian 2: Membuka Project di VS Code

### Cara 1: Dari Terminal

```bash
# Pastikan kamu di folder blog-app
cd blog-app

# Buka di VS Code
code .
```

**Catatan:** Titik (`.`) artinya "folder saat ini"

---

### Cara 2: Dari VS Code Langsung

1. Buka VS Code
2. **File** → **Open Folder**
3. Pilih folder `blog-app`
4. Klik **Select Folder**

---

### Cara 3: Dari Laragon (Termudah!)

1. Buka Laragon
2. Klik kanan pada project `blog-app`
3. Pilih **Open with → VS Code**

---

## 🏗️ Bagian 3: Struktur Folder Laravel

Mari kita pahami setiap folder dengan **Analogi Rumah Makan Padang:**

```
blog-app/
├── app/              🍳 DAPUR - Tempat masak logic
├── bootstrap/        🚀 RUANG MESIN - Starter aplikasi
├── config/           ⚙️ BUKU ATURAN - Konfigurasi
├── database/         📦 GUDANG - Data & blueprint tabel
├── public/           🚪 PINTU MASUK - Entry point
├── resources/        🎨 RUANG DEKOR - View, CSS, JS
├── routes/           📋 BUKU MENU - Daftar halaman
├── storage/          🗄️ LEMARI ARSIP - File & cache
├── tests/            🧪 LAB TESTING - Unit test
├── vendor/           🚚 SUPPLIER - Package dari luar
├── .env              🔐 RAHASIA - Password & config
└── artisan           🤖 ASISTEN - Helper commands
```

---

### 📁 Penjelasan Detail Setiap Folder

#### 1. `app/` - DAPUR RESTORAN 🍳

**Isi:**
```
app/
├── Console/          → Command line commands
├── Exceptions/       → Handle error
├── Http/
│   ├── Controllers/  → PELAYAN (menerima request)
│   └── Middleware/   → SATPAM (filter request)
├── Models/           → JURU BICARA ke database
└── Providers/        → Service providers
```

**Analogi:**
- `Controllers/` = **Pelayan** yang terima pesanan customer
- `Models/` = **Juru bicara** ke gudang (database)
- `Middleware/` = **Satpam** cek tamu sebelum masuk

**Paling sering kamu edit:** Controllers & Models

---

#### 2. `routes/` - BUKU MENU 📋

**Isi:**
```
routes/
├── web.php           → Route untuk website
├── api.php           → Route untuk API
├── console.php       → Route untuk CLI
└── channels.php      → Route untuk broadcasting
```

**Analogi:**
```
web.php = BUKU MENU
Isinya:
- /home        → Halaman home
- /about       → Halaman about
- /contact     → Halaman contact
- /blog/{id}   → Halaman detail blog
```

**File paling penting:** `web.php`

---

#### 3. `resources/views/` - RUANG MAKAN 🎨

**Isi:**
```
resources/
├── views/            → Template HTML (Blade)
├── css/              → Styling
└── js/               → JavaScript
```

**Analogi:**
```
views/ = RUANG MAKAN
- Tempat makanan disajikan ke customer
- Tempat customer melihat tampilan
- Harus cantik dan nyaman!
```

**File paling sering edit:** File `.blade.php` di `views/`

---

#### 4. `database/` - GUDANG 📦

**Isi:**
```
database/
├── migrations/       → Blueprint tabel database
├── seeders/          → Data dummy
└── factories/        → Template data fake
```

**Analogi:**
```
migrations/ = BLUEPRINT GUDANG
- Desain rak mana untuk sayur
- Desain rak mana untuk daging
- Kapan dibangun, kapan dibongkar

seeders/ = ISI GUDANG dengan data contoh
```

---

#### 5. `public/` - PINTU MASUK 🚪

**Isi:**
```
public/
├── index.php         → Entry point aplikasi
├── css/              → CSS public
├── js/               → JS public
└── images/           → Gambar
```

**Analogi:**
```
index.php = PINTU UTAMA restoran
- Semua customer masuk dari sini
- Customer tidak bisa masuk dari dapur!
```

**PENTING:** Hanya file di `public/` yang bisa diakses dari browser!

---

#### 6. `storage/` - LEMARI ARSIP 🗄️

**Isi:**
```
storage/
├── app/              → File upload user
├── framework/        → Cache, session, views
└── logs/             → Log error
```

**Analogi:**
```
app/ = LEMARI untuk simpan dokumen customer
framework/ = LEMARI untuk catatan internal
logs/ = BUKU CATATAN kejadian harian
```

---

#### 7. `config/` - BUKU ATURAN ⚙️

**Isi:**
```
config/
├── app.php           → Config aplikasi
├── database.php      → Config database
├── mail.php          → Config email
└── ...
```

**Analogi:**
```
BUKU ATURAN RESTORAN:
- Jam buka: 08:00 - 22:00
- Kapasitas: 50 orang
- Sistem pembayaran: Cash/Kartu
```

---

#### 8. `vendor/` - SUPPLIER 🚚

**Isi:** 100+ package dari Composer

**Analogi:**
```
SUPPLIER yang antar bahan makanan:
- Supplier sayur
- Supplier daging
- Supplier bumbu
→ JANGAN DISENTUH! Ini wilayah supplier.
```

**PENTING:** Jangan edit file di `vendor/`! Akan hilang saat `composer update`

---

#### 9. `.env` - FILE RAHASIA 🔐

**Isi:**
```
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_app
DB_USERNAME=root
DB_PASSWORD=
```

**Analogi:**
```
FILE RAHASIA RESTORAN:
- Password WiFi
- PIN brankas
- Nomor rekening bank
- Rahasia resep masakan
```

**PENTING:** Jangan share file `.env` ke orang lain atau GitHub!

---

#### 10. `artisan` - ASISTEN PRIBADI 🤖

**Fungsi:** Command line helper

**Analogi:**
```
ASISTEN ROBOT yang bisa disuruh:
"Tolong buatkan controller baru"
"Tolong jalankan migration"
"Tolong bersihkan cache"
→ Tinggal perintah, langsung dikerjakan!
```

**Cara pakai:**
```bash
php artisan serve             # Jalankan server
php artisan make:controller   # Buat controller
php artisan migrate           # Jalankan migration
php artisan list              # Lihat semua command
```

---

## 🔄 Bagian 4: Mental Model - Flow Request di Laravel

**Pahami alur ini sebelum coding!**

```
1. CUSTOMER (Browser)
   ↓
   Ketik: http://localhost:8000/about

2. PINTU MASUK (public/index.php)
   ↓
   "Ada tamu! Teruskan ke buku menu"

3. BUKU MENU (routes/web.php)
   ↓
   Cari: Route untuk /about → Ketemu!
   "Panggil pelayan AboutController"

4. PELAYAN (Controller)
   ↓
   AboutController ambil data dari Model
   "Ambil data company dari database"

5. JURU BICARA (Model)
   ↓
   Model ngobrol dengan database
   "SELECT * FROM companies"

6. DAPUR (Controller lagi)
   ↓
   Controller terima data, olah, kirim ke View
   "Nih data-nya, tampilin ke customer"

7. RUANG MAKAN (View)
   ↓
   View render HTML cantik
   "<h1>About Us</h1>"

8. CUSTOMER (Browser)
   ↓
   Lihat halaman about yang cantik! 🎉
```

**Hafal alur ini!** Ini kunci memahami Laravel!

---

## 🚀 Bagian 5: Menjalankan Server Laravel

### Step 1: Buka Terminal di VS Code

Tekan `Ctrl + ~` (atau `Cmd + ~` di Mac)

Pastikan kamu di folder project:
```bash
pwd
# Output: .../laravel-projects/blog-app
```

---

### Step 2: Jalankan Server

```bash
php artisan serve
```

**Output yang diharapkan:**
```
   INFO  Server running on [http://127.0.0.1:8000].

  Press Ctrl+C to stop the server
```

**Penjelasan:**
- Laravel jalan di `http://127.0.0.1:8000` (sama dengan `http://localhost:8000`)
- Server akan terus jalan sampai kamu tekan `Ctrl+C`

---

### Step 3: Buka di Browser

1. Buka browser (Chrome, Firefox, Edge)
2. Ketik: `http://localhost:8000`
3. Tekan Enter

**Kamu akan lihat:**
```
┌─────────────────────────────┐
│                             │
│      Welcome to Laravel     │
│                             │
│   [Documentation] [Laracasts] │
│                             │
└─────────────────────────────┘
```

**Lihat halaman Laravel welcome?** BERHASIL! 🎉🎉🎉

---

## 📸 Screenshot Welcome Page

![Screenshot placeholder](../assets/images/04-laravel-welcome.png)

**Ini adalah moment penting!** Project Laravel pertamamu berhasil jalan! 💪

---

## 📝 Latihan: Eksplorasi Project

### Latihan 1: Buka File Welcome View

1. Di VS Code, buka: `resources/views/welcome.blade.php`
2. Scroll ke bawah, cari teks "Documentation"
3. Ubah jadi "Dokumentasi"
4. Save (`Ctrl+S`)
5. Refresh browser (`F5`)
6. Lihat perubahannya!

**Berhasil?** Kamu baru saja edit view pertamamu! ✨

---

### Latihan 2: Lihat File Routes

1. Buka: `routes/web.php`
2. Lihat kode di dalamnya:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
```

**Penjelasan:**
- `Route::get('/')` = Route untuk homepage (`/`)
- `function()` = Function yang akan dijalankan
- `return view('welcome')` = Tampilkan view `welcome.blade.php`

**Ini adalah route pertamamu!** Nanti kita akan belajar lebih detail.

---

### Latihan 3: Cek File .env

1. Buka: `.env`
2. Lihat baris:

```
APP_NAME=Laravel
APP_URL=http://localhost
```

3. Ubah jadi:

```
APP_NAME="Blog App"
APP_URL=http://localhost:8000
```

4. Save
5. **Stop server** (Ctrl+C di terminal)
6. **Start lagi** (`php artisan serve`)
7. Lihat tab browser - title berubah!

---

## ⚠️ Troubleshooting

### Problem 1: "composer: command not found"

**Solusi:** Composer belum terinstall atau tidak ada di PATH. Kembali ke Bab 03 dan install Composer.

---

### Problem 2: Port 8000 sudah dipakai

**Error:**
```
Failed to listen on 127.0.0.1:8000 (Address already in use)
```

**Solusi:** Ganti port:
```bash
php artisan serve --port=8080
```

Lalu buka: `http://localhost:8080`

---

### Problem 3: Blank page / Error 500

**Solusi:**
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Set permission (Linux/Mac)
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

### Problem 4: View tidak berubah setelah edit

**Solusi:**
1. Hard refresh browser: `Ctrl+Shift+R` (atau `Cmd+Shift+R`)
2. Clear view cache:
```bash
php artisan view:clear
```

---

## 📖 Summary

Di bab ini kamu sudah:

- ✅ Membuat project Laravel pertama dengan `composer create-project`
- ✅ Memahami struktur folder Laravel dengan analogi Rumah Makan
- ✅ Mengerti flow request: Browser → Routes → Controller → Model → View
- ✅ Menjalankan Laravel server dengan `php artisan serve`
- ✅ Melihat welcome page Laravel di browser
- ✅ Edit view dan lihat hasilnya langsung

**Kamu sekarang punya project Laravel yang jalan!** 🎉

---

## 🎯 Next Chapter Preview

Di chapter berikutnya, kita akan:
- ✅ Membuat route pertama yang return "Hello World"
- ✅ Membuat route dengan parameter
- ✅ Build confidence dengan quick wins!
- ✅ Praktek langsung tanpa konsep yang rumit

**Saatnya coding dan lihat hasil langsung!** 🚀

---

## 🔗 Referensi

- 📖 [Laravel Installation Docs](https://laravel.com/docs/12.x/installation)
- 📖 [Laravel Directory Structure](https://laravel.com/docs/12.x/structure)
- 📖 [Composer create-project](https://getcomposer.org/doc/03-cli.md#create-project)

---

[⬅️ Bab 03: Instalasi Environment](03-instalasi-environment.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 05: Hello World ➡️](05-hello-world.md)

---

<div align="center">

**Project sudah jalan? Ayo buat "Hello World" pertama!** 🚀

**Screenshot welcome page kamu dan share ke teman!** 📸

</div>