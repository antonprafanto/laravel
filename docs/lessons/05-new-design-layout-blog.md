# Pelajaran 5: New Design Layout for Blog Project

Dalam pelajaran ini, kita akan membuat design layout baru yang lebih terstruktur untuk project blog kita. Layout ini akan mempersiapkan template untuk integrasi dengan database di pelajaran-pelajaran selanjutnya.

## 🎯 Tujuan Pembelajaran

Setelah menyelesaikan pelajaran ini, Anda akan:
- ✅ Membuat layout blog yang lebih professional dan clean
- ✅ Menyiapkan template untuk data dinamis dari database
- ✅ Memahami struktur template yang scalable
- ✅ Membuat placeholder yang siap untuk data real

## 🎨 Redesign Layout Blog

### Step 1: Update Blog Homepage Layout

Mari kita update `resources/views/blog/index.blade.php` dengan design yang lebih fokus:

**💡 Best Practice - Named Routes**:
Dalam lesson ini, kita akan konsisten menggunakan named routes seperti `{{ route('blog.show', $id) }}` instead of hardcoded URLs seperti `/blog/post/1`. Ini mengikuti Laravel conventions yang sudah kita establish di lesson-lesson sebelumnya dan memudahkan maintenance.

```html
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

    <!-- Featured Post Section -->
    <section class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl text-white overflow-hidden">
        <div class="p-8 lg:p-12">
            <div class="lg:grid lg:grid-cols-2 lg:gap-12 items-center">
                <div>
                    <div class="inline-flex items-center bg-blue-500 bg-opacity-50 rounded-full px-4 py-2 text-sm font-medium mb-4">
                        <span class="w-2 h-2 bg-yellow-400 rounded-full mr-2"></span>
                        Featured Post
                    </div>
                    <h2 class="text-3xl lg:text-4xl font-bold mb-4">
                        Membangun Aplikasi Blog dengan Laravel 12
                    </h2>
                    <p class="text-xl text-blue-100 mb-6">
                        Pelajari cara membangun aplikasi blog lengkap dari awal hingga deploy dengan Laravel 12 terbaru.
                    </p>
                    <div class="flex items-center space-x-6 text-blue-200 text-sm mb-6">
                        <span>By Admin</span>
                        <span>•</span>
                        <span>10 September 2025</span>
                        <span>•</span>
                        <span>8 min read</span>
                    </div>
                    <a href="{{ route('blog.show', 1) }}" class="inline-flex items-center bg-white text-blue-600 font-semibold px-6 py-3 rounded-lg hover:bg-gray-50 transition-colors">
                        Baca Artikel
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                <div class="mt-8 lg:mt-0">
                    <div class="bg-white bg-opacity-10 rounded-xl p-6 backdrop-blur-sm">
                        <div class="space-y-3">
                            <div class="h-4 bg-white bg-opacity-20 rounded"></div>
                            <div class="h-4 bg-white bg-opacity-20 rounded w-4/5"></div>
                            <div class="h-4 bg-white bg-opacity-20 rounded w-3/5"></div>
                            <div class="h-32 bg-white bg-opacity-10 rounded-lg mt-4"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Posts -->
    <section>
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Recent Posts</h2>
            <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">
                View All →
            </a>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <!-- Post 1 -->
            <article class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                <div class="aspect-video bg-gradient-to-br from-blue-500 to-blue-600 relative">
                    <div class="absolute inset-0 flex items-center justify-center text-white">
                        <div class="text-center">
                            <div class="text-4xl mb-2">🚀</div>
                            <div class="text-sm font-medium">LARAVEL</div>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-4 text-sm text-gray-500 mb-3">
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-medium">Laravel</span>
                        <span>9 September 2025</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">
                        <a href="{{ route('blog.show', 2) }}" class="hover:text-blue-600 transition-colors">
                            Laravel Eloquent: Tips dan Tricks untuk Developer
                        </a>
                    </h3>
                    <p class="text-gray-600 mb-4">
                        Pelajari tips dan tricks Eloquent ORM yang akan membuat kode Laravel Anda lebih efisien dan maintainable.
                    </p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <span>5 min read</span>
                            <span>•</span>
                            <span>124 views</span>
                        </div>
                        <a href="{{ route('blog.show', 2) }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                            Read More
                        </a>
                    </div>
                </div>
            </article>

            <!-- Post 2 -->
            <article class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                <div class="aspect-video bg-gradient-to-br from-green-500 to-green-600 relative">
                    <div class="absolute inset-0 flex items-center justify-center text-white">
                        <div class="text-center">
                            <div class="text-4xl mb-2">📚</div>
                            <div class="text-sm font-medium">TUTORIAL</div>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-4 text-sm text-gray-500 mb-3">
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full font-medium">Tutorial</span>
                        <span>8 September 2025</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">
                        <a href="{{ route('blog.show', 3) }}" class="hover:text-blue-600 transition-colors">
                            Membuat REST API dengan Laravel Sanctum
                        </a>
                    </h3>
                    <p class="text-gray-600 mb-4">
                        Tutorial step-by-step membuat REST API yang secure menggunakan Laravel Sanctum untuk authentication.
                    </p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <span>12 min read</span>
                            <span>•</span>
                            <span>89 views</span>
                        </div>
                        <a href="{{ route('blog.show', 3) }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                            Read More
                        </a>
                    </div>
                </div>
            </article>

            <!-- Post 3 -->
            <article class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                <div class="aspect-video bg-gradient-to-br from-purple-500 to-purple-600 relative">
                    <div class="absolute inset-0 flex items-center justify-center text-white">
                        <div class="text-center">
                            <div class="text-4xl mb-2">⚡</div>
                            <div class="text-sm font-medium">PERFORMANCE</div>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-4 text-sm text-gray-500 mb-3">
                        <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full font-medium">Performance</span>
                        <span>7 September 2025</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">
                        <a href="{{ route('blog.show', 4) }}" class="hover:text-blue-600 transition-colors">
                            Optimasi Performa Aplikasi Laravel
                        </a>
                    </h3>
                    <p class="text-gray-600 mb-4">
                        Teknik-teknik optimasi yang proven untuk meningkatkan performa aplikasi Laravel Anda hingga 10x lebih cepat.
                    </p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <span>15 min read</span>
                            <span>•</span>
                            <span>203 views</span>
                        </div>
                        <a href="{{ route('blog.show', 4) }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                            Read More
                        </a>
                    </div>
                </div>
            </article>

            <!-- Post 4 -->
            <article class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                <div class="aspect-video bg-gradient-to-br from-orange-500 to-orange-600 relative">
                    <div class="absolute inset-0 flex items-center justify-center text-white">
                        <div class="text-center">
                            <div class="text-4xl mb-2">🛠️</div>
                            <div class="text-sm font-medium">TOOLS</div>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-4 text-sm text-gray-500 mb-3">
                        <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full font-medium">Tools</span>
                        <span>6 September 2025</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">
                        <a href="{{ route('blog.show', 5) }}" class="hover:text-blue-600 transition-colors">
                            Developer Tools Wajib untuk Laravel
                        </a>
                    </h3>
                    <p class="text-gray-600 mb-4">
                        Kumpulan tools dan packages yang akan significantly meningkatkan produktivitas development Laravel Anda.
                    </p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <span>8 min read</span>
                            <span>•</span>
                            <span>156 views</span>
                        </div>
                        <a href="{{ route('blog.show', 5) }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                            Read More
                        </a>
                    </div>
                </div>
            </article>
        </div>
    </section>

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
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors">
                    Subscribe
                </button>
            </form>
        </div>
    </section>
</div>
@endsection
```

### Step 2: Update Single Post Template

Mari kita update `resources/views/blog/show.blade.php` untuk template yang lebih clean:

```html
@extends('layouts.app')

@section('title', 'Post #' . $id . ' - Blog Laravel')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('blog.index') }}" class="text-gray-500 hover:text-gray-700">Blog</a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 text-gray-500 md:ml-2">Laravel</span>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 text-gray-500 md:ml-2">Post {{ $id }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Article -->
    <article class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <!-- Featured Image Placeholder -->
        <div class="aspect-video bg-gradient-to-br from-blue-500 to-blue-600 relative">
            <div class="absolute inset-0 flex items-center justify-center text-white">
                <div class="text-center">
                    <div class="text-6xl mb-4">📝</div>
                    <div class="text-lg font-medium">Featured Image</div>
                    <div class="text-sm opacity-75">Post {{ $id }}</div>
                </div>
            </div>
        </div>

        <!-- Article Header -->
        <div class="p-8 lg:p-12 border-b">
            <div class="flex items-center space-x-4 text-sm text-gray-500 mb-6">
                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-medium">Laravel</span>
                <span>•</span>
                <span>10 September 2025</span>
                <span>•</span>
                <span>8 min read</span>
                <span>•</span>
                <span>By Admin</span>
            </div>
            
            <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                Judul Artikel Blog Post {{ $id }}: Membangun Aplikasi Modern dengan Laravel
            </h1>
            
            <p class="text-xl text-gray-600 leading-relaxed">
                Ini adalah excerpt atau ringkasan singkat dari artikel yang akan menarik pembaca 
                untuk membaca keseluruhan konten. Lorem ipsum dolor sit amet consectetur.
            </p>
        </div>

        <!-- Article Content -->
        <div class="p-8 lg:p-12">
            <div class="prose prose-lg max-w-none">
                <p class="lead text-xl text-gray-700 mb-8">
                    Ini adalah konten utama untuk post dengan ID <strong>{{ $id }}</strong>. 
                    Nanti konten ini akan diambil dari database menggunakan Eloquent models.
                </p>
                
                <h2>Pengenalan Laravel Framework</h2>
                <p>
                    Laravel adalah salah satu framework PHP yang paling populer dan powerful di dunia. 
                    Dengan syntax yang elegant dan ekspressive, Laravel memungkinkan developer untuk 
                    membangun aplikasi web dengan cepat dan efisien.
                </p>
                
                <blockquote class="border-l-4 border-blue-500 pl-6 py-4 bg-gray-50 rounded-r-lg">
                    <p class="text-lg italic text-gray-700">
                        "Laravel takes the pain out of development by easing common tasks used in many web projects."
                    </p>
                    <cite class="block text-right text-gray-600 mt-2">— Taylor Otwell, Creator of Laravel</cite>
                </blockquote>
                
                <h3>Fitur-fitur Unggulan Laravel</h3>
                <ul>
                    <li><strong>Eloquent ORM</strong> - Object-relational mapping yang powerful dan intuitive</li>
                    <li><strong>Blade Templating</strong> - Template engine yang simple namun powerful</li>
                    <li><strong>Artisan CLI</strong> - Command line interface untuk development</li>
                    <li><strong>Route Model Binding</strong> - Automatic model injection</li>
                    <li><strong>Middleware</strong> - HTTP middleware untuk filtering requests</li>
                </ul>
                
                <h3>Mengapa Memilih Laravel?</h3>
                <p>
                    Laravel menyediakan tools dan fitur yang diperlukan untuk membangun aplikasi web modern:
                </p>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 my-8">
                    <h4 class="text-lg font-semibold text-blue-900 mb-3">💡 Pro Tip</h4>
                    <p class="text-blue-800">
                        Mulai dengan project sederhana seperti blog untuk memahami konsep dasar Laravel 
                        sebelum moving ke aplikasi yang lebih kompleks.
                    </p>
                </div>
                
                <h3>Langkah Selanjutnya</h3>
                <p>
                    Di pelajaran-pelajaran berikutnya, kita akan mulai membangun aplikasi blog ini dengan:
                </p>
                <ol>
                    <li>Membuat database migrations</li>
                    <li>Membangun Eloquent models</li>
                    <li>Implementasi CRUD operations</li>
                    <li>Adding authentication dan authorization</li>
                </ol>
                
                <p>
                    Stay tuned untuk tutorial selanjutnya!
                </p>
            </div>
        </div>

        <!-- Article Footer -->
        <div class="bg-gray-50 p-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <!-- Tags -->
                <div class="mb-4 md:mb-0">
                    <span class="text-sm font-medium text-gray-600 mr-3">Tags:</span>
                    <div class="inline-flex flex-wrap gap-2">
                        <span class="bg-white text-gray-700 px-3 py-1 rounded-full text-sm border">Laravel</span>
                        <span class="bg-white text-gray-700 px-3 py-1 rounded-full text-sm border">PHP</span>
                        <span class="bg-white text-gray-700 px-3 py-1 rounded-full text-sm border">Tutorial</span>
                        <span class="bg-white text-gray-700 px-3 py-1 rounded-full text-sm border">Beginner</span>
                    </div>
                </div>
                
                <!-- Share -->
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-gray-600">Share:</span>
                    <button class="text-gray-500 hover:text-blue-600 transition-colors">
                        <span class="sr-only">Twitter</span>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84"/>
                        </svg>
                    </button>
                    <button class="text-gray-500 hover:text-blue-600 transition-colors">
                        <span class="sr-only">Facebook</span>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M20 10C20 4.477 15.523 0 10 0S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <button class="text-gray-500 hover:text-blue-600 transition-colors">
                        <span class="sr-only">LinkedIn</span>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-.003-3.096 1.548 1.548 0 01.003 3.096zm-1.337 9.763H6.34v-8.59H3.667v8.59zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.403C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.299V2.298C19 1.581 18.402 1 17.668 1z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </article>

    <!-- Post Navigation -->
    <div class="mt-12 grid md:grid-cols-2 gap-6">
        <!-- Previous Post -->
        @if($id > 1)
        <a href="{{ route('blog.show', $id - 1) }}" class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex items-center text-blue-600 text-sm font-medium mb-2">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Previous Post
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                Post {{ $id - 1 }}: Judul Artikel Sebelumnya
            </h3>
            <p class="text-gray-600 text-sm mt-1">
                Short description of the previous post...
            </p>
        </a>
        @else
        <div></div>
        @endif
        
        <!-- Next Post -->
        <a href="{{ route('blog.show', $id + 1) }}" class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex items-center justify-end text-blue-600 text-sm font-medium mb-2">
                Next Post
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors text-right">
                Post {{ $id + 1 }}: Judul Artikel Selanjutnya
            </h3>
            <p class="text-gray-600 text-sm mt-1 text-right">
                Short description of the next post...
            </p>
        </a>
    </div>

    <!-- Related Posts -->
    <section class="mt-16">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">Related Posts</h2>
        <div class="grid md:grid-cols-3 gap-6">
            @for($i = 1; $i <= 3; $i++)
            @if($i != $id)
            <article class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                <div class="aspect-video bg-gradient-to-br from-gray-400 to-gray-500 rounded-t-lg relative">
                    <div class="absolute inset-0 flex items-center justify-center text-white">
                        <div class="text-2xl">📖</div>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 mb-2 hover:text-blue-600 transition-colors">
                        <a href="{{ route('blog.show', $i == 1 ? 6 : $i) }}">Related Post {{ $i == 1 ? 6 : $i }}</a>
                    </h3>
                    <p class="text-gray-600 text-sm mb-3">
                        Brief description of related post...
                    </p>
                    <div class="text-xs text-gray-500">
                        5 min read
                    </div>
                </div>
            </article>
            @endif
            @endfor
        </div>
    </section>
</div>
@endsection
```

### Step 3: Update Sidebar untuk Data Dinamis

Update `resources/views/components/layout/sidebar.blade.php` dengan placeholders:

```html
<aside class="space-y-6">
    <!-- About Widget -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-4">About This Blog</h3>
        <p class="text-gray-600 text-sm leading-relaxed mb-4">
            Tutorial Laravel terlengkap dalam bahasa Indonesia. Dari basic hingga advanced, 
            semua materi disusun secara sistematis untuk memudahkan pembelajaran.
        </p>
        <a href="{{ route('about') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
            Learn More →
        </a>
    </div>
    
    <!-- Categories Widget -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-4">Categories</h3>
        <div class="space-y-3">
            <!-- These will be dynamic from database -->
            <a href="#" class="flex items-center justify-between text-gray-600 hover:text-gray-900 transition-colors group">
                <span class="group-hover:text-blue-600">Laravel Framework</span>
                <span class="text-xs bg-gray-100 group-hover:bg-blue-100 text-gray-600 group-hover:text-blue-600 px-2 py-1 rounded-full">12</span>
            </a>
            <a href="#" class="flex items-center justify-between text-gray-600 hover:text-gray-900 transition-colors group">
                <span class="group-hover:text-blue-600">PHP Programming</span>
                <span class="text-xs bg-gray-100 group-hover:bg-blue-100 text-gray-600 group-hover:text-blue-600 px-2 py-1 rounded-full">8</span>
            </a>
            <a href="#" class="flex items-center justify-between text-gray-600 hover:text-gray-900 transition-colors group">
                <span class="group-hover:text-blue-600">Web Development</span>
                <span class="text-xs bg-gray-100 group-hover:bg-blue-100 text-gray-600 group-hover:text-blue-600 px-2 py-1 rounded-full">15</span>
            </a>
            <a href="#" class="flex items-center justify-between text-gray-600 hover:text-gray-900 transition-colors group">
                <span class="group-hover:text-blue-600">Tutorial</span>
                <span class="text-xs bg-gray-100 group-hover:bg-blue-100 text-gray-600 group-hover:text-blue-600 px-2 py-1 rounded-full">25</span>
            </a>
        </div>
    </div>
    
    <!-- Recent Posts Widget -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-4">Recent Posts</h3>
        <div class="space-y-4">
            <!-- These will be dynamic from database -->
            @for($i = 1; $i <= 4; $i++)
            <article class="group">
                <h4 class="font-medium text-gray-900 group-hover:text-blue-600 transition-colors mb-1 text-sm leading-tight">
                    <a href="{{ route('blog.show', $i) }}">
                        Recent Post {{ $i }}: Laravel Tips and Tricks
                    </a>
                </h4>
                <div class="flex items-center text-xs text-gray-500 space-x-2">
                    <span>{{ 10 - $i }} September 2025</span>
                    <span>•</span>
                    <span>{{ rand(50, 200) }} views</span>
                </div>
            </article>
            @endfor
        </div>
    </div>
    
    <!-- Popular Tags -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-4">Popular Tags</h3>
        <div class="flex flex-wrap gap-2">
            <!-- These will be dynamic from database -->
            <a href="#" class="bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700 px-3 py-1 rounded-full text-sm transition-colors">
                laravel
            </a>
            <a href="#" class="bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700 px-3 py-1 rounded-full text-sm transition-colors">
                php
            </a>
            <a href="#" class="bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700 px-3 py-1 rounded-full text-sm transition-colors">
                tutorial
            </a>
            <a href="#" class="bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700 px-3 py-1 rounded-full text-sm transition-colors">
                beginner
            </a>
            <a href="#" class="bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700 px-3 py-1 rounded-full text-sm transition-colors">
                tips
            </a>
            <a href="#" class="bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700 px-3 py-1 rounded-full text-sm transition-colors">
                mvc
            </a>
        </div>
    </div>
    
    <!-- Newsletter Signup -->
    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6">
        <h3 class="font-bold text-gray-900 mb-2">Subscribe Newsletter</h3>
        <p class="text-gray-600 text-sm mb-4">
            Get the latest Laravel tutorials and tips delivered to your inbox.
        </p>
        <form class="space-y-3">
            <input type="email" 
                   placeholder="Your email..." 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                Subscribe
            </button>
        </form>
    </div>
</aside>
```

## 🔄 Testing New Layout

Jalankan server dan test layout baru:

```bash
php artisan serve
npm run dev
```

Kunjungi:
- `http://127.0.0.1:8000/blog` - Homepage dengan design baru
- `http://127.0.0.1:8000/blog/post/1` - Single post dengan template clean

## 📋 Persiapan untuk Database Integration

Dalam layout baru ini, kita sudah menyiapkan placeholder untuk:

1. **Data Dinamis yang akan datang dari Models:**
   - Post titles, content, dates
   - Categories dengan post counts  
   - Recent posts list
   - Popular tags
   - Author information

2. **Route Structure yang akan update:**
   - `/blog` - Homepage dengan posts dari database
   - `/blog/post/{slug}` - Single post dengan slug
   - `/blog/category/{slug}` - Posts by category
   - `/blog/tag/{slug}` - Posts by tag

3. **Template Structure:**
   - Modular components untuk reusability
   - Responsive design untuk semua devices
   - SEO-ready structure dengan proper meta tags

## 🎯 Kesimpulan

Selamat! Layout blog baru telah siap:
- ✅ Design yang lebih professional dan modern
- ✅ Template yang siap untuk data dinamis
- ✅ Structure yang scalable untuk fitur database
- ✅ Placeholder untuk semua konten yang akan datang dari database

Di pelajaran selanjutnya, kita akan membuat database structure dan migrations untuk menyimpan data blog kita.

---

**Selanjutnya:** [Pelajaran 6: Database Structure and Migrations](06-database-migrations.md)

*Ready for database! 💾*