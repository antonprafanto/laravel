# Pelajaran 2: Membuat Routes dan Halaman Baru

Setelah instalasi berhasil, sekarang kita akan belajar cara kerja routing di Laravel dan membuat halaman pertama untuk blog kita.

## 🎯 Tujuan Pembelajaran

Setelah menyelesaikan pelajaran ini, Anda akan:
- ✅ Memahami konsep routing di Laravel
- ✅ Membuat route baru untuk blog
- ✅ Membuat view Blade template
- ✅ Mengerti cara kerja request-response cycle

## 🛣️ Memahami Routing Laravel

Routing adalah proses menentukan bagaimana aplikasi merespon request client ke endpoint tertentu. Di Laravel, semua route web didefinisikan di file `routes/web.php`.

### Melihat Route Default

Buka file `routes/web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
```

Route ini berarti:
- **Method**: GET
- **URL**: `/` (homepage)
- **Action**: Mengembalikan view 'welcome'

## 📄 Membuat Halaman Blog Pertama

### Step 1: Membuat Route untuk Homepage Blog

Edit file `routes/web.php`:

```php
<?php

// Ambil kelas Route dari Laravel
use Illuminate\Support\Facades\Route;

// Ketika pengguna buka website.com/ (halaman utama)
Route::get('/', function () {
    return view('welcome'); // Tampilkan berkas welcome.blade.php
});

// Ketika pengguna buka website.com/blog
Route::get('/blog', function () {
    return view('blog.index'); // Tampilkan berkas blog/index.blade.php
});
```

### Step 2: Membuat Direktori dan View untuk Blog

Pertama, buat direktori untuk view blog:

```bash
# Buat folder blog di dalam folder views
# mkdir = buat direktori (buat folder baru)
mkdir resources/views/blog
```

Kemudian buat file `resources/views/blog/index.blade.php`:

```html
<!DOCTYPE html>
<html lang="id"> <!-- Bahasa website kita = Indonesia -->
<head>
    <meta charset="UTF-8"> <!-- Penyandian untuk karakter Indonesia -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Menyesuaikan di HP -->
    <title>Blog Saya</title> <!-- Judul di tab browser -->
    <style>
        body {
            font-family: system-ui, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            line-height: 1.6;
        }
        .header {
            border-bottom: 2px solid #e5e5e5;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
        }
        .post {
            margin-bottom: 2rem;
            padding: 1.5rem;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
        }
        .post-title {
            color: #2563eb;
            margin: 0 0 0.5rem 0;
        }
        .post-meta {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Blog Laravel Saya</h1>
        <p>Selamat datang di blog pertama saya yang dibuat dengan Laravel!</p>
    </div>

    <main>
        <article class="post">
            <h2 class="post-title">Memulai Perjalanan dengan Laravel</h2>
            <div class="post-meta">Ditulis pada 9 September 2025</div>
            <p>
                Ini adalah post pertama di blog Laravel saya. Laravel adalah framework PHP yang elegant 
                dan powerful untuk membangun aplikasi web modern.
            </p>
            <p>
                Dengan Laravel, saya bisa fokus pada logic bisnis tanpa perlu mengkhawatirkan 
                setup dasar seperti routing, database, dan authentication.
            </p>
        </article>

        <article class="post">
            <h2 class="post-title">Mengapa Memilih Laravel?</h2>
            <div class="post-meta">Ditulis pada 8 September 2025</div>
            <p>
                Laravel menyediakan syntax yang bersih, dokumentasi yang excellent, 
                dan ecosystem yang lengkap untuk development yang cepat dan maintainable.
            </p>
        </article>
    </main>
</body>
</html>
```

### Step 3: Test Route Baru

Jalankan server jika belum:

```bash
php artisan serve
```

Kunjungi `http://127.0.0.1:8000/blog` di browser. Anda akan melihat halaman blog dengan 2 post dummy! 🎉

## 🔄 Menambahkan Route Lainnya

Mari kita tambahkan beberapa route lagi untuk melengkapi blog:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/blog', function () {
    return view('blog.index');
});

// Route untuk single post
Route::get('/blog/post/{id}', function ($id) {
    return view('blog.show', ['id' => $id]);
});

// Route untuk about page
Route::get('/about', function () {
    return view('about');
});

// Route untuk contact
Route::get('/contact', function () {
    return view('contact');
});
```

### Membuat View untuk Single Post

Buat file `resources/views/blog/show.blade.php`:

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post {{ $id }} - Blog Saya</title>
    <style>
        body {
            font-family: system-ui, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            line-height: 1.6;
        }
        .back-link {
            color: #2563eb;
            text-decoration: none;
            margin-bottom: 2rem;
            display: inline-block;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .post-meta {
            color: #666;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <a href="/blog" class="back-link">← Kembali ke Blog</a>
    
    <article>
        <h1>Detail Post #{{ $id }}</h1>
        <div class="post-meta">Ditulis pada 9 September 2025</div>
        
        <p>
            Ini adalah konten detail untuk post dengan ID: {{ $id }}
        </p>
        
        <p>
            Nanti di pelajaran selanjutnya, kita akan mengganti data dummy ini 
            dengan data real dari database menggunakan Eloquent models.
        </p>
        
        <p>
            Untuk saat ini, kita fokus memahami dasar-dasar routing dan view 
            di Laravel terlebih dahulu.
        </p>
    </article>
</body>
</html>
```

### Test Route dengan Parameter

Kunjungi beberapa URL berikut:
- `http://127.0.0.1:8000/blog/post/1`
- `http://127.0.0.1:8000/blog/post/5`
- `http://127.0.0.1:8000/blog/post/100`

Perhatikan bagaimana parameter `{id}` di route berubah sesuai URL!

## 📋 Melihat Semua Route

Laravel menyediakan command untuk melihat semua route yang terdaftar:

```bash
php artisan route:list
```

Output akan menampilkan:
```
GET|HEAD  /                    Closure
GET|HEAD  /blog                Closure
GET|HEAD  /blog/post/{id}      Closure
GET|HEAD  /about               Closure
GET|HEAD  /contact             Closure
```

## 🎨 Blade Templating Basics

Blade adalah template engine bawaan Laravel. Beberapa fitur dasar:

### 1. Echoing Data
```blade
{{ $variabel }}          <!-- Safe output (escaped) -->
{!! $htmlContent !!}     <!-- Raw output (unescaped) -->
```

### 2. PHP Code
```blade
@php
    $nama = 'Laravel';
    $versi = 12;
@endphp

<p>Saya menggunakan {{ $nama }} versi {{ $versi }}</p>
```

### 3. Conditionals
```blade
@if($user)
    <p>Halo {{ $user->name }}!</p>
@else
    <p>Silakan login</p>
@endif
```

### 4. Loops
```blade
@foreach($posts as $post)
    <h3>{{ $post->title }}</h3>
@endforeach
```

## 🔧 Route Methods dan Options

Laravel mendukung berbagai HTTP methods:

```php
Route::get('/blog', $callback);       // GET
Route::post('/blog', $callback);      // POST
Route::put('/blog/{id}', $callback);  // PUT
Route::delete('/blog/{id}', $callback); // DELETE

// Multiple methods
Route::match(['get', 'post'], '/contact', $callback);

// Any method
Route::any('/debug', $callback);
```

### Named Routes

Memberikan nama pada route untuk referensi mudah:

```php
Route::get('/blog', function () {
    return view('blog.index');
})->name('blog.index');

Route::get('/blog/post/{id}', function ($id) {
    return view('blog.show', ['id' => $id]);
})->name('blog.show');
```

Penggunaan di view:
```blade
<a href="{{ route('blog.index') }}">Blog</a>
<a href="{{ route('blog.show', ['id' => 1]) }}">Post 1</a>
```

## ✅ Praktik Terbaik

1. **Gunakan named routes** untuk maintainability
2. **Group related routes** (akan dipelajari nanti)
3. **Gunakan parameter constraints** untuk validasi
4. **Pisahkan logic kompleks** ke Controllers (pelajaran mendatang)

## 🎯 Kesimpulan

Selamat! Anda telah berhasil:
- ✅ Memahami dasar routing Laravel
- ✅ Membuat route dengan dan tanpa parameter  
- ✅ Membuat view Blade template sederhana
- ✅ Mengerti request-response cycle

Di pelajaran selanjutnya, kita akan mempercantik tampilan blog menggunakan Tailwind CSS.

## 💡 Tantangan

Coba buat route dan view untuk:
1. Halaman About (`/about`)
2. Halaman Contact (`/contact`) 
3. Route dengan multiple parameter: `/blog/category/{category}/post/{id}`

---

**Selanjutnya:** [Pelajaran 3: Implementasi Tailwind CSS](03-implementasi-tailwind.md)

*Keep coding! 🚀*