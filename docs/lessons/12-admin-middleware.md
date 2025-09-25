# Pelajaran 12: Admin User, Route Groups, Middleware

Dalam pelajaran ini, kita akan memperdalam penggunaan middleware, route groups, dan membuat sistem admin yang lebih robust untuk aplikasi blog kita.

## 🎯 Tujuan Pembelajaran

Setelah menyelesaikan pelajaran ini, Anda akan:
- ✅ Memahami konsep middleware di Laravel
- ✅ Membuat custom middleware untuk admin
- ✅ Mengorganisir routes dengan route groups
- ✅ Implementasi role-based access control
- ✅ Membuat admin navigation yang sophisticated

## 🛡️ Understanding Middleware

Middleware bertindak sebagai filter untuk HTTP requests. Laravel sudah menyediakan beberapa middleware bawaan:

- `auth` - Memastikan user sudah login
- `guest` - Memastikan user belum login
- `verified` - Memastikan email sudah diverifikasi
- `throttle` - Rate limiting

## 🔧 Create Custom Middleware

### Step 1: Create AdminMiddleware

```bash
php artisan make:middleware AdminMiddleware
```

Edit `app/Http/Middleware/AdminMiddleware.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access admin area.');
        }

        // Check if user has admin role
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        // Update last active timestamp
        Auth::user()->updateLastActive();

        return $next($request);
    }
}
```

### Step 2: Create AuthorMiddleware

```bash
php artisan make:middleware AuthorMiddleware
```

Edit `app/Http/Middleware/AuthorMiddleware.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthorMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this area.');
        }

        // Check if user can manage posts (admin or author)
        if (!Auth::user()->canManagePosts()) {
            abort(403, 'Access denied. Author privileges required.');
        }

        // Update last active timestamp
        Auth::user()->updateLastActive();

        return $next($request);
    }
}
```

### Step 3: Register Middleware

Edit `app/Http/Kernel.php` atau `bootstrap/app.php` (Laravel 11+):

Untuk Laravel 11+, edit `bootstrap/app.php`:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AuthorMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'author' => AuthorMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

## 🗂️ Organize Routes dengan Route Groups

**⚠️ PENTING - Simplified Approach untuk Learning**

Dalam lesson ini, kita akan fokus pada konsep middleware dan route groups dengan implementasi yang simple dan tidak terlalu banyak dependencies. Ini memudahkan pembelajaran tanpa overwhelming students dengan terlalu banyak controllers yang belum ada.

### Step 4: Restructure Web Routes (Simplified)

Edit `routes/web.php`:

```php
<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController as PublicCategoryController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('blog.index');
});

// Blog routes (public) - grouped with prefix and name
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/search', [BlogController::class, 'search'])->name('search');
    Route::get('/post/{post:slug}', [BlogController::class, 'show'])->name('show');
    Route::get('/category/{category:slug}', [PublicCategoryController::class, 'show'])->name('category');
    Route::get('/tag/{tag:slug}', [TagController::class, 'show'])->name('tag');
    Route::get('/author/{user:id}', [BlogController::class, 'author'])->name('author');
    Route::get('/archive/{year?}/{month?}', [BlogController::class, 'archive'])->name('archive')
         ->where(['year' => '[0-9]{4}', 'month' => '[0-9]{1,2}']);
});

// Static pages
Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
    
    // User dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Simplified - Authors & Admins)
|--------------------------------------------------------------------------
*/

// Routes untuk Authors (dapat mengelola posts dan categories)
Route::middleware(['auth', 'author'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Categories CRUD (menggunakan controller yang sudah ada dari Lesson 11)
    Route::resource('categories', CategoryController::class);
    Route::patch('/categories/{category}/toggle', [CategoryController::class, 'toggle'])
         ->name('categories.toggle');
});

/*
|--------------------------------------------------------------------------
| Super Admin Routes (Admins Only - Simplified)
|--------------------------------------------------------------------------
*/

// Routes khusus untuk Super Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Contoh route untuk admin-only features
    Route::get('/users', function() {
        return view('admin.users-simple', [
            'users' => \App\Models\User::paginate(20)
        ]);
    })->name('users.index');

    Route::get('/system-info', function() {
        return view('admin.system-info');
    })->name('system.info');
});
```

**💡 Penjelasan Simplifikasi:**

1. **Fokus pada Learning**: Kita hanya menggunakan controllers yang sudah ada (CategoryController dari Lesson 11)
2. **Step-by-Step Approach**: Tidak introduce terlalu banyak controllers baru sekaligus
3. **Practical Examples**: Route closures untuk demo admin-only features tanpa perlu buat controller baru
4. **Real Implementation**: Students bisa langsung test dan melihat perbedaan author vs admin access

## 🎯 Create Required Missing Methods

**⚠️ CRITICAL - Fix User Model Methods Terlebih Dahulu**

Sebelum middleware bisa berfungsi, kita perlu menambahkan methods yang missing di User model.

### Step 5: Update User Model Methods

Edit `app/Models/User.php`, tambahkan methods yang diperlukan oleh middleware:

```php
<?php

namespace App\Models;

// ... existing use statements ...

class User extends Authenticatable
{
    // ... existing code ...

    /**
     * Check if user can manage posts (admin or author)
     */
    public function canManagePosts(): bool
    {
        return $this->isAuthor();
    }

    /**
     * Update last active timestamp
     */
    public function updateLastActive()
    {
        $this->update(['last_active_at' => now()]);
        return $this;
    }

    // ... existing methods isAdmin(), isAuthor(), etc ...
}
```

**Jika methods `isAdmin()` dan `isAuthor()` belum ada, tambahkan juga:**

```php
/**
 * Check if user is admin
 */
public function isAdmin(): bool
{
    return $this->role === 'admin';
}

/**
 * Check if user is author (admin or author role)
 */
public function isAuthor(): bool
{
    return in_array($this->role, ['admin', 'author']);
}
```

### Step 6: Create Simple Admin Views

**Buat simple views untuk demo admin-only routes:**

Buat `resources/views/admin/users-simple.blade.php`:

```html
@extends('layouts.app')

@section('title', 'Users List - Admin Only')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Users List</h1>
        <p class="text-gray-600">This page is only accessible by Admins (not Authors)</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($users as $user)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $user->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $user->email }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                               {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' :
                                  ($user->role === 'author' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="px-6 py-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
```

Buat `resources/views/admin/system-info.blade.php`:

```html
@extends('layouts.app')

@section('title', 'System Info - Admin Only')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">System Information</h1>
        <p class="text-gray-600">Sensitive system information - Admin access only</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">Laravel Info</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-600">Laravel Version:</dt>
                    <dd class="font-medium">{{ app()->version() }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">PHP Version:</dt>
                    <dd class="font-medium">{{ PHP_VERSION }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Environment:</dt>
                    <dd class="font-medium">{{ app()->environment() }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">Statistics</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-600">Total Users:</dt>
                    <dd class="font-medium">{{ \App\Models\User::count() }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Total Posts:</dt>
                    <dd class="font-medium">{{ \App\Models\Post::count() }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Total Categories:</dt>
                    <dd class="font-medium">{{ \App\Models\Category::count() }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
```

## 🧪 Testing Middleware & Route Groups

### Step 7: Test Middleware Implementation

**Pre-Testing Verification:**

```bash
# 1. Verify middleware registration
php artisan route:list --middleware=author
# Expected: Should show admin.dashboard and admin.categories routes

# 2. Test User model methods
php artisan tinker
>>> $user = App\Models\User::first();
>>> $user->canManagePosts();
>>> $user->isAdmin();
>>> $user->isAuthor();
>>> exit

# 3. Verify middleware files exist
ls -la app/Http/Middleware/AdminMiddleware.php
ls -la app/Http/Middleware/AuthorMiddleware.php
```

**Testing URLs:**

1. **Login as different user roles** and test access:
   - `http://localhost:8000/admin/dashboard` (Author & Admin can access)
   - `http://localhost:8000/admin/categories` (Author & Admin can access)
   - `http://localhost:8000/admin/users` (Only Admin can access)
   - `http://localhost:8000/admin/system-info` (Only Admin can access)

2. **Expected Behaviors:**
   - ✅ **Author user**: Can access dashboard & categories, gets 403 on admin-only routes
   - ✅ **Admin user**: Can access all routes
   - ✅ **Regular user**: Gets 403 on all admin routes
   - ✅ **Guest**: Redirected to login

**Troubleshooting Common Errors:**

- **Error**: "Middleware [author] not found"
  - **Solution**: Ensure middleware registered correctly in `bootstrap/app.php`

- **Error**: "Call to undefined method canManagePosts()"
  - **Solution**: Add missing methods to User model (Step 5)

- **Error**: "403 Access Denied" for all users
  - **Solution**: Check user roles in database, ensure test users have correct roles

## 🎯 Kesimpulan

Selamat! Anda telah berhasil mempelajari:

### ✅ **Middleware Concepts:**
- ✅ Cara membuat custom middleware (AdminMiddleware & AuthorMiddleware)
- ✅ Registrasi middleware di `bootstrap/app.php` (Laravel 11+)
- ✅ Implementasi role-based access control

### ✅ **Route Groups & Organization:**
- ✅ Penggunaan `Route::prefix()` dan `Route::name()` untuk grouping
- ✅ Multiple middleware pada route groups
- ✅ Separation of concerns dengan route organization yang clean

### ✅ **Practical Implementation:**
- ✅ User model methods untuk authorization logic
- ✅ Simple admin views untuk testing purposes
- ✅ Real-world testing scenario dengan different user roles

### 💡 **Best Practices Learned:**
1. **Keep middleware simple** - Focus on single responsibility
2. **Use proper naming conventions** for middleware aliases
3. **Group routes logically** by functionality dan permission level
4. **Add comprehensive error handling** untuk better UX
5. **Test with different user roles** untuk ensure proper access control

**Lesson ini telah di-simplified untuk fokus pada core concepts tanpa overwhelming students dengan terlalu banyak dependencies.**

---

**Selanjutnya:** [Pelajaran 13: Posts CRUD and Performance](13-posts-crud-performance.md)

*Middleware & Route Groups mastered! 🛡️*
