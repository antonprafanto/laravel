<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================
 * MIGRATION: Create Posts Table
 * ============================================
 *
 * Table ini untuk menyimpan blog posts dengan:
 * - Basic info (title, slug, body)
 * - Publication status (is_published, published_at)
 * - Foreign key ke users (author)
 * - Foreign key ke categories
 * - Timestamps (created_at, updated_at)
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Basic columns
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('body');

            // Stats & features
            $table->integer('views')->default(0);
            $table->integer('likes')->default(0);
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);

            // Publication info
            $table->timestamp('published_at')->nullable();

            // Foreign key to users (author)
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');  // Jika user dihapus, posts ikut terhapus

            // Foreign key to categories
            $table->foreignId('category_id')
                  ->constrained()
                  ->onDelete('cascade');  // Jika category dihapus, posts ikut terhapus

            // Timestamps (created_at, updated_at)
            $table->timestamps();

            // Soft deletes (deleted_at)
            $table->softDeletes();

            // Indexes untuk performa
            $table->index('is_published');
            $table->index('published_at');
            $table->index('created_at');
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

/**
 * ============================================
 * PENJELASAN COLUMN TYPES
 * ============================================
 *
 * id()              → BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
 * string('name')    → VARCHAR(255)
 * text('body')      → TEXT
 * integer('views')  → INTEGER
 * boolean('flag')   → TINYINT(1)
 * timestamp('date') → TIMESTAMP
 * timestamps()      → created_at + updated_at (TIMESTAMP)
 * softDeletes()     → deleted_at (TIMESTAMP, nullable)
 *
 * ============================================
 * PENJELASAN FOREIGN KEY
 * ============================================
 *
 * foreignId('user_id')     → Creates BIGINT UNSIGNED column
 * ->constrained()          → Adds foreign key to 'users' table 'id' column
 * ->onDelete('cascade')    → Jika parent dihapus, child ikut terhapus
 *
 * Options untuk onDelete:
 * - cascade   → Hapus child jika parent dihapus
 * - set null  → Set child ke null jika parent dihapus
 * - restrict  → Prevent parent deletion if child exists
 * - no action → Do nothing (default)
 *
 * ============================================
 * INDEXES
 * ============================================
 *
 * Indexes membuat query WHERE/ORDER BY lebih cepat
 *
 * Kapan pakai index:
 * ✅ Column yang sering di-WHERE
 * ✅ Column yang sering di-ORDER BY
 * ✅ Foreign keys
 *
 * Kapan JANGAN pakai index:
 * ❌ Column yang jarang diquery
 * ❌ Table kecil (< 1000 rows)
 * ❌ Terlalu banyak index (slow INSERT/UPDATE)
 *
 * ============================================
 * SOFT DELETES
 * ============================================
 *
 * Soft delete = data tidak benar-benar dihapus, hanya ditandai
 *
 * $table->softDeletes()  → Adds deleted_at column
 *
 * Model must use:
 * use Illuminate\Database\Eloquent\SoftDeletes;
 *
 * Usage:
 * $post->delete();              → Soft delete
 * $post->forceDelete();         → Permanent delete
 * $post->restore();             → Restore deleted
 * Post::withTrashed()->get();   → Include deleted
 * Post::onlyTrashed()->get();   → Only deleted
 */
