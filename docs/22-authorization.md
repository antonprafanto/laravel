# Bab 22: Authorization 🛡️

[⬅️ Bab 21: Authentication](21-authentication.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 23: Middleware ➡️](23-middleware.md)

---

## 🎯 Learning Objectives

- ✅ Memahami perbedaan Authentication vs Authorization
- ✅ Menguasai Gates untuk simple authorization
- ✅ Menguasai Policies untuk model-based authorization
- ✅ Implement "user hanya bisa edit/delete post sendiri"
- ✅ Bisa implement role-based access (Admin vs User)

---

## 🎯 Analogi: Authorization = Kartu Akses Lift

**Authentication** (Bab 21) = **KTP ke Satpam** (Siapa kamu?)
**Authorization** (Bab ini) = **Kartu Akses Lift** (Kamu boleh ke lantai mana?)

```
✅ Sudah login (authenticated)
🛡️ Satpam: "Kamu boleh ke lantai berapa?"

👤 User Biasa: Kartu BIRU
   ├── ✅ Boleh ke Lantai 1-3
   └── ❌ TIDAK boleh ke Lantai 5 (Ruang Direktur)

👤 Admin: Kartu EMAS
   ├── ✅ Boleh ke SEMUA lantai
   └── ✅ Termasuk Lantai 5!
```

**Di Laravel:**
- **Gates**: Simple authorization ("Boleh/tidak?")
- **Policies**: Authorization untuk model tertentu (Post, Product, dll)

---

## 📚 Bagian 1: Gates (Simple Authorization)

### Apa itu Gates?

**Gates** = Gerbang akses sederhana untuk cek authorization.

**Use case:** Global permissions yang tidak terkait model tertentu.

---

### Define Gates

**File:** `app/Providers/AuthServiceProvider.php`

```php
<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        /**
         * 🛡️ Gate: access-admin
         * Hanya user dengan is_admin = true yang boleh
         */
        Gate::define('access-admin', function (User $user) {
            return $user->is_admin === true;
        });

        /**
         * 🛡️ Gate: create-post
         * Hanya user yang verified email boleh create post
         */
        Gate::define('create-post', function (User $user) {
            return $user->hasVerifiedEmail();
        });

        /**
         * 🛡️ Gate: delete-any-post
         * Hanya admin boleh delete sembarang post
         */
        Gate::define('delete-any-post', function (User $user) {
            return $user->is_admin === true;
        });
    }
}
```

---

### Check Gates di Controller

```php
use Illuminate\Support\Facades\Gate;

public function create()
{
    // Cek gate
    if (Gate::denies('create-post')) {
        abort(403, 'You must verify your email first.');
    }

    return view('posts.create');
}

// Atau pakai authorize (throw exception jika denied)
public function create()
{
    Gate::authorize('create-post');

    return view('posts.create');
}
```

---

### Check Gates di Blade

```blade
@can('access-admin')
    <a href="/admin">Admin Panel</a>
@endcan

@cannot('create-post')
    <p>Please verify your email to create posts.</p>
@endcannot
```

---

### Gates dengan Parameter

```php
Gate::define('edit-post', function (User $user, Post $post) {
    return $user->id === $post->user_id;
});

// Check
if (Gate::allows('edit-post', $post)) {
    // User boleh edit post ini
}

// Di Blade
@can('edit-post', $post)
    <a href="{{ route('posts.edit', $post) }}">Edit</a>
@endcan
```

---

## 🔐 Bagian 2: Policies (Model-Based Authorization)

### Apa itu Policies?

**Policies** = Class untuk organize authorization logic untuk 1 model tertentu.

**Use case:** Authorization untuk Post, Product, Comment, dll.

**Keuntungan:**
- Organize logic by model
- Clean & maintainable
- Auto-discovery by Laravel

---

### Create Policy

```bash
php artisan make:policy PostPolicy --model=Post
```

**Output:**
```
INFO  Policy [app/Policies/PostPolicy.php] created successfully.
```

---

### Define Policy Methods

**File:** `app/Policies/PostPolicy.php`

```php
<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * 👀 Determine if user can view any posts.
     */
    public function viewAny(User $user): bool
    {
        return true; // Semua user boleh lihat list posts
    }

    /**
     * 👀 Determine if user can view the post.
     */
    public function view(?User $user, Post $post): bool
    {
        // Guest boleh lihat post yang published
        // User login boleh lihat semua posts
        if ($post->is_published) {
            return true;
        }

        return $user && $user->id === $post->user_id;
    }

    /**
     * 📝 Determine if user can create posts.
     */
    public function create(User $user): bool
    {
        return $user->hasVerifiedEmail(); // Harus verify email dulu
    }

    /**
     * ✏️ Determine if user can update the post.
     */
    public function update(User $user, Post $post): bool
    {
        // User hanya boleh edit post sendiri
        // ATAU user adalah admin
        return $user->id === $post->user_id || $user->is_admin;
    }

    /**
     * 🗑️ Determine if user can delete the post.
     */
    public function delete(User $user, Post $post): bool
    {
        // User hanya boleh delete post sendiri
        // ATAU user adalah admin
        return $user->id === $post->user_id || $user->is_admin;
    }

    /**
     * ♻️ Determine if user can restore the post.
     */
    public function restore(User $user, Post $post): bool
    {
        return $user->is_admin;
    }

    /**
     * ❌ Determine if user can permanently delete the post.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        return $user->is_admin;
    }
}
```

---

### Register Policy (Auto-Discovery)

**Laravel 12 otomatis detect Policy!**

**Convention:** `{ModelName}Policy` → `{Model}`
- `PostPolicy` → `Post` model
- `ProductPolicy` → `Product` model

**Manual register (optional):**

**File:** `app/Providers/AuthServiceProvider.php`

```php
protected $policies = [
    Post::class => PostPolicy::class,
];
```

---

### Check Policy di Controller

```php
public function edit(Post $post)
{
    // Authorize dulu sebelum tampilkan form edit
    $this->authorize('update', $post);

    return view('posts.edit', compact('post'));
}

public function update(Request $request, Post $post)
{
    $this->authorize('update', $post);

    $validated = $request->validate([...]);
    $post->update($validated);

    return redirect()->route('posts.show', $post);
}

public function destroy(Post $post)
{
    $this->authorize('delete', $post);

    $post->delete();

    return redirect()->route('posts.index');
}
```

**Jika tidak authorized** → Exception 403 Forbidden!

---

### Check Policy di Blade

```blade
@can('update', $post)
    <a href="{{ route('posts.edit', $post) }}">✏️ Edit</a>
@endcan

@can('delete', $post)
    <form method="POST" action="{{ route('posts.destroy', $post) }}">
        @csrf
        @method('DELETE')
        <button type="submit">🗑️ Delete</button>
    </form>
@endcan
```

---

### Alternative: Gate Facade

```php
use Illuminate\Support\Facades\Gate;

if (Gate::allows('update', $post)) {
    // User boleh update
}

if (Gate::denies('delete', $post)) {
    abort(403);
}
```

---

## 👥 Bagian 3: Role-Based Access (Admin vs User)

### Add is_admin Column

**Migration:**
```bash
php artisan make:migration add_is_admin_to_users_table
```

```php
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->boolean('is_admin')->default(false)->after('email');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('is_admin');
    });
}
```

**Run:**
```bash
php artisan migrate
```

---

### Update Model User

```php
class User extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin', // Add this
    ];

    protected $casts = [
        'is_admin' => 'boolean',
    ];

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }
}
```

---

### Create Admin User via Tinker

```bash
php artisan tinker
```

```php
>>> $user = User::find(1);
>>> $user->is_admin = true;
>>> $user->save();
=> true

>>> $user->isAdmin();
=> true
```

---

### Middleware untuk Admin

```bash
php artisan make:middleware IsAdmin
```

**File:** `app/Http/Middleware/IsAdmin.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin only.');
        }

        return $next($request);
    }
}
```

**Register:** `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\IsAdmin::class,
    ]);
})
```

**Use di routes:**
```php
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']); // Only admin
});
```

---

## 💡 Bagian 4: Praktik di Blog

### Scenario: User vs Admin

**User biasa:**
- ✅ Bisa create post sendiri
- ✅ Bisa edit post sendiri
- ✅ Bisa delete post sendiri
- ❌ TIDAK bisa edit/delete post user lain

**Admin:**
- ✅ Bisa create post
- ✅ Bisa edit SEMUA posts
- ✅ Bisa delete SEMUA posts
- ✅ Akses admin panel

---

### Update PostPolicy

**Sudah kita implement di atas!** Review:

```php
public function update(User $user, Post $post): bool
{
    return $user->id === $post->user_id || $user->is_admin;
}

public function delete(User $user, Post $post): bool
{
    return $user->id === $post->user_id || $user->is_admin;
}
```

---

### Update Routes

```php
// Public
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

// Authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
});

// Admin only
Route::middleware(['auth', 'admin'])->group(function () {
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
});
```

---

### Update Views

**File:** `resources/views/posts/show.blade.php`

```blade
<div>
    <h1>{{ $post->title }}</h1>
    <p>By {{ $post->user->name }}</p>

    <div>
        {{-- Author atau Admin boleh edit --}}
        @can('update', $post)
            <a href="{{ route('posts.edit', $post) }}" class="btn">✏️ Edit Post</a>
        @endcan

        {{-- Author atau Admin boleh delete --}}
        @can('delete', $post)
            <form method="POST" action="{{ route('posts.destroy', $post) }}" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Sure?')">
                    🗑️ Delete
                </button>
            </form>
        @endcan
    </div>

    <article>
        {{ $post->body }}
    </article>
</div>
```

---

## 📖 Summary

- ✅ **Gates**: Simple authorization global
- ✅ **Policies**: Model-based authorization (clean & organized)
- ✅ **Policy Methods**: viewAny, view, create, update, delete, restore, forceDelete
- ✅ **@can Directive**: Check authorization di Blade
- ✅ **$this->authorize()**: Check di Controller (throw 403 jika denied)
- ✅ **Role-Based**: Admin vs User dengan is_admin column
- ✅ **Middleware admin**: Protect routes untuk admin only

**Authorization sudah dikuasai!** 🛡️✅

---

## 🎯 Next Chapter Preview

- ✅ **Middleware** deep dive
- ✅ Custom middleware
- ✅ Middleware groups
- ✅ Global middleware

**Dari authorization ke filtering requests!** 🔄

---

[⬅️ Bab 21: Authentication](21-authentication.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 23: Middleware ➡️](23-middleware.md)