# Bab 12: Pengenalan Database 🗄️

[⬅️ Bab 11: Artisan Helper](11-artisan-helper.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 13: Migration Dasar ➡️](13-migration-dasar.md)

---

## 🎯 Learning Objectives

Setelah mempelajari bab ini, kamu akan:
- ✅ Memahami apa itu database dan mengapa penting
- ✅ Bisa setup database MySQL di Laragon/XAMPP
- ✅ Memahami file .env dan konfigurasinya
- ✅ Bisa konfigurasi koneksi database di Laravel
- ✅ Berhasil test koneksi database
- ✅ Siap untuk belajar migration

---

## 🎯 Analogi Sederhana: Database seperti Lemari Arsip Raksasa

**Tanpa Database:**
```
Data customer ditulis di kertas:
📄 Budi - 081234567890 - budi@mail.com
📄 Ani - 081234567891 - ani@mail.com
📄 Citra - 081234567892 - citra@mail.com

Masalah:
- Susah cari data
- Bisa hilang
- Tidak bisa filter/sort
- Tidak bisa query kompleks
→ Kacau! 😫
```

**Dengan Database:**
```
┌─────────────────────────────────────────┐
│      📊 DATABASE: my_app                │
├─────────────────────────────────────────┤
│ TABLE: users                            │
│ ┌────┬─────────┬───────────┬───────────┐│
│ │ ID │ Nama    │ Telepon   │ Email     ││
│ ├────┼─────────┼───────────┼───────────┤│
│ │ 1  │ Budi    │ 08123...  │ budi@...  ││
│ │ 2  │ Ani     │ 08123...  │ ani@...   ││
│ │ 3  │ Citra   │ 08123...  │ citra@... ││
│ └────┴─────────┴───────────┴───────────┘│
└─────────────────────────────────────────┘

Keuntungan:
✅ Cepat cari data
✅ Aman dan terorganisir
✅ Bisa filter, sort, search
✅ Relasi antar data
```

**Database** = Tempat penyimpanan data terstruktur yang powerful!

---

## 📚 Penjelasan: Apa itu Database?

**Database** = Sistem penyimpanan data yang terorganisir

**Konsep Penting:**

### 1. Database
Wadah untuk menyimpan semua data aplikasi
```
Database: toko_online
```

### 2. Table (Tabel)
Wadah untuk menyimpan data sejenis
```
Table: users, products, orders
```

### 3. Column (Kolom)
Field/atribut dari data
```
Columns: id, name, email, password
```

### 4. Row (Baris)
Data individual
```
Row: 1, "Budi", "budi@mail.com", "hashed_password"
```

---

### Analogi Lengkap: Database seperti Excel

```
DATABASE = File Excel (.xlsx)
├── TABLE 1 = Sheet "Customers"
│   ├── COLUMN: ID, Name, Email, Phone
│   └── ROW: 1, Budi, budi@mail.com, 0812...
│
├── TABLE 2 = Sheet "Products"
│   ├── COLUMN: ID, Name, Price, Stock
│   └── ROW: 1, Laptop, 8000000, 10
│
└── TABLE 3 = Sheet "Orders"
    ├── COLUMN: ID, Customer_ID, Product_ID, Quantity
    └── ROW: 1, 1, 1, 2
```

**Bedanya dengan Excel:**
- Database lebih cepat untuk data besar
- Bisa handle concurrent users
- Bisa relasi antar table
- Lebih aman

---

## 🖥️ Bagian 1: Setup Database di Laragon

### Step 1: Pastikan MySQL Jalan

1. Buka Laragon
2. Klik **Start All**
3. Pastikan MySQL running (icon hijau)

![Screenshot placeholder](../assets/images/12-laragon-mysql.png)

---

### Step 2: Buka Database Manager (HeidiSQL)

**Di Laragon:**
1. Klik **Menu** → **MySQL** → **HeidiSQL**
2. Atau klik **Database** di Laragon

**HeidiSQL akan terbuka** dengan koneksi ke MySQL otomatis.

---

### Step 3: Buat Database Baru

**Di HeidiSQL:**
1. Klik kanan pada koneksi → **Create new** → **Database**
2. **Name:** `blog_app` (sesuaikan dengan project)
3. **Collation:** `utf8mb4_unicode_ci` (untuk support emoji & Indonesia)
4. Klik **OK**

![Screenshot placeholder](../assets/images/12-create-database.png)

**Database berhasil dibuat!** ✅

---

## 🖥️ Alternatif: Setup Database di XAMPP

### Step 1: Start Apache & MySQL

1. Buka XAMPP Control Panel
2. Klik **Start** di Apache
3. Klik **Start** di MySQL
4. Tunggu sampai hijau

---

### Step 2: Buka phpMyAdmin

1. Buka browser
2. Ketik: `http://localhost/phpmyadmin`
3. Enter

---

### Step 3: Buat Database Baru

**Di phpMyAdmin:**
1. Klik tab **Databases**
2. **Database name:** `blog_app`
3. **Collation:** `utf8mb4_unicode_ci`
4. Klik **Create**

![Screenshot placeholder](../assets/images/12-phpmyadmin-create.png)

**Database berhasil dibuat!** ✅

---

## ⚙️ Bagian 2: Konfigurasi Database di Laravel

### File .env - File Konfigurasi Rahasia

**Lokasi:** Root project (`blog-app/.env`)

**Isi default:**
```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

---

### Penjelasan Setiap Line

#### APP Configuration

```env
APP_NAME=Laravel
```
**Arti:** Nama aplikasi (akan muncul di email, notif, dll)
**Ubah jadi:** `APP_NAME="Blog App"`

```env
APP_ENV=local
```
**Arti:** Environment (local/development/production)
**Jangan ubah** saat development

```env
APP_KEY=base64:...
```
**Arti:** Kunci enkripsi aplikasi
**JANGAN UBAH!** Auto-generate saat install Laravel

```env
APP_DEBUG=true
```
**Arti:** Tampilkan error detail
- `true` = Development (tampilkan error detail)
- `false` = Production (sembunyikan error)

```env
APP_URL=http://localhost
```
**Arti:** URL aplikasi
**Ubah jadi:** `APP_URL=http://localhost:8000`

---

#### Database Configuration

```env
DB_CONNECTION=mysql
```
**Arti:** Jenis database
**Pilihan:** `mysql`, `pgsql`, `sqlite`, `sqlsrv`
**Tetap:** `mysql` (kita pakai MySQL)

```env
DB_HOST=127.0.0.1
```
**Arti:** IP server database
- `127.0.0.1` = localhost (database di komputer yang sama)
- Untuk production: IP server database

```env
DB_PORT=3306
```
**Arti:** Port MySQL
**Default MySQL:** 3306
**Jangan ubah** kecuali port MySQL kamu berbeda

```env
DB_DATABASE=laravel
```
**Arti:** Nama database yang akan dipakai
**Ubah jadi:** `DB_DATABASE=blog_app` (sesuai database yang dibuat)

```env
DB_USERNAME=root
```
**Arti:** Username MySQL
**Default Laragon/XAMPP:** `root`

```env
DB_PASSWORD=
```
**Arti:** Password MySQL
**Default Laragon/XAMPP:** Kosong (tidak ada password)
**Jika ada password:** `DB_PASSWORD=your_password`

---

### Konfigurasi .env untuk Project Kita

**Edit file `.env`:**

```env
APP_NAME="Blog App"
APP_ENV=local
APP_KEY=base64:... # JANGAN UBAH
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_LEVEL=debug

# Database Configuration - YANG PENTING!
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_app
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_CONNECTION=log
CACHE_STORE=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

**Save file** (`Ctrl+S`)

---

## 🔍 Bagian 3: File config/database.php

Laravel membaca `.env` dan apply ke `config/database.php`

**Lokasi:** `config/database.php`

**Tidak perlu edit!** Laravel otomatis baca dari `.env`

**Tapi buka dan lihat struktur:**
```php
<?php

return [
    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            // ...
        ],
    ],
];
```

**Penjelasan:**
- `env('DB_HOST', '127.0.0.1')` = Baca dari .env, kalau tidak ada pakai default `127.0.0.1`

---

## ✅ Bagian 4: Test Koneksi Database

### Cara 1: Pakai Artisan Tinker

**Buka terminal:**
```bash
php artisan tinker
```

**Di Tinker, ketik:**
```php
DB::connection()->getPdo();
```

**Jika berhasil, output:**
```
=> PDO {#...
     +"inTransaction": false
     +"attributes": {
       "case": 0,
       ...
     }
   }
```

**Jika error:**
```
SQLSTATE[HY000] [1049] Unknown database 'blog_app'
```
→ Database belum dibuat atau nama salah di `.env`

**Keluar dari Tinker:** `exit`

---

### Cara 2: Buat Route Test

**Buat route di `routes/web.php`:**
```php
Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        $dbName = DB::connection()->getDatabaseName();
        return "Koneksi ke database '{$dbName}' berhasil! ✅";
    } catch (\Exception $e) {
        return "Koneksi gagal: " . $e->getMessage();
    }
});
```

**Test:** Buka `http://localhost:8000/test-db`

**Jika berhasil:**
```
Koneksi ke database 'blog_app' berhasil! ✅
```

**Jika gagal:**
```
Koneksi gagal: SQLSTATE[HY000] [1049] Unknown database 'blog_app'
```

---

### Cara 3: Buat Tabel Test Manual

**Di HeidiSQL atau phpMyAdmin, run SQL:**
```sql
USE blog_app;

CREATE TABLE test_connection (
    id INT PRIMARY KEY AUTO_INCREMENT,
    message VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO test_connection (message) VALUES ('Hello from MySQL!');
```

**Buat route untuk query:**
```php
Route::get('/test-query', function () {
    $result = DB::table('test_connection')->first();
    return $result ? $result->message : 'Tidak ada data';
});
```

**Test:** `http://localhost:8000/test-query`

**Output:** `Hello from MySQL!`

**Berhasil!** Database terhubung! 🎉

---

## 🔄 Bagian 5: Clear Config Cache

Setiap kali ubah `.env`, **clear config cache:**

```bash
php artisan config:clear
```

**Atau clear all cache:**
```bash
php artisan optimize:clear
```

Ini penting agar Laravel baca konfigurasi terbaru!

---

## 📊 Bagian 6: Database Clients yang Berguna

### 1. HeidiSQL (Laragon - Windows)
**Keuntungan:**
- ✅ Gratis
- ✅ Ringan
- ✅ Terintegrasi Laragon
- ✅ Support export/import

---

### 2. phpMyAdmin (XAMPP - All OS)
**Keuntungan:**
- ✅ Gratis
- ✅ Web-based
- ✅ Familiar
- ✅ Terintegrasi XAMPP

---

### 3. TablePlus (Mac/Windows)
**Website:** [tableplus.com](https://tableplus.com)
**Keuntungan:**
- ✅ Modern UI
- ✅ Fast
- ✅ Support banyak DB
- ❌ Paid (ada free version)

---

### 4. DBeaver (All OS)
**Website:** [dbeaver.io](https://dbeaver.io)
**Keuntungan:**
- ✅ Gratis & Open Source
- ✅ Support banyak DB
- ✅ Powerful features

---

### 5. MySQL Workbench (All OS)
**Website:** [mysql.com/products/workbench](https://www.mysql.com/products/workbench/)
**Keuntungan:**
- ✅ Gratis
- ✅ Official MySQL tool
- ✅ Visual ER Designer

---

## ⚠️ Troubleshooting

### Problem 1: Unknown database

**Error:**
```
SQLSTATE[HY000] [1049] Unknown database 'blog_app'
```

**Solusi:**
1. Cek database sudah dibuat di HeidiSQL/phpMyAdmin
2. Cek nama database di `.env` sama dengan yang dibuat
3. Cek spelling (case-sensitive di Linux!)

---

### Problem 2: Access denied

**Error:**
```
SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost'
```

**Solusi:**
1. Cek username di `.env` (default: `root`)
2. Cek password di `.env` (default: kosong)
3. Pastikan MySQL jalan
4. Restart MySQL service

---

### Problem 3: Connection refused

**Error:**
```
SQLSTATE[HY000] [2002] Connection refused
```

**Solusi:**
1. MySQL belum jalan - Start MySQL di Laragon/XAMPP
2. Cek port (default: 3306)
3. Cek host (default: 127.0.0.1)

---

### Problem 4: Config tidak update

**Solusi:**
```bash
php artisan config:clear
php artisan cache:clear
```

Laravel cache config, harus di-clear setelah ubah `.env`

---

### Problem 5: Driver not found

**Error:**
```
could not find driver
```

**Solusi:**
1. PHP extension `pdo_mysql` belum aktif
2. Edit `php.ini`
3. Uncomment: `;extension=pdo_mysql` jadi `extension=pdo_mysql`
4. Restart server

---

## 💡 Best Practices

### 1. Jangan Commit .env ke Git

**File `.gitignore` sudah include `.env`**

**Kenapa?**
- `.env` berisi password database
- `.env` berisi API keys
- `.env` berbeda tiap environment

**Yang di-commit:** `.env.example` (template tanpa value sensitif)

---

### 2. Pakai Environment Variables

**❌ Jangan hardcode:**
```php
// JANGAN INI!
$connection = mysqli_connect('127.0.0.1', 'root', '', 'blog_app');
```

**✅ Pakai config:**
```php
// PAKAI INI!
$connection = config('database.connections.mysql');
```

---

### 3. Different DB untuk Development & Production

**Development (.env):**
```env
DB_DATABASE=blog_app_dev
```

**Production (.env):**
```env
DB_DATABASE=blog_app_prod
DB_USERNAME=secure_user
DB_PASSWORD=strong_password_here
```

---

## 📝 Latihan

### Latihan 1: Setup Database

1. Buat database baru namanya `laravel_practice`
2. Konfigurasi di `.env`
3. Test koneksi dengan Tinker
4. Test koneksi dengan route `/test-db`

---

### Latihan 2: Buat Tabel Manual

Di database manager, buat tabel:
```sql
CREATE TABLE test_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO test_users (name, email) VALUES
('Budi', 'budi@mail.com'),
('Ani', 'ani@mail.com');
```

Query dari Laravel:
```php
Route::get('/users', function () {
    $users = DB::table('test_users')->get();
    return $users;
});
```

---

### Latihan 3: Eksplorasi Database Manager

1. Buka HeidiSQL atau phpMyAdmin
2. Explore features: export, import, query editor
3. Coba buat tabel via GUI
4. Insert data via GUI

---

## 📖 Summary

Di bab ini kamu sudah belajar:

- ✅ Database = Tempat penyimpanan data terstruktur
- ✅ Setup database di Laragon/XAMPP
- ✅ Buat database baru via HeidiSQL/phpMyAdmin
- ✅ File `.env` = Konfigurasi database Laravel
- ✅ `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` di `.env`
- ✅ Test koneksi dengan Tinker atau route
- ✅ `php artisan config:clear` setelah ubah `.env`
- ✅ Database clients: HeidiSQL, phpMyAdmin, TablePlus, DBeaver

**Database sudah terhubung! Siap untuk Migration!** 🎉

---

## 🎯 Next Chapter Preview

Di chapter berikutnya, kita akan belajar:
- ✅ Migration - Blueprint untuk database
- ✅ Membuat migration dengan Artisan
- ✅ Column types di migration
- ✅ `php artisan migrate`
- ✅ Rollback dan refresh database

**Saatnya buat tabel dengan Migration!** 🏗️

---

## 🔗 Referensi

- 📖 [Laravel Database Configuration](https://laravel.com/docs/12.x/database#configuration)
- 📖 [MySQL Documentation](https://dev.mysql.com/doc/)
- 🎥 [Laracasts - Database Setup](https://laracasts.com)

---

[⬅️ Bab 11: Artisan Helper](11-artisan-helper.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 13: Migration Dasar ➡️](13-migration-dasar.md)

---

<div align="center">

**Database terhubung! Config aman di .env!** ✅

**Lanjut ke Migration untuk buat struktur database!** 🏗️

</div>