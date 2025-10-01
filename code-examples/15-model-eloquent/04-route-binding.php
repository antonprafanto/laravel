<?php

/**
 * ============================================
 * ROUTE MODEL BINDING
 * ============================================
 *
 * Route Model Binding otomatis inject model ke controller
 * berdasarkan route parameter.
 *
 * Tanpa binding: Manual find model
 * Dengan binding: Laravel auto-find untuk kita!
 */

// ============================================
// WITHOUT Route Model Binding (Manual)
// ============================================

// routes/web.php
Route::get('/posts/{id}', [PostController::class, 'show']);

// Controller
namespace App\Http\Controllers;

use App\Models\Post;

class PostController extends Controller
{
    public function show($id)
    {
        // Manual find
        $post = Post::findOrFail($id);
        // 🗣️ findOrFail = find atau throw 404

        return view('posts.show', compact('post'));
    }
}
// ❌ Repetitive! Every method needs findOrFail()

// ============================================
// WITH Route Model Binding (Automatic) ✅
// ============================================

// routes/web.php (SAME!)
Route::get('/posts/{post}', [PostController::class, 'show']);
// 🗣️ Parameter name = 'post' (singular model name)

// Controller
class PostController extends Controller
{
    public function show(Post $post)
    {
        // $post sudah di-load otomatis!
        // No need findOrFail()!

        return view('posts.show', compact('post'));
    }
}
// ✅ Clean! Laravel auto-inject model

// ============================================
// HOW IT WORKS
// ============================================

/*
URL: /posts/5

1. Laravel lihat route: /posts/{post}
2. Laravel lihat controller: show(Post $post)
3. Laravel auto execute: Post::findOrFail(5)
4. Laravel inject ke parameter $post
5. Kalau not found → auto throw 404

Magic! ✨
*/

// ============================================
// NAMING CONVENTION
// ============================================

// ✅ CORRECT:
Route::get('/posts/{post}', ...);
public function show(Post $post) {} // Match!

Route::get('/users/{user}', ...);
public function show(User $user) {} // Match!

// ❌ WRONG:
Route::get('/posts/{id}', ...); // 'id' not 'post'
public function show(Post $post) {} // Won't work!

// Solusi jika must use {id}:
Route::get('/posts/{id}', ...);
public function show(Post $id) {} // Works!
// Tapi prefer singular model name

// ============================================
// ALL CRUD METHODS
// ============================================

class PostController extends Controller
{
    // List all posts
    public function index()
    {
        $posts = Post::latest()->paginate(15);
        return view('posts.index', compact('posts'));
    }

    // Show create form
    public function create()
    {
        return view('posts.create');
    }

    // Store new post
    public function store(Request $request)
    {
        $validated = $request->validate([...]);
        $post = Post::create($validated);
        return redirect()->route('posts.show', $post);
    }

    // Show single post
    public function show(Post $post)
    {
        // ✅ $post auto-loaded!
        return view('posts.show', compact('post'));
    }

    // Show edit form
    public function edit(Post $post)
    {
        // ✅ $post auto-loaded!
        return view('posts.edit', compact('post'));
    }

    // Update post
    public function update(Request $request, Post $post)
    {
        // ✅ $post auto-loaded!
        $validated = $request->validate([...]);
        $post->update($validated);
        return redirect()->route('posts.show', $post);
    }

    // Delete post
    public function destroy(Post $post)
    {
        // ✅ $post auto-loaded!
        $post->delete();
        return redirect()->route('posts.index');
    }
}

// ============================================
// RESOURCE ROUTES
// ============================================

// routes/web.php
Route::resource('posts', PostController::class);
// 🗣️ Creates 7 routes automatically!

/*
Generated routes:
GET    /posts              → index()
GET    /posts/create       → create()
POST   /posts              → store()
GET    /posts/{post}       → show(Post $post)    ✅ Binding!
GET    /posts/{post}/edit  → edit(Post $post)    ✅ Binding!
PUT    /posts/{post}       → update(Post $post)  ✅ Binding!
DELETE /posts/{post}       → destroy(Post $post) ✅ Binding!
*/

// Check routes:
php artisan route:list --name=posts

// ============================================
// CUSTOM KEY (Advanced)
// ============================================

// Default: Bind by ID
// /posts/5 → Post::findOrFail(5)

// Custom: Bind by slug!
// /posts/laravel-tips → Post::where('slug', 'laravel-tips')->firstOrFail()

// Method 1: In Model
namespace App\Models;

class Post extends Model
{
    // Tell Laravel to use 'slug' instead of 'id'
    public function getRouteKeyName()
    {
        return 'slug';
    }
}

// Now:
// URL: /posts/laravel-tips
// Route: /posts/{post}
// Laravel: Post::where('slug', 'laravel-tips')->firstOrFail()

// Method 2: In Route (per-route basis)
Route::get('/posts/{post:slug}', [PostController::class, 'show']);
// 🗣️ {post:slug} means: use 'slug' column

// Controller stays same:
public function show(Post $post)
{
    // $post loaded by slug!
    return view('posts.show', compact('post'));
}

// ============================================
// NESTED ROUTE BINDING
// ============================================

// Route with nested resource
Route::get('/posts/{post}/comments/{comment}', ...);

// Controller
public function show(Post $post, Comment $comment)
{
    // Both auto-loaded!
    // But... no validation that comment belongs to post!

    return view('comments.show', compact('post', 'comment'));
}

// Better: Scoped binding (Laravel 7+)
Route::get('/posts/{post}/comments/{comment:id}', ...)
     ->scopeBindings();

// Or in route definition:
Route::scopeBindings()->group(function () {
    Route::get('/posts/{post}/comments/{comment}', ...);
});

// Now Laravel validates: comment.post_id = post.id
// If not match → 404

// ============================================
// SOFT DELETES & BINDING
// ============================================

// By default, binding excludes soft-deleted records

// Include soft deleted:
Route::get('/posts/{post}', ...)->withTrashed();

// Only soft deleted:
Route::get('/posts/{post}', ...)->onlyTrashed();

// Controller
public function show(Post $post)
{
    // $post might be soft-deleted (if withTrashed)
    if ($post->trashed()) {
        return view('posts.deleted', compact('post'));
    }

    return view('posts.show', compact('post'));
}

// ============================================
// BENEFITS
// ============================================

/*
✅ Less code (no findOrFail everywhere)
✅ Auto 404 handling
✅ Type hinting (IDE autocomplete works!)
✅ Consistent pattern
✅ Easier testing
✅ Can use custom keys (slug, uuid, etc)
*/

// ============================================
// BLADE USAGE
// ============================================

// Generating URLs with model
<a href="{{ route('posts.show', $post) }}">
    {{ $post->title }}
</a>
// Laravel auto uses $post->id (or custom key)

// Forms
<form action="{{ route('posts.update', $post) }}" method="POST">
    @csrf
    @method('PUT')
    ...
</form>

// Delete
<form action="{{ route('posts.destroy', $post) }}" method="POST">
    @csrf
    @method('DELETE')
    <button>Delete</button>
</form>

echo "\n✅ Route Model Binding mastered!\n";
