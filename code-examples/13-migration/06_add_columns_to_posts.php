<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================
 * MIGRATION: Add Columns to Posts Table
 * ============================================
 *
 * Migration ini mendemonstrasikan cara MODIFY table yang sudah ada.
 *
 * Use case:
 * - Menambah column baru ke table existing
 * - Tidak perlu drop & recreate table
 * - Data existing tetap aman
 *
 * Contoh scenario:
 * - Table posts sudah ada dengan 1000 posts
 * - Kita ingin tambah fitur: views count, featured posts, soft delete
 * - Buat migration baru untuk add columns
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambah 3 fitur baru ke posts table:
     * 1. views - Track berapa kali post dilihat
     * 2. is_featured - Mark post sebagai featured/highlight
     * 3. soft deletes - Hapus post tapi bisa di-restore
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // 🗣️ Pakai table() bukan create()
            // Karena table sudah ada, kita cuma modify

            // Add views column
            $table->integer('views')->default(0)->after('body');
            // 🗣️ Counter untuk page views
            // Default 0 (post baru = 0 views)
            // after('body') → letakkan setelah column 'body'

            // Add is_featured column
            $table->boolean('is_featured')->default(false);
            // 🗣️ Flag untuk featured posts
            // Default false (post biasa)
            // Bisa di-display berbeda di homepage

            // Add soft deletes
            $table->softDeletes();
            // 🗣️ Menambah column deleted_at (TIMESTAMP NULL)
            // Untuk soft delete feature
        });
    }

    /**
     * Reverse the migrations.
     *
     * Rollback: Hapus columns yang ditambahkan
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Drop columns in reverse order
            $table->dropSoftDeletes();
            // 🗣️ Hapus column deleted_at

            $table->dropColumn(['views', 'is_featured']);
            // 🗣️ Hapus multiple columns sekaligus
            // Bisa juga satu-satu:
            // $table->dropColumn('views');
            // $table->dropColumn('is_featured');
        });
    }
};

/**
 * ============================================
 * SCHEMA::TABLE vs SCHEMA::CREATE
 * ============================================
 *
 * Schema::create() - Untuk table BARU
 * Schema::create('posts', function (Blueprint $table) {
 *     $table->id();
 *     $table->string('title');
 *     // ...
 * });
 *
 * Schema::table() - Untuk MODIFY table existing
 * Schema::table('posts', function (Blueprint $table) {
 *     $table->integer('views')->default(0);
 *     // Add column ke table yang sudah ada
 * });
 *
 * ============================================
 * AFTER() METHOD
 * ============================================
 *
 * after() menentukan posisi column baru.
 *
 * Structure sebelum:
 * posts: id | title | body | created_at | updated_at
 *
 * $table->integer('views')->after('body');
 *
 * Structure setelah:
 * posts: id | title | body | views | created_at | updated_at
 *                            ↑ Inserted here!
 *
 * Tanpa after():
 * Column ditambahkan di akhir (setelah updated_at)
 *
 * after() bagus untuk:
 * - Organization yang lebih baik
 * - Columns related ditaruh berdekatan
 * - Readability di phpMyAdmin/database tools
 *
 * ⚠️ Note: after() tidak selalu supported di semua database
 * MySQL: ✅ Supported
 * PostgreSQL: ❌ Not supported (akan ignore after())
 *
 * ============================================
 * DEFAULT VALUES
 * ============================================
 *
 * default() memberikan nilai default untuk column baru.
 *
 * Kenapa penting?
 * Karena table sudah punya data existing!
 *
 * Example:
 * posts table punya 1000 rows existing
 * Kita add column: views (integer)
 *
 * Tanpa default:
 * +----+---------+-------+
 * | id | title   | views |
 * +----+---------+-------+
 * | 1  | Post 1  | NULL  | ← NULL karena tidak ada nilai
 * | 2  | Post 2  | NULL  |
 * +----+---------+-------+
 *
 * Dengan default(0):
 * +----+---------+-------+
 * | id | title   | views |
 * +----+---------+-------+
 * | 1  | Post 1  | 0     | ← Otomatis isi 0
 * | 2  | Post 2  | 0     |
 * +----+---------+-------+
 *
 * Best practice:
 * ✅ Always provide default untuk column NOT NULL
 * ✅ Atau make it nullable jika memang boleh kosong
 *
 * ============================================
 * SOFT DELETES
 * ============================================
 *
 * softDeletes() adds column: deleted_at (TIMESTAMP NULL)
 *
 * Normal delete (hard delete):
 * $post->delete();
 * → Row dihapus permanent dari database
 * → Data hilang selamanya
 *
 * Soft delete:
 * $post->delete();
 * → Row TIDAK dihapus
 * → Column deleted_at diisi dengan timestamp
 * → Row "hidden" dari query normal
 *
 * Benefits:
 * - Data bisa di-restore
 * - Audit trail (siapa hapus kapan)
 * - "Recycle bin" feature
 * - Prevent accidental deletion
 *
 * Table structure:
 * +----+---------+-------------+---------------------+
 * | id | title   | deleted_at  |
 * +----+---------+-------------+
 * | 1  | Post 1  | NULL        | ← Active (visible)
 * | 2  | Post 2  | 2024-01-15  | ← Deleted (hidden)
 * | 3  | Post 3  | NULL        | ← Active (visible)
 * +----+---------+-------------+
 *
 * Di Model, tambahkan trait:
 * use Illuminate\Database\Eloquent\SoftDeletes;
 *
 * class Post extends Model
 * {
 *     use SoftDeletes;
 * }
 *
 * Usage:
 * $post->delete();              // Soft delete
 * $post->restore();             // Un-delete
 * $post->forceDelete();         // Permanent delete
 *
 * Post::all();                  // Only active posts
 * Post::withTrashed()->get();   // Include soft deleted
 * Post::onlyTrashed()->get();   // Only soft deleted
 *
 * ============================================
 * VIEWS COUNTER IMPLEMENTATION
 * ============================================
 *
 * Setelah add column views, implementasi di Controller:
 *
 * public function show(Post $post)
 * {
 *     // Increment views setiap kali post dibuka
 *     $post->increment('views');
 *
 *     return view('posts.show', compact('post'));
 * }
 *
 * Or dengan protection (1 view per session):
 * public function show(Request $request, Post $post)
 * {
 *     // Check session
 *     $viewed = $request->session()->get('viewed_posts', []);
 *
 *     if (!in_array($post->id, $viewed)) {
 *         // Increment only if not viewed in this session
 *         $post->increment('views');
 *
 *         // Mark as viewed
 *         $viewed[] = $post->id;
 *         $request->session()->put('viewed_posts', $viewed);
 *     }
 *
 *     return view('posts.show', compact('post'));
 * }
 *
 * Display views:
 * <p>{{ $post->views }} views</p>
 *
 * Popular posts:
 * $popular = Post::orderBy('views', 'desc')->limit(5)->get();
 *
 * ============================================
 * FEATURED POSTS IMPLEMENTATION
 * ============================================
 *
 * is_featured untuk highlight posts di homepage.
 *
 * Set featured:
 * $post->update(['is_featured' => true]);
 *
 * Get featured posts:
 * $featured = Post::where('is_featured', true)
 *                ->limit(3)
 *                ->get();
 *
 * Di Blade (homepage):
 * @if($featuredPosts->count() > 0)
 *     <section class="featured">
 *         <h2>Featured Posts</h2>
 *         @foreach($featuredPosts as $post)
 *             <article class="featured-post">
 *                 <h3>{{ $post->title }}</h3>
 *                 <span class="badge">Featured</span>
 *             </article>
 *         @endforeach
 *     </section>
 * @endif
 *
 * Scope untuk reusability:
 * // Post Model
 * public function scopeFeatured($query)
 * {
 *     return $query->where('is_featured', true);
 * }
 *
 * Usage:
 * $featured = Post::featured()->latest()->limit(3)->get();
 *
 * ============================================
 * DROPCOL

UMN METHOD
 * ============================================
 *
 * dropColumn() untuk rollback (hapus column).
 *
 * Drop single column:
 * $table->dropColumn('views');
 *
 * Drop multiple columns:
 * $table->dropColumn(['views', 'is_featured']);
 *
 * Drop with specific method:
 * $table->dropSoftDeletes();  // Drop deleted_at
 * $table->dropTimestamps();   // Drop created_at & updated_at
 * $table->dropRememberToken(); // Drop remember_token
 *
 * ⚠️ WARNING: Dropping column = data hilang!
 * Pastikan backup database sebelum run migration destructive.
 *
 * ============================================
 * MIGRATION BEST PRACTICES
 * ============================================
 *
 * 1. ✅ One migration per feature/change
 *    Jangan gabung 10 perubahan dalam 1 migration
 *    Lebih baik: 1 migration = 1 tujuan
 *
 * 2. ✅ Always provide down() method
 *    Untuk rollback jika ada masalah
 *
 * 3. ✅ Use descriptive names
 *    "add_views_to_posts_table" ✅
 *    "update_posts" ❌
 *
 * 4. ✅ Test rollback
 *    php artisan migrate
 *    php artisan migrate:rollback
 *    Pastikan both directions work!
 *
 * 5. ✅ Provide default values
 *    Untuk column NOT NULL di table existing
 *
 * 6. ⚠️ Never modify old migrations
 *    Jika migration sudah run di production
 *    Buat migration baru untuk changes
 *
 * 7. ⚠️ Backup before destructive changes
 *    DROP, RENAME, MODIFY = risky
 *    Always backup first!
 *
 * ============================================
 * TESTING
 * ============================================
 *
 * # Run migration
 * php artisan migrate
 *
 * # Check di database
 * mysql> DESCRIBE posts;
 *
 * # Expected to see new columns:
 * +-------------+------------------+------+-----+---------+
 * | Field       | Type             | Null | Key | Default |
 * +-------------+------------------+------+-----+---------+
 * | views       | int              | NO   |     | 0       |
 * | is_featured | tinyint(1)       | NO   |     | 0       |
 * | deleted_at  | timestamp        | YES  |     | NULL    |
 * +-------------+------------------+------+-----+---------+
 *
 * # Test rollback
 * php artisan migrate:rollback
 *
 * # Columns should be removed
 */
