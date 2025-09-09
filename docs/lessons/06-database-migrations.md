# Pelajaran 6: Database Structure and Migrations

Sekarang kita akan mulai membangun database untuk aplikasi blog kita. Laravel menyediakan sistem migration yang powerful untuk mengelola schema database dengan version control.

## 🎯 Tujuan Pembelajaran

Setelah menyelesaikan pelajaran ini, Anda akan:
- ✅ Memahami konsep database migrations di Laravel
- ✅ Mendesain struktur tabel untuk aplikasi blog
- ✅ Membuat migrations untuk semua tabel yang dibutuhkan
- ✅ Menjalankan dan rollback migrations
- ✅ Memahami foreign key constraints dan relationships

## 🗃️ Perencanaan Database

### Tabel yang Akan Kita Buat

1. **categories** - Untuk mengorganisir posts
2. **posts** - Tabel utama untuk artikel blog  
3. **tags** - Tag system untuk posts
4. **post_tag** - Pivot table untuk many-to-many relationship

> **Note**: Tabel `users` sudah tersedia dari instalasi Laravel default

## 📋 Migration untuk Categories

### Step 1: Membuat Migration Categories

Jalankan command artisan untuk membuat migration:

```bash
php artisan make:migration create_categories_table
```

Edit file migration yang dibuat di `database/migrations/xxxx_xx_xx_xxxxxx_create_categories_table.php`:

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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#3B82F6'); // Hex color for UI
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('slug');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
```

## 📝 Migration untuk Posts

### Step 2: Membuat Migration Posts

```bash
php artisan make:migration create_posts_table
```

Edit file migration yang dibuat:

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
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->integer('views_count')->default(0);
            
            // SEO fields
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            
            // Foreign keys
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('slug');
            $table->index('status');
            $table->index('is_featured');
            $table->index('published_at');
            $table->index(['status', 'published_at']);
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

## 🏷️ Migration untuk Tags

### Step 3: Membuat Migration Tags

```bash
php artisan make:migration create_tags_table
```

Edit file migration:

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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color', 7)->default('#6B7280'); // Hex color for UI
            $table->timestamps();
            
            // Index for better performance
            $table->index('slug');
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
```

### Step 4: Migration untuk Pivot Table Post-Tag

```bash
php artisan make:migration create_post_tag_table
```

Edit file migration:

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
        Schema::create('post_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Unique constraint untuk prevent duplicate relationships
            $table->unique(['post_id', 'tag_id']);
            
            // Indexes
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
```

## 👤 Update Migration Users (Optional Enhancement)

Jika ingin menambahkan field tambahan pada users table:

```bash
php artisan make:migration add_additional_fields_to_users_table --table=users
```

Edit migration:

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
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('email');
            $table->text('bio')->nullable()->after('avatar');
            $table->enum('role', ['admin', 'author', 'user'])->default('user')->after('bio');
            $table->timestamp('last_active_at')->nullable()->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'bio', 'role', 'last_active_at']);
        });
    }
};
```

## 🚀 Menjalankan Migrations

### Step 5: Jalankan Semua Migrations

```bash
# Jalankan semua migrations
php artisan migrate

# Jika ingin melihat status migrations
php artisan migrate:status

# Fresh install (drop all tables and re-run)
php artisan migrate:fresh
```

Output yang diharapkan:
```
Migrating: 2025_09_09_000001_create_categories_table
Migrated:  2025_09_09_000001_create_categories_table (45.67ms)
Migrating: 2025_09_09_000002_create_posts_table  
Migrated:  2025_09_09_000002_create_posts_table (67.23ms)
Migrating: 2025_09_09_000003_create_tags_table
Migrated:  2025_09_09_000003_create_tags_table (23.45ms)
Migrating: 2025_09_09_000004_create_post_tag_table
Migrated:  2025_09_09_000004_create_post_tag_table (34.12ms)
```

## 🔍 Verifikasi Database Structure

### Step 6: Periksa Tabel yang Dibuat

```bash
# Menggunakan SQLite browser atau command line
sqlite3 database/database.sqlite

# Atau menggunakan Laravel tinker
php artisan tinker
```

Dalam tinker, coba:
```php
// Periksa semua tabel
DB::select("SELECT name FROM sqlite_master WHERE type='table'");

// Periksa struktur tabel
DB::select("PRAGMA table_info(posts)");
```

## 📊 Database Schema Overview

Berikut adalah relasi antar tabel:

```
┌─────────────┐         ┌─────────────┐         ┌─────────────┐
│    Users    │         │    Posts    │         │ Categories  │
├─────────────┤         ├─────────────┤         ├─────────────┤
│ id (PK)     │────┬────│ user_id (FK)│    ┌────│ id (PK)     │
│ name        │    │    │ category_id │────┘    │ name        │
│ email       │    │    │ title       │         │ slug        │
│ role        │    │    │ content     │         │ description │
│ bio         │    │    │ status      │         │ color       │
└─────────────┘    │    │ published_at│         └─────────────┘
                   │    └─────────────┘
                   │
                   │    ┌─────────────┐         ┌─────────────┐
                   │    │ Post_Tag    │         │    Tags     │
                   │    ├─────────────┤         ├─────────────┤
                   └────│ post_id (FK)│    ┌────│ id (PK)     │
                        │ tag_id (FK) │────┘    │ name        │
                        └─────────────┘         │ slug        │
                                               │ color       │
                                               └─────────────┘
```

## 💾 Database Seeders (Bonus)

### Step 7: Membuat Sample Data

Buat seeder untuk testing:

```bash
php artisan make:seeder CategorySeeder
php artisan make:seeder PostSeeder  
php artisan make:seeder TagSeeder
```

Edit `database/seeders/CategorySeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Laravel Framework',
                'slug' => 'laravel-framework',
                'description' => 'Tutorial dan tips tentang Laravel framework',
                'color' => '#EF4444',
            ],
            [
                'name' => 'PHP Programming', 
                'slug' => 'php-programming',
                'description' => 'Pembelajaran PHP dari basic hingga advanced',
                'color' => '#3B82F6',
            ],
            [
                'name' => 'Web Development',
                'slug' => 'web-development', 
                'description' => 'Tips dan tutorial web development modern',
                'color' => '#10B981',
            ],
            [
                'name' => 'Database',
                'slug' => 'database',
                'description' => 'Tutorial database, MySQL, PostgreSQL, dll',
                'color' => '#F59E0B',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
```

Edit `database/seeders/TagSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'Laravel', 'slug' => 'laravel', 'color' => '#EF4444'],
            ['name' => 'PHP', 'slug' => 'php', 'color' => '#3B82F6'], 
            ['name' => 'Tutorial', 'slug' => 'tutorial', 'color' => '#10B981'],
            ['name' => 'Beginner', 'slug' => 'beginner', 'color' => '#F59E0B'],
            ['name' => 'Advanced', 'slug' => 'advanced', 'color' => '#8B5CF6'],
            ['name' => 'Tips', 'slug' => 'tips', 'color' => '#EC4899'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}
```

Update `database/seeders/DatabaseSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            TagSeeder::class,
            // PostSeeder::class, // Akan dibuat di pelajaran selanjutnya
        ]);
    }
}
```

Jalankan seeders:

```bash
php artisan db:seed
```

## 🔧 Migration Commands Reference

### Useful Migration Commands

```bash
# Membuat migration baru
php artisan make:migration create_table_name

# Migration untuk modify existing table  
php artisan make:migration add_column_to_table --table=table_name

# Jalankan migrations
php artisan migrate

# Rollback migration terakhir
php artisan migrate:rollback

# Rollback specific batch
php artisan migrate:rollback --batch=3

# Reset semua migrations
php artisan migrate:reset

# Fresh install (drop + migrate)
php artisan migrate:fresh

# Fresh install + seed
php artisan migrate:fresh --seed

# Status migrations
php artisan migrate:status
```

## 📋 Best Practices untuk Migrations

### 1. **Naming Convention**
```bash
# Good ✅
create_posts_table
add_status_to_posts_table
modify_posts_table_add_index

# Bad ❌ 
posts
post_changes
update_posts
```

### 2. **Column Types yang Tepat**
```php
$table->string('title'); // VARCHAR(255)
$table->text('excerpt'); // TEXT
$table->longText('content'); // LONGTEXT
$table->enum('status', ['draft', 'published']); // ENUM
$table->timestamp('published_at')->nullable(); // TIMESTAMP
$table->boolean('is_featured')->default(false); // BOOLEAN
```

### 3. **Foreign Key Constraints**
```php
// Modern Laravel way
$table->foreignId('user_id')->constrained()->onDelete('cascade');

// Manual way
$table->unsignedBigInteger('user_id');
$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
```

### 4. **Indexes untuk Performance**
```php
$table->index('slug'); // Single column
$table->index(['status', 'published_at']); // Composite index
$table->unique('email'); // Unique constraint
```

## ✅ Verifikasi Migration

### Step 8: Test Database Structure

Coba jalankan beberapa query untuk memastikan struktur benar:

```bash
php artisan tinker
```

```php
// Test create category
$category = new App\Models\Category();
$category->name = 'Test Category';
$category->slug = 'test-category';
$category->description = 'This is a test';
$category->save();

// Test foreign key constraint
try {
    DB::table('posts')->insert([
        'title' => 'Test',
        'slug' => 'test',
        'content' => 'Test content',
        'user_id' => 999, // Non-existent user
        'category_id' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
} catch (\Exception $e) {
    echo "Foreign key constraint working: " . $e->getMessage();
}
```

## 🎯 Kesimpulan

Selamat! Database structure telah berhasil dibuat:
- ✅ Migration untuk 4 tabel utama (categories, posts, tags, post_tag)
- ✅ Foreign key constraints yang proper
- ✅ Indexes untuk performance optimization
- ✅ Seeder untuk sample data
- ✅ Database relationship yang terstruktur

Sekarang kita memiliki fondasi database yang solid untuk aplikasi blog. Di pelajaran selanjutnya, kita akan membuat Eloquent models dan mulai menggunakan data dari database.

## 💡 Troubleshooting

**Error: "Foreign key constraint fails"**
- Pastikan urutan migration benar (parent table dulu)
- Jalankan `php artisan migrate:fresh` untuk reset

**Error: "Table already exists"**
- Jalankan `php artisan migrate:rollback` lalu migrate lagi
- Atau gunakan `php artisan migrate:fresh`

**Error: "Column not found"**
- Check typo dalam nama kolom
- Pastikan migration sudah di-run

---

**Selanjutnya:** [Pelajaran 7: MVC, DB Queries, and Eloquent Models](07-mvc-eloquent-models.md)

*Database foundation is ready! 💾*