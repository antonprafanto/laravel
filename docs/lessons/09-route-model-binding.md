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

### Advanced Custom Bindings

Selain binding dasar di atas, kita juga bisa menambahkan binding yang lebih advanced:

```php
// Di RouteServiceProvider.php, tambahkan binding lainnya:

Route::bind('user', function (string $value) {
    return User::where('username', $value)
               ->where('is_active', true)
               ->firstOrFail();
});

Route::bind('published_post', function (string $value) {
    return Post::published()
               ->where('slug', $value)
               ->with(['category', 'author', 'tags'])
               ->firstOrFail();
});

Route::bind('author', function (string $value) {
    return User::where('username', $value)
               ->orWhere('id', $value)
               ->firstOrFail();
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
use Illuminate\Support\Facades\DB; 

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
        $categoryTags = DB::table('tags')
                          ->select('tags.*', DB::raw('COUNT(post_tag.post_id) as posts_count'))
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

### Update Tag View

Edit `resources/views/blog/tag.blade.php`:

```html
@extends('layouts.app')

@section('title', 'Tag: ' . $tag->name . ' - Blog Laravel')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Tag Header -->
    <div class="bg-white rounded-xl shadow-sm p-8 mb-8">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full mb-4">
                <span class="text-purple-600 text-2xl font-bold">#</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-4"># {{ $tag->name }}</h1>
            <div class="text-sm text-gray-500">
                {{ $posts->total() }} {{ Str::plural('artikel', $posts->total()) }} dengan tag ini
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Posts Grid -->
        <div class="lg:col-span-2">
            @if($posts->count() > 0)
            <div class="grid gap-6">
                @foreach($posts as $post)
                <article class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <div class="md:flex">
                        <div class="md:w-48 bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center">
                            <div class="text-white text-center p-6">
                                <div class="text-3xl mb-2">#️⃣</div>
                                <div class="text-sm font-medium">{{ $post->category->name }}</div>
                            </div>
                        </div>
                        <div class="flex-1 p-6">
                            <div class="flex items-center space-x-4 text-sm text-gray-500 mb-3">
                                <span class="bg-{{ $post->category->color ?? 'gray' }}-100 text-{{ $post->category->color ?? 'gray' }}-700 px-3 py-1 rounded-full font-medium">
                                    {{ $post->category->name }}
                                </span>
                                <span>{{ $post->published_at->format('M d, Y') }}</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3 hover:text-primary-600 transition-colors">
                                <a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a>
                            </h3>
                            <p class="text-gray-600 mb-4">{{ $post->excerpt }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">By {{ $post->user->name }}</span>
                                <a href="{{ route('blog.show', $post) }}" class="text-primary-600 hover:text-primary-700 font-medium text-sm">
                                    Read More →
                                </a>
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
            @else
            <div class="text-center py-12">
                <div class="text-6xl mb-4">#️⃣</div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Tidak ada artikel</h2>
                <p class="text-gray-600">Belum ada artikel dengan tag "{{ $tag->name }}".</p>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Tag Stats -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Tag Statistics</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Total Posts:</span>
                        <span class="font-medium">{{ $posts->total() }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Categories:</span>
                        <span class="font-medium">{{ $tagCategories->count() }}</span>
                    </div>
                </div>
            </div>

            @if(isset($relatedTags) && $relatedTags->count() > 0)
            <!-- Related Tags -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Related Tags</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($relatedTags as $relatedTag)
                    <a href="{{ route('blog.tag', $relatedTag) }}"
                       class="inline-flex items-center bg-gray-100 text-gray-700 px-3 py-1 rounded-full hover:bg-primary-100 hover:text-primary-700 transition-colors text-sm">
                        #{{ $relatedTag->name }}
                        <span class="ml-1 text-xs">({{ $relatedTag->published_posts_count }})</span>
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

### Update Search View

Edit `resources/views/blog/search.blade.php`:

```html
@extends('layouts.app')

@section('title', 'Search: ' . $search . ' - Blog Laravel')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Search Header -->
    <div class="bg-white rounded-xl shadow-sm p-8 mb-8">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1 0 5.25 5.25a7.5 7.5 0 0 0 11.4 11.4z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Search Results</h1>
            <p class="text-lg text-gray-600">
                Showing results for: <strong>"{{ $search }}"</strong>
            </p>
            <div class="text-sm text-gray-500 mt-2">
                {{ $totalResults }} {{ Str::plural('result', $totalResults) }} found
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Search Results -->
        <div class="lg:col-span-2">
            @if($posts->count() > 0)
            <div class="space-y-6">
                @foreach($posts as $post)
                <article class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <div class="md:flex">
                        <div class="md:w-48 bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center">
                            <div class="text-white text-center p-6">
                                <div class="text-3xl mb-2">🔍</div>
                                <div class="text-sm font-medium">{{ $post->category->name }}</div>
                            </div>
                        </div>
                        <div class="flex-1 p-6">
                            <div class="flex items-center space-x-4 text-sm text-gray-500 mb-3">
                                <span class="bg-{{ $post->category->color ?? 'gray' }}-100 text-{{ $post->category->color ?? 'gray' }}-700 px-3 py-1 rounded-full font-medium">
                                    {{ $post->category->name }}
                                </span>
                                <span>{{ $post->published_at->format('M d, Y') }}</span>
                                <span>•</span>
                                <span>{{ $post->reading_time }}</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3 hover:text-primary-600 transition-colors">
                                <a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a>
                            </h3>
                            <p class="text-gray-600 mb-4">{{ $post->excerpt }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">By {{ $post->user->name }} • {{ $post->views_count }} views</span>
                                <a href="{{ route('blog.show', $post) }}" class="text-primary-600 hover:text-primary-700 font-medium text-sm">
                                    Read More →
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $posts->appends(request()->except('page'))->links() }}
            </div>
            @else
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🔍</div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">No Results Found</h2>
                <p class="text-gray-600 mb-6">No articles match your search for "{{ $search }}".</p>

                <!-- Search Tips -->
                <div class="bg-gray-50 rounded-lg p-6 text-left max-w-md mx-auto">
                    <h4 class="font-semibold text-gray-900 mb-3">Search Tips:</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li>• Try shorter keywords</li>
                        <li>• Check your spelling</li>
                        <li>• Use synonyms or related terms</li>
                        <li>• Browse categories or tags</li>
                    </ul>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            @if($suggestedCategories->count() > 0)
            <!-- Suggested Categories -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Suggested Categories</h3>
                <div class="space-y-2">
                    @foreach($suggestedCategories as $category)
                    <a href="{{ route('blog.category', $category) }}"
                       class="block text-gray-600 hover:text-primary-600 transition-colors">
                        {{ $category->name }}
                        <span class="text-xs text-gray-500">({{ $category->published_posts_count }} posts)</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if($suggestedTags->count() > 0)
            <!-- Suggested Tags -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Related Tags</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($suggestedTags as $tag)
                    <a href="{{ route('blog.tag', $tag) }}"
                       class="inline-flex items-center bg-gray-100 text-gray-700 px-3 py-1 rounded-full hover:bg-primary-100 hover:text-primary-700 transition-colors text-sm">
                        #{{ $tag->name }}
                        <span class="ml-1 text-xs">({{ $tag->published_posts_count }})</span>
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

### Update Author View

Edit `resources/views/blog/author.blade.php`:

```html
@extends('layouts.app')

@section('title', 'Author: ' . $user->name . ' - Blog Laravel')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Author Header -->
    <div class="bg-white rounded-xl shadow-sm p-8 mb-8">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-full mb-4 text-white text-2xl font-bold">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $user->name }}</h1>
            @if($user->bio)
            <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-6">{{ $user->bio }}</p>
            @endif

            <!-- Author Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-2xl mx-auto">
                <div class="text-center">
                    <div class="text-2xl font-bold text-indigo-600">{{ $authorStats['total_posts'] }}</div>
                    <div class="text-sm text-gray-500">Articles</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-indigo-600">{{ number_format($authorStats['total_views']) }}</div>
                    <div class="text-sm text-gray-500">Total Views</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-indigo-600">{{ $authorStats['categories_count'] }}</div>
                    <div class="text-sm text-gray-500">Categories</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-indigo-600">
                        {{ $authorStats['first_post_date'] ? \Carbon\Carbon::parse($authorStats['first_post_date'])->format('M Y') : '-' }}
                    </div>
                    <div class="text-sm text-gray-500">Writing Since</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Posts Grid -->
        <div class="lg:col-span-2">
            @if($posts->count() > 0)
            <div class="grid gap-6">
                @foreach($posts as $post)
                <article class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <div class="md:flex">
                        <div class="md:w-48 bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center">
                            <div class="text-white text-center p-6">
                                <div class="text-3xl mb-2">✍️</div>
                                <div class="text-sm font-medium">{{ $post->category->name }}</div>
                            </div>
                        </div>
                        <div class="flex-1 p-6">
                            <div class="flex items-center space-x-4 text-sm text-gray-500 mb-3">
                                <span class="bg-{{ $post->category->color ?? 'gray' }}-100 text-{{ $post->category->color ?? 'gray' }}-700 px-3 py-1 rounded-full font-medium">
                                    {{ $post->category->name }}
                                </span>
                                <span>{{ $post->published_at->format('M d, Y') }}</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3 hover:text-primary-600 transition-colors">
                                <a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a>
                            </h3>
                            <p class="text-gray-600 mb-4">{{ $post->excerpt }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">{{ $post->reading_time }} • {{ $post->views_count }} views</span>
                                <a href="{{ route('blog.show', $post) }}" class="text-primary-600 hover:text-primary-700 font-medium text-sm">
                                    Read More →
                                </a>
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
            @else
            <div class="text-center py-12">
                <div class="text-6xl mb-4">✍️</div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">No Articles Yet</h2>
                <p class="text-gray-600">{{ $user->name }} hasn't published any articles with your current filters.</p>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Author Info -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">About {{ $user->name }}</h3>
                <div class="space-y-3">
                    @if($user->bio)
                    <p class="text-gray-600 text-sm">{{ $user->bio }}</p>
                    @endif
                    <div class="pt-3 border-t">
                        <div class="text-xs text-gray-500 uppercase tracking-wide mb-2">Statistics</div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Total Articles:</span>
                                <span class="font-medium">{{ $authorStats['total_posts'] }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Total Views:</span>
                                <span class="font-medium">{{ number_format($authorStats['total_views']) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

### Update Archive View

Edit `resources/views/blog/archive.blade.php`:

```html
@extends('layouts.app')

@section('title', 'Archive: ' . $archiveInfo['year'] . ($archiveInfo['month'] ? ' ' . $archiveInfo['month_name'] : '') . ' - Blog Laravel')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Archive Header -->
    <div class="bg-white rounded-xl shadow-sm p-8 mb-8">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-4">
                Archive {{ $archiveInfo['year'] }}
                @if($archiveInfo['month'])
                    - {{ $archiveInfo['month_name'] }}
                @endif
            </h1>
            <p class="text-lg text-gray-600">
                Articles published in
                @if($archiveInfo['month'])
                    {{ $archiveInfo['month_name'] }} {{ $archiveInfo['year'] }}
                @else
                    {{ $archiveInfo['year'] }}
                @endif
            </p>
            <div class="text-sm text-gray-500 mt-2">
                {{ $archiveInfo['total_posts'] }} {{ Str::plural('article', $archiveInfo['total_posts']) }} found
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Archive Timeline -->
        <div class="lg:col-span-2">
            @if($posts->count() > 0)
            <div class="space-y-6">
                @foreach($posts as $post)
                <article class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <div class="md:flex">
                        <div class="md:w-48 bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center">
                            <div class="text-white text-center p-6">
                                <div class="text-3xl mb-2">📅</div>
                                <div class="text-sm font-medium">{{ $post->published_at->format('M d') }}</div>
                            </div>
                        </div>
                        <div class="flex-1 p-6">
                            <div class="flex items-center space-x-4 text-sm text-gray-500 mb-3">
                                <span class="bg-{{ $post->category->color ?? 'gray' }}-100 text-{{ $post->category->color ?? 'gray' }}-700 px-3 py-1 rounded-full font-medium">
                                    {{ $post->category->name }}
                                </span>
                                <span>{{ $post->published_at->format('F d, Y') }}</span>
                                <span>•</span>
                                <span>{{ $post->reading_time }}</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3 hover:text-primary-600 transition-colors">
                                <a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a>
                            </h3>
                            <p class="text-gray-600 mb-4">{{ $post->excerpt }}</p>
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-500">
                                    By {{ $post->user->name }} • {{ $post->views_count }} views
                                    @if($post->tags->count() > 0)
                                    <span class="ml-2">
                                        @foreach($post->tags->take(2) as $tag)
                                            <span class="text-primary-600">#{{ $tag->name }}</span>{{ !$loop->last ? ' ' : '' }}
                                        @endforeach
                                    </span>
                                    @endif
                                </div>
                                <a href="{{ route('blog.show', $post) }}" class="text-primary-600 hover:text-primary-700 font-medium text-sm">
                                    Read More →
                                </a>
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
            @else
            <div class="text-center py-12">
                <div class="text-6xl mb-4">📅</div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">No Articles Found</h2>
                <p class="text-gray-600">
                    No articles were published in
                    @if($archiveInfo['month'])
                        {{ $archiveInfo['month_name'] }} {{ $archiveInfo['year'] }}
                    @else
                        {{ $archiveInfo['year'] }}
                    @endif
                </p>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            @if($relatedArchives->count() > 0)
            <!-- Related Archives -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Other Archives</h3>
                <div class="grid gap-3">
                    @foreach($relatedArchives as $archive)
                    <a href="{{ route('blog.archive.month', ['year' => $archive->year, 'month' => $archive->month]) }}"
                       class="block bg-gray-50 rounded-lg p-3 hover:bg-gray-100 transition-colors">
                        <div class="font-medium text-gray-900">{{ $archive->month_name }} {{ $archive->year }}</div>
                        <div class="text-sm text-gray-500">{{ $archive->posts_count }} {{ Str::plural('article', $archive->posts_count) }}</div>
                    </a>
                    @endforeach
                </div>

                @if($archiveInfo['month'])
                <div class="mt-4 pt-4 border-t">
                    <a href="{{ route('blog.archive.year', $archiveInfo['year']) }}"
                       class="text-primary-600 hover:text-primary-700 font-medium text-sm">
                        View all {{ $archiveInfo['year'] }} articles →
                    </a>
                </div>
                @endif
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

## 🧪 Pengujian & Validasi Route Model Binding

Setelah mengimplementasi Route Model Binding yang advanced, mari kita lakukan pengujian komprehensif untuk memastikan semua fitur berfungsi dengan benar dan optimal.

### 🔗 Test 1: Implicit Route Model Binding

**🎯 Tujuan:** Memastikan implicit binding berfungsi dengan benar untuk semua models.

**Test Case 1.1 - Basic Implicit Binding:**
```bash
php artisan tinker
```

```php
// Test resolving Post by slug
$post = App\Models\Post::where('slug', 'memulai-perjalanan-dengan-laravel-12')->first();
if ($post) {
    echo "Post found: " . $post->title;
    echo "URL: " . route('blog.show', $post); // Harus generate URL dengan slug
}

// Test resolving Category by slug
$category = App\Models\Category::where('slug', 'laravel-framework')->first();
if ($category) {
    echo "Category found: " . $category->name;
    echo "URL: " . route('blog.category', $category);
}

// Test resolving Tag by slug
$tag = App\Models\Tag::where('slug', 'laravel')->first();
if ($tag) {
    echo "Tag found: " . $tag->name;
    echo "URL: " . route('blog.tag', $tag);
}
```

**Test Case 1.2 - Browser Testing Implicit Binding:**

Jalankan server dan test di browser:

1. **Post Binding:** `http://127.0.0.1:8000/blog/post/memulai-perjalanan-dengan-laravel-12`
   - ✅ Harus load post berdasarkan slug
   - ✅ Post data tampil dengan benar
   - ✅ Tidak ada query tambahan untuk resolve model

2. **Category Binding:** `http://127.0.0.1:8000/blog/category/laravel-framework`
   - ✅ Harus load category berdasarkan slug
   - ✅ Posts dalam category tampil
   - ✅ Pagination berfungsi dengan model binding

3. **Tag Binding:** `http://127.0.0.1:8000/blog/tag/laravel`
   - ✅ Harus load tag berdasarkan slug
   - ✅ Posts dengan tag tampil
   - ✅ Related categories ditampilkan

**✅ Expected Results:**
- Semua models ter-resolve dengan benar berdasarkan slug
- Tidak ada error 404 untuk slug yang valid
- URL generation menggunakan slug bukan ID

### 🎛️ Test 2: Explicit & Custom Route Model Binding

**🎯 Tujuan:** Memastikan custom bindings di RouteServiceProvider berfungsi dengan benar.

**Test Case 2.1 - Custom Binding Logic:**
```bash
php artisan tinker
```

```php
// Test custom binding dengan constraint published
// Simulate request untuk public route
app('request')->server->set('REQUEST_URI', '/blog/post/test-slug');

try {
    $post = app('App\Models\Post')->resolveRouteBinding('test-slug', 'slug');
    if ($post) {
        echo "Published post found: " . $post->title;
        echo "Status: " . $post->status;
        echo "Published at: " . $post->published_at;
    }
} catch (Exception $e) {
    echo "Expected: " . $e->getMessage();
}

// Test admin route (should bypass published constraint)
app('request')->server->set('REQUEST_URI', '/admin/posts/test-slug');
// Repeat test - should find unpublished posts too
```

**Test Case 2.2 - RouteServiceProvider Bindings:**

Test apakah custom bindings terdaftar dengan benar:

```bash
# Check registered route bindings
php artisan route:list --columns=uri,action | grep -E "\\{.*\\}"
```

Expected Output harus menunjukkan parameter binding yang benar:
```
blog/post/{post:slug}
blog/category/{category:slug}
blog/tag/{tag:slug}
```

**✅ Expected Results:**
- Custom bindings di RouteServiceProvider aktif
- Published constraint diterapkan untuk public routes
- Admin routes bypass constraint published
- Error handling custom memberikan pesan yang informatif

### 🔗 Test 3: Scoped Bindings & Nested Routes

**🎯 Tujuan:** Memastikan scoped bindings berfungsi untuk nested parameters.

**Test Case 3.1 - Scoped Category-Post Binding:**

Browser testing untuk nested routes:

1. **Valid Category-Post:** `http://127.0.0.1:8000/blog/category/laravel-framework/post/memulai-perjalanan-dengan-laravel-12`
   - ✅ Harus load jika post belongs to category
   - ✅ Post data dan category data konsisten
   - ✅ Breadcrumb navigation benar

2. **Invalid Category-Post:** `http://127.0.0.1:8000/blog/category/php-programming/post/memulai-perjalanan-dengan-laravel-12`
   - ✅ Harus return 404 jika post tidak belongs to category
   - ✅ Error message informatif
   - ✅ Redirect ke category atau suggestion

**Test Case 3.2 - Scoped Binding Performance:**
```bash
php artisan tinker
```

```php
// Test query efficiency untuk scoped binding
DB::enableQueryLog();

// Simulate scoped binding untuk category-post
$category = App\Models\Category::where('slug', 'laravel-framework')->first();
$post = $category->posts()->where('slug', 'memulai-perjalanan-dengan-laravel-12')->first();

if ($post) {
    echo "Post belongs to category: " . $post->category->name;
}

$queries = DB::getQueryLog();
echo "Total queries: " . count($queries);
// Harus hanya 2-3 queries dengan proper eager loading
```

**✅ Expected Results:**
- Scoped binding hanya mengembalikan posts yang belongs to category
- Query count optimal (2-3 queries total)
- Error 404 yang appropriate untuk invalid combinations

### 📊 Test 4: Performance & Query Optimization

**🎯 Tujuan:** Memastikan route model binding tidak menimbulkan performance issue.

**Test Case 4.1 - Eager Loading dalam Binding:**
```bash
php artisan tinker
```

```php
// Test apakah eager loading aktif dalam binding
DB::enableQueryLog();

// Access route yang menggunakan model binding
$post = App\Models\Post::where('slug', 'memulai-perjalanan-dengan-laravel-12')->with(['category', 'author', 'tags'])->first();

if ($post) {
    // Access related models
    echo "Author: " . $post->author->name;
    echo "Category: " . $post->category->name;
    echo "Tags: " . $post->tags->pluck('name')->join(', ');
}

$queries = DB::getQueryLog();
echo "Queries with eager loading: " . count($queries);

DB::flushQueryLog();

// Test tanpa eager loading
$post = App\Models\Post::where('slug', 'memulai-perjalanan-dengan-laravel-12')->first();
if ($post) {
    echo "Author: " . $post->author->name;    // +1 query
    echo "Category: " . $post->category->name; // +1 query
    echo "Tags: " . $post->tags->pluck('name')->join(', '); // +1 query
}

$badQueries = DB::getQueryLog();
echo "Queries without eager loading: " . count($badQueries);
```

**Test Case 4.2 - Caching Route Model Binding:**
```php
// Test apakah model binding bisa di-cache
Cache::remember('post-slug-' . 'memulai-perjalanan-dengan-laravel-12', 3600, function() {
    return App\Models\Post::where('slug', 'memulai-perjalanan-dengan-laravel-12')
                          ->with(['category', 'author', 'tags'])
                          ->first();
});

echo "Model cached successfully";
```

**✅ Expected Results:**
- Eager loading mengurangi query count dari 4+ menjadi 1-2
- Caching model binding berfungsi tanpa error
- Response time < 100ms untuk cached requests

### 🛡️ Test 5: Error Handling & Security

**🎯 Tujuan:** Memastikan error handling dan security constraint berfungsi dengan benar.

**Test Case 5.1 - Missing Model Handling:**

Browser testing untuk URL yang tidak valid:

1. **Invalid Slug:** `http://127.0.0.1:8000/blog/post/nonexistent-slug`
   - ✅ Harus return 404
   - ✅ Custom error message ditampilkan
   - ✅ Suggestion untuk posts lain atau search

2. **Unpublished Post:** Test jika ada post dengan status draft
   - ✅ Public route harus return 404 untuk draft posts
   - ✅ Admin route (jika ada) harus bisa akses draft posts

3. **Future Published Date:** Test post dengan published_at di masa depan
   - ✅ Harus return 404 untuk public access
   - ✅ Constraint published_at <= now() aktif

**Test Case 5.2 - Security Constraints:**
```bash
php artisan tinker
```

```php
// Test security constraint untuk published posts
$draftPost = App\Models\Post::create([
    'title' => 'Draft Post Test',
    'slug' => 'draft-post-test',
    'content' => 'This is a draft',
    'status' => 'draft',
    'user_id' => 1,
    'category_id' => 1,
]);

// Test akses draft post melalui public route
try {
    $resolved = app('App\Models\Post')->resolveRouteBinding('draft-post-test', 'slug');
    echo "ERROR: Draft post accessible!";
} catch (Exception $e) {
    echo "CORRECT: " . $e->getMessage();
}

// Cleanup
$draftPost->delete();
```

**✅ Expected Results:**
- Draft/unpublished posts tidak accessible melalui public routes
- Future-dated posts tidak accessible sampai published_at
- Custom error messages informatif dan helpful

### 🎯 Test 6: Advanced Implementation Challenge

**🎯 Tujuan:** Implementasi fitur advanced untuk menguji pemahaman komprehensif.

**Challenge Task:** Implementasi "Smart 404" dengan suggestions.

**Task 6.1 - Smart 404 Handler:**
```php
// app/Models/Post.php
public function resolveRouteBinding($value, $field = null)
{
    $post = $this->where($field ?? $this->getRouteKeyName(), $value);

    if (!request()->is('admin/*')) {
        $post->where('status', 'published')
             ->where('published_at', '<=', now());
    }

    $result = $post->with(['category', 'author', 'tags'])->first();

    if (!$result) {
        // Find similar posts untuk suggestions
        $suggestions = $this->getSimilarPosts($value);

        // Store suggestions di session untuk error page
        session(['post_suggestions' => $suggestions]);

        abort(404, 'Post tidak ditemukan');
    }

    return $result;
}

private function getSimilarPosts($slug, $limit = 3)
{
    // Cari posts dengan slug yang mirip
    $similar = Post::published()
        ->where('slug', 'like', '%' . substr($slug, 0, 5) . '%')
        ->orWhere('title', 'like', '%' . str_replace('-', ' ', $slug) . '%')
        ->with(['category'])
        ->limit($limit)
        ->get();

    return $similar;
}
```

**Task 6.2 - Dynamic Route Caching:**
```php
// Implementasi caching untuk route model binding
public static function boot()
{
    parent::boot();

    // Cache model setelah resolve
    static::retrieved(function ($model) {
        if (request()->route() && request()->route()->parameter($model->getTable())) {
            $cacheKey = 'model-binding:' . get_class($model) . ':' . $model->getKey();
            Cache::put($cacheKey, $model, 300); // 5 minutes
        }
    });
}
```

**✅ Success Criteria:**
- Smart 404 menampilkan suggestions untuk posts yang mirip
- Route model binding ter-cache dan mengurangi database hits
- Performance tetap optimal dengan fitur tambahan
- Error handling tetap user-friendly

## 📋 Checklist Kelulusan Route Model Binding

Tandai ✅ untuk setiap test yang berhasil:

### 🔗 Implicit Binding
- [ ] Post, Category, Tag binding by slug berfungsi
- [ ] URL generation menggunakan model route key
- [ ] Implicit binding tidak memerlukan manual resolution
- [ ] Route parameter constraints diterapkan dengan benar

### 🎛️ Explicit & Custom Binding
- [ ] RouteServiceProvider bindings terdaftar
- [ ] Custom binding logic (published constraint) berfungsi
- [ ] Admin routes bypass public constraints
- [ ] Custom error messages ditampilkan dengan benar

### 🔗 Scoped Bindings
- [ ] Nested route parameters (category/post) berfungsi
- [ ] Scoped binding memvalidasi relationships
- [ ] Invalid combinations return 404 yang appropriate
- [ ] Query efficiency optimal untuk scoped binding

### 📊 Performance & Optimization
- [ ] Eager loading aktif dalam route binding
- [ ] Query count optimal (1-3 queries)
- [ ] Model binding dapat di-cache
- [ ] Response time < 100ms untuk requests normal

### 🛡️ Error Handling & Security
- [ ] Missing models return 404 dengan pesan yang jelas
- [ ] Unpublished posts tidak accessible via public routes
- [ ] Future-dated posts di-handle dengan benar
- [ ] Security constraints tidak dapat di-bypass

### 🎯 Advanced Features
- [ ] Smart 404 dengan suggestions (bonus)
- [ ] Dynamic route caching (bonus)
- [ ] Custom error pages untuk different scenarios
- [ ] Performance monitoring untuk route binding

## 🚨 Troubleshooting Route Model Binding

### ❌ Binding Issues
- **Model not found** → Cek route key name dan database field
- **Wrong model returned** → Verify custom binding logic di RouteServiceProvider
- **Binding tidak aktif** → Pastikan parameter name match dengan model name

### ❌ Performance Issues
- **Slow route resolution** → Tambah database index untuk route key field
- **Too many queries** → Gunakan eager loading dalam custom binding
- **Memory issues** → Implement route caching untuk frequently accessed models

### ❌ Security Issues
- **Unpublished content accessible** → Verify published constraint dalam binding
- **Draft posts visible** → Check status filtering di resolveRouteBinding
- **Future posts accessible** → Implement published_at constraint

## 🎯 Kesimpulan

Selamat! Anda telah menguasai:
- ✅ Implicit dan Explicit Route Model Binding
- ✅ Custom route keys dan constraints
- ✅ Scoped bindings untuk nested routes
- ✅ Advanced controllers dengan model binding
- ✅ Custom error handling untuk missing models
- ✅ Performance optimization dalam route binding
- ✅ **[BARU] Pengujian komprehensif route model binding**

Route Model Binding membuat code Anda lebih clean dan Laravel akan handle semua dependency injection secara otomatis. Dengan pengujian yang telah dilakukan, Anda memastikan bahwa implementasi route model binding telah optimal dan siap untuk production. Di pelajaran selanjutnya, kita akan mengimplementasi authentication dengan Laravel Breeze.

---

**Selanjutnya:** [Pelajaran 10: Starter Kits and Laravel Breeze](10-laravel-breeze.md)

*Route Model Binding mastered! 🎯*
