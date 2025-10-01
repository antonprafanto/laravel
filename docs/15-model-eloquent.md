# Bab 15: Model & Eloquent Dasar 🗣️

[⬅️ Bab 14: Seeder & Factory](14-seeder-factory.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 16: Eloquent Lanjutan ➡️](16-eloquent-lanjutan.md)

---

## 🎯 Learning Objectives

Setelah mempelajari bab ini, kamu akan:
- ✅ Memahami apa itu Model dan Eloquent ORM
- ✅ Bisa membuat Model dengan Artisan
- ✅ Paham naming convention Model dan Table
- ✅ Bisa melakukan CRUD operations (Create, Read, Update, Delete)
- ✅ Paham perbedaan Query Builder vs Eloquent
- ✅ Bisa praktik langsung CRUD di Tinker
- ✅ Memahami mass assignment dan $fillable

---

## 🎯 Analogi Sederhana: Model sebagai Juru Bicara

### Analogi: Model = Penerjemah / Juru Bicara

Bayangkan kamu orang Indonesia yang mau ngobrol dengan database (orang yang cuma ngerti bahasa SQL). Kamu butuh **penerjemah** yang bisa translate bahasa manusia jadi SQL.

```
👤 Kamu (Developer)
   ↓ Ngomong: "Cariin semua postingan dong!"
   ↓
🗣️ MODEL (Penerjemah)
   ↓ Translate jadi: "SELECT * FROM posts"
   ↓
🗄️ DATABASE (SQL)
   ↓ Return: Data posts
   ↓
🗣️ MODEL (Penerjemah)
   ↓ Translate jadi: Collection of Post objects
   ↓
👤 Kamu (Developer)
   ↓ Terima: $posts (mudah dipahami)
```

**Tanpa Model (Manual SQL):**
```php
// 😫 Ribet! Nulis SQL manual
$posts = DB::select('SELECT * FROM posts WHERE is_published = ?', [true]);
```

**Dengan Model (Eloquent):**
```php
// 😍 Gampang! Seperti ngomong biasa
$posts = Post::where('is_published', true)->get();
```

---

## 📚 Penjelasan: Apa itu Eloquent ORM?

### Apa itu ORM?

**ORM** = Object-Relational Mapping

**Konsep:**
- **Object** = PHP Class (Model)
- **Relational** = Database Table
- **Mapping** = Hubungan antara Object dan Table

**Analogi:** Seperti **Google Translate** antara bahasa PHP dan bahasa SQL.

---

### Apa itu Eloquent?

**Eloquent** = ORM bawaan Laravel yang super powerful dan mudah dipakai.

**Fungsi:**
- 🗣️ Bicara ke database dengan PHP object (bukan SQL)
- 🔄 Otomatis mapping table ke class
- ✨ CRUD operations jadi super simple
- 🔗 Relationship antar tabel (nanti di Bab 20)
- 🎯 Query yang readable dan maintainable

---

### Keuntungan Eloquent

| Tanpa Eloquent (Raw SQL) | Dengan Eloquent |
|--------------------------|-----------------|
| `SELECT * FROM posts WHERE id = 1` | `Post::find(1)` |
| `INSERT INTO posts (title, body) VALUES (?, ?)` | `Post::create([...])` |
| `UPDATE posts SET title = ? WHERE id = ?` | `$post->update([...])` |
| `DELETE FROM posts WHERE id = ?` | `$post->delete()` |

**Eloquent = SQL yang "bisa dibaca seperti bahasa Inggris"!** 🗣️

---

## 🔧 Bagian 1: Membuat Model

### Cara 1: Buat Model Saja

```bash
php artisan make:model Post
```

**Output:**
```
INFO  Model [app/Models/Post.php] created successfully.
```

---

### Cara 2: Model + Migration (Recommended!)

```bash
php artisan make:model Post -m
```

**Output:**
```
INFO  Model [app/Models/Post.php] created successfully.
INFO  Migration [database/migrations/2025_01_15_create_posts_table.php] created successfully.
```

**Flag `-m`** = Buat migration sekalian (efisien!)

---

### Cara 3: Model + Migration + Controller + Seeder + Factory (All-in-One!)

```bash
php artisan make:model Post -a
```

**Flag `-a` (all)** akan buat:
- ✅ Model
- ✅ Migration
- ✅ Controller (resource)
- ✅ Seeder
- ✅ Factory
- ✅ Policy
- ✅ Form Requests

**Pakai ini kalau mau bikin CRUD lengkap!**

---

### Flag yang Berguna

```bash
# Model + Migration
php artisan make:model Post -m

# Model + Migration + Controller
php artisan make:model Post -mc

# Model + Migration + Controller (resource) + Seeder + Factory
php artisan make:model Post -mcfs

# Model + Semua (all)
php artisan make:model Post -a
```

---

## 📋 Bagian 2: Struktur File Model

### Lokasi Model

**Path:** `app/Models/Post.php`

**Struktur default:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    // 🗣️ Di sini kita bisa custom behavior Model
}
```

**Penjelasan:**
- `namespace App\Models;` → Namespace untuk organize class
- `use HasFactory;` → Trait untuk Factory support (generate data dummy)
- `extends Model` → Inherit semua fitur Eloquent

---

### Naming Convention (PENTING!)

Laravel punya **aturan penamaan** yang otomatis:

| Model Name | Table Name (Auto) | Penjelasan |
|------------|-------------------|------------|
| `Post` | `posts` | Lowercase + plural |
| `Category` | `categories` | Lowercase + plural (y → ies) |
| `User` | `users` | Lowercase + plural |
| `Product` | `products` | Lowercase + plural |
| `OrderItem` | `order_items` | Snake_case + plural |

**Aturan:**
1. Model: **Singular** (Post, Category, User)
2. Table: **Plural** (posts, categories, users)
3. Table: **snake_case** (order_items, user_profiles)

---

### Custom Table Name (Jika Tidak Sesuai Convention)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    // 🔧 Custom table name (jika tidak sesuai convention)
    protected $table = 'my_posts'; // Table: my_posts

    // 🔧 Custom primary key (default: 'id')
    protected $primaryKey = 'post_id';

    // 🔧 Non-incrementing primary key
    public $incrementing = false;

    // 🔧 Primary key bukan integer
    protected $keyType = 'string';

    // 🔧 Disable timestamps (created_at, updated_at)
    public $timestamps = false;
}
```

**Tapi lebih baik ikuti convention biar tidak ribet!** 🎯

---

## 💡 Bagian 3: CRUD dengan Eloquent

### Setup: Contoh Migration & Model

**Migration:** `database/migrations/xxxx_create_posts_table.php`
```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('slug')->unique();
    $table->text('body');
    $table->boolean('is_published')->default(false);
    $table->timestamps(); // created_at, updated_at
});
```

**Model:** `app/Models/Post.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    // 🔐 Mass assignment protection
    protected $fillable = [
        'title',
        'slug',
        'body',
        'is_published',
    ];

    // Atau pakai $guarded (kebalikan dari $fillable)
    // protected $guarded = ['id']; // Semua boleh kecuali 'id'
}
```

**Jalankan migration:**
```bash
php artisan migrate
```

---

## 🔵 C = CREATE (Membuat Data Baru)

### Cara 1: Create dengan `new` dan `save()`

```php
use App\Models\Post;

// 🌱 Buat instance baru
$post = new Post;
$post->title = 'Tutorial Laravel untuk Pemula';
$post->slug = 'tutorial-laravel-untuk-pemula';
$post->body = 'Laravel adalah framework PHP yang powerful dan mudah dipelajari.';
$post->is_published = true;

// 💾 Simpan ke database
$post->save();

echo "Post berhasil dibuat dengan ID: " . $post->id;
```

**Penjelasan:**
- `new Post` → Buat object Post baru (belum masuk database)
- `$post->title = ...` → Set properties
- `$post->save()` → INSERT ke database
- `$post->id` → ID otomatis ter-generate setelah save

---

### Cara 2: Create dengan `create()` (Mass Assignment)

```php
use App\Models\Post;

// 🌱 Create dan save sekaligus (mass assignment)
$post = Post::create([
    'title' => 'Tips Belajar Laravel',
    'slug' => 'tips-belajar-laravel',
    'body' => 'Belajar Laravel itu mudah jika kamu sudah paham OOP.',
    'is_published' => false,
]);

echo "Post berhasil dibuat dengan ID: " . $post->id;
```

**Penjelasan:**
- `Post::create([...])` → Buat dan save dalam 1 langkah
- **Harus set `$fillable` atau `$guarded` di Model!** (security)

---

### Cara 3: firstOrCreate() - Buat Jika Belum Ada

```php
// 🔍 Cari post dengan slug ini, jika tidak ada → buat baru
$post = Post::firstOrCreate(
    ['slug' => 'hello-world'], // 🔎 Cari berdasarkan ini
    [
        'title' => 'Hello World',
        'body' => 'This is my first post!',
        'is_published' => true,
    ] // 🌱 Data tambahan jika harus buat baru
);
```

**Gunanya:** Hindari duplicate data!

---

### Cara 4: updateOrCreate() - Update atau Buat

```php
// 🔄 Update jika ada, buat baru jika belum ada
$post = Post::updateOrCreate(
    ['slug' => 'hello-world'], // 🔎 Cari berdasarkan ini
    [
        'title' => 'Hello World (Updated)',
        'body' => 'Updated content!',
        'is_published' => true,
    ] // 🔄 Data untuk update/create
);
```

**Gunanya:** Upsert operation (update or insert)!

---

### ⚠️ Mass Assignment Protection

**Problem:** Jika tidak set `$fillable`, akan error!

```php
// ❌ Error: Add [title] to fillable property
Post::create(['title' => 'Test']);
```

**Solusi 1: Set `$fillable`** (Whitelist - recommended)
```php
class Post extends Model
{
    // ✅ Kolom yang boleh di-mass assign
    protected $fillable = ['title', 'slug', 'body', 'is_published'];
}
```

**Solusi 2: Set `$guarded`** (Blacklist)
```php
class Post extends Model
{
    // ✅ Kolom yang TIDAK boleh di-mass assign (sisanya boleh)
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
```

**Solusi 3: Unguard (⚠️ Tidak recommended - security risk!)**
```php
protected $guarded = []; // Semua boleh (dangerous!)
```

**Best Practice:** Pakai `$fillable` untuk security! 🔐

---

## 🔵 R = READ (Membaca Data)

### 1. Get All Data

```php
use App\Models\Post;

// 📚 Ambil semua data posts
$posts = Post::all();

// Loop dan tampilkan
foreach ($posts as $post) {
    echo $post->title . "<br>";
}
```

**Output:** Collection of Post objects

---

### 2. Find by ID

```php
// 🔍 Cari post dengan ID = 1
$post = Post::find(1);

if ($post) {
    echo $post->title;
} else {
    echo "Post tidak ditemukan";
}
```

**Penjelasan:**
- `find(1)` → Cari berdasarkan primary key (id)
- Return: Post object atau `null` jika tidak ada

---

### 3. Find or Fail (Auto 404)

```php
// 🔍 Cari atau throw 404 exception
$post = Post::findOrFail(1);

echo $post->title;
```

**Gunanya:** Di controller, jika post tidak ada → otomatis redirect 404!

---

### 4. Get First Record

```php
// 📖 Ambil record pertama
$post = Post::first();

echo $post->title;
```

---

### 5. Where Condition (Simple)

```php
// 🔍 Cari semua post yang published
$posts = Post::where('is_published', true)->get();

foreach ($posts as $post) {
    echo $post->title . "<br>";
}
```

**Penjelasan:**
- `where('column', 'value')` → Filter
- `get()` → Execute query dan return Collection

---

### 6. Where dengan Operator

```php
// 🔍 Cari post dengan ID > 5
$posts = Post::where('id', '>', 5)->get();

// 🔍 Cari post dengan title yang mengandung "Laravel"
$posts = Post::where('title', 'like', '%Laravel%')->get();
```

**Operator:** `=`, `!=`, `>`, `<`, `>=`, `<=`, `like`, dll

---

### 7. Multiple Where (AND)

```php
// 🔍 Cari post yang published DAN title mengandung "Laravel"
$posts = Post::where('is_published', true)
             ->where('title', 'like', '%Laravel%')
             ->get();
```

**Chaining `where()`** = AND condition

---

### 8. Count, Max, Min, Sum

```php
// 📊 Hitung jumlah posts
$count = Post::count();
echo "Total posts: " . $count;

// 📊 ID terbesar
$maxId = Post::max('id');

// 📊 ID terkecil
$minId = Post::min('id');
```

---

## 🔵 U = UPDATE (Mengubah Data)

### Cara 1: Find, Ubah, Save

```php
// 🔍 Cari post dengan ID = 1
$post = Post::find(1);

// ✏️ Ubah properties
$post->title = 'Tutorial Laravel (Updated)';
$post->body = 'Content yang sudah diupdate.';
$post->is_published = true;

// 💾 Save perubahan ke database
$post->save();

echo "Post berhasil diupdate!";
```

**Penjelasan:**
- `find()` → Cari data
- Ubah properties
- `save()` → UPDATE ke database

---

### Cara 2: Update dengan `update()` (Mass Assignment)

```php
// 🔍 Cari post
$post = Post::find(1);

// ✏️ Update multiple columns sekaligus
$post->update([
    'title' => 'Tutorial Laravel (Updated)',
    'is_published' => true,
]);

echo "Post berhasil diupdate!";
```

**Lebih ringkas untuk update banyak kolom!**

---

### Cara 3: Update Without Retrieving (Bulk Update)

```php
// ✏️ Update semua post yang published
Post::where('is_published', true)
    ->update(['is_published' => false]);

echo "Semua post yang published berhasil di-unpublish!";
```

**Gunanya:** Update banyak records sekaligus tanpa fetch dulu!

---

### Cara 4: Increment & Decrement

```php
// Misal ada kolom 'views' di tabel posts

// ➕ Tambah 1
$post = Post::find(1);
$post->increment('views');

// ➕ Tambah 5
$post->increment('views', 5);

// ➖ Kurang 1
$post->decrement('views');

// ➖ Kurang 3
$post->decrement('views', 3);
```

**Gunanya:** Counter views, likes, stock, dll!

---

## 🔵 D = DELETE (Menghapus Data)

### Cara 1: Find dan Delete

```php
// 🔍 Cari post dengan ID = 1
$post = Post::find(1);

// 🗑️ Hapus dari database
$post->delete();

echo "Post berhasil dihapus!";
```

---

### Cara 2: Delete by ID Langsung

```php
// 🗑️ Hapus post dengan ID = 1 (tanpa fetch dulu)
Post::destroy(1);

// 🗑️ Hapus multiple IDs
Post::destroy([1, 2, 3]);

// 🗑️ Atau dengan variadic
Post::destroy(1, 2, 3);
```

**Lebih efisien jika tidak butuh object-nya!**

---

### Cara 3: Delete dengan Where (Bulk Delete)

```php
// 🗑️ Hapus semua post yang tidak published
Post::where('is_published', false)->delete();

echo "Semua draft posts berhasil dihapus!";
```

**Gunanya:** Hapus banyak records sekaligus!

---

### ⚠️ Soft Delete (Nanti di Bab 16)

Soft delete = **Tidak benar-benar hapus**, tapi tandai sebagai "deleted".

Seperti **Recycle Bin** di Windows! 🗑️

---

## 🔄 Bagian 4: Query Builder vs Eloquent

### Apa itu Query Builder?

**Query Builder** = Cara query database dengan method chaining (tanpa Model).

**Pakai:** `DB::table('nama_tabel')`

---

### Perbandingan Query Builder vs Eloquent

#### 1. Get All Data

**Query Builder:**
```php
use Illuminate\Support\Facades\DB;

$posts = DB::table('posts')->get();

foreach ($posts as $post) {
    echo $post->title; // stdClass object
}
```

**Eloquent:**
```php
use App\Models\Post;

$posts = Post::all();

foreach ($posts as $post) {
    echo $post->title; // Post Model object
}
```

**Perbedaan:**
- Query Builder → Return `stdClass` object
- Eloquent → Return Model object (punya behavior/method)

---

#### 2. Insert Data

**Query Builder:**
```php
DB::table('posts')->insert([
    'title' => 'Tutorial Laravel',
    'slug' => 'tutorial-laravel',
    'body' => 'Content here...',
    'created_at' => now(),
    'updated_at' => now(),
]);
```

**Eloquent:**
```php
Post::create([
    'title' => 'Tutorial Laravel',
    'slug' => 'tutorial-laravel',
    'body' => 'Content here...',
]);
// created_at dan updated_at otomatis!
```

---

#### 3. Update Data

**Query Builder:**
```php
DB::table('posts')
    ->where('id', 1)
    ->update([
        'title' => 'Updated Title',
        'updated_at' => now(),
    ]);
```

**Eloquent:**
```php
$post = Post::find(1);
$post->update(['title' => 'Updated Title']);
// updated_at otomatis!
```

---

#### 4. Delete Data

**Query Builder:**
```php
DB::table('posts')->where('id', 1)->delete();
```

**Eloquent:**
```php
Post::destroy(1);
```

---

### Kapan Pakai Query Builder vs Eloquent?

| Skenario | Pakai Query Builder | Pakai Eloquent |
|----------|---------------------|----------------|
| CRUD sederhana | ❌ | ✅ |
| Complex query (joins, subquery) | ✅ | ❌ (atau pakai raw query) |
| Butuh Model behavior (events, relationships) | ❌ | ✅ |
| Bulk operations (update/delete banyak) | ✅ | ✅ (sama cepat) |
| Performance critical (million records) | ✅ (sedikit lebih cepat) | ⚠️ (sedikit overhead) |

**Rekomendasi untuk pemula:** **Pakai Eloquent!** Lebih clean dan maintainable. 🎯

---

## 🧪 Bagian 5: Praktik CRUD di Tinker

### Apa itu Tinker?

**Tinker** = Interactive shell Laravel untuk testing code secara langsung.

**Buka Tinker:**
```bash
php artisan tinker
```

---

### Praktik 1: CREATE (Buat Post Baru)

```bash
php artisan tinker
```

```php
>>> use App\Models\Post;

>>> $post = new Post;
=> App\Models\Post {#...}

>>> $post->title = "Tutorial Laravel Tinker";
=> "Tutorial Laravel Tinker"

>>> $post->slug = "tutorial-laravel-tinker";
=> "tutorial-laravel-tinker"

>>> $post->body = "Tinker adalah tool yang sangat berguna untuk testing!";
=> "Tinker adalah tool yang sangat berguna untuk testing!"

>>> $post->is_published = true;
=> true

>>> $post->save();
=> true

>>> $post->id;
=> 1
```

**Post berhasil dibuat!** ✅

---

### Praktik 2: CREATE dengan create()

```php
>>> Post::create([
...     'title' => 'Tips Belajar Eloquent',
...     'slug' => 'tips-belajar-eloquent',
...     'body' => 'Eloquent membuat CRUD jadi mudah!',
...     'is_published' => false,
... ]);
=> App\Models\Post {#...
     id: 2,
     title: "Tips Belajar Eloquent",
     slug: "tips-belajar-eloquent",
     ...
   }
```

---

### Praktik 3: READ (Ambil Semua Posts)

```php
>>> $posts = Post::all();
=> Illuminate\Database\Eloquent\Collection {#...
     all: [
       App\Models\Post {#...
         id: 1,
         title: "Tutorial Laravel Tinker",
         ...
       },
       App\Models\Post {#...
         id: 2,
         title: "Tips Belajar Eloquent",
         ...
       },
     ],
   }

>>> $posts->count();
=> 2
```

---

### Praktik 4: READ dengan Find

```php
>>> $post = Post::find(1);
=> App\Models\Post {#...
     id: 1,
     title: "Tutorial Laravel Tinker",
     ...
   }

>>> $post->title;
=> "Tutorial Laravel Tinker"

>>> $post->created_at;
=> Illuminate\Support\Carbon @1705315200 {#...
     date: 2025-01-15 10:00:00.0 UTC (+00:00),
   }

>>> $post->created_at->diffForHumans();
=> "2 hours ago"
```

---

### Praktik 5: UPDATE

```php
>>> $post = Post::find(1);

>>> $post->title = "Tutorial Laravel Tinker (Updated)";
=> "Tutorial Laravel Tinker (Updated)"

>>> $post->save();
=> true

>>> $post->title;
=> "Tutorial Laravel Tinker (Updated)"
```

---

### Praktik 6: UPDATE dengan update()

```php
>>> $post = Post::find(2);

>>> $post->update([
...     'title' => 'Tips Belajar Eloquent (Updated)',
...     'is_published' => true,
... ]);
=> true
```

---

### Praktik 7: DELETE

```php
>>> $post = Post::find(2);

>>> $post->delete();
=> true

>>> Post::all()->count();
=> 1
```

---

### Praktik 8: Where Query

```php
>>> $publishedPosts = Post::where('is_published', true)->get();

>>> $publishedPosts->count();
=> 1

>>> $publishedPosts->first()->title;
=> "Tutorial Laravel Tinker (Updated)"
```

---

### Tips Tinker

**Shortcut:**
- `Ctrl + C` → Exit Tinker
- `Ctrl + L` → Clear screen
- Arrow Up/Down → History command

**Helper:**
```php
>>> Post::all()->pluck('title');
=> Illuminate\Support\Collection {#...
     all: [
       "Tutorial Laravel Tinker (Updated)",
     ],
   }
```

**`pluck()`** → Ambil kolom tertentu saja (return array/collection)

---

## 📖 Summary

Di bab ini kamu sudah belajar:

- ✅ **Model** = Penerjemah antara PHP dan Database (seperti Google Translate)
- ✅ **Eloquent ORM** = Cara query database dengan PHP object (bukan SQL)
- ✅ **Naming Convention**: Model (Singular) → Table (Plural)
- ✅ **Mass Assignment Protection**: `$fillable` atau `$guarded`
- ✅ **CRUD Operations**:
  - **Create**: `new + save()`, `create()`, `firstOrCreate()`, `updateOrCreate()`
  - **Read**: `all()`, `find()`, `findOrFail()`, `first()`, `where()->get()`
  - **Update**: `find() + save()`, `update()`, `increment()`, `decrement()`
  - **Delete**: `delete()`, `destroy()`, bulk delete dengan `where()->delete()`
- ✅ **Query Builder vs Eloquent**: Eloquent lebih clean untuk CRUD sederhana
- ✅ **Tinker**: Interactive shell untuk testing Eloquent queries

**Sekarang kamu bisa CRUD dengan mudah tanpa nulis SQL manual!** 🗣️

---

## 📝 Latihan: CRUD di Tinker

### Latihan 1: Create 3 Posts

**Task:** Buat 3 posts baru dengan data bebas via Tinker.

```bash
php artisan tinker
```

```php
>>> Post::create([
...     'title' => 'Post Pertama',
...     'slug' => 'post-pertama',
...     'body' => 'Ini adalah post pertama saya!',
...     'is_published' => true,
... ]);

>>> Post::create([
...     'title' => 'Post Kedua',
...     'slug' => 'post-kedua',
...     'body' => 'Ini post kedua!',
...     'is_published' => false,
... ]);

>>> Post::create([
...     'title' => 'Post Ketiga',
...     'slug' => 'post-ketiga',
...     'body' => 'Post ketiga nih!',
...     'is_published' => true,
... ]);
```

**Verifikasi:**
```php
>>> Post::count();
=> 3
```

---

### Latihan 2: Read dan Tampilkan

**Task:** Ambil semua posts dan tampilkan title-nya.

```php
>>> $posts = Post::all();

>>> foreach ($posts as $post) {
...     echo $post->title . "\n";
... }
Post Pertama
Post Kedua
Post Ketiga
```

---

### Latihan 3: Update Post

**Task:** Ubah title post dengan ID = 2 menjadi "Post Kedua (Updated)".

```php
>>> $post = Post::find(2);
>>> $post->update(['title' => 'Post Kedua (Updated)']);
>>> $post->title;
=> "Post Kedua (Updated)"
```

---

### Latihan 4: Delete Post

**Task:** Hapus post dengan ID = 3.

```php
>>> Post::destroy(3);
=> 1

>>> Post::count();
=> 2
```

---

### Latihan 5: Where Query

**Task:** Ambil semua post yang `is_published = true`.

```php
>>> $published = Post::where('is_published', true)->get();

>>> $published->count();
=> 1

>>> $published->pluck('title');
=> Illuminate\Support\Collection {#...
     all: [
       "Post Pertama",
     ],
   }
```

---

## ⚠️ Troubleshooting

### Problem: "Add [title] to fillable property"

**Penyebab:** Mass assignment protection, `$fillable` belum diset.

**Solusi:**
```php
// app/Models/Post.php
protected $fillable = ['title', 'slug', 'body', 'is_published'];
```

---

### Problem: "Call to undefined method App\Models\Post::all()"

**Penyebab:** Model tidak extend `Illuminate\Database\Eloquent\Model`.

**Solusi:**
```php
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    // ...
}
```

---

### Problem: "Table 'blog.posts' doesn't exist"

**Penyebab:** Migration belum dijalankan.

**Solusi:**
```bash
php artisan migrate
```

---

### Problem: "Class 'App\Models\Post' not found"

**Penyebab:**
1. Model belum dibuat
2. Namespace salah
3. Autoload Composer belum di-refresh

**Solusi:**
```bash
# Pastikan Model ada di app/Models/Post.php
php artisan make:model Post

# Refresh autoload
composer dump-autoload
```

---

### Problem: Data tidak masuk setelah `save()`

**Penyebab:**
1. Database connection salah
2. Table tidak ada

**Solusi:**
```bash
# Cek .env
DB_DATABASE=blog_app
DB_USERNAME=root
DB_PASSWORD=

# Test koneksi
php artisan tinker
>>> DB::connection()->getPdo();
```

---

## 🎯 Next Chapter Preview

Di chapter berikutnya, kita akan belajar:
- ✅ Where conditions advanced (orWhere, whereBetween, whereIn, dll)
- ✅ Ordering dan Limiting results
- ✅ Soft Deletes dengan analogi "Recycle Bin"
- ✅ Timestamps (created_at, updated_at) dan Carbon
- ✅ Query debugging dengan `toSql()`

**Eloquent masih punya banyak fitur powerful!** 🚀

---

## 🔗 Referensi

- 📖 [Eloquent: Getting Started](https://laravel.com/docs/12.x/eloquent)
- 📖 [Eloquent: Relationships](https://laravel.com/docs/12.x/eloquent-relationships)
- 📖 [Query Builder](https://laravel.com/docs/12.x/queries)
- 🎥 [Laracasts - Eloquent](https://laracasts.com)

---

[⬅️ Bab 14: Seeder & Factory](14-seeder-factory.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 16: Eloquent Lanjutan ➡️](16-eloquent-lanjutan.md)

---

<div align="center">

**Model & Eloquent dasar sudah dikuasai! CRUD jadi mudah!** 🗣️

**Lanjut ke Eloquent Lanjutan untuk fitur lebih powerful!** 🚀

</div>