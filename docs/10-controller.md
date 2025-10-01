# Bab 10: Controller 👔

[⬅️ Bab 09: Blade Layout](09-blade-layout.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 11: Artisan Helper ➡️](11-artisan-helper.md)

---

## 🎯 Learning Objectives

Setelah mempelajari bab ini, kamu akan:
- ✅ Memahami apa itu Controller dan mengapa penting
- ✅ Bisa membuat controller secara manual
- ✅ Bisa membuat controller dengan Artisan
- ✅ Mengerti cara menghubungkan Route → Controller → View
- ✅ Memahami Resource Controller
- ✅ Bisa passing data dari controller ke view

---

## 🎯 Analogi Sederhana: Controller seperti Pelayan Restoran

**Tanpa Pelayan (Chaos!):**
```
Customer langsung ke dapur:
"Saya mau Rendang!"
Chef bingung: "Siapa ini? Mau apa?"
→ Tidak terorganisir! 😫
```

**Dengan Pelayan (Terorganisir!):**
```
1. Customer pesan ke PELAYAN: "Rendang 1 porsi"
2. PELAYAN catat dan ke dapur
3. PELAYAN ambil Rendang dari chef
4. PELAYAN sajikan ke customer
→ Smooth dan profesional! ✨
```

**Controller** = Pelayan yang koordinasi antara request, model, dan view!

---

## 📚 Penjelasan: Apa itu Controller?

**Controller** = File PHP yang handle logic aplikasi

**Lokasi:** `app/Http/Controllers/`

**Tugas:**
- ✅ Terima request dari route
- ✅ Proses logic (validasi, kalkulasi, dll)
- ✅ Ambil data dari Model (optional)
- ✅ Kirim data ke View
- ✅ Return response

**Mengapa butuh Controller?**

### ❌ Tanpa Controller (Routes Berantakan)

**routes/web.php:**
```php
Route::get('/posts', function () {
    // Logic untuk ambil posts
    $posts = DB::table('posts')->get();

    // Logic untuk filter
    if (request('category')) {
        $posts = $posts->where('category', request('category'));
    }

    // Logic untuk sort
    $posts = $posts->sortBy('created_at');

    return view('posts.index', compact('posts'));
});

Route::get('/posts/{id}', function ($id) {
    // Logic untuk ambil single post
    $post = DB::table('posts')->find($id);

    if (!$post) {
        abort(404);
    }

    // Logic untuk ambil related posts
    $relatedPosts = DB::table('posts')
        ->where('category', $post->category)
        ->where('id', '!=', $id)
        ->limit(5)
        ->get();

    return view('posts.show', compact('post', 'relatedPosts'));
});

// Dan puluhan route lainnya dengan logic panjang...
```

**Masalah:**
- ❌ File routes jadi sangat panjang
- ❌ Logic campur dengan routing
- ❌ Susah maintenance
- ❌ Tidak bisa reuse logic
- ❌ Testing susah

---

### ✅ Dengan Controller (Clean & Organized)

**routes/web.php:**
```php
use App\Http\Controllers\PostController;

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);
```

**app/Http/Controllers/PostController.php:**
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        // Logic untuk list posts
        $posts = Post::all();
        return view('posts.index', compact('posts'));
    }

    public function show($id)
    {
        // Logic untuk single post
        $post = Post::findOrFail($id);
        return view('posts.show', compact('post'));
    }
}
```

**Keuntungan:**
- ✅ Route file bersih
- ✅ Logic terpisah rapi
- ✅ Mudah maintenance
- ✅ Bisa reuse logic
- ✅ Testing lebih mudah

---

## 🏗️ Bagian 1: Membuat Controller Manual

### Step 1: Buat File Controller

Buat file: `app/Http/Controllers/PageController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    // Method untuk halaman home
    public function home()
    {
        return view('pages.home');
    }

    // Method untuk halaman about
    public function about()
    {
        $data = [
            'title' => 'Tentang Kami',
            'description' => 'Website tutorial Laravel terbaik di Indonesia',
            'year' => 2024
        ];

        return view('pages.about', $data);
    }

    // Method untuk halaman contact
    public function contact()
    {
        return view('pages.contact');
    }
}
```

**Penjelasan:**
- `namespace App\Http\Controllers;` = Namespace Laravel
- `class PageController extends Controller` = Inherit dari base Controller
- `public function` = Method yang bisa dipanggil dari route

---

### Step 2: Update Routes

**routes/web.php:**
```php
use App\Http\Controllers\PageController;

Route::get('/', [PageController::class, 'home']);
Route::get('/about', [PageController::class, 'about']);
Route::get('/contact', [PageController::class, 'contact']);
```

**Sintaks baru Laravel:**
```php
[PageController::class, 'home']
```

**Artinya:** Panggil class `PageController`, method `home`

---

### Step 3: Test!

1. `http://localhost:8000/` → Method `home()` dipanggil
2. `http://localhost:8000/about` → Method `about()` dipanggil
3. `http://localhost:8000/contact` → Method `contact()` dipanggil

**Berhasil!** Controller pertamamu jalan! 🎉

---

## 🤖 Bagian 2: Membuat Controller dengan Artisan

### Cara Mudah: Pakai Artisan Command

**Buka terminal:**
```bash
php artisan make:controller PostController
```

**Output:**
```
INFO  Controller [app/Http/Controllers/PostController.php] created successfully.
```

**File otomatis dibuat di:** `app/Http/Controllers/PostController.php`

**Isi file:**
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller
{
    //
}
```

**Tinggal tambah method!**

---

### Contoh: Controller untuk Blog

**Buat controller:**
```bash
php artisan make:controller BlogController
```

**Edit file `BlogController.php`:**
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BlogController extends Controller
{
    // Menampilkan daftar blog posts
    public function index()
    {
        $posts = [
            ['id' => 1, 'title' => 'Belajar Laravel', 'author' => 'Budi'],
            ['id' => 2, 'title' => 'Tutorial PHP', 'author' => 'Ani'],
            ['id' => 3, 'title' => 'Tips Coding', 'author' => 'Citra'],
        ];

        return view('blog.index', compact('posts'));
    }

    // Menampilkan single post
    public function show($id)
    {
        $post = [
            'id' => $id,
            'title' => 'Belajar Laravel',
            'author' => 'Budi',
            'date' => '2024-01-15',
            'content' => 'Laravel adalah framework PHP yang powerful...'
        ];

        return view('blog.show', compact('post'));
    }

    // Form untuk create post
    public function create()
    {
        return view('blog.create');
    }

    // Simpan post baru (nanti akan pakai database)
    public function store(Request $request)
    {
        // Nanti akan belajar validation dan save ke DB
        return redirect('/blog')->with('success', 'Post created!');
    }
}
```

---

**Routes untuk Blog:**
```php
use App\Http\Controllers\BlogController;

Route::get('/blog', [BlogController::class, 'index']);
Route::get('/blog/create', [BlogController::class, 'create']);
Route::post('/blog', [BlogController::class, 'store']);
Route::get('/blog/{id}', [BlogController::class, 'show']);
```

---

## 🔄 Bagian 3: Flow Request → Controller → View

Mari kita trace alurnya:

**Skenario:** User buka `/blog/5`

```
1. Browser request: GET /blog/5
   ↓
2. Laravel cek routes/web.php
   ↓
3. Ketemu: Route::get('/blog/{id}', [BlogController::class, 'show'])
   ↓
4. Laravel panggil BlogController
   ↓
5. Method show($id) dijalankan dengan $id = 5
   ↓
6. Method ambil data post dengan id 5
   ↓
7. Method return view('blog.show', compact('post'))
   ↓
8. Laravel render view blog/show.blade.php
   ↓
9. HTML dikirim ke browser
   ↓
10. User lihat halaman! 🎉
```

---

## 📦 Bagian 4: Resource Controller

### 🎯 Analogi: Resource Controller seperti Paket Lengkap

**Manual Controller:**
```
Beli satuan:
- Beli piring
- Beli sendok
- Beli gelas
- Beli pisau
→ Ribet! 😫
```

**Resource Controller:**
```
Beli paket lengkap:
📦 Paket Makan Lengkap
- Piring ✅
- Sendok ✅
- Gelas ✅
- Pisau ✅
→ Praktis! ✨
```

**Resource Controller** = Controller dengan 7 method CRUD standar!

---

### Membuat Resource Controller

```bash
php artisan make:controller ProductController --resource
```

**File dibuat dengan 7 method otomatis:**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Tampilkan daftar products
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Tampilkan form tambah product
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Simpan product baru ke database
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Tampilkan detail product
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Tampilkan form edit product
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Update product di database
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Hapus product dari database
    }
}
```

---

### 7 Method Resource Controller

| Method | Route | URL | Fungsi |
|--------|-------|-----|--------|
| `index()` | GET | `/products` | List semua products |
| `create()` | GET | `/products/create` | Form tambah product |
| `store()` | POST | `/products` | Simpan product baru |
| `show($id)` | GET | `/products/{id}` | Detail product |
| `edit($id)` | GET | `/products/{id}/edit` | Form edit product |
| `update($id)` | PUT/PATCH | `/products/{id}` | Update product |
| `destroy($id)` | DELETE | `/products/{id}` | Hapus product |

---

### Route untuk Resource Controller

**Cara Manual (Ribet!):**
```php
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/create', [ProductController::class, 'create']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/{id}/edit', [ProductController::class, 'edit']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);
```

**Cara Resource (Praktis!):**
```php
Route::resource('products', ProductController::class);
```

**1 baris = 7 routes!** Sangat efisien! ✨

---

### Lihat Routes yang Dibuat

```bash
php artisan route:list --name=products
```

**Output:**
```
GET       /products                products.index
GET       /products/create         products.create
POST      /products                products.store
GET       /products/{product}      products.show
GET       /products/{product}/edit products.edit
PUT       /products/{product}      products.update
DELETE    /products/{product}      products.destroy
```

**Perfect untuk CRUD!** 🎉

---

## 💾 Bagian 5: Passing Data ke View

### Cara 1: Compact (Recommended)

```php
public function show($id)
{
    $product = [
        'id' => $id,
        'name' => 'Laptop',
        'price' => 15000000
    ];

    return view('products.show', compact('product'));
}
```

---

### Cara 2: Array

```php
public function show($id)
{
    return view('products.show', [
        'product' => [
            'id' => $id,
            'name' => 'Laptop',
            'price' => 15000000
        ]
    ]);
}
```

---

### Cara 3: With Method

```php
public function show($id)
{
    $product = ['id' => $id, 'name' => 'Laptop'];

    return view('products.show')->with('product', $product);
}
```

---

### Multiple Data

```php
public function index()
{
    $title = 'Daftar Produk';
    $products = [
        ['name' => 'Laptop', 'price' => 15000000],
        ['name' => 'Mouse', 'price' => 150000],
        ['name' => 'Keyboard', 'price' => 500000],
    ];
    $total = count($products);

    return view('products.index', compact('title', 'products', 'total'));
}
```

---

## 💡 Contoh Lengkap: Product Controller

**Buat controller:**
```bash
php artisan make:controller ProductController --resource
```

**Edit ProductController.php:**
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Dummy data (nanti akan pakai database)
    private $products = [
        ['id' => 1, 'name' => 'Laptop ASUS', 'price' => 8000000, 'stock' => 10],
        ['id' => 2, 'name' => 'Mouse Logitech', 'price' => 150000, 'stock' => 50],
        ['id' => 3, 'name' => 'Keyboard Mechanical', 'price' => 800000, 'stock' => 20],
    ];

    public function index()
    {
        $products = $this->products;
        $title = 'Daftar Produk';

        return view('products.index', compact('products', 'title'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        // Nanti akan belajar validation dan database
        return redirect('/products')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function show($id)
    {
        // Cari product by id
        $product = collect($this->products)->firstWhere('id', $id);

        if (!$product) {
            abort(404, 'Produk tidak ditemukan');
        }

        return view('products.show', compact('product'));
    }

    public function edit($id)
    {
        $product = collect($this->products)->firstWhere('id', $id);

        if (!$product) {
            abort(404);
        }

        return view('products.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        // Nanti akan implement update logic
        return redirect('/products')->with('success', 'Produk berhasil diupdate!');
    }

    public function destroy($id)
    {
        // Nanti akan implement delete logic
        return redirect('/products')->with('success', 'Produk berhasil dihapus!');
    }
}
```

---

**Routes:**
```php
use App\Http\Controllers\ProductController;

Route::resource('products', ProductController::class);
```

---

**View: products/index.blade.php:**
```blade
@extends('layouts.app')

@section('title', $title)

@section('content')
    <h2>{{ $title }}</h2>

    <a href="/products/create">+ Tambah Produk</a>

    <table border="1" style="margin-top: 1rem; width: 100%;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product['id'] }}</td>
                <td>{{ $product['name'] }}</td>
                <td>Rp {{ number_format($product['price'], 0, ',', '.') }}</td>
                <td>{{ $product['stock'] }}</td>
                <td>
                    <a href="/products/{{ $product['id'] }}">Lihat</a>
                    <a href="/products/{{ $product['id'] }}/edit">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
```

---

## 📝 Latihan

### Latihan 1: Student Controller

Buat `StudentController` dengan method:
- `index()` - Daftar mahasiswa
- `show($id)` - Detail mahasiswa
- `create()` - Form tambah mahasiswa

Data dummy:
```php
$students = [
    ['id' => 1, 'name' => 'Budi', 'major' => 'Informatika'],
    ['id' => 2, 'name' => 'Ani', 'major' => 'Sistem Informasi'],
];
```

---

### Latihan 2: About Controller dengan Data

Buat `AboutController` yang passing data:
- Company name
- Founder
- Year established
- Vision & Mission (array)

---

### Latihan 3: Resource Controller

Buat `BookController` sebagai resource controller dan setup route-nya.

---

## ⚠️ Troubleshooting

### Problem 1: Class not found

**Error:** `Class "App\Http\Controllers\PostController" not found`

**Solusi:**
1. Cek namespace di controller: `namespace App\Http\Controllers;`
2. Cek use statement di route: `use App\Http\Controllers\PostController;`
3. Clear cache: `composer dump-autoload`

---

### Problem 2: Method not found

**Error:** `Method App\Http\Controllers\PostController::index does not exist`

**Solusi:**
1. Cek spelling method di route dan controller
2. Pastikan method public: `public function index()`

---

### Problem 3: Too few arguments

**Error:** `Too few arguments to function show()`

**Solusi:** Pastikan parameter di route dan controller sama:
```php
// Route
Route::get('/post/{id}', [PostController::class, 'show']);

// Controller
public function show($id) { ... }
```

---

## 📖 Summary

Di bab ini kamu sudah belajar:

- ✅ Controller = Koordinator antara Route, Model, dan View
- ✅ Controller ada di `app/Http/Controllers/`
- ✅ Buat controller manual atau dengan `php artisan make:controller`
- ✅ Route syntax: `[ControllerName::class, 'method']`
- ✅ Resource Controller = 7 method CRUD standar
- ✅ `Route::resource()` = Shortcut untuk 7 routes
- ✅ Passing data dengan `compact()`, array, atau `with()`

**Controller membuat code lebih terorganisir!** 👔

---

## 🎯 Next Chapter Preview

Di chapter berikutnya, kita akan belajar:
- ✅ Artisan command yang berguna
- ✅ `php artisan make:*` untuk generate files
- ✅ `php artisan route:list`, `cache:clear`, dll
- ✅ Cara baca help untuk setiap command
- ✅ Familiarisasi dengan terminal

**Artisan adalah asisten pribadi Laravel!** 🤖

---

## 🔗 Referensi

- 📖 [Laravel Controllers](https://laravel.com/docs/12.x/controllers)
- 📖 [Resource Controllers](https://laravel.com/docs/12.x/controllers#resource-controllers)
- 🎥 [Laracasts - Controllers](https://laracasts.com)

---

[⬅️ Bab 09: Blade Layout](09-blade-layout.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 11: Artisan Helper ➡️](11-artisan-helper.md)

---

<div align="center">

**Controller sudah dikuasai! Logic terpisah rapi!** ✅

**Lanjut ke Artisan untuk workflow yang lebih cepat!** 🤖

</div>