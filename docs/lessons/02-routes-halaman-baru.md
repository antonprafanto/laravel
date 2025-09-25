# Pelajaran 2: Membuat Routes dan Halaman Baru

Setelah instalasi berhasil, sekarang kita akan belajar cara kerja routing di Laravel dan membuat halaman pertama untuk blog kita.

## 🗺️ Analogi: Routing = Sistem GPS di Mall

Bayangkan Anda ke mall besar yang punya 5 lantai. Di pintu masuk ada **papan petunjuk** (routing) yang menunjukkan:

**🏪 Routing Mall:**
- `/` = Lobby utama (homepage)
- `/food-court` = Lantai 3, area makan
- `/cinema` = Lantai 4, bioskop
- `/bookstore` = Lantai 2, toko buku

**💻 Routing Laravel:**
- `/` = Halaman utama website
- `/blog` = Halaman daftar artikel
- `/blog/post/judul-artikel` = Halaman artikel spesifik
- `/about` = Halaman tentang kami

Ketika pengunjung **klik link** (seperti naik lift ke lantai tertentu), Laravel akan **cek routing table** (seperti GPS mall) dan **kirim mereka ke halaman yang tepat** (seperti antar ke toko yang dimaksud).

**Tanpa routing = seperti mall tanpa petunjuk arah = pengunjung tersesat!** 🤯

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

**📄 Analogi View/Blade**: View Template seperti **template surat** yang sudah jadi. Bayangkan Anda punya template surat lamaran kerja - struktur udah ada (header, body, footer), Anda tinggal ganti nama, alamat, dan isi surat sesuai kebutuhan. Blade template juga begitu - struktur HTML sudah ada, tinggal masukkan data dinamis (seperti judul artikel, nama user, dll).

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

### ⚠️ Troubleshooting: Port Collision Error

**Jika halaman blog menampilkan content yang tidak sesuai atau ada error**, kemungkinan ada service lain yang berjalan di port 8000.

**Masalah yang Mungkin Terjadi:**
- Route `/blog` menampilkan halaman yang salah
- Perubahan di `routes/web.php` tidak terlihat
- Browser menampilkan website yang berbeda

**Solusi:**

**1. Check Port yang Digunakan:**
```bash
# Windows
netstat -an | findstr :8000

# Mac/Linux
netstat -an | grep :8000
```

**2. Gunakan Port Berbeda:**
```bash
# Matikan server saat ini (Ctrl+C)
# Jalankan dengan port custom
php artisan serve --port=8001
```

**3. Akses dengan Port Baru:**
```
http://127.0.0.1:8001/blog
```

**4. Alternative - Kill Process di Port 8000:**
```bash
# Windows
netstat -ano | findstr :8000
taskkill /PID <PID_NUMBER> /F

# Mac/Linux
lsof -ti:8000 | xargs kill -9
```

**💡 Tips:** Selalu gunakan port custom (8001, 8002, etc.) jika Anda develop multiple Laravel project atau ada XAMPP/WAMP yang running.

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
})->name('blog.index');

// Route untuk single post
Route::get('/blog/post/{id}', function ($id) {
    return view('blog.show', ['id' => $id]);
})->name('blog.show');

// Route untuk about page
Route::get('/about', function () {
    return view('about');
})->name('about');

// Route untuk contact
Route::get('/contact', function () {
    return view('contact');
})->name('contact');
```

**⚠️ CATATAN:** Route `/about` dan `/contact` di atas akan error jika diakses karena view nya belum dibuat. View untuk halaman ini akan dibuat di section **Challenge Implementation** di akhir lesson ini. Untuk saat ini, fokus ke route `/blog` dan `/blog/post/{id}` terlebih dahulu.

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
    <a href="{{ route('blog.index') }}" class="back-link">← Kembali ke Blog</a>
    
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

## 🏷️ Named Routes: Mengapa Penting?

Perhatikan bahwa route kita menggunakan `->name()` di akhir. Ini adalah **Named Routes** - salah satu fitur paling powerful di Laravel!

### 🤔 Analogi: Named Routes = Kontak di HP

Bayangkan Anda punya nomor HP `081234567890`. Anda bisa:

**❌ Metode Manual (Hard-coded URLs):**
```html
<!-- Ribet dan rawan error -->
<a href="/blog">Blog</a>
<a href="/blog/post/5">Post Detail</a>
<a href="/about">About</a>
```

**✅ Metode Smart (Named Routes):**
```html
<!-- Elegant dan maintainable -->
<a href="{{ route('blog.index') }}">Blog</a>
<a href="{{ route('blog.show', 5) }}">Post Detail</a>
<a href="{{ route('about') }}">About</a>
```

Sama seperti di HP - lebih mudah klik "Mama" daripada ingat nomornya!

### 🔍 Cara Kerja Named Routes

**1. Definisi Route dengan Name:**
```php
Route::get('/blog', function () {
    return view('blog.index');
})->name('blog.index');  // ← Nama route

Route::get('/blog/post/{id}', function ($id) {
    return view('blog.show', ['id' => $id]);
})->name('blog.show');   // ← Nama route
```

**2. Penggunaan di View:**
```blade
<!-- Generate URL dengan nama -->
<a href="{{ route('blog.index') }}">Ke Blog</a>

<!-- Generate URL dengan parameter -->
<a href="{{ route('blog.show', 5) }}">Post #5</a>

<!-- Check active route -->
@if(Route::currentRouteName() == 'blog.index')
    <span>Sedang di halaman Blog</span>
@endif
```

**3. Penggunaan di Controller (nanti):**
```php
// Redirect dengan nama
return redirect()->route('blog.index');

// Generate URL
$url = route('blog.show', ['id' => 10]);
```

### ✨ Keuntungan Named Routes

**1. Maintainability**
```php
// Kalau URL berubah dari /blog ke /articles
Route::get('/articles', function () {  // ← URL berubah
    return view('blog.index');
})->name('blog.index');                // ← Name tetap sama

// Semua link {{ route('blog.index') }} otomatis update! 🎉
```

**2. IDE Support**
```blade
{{ route('blog.index') }}  <!-- ✅ IDE bisa autocomplete -->
{{ url('/blog') }}         <!-- ❌ IDE tidak tahu -->
```

**3. Parameter Handling**
```blade
<!-- Named route dengan parameter -->
{{ route('blog.show', ['id' => 10]) }}
{{ route('blog.show', 10) }}  <!-- Shorthand -->

<!-- Manual URL (error-prone) -->
{{ "/blog/post/" . 10 }}      <!-- ❌ Ribet -->
```

### 🔧 Melihat Named Routes

Untuk melihat route dengan nama:

```bash
php artisan route:list --name=blog
```

Output:
```
POST     blog.index  /blog        Closure
GET|HEAD blog.show   /blog/post/{id}  Closure
```

### 📝 Best Practices Named Routes

**1. Naming Convention:**
```php
// ✅ Gunakan dot notation yang konsisten
Route::get('/blog', ...)->name('blog.index');
Route::get('/blog/post/{id}', ...)->name('blog.show');
Route::get('/blog/create', ...)->name('blog.create');
Route::post('/blog', ...)->name('blog.store');

// ❌ Jangan inconsistent
Route::get('/blog', ...)->name('blogIndex');
Route::get('/blog/post/{id}', ...)->name('show_blog_post');
```

**2. Logical Grouping:**
```php
// Resource routes (akan dipelajari later)
Route::resource('blog', BlogController::class)->names([
    'index' => 'blog.index',
    'show' => 'blog.show',
    'create' => 'blog.create',
    // dst...
]);
```

**3. Testing Routes:**
```blade
<!-- Update link di blog/show.blade.php -->
<a href="{{ route('blog.index') }}" class="back-link">← Kembali ke Blog</a>
```

Mari kita update link di view kita untuk menggunakan named routes!

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

## 🧪 Pengujian & Validasi Routing

Setelah mempelajari konsep routing dan view, mari kita uji pemahaman Anda dengan berbagai test untuk memastikan semua konsep telah dipahami dengan baik.

### 🛣️ Test 1: Verifikasi Routes Registration

**🎯 Tujuan:** Memastikan semua routes yang dibuat sudah terdaftar dengan benar di Laravel.

**Test Case 1.1 - Routes List:**
```bash
# Tampilkan semua routes yang terdaftar
php artisan route:list

# Filter routes tertentu
php artisan route:list --name=blog
php artisan route:list --path=blog

# Tampilkan routes dalam format compact
php artisan route:list --compact
```

**✅ Expected Results:**
- Route homepage (`/`) terdaftar
- Route blog (`/blog`) terdaftar dengan nama `blog.index`
- Route single post (`/blog/post/{id}`) terdaftar dengan nama `blog.show`
- Route about dan contact (jika dibuat) terdaftar

**Test Case 1.2 - Route Parameters:**
```bash
# Test route dengan parameter menggunakan Tinker
php artisan tinker
```

```php
// Test route generation dengan parameter
route('blog.show', ['id' => 1]);        // Harus: http://localhost/blog/post/1
route('blog.show', ['id' => 100]);      // Harus: http://localhost/blog/post/100

// Test URL generation tanpa parameter
route('blog.index');                    // Harus: http://localhost/blog
url('/');                              // Harus: http://localhost

// Exit tinker
exit;
```

**✅ Expected Results:**
- Route helper menghasilkan URL yang benar
- Parameter diteruskan dengan benar ke URL
- Named routes berfungsi tanpa error

### 🌐 Test 2: Browser Testing Routes

**🎯 Tujuan:** Memastikan semua routes dapat diakses melalui browser dan menampilkan content yang benar.

**Test Case 2.1 - Static Routes:**

Jalankan server development:
```bash
php artisan serve
```

Test di browser:

1. **Homepage:** `http://127.0.0.1:8000/`
   - ✅ Harus tampil halaman welcome Laravel atau redirect ke blog
   - ✅ Status code 200 (berhasil)
   - ✅ Tidak ada error 404 atau 500

2. **Blog Index:** `http://127.0.0.1:8000/blog`
   - ✅ Harus tampil halaman blog index
   - ✅ Content sesuai dengan yang ada di `blog/index.blade.php`
   - ✅ Header dan layout tampil dengan benar

**Test Case 2.2 - Dynamic Routes dengan Parameter:**

Test berbagai parameter:

1. **Post ID 1:** `http://127.0.0.1:8000/blog/post/1`
   - ✅ Harus tampil halaman detail post
   - ✅ Parameter ID (1) tampil di halaman
   - ✅ Link "Kembali ke Blog" berfungsi

2. **Post ID Besar:** `http://127.0.0.1:8000/blog/post/9999`
   - ✅ Harus tampil halaman dengan ID 9999
   - ✅ Tidak error meskipun ID tidak ada di database (saat ini masih dummy)

3. **Parameter String:** `http://127.0.0.1:8000/blog/post/abc`
   - ✅ Harus tetap berfungsi (karena belum ada constraint)
   - ✅ Parameter "abc" tampil di halaman

**✅ Expected Results:**
- Semua routes dapat diakses tanpa error
- Parameter diteruskan dan ditampilkan dengan benar
- Navigation antar halaman berfungsi

### 🎨 Test 3: Blade Template Testing

**🎯 Tujuan:** Memastikan Blade template bekerja dengan benar dan menerima data dari routes.

**Test Case 3.1 - Template Structure:**
```bash
# Cek struktur file view
ls resources/views/
ls resources/views/blog/

# Cek isi file view
cat resources/views/blog/index.blade.php | head -10
cat resources/views/blog/show.blade.php | head -10
```

**Test Case 3.2 - Blade Variables:**

Edit sementara route untuk test variabel:
```php
// Test di routes/web.php - tambahkan route test
Route::get('/test-blade', function () {
    $data = [
        'title' => 'Test Blade Template',
        'message' => 'Hello from Laravel!',
        'items' => ['Item 1', 'Item 2', 'Item 3']
    ];

    return view('test-blade', $data);
});
```

Buat file `resources/views/test-blade.blade.php`:
```php
<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>{{ $message }}</p>

    <ul>
        @foreach($items as $item)
            <li>{{ $item }}</li>
        @endforeach
    </ul>

    @if(count($items) > 2)
        <p>Banyak item tersedia!</p>
    @else
        <p>Item terbatas.</p>
    @endif
</body>
</html>
```

Test: `http://127.0.0.1:8000/test-blade`

**✅ Expected Results:**
- Variabel `$title` dan `$message` tampil dengan benar
- Loop `@foreach` menampilkan semua items
- Conditional `@if` menampilkan pesan yang benar

### 🔗 Test 4: Named Routes & URL Generation

**🎯 Tujuan:** Memastikan named routes dan URL generation berfungsi dengan benar.

**Test Case 4.1 - Named Routes Helper:**
```bash
php artisan tinker
```

```php
// Test named routes
route('blog.index');                    // Harus return URL blog
route('blog.show', ['id' => 5]);       // Harus return URL dengan parameter

// Test apakah route ada
Route::has('blog.index');              // Harus return true
Route::has('blog.show');               // Harus return true
Route::has('nonexistent');             // Harus return false

// Test current route (jika dipanggil dalam request)
// app('router')->current(); // Bisa dicoba nanti saat ada request
```

**Test Case 4.2 - Link Generation dalam View:**

Update file `resources/views/blog/index.blade.php` untuk test link:
```php
<!-- Tambahkan di bagian bawah file -->
<div style="margin-top: 2rem; padding: 1rem; border-top: 1px solid #ccc;">
    <h3>Test Navigation Links:</h3>
    <ul>
        <li><a href="{{ route('blog.index') }}">Blog Home</a></li>
        <li><a href="{{ route('blog.show', ['id' => 1]) }}">Post 1</a></li>
        <li><a href="{{ route('blog.show', ['id' => 2]) }}">Post 2</a></li>
        <li><a href="{{ url('/') }}">Homepage</a></li>
    </ul>
</div>
```

Test: `http://127.0.0.1:8000/blog`

**✅ Expected Results:**
- Semua link ter-generate dengan URL yang benar
- Klik link tidak menghasilkan error 404
- Navigation berfungsi dengan lancar

### 📊 Test 5: Route Parameters & Constraints

**🎯 Tujuan:** Memahami cara kerja route parameters dan menambahkan validasi.

**Test Case 5.1 - Basic Parameters:**

Buat route test dengan multiple parameters:
```php
// Tambahkan route test di routes/web.php
Route::get('/test/category/{category}/post/{id}', function ($category, $id) {
    return "Category: $category, Post ID: $id";
})->name('test.category.post');
```

Test URL:
- `http://127.0.0.1:8000/test/category/laravel/post/5`
- `http://127.0.0.1:8000/test/category/php/post/100`

**Test Case 5.2 - Route Constraints:**

Tambahkan constraints untuk validasi parameter:
```php
// Route dengan constraint ID harus numerik
Route::get('/blog/post/{id}', function ($id) {
    return view('blog.show', ['id' => $id]);
})->where('id', '[0-9]+')->name('blog.show');

// Route dengan constraint category harus alphabetic
Route::get('/blog/category/{category}', function ($category) {
    return "Category: $category";
})->where('category', '[a-zA-Z]+')->name('blog.category');
```

Test constraint:
- ✅ Valid: `http://127.0.0.1:8000/blog/post/123`
- ❌ Invalid: `http://127.0.0.1:8000/blog/post/abc` (harus 404)
- ✅ Valid: `http://127.0.0.1:8000/blog/category/laravel`
- ❌ Invalid: `http://127.0.0.1:8000/blog/category/123` (harus 404)

**✅ Expected Results:**
- Parameter constraints berfungsi dengan benar
- Invalid parameter menghasilkan 404 error
- Valid parameter diterima dan diproses

### 🎯 Test 6: Challenge Implementation

**🎯 Tujuan:** Implementasi tantangan untuk menguji pemahaman komprehensif.

**Challenge Task:** Implementasi 3 route baru dengan requirement spesifik.

**Task 6.1 - About Page:**
```php
// 1. Buat route about
Route::get('/about', function () {
    $data = [
        'title' => 'Tentang Kami',
        'description' => 'Blog Laravel Indonesia untuk pembelajaran web development',
        'features' => ['Tutorial Laravel', 'Tips & Tricks', 'Best Practices']
    ];
    return view('about', $data);
})->name('about');
```

Buat view `resources/views/about.blade.php`:
```php
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: system-ui; max-width: 800px; margin: 0 auto; padding: 2rem; }
        .feature { background: #f0f0f0; padding: 1rem; margin: 0.5rem 0; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>{{ $description }}</p>

    <h3>Fitur Utama:</h3>
    @foreach($features as $feature)
        <div class="feature">{{ $feature }}</div>
    @endforeach

    <p><a href="{{ route('blog.index') }}">← Kembali ke Blog</a></p>
</body>
</html>
```

**Task 6.2 - Contact Page:**
```php
// 2. Buat route contact dengan multiple parameter optional
Route::get('/contact/{type?}', function ($type = 'general') {
    $contacts = [
        'general' => 'info@laravelblog.com',
        'support' => 'support@laravelblog.com',
        'business' => 'business@laravelblog.com'
    ];

    return view('contact', [
        'type' => $type,
        'email' => $contacts[$type] ?? $contacts['general'],
        'allTypes' => $contacts
    ]);
})->name('contact');
```

**Task 6.3 - Complex Route:**
```php
// 3. Buat route dengan multiple parameter dan constraint
Route::get('/blog/category/{category}/post/{id}/comment/{comment?}', function ($category, $id, $comment = null) {
    return view('blog.detail', compact('category', 'id', 'comment'));
})->where(['id' => '[0-9]+', 'comment' => '[0-9]+'])->name('blog.detail');
```

**✅ Success Criteria:**
- Route about dapat diakses dan tampilkan data dengan benar
- Route contact dengan parameter optional berfungsi
- Route complex dengan multiple parameter dan constraint bekerja
- Semua view ter-render tanpa error
- Navigation antar halaman lancar

## 📋 Checklist Kelulusan Routing

Tandai ✅ untuk setiap test yang berhasil:

### 🛣️ Route Registration
- [ ] `php artisan route:list` menampilkan semua routes
- [ ] Named routes terdaftar dengan benar
- [ ] Route dengan parameter terdaftar
- [ ] Route helpers (route(), url()) berfungsi

### 🌐 Browser Access
- [ ] Homepage dapat diakses tanpa error
- [ ] Blog index page tampil dengan benar
- [ ] Single post page menerima parameter dengan benar
- [ ] Navigation links berfungsi antar halaman

### 🎨 Blade Templates
- [ ] View files terbaca tanpa error
- [ ] Blade syntax ({{ }}, @foreach, @if) berfungsi
- [ ] Data dari route diterima di view dengan benar
- [ ] Template rendering berjalan lancar

### 🔗 Named Routes & URLs
- [ ] Named routes dapat dipanggil dengan route() helper
- [ ] URL generation dengan parameter benar
- [ ] Link generation di view berfungsi
- [ ] Current route detection (bonus)

### 📊 Parameters & Constraints
- [ ] Route parameters diterima dengan benar
- [ ] Multiple parameters berfungsi
- [ ] Route constraints memvalidasi parameter
- [ ] Invalid parameters menghasilkan 404

### 🎯 Challenge Implementation
- [ ] About page berhasil dibuat dan berfungsi
- [ ] Contact page dengan optional parameter
- [ ] Complex route dengan multiple parameter
- [ ] Semua views ter-render dengan data yang benar

## 🚨 Troubleshooting Routes

### ❌ Route Issues
- **404 Not Found** → Cek penulisan URL dan route definition
- **Route not defined** → Pastikan route ada di `routes/web.php`
- **Parameter not received** → Cek nama parameter di route dan function

### ❌ View Issues
- **View not found** → Cek path file view dan penulisan nama
- **Variable undefined** → Pastikan data dikirim dari route ke view
- **Blade syntax error** → Cek penulisan {{ }} dan @directive

### ❌ Named Routes Issues
- **Route does not exist** → Cek nama route dengan `route:list`
- **Missing parameters** → Sertakan parameter saat panggil named route
- **URL generation error** → Pastikan parameter sesuai dengan route definition

## 🎯 Kesimpulan

Selamat! Anda telah berhasil:
- ✅ Memahami dasar routing Laravel
- ✅ Membuat route dengan dan tanpa parameter
- ✅ Membuat view Blade template sederhana
- ✅ Mengerti request-response cycle
- ✅ **[BARU] Melakukan pengujian komprehensif routing dan view**

Dengan pengujian yang telah dilakukan, Anda memastikan bahwa konsep routing, parameter handling, dan Blade templating telah dipahami dengan baik. Di pelajaran selanjutnya, kita akan mempercantik tampilan blog menggunakan Tailwind CSS.

## 💡 Tantangan

Coba buat route dan view untuk:
1. Halaman About (`/about`) ✅ Sudah dibuat dalam test
2. Halaman Contact (`/contact`) ✅ Sudah dibuat dalam test
3. Route dengan multiple parameter: `/blog/category/{category}/post/{id}` ✅ Sudah dibuat dalam test

---

**Selanjutnya:** [Pelajaran 3: Implementasi Tailwind CSS](03-implementasi-tailwind.md)

*Keep coding! 🚀*