# Bab 19: Project Blog CRUD Lengkap 📰

[⬅️ Bab 18: Form & Validasi](18-form-validasi.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 20: Database Relationships ➡️](20-relationships.md)

---

## 🎯 Learning Objectives

Setelah mempelajari bab ini, kamu akan:
- ✅ Bisa membuat aplikasi blog lengkap dari nol
- ✅ Implement CRUD dengan relationships (Category has many Posts)
- ✅ Upload dan manage gambar untuk post thumbnail
- ✅ Implement pagination untuk daftar posts
- ✅ Buat fitur search posts
- ✅ Paham workflow development project kompleks
- ✅ Lebih percaya diri tackle project real-world

---

## 📋 Bagian 1: Planning Aplikasi

### Fitur yang Akan Dibuat

**Blog Platform dengan:**
- ✅ Manage Categories (CRUD)
- ✅ Manage Posts (CRUD + Image Upload)
- ✅ Categories → Posts (One-to-Many relationship)
- ✅ List posts dengan pagination
- ✅ Search posts by title
- ✅ Filter posts by category
- ✅ View post detail (public)

---

### Database Design (ERD Sederhana)

```
📁 categories
├── id
├── name
├── slug
└── timestamps

📄 posts
├── id
├── category_id (FK → categories.id)
├── title
├── slug
├── excerpt
├── body
├── image (thumbnail path)
├── is_published
└── timestamps
```

**Relationship:** 1 Category has MANY Posts

---

### Wireframe (Simple Layout)

```
┌─────────────────────────────────────┐
│  🏠 Blog App - Navbar               │
├─────────────────────────────────────┤
│                                     │
│  📰 Latest Posts                    │
│  ┌───────────────────────────────┐ │
│  │ [IMG] Post 1 | Category        │ │
│  │ Title | Excerpt...             │ │
│  └───────────────────────────────┘ │
│  ┌───────────────────────────────┐ │
│  │ [IMG] Post 2 | Category        │ │
│  └───────────────────────────────┘ │
│                                     │
│  [1] [2] [3] ... (Pagination)      │
└─────────────────────────────────────┘
```

---

## 🔧 Bagian 2: Setup & Migrations

### Step 1: Buat Project Baru (Optional)

```bash
# Jika mau project terpisah
composer create-project laravel/laravel blog-app
cd blog-app
```

**Atau pakai project existing!**

---

### Step 2: Konfigurasi Database

**File:** `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_app
DB_USERNAME=root
DB_PASSWORD=
```

**Buat database:** `blog_app` di phpMyAdmin/HeidiSQL.

---

### Step 3: Migration Categories

```bash
php artisan make:migration create_categories_table
```

**File:** `database/migrations/xxxx_create_categories_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
```

---

### Step 4: Migration Posts

```bash
php artisan make:migration create_posts_table
```

**File:** `database/migrations/xxxx_create_posts_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt');
            $table->longText('body');
            $table->string('image')->nullable(); // Path to image
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
```

**Penjelasan:**
- `foreignId('category_id')` → Foreign key ke categories
- `constrained()` → Otomatis references categories(id)
- `onDelete('cascade')` → Jika category dihapus, posts ikut terhapus

---

### Step 5: Jalankan Migration

```bash
php artisan migrate
```

**Output:**
```
INFO  Running migrations.

2025_01_20_create_categories_table ................ DONE
2025_01_20_create_posts_table ..................... DONE
```

---

## 📦 Bagian 3: Models

### Step 1: Model Category

```bash
php artisan make:model Category
```

**File:** `app/Models/Category.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * 🔗 Relationship: Category has many Posts
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
```

---

### Step 2: Model Post

```bash
php artisan make:model Post
```

**File:** `app/Models/Post.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'image',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    /**
     * 🔗 Relationship: Post belongs to Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * 🎯 Scope: Published posts only
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * 🔍 Scope: Search by title
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where('title', 'like', "%{$keyword}%");
    }
}
```

---

## 🌱 Bagian 4: Seeders (Data Dummy)

### Step 1: Category Seeder

```bash
php artisan make:seeder CategorySeeder
```

**File:** `database/seeders/CategorySeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Teknologi',
                'slug' => 'teknologi',
                'description' => 'Artikel tentang teknologi dan programming',
            ],
            [
                'name' => 'Lifestyle',
                'slug' => 'lifestyle',
                'description' => 'Artikel tentang gaya hidup',
            ],
            [
                'name' => 'Bisnis',
                'slug' => 'bisnis',
                'description' => 'Artikel tentang bisnis dan keuangan',
            ],
            [
                'name' => 'Travel',
                'slug' => 'travel',
                'description' => 'Artikel tentang traveling',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
```

---

### Step 2: Post Factory

```bash
php artisan make:factory PostFactory --model=Post
```

**File:** `database/factories/PostFactory.php`

```php
<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::inRandomOrder()->first()->id ?? 1,
            'title' => fake()->sentence(6),
            'slug' => fake()->unique()->slug(),
            'excerpt' => fake()->text(200),
            'body' => fake()->paragraphs(5, true),
            'image' => null, // Nanti kita handle upload
            'is_published' => fake()->boolean(80), // 80% published
        ];
    }
}
```

---

### Step 3: Database Seeder

**File:** `database/seeders/DatabaseSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed categories
        $this->call([
            CategorySeeder::class,
        ]);

        // 2. Generate 50 posts via factory
        Post::factory(50)->create();
    }
}
```

---

### Step 4: Run Seeder

```bash
php artisan migrate:fresh --seed
```

**Result:**
- 4 categories dibuat
- 50 posts random dibuat

---

## 🗺️ Bagian 5: Routes

**File:** `routes/web.php`

```php
<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

// Homepage - Redirect ke posts
Route::get('/', function () {
    return redirect()->route('posts.index');
});

// Public routes (untuk readers)
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

// Admin routes (untuk manage)
Route::prefix('admin')->group(function () {
    // Categories Management
    Route::resource('categories', CategoryController::class);

    // Posts Management
    Route::resource('posts', PostController::class)->except(['index', 'show']);
});
```

**Penjelasan:**
- Public: `posts.index` (list), `posts.show` (detail)
- Admin: CRUD untuk categories & posts di `/admin/*`
- `{post:slug}` → Route Model Binding by slug!

---

## 🎮 Bagian 6: Controllers

### Step 1: Category Controller

```bash
php artisan make:controller CategoryController --resource
```

**File:** `app/Http/Controllers/CategoryController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('posts')->latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
        ]);

        // Auto-generate slug from name
        $validated['slug'] = Str::slug($validated['name']);

        Category::create($validated);

        return redirect()->route('categories.index')
                         ->with('success', 'Category berhasil ditambahkan!');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);

        return redirect()->route('categories.index')
                         ->with('success', 'Category berhasil diupdate!');
    }

    public function destroy(Category $category)
    {
        $category->delete(); // Cascade delete posts juga

        return redirect()->route('categories.index')
                         ->with('success', 'Category berhasil dihapus!');
    }
}
```

---

### Step 2: Post Controller

```bash
php artisan make:controller PostController --resource
```

**File:** `app/Http/Controllers/PostController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * 📋 Public: List all published posts
     */
    public function index(Request $request)
    {
        $query = Post::with('category')->published()->latest();

        // 🔍 Search functionality
        if ($request->search) {
            $query->search($request->search);
        }

        // 🔍 Filter by category
        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        // 📄 Pagination
        $posts = $query->paginate(12);

        $categories = Category::all();

        return view('posts.index', compact('posts', 'categories'));
    }

    /**
     * 👀 Public: Show post detail
     */
    public function show(Post $post)
    {
        // Hanya tampilkan published posts (di public)
        if (!$post->is_published) {
            abort(404);
        }

        return view('posts.show', compact('post'));
    }

    /**
     * 📝 Admin: Form create post
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.posts.create', compact('categories'));
    }

    /**
     * 💾 Admin: Store new post
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|max:255',
            'excerpt' => 'required',
            'body' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_published' => 'boolean',
        ]);

        // Auto-generate slug
        $validated['slug'] = Str::slug($validated['title']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                                          ->store('posts', 'public');
        }

        Post::create($validated);

        return redirect()->route('posts.index')
                         ->with('success', 'Post berhasil ditambahkan!');
    }

    /**
     * ✏️ Admin: Form edit post
     */
    public function edit(Post $post)
    {
        $categories = Category::all();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    /**
     * 🔄 Admin: Update post
     */
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|max:255',
            'excerpt' => 'required',
            'body' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_published' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }

            $validated['image'] = $request->file('image')
                                          ->store('posts', 'public');
        }

        $post->update($validated);

        return redirect()->route('posts.index')
                         ->with('success', 'Post berhasil diupdate!');
    }

    /**
     * 🗑️ Admin: Delete post
     */
    public function destroy(Post $post)
    {
        // Delete image
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return redirect()->route('posts.index')
                         ->with('success', 'Post berhasil dihapus!');
    }
}
```

---

## 📸 Bagian 7: Image Upload Setup

### Step 1: Create Storage Link

```bash
php artisan storage:link
```

**Output:**
```
The [public/storage] link has been connected to [storage/app/public].
```

**Penjelasan:** Link `public/storage` ke `storage/app/public` agar file bisa diakses public.

---

### Step 2: Akses Image di View

```blade
{{-- Jika ada image --}}
@if ($post->image)
    <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}">
@else
    <img src="https://via.placeholder.com/600x400" alt="No image">
@endif
```

---

## 🎨 Bagian 8: Views

### Layout Master

**File:** `resources/views/layouts/app.blade.php`

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Blog App')</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        nav { background: #333; color: white; padding: 15px 0; }
        nav .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; }
        nav a { color: white; text-decoration: none; margin-left: 20px; }
        nav a:hover { text-decoration: underline; }
        .container { max-width: 1200px; margin: 20px auto; padding: 0 20px; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; }
        .posts-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 20px; }
        .post-card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .post-card img { width: 100%; height: 200px; object-fit: cover; }
        .post-card-body { padding: 15px; }
        .post-card h3 { margin-bottom: 10px; }
        .post-card p { color: #666; font-size: 14px; }
        .pagination { display: flex; gap: 5px; margin-top: 20px; }
        .pagination a, .pagination span { padding: 8px 12px; background: white; border: 1px solid #ddd; text-decoration: none; color: #333; }
        .pagination .active { background: #007bff; color: white; }
    </style>
</head>
<body>
    <nav>
        <div class="container">
            <div><a href="/" style="font-size: 20px; font-weight: bold;">📰 Blog App</a></div>
            <div>
                <a href="{{ route('posts.index') }}">Home</a>
                <a href="{{ route('categories.index') }}">Categories</a>
                <a href="{{ route('posts.create') }}">New Post</a>
            </div>
        </div>
    </nav>

    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @yield('content')
    </div>
</body>
</html>
```

---

### Posts Index (Public)

**File:** `resources/views/posts/index.blade.php`

```blade
@extends('layouts.app')

@section('title', 'All Posts')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>📰 Latest Posts</h1>
    </div>

    {{-- Search & Filter Form --}}
    <form method="GET" style="display: flex; gap: 10px; margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Search posts..." value="{{ request('search') }}" style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">

        <select name="category" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            <option value="">All Categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn">🔍 Search</button>
        @if (request('search') || request('category'))
            <a href="{{ route('posts.index') }}" class="btn" style="background: #6c757d;">Clear</a>
        @endif
    </form>

    {{-- Posts Grid --}}
    @if ($posts->isEmpty())
        <p style="text-align: center; padding: 40px; color: #888;">No posts found. 📭</p>
    @else
        <div class="posts-grid">
            @foreach ($posts as $post)
                <div class="post-card">
                    @if ($post->image)
                        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}">
                    @else
                        <img src="https://via.placeholder.com/600x400?text=No+Image" alt="No image">
                    @endif

                    <div class="post-card-body">
                        <small style="color: #007bff;">📁 {{ $post->category->name }}</small>
                        <h3><a href="{{ route('posts.show', $post->slug) }}" style="text-decoration: none; color: #333;">{{ $post->title }}</a></h3>
                        <p>{{ Str::limit($post->excerpt, 100) }}</p>
                        <small style="color: #888;">📅 {{ $post->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="pagination">
            {{ $posts->links() }}
        </div>
    @endif
@endsection
```

---

### Post Show (Detail)

**File:** `resources/views/posts/show.blade.php`

```blade
@extends('layouts.app')

@section('title', $post->title)

@section('content')
    <div style="max-width: 800px; margin: 0 auto;">
        <a href="{{ route('posts.index') }}" style="color: #007bff; text-decoration: none;">← Back to Posts</a>

        <article style="background: white; padding: 30px; border-radius: 8px; margin-top: 20px;">
            <small style="color: #007bff;">📁 {{ $post->category->name }}</small>
            <h1 style="margin: 10px 0 20px;">{{ $post->title }}</h1>

            <small style="color: #888;">📅 Published {{ $post->created_at->format('d F Y') }} ({{ $post->created_at->diffForHumans() }})</small>

            @if ($post->image)
                <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" style="width: 100%; border-radius: 8px; margin: 20px 0;">
            @endif

            <div style="line-height: 1.8; color: #333; margin-top: 20px;">
                {{ $post->body }}
            </div>
        </article>
    </div>
@endsection
```

---

### Admin: Posts Create

**File:** `resources/views/admin/posts/create.blade.php`

```blade
@extends('layouts.app')

@section('title', 'Create Post')

@section('content')
    <h1>➕ Create New Post</h1>

    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" style="max-width: 800px; margin-top: 20px;">
        @csrf

        <div style="margin-bottom: 15px;">
            <label>Category *</label>
            <select name="category_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="">Select Category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')<small style="color:red;">{{ $message }}</small>@enderror
        </div>

        <div style="margin-bottom: 15px;">
            <label>Title *</label>
            <input type="text" name="title" value="{{ old('title') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            @error('title')<small style="color:red;">{{ $message }}</small>@enderror
        </div>

        <div style="margin-bottom: 15px;">
            <label>Excerpt *</label>
            <textarea name="excerpt" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">{{ old('excerpt') }}</textarea>
            @error('excerpt')<small style="color:red;">{{ $message }}</small>@enderror
        </div>

        <div style="margin-bottom: 15px;">
            <label>Body *</label>
            <textarea name="body" rows="10" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">{{ old('body') }}</textarea>
            @error('body')<small style="color:red;">{{ $message }}</small>@enderror
        </div>

        <div style="margin-bottom: 15px;">
            <label>Image (Optional)</label>
            <input type="file" name="image" accept="image/*">
            @error('image')<small style="color:red;">{{ $message }}</small>@enderror
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                <span>Publish immediately</span>
            </label>
        </div>

        <button type="submit" class="btn">💾 Save Post</button>
    </form>
@endsection
```

**Note:** Views untuk edit, categories index/create/edit mengikuti pola yang sama! Untuk hemat space, implementasi lengkapnya bisa lihat To-Do List chapter sebagai reference.

---

## 📊 Bagian 9: Pagination

**Pagination otomatis dengan Laravel!**

**Controller:**
```php
$posts = Post::latest()->paginate(12); // 12 posts per page
```

**View:**
```blade
{{-- Tampilkan pagination links --}}
{{ $posts->links() }}
```

**Hasil:** Previous | 1 | 2 | 3 | ... | Next

**Custom pagination per page:**
```php
$posts = Post::latest()->paginate(
    $perPage = 15,
    $columns = ['*'],
    $pageName = 'page',
    $page = null
);
```

---

## 🔍 Bagian 10: Search Functionality

**Sudah diimplementasi di Controller & View!**

**Search query:**
```
http://localhost:8000/posts?search=laravel
```

**Filter by category:**
```
http://localhost:8000/posts?category=1
```

**Kombinasi:**
```
http://localhost:8000/posts?search=laravel&category=1
```

---

## ✨ Tips & Best Practices

### 1. Slug Auto-generation

```php
use Illuminate\Support\Str;

$slug = Str::slug($title);
// "Tutorial Laravel" → "tutorial-laravel"
```

---

### 2. Image Upload Best Practice

```php
// Store dengan auto-generated filename
$path = $request->file('image')->store('posts', 'public');
// Result: "posts/abc123.jpg"

// Delete old image saat update
if ($post->image) {
    Storage::disk('public')->delete($post->image);
}
```

---

### 3. Eager Loading (Performance!)

```php
// ❌ N+1 Query Problem (lambat!)
$posts = Post::all();
foreach ($posts as $post) {
    echo $post->category->name; // Query per post!
}

// ✅ Eager Loading (cepat!)
$posts = Post::with('category')->all();
foreach ($posts as $post) {
    echo $post->category->name; // No extra query!
}
```

---

## 📝 Latihan: Extend Fitur

### Latihan 1: Filter by Published Status

**Task:** Tambah dropdown filter "All | Published | Draft" di admin.

**Hint:**
```php
// Controller
if ($request->status == 'published') {
    $query->where('is_published', true);
} elseif ($request->status == 'draft') {
    $query->where('is_published', false);
}
```

---

### Latihan 2: Sort by Views

**Task:** Tambah kolom `views` dan sort posts by most viewed.

**Hint:**
```bash
# Migration
php artisan make:migration add_views_to_posts_table
$table->integer('views')->default(0);

# Controller show()
$post->increment('views');

# Controller index()
$posts = Post::orderBy('views', 'desc')->paginate(12);
```

---

### Latihan 3: Related Posts

**Task:** Tampilkan 3 related posts di post detail (same category).

**Hint:**
```php
// Controller show()
$relatedPosts = Post::where('category_id', $post->category_id)
                    ->where('id', '!=', $post->id)
                    ->published()
                    ->limit(3)
                    ->get();

// View
@foreach ($relatedPosts as $related)
    <h3>{{ $related->title }}</h3>
@endforeach
```

---

## 📖 Summary

Di bab ini kamu sudah belajar:

- ✅ **Planning**: ERD, wireframe untuk project kompleks
- ✅ **Migrations**: 2 tables dengan foreign key relationship
- ✅ **Models**: Relationship methods (hasMany, belongsTo)
- ✅ **Seeders & Factories**: Generate data dummy untuk testing
- ✅ **Controllers**: CRUD lengkap dengan image upload
- ✅ **Views**: Layout master, posts index/show, admin forms
- ✅ **Image Upload**: Storage link, store, delete images
- ✅ **Pagination**: Automatic pagination dengan `paginate()`
- ✅ **Search & Filter**: Query string parameters
- ✅ **Scopes**: Reusable queries (published, search)

**Blog CRUD lengkap sudah dikuasai!** 📰✅

---

## 🎯 Next Chapter Preview

Di chapter berikutnya, kita akan deep-dive ke:
- ✅ **Database Relationships** detail
- ✅ One-to-Many (Category-Posts)
- ✅ Many-to-Many (Posts-Tags)
- ✅ One-to-One (User-Profile)
- ✅ Eager loading & N+1 problem
- ✅ Praktik dengan pivot tables

**Relationships adalah jantung aplikasi database!** 🔗

---

## 🔗 Referensi

- 📖 [Eloquent: Relationships](https://laravel.com/docs/12.x/eloquent-relationships)
- 📖 [File Storage](https://laravel.com/docs/12.x/filesystem)
- 📖 [Pagination](https://laravel.com/docs/12.x/pagination)

---

[⬅️ Bab 18: Form & Validasi](18-form-validasi.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 20: Database Relationships ➡️](20-relationships.md)

---

<div align="center">

**Blog CRUD Project selesai! Real-world app sudah bisa dibuat!** 📰✅

**Lanjut ke Relationships untuk fitur lebih kompleks!** 🔗

</div>