<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================
 * MIGRATION: Create Tags Table
 * ============================================
 *
 * Tags adalah lookup table untuk many-to-many relationship.
 *
 * Bedanya dengan Categories:
 * - Categories: 1 post = 1 category (One-to-Many)
 * - Tags: 1 post = banyak tags (Many-to-Many)
 *
 * Contoh:
 * Post "Laravel Tips for Beginners"
 * - Category: Technology (hanya 1)
 * - Tags: Laravel, PHP, Tutorial, Beginners (bisa banyak)
 *
 * Tags membantu:
 * - Filtering lebih detail
 * - Related content discovery
 * - SEO (keywords)
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Tag Information
            $table->string('name', 50);
            // 🗣️ Nama tag: "Laravel", "PHP", "Tutorial"
            // VARCHAR(50) karena tags biasanya pendek

            $table->string('slug', 50)->unique();
            // 🗣️ URL version: "laravel", "php", "tutorial"
            // Unique untuk URL /tag/laravel

            $table->string('color', 7)->nullable();
            // 🗣️ Warna tag untuk UI: "#3b82f6" (hex color)
            // VARCHAR(7) cukup untuk hex (#RRGGBB)
            // Nullable karena optional

            // Usage Counter (optional but useful)
            $table->integer('posts_count')->default(0);
            // 🗣️ Berapa post pakai tag ini?
            // Untuk sorting: show popular tags first

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('name');
            // 🗣️ Untuk search/autocomplete tags
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};

/**
 * ============================================
 * MANY-TO-MANY RELATIONSHIP
 * ============================================
 *
 * Tags menggunakan many-to-many relationship:
 * - 1 Post bisa punya banyak Tags
 * - 1 Tag bisa dimiliki banyak Posts
 *
 * Butuh 3 tables:
 * 1. posts (data posts)
 * 2. tags (data tags) ← Table ini
 * 3. post_tag (pivot table - penghubung)
 *
 * Example:
 *
 * posts:
 * +----+---------------------+
 * | id | title               |
 * +----+---------------------+
 * | 1  | Laravel Tutorial    |
 * | 2  | Vue.js Guide        |
 * +----+---------------------+
 *
 * tags:
 * +----+-----------+
 * | id | name      |
 * +----+-----------+
 * | 1  | Laravel   |
 * | 2  | PHP       |
 * | 3  | JavaScript|
 * +----+-----------+
 *
 * post_tag (pivot):
 * +---------+--------+
 * | post_id | tag_id |
 * +---------+--------+
 * | 1       | 1      | → Post 1 has Tag 1 (Laravel)
 * | 1       | 2      | → Post 1 has Tag 2 (PHP)
 * | 2       | 3      | → Post 2 has Tag 3 (JavaScript)
 * +---------+--------+
 *
 * Di Model:
 *
 * // Post.php
 * public function tags()
 * {
 *     return $this->belongsToMany(Tag::class);
 * }
 *
 * // Tag.php
 * public function posts()
 * {
 *     return $this->belongsToMany(Post::class);
 * }
 *
 * Usage:
 * $post->tags; // Get all tags untuk post
 * $tag->posts; // Get all posts untuk tag
 *
 * $post->tags()->attach([1, 2, 3]); // Add tags
 * $post->tags()->detach([1]); // Remove tag
 * $post->tags()->sync([1, 2]); // Replace all tags
 *
 * ============================================
 * POSTS_COUNT COLUMN
 * ============================================
 *
 * posts_count adalah counter column.
 * Gunanya: Track berapa post pakai tag ini.
 *
 * Kegunaan:
 * - Display popular tags
 * - Sort tags by usage
 * - Show tag cloud dengan size berbeda
 *
 * Update otomatis dengan Eloquent Events:
 *
 * // Post.php Model
 * protected static function booted()
 * {
 *     static::created(function ($post) {
 *         if ($post->tags) {
 *             foreach ($post->tags as $tag) {
 *                 $tag->increment('posts_count');
 *             }
 *         }
 *     });
 *
 *     static::deleted(function ($post) {
 *         foreach ($post->tags as $tag) {
 *             $tag->decrement('posts_count');
 *         }
 *     });
 * }
 *
 * Atau gunakan Laravel's withCount():
 * $tags = Tag::withCount('posts')->get();
 * $tags[0]->posts_count; // Otomatis calculated
 *
 * ============================================
 * COLOR COLUMN
 * ============================================
 *
 * color untuk styling tags di UI.
 *
 * Contoh penggunaan:
 * - Laravel tag → blue (#3b82f6)
 * - PHP tag → purple (#8b5cf6)
 * - JavaScript tag → yellow (#eab308)
 *
 * Di Blade template:
 * <span class="tag" style="background-color: {{ $tag->color }}">
 *     {{ $tag->name }}
 * </span>
 *
 * Atau dengan Tailwind classes:
 * <span class="px-3 py-1 rounded" style="background: {{ $tag->color }}">
 *     #{{ $tag->name }}
 * </span>
 *
 * Hex color format:
 * #RRGGBB
 * - # prefix
 * - RR = Red (00-FF)
 * - GG = Green (00-FF)
 * - BB = Blue (00-FF)
 *
 * Examples:
 * #FF0000 = Pure Red
 * #00FF00 = Pure Green
 * #0000FF = Pure Blue
 * #FFFFFF = White
 * #000000 = Black
 *
 * ============================================
 * EXAMPLE DATA
 * ============================================
 *
 * Contoh tags untuk blog programming:
 *
 * INSERT INTO tags (name, slug, color) VALUES
 * ('Laravel', 'laravel', '#FF2D20'),
 * ('PHP', 'php', '#8993BE'),
 * ('JavaScript', 'javascript', '#F7DF1E'),
 * ('Vue.js', 'vuejs', '#42B883'),
 * ('Tutorial', 'tutorial', '#3B82F6'),
 * ('Beginners', 'beginners', '#10B981');
 *
 * Untuk lifestyle blog:
 *
 * INSERT INTO tags (name, slug, color) VALUES
 * ('Healthy', 'healthy', '#10B981'),
 * ('Recipe', 'recipe', '#F59E0B'),
 * ('Fitness', 'fitness', '#EF4444'),
 * ('Mindfulness', 'mindfulness', '#8B5CF6'),
 * ('DIY', 'diy', '#EC4899');
 *
 * ============================================
 * TAG CLOUD
 * ============================================
 *
 * Tag cloud adalah visualisasi tags berdasarkan popularity.
 * Tag yang sering dipakai = font lebih besar.
 *
 * Example implementation:
 *
 * // Controller
 * $tags = Tag::where('posts_count', '>', 0)
 *            ->orderBy('posts_count', 'desc')
 *            ->limit(20)
 *            ->get();
 *
 * // Blade
 * <div class="tag-cloud">
 *     @foreach($tags as $tag)
 *         @php
 *             // Calculate font size based on posts_count
 *             $min = 12;
 *             $max = 32;
 *             $maxCount = $tags->max('posts_count');
 *             $fontSize = $min + (($tag->posts_count / $maxCount) * ($max - $min));
 *         @endphp
 *
 *         <a href="/tag/{{ $tag->slug }}"
 *            style="font-size: {{ $fontSize }}px; color: {{ $tag->color }}">
 *             #{{ $tag->name }}
 *         </a>
 *     @endforeach
 * </div>
 *
 * Result: Popular tags = bigger, rare tags = smaller
 *
 * ============================================
 * AUTOCOMPLETE SEARCH
 * ============================================
 *
 * Tags biasanya pakai autocomplete untuk UX yang baik.
 *
 * // API endpoint
 * Route::get('/api/tags/search', function (Request $request) {
 *     $query = $request->input('q');
 *
 *     $tags = Tag::where('name', 'LIKE', "%{$query}%")
 *                ->limit(10)
 *                ->get(['id', 'name', 'slug']);
 *
 *     return response()->json($tags);
 * });
 *
 * // JavaScript (dengan Select2 atau autocomplete library)
 * $('#tags').select2({
 *     ajax: {
 *         url: '/api/tags/search',
 *         data: function (params) {
 *             return { q: params.term };
 *         }
 *     }
 * });
 *
 * ============================================
 * BEST PRACTICES
 * ============================================
 *
 * 1. ✅ Keep tag names short
 *    Max 50 characters (lebih pendek lebih baik)
 *
 * 2. ✅ Normalize tag names
 *    "laravel" bukan "Laravel" atau "LARAVEL"
 *    Lowercase semua untuk consistency
 *
 * 3. ✅ Prevent duplicate tags
 *    Check sebelum create:
 *    Tag::firstOrCreate(['slug' => Str::slug($name)], [...]);
 *
 * 4. ✅ Add color for better UI
 *    Visual distinction membantu user
 *
 * 5. ✅ Track usage dengan posts_count
 *    Untuk analytics dan sorting
 *
 * 6. ✅ Limit number of tags per post
 *    Terlalu banyak tags = tidak efektif
 *    Recommended: 3-5 tags per post
 *
 * 7. ⚠️ Consider tag moderation
 *    Untuk public sites, admin should approve new tags
 *    Prevent spam dan maintain quality
 */
