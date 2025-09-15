# Pelajaran 4: Membangun Navigation dan Layout yang Dapat Digunakan Ulang

Dalam pelajaran ini, kita akan memperbaiki struktur layout dan membuat navigation yang lebih sophisticated dan reusable.

## 🎯 Tujuan Pembelajaran

Setelah menyelesaikan pelajaran ini, Anda akan:
- ✅ Memahami konsep layout inheritance di Blade
- ✅ Membuat navigation component yang reusable
- ✅ Mengimplementasi active state untuk navigation
- ✅ Membuat footer dan sidebar components
- ✅ Mengerti component-based architecture

## 🏗️ Memperbaiki Layout Structure

### Step 1: Membuat Layout Modular

Buat direktori untuk components:

```bash
mkdir resources/views/components
mkdir resources/views/components/layout
```

### Step 2: Navigation Component

Buat file `resources/views/components/layout/navigation.blade.php`:

```html
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
                    <span>Blog Laravel</span>
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
                
                <!-- Search -->
                <div class="relative">
                    <input type="search" 
                           placeholder="Cari artikel..." 
                           class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
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
                
                <!-- Mobile Search -->
                <div class="px-4 pt-2">
                    <input type="search" 
                           placeholder="Cari artikel..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    mobileMenuButton?.addEventListener('click', function() {
        mobileMenu?.classList.toggle('hidden');
    });
});
</script>
```

### Step 3: Menambahkan CSS Styles ke app.css

**PENTING: Untuk Tailwind CSS v4**, CSS dengan directive `@apply` tidak boleh ditulis di dalam `<style>` tag dalam file Blade karena akan menyebabkan error. Kita harus memindahkannya ke file CSS terpisah.

Tambahkan CSS untuk navigation di `resources/css/app.css`:

```css
/* CSS untuk Navigation - Tambahkan di akhir file app.css */
.nav-link {
    @apply text-gray-600 hover:text-gray-900 transition-colors font-medium;
}

.nav-link.active {
    @apply text-primary-600;
}
```

**Catatan Best Practice:**
- ✅ CSS dengan `@apply` harus di file `.css`, bukan di Blade
- ✅ Pisahkan styling dari markup untuk maintainability
- ✅ Gunakan Tailwind directives di CSS files

### Step 4: Konfigurasi VS Code untuk Tailwind v4

Buat file `.vscode/settings.json` di root project untuk mengatasi error Tailwind v4:

```json
{
  "css.validate": false,
  "less.validate": false,
  "scss.validate": false,
  "tailwindCSS.experimental.configFile": "resources/css/app.css",
  "tailwindCSS.includeLanguages": {
    "html": "html",
    "php": "html",
    "blade": "html"
  },
  "files.associations": {
    "*.blade.php": "blade"
  },
  "emmet.includeLanguages": {
    "blade": "html"
  }
}
```

**Langkah tambahan:**
1. Install extension "Tailwind CSS IntelliSense" di VS Code
2. Install extension "Laravel Blade Snippets" untuk syntax highlighting
3. Reload VS Code setelah konfigurasi

### Step 6: Footer Component

Buat file `resources/views/components/layout/footer.blade.php`:

```html
<footer class="bg-white border-t mt-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid md:grid-cols-4 gap-8">
            <!-- Brand -->
            <div class="md:col-span-1">
                <div class="flex items-center space-x-2 text-xl font-bold text-gray-900 mb-4">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    <span>Blog Laravel</span>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Berbagi pengalaman dan pembelajaran tentang web development 
                    dengan Laravel dan teknologi modern.
                </p>
            </div>
            
            <!-- Navigation -->
            <div>
                <h3 class="font-semibold text-gray-900 mb-4">Navigasi</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('blog.index') }}" class="text-gray-600 hover:text-gray-900 text-sm transition-colors">Blog</a></li>
                    <li><a href="{{ route('about') }}" class="text-gray-600 hover:text-gray-900 text-sm transition-colors">About</a></li>
                    <li><a href="{{ route('contact') }}" class="text-gray-600 hover:text-gray-900 text-sm transition-colors">Contact</a></li>
                </ul>
            </div>
            
            <!-- Categories -->
            <div>
                <h3 class="font-semibold text-gray-900 mb-4">Kategori</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-600 hover:text-gray-900 text-sm transition-colors">Laravel</a></li>
                    <li><a href="#" class="text-gray-600 hover:text-gray-900 text-sm transition-colors">PHP</a></li>
                    <li><a href="#" class="text-gray-600 hover:text-gray-900 text-sm transition-colors">JavaScript</a></li>
                    <li><a href="#" class="text-gray-600 hover:text-gray-900 text-sm transition-colors">Tutorial</a></li>
                </ul>
            </div>
            
            <!-- Social & Contact -->
            <div>
                <h3 class="font-semibold text-gray-900 mb-4">Ikuti Kami</h3>
                <div class="flex space-x-3 mb-4">
                    <a href="#" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <span class="sr-only">Twitter</span>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <span class="sr-only">GitHub</span>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 0C4.477 0 0 4.484 0 10.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0110 4.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.203 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.942.359.31.678.921.678 1.856 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0020 10.017C20 4.484 15.522 0 10 0z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <span class="sr-only">LinkedIn</span>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-.003-3.096 1.548 1.548 0 01.003 3.096zm-1.337 9.763H6.34v-8.59H3.667v8.59zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.403C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.299V2.298C19 1.581 18.402 1 17.668 1z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                </div>
                <p class="text-gray-600 text-sm">
                    📧 contact@bloglaravel.com
                </p>
            </div>
        </div>
        
        <!-- Copyright -->
        <div class="border-t border-gray-200 pt-8 mt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-500 text-sm">
                    &copy; {{ date('Y') }} Blog Laravel. All rights reserved.
                </p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="text-gray-500 hover:text-gray-700 text-sm transition-colors">Privacy Policy</a>
                    <a href="#" class="text-gray-500 hover:text-gray-700 text-sm transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </div>
</footer>
```

### Step 7: Sidebar Component

Buat file `resources/views/components/layout/sidebar.blade.php`:

```html
<aside class="space-y-8">
    <!-- About Widget -->
    <div class="card">
        <div class="p-6">
            <h3 class="font-bold text-gray-900 mb-4">Tentang Blog Ini</h3>
            <p class="text-gray-600 text-sm leading-relaxed mb-4">
                Blog ini dibuat untuk berbagi pengalaman dan pembelajaran tentang 
                Laravel dan web development modern.
            </p>
            <a href="{{ route('about') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                Selengkapnya →
            </a>
        </div>
    </div>
    
    <!-- Recent Posts -->
    <div class="card">
        <div class="p-6">
            <h3 class="font-bold text-gray-900 mb-4">Post Terbaru</h3>
            <div class="space-y-4">
                <article class="group">
                    <h4 class="font-medium text-gray-900 group-hover:text-primary-600 transition-colors mb-1">
                        <a href="{{ route('blog.show', 1) }}">
                            Memulai dengan Laravel 12
                        </a>
                    </h4>
                    <p class="text-gray-500 text-xs">9 September 2025</p>
                </article>
                
                <article class="group">
                    <h4 class="font-medium text-gray-900 group-hover:text-primary-600 transition-colors mb-1">
                        <a href="{{ route('blog.show', 2) }}">
                            Mengapa Memilih Laravel?
                        </a>
                    </h4>
                    <p class="text-gray-500 text-xs">8 September 2025</p>
                </article>
                
                <article class="group">
                    <h4 class="font-medium text-gray-900 group-hover:text-primary-600 transition-colors mb-1">
                        <a href="{{ route('blog.show', 3) }}">
                            Tips Produktivitas Laravel
                        </a>
                    </h4>
                    <p class="text-gray-500 text-xs">7 September 2025</p>
                </article>
            </div>
        </div>
    </div>
    
    <!-- Categories -->
    <div class="card">
        <div class="p-6">
            <h3 class="font-bold text-gray-900 mb-4">Kategori</h3>
            <div class="space-y-2">
                <a href="#" class="flex items-center justify-between text-gray-600 hover:text-gray-900 transition-colors">
                    <span>Laravel</span>
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">8</span>
                </a>
                <a href="#" class="flex items-center justify-between text-gray-600 hover:text-gray-900 transition-colors">
                    <span>PHP</span>
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">5</span>
                </a>
                <a href="#" class="flex items-center justify-between text-gray-600 hover:text-gray-900 transition-colors">
                    <span>Tutorial</span>
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">12</span>
                </a>
                <a href="#" class="flex items-center justify-between text-gray-600 hover:text-gray-900 transition-colors">
                    <span>Tips</span>
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">3</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Newsletter -->
    <div class="card bg-gradient-to-br from-primary-50 to-primary-100">
        <div class="p-6">
            <h3 class="font-bold text-gray-900 mb-2">Newsletter</h3>
            <p class="text-gray-600 text-sm mb-4">
                Dapatkan update artikel terbaru langsung di inbox Anda.
            </p>
            <form class="space-y-3">
                <input type="email" 
                       placeholder="Email Anda..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                <button type="submit" class="w-full btn-primary text-sm py-2">
                    Subscribe
                </button>
            </form>
        </div>
    </div>
</aside>
```

### Step 8: Update Main Layout

Update `resources/views/layouts/app.blade.php`:

```html
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('description', 'Blog Laravel - Berbagi pengalaman web development')">
    <title>@yield('title', 'Blog Laravel Saya')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('head')
</head>
<body class="h-full bg-gray-50">
    <!-- Navigation -->
    @include('components.layout.navigation')

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
</body>
</html>
```

### Step 9: Update Blog Views

Update `resources/views/blog/index.blade.php`:

```html
@extends('layouts.app')

@section('title', 'Blog - Laravel Tutorial Indonesia')
@section('description', 'Tutorial Laravel dalam bahasa Indonesia - Belajar web development modern dengan Laravel framework.')

@section('content')
@php $showSidebar = true; @endphp

<div class="space-y-8">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-2xl text-white p-8 lg:p-12">
        <div class="max-w-3xl">
            <h1 class="text-4xl lg:text-5xl font-bold mb-4">
                Selamat Datang di Blog Laravel
            </h1>
            <p class="text-xl text-primary-100 mb-6">
                Tempat belajar Laravel dan web development modern dalam bahasa Indonesia. 
                Dari basic hingga advanced, semua ada di sini.
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="#posts" class="bg-white text-primary-600 font-semibold px-6 py-3 rounded-lg hover:bg-gray-50 transition-colors text-center">
                    Mulai Belajar
                </a>
                <a href="{{ route('about') }}" class="border border-primary-200 text-white font-semibold px-6 py-3 rounded-lg hover:bg-primary-500 transition-colors text-center">
                    Tentang Blog
                </a>
            </div>
        </div>
    </div>

    <!-- Featured Categories -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-4 rounded-lg text-center">
            <div class="text-2xl mb-2">🚀</div>
            <h3 class="font-semibold">Laravel</h3>
            <p class="text-xs opacity-80">8 artikel</p>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-4 rounded-lg text-center">
            <div class="text-2xl mb-2">📚</div>
            <h3 class="font-semibold">Tutorial</h3>
            <p class="text-xs opacity-80">12 artikel</p>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-4 rounded-lg text-center">
            <div class="text-2xl mb-2">💡</div>
            <h3 class="font-semibold">Tips</h3>
            <p class="text-xs opacity-80">3 artikel</p>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white p-4 rounded-lg text-center">
            <div class="text-2xl mb-2">🛠️</div>
            <h3 class="font-semibold">Tools</h3>
            <p class="text-xs opacity-80">5 artikel</p>
        </div>
    </div>

    <!-- Blog Posts -->
    <div id="posts" class="space-y-8">
        <h2 class="text-3xl font-bold text-gray-900">Artikel Terbaru</h2>
        
        <div class="space-y-8">
            <!-- Featured Post -->
            <article class="card overflow-hidden">
                <div class="md:flex">
                    <div class="md:w-1/3 bg-gradient-to-br from-primary-500 to-primary-600 p-8 text-white flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-6xl mb-4">🚀</div>
                            <div class="text-sm font-medium">FEATURED</div>
                        </div>
                    </div>
                    <div class="md:w-2/3 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="bg-primary-100 text-primary-700 text-sm font-medium px-3 py-1 rounded-full">
                                Laravel
                            </span>
                            <time class="text-gray-500 text-sm">9 September 2025</time>
                        </div>
                        
                        <h2 class="text-2xl font-bold text-gray-900 mb-3">
                            <a href="{{ route('blog.show', 1) }}" class="hover:text-primary-600 transition-colors">
                                Memulai Perjalanan dengan Laravel 12: Panduan Lengkap
                            </a>
                        </h2>
                        
                        <p class="text-gray-600 mb-4 leading-relaxed">
                            Laravel 12 membawa banyak fitur baru yang revolusioner. Dalam artikel ini, 
                            saya akan memandu Anda step-by-step untuk memulai project pertama dengan Laravel 12.
                        </p>
                        
                        <div class="flex items-center justify-between">
                            <a href="{{ route('blog.show', 1) }}" class="btn-primary">
                                Baca Artikel
                            </a>
                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                <span>8 min read</span>
                                <span>•</span>
                                <span>156 views</span>
                            </div>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Regular Posts -->
            @for($i = 2; $i <= 4; $i++)
            <article class="card hover:shadow-lg transition-shadow">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="bg-{{ $i == 2 ? 'green' : ($i == 3 ? 'purple' : 'orange') }}-100 text-{{ $i == 2 ? 'green' : ($i == 3 ? 'purple' : 'orange') }}-700 text-sm font-medium px-3 py-1 rounded-full">
                            {{ $i == 2 ? 'Tutorial' : ($i == 3 ? 'Tips' : 'Tools') }}
                        </span>
                        <time class="text-gray-500 text-sm">{{ 11 - $i }} September 2025</time>
                    </div>
                    
                    <h2 class="text-xl font-bold text-gray-900 mb-3">
                        <a href="{{ route('blog.show', $i) }}" class="hover:text-primary-600 transition-colors">
                            {{ $i == 2 ? 'Mengapa Memilih Laravel untuk Project Anda?' : ($i == 3 ? '10 Tips Produktivitas untuk Developer Laravel' : 'Tools Wajib untuk Laravel Developer') }}
                        </a>
                    </h2>
                    
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        {{ $i == 2 ? 'Laravel menyediakan ekosistem yang lengkap dan syntax yang elegant. Mari kita bahas mengapa Laravel menjadi pilihan utama developer.' : ($i == 3 ? 'Tingkatkan produktivitas coding Anda dengan tips dan trick yang telah teruji. Dari Artisan commands sampai debugging tools.' : 'Kumpulan tools dan extension yang akan membuat development Laravel Anda jauh lebih efisien dan menyenangkan.') }}
                    </p>
                    
                    <div class="flex items-center justify-between">
                        <a href="{{ route('blog.show', $i) }}" class="text-primary-600 hover:text-primary-700 font-medium text-sm">
                            Baca Selengkapnya →
                        </a>
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <span>{{ $i + 2 }} min read</span>
                            <span>•</span>
                            <span>{{ rand(20, 80) }} views</span>
                        </div>
                    </div>
                </div>
            </article>
            @endfor
        </div>

        <!-- Load More -->
        <div class="text-center">
            <button class="bg-white border border-gray-300 text-gray-700 font-medium px-6 py-3 rounded-lg hover:bg-gray-50 transition-colors">
                Muat Artikel Lainnya
            </button>
        </div>
    </div>
</div>
@endsection
```

## 🎯 Membuat Halaman About & Contact

### About Page

Buat `resources/views/about.blade.php`:

```html
@extends('layouts.app')

@section('title', 'About - Blog Laravel')
@section('description', 'Tentang blog Laravel dan author yang berbagi pengalaman web development.')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Tentang Blog Ini</h1>
        <p class="text-xl text-gray-600">
            Berbagi pengetahuan Laravel dan web development dalam bahasa Indonesia
        </p>
    </div>

    <!-- Content -->
    <div class="grid md:grid-cols-2 gap-12 items-center mb-16">
        <div class="space-y-6">
            <h2 class="text-2xl font-bold text-gray-900">Misi Kami</h2>
            <p class="text-gray-600 leading-relaxed">
                Blog ini dibuat dengan tujuan untuk menyediakan tutorial Laravel 
                berkualitas dalam bahasa Indonesia. Kami percaya bahwa setiap developer 
                berhak mendapatkan akses pembelajaran yang mudah dipahami.
            </p>
            <p class="text-gray-600 leading-relaxed">
                Dari basic hingga advanced, kami akan membahas semua aspek Laravel 
                development dengan pendekatan praktis dan real-world examples.
            </p>
        </div>
        <div class="bg-gradient-to-br from-primary-500 to-primary-600 text-white p-8 rounded-2xl">
            <div class="text-center">
                <div class="text-5xl mb-4">📚</div>
                <h3 class="text-xl font-semibold mb-2">Learning-First Approach</h3>
                <p class="text-primary-100">
                    Setiap tutorial dirancang untuk memberikan pengalaman belajar 
                    yang optimal dengan contoh praktis.
                </p>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-16">
        <div class="text-center">
            <div class="text-3xl font-bold text-primary-600 mb-2">25+</div>
            <div class="text-gray-600">Tutorial</div>
        </div>
        <div class="text-center">
            <div class="text-3xl font-bold text-primary-600 mb-2">1000+</div>
            <div class="text-gray-600">Pembaca</div>
        </div>
        <div class="text-center">
            <div class="text-3xl font-bold text-primary-600 mb-2">50+</div>
            <div class="text-gray-600">Code Examples</div>
        </div>
        <div class="text-center">
            <div class="text-3xl font-bold text-primary-600 mb-2">100%</div>
            <div class="text-gray-600">Gratis</div>
        </div>
    </div>

    <!-- Author -->
    <div class="bg-white rounded-2xl p-8 shadow-sm">
        <div class="flex flex-col md:flex-row items-center space-y-6 md:space-y-0 md:space-x-8">
            <div class="w-32 h-32 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-white text-4xl font-bold">
                BL
            </div>
            <div class="text-center md:text-left">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Blog Laravel Author</h3>
                <p class="text-primary-600 font-medium mb-4">Full-Stack Developer & Laravel Enthusiast</p>
                <p class="text-gray-600 leading-relaxed">
                    Passionate about web development dan open source. Sudah menggunakan Laravel 
                    sejak versi 5 dan senang berbagi pengalaman dengan komunitas developer Indonesia.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
```

### Contact Page

Buat `resources/views/contact.blade.php`:

```html
@extends('layouts.app')

@section('title', 'Contact - Blog Laravel')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Hubungi Kami</h1>
        <p class="text-xl text-gray-600">
            Ada pertanyaan atau saran? Jangan ragu untuk menghubungi kami!
        </p>
    </div>

    <div class="grid md:grid-cols-2 gap-12">
        <!-- Contact Form -->
        <div class="card">
            <div class="p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Kirim Pesan</h2>
                <form class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap
                        </label>
                        <input type="text" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               placeholder="John Doe">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input type="email" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               placeholder="john@example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Subject
                        </label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option>Pertanyaan Umum</option>
                            <option>Request Tutorial</option>
                            <option>Laporkan Bug</option>
                            <option>Kolaborasi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pesan
                        </label>
                        <textarea rows="6" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                  placeholder="Tulis pesan Anda di sini..."></textarea>
                    </div>
                    <button type="submit" class="w-full btn-primary py-3">
                        Kirim Pesan
                    </button>
                </form>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="space-y-8">
            <div class="card">
                <div class="p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Info Kontak</h3>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center">
                                📧
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Email</div>
                                <div class="text-gray-600 text-sm">contact@bloglaravel.com</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center">
                                🐦
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Twitter</div>
                                <div class="text-gray-600 text-sm">@bloglaravel</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center">
                                💻
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">GitHub</div>
                                <div class="text-gray-600 text-sm">github.com/bloglaravel</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ -->
            <div class="card">
                <div class="p-6">
                    <h3 class="font-bold text-gray-900 mb-4">FAQ</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="font-medium text-gray-900 mb-1">Apakah tutorial gratis?</div>
                            <div class="text-gray-600 text-sm">Ya, semua tutorial di blog ini gratis 100%.</div>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900 mb-1">Bisa request tutorial?</div>
                            <div class="text-gray-600 text-sm">Tentu! Kirim request melalui form di samping.</div>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900 mb-1">Source code tersedia?</div>
                            <div class="text-gray-600 text-sm">Ya, semua ada di GitHub repository kami.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

## ✅ Update Routes untuk Named Routes

Pastikan semua route sudah menggunakan named routes:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('blog.index');
});

Route::get('/blog', function () {
    return view('blog.index');
})->name('blog.index');

Route::get('/blog/post/{id}', function ($id) {
    return view('blog.show', ['id' => $id]);
})->name('blog.show');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');
```

## ⚠️ Troubleshooting untuk Tailwind CSS v4

### Masalah yang Sering Terjadi:

**1. Error "Unknown at rule @apply"**
- **Lokasi**: `navigation.blade.php` baris 80 & 83
- **Penyebab**: CSS `@apply` di dalam `<style>` tag dalam file Blade
- **Solusi**: Pindahkan CSS ke `resources/css/app.css`

**2. Error di app.css dengan Tailwind v4**
- **Error**: `Unknown at rule @source`, `@theme`, `@apply`
- **Penyebab**: VS Code tidak mengenali Tailwind CSS v4 directives
- **Solusi**:
  1. Buat `.vscode/settings.json` dengan konfigurasi di atas
  2. Install extension "Tailwind CSS IntelliSense"
  3. Restart VS Code

### Langkah Perbaikan Lengkap:

```bash
# 1. Pindahkan CSS dari Blade ke app.css
# HAPUS dari navigation.blade.php:
<style>
.nav-link { @apply text-gray-600 hover:text-gray-900 transition-colors font-medium; }
.nav-link.active { @apply text-primary-600; }
</style>

# TAMBAHKAN ke resources/css/app.css:
.nav-link {
    @apply text-gray-600 hover:text-gray-900 transition-colors font-medium;
}
.nav-link.active {
    @apply text-primary-600;
}
```

```json
// 2. Buat .vscode/settings.json
{
  "css.validate": false,
  "tailwindCSS.experimental.configFile": null,
  "tailwindCSS.files.exclude": [],
  "files.associations": {
    "*.blade.php": "blade"
  },
  "emmet.includeLanguages": {
    "blade": "html"
  }
}
```

**3. Install Extensions yang Diperlukan:**
- Tailwind CSS IntelliSense
- Laravel Blade Snippets

**Best Practices untuk Tailwind v4:**
- ✅ Gunakan CSS directives di file `.css`, bukan di Blade
- ✅ Pisahkan styling dari markup
- ✅ Konfigurasi VS Code untuk Tailwind v4
- ✅ Gunakan extension untuk syntax highlighting

## 🎯 Kesimpulan

Selamat! Anda telah berhasil:
- ✅ Membuat layout modular dengan components
- ✅ Implementasi navigation dengan active states
- ✅ Membuat footer dan sidebar yang reusable
- ✅ Mendesain halaman About dan Contact
- ✅ Menggunakan component-based architecture

Modul 1 telah selesai! Di modul selanjutnya, kita akan mulai bekerja dengan database dan mengganti data dummy dengan data real.

---

**Selanjutnya:** [Pelajaran 5: Mendesain Layout Project Blog](05-design-layout-blog.md)

*Build it modular! 🏗️*
