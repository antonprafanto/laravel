# Pelajaran 10: Starter Kits and Using Laravel Breeze

Laravel Breeze adalah starter kit authentication yang lightweight dan simple untuk Laravel. Dalam pelajaran ini, kita akan mengintegrasikan authentication system ke aplikasi blog kita.

## 🎯 Tujuan Pembelajaran

Setelah menyelesaikan pelajaran ini, Anda akan:
- ✅ Memahami Laravel starter kits yang tersedia
- ✅ Menginstall dan konfigurasi Laravel Breeze
- ✅ Mengintegrasikan authentication dengan blog
- ✅ Membuat user registration dan login
- ✅ Menyiapkan foundation untuk admin area

## 🚀 Laravel Starter Kits Overview

### Pilihan Starter Kits

Laravel menyediakan beberapa starter kits:

1. **Laravel Breeze** - Simple, minimal authentication
2. **Laravel Jetstream** - Full-featured dengan teams, 2FA
3. **Laravel UI** - Legacy, Bootstrap-based

Untuk blog sederhana, **Breeze** adalah pilihan terbaik karena:
- ✅ Lightweight dan tidak bloated
- ✅ Menggunakan Blade templates
- ✅ Terintegrasi dengan Tailwind CSS
- ✅ Mudah dikustomisasi

## 📦 Instalasi Laravel Breeze

### Step 1: Install Breeze Package

```bash
composer require laravel/breeze --dev
```

### Step 2: Install Breeze dengan Blade Stack

```bash
php artisan breeze:install blade

# Install NPM dependencies
npm install

# Build assets
npm run dev
```

### Step 3: Run Migrations

```bash
php artisan migrate
```

Output yang diharapkan:
```
INFO  Breeze scaffolding installed successfully.

Please execute the "npm install && npm run dev" command to build your assets.
```

## 🔧 Kustomisasi Breeze untuk Blog

### Step 4: Update User Migration

Edit migration untuk menambahkan field yang sudah kita rencanakan:

```bash
php artisan make:migration add_blog_fields_to_users_table --table=users
```

Edit migration file:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('email');
            $table->text('bio')->nullable()->after('avatar');
            $table->enum('role', ['admin', 'author', 'user'])->default('user')->after('bio');
            $table->timestamp('last_active_at')->nullable()->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'bio', 'role', 'last_active_at']);
        });
    }
};
```

Jalankan migration:

```bash
php artisan migrate
```

### Step 5: Update User Model

Breeze sudah mengupdate User model, tapi kita perlu menambahkan field baru:

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
        return $this->posts()->where('status', 'published')
                             ->where('published_at', '<=', now());
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

    /**
     * Check if user can manage posts
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
    }

    /**
     * Get user avatar URL
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        
        // Generate avatar dengan initials
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=3B82F6&background=EBF4FF';
    }
}
```

## 🎨 Integrasikan Breeze dengan Blog Layout

### Step 6: Update Main Layout

Edit `resources/views/layouts/app.blade.php` untuk mengintegrasikan dengan authentication:

```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description', 'Blog Laravel - Tutorial web development dalam bahasa Indonesia')">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('head')
</head>
<body class="h-full bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center">
                    <a href="{{ route('blog.index') }}" class="flex items-center space-x-2 text-xl font-bold text-gray-900">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                        <span>{{ config('app.name', 'Blog Laravel') }}</span>
                    </a>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('blog.index') }}" 
                       class="nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}">
                        Blog
                    </a>
                    <a href="{{ route('about') }}" 
                       class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">
                        About
                    </a>
                    <a href="{{ route('contact') }}" 
                       class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">
                        Contact
                    </a>
                    
                    <!-- Search Form -->
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

                    <!-- Authentication Links -->
                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = ! open" class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 focus:outline-none">
                                <img src="{{ auth()->user()->avatar_url }}" 
                                     alt="{{ auth()->user()->name }}"
                                     class="w-8 h-8 rounded-full">
                                <span class="text-sm font-medium">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="open" 
                                 x-transition
                                 @click.away="open = false"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50">
                                
                                <a href="{{ route('profile.edit') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Profile Settings
                                </a>

                                @if(auth()->user()->canManagePosts())
                                    <a href="{{ route('admin.dashboard') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Admin Dashboard
                                    </a>
                                @endif

                                <div class="border-t border-gray-100"></div>
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('login') }}" 
                               class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                                Login
                            </a>
                            <a href="{{ route('register') }}" 
                               class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Register
                            </a>
                        </div>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-button" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Navigation -->
            <div id="mobile-menu" class="md:hidden hidden border-t border-gray-200 py-4">
                <div class="space-y-2">
                    <a href="{{ route('blog.index') }}" 
                       class="block px-4 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md {{ request()->routeIs('blog.*') ? 'bg-primary-50 text-primary-700' : '' }}">
                        Blog
                    </a>
                    <a href="{{ route('about') }}" 
                       class="block px-4 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md {{ request()->routeIs('about') ? 'bg-primary-50 text-primary-700' : '' }}">
                        About
                    </a>
                    <a href="{{ route('contact') }}" 
                       class="block px-4 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md {{ request()->routeIs('contact') ? 'bg-primary-50 text-primary-700' : '' }}">
                        Contact
                    </a>
                    
                    @auth
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex items-center px-4 py-2">
                                <img src="{{ auth()->user()->avatar_url }}" 
                                     alt="{{ auth()->user()->name }}"
                                     class="w-8 h-8 rounded-full mr-3">
                                <span class="font-medium text-gray-900">{{ auth()->user()->name }}</span>
                            </div>
                            <a href="{{ route('profile.edit') }}" 
                               class="block px-4 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">
                                Profile Settings
                            </a>
                            @if(auth()->user()->canManagePosts())
                                <a href="{{ route('admin.dashboard') }}" 
                                   class="block px-4 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">
                                    Admin Dashboard
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="block w-full text-left px-4 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="border-t border-gray-200 pt-4 space-y-2">
                            <a href="{{ route('login') }}" 
                               class="block px-4 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">
                                Login
                            </a>
                            <a href="{{ route('register') }}" 
                               class="block px-4 py-2 bg-primary-600 text-white hover:bg-primary-700 rounded-md mx-4">
                                Register
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="@yield('main-class', 'max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8')">
        @if(isset($showSidebar) && $showSidebar)
            <div class="lg:grid lg:grid-cols-3 lg:gap-12">
                <div class="lg:col-span-2">
                    @yield('content')
                </div>
                <div class="lg:col-span-1 mt-12 lg:mt-0">
                    @include('components.layout.sidebar')
                </div>
            </div>
        @else
            @yield('content')
        @endif
    </main>

    <!-- Footer -->
    @include('components.layout.footer')
    
    @stack('scripts')

    <style>
    .nav-link {
        @apply text-gray-600 hover:text-gray-900 transition-colors font-medium;
    }
    .nav-link.active {
        @apply text-primary-600;
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuButton?.addEventListener('click', function() {
            mobileMenu?.classList.toggle('hidden');
        });
    });
    </script>
</body>
</html>
```

### Step 7: Kustomisasi Breeze Views

Sesuaikan tampilan Breeze agar consistent dengan design blog kita.

Edit `resources/views/layouts/guest.blade.php`:

```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <!-- Logo -->
            <div class="mb-8">
                <a href="{{ route('blog.index') }}" class="flex items-center space-x-2 text-2xl font-bold text-gray-900">
                    <svg class="w-10 h-10 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    <span>{{ config('app.name', 'Blog Laravel') }}</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-lg overflow-hidden sm:rounded-xl">
                {{ $slot }}
            </div>

            <!-- Back to Blog Link -->
            <div class="mt-6">
                <a href="{{ route('blog.index') }}" 
                   class="text-sm text-gray-600 hover:text-gray-900 transition-colors">
                    ← Kembali ke Blog
                </a>
            </div>
        </div>
    </body>
</html>
```

## 🔐 Setup Admin Area Foundation

### Step 8: Create Admin Routes

Buat routes untuk admin area di `routes/web.php`:

```php
<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('blog.index');
});

// Blog routes (public)
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/search', [BlogController::class, 'search'])->name('search');
    Route::get('/post/{post:slug}', [BlogController::class, 'show'])->name('show');
    Route::get('/category/{category:slug}', [CategoryController::class, 'show'])->name('category');
    Route::get('/tag/{tag:slug}', [TagController::class, 'show'])->name('tag');
    Route::get('/author/{user:id}', [BlogController::class, 'author'])->name('author');
});

// Static pages
Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

// Authentication routes (handled by Breeze)
require __DIR__.'/auth.php';

// Profile routes (requires authentication)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes (requires auth + admin/author role)
Route::middleware(['auth', 'can:manage-posts'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Categories management (akan dibuat di pelajaran selanjutnya)
    // Route::resource('categories', CategoryController::class);
    
    // Posts management (akan dibuat di pelajaran selanjutnya)  
    // Route::resource('posts', PostController::class);
});
```

### Step 9: Create Authorization Gate

Buat gate untuk authorization di `app/Providers/AppServiceProvider.php`:

```php
<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Authorization gates
        Gate::define('manage-posts', function (User $user) {
            return $user->canManagePosts();
        });

        Gate::define('admin-access', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('author-access', function (User $user) {
            return $user->isAuthor();
        });
    }
}
```

### Step 10: Create Admin Dashboard Controller

```bash
php artisan make:controller Admin/DashboardController
```

Edit `app/Http/Controllers/Admin/DashboardController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistics
        $stats = [
            'total_posts' => Post::count(),
            'published_posts' => Post::where('status', 'published')->count(),
            'draft_posts' => Post::where('status', 'draft')->count(),
            'total_categories' => Category::count(),
            'total_tags' => Tag::count(),
            'total_users' => User::count(),
        ];

        // Recent posts
        $recentPosts = Post::with(['author', 'category'])
                          ->orderBy('created_at', 'desc')
                          ->limit(5)
                          ->get();

        // Popular posts
        $popularPosts = Post::published()
                           ->with(['author', 'category'])
                           ->orderBy('views_count', 'desc')
                           ->limit(5)
                           ->get();

        // Recent users
        $recentUsers = User::orderBy('created_at', 'desc')
                          ->limit(5)
                          ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentPosts',
            'popularPosts', 
            'recentUsers'
        ));
    }
}
```

### Step 11: Create Admin Dashboard View

Buat direktori dan file view:

```bash
mkdir resources/views/admin
```

Buat `resources/views/admin/dashboard.blade.php`:

```html
@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-600">Selamat datang kembali, {{ auth()->user()->name }}!</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Posts</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_posts'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Published</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['published_posts'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Drafts</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['draft_posts'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Categories</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_categories'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-2 bg-indigo-100 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Tags</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_tags'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-2 bg-pink-100 rounded-lg">
                    <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Users</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_users'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-8">
        <!-- Recent Posts -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Recent Posts</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($recentPosts as $post)
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-900 truncate">
                                {{ $post->title }}
                            </h3>
                            <p class="text-xs text-gray-500">
                                {{ $post->author->name }} • {{ $post->category->name }} • {{ $post->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                   {{ $post->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($post->status) }}
                        </span>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-4">Belum ada post yang dibuat</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Popular Posts -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Popular Posts</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($popularPosts as $post)
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-900 truncate">
                                {{ $post->title }}
                            </h3>
                            <p class="text-xs text-gray-500">
                                {{ $post->views_count }} views • {{ $post->published_at?->diffForHumans() }}
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-900">{{ $post->views_count }}</div>
                            <div class="text-xs text-gray-500">views</div>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-4">Belum ada post yang dipublikasi</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="#" class="flex items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary-500 transition-colors">
                    <div class="text-center">
                        <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="text-sm text-gray-600">New Post</span>
                    </div>
                </a>
                <a href="#" class="flex items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary-500 transition-colors">
                    <div class="text-center">
                        <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <span class="text-sm text-gray-600">Categories</span>
                    </div>
                </a>
                <a href="{{ route('blog.index') }}" class="flex items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary-500 transition-colors">
                    <div class="text-center">
                        <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span class="text-sm text-gray-600">View Blog</span>
                    </div>
                </a>
                <a href="{{ route('profile.edit') }}" class="flex items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary-500 transition-colors">
                    <div class="text-center">
                        <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="text-sm text-gray-600">Profile</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
```

## 🧪 Testing Authentication

### Step 12: Create Test Users

Buat seeder untuk admin user:

```bash
php artisan make:seeder AdminUserSeeder
```

Edit `database/seeders/AdminUserSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin Blog',
            'email' => 'admin@blog.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'bio' => 'Administrator blog Laravel Indonesia',
            'email_verified_at' => now(),
        ]);

        // Create author user
        User::create([
            'name' => 'Author Blog',
            'email' => 'author@blog.test', 
            'password' => Hash::make('password'),
            'role' => 'author',
            'bio' => 'Penulis artikel Laravel dan web development',
            'email_verified_at' => now(),
        ]);

        // Create regular user
        User::create([
            'name' => 'Regular User',
            'email' => 'user@blog.test',
            'password' => Hash::make('password'),
            'role' => 'user', 
            'bio' => 'Pembaca setia blog Laravel',
            'email_verified_at' => now(),
        ]);
    }
}
```

Update `database/seeders/DatabaseSeeder.php`:

```php
public function run(): void
{
    $this->call([
        AdminUserSeeder::class,
        CategorySeeder::class,
        TagSeeder::class,
        PostSeeder::class,
    ]);
}
```

Jalankan seeder:

```bash
php artisan db:seed --class=AdminUserSeeder
```

### Step 13: Test Authentication Flow

Test semua fitur authentication:

```bash
php artisan serve
npm run dev
```

Test URLs:
- `/register` - Registration form
- `/login` - Login form  
- `/admin/dashboard` - Admin dashboard (perlu login sebagai admin/author)
- `/profile` - Profile settings (perlu login)

## 🎯 Kesimpulan

Selamat! Anda telah berhasil:
- ✅ Menginstall dan konfigurasi Laravel Breeze
- ✅ Mengintegrasikan authentication dengan blog layout
- ✅ Membuat role-based authorization (admin, author, user)
- ✅ Menyiapkan admin dashboard foundation
- ✅ Membuat test users untuk berbagai roles
- ✅ Mengkustomisasi Breeze views sesuai design blog

Authentication system sekarang sudah siap. Di pelajaran selanjutnya, kita akan mulai membangun CRUD operations untuk categories.

---

**Selanjutnya:** [Pelajaran 11: Categories CRUD Operations](11-crud-categories.md)

*Authentication ready! 🔐*