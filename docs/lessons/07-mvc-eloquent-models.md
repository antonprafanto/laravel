# Pelajaran 7: MVC, DB Queries, and Eloquent Models

Sekarang kita akan membangun Eloquent models dan mengimplementasi pola MVC untuk mengganti data dummy dengan data real dari database. Ini adalah jantung dari aplikasi Laravel.

## 🏢 Analogi: MVC = Restoran 3 Michelin Star

Bayangkan Anda punya **restoran mewah** dengan sistem yang terorganisir rapi:

**👨‍🍳 Model (Chef + Gudang):**
- **Chef** = Eloquent Model (yang tahu cara masak/olah data)
- **Gudang bahan** = Database (tempat simpan semua ingredient/data)
- **Resep rahasia** = Business logic (aturan cara olah data)

**🍽️ View (Pelayan + Penyajian):**
- **Pelayan** = Blade Template (yang sajikan makanan ke customer)
- **Piring cantik** = HTML/CSS (tampilan yang menarik)
- **Cara penyajian** = User Interface (bagaimana data ditampilkan)

**📋 Controller (Manager Restoran):**
- **Manager** = Controller (yang koordinasi semuanya)
- **Terima pesanan** = Handle HTTP Request
- **Instruksi ke chef** = Panggil Model untuk ambil data
- **Cek hasil masakan** = Proses data
- **Kirim ke pelayan** = Return View dengan data

**🔄 Alur Kerja Restoran (MVC Flow):**
1. **Customer pesan** → HTTP Request ke Controller
2. **Manager terima pesanan** → Controller terima request
3. **Manager bilang chef masak** → Controller panggil Model
4. **Chef ambil bahan dari gudang** → Model query Database
5. **Chef masak sesuai resep** → Model proses data
6. **Manager cek hasil** → Controller terima data dari Model
7. **Manager kasih ke pelayan** → Controller kirim data ke View
8. **Pelayan sajikan cantik** → View render HTML
9. **Customer senang** → User lihat halaman yang bagus

**Kenapa pakai MVC?** Sama seperti restoran - **pembagian tugas yang jelas** membuat semuanya **terorganisir**, **mudah maintenance**, dan **bisa dikembangkan** (hire chef baru, ganti dekorasi, dll) tanpa ganggu bagian lain!

## 🎯 Tujuan Pembelajaran

Setelah menyelesaikan pelajaran ini, Anda akan:
- ✅ Memahami arsitektur MVC di Laravel
- ✅ Membuat Eloquent models untuk semua tabel
- ✅ Melakukan database queries dengan Eloquent
- ✅ Menggunakan data real di views
- ✅ Memahami mass assignment dan fillable properties

## 🏗️ Memahami MVC Architecture

### Model-View-Controller Pattern

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│    MODEL    │    │ CONTROLLER  │    │    VIEW     │
│             │    │             │    │             │
│ - Database  │◄──►│ - Logic     │◄──►│ - UI/HTML   │
│ - Business  │    │ - Routes    │    │ - Templates │
│ - Rules     │    │ - Requests  │    │ - Display   │
└─────────────┘    └─────────────┘    └─────────────┘
```

**Model**: Mengelola data dan business logic  
**View**: Menampilkan data ke user (Blade templates)  
**Controller**: Menghubungkan Model dan View, handle requests

## 🎭 Membuat Eloquent Models

**🏭 Analogi Eloquent Model = Pabrik Pintar**

Bayangkan Anda punya **pabrik produksi** yang sangat canggih:

**🤖 Model = Mesin Pabrik:**
- **Tahu cara bikin produk** = Model tahu struktur data
- **Punya SOP khusus** = Model punya method & rules
- **Bisa komunikasi antar line produksi** = Relationships antar Model

**📦 Relationships = Supply Chain:**
- **1 Pabrik → Banyak Produk** = hasMany (Category → Posts)
- **1 Produk ← 1 Pabrik** = belongsTo (Post ← Category)
- **Banyak Produk ↔ Banyak Label** = belongsToMany (Posts ↔ Tags)

**🔄 Query = Sistem Order:**
- `Post::where('title', 'Laravel')` = "Cari produk dengan nama Laravel"
- `$post->category` = "Tunjukkan pabrik yang bikin produk ini"
- `$category->posts` = "Tunjukkan semua produk dari pabrik ini"

**✨ Eloquent Magic:**
Alih-alih Anda nulis SQL manual (seperti ngatur pabrik manual), Eloquent seperti **sistem otomatis yang sudah tahu cara kerja pabrik**. Anda tinggal bilang "ambil semua produk berwarna merah", sistem otomatis tahu harus cari dimana dan gimana!

### Step 1: Generate Models

```bash
# Generate models untuk semua tabel
php artisan make:model Category
php artisan make:model Post
php artisan make:model Tag

# atau dengan flag untuk membuat sekaligus migration, factory, seeder
php artisan make:model Category -mfs
```

### Step 2: Category Model

Edit `app/Models/Category.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi ketika kita buat/ubah kategori
    // Laravel keamanan: cuma kolom ini yang boleh diisi dari formulir
    protected $fillable = [
        'name',        // Nama kategori (misal: "Tutorial Laravel")
        'slug',        // Ramah alamat web (misal: "tutorial-laravel")
        'description', // Penjelasan kategori
        'color',       // Warna untuk tampilan (misal: "blue", "green")
        'is_active',   // Apakah kategori ini aktif (benar/salah)
        'sort_order',  // Urutan tampil (angka: 1,2,3...)
    ];

    // Ubah format data otomatis saat disimpan/diambil dari database
    // Laravel akan otomatis ubah jadi format yang tepat
    protected $casts = [
        'is_active' => 'boolean',  // Ubah jadi benar/salah (bukan 1/0)
        'sort_order' => 'integer', // Ubah jadi angka bulat
    ];

    // Fungsi yang jalan otomatis saat kategori dibuat atau diubah
    // Laravel panggil fungsi ini sendiri, kita nggak perlu panggil manual
    protected static function boot()
    {
        parent::boot(); // Panggil fungsi asli Laravel dulu

        // Ketika kategori baru dibuat
        static::creating(function ($category) {
            // Kalau slug belum diisi, buat otomatis dari nama
            if (empty($category->slug)) {
                // Str::slug() = ubah "Tutorial Laravel" jadi "tutorial-laravel"
                $category->slug = Str::slug($category->name);
            }
        });

        // Ketika kategori diubah/diedit
        static::updating(function ($category) {
            // Kalau nama berubah tapi slug tidak diubah manual, buat slug baru
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // Hubungan: 1 kategori punya banyak artikel
    // Kayak folder yang isinya banyak file
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class); // Ambil semua artikel di kategori ini
    }

    // Ambil artikel yang sudah diterbitkan saja (bukan konsep)
    // Fungsi khusus untuk artikel yang siap ditampilkan ke pengunjung
    public function publishedPosts(): HasMany
    {
        return $this->posts()->where('status', 'published')        // Status = "diterbitkan"
                            ->where('published_at', '<=', now()); // Tanggal terbit sudah lewat
    }

    // Pencarian khusus: cari kategori yang aktif saja
    // Cara pakai: Category::active()->get()
    public function scopeActive($query)
    {
        return $query->where('is_active', true); // is_active = benar
    }

    // Pencarian khusus: urutkan kategori sesuai urutan tampil
    // Cara pakai: Category::ordered()->get()
    public function scopeOrdered($query)
    {
        // Urutkan berdasarkan: 1) urutan tampil, 2) nama (A-Z)
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Hitung berapa artikel yang sudah diterbitkan di kategori ini
    // Cara pakai: $category->posts_count (otomatis jadi angka)
    public function getPostsCountAttribute()
    {
        return $this->publishedPosts()->count(); // Hitung jumlah artikel
    }

    // Bilang Laravel: pakai 'slug' buat alamat web (bukan 'id')
    // Jadi alamat web: /category/tutorial-laravel (bukan /category/1)
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
```

### Step 3: Post Model

Edit `app/Models/Post.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Post extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi ketika kita buat/ubah artikel
    // Laravel keamanan: cuma kolom ini yang boleh diisi dari formulir
    protected $fillable = [
        'title',           // Judul artikel
        'slug',            // Alamat web ramah (dari judul)
        'excerpt',         // Ringkasan pendek artikel
        'content',         // Isi artikel lengkap
        'featured_image',  // Gambar utama artikel
        'status',          // Status: konsep/diterbitkan/arsip
        'is_featured',     // Apakah artikel unggulan (tampil besar)
        'published_at',    // Kapan artikel diterbitkan
        'views_count',     // Berapa kali artikel dibaca
        'meta_title',      // Judul untuk mesin pencari (SEO)
        'meta_description', // Deskripsi untuk mesin pencari (SEO)
        'user_id',         // ID penulis artikel
        'category_id',     // ID kategori artikel
    ];

    // Ubah format data otomatis saat disimpan/diambil dari database
    protected $casts = [
        'published_at' => 'datetime', // Ubah jadi tanggal-waktu yang mudah dipakai
        'is_featured' => 'boolean',   // Ubah jadi benar/salah (bukan 1/0)
        'views_count' => 'integer',   // Ubah jadi angka bulat
    ];

    // Fungsi yang jalan otomatis saat artikel dibuat atau diubah
    protected static function boot()
    {
        parent::boot(); // Panggil fungsi asli Laravel dulu

        // Ketika artikel baru dibuat
        static::creating(function ($post) {
            // Kalau slug belum diisi, buat otomatis dari judul
            if (empty($post->slug)) {
                // Ubah "Tutorial Laravel" jadi "tutorial-laravel"
                $post->slug = Str::slug($post->title);
            }

            // Kalau ringkasan kosong, buat otomatis dari isi artikel
            if (empty($post->excerpt) && !empty($post->content)) {
                // Ambil 160 huruf pertama, hapus tag HTML
                $post->excerpt = Str::limit(strip_tags($post->content), 160);
            }
        });

        // Ketika artikel diubah/diedit
        static::updating(function ($post) {
            // Kalau judul berubah tapi slug tidak diubah manual, buat slug baru
            if ($post->isDirty('title') && !$post->isDirty('slug')) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    // Hubungan: artikel ini ditulis oleh 1 pengguna
    // Kayak surat yang punya 1 pengirim
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class); // Ambil data pengguna penulis
    }

    // Nama lain untuk user() - supaya lebih jelas ini penulis artikel
    // Cara pakai: $artikel->author->name (sama dengan $artikel->user->name)
    public function author(): BelongsTo
    {
        return $this->user();
    }

    // Hubungan: artikel ini masuk ke 1 kategori
    // Kayak file yang ada di 1 folder
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class); // Ambil data kategori artikel ini
    }

    // Hubungan: artikel ini punya banyak label, 1 label bisa di banyak artikel
    // Kayak artikel bisa punya label: "Tutorial", "Pemula", "Laravel"
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class); // Ambil semua label artikel ini
    }

    // Pencarian khusus: cari artikel yang sudah diterbitkan
    // Cara pakai: Post::published()->get()
    public function scopePublished($query)
    {
        return $query->where('status', 'published')        // Status = "diterbitkan"
                    ->where('published_at', '<=', now());  // Tanggal terbit sudah lewat
    }

    // Pencarian khusus: cari artikel unggulan
    // Cara pakai: Post::featured()->get()
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true); // is_featured = benar
    }

    // Pencarian khusus: cari artikel terbaru
    // Cara pakai: Post::recent(10)->get() = ambil 10 artikel terbaru
    public function scopeRecent($query, $limit = 5)
    {
        return $query->orderBy('published_at', 'desc') // Urutkan terbaru dulu
                    ->limit($limit);                   // Batasi jumlah hasil
    }

    // Pencarian khusus: cari artikel populer (banyak dibaca)
    // Cara pakai: Post::popular(5)->get() = ambil 5 artikel terpopuler
    public function scopePopular($query, $limit = 5)
    {
        return $query->orderBy('views_count', 'desc') // Urutkan paling banyak dibaca dulu
                    ->limit($limit);                  // Batasi jumlah hasil
    }

    // Ubah tanggal terbit jadi format yang mudah dibaca
    // Cara pakai: $artikel->published_date = "15 September 2025"
    public function getPublishedDateAttribute()
    {
        // Format: tanggal Bulan tahun (misal: 15 September 2025)
        return $this->published_at?->format('d F Y');
    }

    // Hitung perkiraan waktu baca artikel (dalam menit)
    // Cara pakai: $artikel->reading_time = "5 min read"
    public function getReadingTimeAttribute()
    {
        $wordCount = str_word_count(strip_tags($this->content)); // Hitung kata di artikel
        $readingSpeed = 200; // Rata-rata orang baca 200 kata per menit
        $minutes = ceil($wordCount / $readingSpeed);             // Bagi jumlah kata dengan kecepatan baca

        return $minutes . ' min read'; // Hasil: "5 min read"
    }

    // Bilang Laravel: pakai 'slug' buat alamat web (bukan 'id')
    // Jadi alamat: /post/tutorial-laravel (bukan /post/1)
    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Tambah 1 ke jumlah kali artikel dibaca
    // Dipanggil setiap kali orang buka artikel
    public function incrementViews()
    {
        $this->increment('views_count'); // +1 ke kolom views_count
    }
}
```

### Step 4: Tag Model

Edit `app/Models/Tag.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi ketika kita buat/ubah label
    protected $fillable = [
        'name',  // Nama label (misal: "Laravel", "Tutorial")
        'slug',  // Alamat web ramah (misal: "laravel", "tutorial")
        'color', // Warna label untuk tampilan (misal: "blue", "red")
    ];

    // Fungsi yang jalan otomatis saat label dibuat
    protected static function boot()
    {
        parent::boot(); // Panggil fungsi asli Laravel dulu

        // Ketika label baru dibuat
        static::creating(function ($tag) {
            // Kalau slug belum diisi, buat otomatis dari nama
            if (empty($tag->slug)) {
                // Ubah "Laravel Framework" jadi "laravel-framework"
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    // Hubungan: label ini ada di banyak artikel, artikel bisa punya banyak label
    // Kayak hashtag di media sosial
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class); // Ambil semua artikel dengan label ini
    }

    // Ambil artikel yang sudah diterbitkan saja (bukan konsep)
    public function publishedPosts(): BelongsToMany
    {
        return $this->posts()->where('status', 'published')        // Status = "diterbitkan"
                             ->where('published_at', '<=', now()); // Tanggal terbit sudah lewat
    }

    // Hitung berapa artikel yang pakai label ini (yang sudah diterbitkan)
    // Cara pakai: $label->posts_count = angka
    public function getPostsCountAttribute()
    {
        return $this->publishedPosts()->count(); // Hitung jumlah artikel
    }

    // Bilang Laravel: pakai 'slug' buat alamat web (bukan 'id')
    // Jadi alamat: /tag/laravel (bukan /tag/1)
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
```

### Step 5: Update User Model

Edit `app/Models/User.php` untuk menambahkan relationship:

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

    // Kolom yang boleh diisi ketika daftar/ubah profil pengguna
    protected $fillable = [
        'name',     // Nama lengkap pengguna
        'email',    // Alamat email (untuk login)
        'password', // Kata sandi (otomatis di-hash/acak Laravel)
        'avatar',   // Foto profil pengguna
        'bio',      // Deskripsi singkat tentang pengguna
        'role',     // Peran: admin, penulis, pengguna biasa
    ];

    // Data yang disembunyikan saat pengguna diubah jadi JSON/array
    // Keamanan: jangan pernah tampilkan kata sandi ke browser
    protected $hidden = [
        'password',       // Kata sandi (rahasia!)
        'remember_token', // Token "ingat saya" (rahasia!)
    ];

    // Ubah format data otomatis saat disimpan/diambil dari database
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // Ubah jadi tanggal-waktu
            'password' => 'hashed',            // Otomatis acak kata sandi (keamanan)
            'last_active_at' => 'datetime',    // Kapan terakhir online
        ];
    }

    // Hubungan: 1 pengguna bisa menulis banyak artikel
    // Kayak 1 penulis yang punya banyak buku
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class); // Ambil semua artikel pengguna ini
    }

    // Ambil artikel yang sudah diterbitkan saja (bukan konsep)
    public function publishedPosts(): HasMany
    {
        return $this->posts()->published(); // Pakai scope published() dari model Post
    }

    // Cek apakah pengguna ini admin
    // Cara pakai: if ($user->isAdmin()) { ... }
    public function isAdmin(): bool
    {
        return $this->role === 'admin'; // Bandingkan role dengan "admin"
    }

    // Cek apakah pengguna ini bisa menulis artikel
    // Admin dan penulis boleh menulis, pengguna biasa tidak
    public function isAuthor(): bool
    {
        return in_array($this->role, ['admin', 'author']); // Role ada dalam daftar ini
    }
}
```

## 📊 Membuat Controllers

### Step 6: Blog Controller

```bash
php artisan make:controller BlogController
```

Edit `app/Http/Controllers/BlogController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display blog homepage
     */
    public function index()
    {
        // Cari 1 artikel "unggulan" untuk ditampilkan besar di atas
        $featuredPost = Post::published()          // Cari artikel yang sudah diterbitkan (bukan konsep)
                          ->featured()             // Yang di-centang sebagai "unggulan"
                          ->with(['category', 'author']) // Sekalian ambil data kategori & penulis (hemat pencarian)
                          ->first();               // Ambil yang pertama aja (cuma 1)

        // Cari artikel terbaru lainnya (selain yang unggulan)
        $posts = Post::published()                 // Artikel yang sudah terbit
                   ->with(['category', 'author'])  // Ambil juga data kategori & penulis
                   ->when($featuredPost, function ($query, $featuredPost) {
                       // Kalau ada artikel unggulan, jangan tampilkan lagi di daftar biasa
                       return $query->where('id', '!=', $featuredPost->id);
                   })
                   ->recent(6)                     // Urutkan terbaru dulu, ambil 6 artikel
                   ->get();                        // Jalankan pencarian, dapat daftar artikel

        // Cari semua kategori yang aktif, sekalian hitung jumlah artikelnya
        $categories = Category::active()           // Kategori yang is_active = benar
                            ->ordered()            // Urutkan sesuai urutan tampil
                            ->withCount(['publishedPosts']) // Hitung berapa artikel per kategori
                            ->get();

        // Cari label yang populer (banyak artikel pakai label ini)
        $popularTags = Tag::has('posts')           // Label yang punya artikel (minimal 1)
                         ->withCount(['publishedPosts']) // Hitung berapa artikel per label
                         ->orderBy('published_posts_count', 'desc') // Urutkan: yang paling banyak artikel di atas
                         ->limit(10)               // Ambil 10 label teratas aja
                         ->get();

        // Kirim semua data ke tampilan blog/index.blade.php
        return view('blog.index', compact(
            'featuredPost',  // Data artikel unggulan
            'posts',         // Data artikel biasa (daftar)
            'categories',    // Data semua kategori
            'popularTags'    // Data label populer
        ));
        // compact() = jalan pintas untuk buat daftar: ['featuredPost' => $featuredPost, ...]
    }

    /**
     * Display single post
     */
    public function show(Post $post)
    {
        // Increment views
        $post->incrementViews();

        // Muat data terkait (kategori, penulis, label)
        $post->load(['category', 'author', 'tags']);

        // Cari artikel terkait (kategori sama)
        $relatedPosts = Post::published()
                          ->where('id', '!=', $post->id)
                          ->where('category_id', $post->category_id)
                          ->recent(3)
                          ->get();

        // Cari artikel sebelum/sesudah
        $previousPost = Post::published()
                          ->where('published_at', '<', $post->published_at)
                          ->orderBy('published_at', 'desc')
                          ->first();

        $nextPost = Post::published()
                      ->where('published_at', '>', $post->published_at)
                      ->orderBy('published_at', 'asc')
                      ->first();

        return view('blog.show', compact(
            'post',
            'relatedPosts',
            'previousPost',
            'nextPost'
        ));
    }

    /**
     * Display posts by category
     */
    public function category(Category $category)
    {
        $posts = Post::published()
                   ->where('category_id', $category->id)
                   ->with(['author', 'tags'])
                   ->paginate(10);

        return view('blog.category', compact('category', 'posts'));
    }

    /**
     * Display posts by tag
     */
    public function tag(Tag $tag)
    {
        $posts = $tag->publishedPosts()
                   ->with(['category', 'author'])
                   ->paginate(10);

        return view('blog.tag', compact('tag', 'posts'));
    }
}
```

## 🛠️ Update Routes

Edit `routes/web.php`:

```php
<?php

use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('blog.index');
});

// Blog routes
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/post/{post:slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/blog/category/{category:slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/tag/{tag:slug}', [BlogController::class, 'tag'])->name('blog.tag');

// Static pages
Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');
```

## 🎨 Update Views dengan Data Real

### Step 7: Update Blog Index View

Edit `resources/views/blog/index.blade.php`:

```php
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

    @if($featuredPost)
    <!-- Featured Post Section -->
    <section class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-2xl text-white overflow-hidden">
        <div class="p-8 lg:p-12">
            <div class="lg:grid lg:grid-cols-2 lg:gap-12 items-center">
                <div>
                    <div class="inline-flex items-center bg-primary-500 bg-opacity-50 rounded-full px-4 py-2 text-sm font-medium mb-4">
                        <span class="w-2 h-2 bg-yellow-400 rounded-full mr-2"></span>
                        Featured Post
                    </div>
                    <h2 class="text-3xl lg:text-4xl font-bold mb-4">
                        {{ $featuredPost->title }}
                    </h2>
                    <p class="text-xl text-primary-100 mb-6">
                        {{ $featuredPost->excerpt }}
                    </p>
                    <div class="flex items-center space-x-6 text-primary-200 text-sm mb-6">
                        <span>By {{ $featuredPost->author->name }}</span>
                        <span>•</span>
                        <span>{{ $featuredPost->published_date }}</span>
                        <span>•</span>
                        <span>{{ $featuredPost->reading_time }}</span>
                    </div>
                    <a href="{{ route('blog.show', $featuredPost) }}" class="inline-flex items-center bg-white text-primary-600 font-semibold px-6 py-3 rounded-lg hover:bg-gray-50 transition-colors">
                        Baca Artikel
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                <div class="mt-8 lg:mt-0">
                    @if($featuredPost->featured_image)
                        <img src="{{ asset('storage/' . $featuredPost->featured_image) }}" 
                             alt="{{ $featuredPost->title }}"
                             class="rounded-xl shadow-lg">
                    @else
                        <div class="bg-white bg-opacity-10 rounded-xl p-6 backdrop-blur-sm">
                            <div class="space-y-3">
                                <div class="h-4 bg-white bg-opacity-20 rounded"></div>
                                <div class="h-4 bg-white bg-opacity-20 rounded w-4/5"></div>
                                <div class="h-4 bg-white bg-opacity-20 rounded w-3/5"></div>
                                <div class="h-32 bg-white bg-opacity-10 rounded-lg mt-4 flex items-center justify-center">
                                    <div class="text-6xl">📝</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    @endif

    @if($posts->count() > 0)
    <!-- Recent Posts -->
    <section>
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Recent Posts</h2>
            <a href="#" class="text-primary-600 hover:text-primary-700 font-medium">
                View All →
            </a>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($posts as $post)
            <article class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                <div class="aspect-video bg-gradient-to-br from-{{ $post->category->color }}-500 to-{{ $post->category->color }}-600 relative">
                    @if($post->featured_image)
                        <img src="{{ asset('storage/' . $post->featured_image) }}" 
                             alt="{{ $post->title }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="absolute inset-0 flex items-center justify-center text-white">
                            <div class="text-center">
                                <div class="text-4xl mb-2">📖</div>
                                <div class="text-sm font-medium">{{ strtoupper($post->category->name) }}</div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-4 text-sm text-gray-500 mb-3">
                        <span class="bg-{{ $post->category->color }}-100 text-{{ $post->category->color }}-700 px-3 py-1 rounded-full font-medium">
                            {{ $post->category->name }}
                        </span>
                        <span>{{ $post->published_date }}</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">
                        <a href="{{ route('blog.show', $post) }}" class="hover:text-primary-600 transition-colors">
                            {{ $post->title }}
                        </a>
                    </h3>
                    <p class="text-gray-600 mb-4">
                        {{ $post->excerpt }}
                    </p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <span>{{ $post->reading_time }}</span>
                            <span>•</span>
                            <span>{{ $post->views_count }} views</span>
                        </div>
                        <a href="{{ route('blog.show', $post) }}" class="text-primary-600 hover:text-primary-700 font-medium text-sm">
                            Read More
                        </a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
    </section>
    @endif

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
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <button type="submit" 
                        class="bg-primary-600 hover:bg-primary-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors">
                    Subscribe
                </button>
            </form>
        </div>
    </section>
</div>
@endsection
```

### Step 8: Update Sidebar dengan Data Real

Edit `resources/views/components/layout/sidebar.blade.php`:

```php
<aside class="space-y-6">
    <!-- About Widget -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-4">About This Blog</h3>
        <p class="text-gray-600 text-sm leading-relaxed mb-4">
            Tutorial Laravel terlengkap dalam bahasa Indonesia. Dari basic hingga advanced, 
            semua materi disusun secara sistematis untuk memudahkan pembelajaran.
        </p>
        <a href="{{ route('about') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
            Learn More →
        </a>
    </div>
    
    @if(isset($categories) && $categories->count() > 0)
    <!-- Categories Widget -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-4">Categories</h3>
        <div class="space-y-3">
            @foreach($categories as $category)
            <a href="{{ route('blog.category', $category) }}" class="flex items-center justify-between text-gray-600 hover:text-gray-900 transition-colors group">
                <span class="group-hover:text-primary-600">{{ $category->name }}</span>
                <span class="text-xs bg-gray-100 group-hover:bg-primary-100 text-gray-600 group-hover:text-primary-600 px-2 py-1 rounded-full">
                    {{ $category->published_posts_count }}
                </span>
            </a>
            @endforeach
        </div>
    </div>
    @endif
    
    <!-- Recent Posts Widget -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-4">Recent Posts</h3>
        <div class="space-y-4">
            @php
                $recentPostsSidebar = \App\Models\Post::published()
                    ->with(['author'])
                    ->recent(4)
                    ->get();
            @endphp
            
            @forelse($recentPostsSidebar as $post)
            <article class="group">
                <h4 class="font-medium text-gray-900 group-hover:text-primary-600 transition-colors mb-1 text-sm leading-tight">
                    <a href="{{ route('blog.show', $post) }}">
                        {{ $post->title }}
                    </a>
                </h4>
                <div class="flex items-center text-xs text-gray-500 space-x-2">
                    <span>{{ $post->published_date }}</span>
                    <span>•</span>
                    <span>{{ $post->views_count }} views</span>
                </div>
            </article>
            @empty
            <p class="text-gray-500 text-sm">No recent posts found.</p>
            @endforelse
        </div>
    </div>
    
    @if(isset($popularTags) && $popularTags->count() > 0)
    <!-- Popular Tags -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-4">Popular Tags</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($popularTags as $tag)
            <a href="{{ route('blog.tag', $tag) }}"
               class="bg-gray-100 hover:bg-primary-100 text-gray-700 hover:text-primary-700 px-3 py-1 rounded-full text-sm transition-colors">
                {{ $tag->name }}
                <span class="text-xs opacity-75">({{ $tag->published_posts_count }})</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif
    
    <!-- Newsletter Signup -->
    <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-xl p-6">
        <h3 class="font-bold text-gray-900 mb-2">Subscribe Newsletter</h3>
        <p class="text-gray-600 text-sm mb-4">
            Get the latest Laravel tutorials and tips delivered to your inbox.
        </p>
        <form class="space-y-3">
            <input type="email" 
                   placeholder="Your email..." 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
            <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                Subscribe
            </button>
        </form>
    </div>
</aside>
```

### Step 9: Membuat View untuk Category dan Tag

Tutorial sebelumnya sudah membuat controller methods untuk category dan tag, sekarang kita perlu membuat view templates-nya.

#### Membuat View Category

Buat file `resources/views/blog/category.blade.php`:

```php
@extends('layouts.app')

@section('title', $category->name . ' - Blog Laravel')

@section('content')
@php $showSidebar = true; @endphp

<div class="space-y-8">
    <!-- Category Header -->
    <div class="bg-gradient-to-r from-{{ $category->color }}-500 to-{{ $category->color }}-600 rounded-2xl text-white p-8">
        <div class="max-w-4xl">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <div class="text-2xl">📁</div>
                </div>
                <div>
                    <h1 class="text-3xl font-bold">{{ $category->name }}</h1>
                    <p class="text-{{ $category->color }}-100">
                        {{ $posts->total() }} artikel dalam kategori ini
                    </p>
                </div>
            </div>

            @if($category->description)
            <p class="text-lg text-{{ $category->color }}-100 leading-relaxed">
                {{ $category->description }}
            </p>
            @endif
        </div>
    </div>

    <!-- Posts Grid -->
    @if($posts->count() > 0)
    <div>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">
                Artikel {{ $category->name }}
            </h2>
            <div class="text-sm text-gray-500">
                Halaman {{ $posts->currentPage() }} dari {{ $posts->lastPage() }}
            </div>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($posts as $post)
            <article class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                <!-- Featured Image atau Placeholder -->
                <div class="aspect-video bg-gradient-to-br from-{{ $category->color }}-500 to-{{ $category->color }}-600 relative">
                    @if($post->featured_image)
                        <img src="{{ asset('storage/' . $post->featured_image) }}"
                             alt="{{ $post->title }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="absolute inset-0 flex items-center justify-center text-white">
                            <div class="text-center">
                                <div class="text-4xl mb-2">📖</div>
                                <div class="text-sm font-medium">{{ strtoupper($category->name) }}</div>
                            </div>
                        </div>
                    @endif

                    @if($post->is_featured)
                    <div class="absolute top-3 left-3">
                        <span class="bg-yellow-500 text-yellow-900 text-xs font-medium px-2 py-1 rounded-full">
                            ⭐ Unggulan
                        </span>
                    </div>
                    @endif
                </div>

                <!-- Post Content -->
                <div class="p-6">
                    <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
                        <span>{{ $post->published_date }}</span>
                        <span>{{ $post->reading_time }}</span>
                    </div>

                    <h3 class="text-xl font-bold text-gray-900 mb-3 leading-tight">
                        <a href="{{ route('blog.show', $post) }}" class="hover:text-{{ $category->color }}-600 transition-colors">
                            {{ $post->title }}
                        </a>
                    </h3>

                    <p class="text-gray-600 mb-4 leading-relaxed">
                        {{ $post->excerpt }}
                    </p>

                    <!-- Post Meta -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 text-sm text-gray-500">
                            <span>{{ $post->author->name }}</span>
                            <span>•</span>
                            <span>{{ $post->views_count }} views</span>
                        </div>

                        <!-- Post Tags -->
                        @if($post->tags->count() > 0)
                        <div class="flex space-x-1">
                            @foreach($post->tags->take(2) as $tag)
                            <a href="{{ route('blog.tag', $tag) }}"
                               class="text-xs bg-gray-100 hover:bg-{{ $category->color }}-100 text-gray-600 hover:text-{{ $category->color }}-700 px-2 py-1 rounded transition-colors">
                                {{ $tag->name }}
                            </a>
                            @endforeach
                            @if($post->tags->count() > 2)
                            <span class="text-xs text-gray-400">+{{ $post->tags->count() - 2 }}</span>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $posts->links() }}
        </div>
    </div>
    @else
    <!-- Empty State -->
    <div class="text-center py-16">
        <div class="max-w-md mx-auto">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <div class="text-3xl text-gray-400">📝</div>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                Belum Ada Artikel
            </h3>
            <p class="text-gray-600 mb-6">
                Kategori <strong>{{ $category->name }}</strong> belum memiliki artikel yang dipublikasikan.
            </p>
            <a href="{{ route('blog.index') }}"
               class="inline-flex items-center bg-{{ $category->color }}-600 hover:bg-{{ $category->color }}-700 text-white font-medium px-6 py-3 rounded-lg transition-colors">
                Lihat Semua Artikel
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
```

#### Membuat View Tag

Buat file `resources/views/blog/tag.blade.php`:

```php
@extends('layouts.app')

@section('title', 'Tag: ' . $tag->name . ' - Blog Laravel')

@section('content')
@php $showSidebar = true; @endphp

<div class="space-y-8">
    <!-- Tag Header -->
    <div class="bg-gradient-to-r from-gray-600 to-gray-700 rounded-2xl text-white p-8">
        <div class="max-w-4xl">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <div class="text-2xl">#</div>
                </div>
                <div>
                    <h1 class="text-3xl font-bold">{{ $tag->name }}</h1>
                    <p class="text-gray-200">
                        {{ $posts->total() }} artikel dengan tag ini
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Categories untuk Tag -->
    @php
        $tagCategories = \App\Models\Category::whereHas('posts', function($query) use ($tag) {
            $query->whereHas('tags', function($q) use ($tag) {
                $q->where('tags.id', $tag->id);
            })->where('status', 'published');
        })->withCount(['publishedPosts' => function($query) use ($tag) {
            $query->whereHas('tags', function($q) use ($tag) {
                $q->where('tags.id', $tag->id);
            });
        }])->orderBy('published_posts_count', 'desc')->get();
    @endphp

    @if($tagCategories->count() > 0)
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Kategori dengan Tag "{{ $tag->name }}"</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($tagCategories as $category)
            <a href="{{ route('blog.category', $category) }}"
               class="bg-{{ $category->color }}-100 hover:bg-{{ $category->color }}-200 text-{{ $category->color }}-700 hover:text-{{ $category->color }}-800 px-3 py-1 rounded-full text-sm transition-colors">
                {{ $category->name }}
                <span class="text-xs opacity-75">({{ $category->published_posts_count }})</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Posts Grid -->
    @if($posts->count() > 0)
    <div>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">
                Artikel dengan Tag "{{ $tag->name }}"
            </h2>
            <div class="text-sm text-gray-500">
                Halaman {{ $posts->currentPage() }} dari {{ $posts->lastPage() }}
            </div>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($posts as $post)
            <article class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                <!-- Featured Image atau Placeholder -->
                <div class="aspect-video bg-gradient-to-br from-{{ $post->category->color }}-500 to-{{ $post->category->color }}-600 relative">
                    @if($post->featured_image)
                        <img src="{{ asset('storage/' . $post->featured_image) }}"
                             alt="{{ $post->title }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="absolute inset-0 flex items-center justify-center text-white">
                            <div class="text-center">
                                <div class="text-4xl mb-2">📖</div>
                                <div class="text-sm font-medium">{{ strtoupper($post->category->name) }}</div>
                            </div>
                        </div>
                    @endif

                    @if($post->is_featured)
                    <div class="absolute top-3 left-3">
                        <span class="bg-yellow-500 text-yellow-900 text-xs font-medium px-2 py-1 rounded-full">
                            ⭐ Unggulan
                        </span>
                    </div>
                    @endif
                </div>

                <!-- Post Content -->
                <div class="p-6">
                    <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
                        <a href="{{ route('blog.category', $post->category) }}"
                           class="bg-{{ $post->category->color }}-100 text-{{ $post->category->color }}-700 px-3 py-1 rounded-full font-medium hover:bg-{{ $post->category->color }}-200 transition-colors">
                            {{ $post->category->name }}
                        </a>
                        <span>{{ $post->published_date }}</span>
                    </div>

                    <h3 class="text-xl font-bold text-gray-900 mb-3 leading-tight">
                        <a href="{{ route('blog.show', $post) }}" class="hover:text-gray-600 transition-colors">
                            {{ $post->title }}
                        </a>
                    </h3>

                    <p class="text-gray-600 mb-4 leading-relaxed">
                        {{ $post->excerpt }}
                    </p>

                    <!-- Post Meta -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 text-sm text-gray-500">
                            <span>{{ $post->author->name }}</span>
                            <span>•</span>
                            <span>{{ $post->reading_time }}</span>
                        </div>

                        <div class="text-sm text-gray-500">
                            {{ $post->views_count }} views
                        </div>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $posts->links() }}
        </div>
    </div>
    @else
    <!-- Empty State -->
    <div class="text-center py-16">
        <div class="max-w-md mx-auto">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <div class="text-3xl text-gray-400">#</div>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                Belum Ada Artikel
            </h3>
            <p class="text-gray-600 mb-6">
                Tag <strong>"{{ $tag->name }}"</strong> belum memiliki artikel yang dipublikasikan.
            </p>
            <a href="{{ route('blog.index') }}"
               class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white font-medium px-6 py-3 rounded-lg transition-colors">
                Lihat Semua Artikel
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
```

## 💾 Membuat Sample Data

### Step 10: Update Seeders dengan Data Real

Edit `database/seeders/PostSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan ada user admin
        $admin = User::firstOrCreate([
            'email' => 'admin@blog.test'
        ], [
            'name' => 'Admin Blog',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'bio' => 'Administrator dan penulis utama blog Laravel Indonesia.',
            'email_verified_at' => now(),
        ]);

        // Get categories
        $laravelCategory = Category::where('slug', 'laravel-framework')->first();
        $phpCategory = Category::where('slug', 'php-programming')->first();

        // Ambil label yang sudah ada
        $laravelTag = Tag::where('slug', 'laravel')->first();
        $phpTag = Tag::where('slug', 'php')->first();
        $tutorialTag = Tag::where('slug', 'tutorial')->first();
        $beginnerTag = Tag::where('slug', 'beginner')->first();

        $posts = [
            [
                'title' => 'Memulai Perjalanan dengan Laravel 12',
                'excerpt' => 'Panduan lengkap untuk memulai project Laravel 12 dari nol hingga deploy. Pelajari semua dasar-dasar yang perlu Anda ketahui.',
                'content' => $this->getLaravelContent(),
                'status' => 'published',
                'is_featured' => true,
                'published_at' => now()->subDays(1),
                'user_id' => $admin->id,
                'category_id' => $laravelCategory->id,
                'views_count' => 156,
                'tags' => [$laravelTag, $tutorialTag, $beginnerTag],
            ],
            [
                'title' => 'Laravel Eloquent: Tips dan Tricks untuk Developer',
                'excerpt' => 'Kumpulan tips dan tricks Eloquent ORM yang akan membuat kode Laravel Anda lebih efisien dan maintainable.',
                'content' => $this->getEloquentContent(),
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now()->subDays(2),
                'user_id' => $admin->id,
                'category_id' => $laravelCategory->id,
                'views_count' => 124,
                'tags' => [$laravelTag, $phpTag],
            ],
            [
                'title' => 'Membuat REST API dengan Laravel Sanctum',
                'excerpt' => 'Tutorial step-by-step membuat REST API yang secure menggunakan Laravel Sanctum untuk authentication.',
                'content' => $this->getApiContent(),
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now()->subDays(3),
                'user_id' => $admin->id,
                'category_id' => $laravelCategory->id,
                'views_count' => 89,
                'tags' => [$laravelTag, $tutorialTag],
            ],
            [
                'title' => 'Optimasi Performa Aplikasi Laravel',
                'excerpt' => 'Teknik-teknik optimasi yang proven untuk meningkatkan performa aplikasi Laravel hingga 10x lebih cepat.',
                'content' => $this->getPerformanceContent(),
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now()->subDays(4),
                'user_id' => $admin->id,
                'category_id' => $laravelCategory->id,
                'views_count' => 203,
                'tags' => [$laravelTag, $phpTag],
            ],
            [
                'title' => 'PHP 8.3: Fitur Baru yang Wajib Diketahui',
                'excerpt' => 'Eksplorasi fitur-fitur terbaru di PHP 8.3 yang akan membantu development Anda menjadi lebih efisien.',
                'content' => $this->getPhpContent(),
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now()->subDays(5),
                'user_id' => $admin->id,
                'category_id' => $phpCategory->id,
                'views_count' => 67,
                'tags' => [$phpTag, $tutorialTag],
            ],
        ];

        // Loop setiap artikel dan simpan ke database
        foreach ($posts as $postData) {
            // Pisahkan label dari data artikel (karena label punya tabel sendiri)
            $tags = $postData['tags'];          // Ambil dulu data label-nya
            unset($postData['tags']);           // Hapus label dari array artikel

            // Buat atau update artikel
            $post = Post::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($postData['title'])], // Buat slug dari judul
                $postData                       // Data artikel lainnya
            );

            // Hubungkan artikel dengan label (many-to-many relationship)
            $post->tags()->sync($tags);         // sync = hapus label lama, pasang label baru
        }
    }

    private function getLaravelContent()
    {
        return 'Laravel tutorial content...';
    }

    private function getEloquentContent()
    {
        return 'Eloquent tips and tricks content...';
    }

    private function getApiContent()
    {
        return 'Laravel Sanctum API content...';
    }

    private function getPerformanceContent()
    {
        return 'Laravel performance optimization content...';
    }

    private function getPhpContent()
    {
        return 'PHP 8.3 features content...';
    }
}
```

Update `database/seeders/DatabaseSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            TagSeeder::class,
            PostSeeder::class,
        ]);
    }
}
```

## 🚀 Testing dengan Data Real

### Step 11: Run Seeders dan Test

```bash
# Fresh install dengan data
php artisan migrate:fresh --seed

# Atau hanya seeders
php artisan db:seed
```

### 🔧 Troubleshooting Common Issues

#### Migration Duplikat Error

**Error yang mungkin terjadi:**
```
SQLSTATE[HY000]: General error: 1 table "categories" already exists
```

**Penyebab:** Ada 2 migration file yang sama-sama membuat tabel yang sama.

**Cara mengidentifikasi:**
```bash
php artisan migrate:status
```

Output yang bermasalah:
```
2025_09_15_025109_create_categories_table ...................... [Ran]
2025_09_15_034154_create_categories_table ...................... [Pending]
```

**Solusi:**
1. Identifikasi migration duplikat:
```bash
ls database/migrations/ | grep categories
```

2. Hapus migration yang lebih baru (timestamp lebih tinggi):
```bash
rm "database/migrations/2025_09_15_034154_create_categories_table.php"
```

3. Jalankan ulang migration:
```bash
php artisan migrate:fresh --seed
```

#### PostSeeder Syntax Error

**Error yang mungkin terjadi:**
```
syntax error, unexpected string content "", expecting ";"
```

**Penyebab:** String multiline tidak menggunakan heredoc syntax.

**Solusi:** Sudah diperbaiki dalam tutorial ini menggunakan heredoc syntax:
```php
private function getLaravelContent()
{
    return <<<'EOD'
# Content here
EOD;
}
```

### 📝 Best Practices untuk Migration

1. **Selalu cek status migration sebelum membuat yang baru:**
```bash
php artisan migrate:status
```

2. **Gunakan nama yang descriptive dan unique:**
```bash
# Good
php artisan make:migration create_categories_table
php artisan make:migration add_slug_to_categories_table

# Avoid duplicate names
```

3. **Periksa existing migrations sebelum membuat:**
```bash
ls database/migrations/ | grep categories
```

4. **Gunakan rollback jika perlu undo migration:**
```bash
php artisan migrate:rollback
php artisan migrate:rollback --step=2
```

Test aplikasi:

```bash
php artisan serve
npm run dev
```

Kunjungi dan test semua URL:
- `http://127.0.0.1:8000/blog` - Homepage dengan data real
- `http://127.0.0.1:8000/blog/post/memulai-perjalanan-dengan-laravel-12` - Single post
- `http://127.0.0.1:8000/blog/category/laravel-framework` - Category page
- `http://127.0.0.1:8000/blog/category/php-programming` - Category page
- `http://127.0.0.1:8000/blog/tag/laravel` - Tag page
- `http://127.0.0.1:8000/blog/tag/tutorial` - Tag page

Semua halaman sekarang seharusnya berfungsi tanpa error "View not found"!

## ✅ Verifikasi MVC Implementation

### Test di Tinker

```bash
php artisan tinker
```

```php
// Test Model relationships
$post = App\Models\Post::first();
$post->author->name; // Author name
$post->category->name; // Category name
$post->tags; // Collection of tags

// Test scopes
App\Models\Post::published()->count();
App\Models\Post::featured()->first();

// Test categories with post counts
App\Models\Category::withCount('publishedPosts')->get();
```


## 🎯 Kesimpulan

Selamat! Anda telah berhasil:
- ✅ Membangun Eloquent models yang lengkap dengan relationships
- ✅ Mengimplementasi arsitektur MVC yang proper
- ✅ Membuat database queries dengan Eloquent
- ✅ Mengganti data dummy dengan data real dari database
- ✅ Menambahkan scopes dan accessor/mutator
- ✅ Membuat seeders untuk sample data

Aplikasi blog sekarang sudah menggunakan data real dari database dengan struktur MVC yang proper. Di pelajaran selanjutnya, kita akan belajar tentang Eloquent relationships dan GET parameters.

---

**Selanjutnya:** [Pelajaran 8: Eloquent Relations and GET Parameters](08-eloquent-relations-get-parameters.md)

*MVC Architecture is ready! 🏗️*