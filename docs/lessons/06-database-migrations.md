# Pelajaran 6: Database Structure and Migrations

Sekarang kita akan mulai membangun database untuk aplikasi blog kita. Laravel menyediakan sistem migration yang powerful untuk mengelola schema database dengan version control.

## 🎯 Tujuan Pembelajaran

Setelah menyelesaikan pelajaran ini, Anda akan:
- ✅ Memahami konsep database migrations di Laravel
- ✅ Mendesain struktur tabel untuk aplikasi blog
- ✅ Membuat migrations untuk semua tabel yang dibutuhkan
- ✅ Menjalankan dan rollback migrations
- ✅ Memahami foreign key constraints dan relationships
- ✅ Membuat Eloquent models yang sesuai dengan tabel database
- ✅ Memahami urutan yang benar: Migration → Model → Seeder

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
        // Buat tabel 'categories' di database
        Schema::create('categories', function (Blueprint $table) {
            $table->id();                                          // id (auto increment, primary key)
            $table->string('name');                                // nama kategori (varchar 255)
            $table->string('slug')->unique();                     // URL-friendly name (harus unik)
            $table->text('description')->nullable();              // deskripsi panjang (boleh kosong)
            $table->string('color', 7)->default('#3B82F6');       // warna hex (7 karakter: #ff0000)
            $table->boolean('is_active')->default(true);          // status aktif (default: aktif)
            $table->integer('sort_order')->default(0);            // urutan tampilan
            $table->timestamps();                                  // created_at & updated_at otomatis

            // Bikin index untuk pencarian lebih cepat
            $table->index('slug');                                 // index untuk search by slug
            $table->index('is_active');                           // index untuk filter aktif/tidak
        });
    }

    /**
     * Rollback migration (kalau ada error atau mau undo)
     */
    public function down(): void
    {
        // Hapus tabel 'categories' kalau ada
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
# Menggunakan phpMyAdmin
# Buka http://localhost/phpmyadmin dan pilih database laravel_blog

# Atau menggunakan Laravel tinker
php artisan tinker
```

Dalam tinker, coba:
```php
// Periksa semua tabel
DB::select("SHOW TABLES");

// Periksa struktur tabel
DB::select("DESCRIBE posts");

// Atau gunakan Schema facade
Schema::getColumnListing('posts');
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

## 📝 Membuat Eloquent Models

### Step 6: Membuat Model Category

**PENTING**: Sebelum membuat seeder, kita harus membuat model terlebih dahulu agar seeder bisa menggunakan model tersebut.

```bash
php artisan make:model Category
```

Edit file `app/Models/Category.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi ketika kita buat/ubah kategori
    // Laravel keamanan: cuma kolom ini yang boleh diisi dari formulir
    protected $fillable = [
        'name',        // Nama kategori (misal: "Tutorial Laravel")
        'slug',        // Ramah alamat web (misal: "tutorial-laravel")
        'description', // Penjelasan kategori
        'color',       // Warna untuk tampilan antarmuka (misal: "#ff0000")
        'is_active',   // Aktif atau tidak (benar/salah)
        'sort_order',  // Urutan tampil (1, 2, 3, ...)
    ];

    // Laravel otomatis ubah jenis data dari database
    protected $casts = [
        'is_active' => 'boolean',  // Database simpan 0/1, tapi kita terima benar/salah
        'sort_order' => 'integer', // Pastikan angka, bukan teks
    ];

    /**
     * Mulai model - kejadian yang dijalankan otomatis
     * Ini seperti "pemicu" di database
     */
    protected static function boot()
    {
        parent::boot();

        // Ketika kita buat kategori baru
        static::creating(function ($category) {
            // Kalau alamat web kosong, buat otomatis dari nama
            if (empty($category->slug)) {
                // Str::slug ubah "Tutorial Laravel" jadi "tutorial-laravel"
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Get posts that belong to this category.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Scope for active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered categories.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
```

### Step 7: Membuat Model Tag

```bash
php artisan make:model Tag
```

Edit file `app/Models/Tag.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'color',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from name
        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    /**
     * Get posts that have this tag.
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
```

### Step 8: Membuat Model Post (Preview)

```bash
php artisan make:model Post
```

Edit file `app/Models/Post.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'status',
        'is_featured',
        'published_at',
        'views_count',
        'meta_title',
        'meta_description',
        'user_id',
        'category_id',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'views_count' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from title
        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    /**
     * Get the user that owns the post.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that owns the post.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the tags for the post.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Scope for published posts.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    /**
     * Scope for featured posts.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
```

## 💾 Database Seeders (Bonus)

### Step 9: Membuat Sample Data

Buat seeder untuk testing:

```bash
php artisan make:seeder CategorySeeder
php artisan make:seeder PostSeeder  
php artisan make:seeder TagSeeder
```

**PENTING**: Gunakan `updateOrCreate()` untuk seeder agar idempotent (bisa dijalankan berulang kali tanpa error).

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

        // Ulang setiap kategori dan simpan ke database
        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']], // Cek: apakah kategori dengan alamat web ini sudah ada?
                $category                       // Kalau belum ada -> buat baru, kalau sudah ada -> perbarui
            );
            // updateOrCreate = cara pintar: cek dulu, baru buat atau perbarui
            // Jadi aman dijalankan berulang kali tanpa galat
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
            Tag::updateOrCreate(
                ['slug' => $tag['slug']], // Check by slug
                $tag // Data to create or update
            );
        }
    }
}
```

### 💡 Mengapa menggunakan updateOrCreate()?

**Masalah dengan `create()`:**
```php
// ❌ Error jika data sudah ada
Category::create($category); // SQLSTATE[23000]: Integrity constraint violation
```

**Solusi dengan `updateOrCreate()`:**
```php
// ✅ Idempotent - aman dijalankan berulang kali
Category::updateOrCreate(
    ['slug' => $category['slug']], // Kondisi pencarian
    $category                      // Data untuk create/update
);
```

**Manfaat:**
- ✅ **Idempotent**: Seeder bisa dijalankan berulang tanpa error
- ✅ **Tidak duplikat**: Jika slug sudah ada, data akan diupdate
- ✅ **Insert baru**: Jika slug belum ada, data akan dibuat baru
- ✅ **Aman untuk production**: Tidak akan merusak data existing

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

### Step 10: Test Database Structure

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

Selamat! Database structure dan models telah berhasil dibuat:
- ✅ Migration untuk 4 tabel utama (categories, posts, tags, post_tag)
- ✅ Foreign key constraints yang proper
- ✅ Indexes untuk performance optimization
- ✅ Eloquent models (Category, Tag, Post) dengan relationships
- ✅ Seeder untuk sample data (dengan model yang sudah siap)
- ✅ Database relationship yang terstruktur

**Urutan yang benar telah diikuti**: Migration → Model → Seeder

Sekarang kita memiliki fondasi database yang solid untuk aplikasi blog. Di pelajaran selanjutnya, kita akan menggunakan models ini untuk menampilkan data di controllers dan views.


### 🔄 Urutan yang Benar untuk Migration + Models + Seeders:

```bash
# 1. Buat migrations terlebih dahulu
php artisan make:migration create_categories_table
php artisan make:migration create_posts_table
php artisan make:migration create_tags_table
php artisan make:migration create_post_tag_table

# 2. Edit file migration (isi struktur tabel)

# 3. Jalankan migrations
php artisan migrate

# 4. PENTING: Buat models sebelum seeder
php artisan make:model Category
php artisan make:model Tag
php artisan make:model Post

# 5. Edit models (fillable, relationships, dll)

# 6. Baru kemudian buat dan jalankan seeders
php artisan make:seeder CategorySeeder
php artisan make:seeder TagSeeder
php artisan db:seed
```

### 🎯 Best Practices untuk Database Seeding

**1. Gunakan updateOrCreate() untuk Idempotent Seeding**
```php
// ✅ RECOMMENDED
Model::updateOrCreate(['unique_field' => $value], $data);

// ❌ AVOID - Can cause constraint violations
Model::create($data);
```

**2. Gunakan firstOrCreate() untuk User Seeding**
```php
$user = User::firstOrCreate([
    'email' => 'admin@example.com'
], [
    'name' => 'Admin User',
    'password' => bcrypt('password')
]);
```

**3. Gunakan sync() untuk Many-to-Many Relationships**
```php
// Untuk relationships yang bisa berubah
$post->tags()->sync($tagIds);

// Untuk append tanpa menghapus existing
$post->tags()->attach($tagIds);
```

**4. Check Existence Before Operations**
```php
if (!Category::where('slug', 'laravel')->exists()) {
    // Only seed if doesn't exist
}
```

**Catatan**: Model harus dibuat dan dikonfigurasi sebelum seeder karena seeder menggunakan model untuk memasukkan data ke database.

---

**Selanjutnya:** [Pelajaran 7: MVC, DB Queries, and Eloquent Models](07-mvc-eloquent-models.md)

*Database foundation is ready! 💾*