<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================
 * MIGRATION: Create Categories Table
 * ============================================
 *
 * Categories adalah lookup table (tabel referensi).
 * Gunakan untuk mengelompokkan data.
 *
 * Contoh penggunaan:
 * - Blog: Technology, Lifestyle, Travel
 * - E-commerce: Electronics, Fashion, Books
 * - Inventory: Raw Materials, Finished Goods
 *
 * Karakteristik lookup table:
 * - Data sedikit (biasanya < 100 rows)
 * - Jarang berubah
 * - Digunakan untuk grouping/filtering
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            // Primary Key
            $table->id();
            // 🗣️ ID category (1, 2, 3, ...)

            // Category Information
            $table->string('name');
            // 🗣️ Nama category: "Technology", "Lifestyle"
            // VARCHAR(255) - cukup untuk nama category

            $table->string('slug')->unique();
            // 🗣️ URL-friendly version: "technology", "lifestyle"
            // Unique karena akan digunakan di URL
            // Contoh URL: /category/technology

            $table->text('description')->nullable();
            // 🗣️ Deskripsi category (optional)
            // TEXT karena bisa panjang
            // Nullable karena tidak wajib

            // Timestamps
            $table->timestamps();
            // 🗣️ created_at & updated_at

            // Indexes
            $table->index('name');
            // 🗣️ Untuk query: WHERE name LIKE '%tech%'

            $table->index('slug');
            // 🗣️ Untuk query: WHERE slug = 'technology'
            // (sudah ada unique, tapi index tetap bagus untuk performa)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
        // 🗣️ Hapus table saat rollback
    }
};

/**
 * ============================================
 * APA ITU SLUG?
 * ============================================
 *
 * Slug adalah versi URL-friendly dari title/name.
 *
 * Transformasi:
 * "Technology & Gadgets" → "technology-gadgets"
 * "Lifestyle Tips!"      → "lifestyle-tips"
 * "Travel & Adventure"   → "travel-adventure"
 *
 * Karakteristik slug:
 * - Lowercase semua
 * - Spasi jadi dash (-)
 * - Karakter spesial dihilangkan
 * - Harus unique (untuk URL)
 *
 * Kegunaan:
 * - SEO friendly URLs
 * - User-friendly (mudah dibaca)
 * - Tidak perlu expose ID ke user
 *
 * Contoh URL:
 * ❌ BAD: /category/1
 * ✅ GOOD: /category/technology
 *
 * Di Laravel, generate slug dengan:
 * use Illuminate\Support\Str;
 * $slug = Str::slug('Technology & Gadgets');
 * // Result: "technology-gadgets"
 *
 * ============================================
 * LOOKUP TABLE vs REGULAR TABLE
 * ============================================
 *
 * Lookup Table (seperti categories):
 * - Data sedikit (< 100 rows biasanya)
 * - Jarang insert/update
 * - Sering di-JOIN dengan table lain
 * - Digunakan untuk grouping/filtering
 *
 * Regular Table (seperti posts):
 * - Data banyak (bisa ribuan/jutaan)
 * - Sering insert/update
 * - Contains actual content/records
 *
 * Perbedaan struktural:
 * Categories:
 * - Simple structure (id, name, slug)
 * - Tidak banyak relationships
 * - Data relatif static
 *
 * Posts:
 * - Complex structure (title, body, author, etc.)
 * - Banyak relationships (user, category, tags)
 * - Data dinamis (sering berubah)
 *
 * ============================================
 * RELATIONSHIP: One-to-Many
 * ============================================
 *
 * Categories akan di-reference oleh table lain.
 * Contoh: posts table
 *
 * posts table:
 * +----+---------------+-------------+
 * | id | title         | category_id |
 * +----+---------------+-------------+
 * | 1  | Laravel Tips  | 1           | → Technology
 * | 2  | Cooking 101   | 2           | → Lifestyle
 * | 3  | Bali Trip     | 3           | → Travel
 * +----+---------------+-------------+
 *
 * categories table:
 * +----+------------+
 * | id | name       |
 * +----+------------+
 * | 1  | Technology |
 * | 2  | Lifestyle  |
 * | 3  | Travel     |
 * +----+------------+
 *
 * Relationship:
 * - 1 Category has many Posts
 * - 1 Post belongs to 1 Category
 *
 * Di Model:
 *
 * // Category.php
 * public function posts()
 * {
 *     return $this->hasMany(Post::class);
 * }
 *
 * // Post.php
 * public function category()
 * {
 *     return $this->belongsTo(Category::class);
 * }
 *
 * ============================================
 * EXAMPLE DATA
 * ============================================
 *
 * Contoh data untuk blog:
 *
 * INSERT INTO categories (name, slug, description) VALUES
 * ('Technology', 'technology', 'Latest tech news and tutorials'),
 * ('Lifestyle', 'lifestyle', 'Healthy living and life tips'),
 * ('Travel', 'travel', 'Travel guides and experiences'),
 * ('Food', 'food', 'Recipes and restaurant reviews'),
 * ('Business', 'business', 'Business tips and entrepreneurship');
 *
 * Contoh untuk e-commerce:
 *
 * INSERT INTO categories (name, slug) VALUES
 * ('Electronics', 'electronics'),
 * ('Fashion', 'fashion'),
 * ('Books', 'books'),
 * ('Home & Garden', 'home-garden'),
 * ('Sports', 'sports');
 *
 * ============================================
 * SEEDING CATEGORIES
 * ============================================
 *
 * Karena categories adalah lookup table,
 * biasanya kita seed (populate) dengan data awal.
 *
 * Buat seeder:
 * php artisan make:seeder CategorySeeder
 *
 * // CategorySeeder.php
 * public function run()
 * {
 *     $categories = [
 *         ['name' => 'Technology', 'slug' => 'technology'],
 *         ['name' => 'Lifestyle', 'slug' => 'lifestyle'],
 *         ['name' => 'Travel', 'slug' => 'travel'],
 *     ];
 *
 *     foreach ($categories as $category) {
 *         Category::create($category);
 *     }
 * }
 *
 * Run seeder:
 * php artisan db:seed --class=CategorySeeder
 *
 * ============================================
 * BEST PRACTICES
 * ============================================
 *
 * 1. ✅ Always make slug unique
 *    Untuk URL yang clean dan unique
 *
 * 2. ✅ Add description field
 *    Untuk SEO dan user clarity
 *    Make it nullable (optional)
 *
 * 3. ✅ Keep it simple
 *    Lookup table tidak perlu banyak columns
 *    Focus on: id, name, slug
 *
 * 4. ✅ Add indexes
 *    name & slug akan sering di-query
 *
 * 5. ✅ Seed with initial data
 *    Categories biasanya pre-defined
 *    Buat seeder untuk data awal
 *
 * 6. ⚠️ Think about hierarchy
 *    Apakah butuh parent_id untuk sub-categories?
 *    Contoh: Electronics > Laptops > Gaming Laptops
 *    Jika ya, tambah:
 *    $table->foreignId('parent_id')->nullable()->constrained('categories');
 *
 * ============================================
 * MIGRATION ORDER
 * ============================================
 *
 * Categories harus dibuat SEBELUM tables yang reference-nya
 *
 * ✅ CORRECT ORDER:
 * 1. 01_create_users_table.php
 * 2. 02_create_categories_table.php ← Ini duluan
 * 3. 03_create_posts_table.php      ← Ini setelahnya (reference categories)
 *
 * ❌ WRONG ORDER:
 * 1. create_posts_table.php (reference categories_id)
 * 2. create_categories_table.php ← ERROR! Categories belum ada
 *
 * Laravel run migrations berdasarkan timestamp di filename:
 * 2024_01_01_... → Run first
 * 2024_01_02_... → Run second
 * dst.
 */
