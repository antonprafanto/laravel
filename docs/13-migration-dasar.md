# Bab 13: Migration Dasar 🏗️

[⬅️ Bab 12: Pengenalan Database](12-database-intro.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 14: Seeder & Factory ➡️](14-seeder-factory.md)

---

## 🎯 Learning Objectives

Setelah mempelajari bab ini, kamu akan:
- ✅ Memahami apa itu migration dan mengapa penting
- ✅ Bisa membuat migration dengan Artisan
- ✅ Menguasai column types yang sering dipakai
- ✅ Bisa menjalankan migration dengan `php artisan migrate`
- ✅ Bisa rollback migration
- ✅ Memahami migration history

---

## 🎯 Analogi Sederhana: Migration seperti Blueprint Rumah

**Tanpa Migration (Manual SQL):**
```sql
-- Developer A buat tabel di komputernya
CREATE TABLE posts (
    id INT PRIMARY KEY,
    title VARCHAR(255)
);

-- Developer B tidak tahu struktur tabelnya
-- Production server beda struktur
-- Chaos! 😫
```

**Dengan Migration (Blueprint):**
```
┌─────────────────────────────────┐
│     📋 BLUEPRINT (Migration)     │
├─────────────────────────────────┤
│ Tahap 1: Buat tabel posts       │
│ Tahap 2: Tambah kolom author    │
│ Tahap 3: Buat tabel comments    │
└─────────────────────────────────┘

✅ Semua developer pakai blueprint sama
✅ Production pakai blueprint sama
✅ Bisa rollback kalau salah
✅ Ada history perubahan
```

**Migration** = Blueprint database yang bisa di-version control!

---

## 📚 Penjelasan: Apa itu Migration?

**Migration** = File PHP yang berisi instruksi untuk membuat/mengubah struktur database

**Keuntungan:**
- ✅ **Version control** - Track perubahan database
- ✅ **Reproducible** - Semua developer punya struktur sama
- ✅ **Rollback** - Bisa undo perubahan
- ✅ **Team-friendly** - Kolaborasi lebih mudah
- ✅ **Documentation** - History perubahan database

**Lokasi:** `database/migrations/`

---

### Mengapa Pakai Migration?

**❌ Tanpa Migration:**
```
Developer A: "Aku tambah kolom 'status' ke tabel posts"
Developer B: "Kok error? Kolom 'status' tidak ada!"
Developer A: "Oh iya, run SQL ini di database kamu..."
Developer B: Copy-paste SQL, eksekusi manual
→ Ribet dan error-prone! 😫
```

**✅ Dengan Migration:**
```
Developer A: Buat migration, commit ke Git
Developer B: Pull dari Git, run 'php artisan migrate'
→ Database Developer B otomatis update! ✨
```

---

## 🚀 Bagian 1: Membuat Migration Pertama

### Step 1: Lihat Migration Bawaan Laravel

**Cek folder:** `database/migrations/`

**Kamu akan lihat file:**
```
2024_01_01_000000_create_users_table.php
2024_01_01_100000_create_password_reset_tokens_table.php
2024_01_01_200000_create_cache_table.php
2024_01_01_300000_create_jobs_table.php
...
```

**Format nama:** `YYYY_MM_DD_HHMMSS_description.php`

**Timestamp** di depan memastikan migration jalan berurutan!

---

### Step 2: Buat Migration untuk Tabel Posts

```bash
php artisan make:migration create_posts_table
```

**Output:**
```
INFO  Migration [database/migrations/2024_01_15_123456_create_posts_table.php] created successfully.
```

**File dibuat dengan timestamp otomatis!**

---

### Step 3: Edit File Migration

Buka file yang baru dibuat: `database/migrations/2024_xx_xx_create_posts_table.php`

**Isi default:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
```

**Penjelasan:**
- `up()` = Yang dilakukan saat migrate (buat tabel)
- `down()` = Yang dilakukan saat rollback (hapus tabel)
- `$table->id()` = Kolom ID auto increment
- `$table->timestamps()` = Kolom created_at dan updated_at

---

### Step 4: Tambah Kolom

**Edit method `up()`:**
```php
public function up(): void
{
    Schema::create('posts', function (Blueprint $table) {
        $table->id();                              // ID auto increment
        $table->string('title');                   // VARCHAR(255)
        $table->string('slug')->unique();          // VARCHAR(255) UNIQUE
        $table->text('content');                   // TEXT
        $table->string('author', 100);             // VARCHAR(100)
        $table->boolean('is_published')->default(false); // BOOLEAN default FALSE
        $table->integer('views')->default(0);      // INT default 0
        $table->timestamps();                      // created_at, updated_at
    });
}
```

**Struktur tabel yang akan dibuat:**
```
posts
├── id (BIGINT, PRIMARY KEY, AUTO_INCREMENT)
├── title (VARCHAR 255)
├── slug (VARCHAR 255, UNIQUE)
├── content (TEXT)
├── author (VARCHAR 100)
├── is_published (BOOLEAN, default FALSE)
├── views (INT, default 0)
├── created_at (TIMESTAMP)
└── updated_at (TIMESTAMP)
```

---

### Step 5: Jalankan Migration

```bash
php artisan migrate
```

**Output:**
```
INFO  Running migrations.

2024_01_01_000000_create_users_table ........... 25ms DONE
2024_01_01_100000_create_password_reset_tokens_table ... 20ms DONE
2024_01_15_123456_create_posts_table ........... 30ms DONE
```

**Cek di database:** Tabel `posts` sudah dibuat! 🎉

---

## 📊 Bagian 2: Column Types yang Sering Dipakai

### String Types

```php
// VARCHAR(255) - Default
$table->string('name');

// VARCHAR dengan panjang custom
$table->string('phone', 20);

// TEXT - Untuk konten panjang
$table->text('description');

// LONGTEXT - Untuk konten sangat panjang (artikel, dll)
$table->longText('content');
```

---

### Numeric Types

```php
// INTEGER
$table->integer('age');

// BIGINT
$table->bigInteger('population');

// TINYINT (0-255)
$table->tinyInteger('status');

// FLOAT
$table->float('rating', 3, 2); // 3 digit total, 2 desimal (0.00 - 9.99)

// DOUBLE
$table->double('price', 10, 2); // 10 digit total, 2 desimal

// DECIMAL (untuk uang - lebih presisi)
$table->decimal('price', 10, 2); // 10 digit total, 2 desimal
```

---

### Date & Time Types

```php
// DATE - YYYY-MM-DD
$table->date('birth_date');

// TIME - HH:MM:SS
$table->time('opening_time');

// DATETIME - YYYY-MM-DD HH:MM:SS
$table->dateTime('published_at');

// TIMESTAMP
$table->timestamp('created_at');

// TIMESTAMPS - created_at & updated_at (auto)
$table->timestamps();
```

---

### Boolean Type

```php
// BOOLEAN (TRUE/FALSE atau 1/0)
$table->boolean('is_active');
$table->boolean('is_published')->default(false);
```

---

### Special Types

```php
// Primary Key (ID auto increment)
$table->id(); // Sama dengan: bigInteger('id')->autoIncrement()->primary()

// Foreign Key
$table->foreignId('user_id')->constrained();

// UUID (untuk ID unik string)
$table->uuid('uuid');

// JSON
$table->json('metadata');

// ENUM
$table->enum('status', ['draft', 'published', 'archived']);
```

---

## 🔧 Bagian 3: Column Modifiers

### Nullable

```php
// Boleh kosong (NULL)
$table->string('middle_name')->nullable();
```

---

### Default Value

```php
// Nilai default
$table->boolean('is_active')->default(true);
$table->integer('views')->default(0);
$table->string('status')->default('pending');
```

---

### Unique

```php
// Nilai harus unik
$table->string('email')->unique();
$table->string('slug')->unique();
```

---

### After (Urutan Kolom)

```php
// Letakkan setelah kolom tertentu
$table->string('phone')->after('email');
```

---

### Comment

```php
// Tambah comment
$table->string('code')->comment('Product code');
```

---

### Kombinasi Modifiers

```php
$table->string('email')->unique()->nullable()->comment('User email address');
$table->decimal('price', 10, 2)->default(0)->comment('Product price in IDR');
```

---

## 💡 Contoh Lengkap: Migration untuk E-Commerce

### Migration: categories

```bash
php artisan make:migration create_categories_table
```

```php
public function up(): void
{
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name', 100);
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}
```

---

### Migration: products

```bash
php artisan make:migration create_products_table
```

```php
public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->foreignId('category_id')->constrained()->onDelete('cascade');
        $table->string('name');
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->decimal('price', 12, 2);
        $table->decimal('discount_price', 12, 2)->nullable();
        $table->integer('stock')->default(0);
        $table->string('sku', 50)->unique();
        $table->boolean('is_available')->default(true);
        $table->integer('views')->default(0);
        $table->timestamps();
        $table->softDeletes(); // deleted_at untuk soft delete
    });
}
```

---

### Migration: orders

```bash
php artisan make:migration create_orders_table
```

```php
public function up(): void
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->string('order_number', 20)->unique();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->decimal('subtotal', 12, 2);
        $table->decimal('tax', 12, 2)->default(0);
        $table->decimal('shipping_cost', 12, 2)->default(0);
        $table->decimal('total', 12, 2);
        $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
        $table->text('shipping_address');
        $table->text('notes')->nullable();
        $table->timestamp('paid_at')->nullable();
        $table->timestamps();
    });
}
```

**Run migration:**
```bash
php artisan migrate
```

**3 tabel dengan relasi lengkap!** ✅

---

## 🔄 Bagian 4: Rollback Migration

### migrate:rollback - Undo Batch Terakhir

```bash
php artisan migrate:rollback
```

**Apa yang terjadi:**
- Method `down()` dari migration terakhir dijalankan
- Tabel dihapus (atau perubahan di-undo)

---

### migrate:rollback dengan Step

```bash
# Rollback 2 batch terakhir
php artisan migrate:rollback --step=2

# Rollback 1 file migration terakhir
php artisan migrate:rollback --step=1
```

---

### migrate:reset - Rollback Semua

```bash
php artisan migrate:reset
```

**Apa yang terjadi:**
- Semua migration di-rollback
- Database jadi kosong

---

### migrate:refresh - Rollback & Migrate Lagi

```bash
php artisan migrate:refresh
```

**Sama dengan:**
```bash
php artisan migrate:reset
php artisan migrate
```

**Berguna untuk:** Reset database saat development

---

### migrate:fresh - Drop Semua Table & Migrate

```bash
php artisan migrate:fresh
```

**Apa yang terjadi:**
- Drop semua table (tanpa rollback)
- Run semua migration dari awal

**Lebih cepat dari `refresh`** tapi lebih destructive!

---

## 📋 Bagian 5: Migration Status & History

### migrate:status - Cek Status Migration

```bash
php artisan migrate:status
```

**Output:**
```
Migration name                             Batch  Status
2024_01_01_000000_create_users_table ..... 1      Ran
2024_01_01_100000_create_cache_table ..... 1      Ran
2024_01_15_123456_create_posts_table ..... 2      Ran
2024_01_16_120000_create_categories_table  -      Pending
```

**Penjelasan:**
- **Ran** = Sudah dijalankan
- **Pending** = Belum dijalankan
- **Batch** = Nomor batch (untuk rollback)

---

### Tabel migrations

Laravel track migration di tabel `migrations`:

**Cek di database:**
```sql
SELECT * FROM migrations;
```

**Isi:**
```
| id | migration                           | batch |
|----|-------------------------------------|-------|
| 1  | 2024_01_01_000000_create_users...  | 1     |
| 2  | 2024_01_01_100000_create_cache...  | 1     |
| 3  | 2024_01_15_123456_create_posts...  | 2     |
```

**Jangan edit tabel ini manual!**

---

## 🔨 Bagian 6: Modifying Existing Tables

### Tambah Kolom ke Tabel Existing

```bash
php artisan make:migration add_status_to_posts_table
```

**Edit migration:**
```php
public function up(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->string('status')->default('draft')->after('content');
    });
}

public function down(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}
```

**Run:**
```bash
php artisan migrate
```

---

### Ubah Kolom

**Perlu package `doctrine/dbal`:**
```bash
composer require doctrine/dbal
```

**Migration:**
```bash
php artisan make:migration modify_posts_title_column
```

```php
public function up(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->string('title', 500)->change(); // Ubah dari 255 jadi 500
    });
}

public function down(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->string('title', 255)->change();
    });
}
```

---

### Hapus Kolom

```php
public function up(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->dropColumn('author');
    });
}

public function down(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->string('author', 100);
    });
}
```

---

### Rename Kolom

```php
public function up(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->renameColumn('author', 'author_name');
    });
}

public function down(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->renameColumn('author_name', 'author');
    });
}
```

---

## 📝 Latihan

### Latihan 1: Buat Migration untuk Blog

Buat migration untuk tabel-tabel ini:

**1. categories:**
- id
- name (string 100)
- slug (string, unique)
- description (text, nullable)
- timestamps

**2. posts:**
- id
- category_id (foreign key)
- title (string)
- slug (string, unique)
- excerpt (text, nullable)
- content (text)
- featured_image (string, nullable)
- is_published (boolean, default false)
- published_at (timestamp, nullable)
- views (integer, default 0)
- timestamps

**3. comments:**
- id
- post_id (foreign key)
- user_id (foreign key)
- content (text)
- is_approved (boolean, default false)
- timestamps

---

### Latihan 2: Run & Check

1. Buat ketiga migration
2. Run `php artisan migrate`
3. Cek di database apakah tabel sudah dibuat
4. Run `php artisan migrate:status`
5. Cek isi tabel `migrations`

---

### Latihan 3: Rollback & Refresh

1. Rollback 1 batch terakhir
2. Cek tabel yang hilang
3. Migrate lagi
4. Coba `migrate:refresh`
5. Coba `migrate:fresh`

---

## ⚠️ Troubleshooting

### Problem 1: Syntax error in migration

**Error:** `Syntax error, unexpected 'string'`

**Solusi:**
1. Cek semicolon (`;`) di akhir line
2. Cek kurung `{ }` dan `( )` balance
3. Cek typo di method name

---

### Problem 2: Table already exists

**Error:** `SQLSTATE[42S01]: Base table or view already exists`

**Solusi:**
1. Tabel sudah ada di database - hapus manual atau rollback
2. Atau rename tabelnya di migration

---

### Problem 3: Foreign key constraint fails

**Error:** `Cannot add foreign key constraint`

**Solusi:**
1. Pastikan tabel parent sudah dibuat dulu
2. Migration parent harus punya timestamp lebih awal
3. Atau run migration secara manual berurutan

---

### Problem 4: Class not found

**Error:** `Class 'Schema' not found`

**Solusi:** Tambahkan `use` statement:
```php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
```

---

## 📖 Summary

Di bab ini kamu sudah belajar:

- ✅ Migration = Blueprint database yang bisa di-version control
- ✅ `php artisan make:migration` untuk buat migration
- ✅ Method `up()` untuk migrate, `down()` untuk rollback
- ✅ Column types: string, text, integer, boolean, timestamp, dll
- ✅ Modifiers: nullable(), default(), unique(), after()
- ✅ `php artisan migrate` untuk run migration
- ✅ `php artisan migrate:rollback` untuk undo
- ✅ `php artisan migrate:refresh` untuk reset & migrate lagi
- ✅ `php artisan migrate:status` untuk cek status

**Migration membuat database management jadi mudah!** 🏗️

---

## 🎯 Next Chapter Preview

Di chapter berikutnya, kita akan belajar:
- ✅ Seeder - Isi data dummy ke database
- ✅ Factory - Generate fake data otomatis
- ✅ `php artisan db:seed`
- ✅ Kombinasi Migration + Seeder

**Saatnya isi database dengan data!** 🌱

---

## 🔗 Referensi

- 📖 [Laravel Migrations](https://laravel.com/docs/12.x/migrations)
- 📖 [Available Column Types](https://laravel.com/docs/12.x/migrations#available-column-types)
- 📖 [Column Modifiers](https://laravel.com/docs/12.x/migrations#column-modifiers)

---

[⬅️ Bab 12: Pengenalan Database](12-database-intro.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 14: Seeder & Factory ➡️](14-seeder-factory.md)

---

<div align="center">

**Migration sudah dikuasai! Database structure under control!** ✅

**Lanjut ke Seeder untuk isi data dummy!** 🌱

</div>