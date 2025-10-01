# Bab 09: Blade Layout & Components 🏗️

[⬅️ Bab 08: View & Blade Dasar](08-view-blade-dasar.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 10: Controller ➡️](10-controller.md)

---

## 🎯 Learning Objectives

Setelah mempelajari bab ini, kamu akan:
- ✅ Memahami konsep layout dan template inheritance
- ✅ Bisa membuat master layout dengan @extends
- ✅ Menguasai @section, @yield, dan @parent
- ✅ Bisa membuat header dan footer reusable
- ✅ Memahami cara include file dengan @include
- ✅ Bisa manage asset (CSS, JS, images)

---

## 🎯 Analogi Sederhana: Layout seperti Template PowerPoint

**Tanpa Layout (Copy-Paste Hell!):**
```
Slide 1: Header + Content + Footer (tulis manual)
Slide 2: Header + Content + Footer (copy-paste)
Slide 3: Header + Content + Footer (copy-paste)

Mau ubah header? Harus ubah SEMUA slide! 😫
```

**Dengan Master Layout:**
```
Master Template:
┌─────────────────┐
│ HEADER (fixed)  │
├─────────────────┤
│ CONTENT (vary)  │ ← Hanya ini yang berubah!
├─────────────────┤
│ FOOTER (fixed)  │
└─────────────────┘

Ubah master → Semua slide berubah! ✨
```

**Layout Blade** = Master template untuk semua halaman!

---

## 📚 Penjelasan: Mengapa Butuh Layout?

### ❌ Tanpa Layout (Repetitif)

Setiap halaman punya kode yang sama:

**home.blade.php:**
```html
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Website Saya</h1>
        <nav>
            <a href="/">Home</a>
            <a href="/about">About</a>
        </nav>
    </header>

    <main>
        <h2>Welcome to Home Page</h2>
    </main>

    <footer>
        <p>&copy; 2024 Website Saya</p>
    </footer>
</body>
</html>
```

**about.blade.php:**
```html
<!DOCTYPE html>
<html>
<head>
    <title>About</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Website Saya</h1>
        <nav>
            <a href="/">Home</a>
            <a href="/about">About</a>
        </nav>
    </header>

    <main>
        <h2>About Us Page</h2>
    </main>

    <footer>
        <p>&copy; 2024 Website Saya</p>
    </footer>
</body>
</html>
```

**Masalah:**
- ❌ Header dan footer di-copy paste ke semua file
- ❌ Mau ubah logo? Harus ubah di semua file
- ❌ Mau tambah menu? Harus ubah di semua file
- ❌ Tidak efisien dan error-prone

---

### ✅ Dengan Layout (DRY - Don't Repeat Yourself)

**layouts/app.blade.php:** (Master)
```html
<!DOCTYPE html>
<html>
<head>
    <title>@yield('title') - Website Saya</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Website Saya</h1>
        <nav>
            <a href="/">Home</a>
            <a href="/about">About</a>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <p>&copy; 2024 Website Saya</p>
    </footer>
</body>
</html>
```

**home.blade.php:**
```blade
@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <h2>Welcome to Home Page</h2>
@endsection
```

**about.blade.php:**
```blade
@extends('layouts.app')

@section('title', 'About')

@section('content')
    <h2>About Us Page</h2>
@endsection
```

**Keuntungan:**
- ✅ Header dan footer cuma di 1 file
- ✅ Ubah logo sekali, semua halaman berubah
- ✅ Code lebih bersih dan maintainable
- ✅ Konsisten di semua halaman

---

## 🏗️ Bagian 1: Membuat Master Layout

### Step 1: Buat Folder layouts

Buat folder: `resources/views/layouts/`

---

### Step 2: Buat File Master Layout

Buat file: `resources/views/layouts/app.blade.php`

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Default Title')</title>

    <!-- CSS -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }

        header {
            background: #333;
            color: white;
            padding: 1rem;
        }

        nav {
            margin-top: 1rem;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-right: 1rem;
            padding: 0.5rem 1rem;
            background: #555;
            border-radius: 5px;
        }

        nav a:hover {
            background: #777;
        }

        main {
            padding: 2rem;
            min-height: calc(100vh - 200px);
        }

        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 1rem;
            margin-top: 2rem;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- HEADER -->
    <header>
        <h1>🚀 Laravel Tutorial</h1>
        <nav>
            <a href="/">Home</a>
            <a href="/about">About</a>
            <a href="/contact">Contact</a>
            <a href="/blog">Blog</a>
        </nav>
    </header>

    <!-- CONTENT -->
    <main>
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer>
        <p>&copy; 2024 Laravel Tutorial. Made with ❤️</p>
    </footer>

    @stack('scripts')
</body>
</html>
```

**Penjelasan:**
- `@yield('title')` = Placeholder untuk title (dari child)
- `@yield('content')` = Placeholder untuk konten (dari child)
- `@stack('styles')` = Placeholder untuk CSS tambahan (optional)
- `@stack('scripts')` = Placeholder untuk JS tambahan (optional)

---

### Step 3: Buat Child View

**Buat file: resources/views/pages/home.blade.php**

```blade
@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <h2>Selamat Datang di Laravel Tutorial! 🎉</h2>
    <p>Ini adalah halaman home yang menggunakan master layout.</p>

    <div style="margin-top: 2rem;">
        <h3>Apa yang akan kamu pelajari?</h3>
        <ul>
            <li>Blade Template Engine</li>
            <li>Eloquent ORM</li>
            <li>Authentication & Authorization</li>
            <li>CRUD Operations</li>
            <li>Dan masih banyak lagi!</li>
        </ul>
    </div>
@endsection
```

**Buat file: resources/views/pages/about.blade.php**

```blade
@extends('layouts.app')

@section('title', 'About Us')

@section('content')
    <h2>Tentang Kami</h2>
    <p>Tutorial Laravel untuk pemula yang ingin belajar framework modern.</p>

    <h3>Visi</h3>
    <p>Membuat Laravel mudah dipahami oleh semua orang.</p>

    <h3>Misi</h3>
    <ul>
        <li>Mengajarkan dengan analogi yang mudah</li>
        <li>Praktek langsung dengan project nyata</li>
        <li>Komunitas yang supportif</li>
    </ul>
@endsection
```

---

### Step 4: Buat Routes

**routes/web.php:**
```php
Route::get('/', function () {
    return view('pages.home');
});

Route::get('/about', function () {
    return view('pages.about');
});
```

---

### Step 5: Test!

1. `http://localhost:8000/` → Lihat home page
2. `http://localhost:8000/about` → Lihat about page

**Coba ubah header di `layouts/app.blade.php`** → Kedua halaman ikut berubah! 🎉

---

## 📦 Bagian 2: @section dengan Content Panjang

### Variasi 1: Inline Section (Pendek)

```blade
@section('title', 'Home')
```

Untuk content yang singkat (1 baris)

---

### Variasi 2: Block Section (Panjang)

```blade
@section('content')
    <h2>Title</h2>
    <p>Paragraph...</p>
    <ul>
        <li>Item 1</li>
        <li>Item 2</li>
    </ul>
@endsection
```

Untuk content yang banyak (multi-line)

---

## 🔗 Bagian 3: @include - Include Partial View

### 🎯 Analogi: Include seperti Import Part di Assembly Line

```
Mobil = Header + Body + Wheels + Engine
         ↑       ↑      ↑       ↑
       (part)  (part) (part)  (part)

Rakit dengan include semua parts!
```

**@include** = Import file view lain ke dalam view

---

### Contoh: Navbar Terpisah

**Step 1: Buat Partial**

**resources/views/partials/navbar.blade.php:**
```html
<nav style="background: #007bff; padding: 1rem;">
    <a href="/" style="color: white; margin-right: 1rem;">Home</a>
    <a href="/about" style="color: white; margin-right: 1rem;">About</a>
    <a href="/blog" style="color: white; margin-right: 1rem;">Blog</a>
    <a href="/contact" style="color: white;">Contact</a>
</nav>
```

---

**Step 2: Include di Layout**

**layouts/app.blade.php:**
```blade
<body>
    @include('partials.navbar')

    <main>
        @yield('content')
    </main>

    <footer>
        <p>&copy; 2024</p>
    </footer>
</body>
```

---

### Include dengan Data

**Pass data ke included view:**

```blade
@include('partials.alert', ['type' => 'success', 'message' => 'Data saved!'])
```

**partials/alert.blade.php:**
```blade
<div style="padding: 1rem; background: {{ $type === 'success' ? '#28a745' : '#dc3545' }}; color: white;">
    {{ $message }}
</div>
```

---

### @includeIf - Include Jika File Ada

```blade
@includeIf('partials.sidebar')
```

Kalau file `sidebar.blade.php` tidak ada, tidak akan error.

---

### @includeWhen - Include dengan Kondisi

```blade
@includeWhen($isAdmin, 'partials.admin-menu')
```

Hanya include jika `$isAdmin` true.

---

## 🎨 Bagian 4: @stack dan @push - CSS/JS Tambahan

### 🎯 Analogi: Stack seperti Tumpukan Piring

```
Layout punya placeholder (kotak kosong):
┌─────────────────┐
│ [STACK: styles] │ ← Kotak kosong
└─────────────────┘

Child view "push" isi ke kotak:
┌─────────────────┐
│ style1.css      │
│ style2.css      │ ← Ditumpuk dari berbagai view
│ style3.css      │
└─────────────────┘
```

---

### Contoh: Push Styles

**Layout:**
```blade
<head>
    <title>@yield('title')</title>

    <!-- Default styles -->
    <link rel="stylesheet" href="/css/app.css">

    <!-- Placeholder untuk styles tambahan -->
    @stack('styles')
</head>
```

**Child View:**
```blade
@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="/css/dashboard.css">
    <style>
        .custom { background: red; }
    </style>
@endpush

@section('content')
    <div class="custom">Dashboard</div>
@endsection
```

**Hasil render:**
```html
<head>
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/css/dashboard.css">
    <style>
        .custom { background: red; }
    </style>
</head>
```

---

### Contoh: Push Scripts

**Layout:**
```blade
<body>
    <main>@yield('content')</main>

    <!-- Default scripts -->
    <script src="/js/app.js"></script>

    <!-- Placeholder untuk scripts tambahan -->
    @stack('scripts')
</body>
```

**Child View:**
```blade
@extends('layouts.app')

@section('content')
    <button id="myBtn">Click Me</button>
@endsection

@push('scripts')
    <script>
        document.getElementById('myBtn').addEventListener('click', function() {
            alert('Button clicked!');
        });
    </script>
@endpush
```

**Berguna untuk:** Page-specific JS/CSS tanpa load di semua halaman!

---

## 🔄 Bagian 5: @parent - Tambah ke Section Parent

### Tanpa @parent (Override)

**Layout:**
```blade
@section('sidebar')
    <li>Default Menu 1</li>
    <li>Default Menu 2</li>
@show
```

**Child View:**
```blade
@extends('layouts.app')

@section('sidebar')
    <li>Custom Menu</li>
@endsection
```

**Hasil:** Hanya "Custom Menu" (menu default hilang)

---

### Dengan @parent (Append)

**Child View:**
```blade
@extends('layouts.app')

@section('sidebar')
    @parent
    <li>Custom Menu</li>
@endsection
```

**Hasil:**
```
- Default Menu 1
- Default Menu 2
- Custom Menu
```

Menu default tetap ada + menu custom ditambahkan!

---

## 📁 Bagian 6: Organisasi File View

### Struktur yang Direkomendasikan

```
resources/views/
├── layouts/
│   ├── app.blade.php           # Master layout
│   ├── guest.blade.php         # Layout untuk guest (belum login)
│   └── admin.blade.php         # Layout untuk admin
│
├── partials/
│   ├── navbar.blade.php        # Navbar component
│   ├── sidebar.blade.php       # Sidebar component
│   ├── footer.blade.php        # Footer component
│   └── alert.blade.php         # Alert component
│
├── pages/
│   ├── home.blade.php          # Homepage
│   ├── about.blade.php         # About page
│   └── contact.blade.php       # Contact page
│
└── posts/
    ├── index.blade.php         # List posts
    ├── show.blade.php          # Single post
    ├── create.blade.php        # Form create
    └── edit.blade.php          # Form edit
```

---

## 💡 Contoh Lengkap: Multiple Layouts

### Layout untuk User

**layouts/app.blade.php:**
```blade
<!DOCTYPE html>
<html>
<head>
    <title>@yield('title') - My Site</title>
</head>
<body>
    @include('partials.navbar')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')
</body>
</html>
```

---

### Layout untuk Admin

**layouts/admin.blade.php:**
```blade
<!DOCTYPE html>
<html>
<head>
    <title>@yield('title') - Admin Panel</title>
    <style>
        .admin-wrapper { display: flex; }
        .sidebar { width: 250px; background: #2c3e50; color: white; min-height: 100vh; }
        .content { flex: 1; padding: 2rem; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        @include('partials.admin-sidebar')

        <div class="content">
            <h1>@yield('page-title')</h1>
            @yield('content')
        </div>
    </div>
</body>
</html>
```

---

### View untuk User

```blade
@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <h2>User Dashboard</h2>
@endsection
```

---

### View untuk Admin

```blade
@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('page-title', 'Dashboard')

@section('content')
    <p>Welcome to admin panel!</p>
@endsection
```

---

## 📝 Latihan

### Latihan 1: Blog Layout

Buat layout untuk blog dengan struktur:
- Header (logo + nav)
- Sidebar (kategori, recent posts)
- Content area
- Footer

---

### Latihan 2: Dashboard dengan Sidebar

Buat dashboard layout dengan:
- Top navbar
- Left sidebar (menu admin)
- Main content
- Breadcrumb

---

### Latihan 3: Multi-Level @include

Buat struktur:
- Layout utama
- Include navbar (navbar punya include logo dan menu)
- Include sidebar
- Include footer (footer punya include social media)

---

## ⚠️ Troubleshooting

### Problem 1: @extends harus di baris pertama

**❌ Salah:**
```blade
<h1>Title</h1>
@extends('layouts.app')
```

**✅ Benar:**
```blade
@extends('layouts.app')

@section('content')
    <h1>Title</h1>
@endsection
```

---

### Problem 2: Lupa @endsection

**Error:** `Undefined directive 'section'`

**Solusi:** Pastikan ada `@endsection` atau `@show`

---

### Problem 3: File tidak ditemukan

**Error:** `View [layouts.app] not found`

**Solusi:**
1. Cek path: `layouts/app.blade.php`
2. Cek spelling
3. Clear cache: `php artisan view:clear`

---

## 📖 Summary

Di bab ini kamu sudah belajar:

- ✅ Layout = Master template untuk semua halaman
- ✅ `@extends('layouts.app')` = Gunakan layout
- ✅ `@yield('name')` = Placeholder di layout
- ✅ `@section` ... `@endsection` = Isi placeholder
- ✅ `@include('partial')` = Import view lain
- ✅ `@stack` dan `@push` = Untuk CSS/JS tambahan
- ✅ `@parent` = Tambah ke section parent (tidak override)
- ✅ Organisasi file view yang rapi

**Code sekarang DRY dan maintainable!** 🎉

---

## 🎯 Next Chapter Preview

Di chapter berikutnya, kita akan belajar:
- ✅ Controller - Pelayan yang koordinasi semua
- ✅ Membuat controller dengan Artisan
- ✅ Menghubungkan route → controller → view
- ✅ Resource controller
- ✅ Mengapa butuh controller?

**Saatnya pakai Controller!** 👔

---

## 🔗 Referensi

- 📖 [Blade Template Inheritance](https://laravel.com/docs/12.x/blade#template-inheritance)
- 📖 [Blade Components](https://laravel.com/docs/12.x/blade#components)
- 📖 [Blade Stacks](https://laravel.com/docs/12.x/blade#stacks)

---

[⬅️ Bab 08: View & Blade Dasar](08-view-blade-dasar.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 10: Controller ➡️](10-controller.md)

---

<div align="center">

**Layout sudah dikuasai! Code lebih DRY!** ✅

**Lanjut ke Controller untuk logic yang lebih terorganisir!** 🚀

</div>