<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================
 * MIGRATION: Create Post Tag Pivot Table
 * ============================================
 *
 * Pivot table untuk Many-to-Many relationship:
 * - 1 Post bisa punya banyak Tags
 * - 1 Tag bisa dimiliki banyak Posts
 *
 * Naming convention: {singular1}_{singular2} (alphabetical order)
 * ✅ post_tag
 * ❌ tag_post (salah urutan abjad)
 * ❌ posts_tags (plural)
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('post_tag', function (Blueprint $table) {
            // Primary key (optional untuk pivot table)
            $table->id();

            // Foreign key to posts table
            $table->foreignId('post_id')
                  ->constrained()
                  ->onDelete('cascade');  // Jika post dihapus, relasi ikut terhapus

            // Foreign key to tags table
            $table->foreignId('tag_id')
                  ->constrained()
                  ->onDelete('cascade');  // Jika tag dihapus, relasi ikut terhapus

            // Timestamps (optional untuk pivot table)
            $table->timestamps();

            // Composite unique constraint
            // Prevent duplicate: 1 post tidak bisa punya tag yang sama 2x
            $table->unique(['post_id', 'tag_id']);

            // Indexes untuk performa
            $table->index('post_id');
            $table->index('tag_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_tag');
    }
};

/**
 * ============================================
 * PENJELASAN MANY-TO-MANY
 * ============================================
 *
 * Example data:
 *
 * posts:
 * | id | title                  |
 * |----|------------------------|
 * | 1  | Tutorial Laravel       |
 * | 2  | Belajar Vue.js         |
 * | 3  | PHP 8 Features         |
 *
 * tags:
 * | id | name       |
 * |----|------------|
 * | 1  | Laravel    |
 * | 2  | PHP        |
 * | 3  | JavaScript |
 *
 * post_tag (pivot):
 * | id | post_id | tag_id | created_at |
 * |----|---------|--------|------------|
 * | 1  | 1       | 1      | 2024-01-01 | → Post 1 has tag 1 (Laravel)
 * | 2  | 1       | 2      | 2024-01-01 | → Post 1 has tag 2 (PHP)
 * | 3  | 2       | 3      | 2024-01-02 | → Post 2 has tag 3 (JavaScript)
 * | 4  | 3       | 2      | 2024-01-03 | → Post 3 has tag 2 (PHP)
 *
 * Query examples:
 * $post->tags;              → Get all tags for a post
 * $tag->posts;              → Get all posts for a tag
 * $post->tags()->attach(1); → Add tag to post
 * $post->tags()->detach(1); → Remove tag from post
 * $post->tags()->sync([1,2,3]); → Replace all tags
 *
 * ============================================
 * UNIQUE CONSTRAINT
 * ============================================
 *
 * $table->unique(['post_id', 'tag_id']);
 *
 * Gunanya: Prevent duplicate entries
 *
 * ❌ Without unique:
 * | post_id | tag_id |
 * |---------|--------|
 * | 1       | 5      | ← Duplicate!
 * | 1       | 5      | ← Duplicate!
 *
 * ✅ With unique constraint:
 * Attempt to insert duplicate → SQL Error!
 *
 * ============================================
 * TIMESTAMPS PADA PIVOT TABLE
 * ============================================
 *
 * By default, pivot table TIDAK punya timestamps.
 *
 * Jika ingin track kapan relasi dibuat:
 * 1. Add $table->timestamps() di migration
 * 2. Add ->withTimestamps() di Model:
 *
 * // Model Post
 * public function tags()
 * {
 *     return $this->belongsToMany(Tag::class)
 *                 ->withTimestamps();
 * }
 *
 * Gunanya:
 * - Track kapan tag ditambahkan ke post
 * - Order by "recently tagged"
 *
 * ============================================
 * ADDITIONAL PIVOT DATA
 * ============================================
 *
 * Pivot table bisa punya column tambahan:
 *
 * $table->integer('order')->default(0);
 * $table->boolean('is_primary')->default(false);
 *
 * Access di Model:
 * public function tags()
 * {
 *     return $this->belongsToMany(Tag::class)
 *                 ->withPivot('order', 'is_primary')
 *                 ->withTimestamps();
 * }
 *
 * Usage:
 * foreach ($post->tags as $tag) {
 *     echo $tag->pivot->order;
 *     echo $tag->pivot->is_primary;
 * }
 *
 * ============================================
 * BEST PRACTICES
 * ============================================
 *
 * 1. ✅ Naming: {singular1}_{singular2} (alphabetical)
 *    post_tag, not tag_post or posts_tags
 *
 * 2. ✅ Always add unique constraint
 *    $table->unique(['post_id', 'tag_id']);
 *
 * 3. ✅ Add onDelete('cascade')
 *    Prevent orphaned records
 *
 * 4. ✅ Add indexes for performance
 *    $table->index('post_id');
 *    $table->index('tag_id');
 *
 * 5. ⚠️ Primary key (id) optional
 *    Bisa pakai composite primary key:
 *    $table->primary(['post_id', 'tag_id']);
 */
