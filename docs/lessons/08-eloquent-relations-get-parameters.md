# Pelajaran 8: Eloquent Relations and GET Parameters

Dalam pelajaran ini, kita akan mendalami Eloquent relationships dan mengimplementasi GET parameters untuk filtering dan searching di aplikasi blog kita.

## 🎯 Tujuan Pembelajaran

Setelah menyelesaikan pelajaran ini, Anda akan:
- ✅ Memahami semua jenis Eloquent relationships
- ✅ Mengimplementasi GET parameters untuk filtering
- ✅ Membuat search functionality
- ✅ Menambahkan pagination dengan parameters
- ✅ Optimasi query dengan relationship constraints
- ✅ Mengimplementasi Custom Route Model Binding

## 🛣️ Custom Route Model Binding

Sebelum masuk ke relationships yang lebih dalam, mari kita setup Route Model Binding yang profesional untuk aplikasi blog kita. Ini akan membuat URL lebih SEO-friendly dan menambahkan kontrol akses yang proper.

### Apa itu Route Model Binding?

Route Model Binding adalah fitur Laravel yang secara otomatis mengkonversi parameter URL menjadi model instances. Alih-alih menerima ID dan melakukan query manual di controller, Laravel akan otomatis melakukan query dan inject model ke controller method.

### Step 1: Setup Custom Route Model Binding

Edit file `app/Providers/RouteServiceProvider.php`. **Tambahkan** custom bindings setelah RateLimiter configuration:

```php
<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // ... existing bindings ...

        Route::bind('post', function (string $value, Request $request) {
            $query = Post::where('slug', $value);

            if (!$request->is('admin/*')) {
                $query->where('status', 'published')
                      ->where('published_at', '<=', now());
            }

            $post = $query->with(['category', 'author', 'tags'])->first();

            if (!$post) {
                abort(404, 'Post tidak ditemukan atau belum dipublikasi');
            }

            return $post;
        });

        Route::bind('category', function (string $value, Request $request) {
            $query = Category::where('slug', $value);

            if (!$request->is('admin/*')) {
                $query->where('is_active', true);
            }

            $category = $query->first();

            if (!$category) {
                abort(404, 'Kategori tidak ditemukan');
            }

            return $category;
        });

        Route::bind('tag', function (string $value, Request $request) {
            $tag = Tag::where('slug', $value)->first();

            if (!$tag) {
                abort(404, 'Tag tidak ditemukan');
            }

            return $tag;
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
```

**Catatan**: Komentar `// ... existing bindings ...` menandakan bahwa mungkin ada binding lain yang sudah ada. Anda tinggal menambahkan 3 binding baru (post, category, tag) setelah RateLimiter configuration.

### Step 2: Perbandingan Sebelum vs Sesudah

#### Sebelum (Default Laravel):

```php
// routes/web.php
Route::get('/blog/post/{post}', [BlogController::class, 'show']);

// URL: /blog/post/123 (menggunakan ID)
// Controller menerima: Post model dengan ID 123
// Laravel query: Post::findOrFail(123)
// Masalah:
// - URL tidak SEO friendly
// - Bisa akses draft post
// - Error 404 generic
```

#### Sesudah (Custom Binding):

```php
// routes/web.php
Route::get('/blog/post/{post:slug}', [BlogController::class, 'show']);

// URL: /blog/post/laravel-eloquent-tips (menggunakan slug)
// Controller menerima: Post model lengkap dengan relationships
// Custom query: Post::where('slug', 'laravel-eloquent-tips')
//                   ->where('status', 'published')
//                   ->where('published_at', '<=', now())
//                   ->with(['category', 'author', 'tags'])
//                   ->first()
// Keuntungan:
// ✅ URL SEO friendly
// ✅ Auto filter published posts
// ✅ Eager loading relationships
// ✅ Custom error messages
```

### Step 3: Keuntungan Implementasi

#### 1. SEO-Friendly URLs:
```
❌ /blog/post/123
✅ /blog/post/memulai-perjalanan-dengan-laravel-12
```

#### 2. Security & Privacy:
```php
// User biasa tidak bisa akses draft
// URL: /blog/post/my-draft-post → 404 dengan pesan custom

// Admin tetap bisa akses semua
// URL: /admin/posts/my-draft-post → berhasil diakses
```

#### 3. Performance:
```php
// Eager loading otomatis di binding
$post = // sudah include category, author, tags
// Tidak perlu query N+1 di controller
```

#### 4. Better User Experience:
```
❌ "404 Not Found"
✅ "Post tidak ditemukan atau belum dipublikasi"
```

### Step 4: Cara Kerja Route Model Binding

```
1. User akses: /blog/post/laravel-tips
2. Laravel extract parameter: {post} = "laravel-tips"
3. Custom binding triggered: Route::bind('post', function('laravel-tips', $request))
4. Query executed: Cari post dengan slug + filter status
5. Result: Post object dengan relationships di-inject ke controller
6. Controller receives: public function show(Post $post) // $post sudah siap pakai
```

### Step 5: Update Controller Methods

Karena sekarang binding sudah handle semua logic, controller jadi lebih bersih:

```php
// app/Http/Controllers/BlogController.php

public function show(Post $post)
{
    // $post sudah:
    // - Terfilter (published only untuk user biasa)
    // - Include relationships (category, author, tags)
    // - 404 handling sudah di binding

    // Tambah view count
    $post->incrementViews();

    // Langsung return view
    return view('blog.show', compact('post'));
}

public function category(Category $category)
{
    // $category sudah:
    // - Terfilter (active only untuk user biasa)
    // - 404 handling sudah di binding

    $posts = $category->publishedPosts()
                     ->with(['author', 'tags'])
                     ->paginate(10);

    return view('blog.category', compact('category', 'posts'));
}

public function tag(Tag $tag)
{
    // $tag sudah ter-handle di binding

    $posts = $tag->publishedPosts()
                 ->with(['category', 'author'])
                 ->paginate(10);

    return view('blog.tag', compact('tag', 'posts'));
}
```

### Step 6: Test Custom Route Model Binding

```bash
# Test URL yang berfungsi
http://127.0.0.1:8000/blog/post/memulai-perjalanan-dengan-laravel-12
http://127.0.0.1:8000/blog/category/laravel-framework
http://127.0.0.1:8000/blog/tag/laravel

# Test error handling
http://127.0.0.1:8000/blog/post/nonexistent-post
# Response: 404 "Post tidak ditemukan atau belum dipublikasi"

http://127.0.0.1:8000/blog/category/inactive-category
# Response: 404 "Kategori tidak ditemukan"
```

Route Model Binding ini adalah fondasi yang sangat powerful untuk aplikasi blog yang profesional! 🚀

## 🔗 Deep Dive: Eloquent Relationships

### One-to-Many Relationship (Category → Posts)

Mari kita perbaiki relationship dan menambahkan fitur filtering:

```php
// app/Models/Category.php - Enhanced version
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    // ... existing code ...

    /**
     * Get posts with filtering options
     */
    public function posts(): HasMany 
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get published posts with optional filters
     */
    public function publishedPosts($filters = [])
    {
        $query = $this->posts()
                      ->where('status', 'published')
                      ->where('published_at', '<=', now());

        // Apply filters
        if (isset($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('content', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['tag'])) {
            $query->whereHas('tags', function($q) use ($filters) {
                $q->where('slug', $filters['tag']);
            });
        }

        return $query->with(['author', 'tags'])
                    ->orderBy('published_at', 'desc');
    }
}
```

### Many-to-Many Relationship (Posts ↔ Tags)

Update Post model untuk better relationship handling:

```php
// app/Models/Post.php - Enhanced version
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    // ... existing code ...

    /**
     * Tags relationship with pivot data
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)
                    ->withTimestamps();
    }

    /**
     * Scope untuk filtering berdasarkan GET parameters
     */
    public function scopeFilter($query, array $filters)
    {
        // Search filter
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                      ->orWhere('content', 'like', '%' . $search . '%')
                      ->orWhere('excerpt', 'like', '%' . $search . '%');
            });
        });

        // Category filter
        $query->when($filters['category'] ?? null, function ($query, $category) {
            $query->whereHas('category', function ($query) use ($category) {
                $query->where('slug', $category);
            });
        });

        // Tag filter
        $query->when($filters['tag'] ?? null, function ($query, $tag) {
            $query->whereHas('tags', function ($query) use ($tag) {
                $query->where('slug', $tag);
            });
        });

        // Author filter
        $query->when($filters['author'] ?? null, function ($query, $author) {
            $query->whereHas('author', function ($query) use ($author) {
                $query->where('name', 'like', '%' . $author . '%');
            });
        });

        // Date range filter
        $query->when($filters['from'] ?? null, function ($query, $from) {
            $query->where('published_at', '>=', $from);
        });

        $query->when($filters['to'] ?? null, function ($query, $to) {
            $query->where('published_at', '<=', $to);
        });

        // Sort options
        $query->when($filters['sort'] ?? null, function ($query, $sort) {
            switch ($sort) {
                case 'oldest':
                    return $query->orderBy('published_at', 'asc');
                case 'popular':
                    return $query->orderBy('views_count', 'desc');
                case 'title':
                    return $query->orderBy('title', 'asc');
                default:
                    return $query->orderBy('published_at', 'desc');
            }
        }, function ($query) {
            return $query->orderBy('published_at', 'desc');
        });
    }
}
```

## 🔍 Implementasi Search & Filter Functionality

### Step 1: Update BlogController

Edit `app/Http/Controllers/BlogController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display blog homepage dengan search dan filter
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'category', 'tag', 'author', 'sort']);
        
        // Get featured post (jika tidak ada filter)
        $featuredPost = null;
        if (empty(array_filter($filters))) {
            $featuredPost = Post::published()
                              ->featured()
                              ->with(['category', 'author'])
                              ->first();
        }

        // Get posts dengan filtering
        $postsQuery = Post::published()
                        ->with(['category', 'author', 'tags'])
                        ->filter($filters);

        // Exclude featured post jika ada
        if ($featuredPost) {
            $postsQuery->where('id', '!=', $featuredPost->id);
        }

        $posts = $postsQuery->paginate(9)->withQueryString();

        // Get filter options untuk UI
        $categories = Category::active()
                            ->withCount(['publishedPosts'])
                            ->orderBy('name')
                            ->get();

        $popularTags = Tag::has('posts')
                         ->withCount(['publishedPosts'])
                         ->orderBy('published_posts_count', 'desc')
                         ->limit(20)
                         ->get();

        $authors = User::has('posts')
                     ->withCount(['publishedPosts'])
                     ->orderBy('name')
                     ->get();

        return view('blog.index', compact(
            'featuredPost', 
            'posts',
            'categories', 
            'popularTags',
            'authors',
            'filters'
        ));
    }

    /**
     * Display single post dengan related posts yang lebih smart
     */
    public function show(Post $post)
    {
        // Increment views
        $post->incrementViews();

        // Load relationships
        $post->load(['category', 'author', 'tags']);

        // Get related posts berdasarkan tags dan category
        $relatedPosts = Post::published()
                          ->where('id', '!=', $post->id)
                          ->where(function ($query) use ($post) {
                              // Posts dengan category yang sama
                              $query->where('category_id', $post->category_id)
                                    // Atau posts dengan tags yang sama
                                    ->orWhereHas('tags', function ($query) use ($post) {
                                        $query->whereIn('tags.id', $post->tags->pluck('id'));
                                    });
                          })
                          ->with(['category', 'author'])
                          ->orderBy('published_at', 'desc')
                          ->limit(6)
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

        // Get other posts from same author
        $authorPosts = Post::published()
                         ->where('user_id', $post->user_id)
                         ->where('id', '!=', $post->id)
                         ->with(['category'])
                         ->limit(3)
                         ->get();

        return view('blog.show', compact(
            'post', 
            'relatedPosts', 
            'previousPost', 
            'nextPost',
            'authorPosts'
        ));
    }

    /**
     * Display posts by category dengan pagination dan filter
     */
    public function category(Category $category, Request $request)
    {
        $filters = $request->only(['search', 'tag', 'sort']);
        
        $postsQuery = Post::published()
                        ->where('category_id', $category->id)
                        ->with(['author', 'tags'])
                        ->filter($filters);

        $posts = $postsQuery->paginate(12)->withQueryString();

        // Get tags untuk filter dalam category ini
        $categoryTags = Tag::whereHas('posts', function ($query) use ($category) {
                             $query->where('category_id', $category->id);
                         })
                         ->withCount(['publishedPosts'])
                         ->orderBy('name')
                         ->get();

        return view('blog.category', compact(
            'category', 
            'posts', 
            'categoryTags',
            'filters'
        ));
    }

    /**
     * Display posts by tag dengan pagination
     */
    public function tag(Tag $tag, Request $request)
    {
        $filters = $request->only(['search', 'category', 'sort']);

        $postsQuery = $tag->posts()
                        ->published()
                        ->with(['category', 'author'])
                        ->filter($filters);

        $posts = $postsQuery->paginate(12)->withQueryString();

        // Get categories yang ada posts dengan tag ini
        $tagCategories = Category::whereHas('posts.tags', function ($query) use ($tag) {
                                   $query->where('tags.id', $tag->id);
                               })
                               ->withCount(['publishedPosts'])
                               ->orderBy('name')
                               ->get();

        return view('blog.tag', compact(
            'tag', 
            'posts', 
            'tagCategories',
            'filters'
        ));
    }

    /**
     * Search functionality
     */
    public function search(Request $request)
    {
        $search = $request->get('q');
        $filters = array_merge($request->only(['category', 'tag', 'sort']), ['search' => $search]);

        if (empty($search)) {
            return redirect()->route('blog.index');
        }

        $postsQuery = Post::published()
                        ->with(['category', 'author', 'tags'])
                        ->filter($filters);

        $posts = $postsQuery->paginate(12)->withQueryString();

        // Search statistics
        $totalResults = $posts->total();
        
        // Get suggested categories dan tags berdasarkan hasil search
        $suggestedCategories = Category::whereHas('posts', function ($query) use ($search) {
                                        $query->where('title', 'like', '%' . $search . '%')
                                              ->orWhere('content', 'like', '%' . $search . '%');
                                    })
                                    ->withCount(['publishedPosts'])
                                    ->limit(5)
                                    ->get();

        $suggestedTags = Tag::whereHas('posts', function ($query) use ($search) {
                              $query->where('title', 'like', '%' . $search . '%')
                                    ->orWhere('content', 'like', '%' . $search . '%');
                          })
                          ->withCount(['publishedPosts'])
                          ->limit(10)
                          ->get();

        return view('blog.search', compact(
            'posts',
            'search',
            'totalResults',
            'suggestedCategories',
            'suggestedTags',
            'filters'
        ));
    }
}
```

### Step 2: Update Routes

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
Route::get('/blog/search', [BlogController::class, 'search'])->name('blog.search');
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

## 🎨 Update Views dengan Filter UI

### Step 3: Create Search Results View

Buat `resources/views/blog/search.blade.php`:

```html
@extends('layouts.app')

@section('title', 'Search: ' . $search . ' - Blog Laravel')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Search Header -->
    <div class="bg-white rounded-xl shadow-sm p-8 mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">
            Search Results for "{{ $search }}"
        </h1>
        <p class="text-gray-600 mb-6">
            Found {{ $totalResults }} {{ Str::plural('result', $totalResults) }} 
            @if($totalResults > 0)
                for your search
            @endif
        </p>

        <!-- Search Form -->
        <form action="{{ route('blog.search') }}" method="GET" class="flex gap-4">
            <div class="flex-1">
                <input type="text" 
                       name="q"
                       value="{{ $search }}"
                       placeholder="Search posts..."
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                Search
            </button>
        </form>
    </div>

    @if($posts->count() > 0)
        <div class="grid lg:grid-cols-4 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-3">
                <!-- Filter Bar -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <form method="GET" action="{{ route('blog.search') }}" class="flex flex-wrap gap-4">
                        <input type="hidden" name="q" value="{{ $search }}">
                        
                        <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">All Categories</option>
                            @foreach($suggestedCategories as $category)
                                <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ $category->published_posts_count }})
                                </option>
                            @endforeach
                        </select>

                        <select name="sort" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Sort by: Newest</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                            <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title A-Z</option>
                        </select>

                        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Apply Filters
                        </button>
                    </form>
                </div>

                <!-- Results -->
                <div class="space-y-6">
                    @foreach($posts as $post)
                    <article class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
                        <div class="flex flex-col md:flex-row gap-6">
                            <!-- Featured Image -->
                            <div class="md:w-48 md:h-32 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                @if($post->featured_image)
                                    <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                         alt="{{ $post->title }}"
                                         class="w-full h-full object-cover rounded-lg">
                                @else
                                    <div class="text-white text-center">
                                        <div class="text-2xl mb-1">📄</div>
                                        <div class="text-xs">{{ $post->category->name }}</div>
                                    </div>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="flex-1">
                                <div class="flex items-center space-x-4 text-sm text-gray-500 mb-3">
                                    <span class="bg-{{ $post->category->color ?? 'gray' }}-100 text-{{ $post->category->color ?? 'gray' }}-700 px-3 py-1 rounded-full font-medium">
                                        {{ $post->category->name }}
                                    </span>
                                    <span>{{ $post->published_date }}</span>
                                    <span>{{ $post->author->name }}</span>
                                </div>

                                <h3 class="text-xl font-bold text-gray-900 mb-3">
                                    <a href="{{ route('blog.show', $post) }}" class="hover:text-primary-600 transition-colors">
                                        {{ $post->title }}
                                    </a>
                                </h3>

                                <p class="text-gray-600 mb-4 line-clamp-2">
                                    {{ $post->excerpt }}
                                </p>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <span>{{ $post->reading_time }}</span>
                                        <span>•</span>
                                        <span>{{ $post->views_count }} views</span>
                                    </div>
                                    
                                    @if($post->tags->count() > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($post->tags->take(3) as $tag)
                                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">
                                                    {{ $tag->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $posts->links() }}
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                @if($suggestedCategories->count() > 0)
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <h3 class="font-bold text-gray-900 mb-4">Related Categories</h3>
                    <div class="space-y-2">
                        @foreach($suggestedCategories as $category)
                        <a href="{{ route('blog.category', $category) }}" 
                           class="block text-gray-600 hover:text-primary-600 transition-colors">
                            {{ $category->name }} ({{ $category->published_posts_count }})
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($suggestedTags->count() > 0)
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Related Tags</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($suggestedTags as $tag)
                        <a href="{{ route('blog.tag', $tag) }}" 
                           class="bg-gray-100 hover:bg-primary-100 text-gray-700 hover:text-primary-700 px-3 py-1 rounded-full text-sm transition-colors">
                            {{ $tag->name }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    @else
        <!-- No Results -->
        <div class="text-center py-16">
            <div class="text-6xl mb-4">🔍</div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">No posts found</h2>
            <p class="text-gray-600 mb-8">
                Try adjusting your search terms or browse our categories
            </p>
            <a href="{{ route('blog.index') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                Browse All Posts
            </a>
        </div>
    @endif
</div>
@endsection
```

### Step 4: Update Navigation dengan Search

Edit `resources/views/components/layout/navigation.blade.php` untuk menambahkan search form:

```html
<!-- Update bagian search di navigation -->
<div class="relative">
    <form action="{{ route('blog.search') }}" method="GET" class="relative">
        <input type="text" 
               name="q"
               value="{{ request('q') }}"
               placeholder="Cari artikel..." 
               class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
        <button type="submit" class="absolute left-3 top-2.5 text-gray-400 hover:text-gray-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </button>
    </form>
</div>
```

## 🧪 Testing GET Parameters

### Step 5: Test Filter Functionality

Test berbagai URL dengan GET parameters:

```bash
# Basic search
/blog/search?q=laravel

# Search dengan category filter  
/blog/search?q=laravel&category=laravel-framework

# Category page dengan tag filter
/blog/category/laravel-framework?tag=tutorial

# Sorting
/blog?sort=popular

# Multiple filters
/blog?search=eloquent&category=laravel-framework&sort=oldest
```

### Database Query Optimization

Untuk memastikan performance yang baik:

```php
// Add indexes di migration
Schema::table('posts', function (Blueprint $table) {
    $table->index(['status', 'published_at']);
    $table->index(['category_id', 'status']);
    $table->fullText(['title', 'content', 'excerpt']); // MySQL 5.7+
});
```

## 🎯 Kesimpulan

Selamat! Anda telah berhasil:
- ✅ Mengimplementasi advanced Eloquent relationships
- ✅ Membuat sistem filtering dengan GET parameters
- ✅ Menambahkan search functionality yang powerful
- ✅ Mengoptimalkan query performance
- ✅ Membuat UI yang user-friendly untuk filtering

Di pelajaran selanjutnya, kita akan belajar Route Model Binding dengan parameters yang lebih advanced.

---

**Selanjutnya:** [Pelajaran 9: Route Model Binding with Parameters](09-route-model-binding.md)

*Advanced relationships ready! 🔗*