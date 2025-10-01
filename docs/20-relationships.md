# Bab 20: Database Relationships 🔗

[⬅️ Bab 19: Project Blog CRUD](19-project-blog.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 21: Authentication ➡️](21-authentication.md)

---

## 🎯 Learning Objectives

Setelah mempelajari bab ini, kamu akan:
- ✅ Memahami 3 jenis relationships: One-to-One, One-to-Many, Many-to-Many
- ✅ Bisa implement relationships di Laravel Eloquent
- ✅ Menguasai eager loading vs lazy loading (solve N+1 problem!)
- ✅ Paham pivot tables untuk Many-to-Many
- ✅ Bisa query data dengan relationships
- ✅ Implement tags system untuk blog project

---

## 🎯 Analogi Sederhana: 3 Jenis Relationships

### 1. One-to-One = KTP dan Pemilik

```
👤 Satu orang → Punya 1 KTP saja
🪪 Satu KTP → Dimiliki 1 orang saja

ONE User ↔️ ONE Profile
```

**Contoh:**
- User has one Profile
- Profile belongs to one User

---

### 2. One-to-Many = Ibu dan Anak

```
👩 Satu ibu → Bisa punya BANYAK anak
👶 Satu anak → Hanya punya 1 ibu

ONE Category → MANY Posts
ONE Post → belongs to ONE Category
```

**Contoh:**
- Category has many Posts
- Post belongs to one Category
- User has many Posts
- Post belongs to one User

---

### 3. Many-to-Many = Mahasiswa dan Mata Kuliah

```
👨‍🎓 Satu mahasiswa → Bisa ambil BANYAK mata kuliah
📚 Satu mata kuliah → Bisa diambil BANYAK mahasiswa

MANY Posts ↔️ MANY Tags
```

**Contoh:**
- Post has many Tags
- Tag has many Posts
- Student belongs to many Courses
- Course belongs to many Students

**Butuh pivot table!** Table penghubung di tengah.

---

## 📚 Bagian 1: One-to-Many (Deep Dive)

### Contoh: Category has many Posts

**Sudah kita implement di Blog Project!** Mari review lebih dalam.

---

### Database Structure

**Table:** `categories`
```
id | name       | slug
1  | Teknologi  | teknologi
2  | Lifestyle  | lifestyle
```

**Table:** `posts`
```
id | category_id | title                 | slug
1  | 1           | Tutorial Laravel      | tutorial-laravel
2  | 1           | Tips PHP              | tips-php
3  | 2           | Traveling ke Bali     | traveling-ke-bali
```

**Foreign Key:** `posts.category_id` → `categories.id`

---

### Model: Define Relationship

**Model:** `app/Models/Category.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * 🔗 ONE Category HAS MANY Posts
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
        // Laravel otomatis cari foreign key: category_id di table posts
    }
}
```

**Model:** `app/Models/Post.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * 🔗 ONE Post BELONGS TO ONE Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
        // Laravel otomatis cari foreign key: category_id di table posts
    }
}
```

---

### Query: Akses Relationship

#### 1. Dari Parent ke Children (Category → Posts)

```php
$category = Category::find(1);

// 📚 Ambil semua posts di category ini
$posts = $category->posts; // Collection of Posts

echo "Category: " . $category->name;
echo "Total posts: " . $posts->count();

foreach ($posts as $post) {
    echo $post->title;
}
```

---

#### 2. Dari Child ke Parent (Post → Category)

```php
$post = Post::find(1);

// 📁 Ambil category dari post ini
$category = $post->category; // Category object

echo "Post: " . $post->title;
echo "Category: " . $category->name;
```

---

#### 3. Query dengan Where

```php
// 📚 Ambil category "Teknologi" dengan semua posts-nya
$category = Category::with('posts')
                    ->where('slug', 'teknologi')
                    ->first();

// 📚 Ambil category dengan posts yang published saja
$category = Category::with(['posts' => function ($query) {
                        $query->where('is_published', true);
                    }])
                    ->find(1);
```

---

#### 4. Count Relationship

```php
// 🔢 Hitung jumlah posts per category
$categories = Category::withCount('posts')->get();

foreach ($categories as $category) {
    echo $category->name . ": " . $category->posts_count . " posts";
}
```

---

### Custom Foreign Key & Owner Key

**Default convention:**
```php
// Laravel assume:
// - Foreign key di posts table: category_id
// - Owner key di categories table: id

$this->hasMany(Post::class);
```

**Custom keys:**
```php
// Custom foreign key & owner key
$this->hasMany(Post::class, 'cat_id', 'category_primary_key');
```

---

## 🔗 Bagian 2: Many-to-Many (Tags System)

### Contoh: Posts and Tags

**Skenario:**
- 1 Post bisa punya BANYAK tags (Laravel, PHP, Tutorial)
- 1 Tag bisa ada di BANYAK posts

**Butuh 3 tables!**

---

### Database Structure

**Table:** `posts`
```
id | title
1  | Tutorial Laravel
2  | Tips PHP
```

**Table:** `tags`
```
id | name
1  | Laravel
2  | PHP
3  | Tutorial
```

**Table:** `post_tag` (Pivot Table)
```
id | post_id | tag_id
1  | 1       | 1      (Post 1 has tag Laravel)
2  | 1       | 2      (Post 1 has tag PHP)
3  | 1       | 3      (Post 1 has tag Tutorial)
4  | 2       | 2      (Post 2 has tag PHP)
```

**Naming convention pivot table:** `singular_singular` alphabetically sorted!
- `post_tag` ✅
- `tag_post` ❌
- `posts_tags` ❌

---

### Step 1: Migration Tags

```bash
php artisan make:migration create_tags_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
```

---

### Step 2: Migration Pivot Table

```bash
php artisan make:migration create_post_tag_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->timestamps(); // Optional: track when relationship created
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_tag');
    }
};
```

**Run migration:**
```bash
php artisan migrate
```

---

### Step 3: Model Tag

```bash
php artisan make:model Tag
```

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    /**
     * 🔗 MANY Tags BELONG TO MANY Posts
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
```

---

### Step 4: Update Model Post

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    // ... existing code ...

    /**
     * 🔗 MANY Posts BELONG TO MANY Tags
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
```

---

### Query: Akses Many-to-Many

#### 1. Dari Post ke Tags

```php
$post = Post::find(1);

// 🏷️ Ambil semua tags untuk post ini
$tags = $post->tags; // Collection of Tags

foreach ($tags as $tag) {
    echo $tag->name; // Laravel, PHP, Tutorial
}
```

---

#### 2. Dari Tag ke Posts

```php
$tag = Tag::where('slug', 'laravel')->first();

// 📚 Ambil semua posts dengan tag "Laravel"
$posts = $tag->posts; // Collection of Posts

foreach ($posts as $post) {
    echo $post->title;
}
```

---

#### 3. Attach Tags (Tambah Relationship)

```php
$post = Post::find(1);

// 🏷️ Attach tag dengan ID 1, 2, 3
$post->tags()->attach([1, 2, 3]);

// 🏷️ Attach satu tag
$post->tags()->attach(1);
```

**Insert ke pivot table:** `post_tag`

---

#### 4. Detach Tags (Hapus Relationship)

```php
$post = Post::find(1);

// 🗑️ Detach tag dengan ID 2
$post->tags()->detach(2);

// 🗑️ Detach semua tags
$post->tags()->detach();
```

---

#### 5. Sync Tags (Replace All)

```php
$post = Post::find(1);

// 🔄 Sync: Hapus semua tags lama, ganti dengan [1, 2, 3]
$post->tags()->sync([1, 2, 3]);

// Sama dengan:
// 1. Detach all
// 2. Attach [1, 2, 3]
```

**Gunanya:** Update tags dari form! User pilih tags baru → sync!

---

#### 6. Toggle Tags

```php
$post = Post::find(1);

// 🔄 Toggle: Jika ada → detach, jika tidak ada → attach
$post->tags()->toggle([1, 2, 3]);
```

---

### Custom Pivot Table Name

**Default:** Laravel cari table `post_tag`

**Custom:**
```php
public function tags()
{
    return $this->belongsToMany(Tag::class, 'custom_pivot_table');
}
```

---

### Akses Pivot Data

```php
$post = Post::with('tags')->find(1);

foreach ($post->tags as $tag) {
    echo $tag->name;

    // Akses pivot data (data dari table post_tag)
    echo $tag->pivot->created_at; // Kapan relationship dibuat
}
```

---

### withPivot() - Include Extra Columns

**Jika pivot table punya extra columns:**

**Migration:**
```php
Schema::create('post_tag', function (Blueprint $table) {
    $table->id();
    $table->foreignId('post_id')->constrained();
    $table->foreignId('tag_id')->constrained();
    $table->integer('order')->default(0); // Extra column!
    $table->timestamps();
});
```

**Model:**
```php
public function tags()
{
    return $this->belongsToMany(Tag::class)
                ->withPivot('order')
                ->withTimestamps();
}
```

**Query:**
```php
foreach ($post->tags as $tag) {
    echo $tag->name . " (Order: " . $tag->pivot->order . ")";
}
```

---

## 👤 Bagian 3: One-to-One (User Profile)

### Contoh: User has one Profile

**Skenario:**
- 1 User punya 1 Profile detail (bio, avatar, phone, address)
- Data user (name, email, password) di table `users`
- Data profile (bio, avatar, phone) di table `profiles`

---

### Database Structure

**Table:** `users`
```
id | name      | email
1  | John Doe  | john@example.com
```

**Table:** `profiles`
```
id | user_id | bio                 | avatar       | phone
1  | 1       | Laravel developer   | john.jpg     | 08123456789
```

**Foreign Key:** `profiles.user_id` → `users.id`

---

### Step 1: Migration Profiles

```bash
php artisan make:migration create_profiles_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
```

**`unique()`** → Pastikan 1 user hanya bisa punya 1 profile!

---

### Step 2: Model Profile

```bash
php artisan make:model Profile
```

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'bio',
        'avatar',
        'phone',
        'address',
    ];

    /**
     * 🔗 ONE Profile BELONGS TO ONE User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

---

### Step 3: Update Model User

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    // ... existing code ...

    /**
     * 🔗 ONE User HAS ONE Profile
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
}
```

---

### Query: Akses One-to-One

#### 1. Dari User ke Profile

```php
$user = User::find(1);

// 👤 Ambil profile user
$profile = $user->profile; // Profile object or null

if ($profile) {
    echo $profile->bio;
    echo $profile->phone;
}
```

---

#### 2. Dari Profile ke User

```php
$profile = Profile::find(1);

// 👤 Ambil user dari profile
$user = $profile->user; // User object

echo $user->name;
echo $user->email;
```

---

#### 3. Create Profile for User

```php
$user = User::find(1);

// 🌱 Buat profile untuk user
$user->profile()->create([
    'bio' => 'Laravel developer from Indonesia',
    'phone' => '08123456789',
    'address' => 'Jakarta, Indonesia',
]);
```

---

#### 4. Update or Create

```php
$user = User::find(1);

// 🔄 Update jika ada, create jika belum ada
$user->profile()->updateOrCreate(
    ['user_id' => $user->id], // Find by
    [
        'bio' => 'Updated bio',
        'phone' => '08999999999',
    ] // Data to update/create
);
```

---

## ⚡ Bagian 4: Eager Loading vs Lazy Loading

### Masalah: N+1 Query Problem

**Skenario:** Tampilkan 10 posts dengan category-nya.

**Lazy Loading (Bad!):**

```php
$posts = Post::all(); // 1 query

foreach ($posts as $post) {
    echo $post->title;
    echo $post->category->name; // +1 query PER POST! (N queries)
}

// Total: 1 + 10 = 11 queries! 😱
```

**Database:**
```sql
SELECT * FROM posts; -- 1 query
SELECT * FROM categories WHERE id = 1; -- Query #2
SELECT * FROM categories WHERE id = 1; -- Query #3 (duplicate!)
SELECT * FROM categories WHERE id = 2; -- Query #4
...
-- Total: 11 queries (sangat lambat!)
```

---

### Solusi: Eager Loading (Good!)

```php
$posts = Post::with('category')->get(); // 2 queries saja!

foreach ($posts as $post) {
    echo $post->title;
    echo $post->category->name; // No extra query!
}

// Total: 2 queries! ✅
```

**Database:**
```sql
SELECT * FROM posts; -- Query #1
SELECT * FROM categories WHERE id IN (1, 2, 3, ...); -- Query #2

-- Total: 2 queries (sangat cepat!)
```

---

### with() - Eager Load Multiple Relationships

```php
// Load post dengan category DAN tags
$posts = Post::with(['category', 'tags'])->get();

foreach ($posts as $post) {
    echo $post->category->name; // No extra query
    foreach ($post->tags as $tag) {
        echo $tag->name; // No extra query
    }
}
```

---

### Nested Eager Loading

```php
// Load post dengan category, dan posts lain di category yang sama
$posts = Post::with('category.posts')->get();

foreach ($posts as $post) {
    echo $post->category->name;

    // Posts lain di category yang sama
    foreach ($post->category->posts as $relatedPost) {
        echo $relatedPost->title;
    }
}
```

---

### lazy() vs load() - Conditional Loading

```php
$posts = Post::all(); // No relationships loaded

// Lazy load relationship hanya jika dibutuhkan
if ($someCondition) {
    $posts->load('category');
}
```

---

### Debug Queries

```php
use Illuminate\Support\Facades\DB;

DB::enableQueryLog();

$posts = Post::with('category')->get();

dd(DB::getQueryLog()); // Lihat semua queries yang dijalankan
```

---

## 📝 Bagian 5: Implement Tags di Blog

### Step 1: Seeder Tags

```bash
php artisan make:seeder TagSeeder
```

```php
<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'Laravel', 'PHP', 'JavaScript', 'Vue.js', 'React',
            'Tailwind CSS', 'MySQL', 'Docker', 'Git', 'API',
        ];

        foreach ($tags as $tagName) {
            Tag::create([
                'name' => $tagName,
                'slug' => Str::slug($tagName),
            ]);
        }
    }
}
```

**Run seeder:**
```bash
php artisan db:seed --class=TagSeeder
```

---

### Step 2: Update Post Create Form

**View:** `resources/views/admin/posts/create.blade.php`

```blade
{{-- ... existing form fields ... --}}

<div style="margin-bottom: 15px;">
    <label>Tags (Optional)</label>
    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
        @foreach ($tags as $tag)
            <label style="display: flex; align-items: center; gap: 5px;">
                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
                <span>{{ $tag->name }}</span>
            </label>
        @endforeach
    </div>
</div>
```

---

### Step 3: Update Controller

**Controller:** `app/Http/Controllers/PostController.php`

```php
public function create()
{
    $categories = Category::all();
    $tags = Tag::all(); // Tambahkan tags
    return view('admin.posts.create', compact('categories', 'tags'));
}

public function store(Request $request)
{
    $validated = $request->validate([
        'category_id' => 'required|exists:categories,id',
        'title' => 'required|max:255',
        'excerpt' => 'required',
        'body' => 'required',
        'image' => 'nullable|image|max:2048',
        'is_published' => 'boolean',
        'tags' => 'nullable|array', // Validate tags
        'tags.*' => 'exists:tags,id',
    ]);

    $validated['slug'] = Str::slug($validated['title']);

    if ($request->hasFile('image')) {
        $validated['image'] = $request->file('image')->store('posts', 'public');
    }

    $post = Post::create($validated);

    // 🔗 Attach tags
    if ($request->tags) {
        $post->tags()->attach($request->tags);
    }

    return redirect()->route('posts.index')
                     ->with('success', 'Post berhasil ditambahkan!');
}

public function update(Request $request, Post $post)
{
    // ... validation sama ...

    $post->update($validated);

    // 🔄 Sync tags (replace all)
    if ($request->has('tags')) {
        $post->tags()->sync($request->tags);
    } else {
        $post->tags()->detach(); // Remove all tags
    }

    return redirect()->route('posts.index')
                     ->with('success', 'Post berhasil diupdate!');
}
```

---

### Step 4: Display Tags

**View:** `resources/views/posts/index.blade.php`

```blade
{{-- Di dalam post card --}}
@if ($post->tags->isNotEmpty())
    <div style="margin-top: 10px;">
        @foreach ($post->tags as $tag)
            <span style="display: inline-block; padding: 4px 8px; background: #e9ecef; border-radius: 3px; font-size: 12px; margin-right: 5px;">
                #{{ $tag->name }}
            </span>
        @endforeach
    </div>
@endif
```

**View:** `resources/views/posts/show.blade.php`

```blade
@if ($post->tags->isNotEmpty())
    <div style="margin-top: 20px;">
        <strong>Tags:</strong>
        @foreach ($post->tags as $tag)
            <span style="display: inline-block; padding: 6px 12px; background: #007bff; color: white; border-radius: 4px; margin-right: 5px;">
                #{{ $tag->name }}
            </span>
        @endforeach
    </div>
@endif
```

---

### Step 5: Eager Load Tags (Performance!)

```php
// Controller index()
$posts = Post::with(['category', 'tags'])->published()->latest()->paginate(12);

// Controller show()
$post = Post::with(['category', 'tags'])->where('slug', $slug)->firstOrFail();
```

**Result:** No N+1 queries! Fast & efficient! ⚡

---

## 📝 Latihan: Fitur Tambahan

### Latihan 1: Filter by Tag

**Task:** Tampilkan posts by tag tertentu.

**Hint:**
```php
// Route
Route::get('/tags/{tag:slug}', [TagController::class, 'show'])->name('tags.show');

// Controller
public function show(Tag $tag)
{
    $posts = $tag->posts()->with('category')->published()->paginate(12);
    return view('tags.show', compact('tag', 'posts'));
}
```

---

### Latihan 2: Popular Tags

**Task:** Tampilkan 10 tags terpopuler (yang paling banyak dipakai posts).

**Hint:**
```php
$popularTags = Tag::withCount('posts')
                  ->orderBy('posts_count', 'desc')
                  ->limit(10)
                  ->get();
```

---

### Latihan 3: Related Posts by Tags

**Task:** Di post detail, tampilkan related posts yang punya tag sama.

**Hint:**
```php
// Get tag IDs from current post
$tagIds = $post->tags->pluck('id');

// Find posts with same tags
$relatedPosts = Post::whereHas('tags', function ($query) use ($tagIds) {
                        $query->whereIn('tags.id', $tagIds);
                    })
                    ->where('id', '!=', $post->id)
                    ->published()
                    ->limit(3)
                    ->get();
```

---

## 📖 Summary

Di bab ini kamu sudah belajar:

- ✅ **One-to-Many**: Category has many Posts (hasMany, belongsTo)
- ✅ **Many-to-Many**: Posts and Tags (belongsToMany, pivot table)
- ✅ **One-to-One**: User has one Profile (hasOne)
- ✅ **Eager Loading**: Solve N+1 problem dengan `with()`
- ✅ **Query Relationships**: attach, detach, sync, toggle
- ✅ **Pivot Data**: withPivot, withTimestamps
- ✅ **Implement Tags**: Full CRUD dengan tags system

**Relationships adalah jantung aplikasi database!** 🔗

---

## 🎯 Next Chapter Preview

Di chapter berikutnya, kita akan belajar:
- ✅ **Authentication** dengan Laravel Breeze
- ✅ Register, Login, Logout flow
- ✅ Protecting routes dengan middleware auth
- ✅ User management
- ✅ Email verification

**Saatnya tambah fitur auth ke aplikasi!** 🔐

---

## 🔗 Referensi

- 📖 [Eloquent: Relationships](https://laravel.com/docs/12.x/eloquent-relationships)
- 📖 [Eager Loading](https://laravel.com/docs/12.x/eloquent-relationships#eager-loading)
- 📖 [Many-to-Many](https://laravel.com/docs/12.x/eloquent-relationships#many-to-many)

---

[⬅️ Bab 19: Project Blog CRUD](19-project-blog.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 21: Authentication ➡️](21-authentication.md)

---

<div align="center">

**Database Relationships dikuasai! Aplikasi jadi lebih powerful!** 🔗✅

**Lanjut ke Authentication untuk fitur user management!** 🔐

</div>