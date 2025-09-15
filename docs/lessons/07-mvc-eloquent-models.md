# Pelajaran 7: MVC, DB Queries, and Eloquent Models

Sekarang kita akan membangun Eloquent models dan mengimplementasi pola MVC untuk mengganti data dummy dengan data real dari database. Ini adalah jantung dari aplikasi Laravel.

## 🎯 Tujuan Pembelajaran

Setelah menyelesaikan pelajaran ini, Anda akan:
- ✅ Memahami arsitektur MVC di Laravel
- ✅ Membuat Eloquent models untuk semua tabel
- ✅ Melakukan database queries dengan Eloquent
- ✅ Menggunakan data real di views
- ✅ Memahami mass assignment dan fillable properties

## 🏗️ Memahami MVC Architecture

### Model-View-Controller Pattern

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│    MODEL    │    │ CONTROLLER  │    │    VIEW     │
│             │    │             │    │             │
│ - Database  │◄──►│ - Logic     │◄──►│ - UI/HTML   │
│ - Business  │    │ - Routes    │    │ - Templates │
│ - Rules     │    │ - Requests  │    │ - Display   │
└─────────────┘    └─────────────┘    └─────────────┘
```

**Model**: Mengelola data dan business logic  
**View**: Menampilkan data ke user (Blade templates)  
**Controller**: Menghubungkan Model dan View, handle requests

## 🎭 Membuat Eloquent Models

### Step 1: Generate Models

```bash
# Generate models untuk semua tabel
php artisan make:model Category
php artisan make:model Post
php artisan make:model Tag

# atau dengan flag untuk membuat sekaligus migration, factory, seeder
php artisan make:model Category -mfs
```

### Step 2: Category Model

Edit `app/Models/Category.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug', 
        'description',
        'color',
        'is_active',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Boot method untuk auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Relationship dengan Posts
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get published posts only
     */
    public function publishedPosts(): HasMany
    {
        return $this->posts()->where('status', 'published')
                            ->where('published_at', '<=', now());
    }

    /**
     * Scope untuk active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk ordering
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get posts count attribute
     */
    public function getPostsCountAttribute()
    {
        return $this->publishedPosts()->count();
    }

    /**
     * Get route key name untuk URL
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
```

### Step 3: Post Model

Edit `app/Models/Post.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
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

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
    ];

    /**
     * Boot method untuk auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            
            // Auto-generate excerpt dari content jika kosong
            if (empty($post->excerpt) && !empty($post->content)) {
                $post->excerpt = Str::limit(strip_tags($post->content), 160);
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('title') && !$post->isDirty('slug')) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    /**
     * Relationship dengan User (Author)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias untuk author
     */
    public function author(): BelongsTo
    {
        return $this->user();
    }

    /**
     * Relationship dengan Category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relationship dengan Tags (Many-to-Many)
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Scope untuk published posts
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('published_at', '<=', now());
    }

    /**
     * Scope untuk featured posts
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope untuk recent posts
     */
    public function scopeRecent($query, $limit = 5)
    {
        return $query->orderBy('published_at', 'desc')->limit($limit);
    }

    /**
     * Scope untuk popular posts
     */
    public function scopePopular($query, $limit = 5)
    {
        return $query->orderBy('views_count', 'desc')->limit($limit);
    }

    /**
     * Get formatted published date
     */
    public function getPublishedDateAttribute()
    {
        return $this->published_at?->format('d F Y');
    }

    /**
     * Get reading time estimate
     */
    public function getReadingTimeAttribute()
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $readingSpeed = 200; // words per minute
        $minutes = ceil($wordCount / $readingSpeed);
        
        return $minutes . ' min read';
    }

    /**
     * Get route key name untuk URL
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Increment views count
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }
}
```

### Step 4: Tag Model

Edit `app/Models/Tag.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'color',
    ];

    /**
     * Boot method untuk auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    /**
     * Relationship dengan Posts (Many-to-Many)
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    /**
     * Get published posts only
     */
    public function publishedPosts(): BelongsToMany
    {
        return $this->posts()->where('status', 'published')
                             ->where('published_at', '<=', now());
    }

    /**
     * Get posts count attribute
     */
    public function getPostsCountAttribute()
    {
        return $this->publishedPosts()->count();
    }

    /**
     * Get route key name untuk URL
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
```

### Step 5: Update User Model

Edit `app/Models/User.php` untuk menambahkan relationship:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'bio',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_active_at' => 'datetime',
        ];
    }

    /**
     * Relationship dengan Posts
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get published posts only
     */
    public function publishedPosts(): HasMany
    {
        return $this->posts()->published();
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is author
     */
    public function isAuthor(): bool
    {
        return in_array($this->role, ['admin', 'author']);
    }
}
```

## 📊 Membuat Controllers

### Step 6: Blog Controller

```bash
php artisan make:controller BlogController
```

Edit `app/Http/Controllers/BlogController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display blog homepage
     */
    public function index()
    {
        // Get featured post
        $featuredPost = Post::published()
                          ->featured()
                          ->with(['category', 'author'])
                          ->first();

        // Get recent posts (excluding featured)
        $posts = Post::published()
                   ->with(['category', 'author'])
                   ->when($featuredPost, function ($query, $featuredPost) {
                       return $query->where('id', '!=', $featuredPost->id);
                   })
                   ->recent(6)
                   ->get();

        // Get categories with post count
        $categories = Category::active()
                            ->ordered()
                            ->withCount(['publishedPosts'])
                            ->get();

        // Get popular tags
        $popularTags = Tag::has('posts')
                         ->withCount(['publishedPosts'])
                         ->orderBy('published_posts_count', 'desc')
                         ->limit(10)
                         ->get();

        return view('blog.index', compact(
            'featuredPost',
            'posts',
            'categories',
            'popularTags'
        ));
    }

    /**
     * Display single post
     */
    public function show(Post $post)
    {
        // Increment views
        $post->incrementViews();

        // Load relationships
        $post->load(['category', 'author', 'tags']);

        // Get related posts
        $relatedPosts = Post::published()
                          ->where('id', '!=', $post->id)
                          ->where('category_id', $post->category_id)
                          ->recent(3)
                          ->get();

        // Get next/previous posts
        $previousPost = Post::published()
                          ->where('published_at', '<', $post->published_at)
                          ->orderBy('published_at', 'desc')
                          ->first();

        $nextPost = Post::published()
                      ->where('published_at', '>', $post->published_at)
                      ->orderBy('published_at', 'asc')
                      ->first();

        return view('blog.show', compact(
            'post', 
            'relatedPosts', 
            'previousPost', 
            'nextPost'
        ));
    }

    /**
     * Display posts by category
     */
    public function category(Category $category)
    {
        $posts = Post::published()
                   ->where('category_id', $category->id)
                   ->with(['author', 'tags'])
                   ->paginate(10);

        return view('blog.category', compact('category', 'posts'));
    }

    /**
     * Display posts by tag
     */
    public function tag(Tag $tag)
    {
        $posts = $tag->publishedPosts()
                   ->with(['category', 'author'])
                   ->paginate(10);

        return view('blog.tag', compact('tag', 'posts'));
    }
}
```

## 🛠️ Update Routes

Edit `routes/web.php`:

```php
<?php

use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('blog.index');
});

// Blog routes
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/post/{post:slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/blog/category/{category:slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/tag/{tag:slug}', [BlogController::class, 'tag'])->name('blog.tag');

// Static pages
Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');
```

## 🎨 Update Views dengan Data Real

### Step 7: Update Blog Index View

Edit `resources/views/blog/index.blade.php`:

```php
@extends('layouts.app')

@section('title', 'Blog - Laravel Tutorial Indonesia')

@section('content')
@php $showSidebar = true; @endphp

<div class="space-y-12">
    <!-- Blog Header -->
    <div class="text-center bg-white rounded-2xl p-8 shadow-sm">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            Laravel Development Blog
        </h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Tutorial, tips, dan best practices untuk Laravel development dalam bahasa Indonesia
        </p>
    </div>

    @if($featuredPost)
    <!-- Featured Post Section -->
    <section class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-2xl text-white overflow-hidden">
        <div class="p-8 lg:p-12">
            <div class="lg:grid lg:grid-cols-2 lg:gap-12 items-center">
                <div>
                    <div class="inline-flex items-center bg-primary-500 bg-opacity-50 rounded-full px-4 py-2 text-sm font-medium mb-4">
                        <span class="w-2 h-2 bg-yellow-400 rounded-full mr-2"></span>
                        Featured Post
                    </div>
                    <h2 class="text-3xl lg:text-4xl font-bold mb-4">
                        {{ $featuredPost->title }}
                    </h2>
                    <p class="text-xl text-primary-100 mb-6">
                        {{ $featuredPost->excerpt }}
                    </p>
                    <div class="flex items-center space-x-6 text-primary-200 text-sm mb-6">
                        <span>By {{ $featuredPost->author->name }}</span>
                        <span>•</span>
                        <span>{{ $featuredPost->published_date }}</span>
                        <span>•</span>
                        <span>{{ $featuredPost->reading_time }}</span>
                    </div>
                    <a href="{{ route('blog.show', $featuredPost) }}" class="inline-flex items-center bg-white text-primary-600 font-semibold px-6 py-3 rounded-lg hover:bg-gray-50 transition-colors">
                        Baca Artikel
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                <div class="mt-8 lg:mt-0">
                    @if($featuredPost->featured_image)
                        <img src="{{ asset('storage/' . $featuredPost->featured_image) }}" 
                             alt="{{ $featuredPost->title }}"
                             class="rounded-xl shadow-lg">
                    @else
                        <div class="bg-white bg-opacity-10 rounded-xl p-6 backdrop-blur-sm">
                            <div class="space-y-3">
                                <div class="h-4 bg-white bg-opacity-20 rounded"></div>
                                <div class="h-4 bg-white bg-opacity-20 rounded w-4/5"></div>
                                <div class="h-4 bg-white bg-opacity-20 rounded w-3/5"></div>
                                <div class="h-32 bg-white bg-opacity-10 rounded-lg mt-4 flex items-center justify-center">
                                    <div class="text-6xl">📝</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    @endif

    @if($posts->count() > 0)
    <!-- Recent Posts -->
    <section>
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Recent Posts</h2>
            <a href="#" class="text-primary-600 hover:text-primary-700 font-medium">
                View All →
            </a>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($posts as $post)
            <article class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                <div class="aspect-video bg-gradient-to-br from-{{ $post->category->color }}-500 to-{{ $post->category->color }}-600 relative">
                    @if($post->featured_image)
                        <img src="{{ asset('storage/' . $post->featured_image) }}" 
                             alt="{{ $post->title }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="absolute inset-0 flex items-center justify-center text-white">
                            <div class="text-center">
                                <div class="text-4xl mb-2">📖</div>
                                <div class="text-sm font-medium">{{ strtoupper($post->category->name) }}</div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-4 text-sm text-gray-500 mb-3">
                        <span class="bg-{{ $post->category->color }}-100 text-{{ $post->category->color }}-700 px-3 py-1 rounded-full font-medium">
                            {{ $post->category->name }}
                        </span>
                        <span>{{ $post->published_date }}</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">
                        <a href="{{ route('blog.show', $post) }}" class="hover:text-primary-600 transition-colors">
                            {{ $post->title }}
                        </a>
                    </h3>
                    <p class="text-gray-600 mb-4">
                        {{ $post->excerpt }}
                    </p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <span>{{ $post->reading_time }}</span>
                            <span>•</span>
                            <span>{{ $post->views_count }} views</span>
                        </div>
                        <a href="{{ route('blog.show', $post) }}" class="text-primary-600 hover:text-primary-700 font-medium text-sm">
                            Read More
                        </a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Newsletter CTA -->
    <section class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-8 lg:p-12">
        <div class="max-w-2xl mx-auto text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">
                Stay Updated with Laravel News
            </h2>
            <p class="text-lg text-gray-600 mb-8">
                Dapatkan tutorial terbaru, tips, dan update Laravel langsung di inbox Anda. 
                Gratis, no spam, unsubscribe kapan saja.
            </p>
            <form class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                <input type="email" 
                       placeholder="Email address..." 
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <button type="submit" 
                        class="bg-primary-600 hover:bg-primary-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors">
                    Subscribe
                </button>
            </form>
        </div>
    </section>
</div>
@endsection
```

### Step 8: Update Sidebar dengan Data Real

Edit `resources/views/components/layout/sidebar.blade.php`:

```php
<aside class="space-y-6">
    <!-- About Widget -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-4">About This Blog</h3>
        <p class="text-gray-600 text-sm leading-relaxed mb-4">
            Tutorial Laravel terlengkap dalam bahasa Indonesia. Dari basic hingga advanced, 
            semua materi disusun secara sistematis untuk memudahkan pembelajaran.
        </p>
        <a href="{{ route('about') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
            Learn More →
        </a>
    </div>
    
    @if(isset($categories) && $categories->count() > 0)
    <!-- Categories Widget -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-4">Categories</h3>
        <div class="space-y-3">
            @foreach($categories as $category)
            <a href="{{ route('blog.category', $category) }}" class="flex items-center justify-between text-gray-600 hover:text-gray-900 transition-colors group">
                <span class="group-hover:text-primary-600">{{ $category->name }}</span>
                <span class="text-xs bg-gray-100 group-hover:bg-primary-100 text-gray-600 group-hover:text-primary-600 px-2 py-1 rounded-full">
                    {{ $category->published_posts_count }}
                </span>
            </a>
            @endforeach
        </div>
    </div>
    @endif
    
    <!-- Recent Posts Widget -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-4">Recent Posts</h3>
        <div class="space-y-4">
            @php
                $recentPostsSidebar = \App\Models\Post::published()
                    ->with(['author'])
                    ->recent(4)
                    ->get();
            @endphp
            
            @forelse($recentPostsSidebar as $post)
            <article class="group">
                <h4 class="font-medium text-gray-900 group-hover:text-primary-600 transition-colors mb-1 text-sm leading-tight">
                    <a href="{{ route('blog.show', $post) }}">
                        {{ $post->title }}
                    </a>
                </h4>
                <div class="flex items-center text-xs text-gray-500 space-x-2">
                    <span>{{ $post->published_date }}</span>
                    <span>•</span>
                    <span>{{ $post->views_count }} views</span>
                </div>
            </article>
            @empty
            <p class="text-gray-500 text-sm">No recent posts found.</p>
            @endforelse
        </div>
    </div>
    
    @if(isset($popularTags) && $popularTags->count() > 0)
    <!-- Popular Tags -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-4">Popular Tags</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($popularTags as $tag)
            <a href="{{ route('blog.tag', $tag) }}" 
               class="bg-gray-100 hover:bg-primary-100 text-gray-700 hover:text-primary-700 px-3 py-1 rounded-full text-sm transition-colors">
                {{ $tag->name }}
                <span class="text-xs opacity-75">({{ $tag->published_posts_count }})</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif
    
    <!-- Newsletter Signup -->
    <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-xl p-6">
        <h3 class="font-bold text-gray-900 mb-2">Subscribe Newsletter</h3>
        <p class="text-gray-600 text-sm mb-4">
            Get the latest Laravel tutorials and tips delivered to your inbox.
        </p>
        <form class="space-y-3">
            <input type="email" 
                   placeholder="Your email..." 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
            <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                Subscribe
            </button>
        </form>
    </div>
</aside>
```

## 💾 Membuat Sample Data

### Step 9: Update Seeders dengan Data Real

Edit `database/seeders/PostSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan ada user admin
        $admin = User::firstOrCreate([
            'email' => 'admin@blog.test'
        ], [
            'name' => 'Admin Blog',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'bio' => 'Administrator dan penulis utama blog Laravel Indonesia.',
            'email_verified_at' => now(),
        ]);

        // Get categories
        $laravelCategory = Category::where('slug', 'laravel-framework')->first();
        $phpCategory = Category::where('slug', 'php-programming')->first();

        // Get tags
        $laravelTag = Tag::where('slug', 'laravel')->first();
        $phpTag = Tag::where('slug', 'php')->first();
        $tutorialTag = Tag::where('slug', 'tutorial')->first();
        $beginnerTag = Tag::where('slug', 'beginner')->first();

        $posts = [
            [
                'title' => 'Memulai Perjalanan dengan Laravel 12',
                'excerpt' => 'Panduan lengkap untuk memulai project Laravel 12 dari nol hingga deploy. Pelajari semua dasar-dasar yang perlu Anda ketahui.',
                'content' => $this->getLaravelContent(),
                'status' => 'published',
                'is_featured' => true,
                'published_at' => now()->subDays(1),
                'user_id' => $admin->id,
                'category_id' => $laravelCategory->id,
                'views_count' => 156,
                'tags' => [$laravelTag, $tutorialTag, $beginnerTag],
            ],
            [
                'title' => 'Laravel Eloquent: Tips dan Tricks untuk Developer',
                'excerpt' => 'Kumpulan tips dan tricks Eloquent ORM yang akan membuat kode Laravel Anda lebih efisien dan maintainable.',
                'content' => $this->getEloquentContent(),
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now()->subDays(2),
                'user_id' => $admin->id,
                'category_id' => $laravelCategory->id,
                'views_count' => 124,
                'tags' => [$laravelTag, $phpTag],
            ],
            [
                'title' => 'Membuat REST API dengan Laravel Sanctum',
                'excerpt' => 'Tutorial step-by-step membuat REST API yang secure menggunakan Laravel Sanctum untuk authentication.',
                'content' => $this->getApiContent(),
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now()->subDays(3),
                'user_id' => $admin->id,
                'category_id' => $laravelCategory->id,
                'views_count' => 89,
                'tags' => [$laravelTag, $tutorialTag],
            ],
            [
                'title' => 'Optimasi Performa Aplikasi Laravel',
                'excerpt' => 'Teknik-teknik optimasi yang proven untuk meningkatkan performa aplikasi Laravel hingga 10x lebih cepat.',
                'content' => $this->getPerformanceContent(),
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now()->subDays(4),
                'user_id' => $admin->id,
                'category_id' => $laravelCategory->id,
                'views_count' => 203,
                'tags' => [$laravelTag, $phpTag],
            ],
            [
                'title' => 'PHP 8.3: Fitur Baru yang Wajib Diketahui',
                'excerpt' => 'Eksplorasi fitur-fitur terbaru di PHP 8.3 yang akan membantu development Anda menjadi lebih efisien.',
                'content' => $this->getPhpContent(),
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now()->subDays(5),
                'user_id' => $admin->id,
                'category_id' => $phpCategory->id,
                'views_count' => 67,
                'tags' => [$phpTag, $tutorialTag],
            ],
        ];

        foreach ($posts as $postData) {
            $tags = $postData['tags'];
            unset($postData['tags']);

            $post = Post::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($postData['title'])],
                $postData
            );

            // Sync tags (akan menghapus existing dan attach yang baru)
            $post->tags()->sync($tags);
        }
    }

    private function getLaravelContent()
    {
        return 'Laravel tutorial content...';
    }

    private function getEloquentContent()
    {
        return 'Eloquent tips and tricks content...';
    }

    private function getApiContent()
    {
        return 'Laravel Sanctum API content...';
    }

    private function getPerformanceContent()
    {
        return 'Laravel performance optimization content...';
    }

    private function getPhpContent()
    {
        return 'PHP 8.3 features content...';
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
            PostSeeder::class,
        ]);
    }
}
```

## 🚀 Testing dengan Data Real

### Step 10: Run Seeders dan Test

```bash
# Fresh install dengan data
php artisan migrate:fresh --seed

# Atau hanya seeders
php artisan db:seed
```

### 🔧 Troubleshooting Common Issues

#### Migration Duplikat Error

**Error yang mungkin terjadi:**
```
SQLSTATE[HY000]: General error: 1 table "categories" already exists
```

**Penyebab:** Ada 2 migration file yang sama-sama membuat tabel yang sama.

**Cara mengidentifikasi:**
```bash
php artisan migrate:status
```

Output yang bermasalah:
```
2025_09_15_025109_create_categories_table ...................... [Ran]
2025_09_15_034154_create_categories_table ...................... [Pending]
```

**Solusi:**
1. Identifikasi migration duplikat:
```bash
ls database/migrations/ | grep categories
```

2. Hapus migration yang lebih baru (timestamp lebih tinggi):
```bash
rm "database/migrations/2025_09_15_034154_create_categories_table.php"
```

3. Jalankan ulang migration:
```bash
php artisan migrate:fresh --seed
```

#### PostSeeder Syntax Error

**Error yang mungkin terjadi:**
```
syntax error, unexpected string content "", expecting ";"
```

**Penyebab:** String multiline tidak menggunakan heredoc syntax.

**Solusi:** Sudah diperbaiki dalam tutorial ini menggunakan heredoc syntax:
```php
private function getLaravelContent()
{
    return <<<'EOD'
# Content here
EOD;
}
```

### 📝 Best Practices untuk Migration

1. **Selalu cek status migration sebelum membuat yang baru:**
```bash
php artisan migrate:status
```

2. **Gunakan nama yang descriptive dan unique:**
```bash
# Good
php artisan make:migration create_categories_table
php artisan make:migration add_slug_to_categories_table

# Avoid duplicate names
```

3. **Periksa existing migrations sebelum membuat:**
```bash
ls database/migrations/ | grep categories
```

4. **Gunakan rollback jika perlu undo migration:**
```bash
php artisan migrate:rollback
php artisan migrate:rollback --step=2
```

Test aplikasi:

```bash
php artisan serve
npm run dev
```

Kunjungi:
- `http://127.0.0.1:8000/blog` - Homepage dengan data real
- `http://127.0.0.1:8000/blog/post/memulai-perjalanan-dengan-laravel-12` - Single post

## ✅ Verifikasi MVC Implementation

### Test di Tinker

```bash
php artisan tinker
```

```php
// Test Model relationships
$post = App\Models\Post::first();
$post->author->name; // Author name
$post->category->name; // Category name
$post->tags; // Collection of tags

// Test scopes
App\Models\Post::published()->count();
App\Models\Post::featured()->first();

// Test categories with post counts
App\Models\Category::withCount('publishedPosts')->get();
```


## 🎯 Kesimpulan

Selamat! Anda telah berhasil:
- ✅ Membangun Eloquent models yang lengkap dengan relationships
- ✅ Mengimplementasi arsitektur MVC yang proper
- ✅ Membuat database queries dengan Eloquent
- ✅ Mengganti data dummy dengan data real dari database
- ✅ Menambahkan scopes dan accessor/mutator
- ✅ Membuat seeders untuk sample data

Aplikasi blog sekarang sudah menggunakan data real dari database dengan struktur MVC yang proper. Di pelajaran selanjutnya, kita akan belajar tentang Eloquent relationships dan GET parameters.

---

**Selanjutnya:** [Pelajaran 8: Eloquent Relations and GET Parameters](08-eloquent-relations-get-parameters.md)

*MVC Architecture is ready! 🏗️*