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

### Step 4: Restructure Web Routes

Edit `routes/web.php`:

```php
<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\UserController;
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

// Blog routes (public)
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
| Admin Routes (Authors & Admins)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'author'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Categories (full CRUD for admins, limited for authors)
    Route::resource('categories', CategoryController::class);
    Route::patch('/categories/{category}/toggle', [CategoryController::class, 'toggle'])
         ->name('categories.toggle');
    
    // Posts management
    Route::resource('posts', PostController::class);
    Route::patch('/posts/{post}/toggle-featured', [PostController::class, 'toggleFeatured'])
         ->name('posts.toggle-featured');
    Route::post('/posts/{post}/duplicate', [PostController::class, 'duplicate'])
         ->name('posts.duplicate');
    
    // Tags management
    Route::get('/tags', [TagController::class, 'adminIndex'])->name('tags.index');
    Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
    Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');
    
    // Media uploads
    Route::post('/upload/image', [MediaController::class, 'uploadImage'])->name('upload.image');
    Route::delete('/media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
});

/*
|--------------------------------------------------------------------------
| Super Admin Routes (Admins Only)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // User management
    Route::resource('users', UserController::class)->except(['show']);
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
         ->name('users.toggle-status');
    Route::patch('/users/{user}/change-role', [UserController::class, 'changeRole'])
         ->name('users.change-role');
    
    // System settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');
    
    // Analytics & reports
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/reports/posts', [ReportsController::class, 'posts'])->name('reports.posts');
    Route::get('/reports/users', [ReportsController::class, 'users'])->name('reports.users');
});

/*
|--------------------------------------------------------------------------
| API Routes (Optional)
|--------------------------------------------------------------------------
*/

Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/posts', [BlogController::class, 'apiIndex']);
    Route::get('/categories', [PublicCategoryController::class, 'apiIndex']);
    Route::get('/tags', [TagController::class, 'apiIndex']);
});
```

## 👤 Enhanced User Management

### Step 5: Create UserController

```bash
php artisan make:controller Admin/UserController --resource
```

Edit `app/Http/Controllers/Admin/UserController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $role = $request->get('role');
        
        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($role, function ($query, $role) {
                $query->where('role', $role);
            })
            ->withCount(['posts', 'publishedPosts'])
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $roles = ['admin', 'author', 'user'];

        return view('admin.users.index', compact('users', 'search', 'role', 'roles'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = ['admin', 'author', 'user'];
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,author,user'],
            'bio' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['email_verified_at'] = now();

        $user = User::create($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User '{$user->name}' created successfully!");
    }

    /**
     * Show the form for editing user
     */
    public function edit(User $user)
    {
        $roles = ['admin', 'author', 'user'];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'role' => ['required', 'in:admin,author,user'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User '{$user->name}' updated successfully!");
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevent deleting current user
        if ($user->id === auth()->id()) {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete your own account!');
        }

        // Check if user has posts
        if ($user->posts()->exists()) {
            return redirect()
                ->back()
                ->with('error', "Cannot delete user '{$user->name}' because they have posts. Please reassign or delete the posts first.");
        }

        $userName = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User '{$userName}' deleted successfully!");
    }

    /**
     * Change user role
     */
    public function changeRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', 'in:admin,author,user']
        ]);

        // Prevent changing own role
        if ($user->id === auth()->id()) {
            return redirect()
                ->back()
                ->with('error', 'Cannot change your own role!');
        }

        $oldRole = $user->role;
        $user->update($validated);

        return redirect()
            ->back()
            ->with('success', "User role changed from '{$oldRole}' to '{$user->role}'!");
    }
}
```

## 🎨 Create Admin Navigation Component

### Step 6: Enhanced Admin Layout

Buat `resources/views/layouts/admin.blade.php`:

```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="h-full bg-gray-50" x-data="{ sidebarOpen: false }">
    <div class="flex h-full">
        <!-- Sidebar -->
        <div class="hidden lg:flex lg:flex-shrink-0">
            <div class="flex flex-col w-64">
                <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto bg-white border-r border-gray-200">
                    <!-- Logo -->
                    <div class="flex items-center flex-shrink-0 px-4">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                            <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                            <span class="text-xl font-bold text-gray-900">Admin</span>
                        </a>
                    </div>
                    
                    <!-- Navigation -->
                    <nav class="mt-8 flex-1 px-2 space-y-1">
                        <!-- Dashboard -->
                        <a href="{{ route('admin.dashboard') }}" 
                           class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h2a2 2 0 012 2v2H8V5z"/>
                            </svg>
                            Dashboard
                        </a>

                        <!-- Posts -->
                        <div class="space-y-1">
                            <div class="admin-nav-section">Content</div>
                            <a href="{{ route('admin.posts.index') }}" 
                               class="admin-nav-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Posts
                            </a>

                            <a href="{{ route('admin.categories.index') }}" 
                               class="admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                Categories
                            </a>

                            <a href="{{ route('admin.tags.index') }}" 
                               class="admin-nav-link {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                </svg>
                                Tags
                            </a>
                        </div>

                        @can('admin-access')
                        <!-- Admin Only -->
                        <div class="space-y-1">
                            <div class="admin-nav-section">Administration</div>
                            <a href="{{ route('admin.users.index') }}" 
                               class="admin-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                                Users
                            </a>

                            <a href="{{ route('admin.settings.index') }}" 
                               class="admin-nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Settings
                            </a>
                        </div>
                        @endcan

                        <!-- Quick Links -->
                        <div class="space-y-1 pt-4 border-t">
                            <div class="admin-nav-section">Quick Links</div>
                            <a href="{{ route('blog.index') }}" 
                               class="admin-nav-link" target="_blank">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View Blog
                            </a>
                        </div>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Top navigation -->
            <div class="relative z-10 flex-shrink-0 flex h-16 bg-white shadow">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = true" 
                        class="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500 lg:hidden">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                </button>

                <!-- Top bar -->
                <div class="flex-1 px-4 flex justify-between">
                    <div class="flex-1 flex">
                        <div class="w-full flex md:ml-0">
                            <!-- Breadcrumb or page title could go here -->
                            <div class="flex items-center">
                                <h1 class="text-lg font-medium text-gray-900">
                                    @yield('page-title', 'Admin Dashboard')
                                </h1>
                            </div>
                        </div>
                    </div>

                    <!-- User menu -->
                    <div class="ml-4 flex items-center md:ml-6">
                        <div class="ml-3 relative" x-data="{ open: false }">
                            <div>
                                <button @click="open = !open" 
                                        class="max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    <img class="h-8 w-8 rounded-full" 
                                         src="{{ auth()->user()->avatar_url }}" 
                                         alt="{{ auth()->user()->name }}">
                                    <span class="ml-3 text-gray-700 text-sm font-medium hidden md:block">{{ auth()->user()->name }}</span>
                                </button>
                            </div>

                            <div x-show="open" 
                                 x-transition
                                 @click.away="open = false"
                                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                                <a href="{{ route('profile.edit') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Your Profile
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Sign out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page content -->
            <main class="flex-1 relative overflow-y-auto focus:outline-none">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Mobile sidebar -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 flex z-40 lg:hidden">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="sidebarOpen = false"></div>
        <!-- Mobile sidebar content would go here -->
    </div>

    @include('components.flash-messages')

    <style>
        .admin-nav-link {
            @apply group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900;
        }
        .admin-nav-link.active {
            @apply bg-primary-100 text-primary-700;
        }
        .admin-nav-section {
            @apply px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider;
        }
    </style>

    @stack('scripts')
</body>
</html>
```

## 🎯 Kesimpulan Pelajaran 12

Selamat! Anda telah berhasil:
- ✅ Membuat custom middleware untuk admin dan author
- ✅ Mengorganisir routes dengan route groups yang terstruktur
- ✅ Implementasi role-based access control
- ✅ Membuat admin layout yang sophisticated
- ✅ Menambahkan user management untuk admin

Sistem admin sekarang sudah robust dengan proper authorization dan navigation yang user-friendly.

---

**Selanjutnya:** [Pelajaran 13: Posts CRUD with Performance and Debugbar](13-posts-crud-performance.md)

*Admin system enhanced! 🔐*