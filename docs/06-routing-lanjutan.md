# Bab 06: Routing Lanjutan 🛣️

[⬅️ Bab 05: Hello World](05-hello-world.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 07: Pengenalan MVC ➡️](07-mvc-pengenalan.md)

---

## 🎯 Learning Objectives

Setelah mempelajari bab ini, kamu akan:
- ✅ Memahami HTTP methods (GET, POST, PUT, DELETE)
- ✅ Bisa membuat route dengan nama (named routes)
- ✅ Mengerti route groups untuk organisasi yang lebih baik
- ✅ Bisa membuat parameter optional
- ✅ Memahami route constraints (validasi parameter)
- ✅ Bisa melihat semua route dengan `php artisan route:list`

---

## 🎯 Analogi Sederhana: HTTP Methods seperti Cara Berkomunikasi di Restoran

Bayangkan kamu di restoran:

| HTTP Method | Analogi | Fungsi | Contoh |
|-------------|---------|--------|--------|
| **GET** | "Lihat menu" | Mengambil/melihat data | Lihat daftar makanan |
| **POST** | "Pesan makanan baru" | Menambah data baru | Pesan 1 Nasi Goreng |
| **PUT/PATCH** | "Ubah pesanan" | Update data | Ganti jadi Mie Goreng |
| **DELETE** | "Batalkan pesanan" | Hapus data | Batalkan pesanan |

**Di Laravel:**
- **GET** = Menampilkan halaman, ambil data
- **POST** = Submit form, tambah data baru
- **PUT/PATCH** = Update data yang sudah ada
- **DELETE** = Hapus data

---

## 📚 Bagian 1: HTTP Methods

### 1. Route GET (Sudah Kita Pelajari)

```php
// Menampilkan halaman
Route::get('/products', function () {
    return "Daftar produk";
});
```

**Kapan pakai GET?**
- Menampilkan halaman
- Mengambil data
- Tidak mengubah data di server

---

### 2. Route POST (Untuk Form Submission)

```php
// Menerima data dari form
Route::post('/products', function () {
    return "Produk baru berhasil ditambahkan!";
});
```

**Kapan pakai POST?**
- Submit form (login, register, tambah data)
- Menambah data baru ke database
- Mengubah state di server

**Catatan:** POST tidak bisa ditest langsung dari browser address bar. Nanti kita akan pakai form HTML.

---

### 3. Route PUT/PATCH (Untuk Update)

```php
// Update data
Route::put('/products/{id}', function ($id) {
    return "Produk dengan ID $id berhasil diupdate!";
});

// Atau pakai PATCH (partial update)
Route::patch('/products/{id}', function ($id) {
    return "Produk dengan ID $id diupdate sebagian";
});
```

**Kapan pakai PUT/PATCH?**
- Update data yang sudah ada
- PUT = Update seluruh data
- PATCH = Update sebagian data

---

### 4. Route DELETE (Untuk Hapus)

```php
// Hapus data
Route::delete('/products/{id}', function ($id) {
    return "Produk dengan ID $id berhasil dihapus!";
});
```

**Kapan pakai DELETE?**
- Menghapus data dari database
- Soft delete atau hard delete

---

### 5. Route ANY (Menerima Semua Method)

```php
// Menerima GET, POST, PUT, DELETE, dll
Route::any('/test', function () {
    return "Route ini menerima semua HTTP methods";
});
```

**Jarang dipakai!** Biasanya kita spesifik method-nya.

---

### 6. Route MATCH (Beberapa Method Tertentu)

```php
// Hanya menerima GET dan POST
Route::match(['get', 'post'], '/form', function () {
    return "Route ini menerima GET dan POST saja";
});
```

---

## 📋 Bagian 2: Named Routes (Kasih Nama Route)

### 🎯 Analogi: Named Routes seperti Kontak di HP

**Tanpa nama (ribet!):**
```
"Telepon 0812-3456-7890"
"Kirim WA ke 0812-3456-7890"
"Video call 0812-3456-7890"
→ Harus hafal nomornya! 😫
```

**Dengan nama (praktis!):**
```
"Telepon Budi"
"Kirim WA ke Budi"
"Video call Budi"
→ Tinggal sebut nama! 😊
```

**Named routes** membuat kode lebih mudah maintenance!

---

### Cara Membuat Named Route

```php
// Tanpa nama
Route::get('/products', function () {
    return "Daftar produk";
});

// Dengan nama
Route::get('/products', function () {
    return "Daftar produk";
})->name('products.index');
```

**Konvensi penamaan:**
```php
Route::get('/products', ...)->name('products.index');         // List
Route::get('/products/create', ...)->name('products.create'); // Form tambah
Route::post('/products', ...)->name('products.store');        // Simpan
Route::get('/products/{id}', ...)->name('products.show');     // Detail
Route::get('/products/{id}/edit', ...)->name('products.edit'); // Form edit
Route::put('/products/{id}', ...)->name('products.update');   // Update
Route::delete('/products/{id}', ...)->name('products.destroy'); // Hapus
```

**Ini mengikuti pattern RESTful!** Nanti kita akan pakai ini untuk CRUD.

---

### Keuntungan Named Routes

**1. Mudah generate URL:**
```php
// Di view atau controller
$url = route('products.index');
// Output: http://localhost:8000/products
```

**2. Mudah redirect:**
```php
return redirect()->route('products.index');
```

**3. Kalau URL berubah, tidak perlu ubah di banyak tempat:**
```php
// Awalnya
Route::get('/produk', ...)->name('products.index');

// Diubah jadi
Route::get('/daftar-produk', ...)->name('products.index');

// Semua link masih jalan karena pakai name, bukan URL!
```

---

### Contoh Lengkap Named Routes

```php
// Buat route dengan nama
Route::get('/about', function () {
    return "Halaman About";
})->name('about');

Route::get('/contact', function () {
    return "Halaman Contact";
})->name('contact');

Route::get('/services', function () {
    return "Halaman Services";
})->name('services');

// Test: Lihat route dengan nama
Route::get('/test-routing', function () {
    return "
        <h2>Named Routes</h2>
        <ul>
            <li><a href='" . route('about') . "'>About</a></li>
            <li><a href='" . route('contact') . "'>Contact</a></li>
            <li><a href='" . route('services') . "'>Services</a></li>
        </ul>
    ";
});
```

**Test:** Buka `http://localhost:8000/test-routing`

---

## 📦 Bagian 3: Route Groups

### 🎯 Analogi: Route Groups seperti Folder di Komputer

**Tanpa folder (berantakan!):**
```
Desktop/
- foto-liburan-bali-1.jpg
- foto-liburan-bali-2.jpg
- foto-keluarga-1.jpg
- foto-keluarga-2.jpg
- dokumen-kuliah-1.pdf
- dokumen-kuliah-2.pdf
→ Susah cari! 😫
```

**Dengan folder (rapi!):**
```
Desktop/
├── Liburan Bali/
│   ├── foto-1.jpg
│   └── foto-2.jpg
├── Foto Keluarga/
│   ├── foto-1.jpg
│   └── foto-2.jpg
└── Dokumen Kuliah/
    ├── dokumen-1.pdf
    └── dokumen-2.pdf
→ Rapi dan mudah cari! ✅
```

**Route groups** membuat route lebih terorganisir!

---

### Route Group dengan Prefix

```php
// Tanpa group (repetitif)
Route::get('/admin/dashboard', function () {
    return "Admin Dashboard";
});

Route::get('/admin/users', function () {
    return "Admin Users";
});

Route::get('/admin/products', function () {
    return "Admin Products";
});

// Dengan group (lebih rapi!)
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return "Admin Dashboard";
    });

    Route::get('/users', function () {
        return "Admin Users";
    });

    Route::get('/products', function () {
        return "Admin Products";
    });
});
```

**URL yang dihasilkan:**
- `/admin/dashboard`
- `/admin/users`
- `/admin/products`

**Lebih rapi dan DRY (Don't Repeat Yourself)!**

---

### Route Group dengan Name Prefix

```php
Route::name('admin.')->group(function () {
    Route::get('/admin/dashboard', function () {
        return "Admin Dashboard";
    })->name('dashboard'); // Nama: admin.dashboard

    Route::get('/admin/users', function () {
        return "Admin Users";
    })->name('users'); // Nama: admin.users
});
```

**Generate URL:**
```php
route('admin.dashboard'); // /admin/dashboard
route('admin.users');     // /admin/users
```

---

### Route Group Gabungan (Prefix + Name)

```php
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return "Admin Dashboard";
    })->name('dashboard'); // URL: /admin/dashboard, Name: admin.dashboard

    Route::get('/users', function () {
        return "Admin Users";
    })->name('users'); // URL: /admin/users, Name: admin.users

    Route::get('/products', function () {
        return "Admin Products";
    })->name('products'); // URL: /admin/products, Name: admin.products
});
```

**Sangat efisien!** ✨

---

## 🔧 Bagian 4: Parameter Optional

### Parameter Wajib vs Optional

```php
// Parameter WAJIB - harus ada
Route::get('/user/{name}', function ($name) {
    return "User: $name";
});
// /user → ERROR! Parameter wajib
// /user/john → OK!

// Parameter OPTIONAL - boleh ada, boleh tidak
Route::get('/user/{name?}', function ($name = 'Guest') {
    return "User: $name";
});
// /user → OK! Akan tampilkan "User: Guest"
// /user/john → OK! Akan tampilkan "User: John"
```

**Tanda `?` = optional**

---

### Contoh Real-World: Pagination

```php
Route::get('/products/{page?}', function ($page = 1) {
    return "Menampilkan produk halaman: $page";
});
```

**Test:**
- `/products` → "Menampilkan produk halaman: 1"
- `/products/2` → "Menampilkan produk halaman: 2"
- `/products/5` → "Menampilkan produk halaman: 5"

---

### Contoh: Greeting dengan Bahasa Optional

```php
Route::get('/hello/{name}/{lang?}', function ($name, $lang = 'id') {
    $greetings = [
        'id' => 'Halo',
        'en' => 'Hello',
        'es' => 'Hola',
        'fr' => 'Bonjour'
    ];

    $greeting = $greetings[$lang] ?? $greetings['id'];
    return "$greeting, $name!";
});
```

**Test:**
- `/hello/Budi` → "Halo, Budi!"
- `/hello/John/en` → "Hello, John!"
- `/hello/Juan/es` → "Hola, Juan!"
- `/hello/Pierre/fr` → "Bonjour, Pierre!"

---

## 🔒 Bagian 5: Route Constraints (Validasi Parameter)

### 🎯 Analogi: Constraints seperti Filter di Aplikasi

**Tanpa filter:**
```
Cari produk: [bisa ketik apapun]
"abc123", "###", "!@#$"
→ Hasil acak! 😵
```

**Dengan filter:**
```
Cari produk: [hanya angka]
"abc" → DITOLAK
"123" → DITERIMA ✅
```

**Constraints** memastikan parameter sesuai format!

---

### Where Constraint (Regex)

```php
// Parameter harus angka
Route::get('/user/{id}', function ($id) {
    return "User ID: $id";
})->where('id', '[0-9]+');
```

**Test:**
- `/user/123` → OK! ✅
- `/user/abc` → 404 Error ❌

---

### Where dengan Multiple Parameters

```php
Route::get('/post/{id}/{slug}', function ($id, $slug) {
    return "Post ID: $id, Slug: $slug";
})->where([
    'id' => '[0-9]+',      // id harus angka
    'slug' => '[a-z-]+'    // slug harus huruf kecil dan dash
]);
```

**Test:**
- `/post/1/hello-world` → OK! ✅
- `/post/abc/hello-world` → 404 ❌ (id bukan angka)
- `/post/1/Hello-World` → 404 ❌ (slug ada huruf besar)

---

### Helper Methods untuk Constraint

```php
// whereNumber - hanya angka
Route::get('/user/{id}', function ($id) {
    return "User ID: $id";
})->whereNumber('id');

// whereAlpha - hanya huruf
Route::get('/category/{name}', function ($name) {
    return "Category: $name";
})->whereAlpha('name');

// whereAlphaNumeric - huruf dan angka
Route::get('/product/{code}', function ($code) {
    return "Product Code: $code";
})->whereAlphaNumeric('code');

// whereUuid - format UUID
Route::get('/order/{uuid}', function ($uuid) {
    return "Order UUID: $uuid";
})->whereUuid('uuid');
```

---

## 🔍 Bagian 6: Melihat Semua Route

### Command: php artisan route:list

```bash
php artisan route:list
```

**Output:**
```
  GET|HEAD  /                    .....
  GET|HEAD  /halo                .....
  GET|HEAD  /products            products.index
  POST      /products            products.store
  GET|HEAD  /products/{id}       products.show
  PUT       /products/{id}       products.update
  DELETE    /products/{id}       products.destroy
```

**Berguna untuk:**
- Melihat semua route yang ada
- Debug route yang tidak jalan
- Cek nama route
- Lihat method yang diterima

---

### Filter Route List

```bash
# Cari route yang mengandung "product"
php artisan route:list --path=product

# Cari route dengan method GET
php artisan route:list --method=GET

# Cari route dengan nama tertentu
php artisan route:list --name=products
```

---

## 💡 Contoh Praktis: Route untuk Blog

Mari kita buat route lengkap untuk blog:

```php
<?php

use Illuminate\Support\Facades\Route;

// Homepage
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Blog routes dengan group
Route::prefix('blog')->name('blog.')->group(function () {

    // Daftar artikel
    Route::get('/', function () {
        return "Daftar artikel blog";
    })->name('index');

    // Detail artikel
    Route::get('/{slug}', function ($slug) {
        return "Detail artikel: $slug";
    })->where('slug', '[a-z0-9-]+')->name('show');

    // Kategori
    Route::get('/category/{category}', function ($category) {
        return "Artikel kategori: $category";
    })->whereAlpha('category')->name('category');

    // Tag
    Route::get('/tag/{tag}', function ($tag) {
        return "Artikel dengan tag: $tag";
    })->whereAlphaNumeric('tag')->name('tag');

    // Archive by year and month
    Route::get('/archive/{year}/{month?}', function ($year, $month = null) {
        if ($month) {
            return "Artikel bulan $month tahun $year";
        }
        return "Artikel tahun $year";
    })->where([
        'year' => '[0-9]{4}',
        'month' => '[0-9]{2}'
    ])->name('archive');
});

// Static pages
Route::get('/about', function () {
    return "About Us";
})->name('about');

Route::get('/contact', function () {
    return "Contact Us";
})->name('contact');
```

**Test URLs:**
- `/blog` → Daftar artikel
- `/blog/hello-world` → Detail artikel
- `/blog/category/teknologi` → Kategori teknologi
- `/blog/tag/laravel` → Tag laravel
- `/blog/archive/2024` → Artikel tahun 2024
- `/blog/archive/2024/12` → Artikel Desember 2024

---

## 📝 Latihan Lengkap

### Latihan 1: Route untuk E-Commerce

Buat route untuk e-commerce dengan struktur:
- `/products` - Daftar produk
- `/products/{id}` - Detail produk (id harus angka)
- `/products/category/{category}` - Filter by category
- `/cart` - Keranjang belanja
- `/checkout` - Halaman checkout

**Hint:**
```php
Route::prefix('products')->name('products.')->group(function () {
    // Isi di sini
});
```

---

### Latihan 2: Route untuk Dashboard Admin

Buat route untuk admin dengan struktur:
- `/admin/dashboard`
- `/admin/users`
- `/admin/products`
- `/admin/orders`

Gunakan route group dengan prefix dan name prefix!

---

### Latihan 3: Route API Sederhana

Buat route API untuk aplikasi todo:
- `GET /api/todos` - List todos
- `POST /api/todos` - Create todo
- `GET /api/todos/{id}` - Show todo
- `PUT /api/todos/{id}` - Update todo
- `DELETE /api/todos/{id}` - Delete todo

**Hint:** Gunakan route group dengan prefix `api`

---

## ⚠️ Troubleshooting

### Problem 1: Route Tidak Ditemukan (404)

**Solusi:**
```bash
# Clear route cache
php artisan route:clear

# Cek apakah route ada
php artisan route:list
```

---

### Problem 2: Constraint Tidak Bekerja

**Penyebab:** Regex salah atau typo

**Solusi:** Test regex di [regex101.com](https://regex101.com)

---

### Problem 3: Named Route Tidak Ada

**Error:** `Route [xxx] not defined`

**Solusi:**
1. Cek spelling nama route
2. Clear cache: `php artisan route:clear`
3. Verify: `php artisan route:list --name=xxx`

---

## 📖 Summary

Di bab ini kamu sudah belajar:

- ✅ HTTP Methods: GET (lihat), POST (tambah), PUT/PATCH (update), DELETE (hapus)
- ✅ Named Routes: Kasih nama route untuk maintenance yang mudah
- ✅ Route Groups: Organisir route dengan prefix dan name prefix
- ✅ Parameter Optional: Parameter dengan `?` dan default value
- ✅ Route Constraints: Validasi parameter dengan regex atau helper
- ✅ `php artisan route:list`: Lihat semua route

**Routing sekarang lebih powerful!** 🚀

---

## 🎯 Next Chapter Preview

Di chapter berikutnya, kita akan belajar:
- ✅ Konsep MVC dengan analogi Restoran (detail!)
- ✅ Diagram MVC yang jelas
- ✅ Flow request-response
- ✅ Mengapa pakai MVC?
- ✅ **Belum coding** - fokus pahami konsep dulu!

**Saatnya paham arsitektur Laravel!** 🏗️

---

## 🔗 Referensi

- 📖 [Laravel Routing - HTTP Methods](https://laravel.com/docs/12.x/routing#basic-routing)
- 📖 [Named Routes](https://laravel.com/docs/12.x/routing#named-routes)
- 📖 [Route Groups](https://laravel.com/docs/12.x/routing#route-groups)
- 📖 [Route Parameters](https://laravel.com/docs/12.x/routing#route-parameters)
- 📖 [Route Constraints](https://laravel.com/docs/12.x/routing#parameters-regular-expression-constraints)

---

[⬅️ Bab 05: Hello World](05-hello-world.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 07: Pengenalan MVC ➡️](07-mvc-pengenalan.md)

---

<div align="center">

**Routing sudah advanced! Sekarang saatnya pahami MVC!** 🏗️

</div>