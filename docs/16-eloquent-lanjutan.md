# Bab 16: Eloquent Lanjutan 🚀

[⬅️ Bab 15: Model & Eloquent Dasar](15-model-eloquent.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 17: Praktik To-Do List ➡️](17-praktik-todo.md)

---

## 🎯 Learning Objectives

Setelah mempelajari bab ini, kamu akan:
- ✅ Menguasai where conditions advanced (orWhere, whereIn, whereBetween, dll)
- ✅ Bisa ordering, limiting, dan pagination results
- ✅ Memahami Soft Deletes dengan analogi Recycle Bin
- ✅ Menguasai timestamps (created_at, updated_at) dan Carbon
- ✅ Bisa gunakan scopes untuk query yang reusable
- ✅ Paham query debugging dengan `toSql()` dan `dd()`
- ✅ Bisa kombinasikan multiple conditions untuk query kompleks

---

## 🔍 Bagian 1: Where Conditions Advanced

### Setup: Contoh Data

**Misal kita punya tabel `posts`:**

| id | title | slug | views | is_published | created_at |
|----|-------|------|-------|--------------|------------|
| 1 | Tutorial Laravel | tutorial-laravel | 100 | true | 2025-01-10 |
| 2 | Tips PHP | tips-php | 50 | true | 2025-01-12 |
| 3 | Belajar Vue.js | belajar-vuejs | 80 | false | 2025-01-15 |
| 4 | Docker Tutorial | docker-tutorial | 150 | true | 2025-01-18 |
| 5 | Git Basics | git-basics | 30 | false | 2025-01-20 |

---

### 1. where() - Single Condition

```php
// 🔍 Post yang published
$posts = Post::where('is_published', true)->get();

// 🔍 Post dengan views > 100
$posts = Post::where('views', '>', 100)->get();

// 🔍 Post dengan title tertentu
$post = Post::where('slug', 'tutorial-laravel')->first();
```

**Operator yang tersedia:**
- `=`, `!=`, `<>` (not equal)
- `>`, `>=`, `<`, `<=`
- `like`, `not like`
- `in`, `not in`
- `between`, `not between`

---

### 2. Multiple where() - AND Condition

```php
// 🔍 Post yang published DAN views > 50
$posts = Post::where('is_published', true)
             ->where('views', '>', 50)
             ->get();

// Sama dengan SQL: WHERE is_published = 1 AND views > 50
```

**Chaining `where()`** = AND condition

---

### 3. orWhere() - OR Condition

```php
// 🔍 Post yang published ATAU views > 100
$posts = Post::where('is_published', true)
             ->orWhere('views', '>', 100)
             ->get();

// SQL: WHERE is_published = 1 OR views > 100
```

**Result:** Post ID 1, 2, 4 (published) + Post ID 4 (views > 100)

---

### 4. whereIn() - IN Condition

```php
// 🔍 Post dengan ID 1, 3, atau 5
$posts = Post::whereIn('id', [1, 3, 5])->get();

// SQL: WHERE id IN (1, 3, 5)
```

**Gunanya:** Filter berdasarkan multiple values!

---

### 5. whereNotIn() - NOT IN Condition

```php
// 🔍 Post KECUALI ID 2 dan 4
$posts = Post::whereNotIn('id', [2, 4])->get();

// SQL: WHERE id NOT IN (2, 4)
```

---

### 6. whereBetween() - BETWEEN Condition

```php
// 🔍 Post dengan views antara 50 sampai 100
$posts = Post::whereBetween('views', [50, 100])->get();

// SQL: WHERE views BETWEEN 50 AND 100
```

**Result:** Post ID 1 (views=100), 2 (views=50), 3 (views=80)

---

### 7. whereNotBetween() - NOT BETWEEN

```php
// 🔍 Post dengan views TIDAK antara 50 sampai 100
$posts = Post::whereNotBetween('views', [50, 100])->get();

// SQL: WHERE views NOT BETWEEN 50 AND 100
```

**Result:** Post ID 4 (views=150), 5 (views=30)

---

### 8. whereNull() dan whereNotNull()

```php
// 🔍 Post yang belum ada excerpt (NULL)
$posts = Post::whereNull('excerpt')->get();

// 🔍 Post yang sudah ada excerpt (NOT NULL)
$posts = Post::whereNotNull('excerpt')->get();
```

**Gunanya:** Cek kolom yang kosong atau terisi!

---

### 9. whereDate(), whereMonth(), whereYear()

```php
// 🔍 Post yang dibuat tanggal 15 Januari 2025
$posts = Post::whereDate('created_at', '2025-01-15')->get();

// 🔍 Post yang dibuat di bulan Januari
$posts = Post::whereMonth('created_at', 1)->get();

// 🔍 Post yang dibuat di tahun 2025
$posts = Post::whereYear('created_at', 2025)->get();

// 🔍 Post yang dibuat hari ini
$posts = Post::whereDate('created_at', today())->get();
```

**Gunanya:** Filter berdasarkan tanggal!

---

### 10. where() dengan Closure (Grouping)

**Analogi:** Seperti pakai **kurung ()** di matematika.

```php
// 🔍 (Published DAN views > 50) ATAU (Draft DAN views > 100)
$posts = Post::where(function ($query) {
                 $query->where('is_published', true)
                       ->where('views', '>', 50);
             })
             ->orWhere(function ($query) {
                 $query->where('is_published', false)
                       ->where('views', '>', 100);
             })
             ->get();

// SQL: WHERE (is_published = 1 AND views > 50) OR (is_published = 0 AND views > 100)
```

**Gunanya:** Query kompleks dengan kombinasi AND/OR!

---

### 11. whereLike() - Search Pattern

```php
// 🔍 Post dengan title mengandung kata "Laravel"
$posts = Post::where('title', 'like', '%Laravel%')->get();

// 🔍 Post dengan title diawali kata "Tutorial"
$posts = Post::where('title', 'like', 'Tutorial%')->get();

// 🔍 Post dengan title diakhiri kata "Basics"
$posts = Post::where('title', 'like', '%Basics')->get();
```

**Pattern:**
- `%Laravel%` → Mengandung "Laravel" di mana saja
- `Tutorial%` → Diawali "Tutorial"
- `%Basics` → Diakhiri "Basics"

---

### 12. whereColumn() - Compare Columns

```php
// 🔍 Post yang updated_at = created_at (belum pernah diupdate)
$posts = Post::whereColumn('updated_at', 'created_at')->get();

// 🔍 Post yang comments > likes
$posts = Post::whereColumn('comments_count', '>', 'likes_count')->get();
```

---

## 📊 Bagian 2: Ordering & Limiting

### 1. orderBy() - Sorting

```php
// 📈 Urutkan berdasarkan views terbesar ke terkecil (DESC)
$posts = Post::orderBy('views', 'desc')->get();

// 📉 Urutkan berdasarkan views terkecil ke terbesar (ASC)
$posts = Post::orderBy('views', 'asc')->get();

// 📅 Urutkan berdasarkan tanggal terbaru
$posts = Post::orderBy('created_at', 'desc')->get();
```

**Parameter:**
- `asc` = Ascending (kecil ke besar, A-Z, lama ke baru)
- `desc` = Descending (besar ke kecil, Z-A, baru ke lama)

---

### 2. latest() dan oldest() - Shortcut Sorting

```php
// 📅 Post terbaru dulu (orderBy created_at DESC)
$posts = Post::latest()->get();

// 📅 Post terlama dulu (orderBy created_at ASC)
$posts = Post::oldest()->get();

// 📅 Latest berdasarkan kolom lain
$posts = Post::latest('updated_at')->get();
```

**`latest()`** = Shortcut untuk `orderBy('created_at', 'desc')`

---

### 3. Multiple Ordering

```php
// 📊 Urutkan berdasarkan is_published (published dulu), lalu views (terbesar dulu)
$posts = Post::orderBy('is_published', 'desc')
             ->orderBy('views', 'desc')
             ->get();
```

---

### 4. inRandomOrder() - Random Sorting

```php
// 🎲 Ambil post secara random
$posts = Post::inRandomOrder()->get();

// 🎲 Ambil 3 post random
$posts = Post::inRandomOrder()->limit(3)->get();
```

**Gunanya:** Featured posts, random quotes, dll!

---

### 5. limit() dan take() - Batasi Hasil

```php
// 🔢 Ambil 5 post pertama
$posts = Post::limit(5)->get();

// Sama dengan take()
$posts = Post::take(5)->get();

// 🔢 Ambil 10 post terbaru
$posts = Post::latest()->limit(10)->get();
```

---

### 6. skip() dan offset() - Skip Records

```php
// ⏭️ Skip 5 post pertama, ambil sisanya
$posts = Post::skip(5)->get();

// ⏭️ Skip 10 post, ambil 5 berikutnya
$posts = Post::skip(10)->take(5)->get();

// Sama dengan offset()
$posts = Post::offset(10)->limit(5)->get();
```

**Gunanya:** Pagination manual!

---

### 7. paginate() - Auto Pagination

```php
// 📄 Pagination otomatis (15 per page)
$posts = Post::paginate(15);

// Di view (Blade)
@foreach ($posts as $post)
    <h2>{{ $post->title }}</h2>
@endforeach

{{ $posts->links() }} <!-- Pagination links otomatis! -->
```

**Analogi:** Seperti **halaman buku** yang otomatis dibuat!

**Output:** Previous, 1, 2, 3, ... Next

---

### 8. simplePaginate() - Simple Pagination

```php
// 📄 Simple pagination (hanya Previous & Next)
$posts = Post::simplePaginate(15);

{{ $posts->links() }} <!-- Previous | Next -->
```

**Gunanya:** Lebih ringan untuk dataset besar (tidak hitung total pages)!

---

## 🗑️ Bagian 3: Soft Deletes

### Analogi: Soft Delete = Recycle Bin

**Hard Delete:**
```
📄 File → 🗑️ Delete → ❌ Hilang PERMANEN (tidak bisa dikembalikan)
```

**Soft Delete:**
```
📄 File → 🗑️ Delete → 📦 Recycle Bin → ✅ Bisa di-restore!
```

**Soft Delete di Laravel** = Tandai data sebagai "deleted" (tidak benar-benar hapus).

---

### Setup Soft Delete

#### Step 1: Migration (Tambahkan `deleted_at` column)

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('body');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->softDeletes(); // 🗑️ Tambahkan ini untuk soft delete
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
```

**`$table->softDeletes()`** → Buat kolom `deleted_at` (TIMESTAMP NULL)

---

#### Step 2: Model (Import `SoftDeletes` trait)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 🗑️ Import trait

class Post extends Model
{
    use HasFactory, SoftDeletes; // 🗑️ Tambahkan trait

    protected $fillable = [
        'title',
        'slug',
        'body',
        'is_published',
    ];
}
```

---

### Cara Pakai Soft Delete

#### 1. Delete (Soft Delete)

```php
// 🗑️ Soft delete post dengan ID = 1
$post = Post::find(1);
$post->delete();

// Atau langsung
Post::destroy(1);
```

**Yang terjadi:**
- Data **TIDAK** dihapus dari database
- Kolom `deleted_at` diisi dengan timestamp sekarang
- Query `Post::all()` **TIDAK** akan tampilkan post ini (otomatis difilter!)

**Database setelah soft delete:**

| id | title | slug | deleted_at |
|----|-------|------|------------|
| 1 | Tutorial Laravel | tutorial-laravel | 2025-01-20 10:00:00 |
| 2 | Tips PHP | tips-php | NULL |

---

#### 2. Query Data (Tanpa Soft Deleted)

```php
// 📚 Ambil semua post (TIDAK termasuk yang soft deleted)
$posts = Post::all();

// Result: Hanya post yang deleted_at = NULL
```

**Eloquent otomatis filter `deleted_at IS NULL`!**

---

#### 3. Query WITH Soft Deleted (Termasuk yang Dihapus)

```php
// 📚 Ambil SEMUA post (termasuk yang soft deleted)
$posts = Post::withTrashed()->get();

// 🔍 Cari post termasuk yang soft deleted
$post = Post::withTrashed()->find(1);
```

---

#### 4. Query ONLY Soft Deleted (Recycle Bin)

```php
// 🗑️ Ambil HANYA post yang soft deleted
$deletedPosts = Post::onlyTrashed()->get();

// Analogi: Buka Recycle Bin dan lihat apa aja isinya
```

---

#### 5. Restore (Kembalikan dari Recycle Bin)

```php
// ♻️ Restore post yang soft deleted
$post = Post::withTrashed()->find(1);
$post->restore();

// Atau restore multiple
Post::onlyTrashed()->where('is_published', false)->restore();
```

**Yang terjadi:** Kolom `deleted_at` diisi kembali jadi `NULL`

---

#### 6. Force Delete (Hapus Permanen)

```php
// ❌ Hapus PERMANEN (tidak bisa di-restore)
$post = Post::withTrashed()->find(1);
$post->forceDelete();

// Atau langsung
Post::onlyTrashed()->forceDelete(); // Hapus semua yang di Recycle Bin
```

**Hati-hati!** Data akan hilang PERMANEN dari database!

---

### Contoh Workflow Soft Delete

```php
// 1️⃣ Create post
$post = Post::create([
    'title' => 'Tutorial Soft Delete',
    'slug' => 'tutorial-soft-delete',
    'body' => 'Soft delete seperti Recycle Bin!',
]);

// 2️⃣ Soft delete
$post->delete();
echo "Post masuk Recycle Bin!";

// 3️⃣ Query (tidak termasuk yang dihapus)
$posts = Post::all(); // Post tadi TIDAK muncul

// 4️⃣ Query Recycle Bin
$trashedPosts = Post::onlyTrashed()->get(); // Post tadi MUNCUL

// 5️⃣ Restore
$post = Post::withTrashed()->find($post->id);
$post->restore();
echo "Post dikembalikan dari Recycle Bin!";

// 6️⃣ Query lagi
$posts = Post::all(); // Post tadi MUNCUL lagi!
```

---

### Kapan Pakai Soft Delete?

| Skenario | Pakai Soft Delete? |
|----------|-------------------|
| User account (bisa diaktifkan kembali) | ✅ |
| Blog post (bisa di-unpublish sementara) | ✅ |
| Order/transaksi (harus ada audit trail) | ✅ |
| Log activity (perlu history) | ✅ |
| Temporary data (tidak penting) | ❌ (Hard delete saja) |
| Test data | ❌ (Hard delete saja) |

---

## 📅 Bagian 4: Timestamps & Carbon

### Timestamps di Laravel

Laravel otomatis manage 2 kolom timestamps:
- **`created_at`** → Kapan data dibuat
- **`updated_at`** → Kapan data terakhir diupdate

**Setup:** `$table->timestamps()` di migration

---

### Carbon = Library Tanggal Laravel

**Carbon** = PHP library untuk manipulasi tanggal (bawaan Laravel).

**Import:**
```php
use Carbon\Carbon;
```

---

### 1. Akses Timestamps

```php
$post = Post::find(1);

// 📅 Tanggal dibuat
echo $post->created_at; // 2025-01-20 10:00:00

// 📅 Tanggal diupdate
echo $post->updated_at; // 2025-01-20 15:30:00
```

**Otomatis di-cast jadi Carbon object!**

---

### 2. Format Tanggal

```php
$post = Post::find(1);

// Format default (Y-m-d H:i:s)
echo $post->created_at; // 2025-01-20 10:00:00

// Format custom
echo $post->created_at->format('d/m/Y'); // 20/01/2025
echo $post->created_at->format('d F Y'); // 20 January 2025
echo $post->created_at->format('l, d F Y'); // Monday, 20 January 2025
```

**Format options:**
- `d` = Day (01-31)
- `m` = Month (01-12)
- `Y` = Year (2025)
- `H` = Hour (00-23)
- `i` = Minute (00-59)
- `s` = Second (00-59)
- `F` = Month name (January)
- `l` = Day name (Monday)

---

### 3. diffForHumans() - Relative Time

```php
$post = Post::find(1);

// ⏰ Relative time (human readable)
echo $post->created_at->diffForHumans();
// Output: "2 hours ago"
// Output: "3 days ago"
// Output: "1 month ago"
```

**Analogi:** Seperti waktu di **Facebook** ("2 jam yang lalu")!

---

### 4. Carbon Helper Functions

```php
// 📅 Tanggal hari ini
$today = today(); // 2025-01-20 00:00:00

// ⏰ Waktu sekarang
$now = now(); // 2025-01-20 15:30:45

// 📅 Kemarin
$yesterday = today()->subDay();

// 📅 Besok
$tomorrow = today()->addDay();

// 📅 1 minggu lalu
$lastWeek = now()->subWeek();

// 📅 1 bulan dari sekarang
$nextMonth = now()->addMonth();
```

---

### 5. Carbon Comparison

```php
$post = Post::find(1);

// ❓ Apakah post dibuat hari ini?
if ($post->created_at->isToday()) {
    echo "Post ini dibuat hari ini!";
}

// ❓ Apakah post dibuat kemarin?
if ($post->created_at->isYesterday()) {
    echo "Post ini dibuat kemarin!";
}

// ❓ Apakah post dibuat lebih dari 1 minggu lalu?
if ($post->created_at->lt(now()->subWeek())) {
    echo "Post ini lebih dari 1 minggu lalu!";
}
```

**Comparison methods:**
- `isToday()`, `isYesterday()`, `isTomorrow()`
- `isPast()`, `isFuture()`
- `eq($date)` → Equal
- `gt($date)` → Greater than (>)
- `lt($date)` → Less than (<)
- `gte($date)` → Greater than or equal (>=)
- `lte($date)` → Less than or equal (<=)

---

### 6. Disable Timestamps (Optional)

```php
class Post extends Model
{
    // ⚠️ Disable timestamps jika tidak butuh
    public $timestamps = false;
}
```

**Gunanya:** Untuk tabel yang tidak butuh `created_at` dan `updated_at`.

---

## 🎯 Bagian 5: Query Scopes (Reusable Queries)

### Analogi: Scope = Remote Control Pre-Set

Bayangkan remote TV punya tombol **"Sports Channel"** yang langsung ke channel olahraga.

**Scope** = Query yang sudah di-set sebelumnya, bisa dipanggil kapan aja!

---

### Local Scope

**Model:** `app/Models/Post.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Post extends Model
{
    // 🎯 Scope: Post yang published
    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    // 🎯 Scope: Post yang popular (views > 100)
    public function scopePopular(Builder $query): void
    {
        $query->where('views', '>', 100);
    }

    // 🎯 Scope: Post dengan search
    public function scopeSearch(Builder $query, string $keyword): void
    {
        $query->where('title', 'like', "%{$keyword}%");
    }
}
```

**Aturan:**
- Method diawali `scope`
- Parameter pertama: `Builder $query`
- Return type: `void`

---

### Cara Pakai Scope

```php
// 📚 Ambil post yang published (pakai scope)
$posts = Post::published()->get();

// Sama dengan:
// $posts = Post::where('is_published', true)->get();

// 📚 Ambil post yang popular
$posts = Post::popular()->get();

// 📚 Ambil post yang published DAN popular (chaining!)
$posts = Post::published()->popular()->get();

// 🔍 Search post dengan keyword "Laravel"
$posts = Post::search('Laravel')->get();

// 🔍 Search post yang published dengan keyword "Laravel"
$posts = Post::published()->search('Laravel')->get();
```

**Keuntungan Scope:**
- ✅ Query jadi reusable
- ✅ Code lebih clean dan readable
- ✅ Mudah di-maintain

---

### Contoh Real-World: Blog Scope

```php
class Post extends Model
{
    // 🎯 Published posts
    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    // 🎯 Draft posts
    public function scopeDraft(Builder $query): void
    {
        $query->where('is_published', false);
    }

    // 🎯 Featured posts
    public function scopeFeatured(Builder $query): void
    {
        $query->where('is_featured', true);
    }

    // 🎯 Latest posts (terbaru dulu)
    public function scopeLatest(Builder $query): void
    {
        $query->orderBy('created_at', 'desc');
    }

    // 🎯 Filter by category
    public function scopeCategory(Builder $query, $categoryId): void
    {
        $query->where('category_id', $categoryId);
    }
}
```

**Usage:**
```php
// Homepage: 5 featured posts terbaru
$featuredPosts = Post::published()
                     ->featured()
                     ->latest()
                     ->limit(5)
                     ->get();

// Category page: Posts di kategori "Teknologi"
$posts = Post::published()
             ->category(1)
             ->latest()
             ->paginate(15);
```

---

## 🐛 Bagian 6: Query Debugging

### 1. toSql() - Lihat SQL Query

```php
// 🔍 Lihat SQL query yang akan dijalankan
$sql = Post::where('is_published', true)
           ->orderBy('views', 'desc')
           ->toSql();

echo $sql;
// Output: select * from `posts` where `is_published` = ? order by `views` desc
```

**Gunanya:** Debug query yang kompleks!

---

### 2. dd() dan dump() - Debug Query Results

```php
// 🐛 Debug dan STOP execution
$posts = Post::where('is_published', true)->get();
dd($posts); // Die and Dump

// 🐛 Debug tapi LANJUT execution
dump($posts); // Dump only
```

**`dd()`** = Die and Dump (stop execution)
**`dump()`** = Dump only (lanjut execution)

---

### 3. DB::enableQueryLog() - Log Semua Queries

```php
use Illuminate\Support\Facades\DB;

// 🔥 Enable query log
DB::enableQueryLog();

// Run queries
$posts = Post::where('is_published', true)->get();
$users = User::all();

// 📊 Lihat semua queries
$queries = DB::getQueryLog();
dd($queries);

// Output: Array of queries dengan SQL, bindings, dan time
```

---

### 4. explain() - EXPLAIN SQL Query

```php
// 🔍 Lihat EXPLAIN query (performance analysis)
$explain = Post::where('is_published', true)
               ->orderBy('views', 'desc')
               ->explain();

dd($explain);
```

**Gunanya:** Optimize performance dengan analisis index!

---

## 📝 Latihan: Query Eloquent

### Latihan 1: Where Conditions

**Task:** Buat query untuk ambil:
1. Post yang published DAN views > 50
2. Post yang dibuat bulan ini
3. Post dengan title mengandung "Laravel"

**Solusi:**
```php
// 1. Published DAN views > 50
$posts = Post::where('is_published', true)
             ->where('views', '>', 50)
             ->get();

// 2. Post bulan ini
$posts = Post::whereMonth('created_at', now()->month)
             ->whereYear('created_at', now()->year)
             ->get();

// 3. Title mengandung "Laravel"
$posts = Post::where('title', 'like', '%Laravel%')->get();
```

---

### Latihan 2: Ordering & Limiting

**Task:**
1. Ambil 10 post terbaru
2. Ambil 5 post dengan views terbanyak
3. Ambil post random

**Solusi:**
```php
// 1. 10 post terbaru
$posts = Post::latest()->limit(10)->get();

// 2. 5 post views terbanyak
$posts = Post::orderBy('views', 'desc')->limit(5)->get();

// 3. Post random
$posts = Post::inRandomOrder()->first();
```

---

### Latihan 3: Soft Deletes

**Task:**
1. Soft delete post dengan ID = 1
2. Ambil semua post yang di Recycle Bin
3. Restore post dengan ID = 1

**Solusi:**
```php
// 1. Soft delete
$post = Post::find(1);
$post->delete();

// 2. Ambil yang di Recycle Bin
$trashedPosts = Post::onlyTrashed()->get();

// 3. Restore
$post = Post::withTrashed()->find(1);
$post->restore();
```

---

### Latihan 4: Timestamps

**Task:**
1. Tampilkan created_at dengan format "20 Januari 2025"
2. Tampilkan "2 hours ago" format
3. Cek apakah post dibuat hari ini

**Solusi:**
```php
$post = Post::find(1);

// 1. Format "20 Januari 2025"
echo $post->created_at->format('d F Y');

// 2. Relative time
echo $post->created_at->diffForHumans();

// 3. Cek hari ini
if ($post->created_at->isToday()) {
    echo "Post dibuat hari ini!";
}
```

---

### Latihan 5: Query Scopes

**Task:** Buat scope `popular()` untuk post dengan views > 100, lalu pakai!

**Solusi:**
```php
// Model: app/Models/Post.php
public function scopePopular(Builder $query): void
{
    $query->where('views', '>', 100);
}

// Usage:
$popularPosts = Post::popular()->get();
```

---

## ⚠️ Troubleshooting

### Problem: "Call to undefined method scopePublished()"

**Penyebab:** Method scope tidak diawali `scope` atau parameter salah.

**Solusi:**
```php
// ✅ Benar
public function scopePublished(Builder $query): void
{
    $query->where('is_published', true);
}

// ❌ Salah (tidak pakai Builder)
public function scopePublished($query)
{
    // ...
}
```

---

### Problem: Soft delete tidak jalan

**Penyebab:**
1. Trait `SoftDeletes` belum di-import
2. Migration `softDeletes()` belum dijalankan

**Solusi:**
```php
// Model
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes; // ✅ Tambahkan trait
}
```

```bash
# Migration
php artisan migrate
```

---

### Problem: Carbon format error

**Penyebab:** Format string salah.

**Solusi:** Cek dokumentasi Carbon format:
```php
// ✅ Benar
$post->created_at->format('d/m/Y');

// ❌ Salah (format tidak valid)
$post->created_at->format('dd/mm/yyyy'); // Harus huruf kecil
```

---

## 📖 Summary

Di bab ini kamu sudah belajar:

- ✅ **Where Conditions Advanced**: orWhere, whereIn, whereBetween, whereNull, whereDate, closure grouping
- ✅ **Ordering**: orderBy, latest, oldest, inRandomOrder
- ✅ **Limiting**: limit, take, skip, offset, paginate, simplePaginate
- ✅ **Soft Deletes**: delete, restore, forceDelete, onlyTrashed, withTrashed
- ✅ **Timestamps & Carbon**: format, diffForHumans, isToday, comparison
- ✅ **Query Scopes**: Local scope untuk reusable queries
- ✅ **Query Debugging**: toSql, dd, dump, enableQueryLog, explain

**Eloquent jadi semakin powerful dengan fitur-fitur ini!** 🚀

---

## 🎯 Next Chapter Preview

Di chapter berikutnya, kita akan:
- ✅ Praktik membuat aplikasi **To-Do List** mini
- ✅ Implement CRUD lengkap dengan Eloquent
- ✅ Belajar dari project nyata yang sederhana
- ✅ Persiapan sebelum project Blog yang lebih kompleks

**Saatnya praktik langsung dengan mini project!** 📝

---

## 🔗 Referensi

- 📖 [Eloquent: Query Scopes](https://laravel.com/docs/12.x/eloquent#query-scopes)
- 📖 [Eloquent: Soft Deleting](https://laravel.com/docs/12.x/eloquent#soft-deleting)
- 📖 [Carbon Documentation](https://carbon.nesbot.com/docs/)
- 📖 [Database: Pagination](https://laravel.com/docs/12.x/pagination)

---

[⬅️ Bab 15: Model & Eloquent Dasar](15-model-eloquent.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 17: Praktik To-Do List ➡️](17-praktik-todo.md)

---

<div align="center">

**Eloquent Advanced sudah dikuasai! Query jadi lebih powerful!** 🚀

**Lanjut ke praktik To-Do List untuk implement semua yang sudah dipelajari!** 📝

</div>