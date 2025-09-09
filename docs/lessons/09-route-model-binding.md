# Pelajaran 9: Route Model Binding with Parameters

Route Model Binding adalah fitur powerful Laravel yang secara otomatis menginjeksi model instances ke dalam route callbacks atau controller methods. Dalam pelajaran ini, kita akan mendalami implementasi yang lebih advanced.

## 🎯 Tujuan Pembelajaran

Setelah menyelesaikan pelajaran ini, Anda akan:
- ✅ Memahami Implicit dan Explicit Route Model Binding
- ✅ Menggunakan custom keys untuk route binding
- ✅ Membuat binding constraints dan scoped bindings
- ✅ Handling missing models dengan custom responses
- ✅ Optimasi queries dalam route binding

## 🛣️ Implicit Route Model Binding

### Basic Implementation

Laravel secara otomatis menggunakan route model binding ketika parameter route sesuai dengan nama model:

```php
// routes/web.php
Route::get('/blog/post/{post}', [BlogController::class, 'show']);

// Controller method
public function show(Post $post)
{
    // $post sudah loaded secara otomatis
    return view('blog.show', compact('post'));
}
```

### Custom Route Key

Secara default, Laravel menggunakan `id`. Kita bisa menggunakan kolom lain dengan menentukan di model:

```php
// app/Models/Post.php
public function getRouteKeyName()
{
    return 'slug';
}

// app/Models/Category.php  
public function getRouteKeyName()
{
    return 'slug';
}

// app/Models/Tag.php
public function getRouteKeyName()
{
    return 'slug';
}
```

### Advanced Route Binding dengan Multiple Parameters

Update `routes/web.php` untuk lebih advanced:

```php
<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('blog.index');
});

// Blog routes dengan route model binding
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/search', [BlogController::class, 'search'])->name('search');
    
    // Single post dengan slug binding
    Route::get('/post/{post:slug}', [BlogController::class, 'show'])->name('show');
    
    // Category routes dengan nested binding
    Route::get('/category/{category:slug}', [CategoryController::class, 'show'])->name('category');
    Route::get('/category/{category:slug}/feed', [CategoryController::class, 'feed'])->name('category.feed');
    
    // Tag routes
    Route::get('/tag/{tag:slug}', [TagController::class, 'show'])->name('tag');
    Route::get('/tag/{tag:slug}/posts', [TagController::class, 'posts'])->name('tag.posts');
    
    // Author posts
    Route::get('/author/{user:id}', [BlogController::class, 'author'])->name('author');
    Route::get('/author/{user:id}/posts', [BlogController::class, 'authorPosts'])->name('author.posts');
    
    // Archive routes dengan parameter constraints
    Route::get('/archive/{year}', [BlogController::class, 'archive'])->name('archive.year')
         ->where('year', '[0-9]{4}');
    Route::get('/archive/{year}/{month}', [BlogController::class, 'archive'])->name('archive.month')
         ->where(['year' => '[0-9]{4}', 'month' => '[0-9]{1,2}']);
});

// Static pages
Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');
```

## 🎛️ Explicit Route Model Binding

### Custom Resolution Logic

Buat custom binding di `RouteServiceProvider`:

```php
// app/Providers/RouteServiceProvider.php
<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/blog';

    public function boot(): void
    {
        // Custom route model bindings
        Route::bind('post', function (string $value, Request $request) {
            // Find by slug, dengan published constraint untuk public routes
            $query = Post::where('slug', $value);
            
            // Jika bukan admin area, hanya show published posts
            if (!$request->is('admin/*')) {
                $query->where('status', 'published')
                      ->where('published_at', '<=', now());
            }
            
            return $query->with(['category', 'author', 'tags'])
                         ->firstOrFail();
        });

        Route::bind('category', function (string $value) {
            return Category::where('slug', $value)
                          ->where('is_active', true)
                          ->with(['posts' => function($query) {
                              $query->published()->limit(5);
                          }])
                          ->firstOrFail();
        });

        Route::bind('tag', function (string $value) {
            return Tag::where('slug', $value)
                     ->withCount(['posts' => function($query) {
                         $query->published();
                     }])
                     ->firstOrFail();
        });

        Route::bind('author', function (string $value) {
            return User::findOrFail($value);
        });

        $this->configureRateLimiting();
    }

    // ... rest of the class
}
```

## 🔧 Scoped Route Model Binding

### Nested Route Constraints

Untuk route yang nested, kita bisa menggunakan scoped binding:

```php
// Contoh: Post harus belong to Category
Route::get('/category/{category:slug}/post/{post:slug}', function (Category $category, Post $post) {
    // Laravel otomatis memastikan post belongs to category
    return view('blog.category-post', compact('category', 'post'));
})->scopeBindings();

// Atau dengan constraint manual di controller
Route::get('/category/{category:slug}/posts/{post:slug}', [BlogController::class, 'categoryPost']);
```

Controller method untuk scoped binding:

```php
// app/Http/Controllers/BlogController.php
public function categoryPost(Category $category, Post $post)
{
    // Pastikan post belongs to category
    if ($post->category_id !== $category->id) {
        abort(404, 'Post not found in this category');
    }
    
    return view('blog.category-post', compact('category', 'post'));
}
```

## 📊 Advanced Controllers dengan Route Model Binding

### CategoryController

Buat controller baru:

```bash
php artisan make:controller CategoryController
```

Edit `app/Http/Controllers/CategoryController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display category page dengan posts
     */
    public function show(Category $category, Request $request)
    {
        $filters = $request->only(['search', 'tag', 'sort']);
        
        // Get posts untuk category ini dengan filtering
        $postsQuery = $category->posts()
                             ->published()
                             ->with(['author', 'tags'])
                             ->when($filters['search'] ?? null, function ($query, $search) {
                                 $query->where(function ($query) use ($search) {
                                     $query->where('title', 'like', '%' . $search . '%')
                                           ->orWhere('content', 'like', '%' . $search . '%');
                                 });
                             })
                             ->when($filters['tag'] ?? null, function ($query, $tag) {
                                 $query->whereHas('tags', function ($query) use ($tag) {
                                     $query->where('slug', $tag);
                                 });
                             });

        // Apply sorting
        switch ($filters['sort'] ?? '') {
            case 'oldest':
                $postsQuery->orderBy('published_at', 'asc');
                break;
            case 'popular':
                $postsQuery->orderBy('views_count', 'desc');
                break;
            case 'title':
                $postsQuery->orderBy('title', 'asc');
                break;
            default:
                $postsQuery->orderBy('published_at', 'desc');
        }

        $posts = $postsQuery->paginate(12)->withQueryString();

        // Get featured post untuk category ini
        $featuredPost = $category->posts()
                                ->published()
                                ->where('is_featured', true)
                                ->first();

        // Get related categories
        $relatedCategories = Category::active()
                                   ->where('id', '!=', $category->id)
                                   ->withCount(['publishedPosts'])
                                   ->orderBy('published_posts_count', 'desc')
                                   ->limit(5)
                                   ->get();

        // Get popular tags dalam category ini
        $categoryTags = \DB::table('tags')
                          ->select('tags.*', \DB::raw('COUNT(post_tag.post_id) as posts_count'))
                          ->join('post_tag', 'tags.id', '=', 'post_tag.tag_id')
                          ->join('posts', 'post_tag.post_id', '=', 'posts.id')
                          ->where('posts.category_id', $category->id)
                          ->where('posts.status', 'published')
                          ->groupBy('tags.id', 'tags.name', 'tags.slug', 'tags.color', 'tags.created_at', 'tags.updated_at')
                          ->orderBy('posts_count', 'desc')
                          ->limit(10)
                          ->get()
                          ->map(function ($tag) {
                              return (object) [
                                  'id' => $tag->id,
                                  'name' => $tag->name,
                                  'slug' => $tag->slug,
                                  'color' => $tag->color,
                                  'posts_count' => $tag->posts_count
                              ];
                          });

        return view('blog.category', compact(
            'category',
            'posts', 
            'featuredPost',
            'relatedCategories',
            'categoryTags',
            'filters'
        ));
    }

    /**
     * Generate RSS feed untuk category
     */
    public function feed(Category $category)
    {
        $posts = $category->posts()
                         ->published()
                         ->with(['author'])
                         ->orderBy('published_at', 'desc')
                         ->limit(20)
                         ->get();

        return response()->view('blog.category-feed', compact('category', 'posts'))
                         ->header('Content-Type', 'application/xml');
    }
}
```

### TagController

```bash
php artisan make:controller TagController
```

Edit `app/Http/Controllers/TagController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Category;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display tag page
     */
    public function show(Tag $tag, Request $request)
    {
        $filters = $request->only(['search', 'category', 'sort']);

        $postsQuery = $tag->posts()
                        ->published()
                        ->with(['category', 'author']);

        // Apply filters
        if ($filters['search'] ?? null) {
            $postsQuery->where(function ($query) use ($filters) {
                $query->where('title', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('content', 'like', '%' . $filters['search'] . '%');
            });
        }

        if ($filters['category'] ?? null) {
            $postsQuery->whereHas('category', function ($query) use ($filters) {
                $query->where('slug', $filters['category']);
            });
        }

        // Apply sorting
        switch ($filters['sort'] ?? '') {
            case 'oldest':
                $postsQuery->orderBy('published_at', 'asc');
                break;
            case 'popular':
                $postsQuery->orderBy('views_count', 'desc');
                break;
            default:
                $postsQuery->orderBy('published_at', 'desc');
        }

        $posts = $postsQuery->paginate(12)->withQueryString();

        // Get categories yang memiliki posts dengan tag ini
        $tagCategories = Category::whereHas('posts.tags', function ($query) use ($tag) {
                                  $query->where('tags.id', $tag->id);
                              })
                              ->withCount(['publishedPosts'])
                              ->orderBy('name')
                              ->get();

        // Get related tags
        $relatedTags = Tag::whereHas('posts', function ($query) use ($tag) {
                            $query->whereHas('tags', function ($query) use ($tag) {
                                $query->where('tags.id', $tag->id);
                            });
                        })
                        ->where('id', '!=', $tag->id)
                        ->withCount(['publishedPosts'])
                        ->orderBy('published_posts_count', 'desc')
                        ->limit(10)
                        ->get();

        return view('blog.tag', compact(
            'tag',
            'posts',
            'tagCategories', 
            'relatedTags',
            'filters'
        ));
    }

    /**
     * API endpoint untuk posts dengan tag tertentu
     */
    public function posts(Tag $tag, Request $request)
    {
        $posts = $tag->posts()
                   ->published()
                   ->with(['category', 'author'])
                   ->orderBy('published_at', 'desc')
                   ->paginate(10);

        if ($request->wantsJson()) {
            return response()->json([
                'tag' => $tag->only(['name', 'slug']),
                'posts' => $posts->items(),
                'pagination' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ]
            ]);
        }

        return view('blog.tag-posts', compact('tag', 'posts'));
    }
}
```

## 🎨 Update Views untuk Route Model Binding

### Update Category View

Edit `resources/views/blog/category.blade.php`:

```html
@extends('layouts.app')

@section('title', $category->name . ' - Blog Laravel')
@section('description', $category->description)

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Category Header -->
    <div class="bg-white rounded-xl shadow-sm p-8 mb-8">
        <div class="text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center text-white text-2xl font-bold"
                 style="background-color: {{ $category->color }}">
                {{ strtoupper(substr($category->name, 0, 1)) }}
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $category->name }}</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-6">
                {{ $category->description }}
            </p>
            <div class="text-sm text-gray-500">
                {{ $posts->total() }} {{ Str::plural('artikel', $posts->total()) }} dalam kategori ini
            </div>
        </div>
    </div>

    @if($featuredPost)
    <!-- Featured Post -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-xl text-white p-8 mb-8">
        <div class="lg:grid lg:grid-cols-2 lg:gap-8 items-center">
            <div>
                <div class="inline-flex items-center bg-yellow-500 text-yellow-900 px-3 py-1 rounded-full text-sm font-medium mb-4">
                    Featured
                </div>
                <h2 class="text-2xl lg:text-3xl font-bold mb-3">
                    {{ $featuredPost->title }}
                </h2>
                <p class="text-lg text-primary-100 mb-4">
                    {{ $featuredPost->excerpt }}
                </p>
                <a href="{{ route('blog.show', $featuredPost) }}" 
                   class="inline-flex items-center bg-white text-primary-600 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                    Baca Artikel
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    @endif

    <div class="grid lg:grid-cols-4 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-3">
            <!-- Filter Bar -->
            @if($categoryTags->count() > 0)
            <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900">Filter by Tags:</h3>
                    @if(request('tag'))
                        <a href="{{ route('blog.category', $category) }}" 
                           class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                            Clear Filter
                        </a>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($categoryTags as $tag)
                    <a href="{{ route('blog.category', array_merge(['category' => $category->slug], request()->only(['search', 'sort']), ['tag' => $tag->slug])) }}"
                       class="px-3 py-1 rounded-full text-sm font-medium transition-colors
                              {{ request('tag') == $tag->slug 
                                 ? 'bg-primary-600 text-white' 
                                 : 'bg-gray-100 text-gray-700 hover:bg-primary-100' }}">
                        {{ $tag->name }} ({{ $tag->posts_count }})
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if($posts->count() > 0)
            <!-- Posts Grid -->
            <div class="grid md:grid-cols-2 gap-6">
                @foreach($posts as $post)
                <article class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                    <div class="aspect-video bg-gradient-to-br from-{{ $category->color ?? 'gray' }}-400 to-{{ $category->color ?? 'gray' }}-600 relative">
                        @if($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                 alt="{{ $post->title }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center text-white">
                                <div class="text-center">
                                    <div class="text-3xl mb-2">📖</div>
                                    <div class="text-xs font-medium">{{ strtoupper($category->name) }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-6">
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
                            <span>{{ $post->published_date }}</span>
                            <span>{{ $post->reading_time }}</span>
                        </div>
                        
                        <h3 class="text-lg font-bold text-gray-900 mb-3">
                            <a href="{{ route('blog.show', $post) }}" 
                               class="hover:text-primary-600 transition-colors">
                                {{ $post->title }}
                            </a>
                        </h3>
                        
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                            {{ $post->excerpt }}
                        </p>
                        
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                By {{ $post->author->name }}
                            </div>
                            @if($post->tags->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($post->tags->take(2) as $tag)
                                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </article>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $posts->links() }}
            </div>
            @else
            <!-- No Posts -->
            <div class="text-center py-12">
                <div class="text-6xl mb-4">📝</div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Belum ada artikel</h2>
                <p class="text-gray-600">Kategori ini belum memiliki artikel yang dipublikasi.</p>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Category Info -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">About This Category</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Total Posts:</span>
                        <span class="font-medium">{{ $posts->total() }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Created:</span>
                        <span class="font-medium">{{ $category->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>

            @if($relatedCategories->count() > 0)
            <!-- Related Categories -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Related Categories</h3>
                <div class="space-y-3">
                    @foreach($relatedCategories as $relatedCategory)
                    <a href="{{ route('blog.category', $relatedCategory) }}" 
                       class="flex items-center justify-between text-gray-600 hover:text-primary-600 transition-colors">
                        <span>{{ $relatedCategory->name }}</span>
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">
                            {{ $relatedCategory->published_posts_count }}
                        </span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
```

## 🛡️ Error Handling untuk Route Model Binding

### Custom 404 Responses

Buat custom error handling di model:

```php
// app/Models/Post.php
public function resolveRouteBinding($value, $field = null)
{
    // Custom logic untuk route binding
    $post = $this->where($field ?? $this->getRouteKeyName(), $value);
    
    // Tambahkan constraint untuk published posts di public routes
    if (!request()->is('admin/*')) {
        $post->where('status', 'published')
             ->where('published_at', '<=', now());
    }
    
    return $post->firstOrFail();
}
```

### Missing Model Callback

Di `RouteServiceProvider`, tambahkan handling untuk missing models:

```php
// app/Providers/RouteServiceProvider.php
public function boot(): void
{
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
}
```

## 🎯 Kesimpulan

Selamat! Anda telah menguasai:
- ✅ Implicit dan Explicit Route Model Binding
- ✅ Custom route keys dan constraints
- ✅ Scoped bindings untuk nested routes
- ✅ Advanced controllers dengan model binding
- ✅ Custom error handling untuk missing models
- ✅ Performance optimization dalam route binding

Route Model Binding membuat code Anda lebih clean dan Laravel akan handle semua dependency injection secara otomatis. Di pelajaran selanjutnya, kita akan mengimplementasi authentication dengan Laravel Breeze.

---

**Selanjutnya:** [Pelajaran 10: Starter Kits and Laravel Breeze](10-laravel-breeze.md)

*Route Model Binding mastered! 🎯*