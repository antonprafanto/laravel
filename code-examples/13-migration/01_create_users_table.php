<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================
 * MIGRATION: Create Users Table
 * ============================================
 *
 * Table ini adalah foundation dari aplikasi.
 * Hampir semua aplikasi butuh users table untuk:
 * - Authentication (login/logout)
 * - Authorization (permissions)
 * - Tracking (siapa yang buat/edit data)
 *
 * Laravel menyediakan users migration by default,
 * tapi ini contoh jika kita buat manual.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Method ini dijalankan saat: php artisan migrate
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // Primary Key - Auto increment ID
            $table->id();
            // 🗣️ Seperti nomor antrian yang otomatis bertambah

            // Basic User Information
            $table->string('name');
            // 🗣️ Nama lengkap user (VARCHAR 255)

            $table->string('email')->unique();
            // 🗣️ Email harus unique (tidak boleh duplikat)
            // Seperti KTP - setiap orang cuma punya 1

            $table->timestamp('email_verified_at')->nullable();
            // 🗣️ Kapan user verify email? (bisa kosong)

            $table->string('password');
            // 🗣️ Password (sudah di-hash, bukan plain text!)

            $table->rememberToken();
            // 🗣️ Token untuk "Remember Me" saat login

            // Timestamps - Laravel auto-manage ini
            $table->timestamps();
            // 🗣️ Otomatis tambah created_at & updated_at

            // Indexes untuk performa
            $table->index('email');
            // 🗣️ Biar query WHERE email='...' jadi cepat
        });
    }

    /**
     * Reverse the migrations.
     *
     * Method ini dijalankan saat: php artisan migrate:rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        // 🗣️ Hapus table jika rollback
    }
};

/**
 * ============================================
 * PENJELASAN COLUMN TYPES
 * ============================================
 *
 * id()
 * - Type: BIGINT UNSIGNED AUTO_INCREMENT
 * - Primary Key: YES
 * - Auto Increment: YES
 * - Nullable: NO
 * - Gunakan untuk: Primary key semua table
 *
 * string('name')
 * - Type: VARCHAR(255)
 * - Default length: 255 characters
 * - Gunakan untuk: Nama, title, short text
 * - Custom length: string('code', 10) → VARCHAR(10)
 *
 * string()->unique()
 * - Sama seperti string()
 * - Plus: Menambahkan UNIQUE constraint
 * - Artinya: Tidak boleh ada nilai duplikat
 * - Contoh: email, username, slug
 *
 * timestamp()->nullable()
 * - Type: TIMESTAMP
 * - Nullable: YES (boleh kosong)
 * - Gunakan untuk: Tanggal yang optional
 * - Contoh: email_verified_at, published_at
 *
 * timestamps()
 * - Shortcut untuk tambah 2 columns:
 *   1. created_at (TIMESTAMP)
 *   2. updated_at (TIMESTAMP)
 * - Laravel otomatis isi value-nya
 * - Sangat berguna untuk tracking
 *
 * rememberToken()
 * - Type: VARCHAR(100) NULLABLE
 * - Khusus untuk Laravel authentication
 * - Store token "Remember Me"
 *
 * ============================================
 * INDEXES & PERFORMANCE
 * ============================================
 *
 * Kenapa pakai index?
 * - Biar query jadi lebih cepat
 * - Bayangkan seperti index di buku
 * - Cari kata di index → langsung ke halaman yang tepat
 * - Tanpa index → harus baca semua halaman
 *
 * Kapan pakai index?
 * ✅ Column yang sering di-WHERE
 * ✅ Column yang sering di-ORDER BY
 * ✅ Column dengan UNIQUE constraint
 * ✅ Foreign keys
 *
 * Contoh:
 * WHERE email = 'john@example.com' → butuh index di email
 * ORDER BY created_at DESC → butuh index di created_at
 *
 * ⚠️ Jangan berlebihan pakai index:
 * - Terlalu banyak index → INSERT/UPDATE jadi lambat
 * - Hanya index column yang SERING digunakan
 *
 * ============================================
 * UNIQUE CONSTRAINT
 * ============================================
 *
 * unique() artinya: Nilai tidak boleh duplikat
 *
 * Contoh di users table:
 * - email MUST be unique
 * - Tidak boleh 2 user pakai email yang sama
 *
 * Database akan reject dengan error jika insert duplikat:
 * ❌ INSERT INTO users (email) VALUES ('john@mail.com');
 * ❌ INSERT INTO users (email) VALUES ('john@mail.com'); // ERROR!
 *
 * Gunakan unique() untuk:
 * - Email addresses
 * - Usernames
 * - Slug (URL-friendly titles)
 * - Product SKU/codes
 * - Any identifier yang harus unik
 *
 * ============================================
 * NULLABLE vs NOT NULL
 * ============================================
 *
 * Default: Semua column adalah NOT NULL (wajib diisi)
 * nullable(): Column boleh kosong (NULL)
 *
 * Contoh:
 * $table->string('name');              // Wajib diisi
 * $table->string('phone')->nullable(); // Boleh kosong
 *
 * email_verified_at adalah nullable karena:
 * - Saat user baru register → belum verify → NULL
 * - Setelah klik link verify → diisi dengan timestamp
 *
 * ============================================
 * TESTING MIGRATION
 * ============================================
 *
 * # Run migration
 * php artisan migrate
 *
 * # Check di database
 * mysql> DESCRIBE users;
 *
 * # Expected structure:
 * +-------------------+-----------------+------+-----+---------+
 * | Field             | Type            | Null | Key | Default |
 * +-------------------+-----------------+------+-----+---------+
 * | id                | bigint unsigned | NO   | PRI | NULL    |
 * | name              | varchar(255)    | NO   |     | NULL    |
 * | email             | varchar(255)    | NO   | UNI | NULL    |
 * | email_verified_at | timestamp       | YES  |     | NULL    |
 * | password          | varchar(255)    | NO   |     | NULL    |
 * | remember_token    | varchar(100)    | YES  |     | NULL    |
 * | created_at        | timestamp       | YES  |     | NULL    |
 * | updated_at        | timestamp       | YES  |     | NULL    |
 * +-------------------+-----------------+------+-----+---------+
 *
 * # Test rollback
 * php artisan migrate:rollback
 *
 * # Expected: users table terhapus
 *
 * ============================================
 * BEST PRACTICES
 * ============================================
 *
 * 1. ✅ Always use id() untuk primary key
 *    Jangan manual create integer('id')->primary()
 *
 * 2. ✅ Always use timestamps()
 *    Tracking created_at/updated_at sangat berguna
 *
 * 3. ✅ Email should always be unique
 *    Prevent duplicate accounts
 *
 * 4. ✅ Never store plain text passwords
 *    Laravel Hash::make() otomatis hash password
 *
 * 5. ✅ Use nullable() untuk optional fields
 *    Lebih baik NULL daripada empty string
 *
 * 6. ✅ Add index untuk columns yang sering di-query
 *    Performance improvement significant
 */
