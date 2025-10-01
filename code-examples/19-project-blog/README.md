# 📝 Blog CRUD Application - Complete Example

> **Chapter 19: Project Blog CRUD Lengkap**
> Contoh kode lengkap untuk aplikasi Blog dengan fitur advanced

---

## 🎯 Fitur Aplikasi

✅ **CRUD Operations:**
- Create: Tambah post baru dengan image upload
- Read: List posts dengan pagination & search
- Update: Edit post & update image
- Delete: Hapus post & hapus file image

✅ **Advanced Features:**
- **Image Upload** - Upload gambar post ke storage dengan preview
- **Categories** - One-to-Many relationship dengan statistics
- **Tags** - Many-to-Many relationship dengan tag cloud
- **Pagination** - List posts dengan pagination (10 per page)
- **Search** - Cari post berdasarkan title/body
- **Slug** - Auto-generate slug dari title (JavaScript)
- **Authentication** - Only logged-in users can create/edit/delete
- **Authorization** - Users can only edit/delete their own posts (Policies)
- **Share Buttons** - Facebook, Twitter, WhatsApp
- **Reading Time** - Automatic calculation
- **Flash Messages** - Auto-dismissing alerts
- **Responsive Design** - Bootstrap 5 dengan gradient theme

---

## 📋 Prerequisites

Pastikan sudah terinstall:
- PHP 8.2 atau lebih tinggi
- Composer
- MySQL/MariaDB
- Laravel 12.x
- Node.js & NPM (untuk Vite)

---

## 🚀 Instalasi & Setup

### 1. Create New Laravel Project

```bash
composer create-project laravel/laravel blog-app
cd blog-app
```

### 2. Setup Database

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_db
DB_USERNAME=root
DB_PASSWORD=
```

Buat database:

```sql
CREATE DATABASE blog_db;
```

### 3. Install Laravel Breeze (Authentication)

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install
npm run dev
```

### 4. Copy Files

Copy semua file dari folder ini ke project Laravel Anda:
- `app/` → Models, Controllers, Policies
- `database/` → Migrations, Seeders, Factories
- `resources/` → Views
- `routes/` → web.php
- `public/` → CSS/JS (jika ada)

### 5. Create Storage Link

```bash
php artisan storage:link
```

Ini membuat symbolic link dari `public/storage` ke `storage/app/public`.

### 6. Run Migrations

```bash
php artisan migrate
```

### 7. Seed Sample Data

```bash
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=TagSeeder
php artisan db:seed --class=PostSeeder
```

### 8. Run Application

Terminal 1:
```bash
php artisan serve
```

Terminal 2:
```bash
npm run dev
```

Buka browser: http://localhost:8000

---

## 📁 Struktur File

```
code-examples/19-project-blog/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── PostController.php       # CRUD posts
│   │       ├── CategoryController.php   # CRUD categories
│   │       └── TagController.php        # CRUD tags
│   ├── Models/
│   │   ├── Post.php                     # Post model dengan relationships
│   │   ├── Category.php                 # Category model
│   │   └── Tag.php                      # Tag model
│   └── Policies/
│       └── PostPolicy.php               # Authorization untuk posts
│
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_create_categories_table.php
│   │   ├── 2024_01_02_create_tags_table.php
│   │   ├── 2024_01_03_create_posts_table.php
│   │   └── 2024_01_04_create_post_tag_table.php  # Pivot table
│   ├── factories/
│   │   └── PostFactory.php              # Dummy posts
│   └── seeders/
│       ├── CategorySeeder.php
│       ├── TagSeeder.php
│       └── PostSeeder.php
│
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php           # ✅ Layout utama (Bootstrap 5 + Auth)
│       ├── posts/
│       │   ├── index.blade.php         # ✅ List all posts
│       │   ├── show.blade.php          # ✅ Detail post
│       │   ├── create.blade.php        # ✅ Form create
│       │   └── edit.blade.php          # ✅ Form edit
│       ├── categories/
│       │   └── index.blade.php         # ✅ Category list & stats
│       └── tags/
│           └── index.blade.php         # ✅ Tag cloud & list
│
├── routes/
│   └── web.php                         # All routes
│
├── public/
│   └── storage/                        # Symlink to storage/app/public
│
└── README.md                           # File ini
```

---

## 🔗 Routes

### Post Routes (Protected with auth middleware)

| Method | URI | Name | Action |
|--------|-----|------|--------|
| GET | /posts | posts.index | List all posts (public) |
| GET | /posts/create | posts.create | Form create (auth) |
| POST | /posts | posts.store | Store new post (auth) |
| GET | /posts/{post} | posts.show | Show detail (public) |
| GET | /posts/{post}/edit | posts.edit | Form edit (auth + policy) |
| PUT | /posts/{post} | posts.update | Update post (auth + policy) |
| DELETE | /posts/{post} | posts.destroy | Delete post (auth + policy) |

### Category & Tag Routes

| Method | URI | Name | Action |
|--------|-----|------|--------|
| GET | /categories | categories.index | List categories |
| GET | /categories/{category} | categories.show | Posts by category |
| GET | /tags | tags.index | List tags |
| GET | /tags/{tag} | tags.show | Posts by tag |

### Cek Routes

```bash
php artisan route:list --name=posts
```

---

## 💡 Cara Pakai

### 1. Register & Login

1. Akses http://localhost:8000/register
2. Buat akun baru
3. Login dengan akun tersebut

### 2. Buat Post

1. Klik "Create Post"
2. Isi form:
   - Title (required, max 255)
   - Slug (optional, auto-generated dari title)
   - Body (required, gunakan TinyMCE editor)
   - Image (optional, max 2MB, jpg/png/jpeg)
   - Category (required, pilih dari dropdown)
   - Tags (optional, pilih multiple tags)
   - Published (checkbox, default false)
3. Klik "Publish Post"

### 3. Edit Post

1. Di list posts, klik "Edit" pada post milik Anda
2. Ubah data
3. Upload image baru (optional, akan replace image lama)
4. Klik "Update Post"

### 4. Delete Post

1. Klik "Delete" pada post milik Anda
2. Konfirmasi delete
3. Post & image terhapus

### 5. Search Posts

1. Gunakan search box di halaman posts
2. Ketik keyword (title atau body)
3. Enter

### 6. Filter by Category/Tag

1. Klik category name atau tag badge
2. Muncul posts dengan category/tag tersebut

---

## 🧪 Testing Manual

### Test 1: Create Post dengan Image

```
1. Login
2. Buka /posts/create
3. Isi semua field + upload image
4. Submit
5. ✅ Post muncul dengan image di /posts
6. ✅ Image tersimpan di storage/app/public/posts/
```

### Test 2: Slug Auto-generation

```
1. Isi title: "Belajar Laravel Eloquent"
2. Kosongkan slug
3. Submit
4. ✅ Slug auto-generated: "belajar-laravel-eloquent"
```

### Test 3: Many-to-Many Tags

```
1. Create post dengan 3 tags
2. ✅ Check database: post_tag table punya 3 rows
3. Edit post, hapus 1 tag
4. ✅ post_tag table punya 2 rows
```

### Test 4: Authorization

```
1. Login sebagai User A
2. Create post
3. Logout, login sebagai User B
4. Coba edit post User A
5. ✅ Harus 403 Forbidden (tidak punya akses)
```

### Test 5: Image Delete on Update

```
1. Create post dengan image A
2. Edit post, upload image B
3. ✅ Image A terhapus dari storage
4. ✅ Image B tersimpan
```

### Test 6: Pagination

```
1. Seed 50 posts: php artisan db:seed --class=PostSeeder
2. Buka /posts
3. ✅ Hanya 10 posts per page
4. ✅ Pagination links muncul
```

### Test 7: Search

```
1. Search "Laravel"
2. ✅ Hanya posts dengan "Laravel" di title/body
3. Search string kosong
4. ✅ Semua posts muncul
```

---

## 📝 Code Highlights

### Model Post dengan Relationships

```php
// app/Models/Post.php
class Post extends Model
{
    protected $fillable = [
        'title', 'slug', 'body', 'image',
        'category_id', 'user_id', 'is_published'
    ];

    // One-to-Many: Post belongsTo Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Many-to-Many: Post belongsToMany Tags
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    // One-to-Many: Post belongsTo User (author)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Auto-generate slug from title
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });
    }
}
```

### Controller Image Upload

```php
// app/Http/Controllers/PostController.php
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|max:255',
        'slug' => 'nullable|unique:posts,slug',
        'body' => 'required',
        'image' => 'nullable|image|max:2048',
        'category_id' => 'required|exists:categories,id',
        'tags' => 'nullable|array',
        'is_published' => 'boolean',
    ]);

    // Handle image upload
    if ($request->hasFile('image')) {
        $validated['image'] = $request->file('image')
                                      ->store('posts', 'public');
    }

    // Auto-assign user_id
    $validated['user_id'] = auth()->id();

    // Create post
    $post = Post::create($validated);

    // Attach tags (many-to-many)
    if ($request->tags) {
        $post->tags()->attach($request->tags);
    }

    return redirect()->route('posts.index')
                     ->with('success', 'Post berhasil dibuat!');
}
```

### Authorization dengan Policy

```php
// app/Policies/PostPolicy.php
public function update(User $user, Post $post)
{
    return $user->id === $post->user_id;
}

// Controller
public function edit(Post $post)
{
    $this->authorize('update', $post);

    $categories = Category::all();
    $tags = Tag::all();

    return view('posts.edit', compact('post', 'categories', 'tags'));
}
```

### Blade Pagination & Search

```blade
{{-- resources/views/posts/index.blade.php --}}
<form method="GET" action="{{ route('posts.index') }}">
    <input type="text" name="search" value="{{ request('search') }}">
    <button type="submit">Search</button>
</form>

@foreach($posts as $post)
    <div class="post-card">...</div>
@endforeach

{{ $posts->links() }}
```

---

## 🔧 Troubleshooting

### Error: "The POST method is not supported"

**Penyebab:** Lupa `@method('PUT')` atau `@method('DELETE')`

**Solusi:**
```blade
<form method="POST" action="{{ route('posts.update', $post) }}">
    @csrf
    @method('PUT')  {{-- ADD THIS! --}}
</form>
```

### Error: "Class 'Storage' not found"

**Solusi:**
```php
use Illuminate\Support\Facades\Storage;
```

### Image tidak muncul

**Cek:**
1. `php artisan storage:link` sudah dijalankan?
2. File ada di `storage/app/public/posts/`?
3. Akses via `{{ asset('storage/' . $post->image) }}`

### Error: "SQLSTATE[23000]: Duplicate entry for slug"

**Solusi:** Slug harus unique. Auto-generate dengan:
```php
use Illuminate\Support\Str;

$slug = Str::slug($request->title);
```

### N+1 Query Problem

**Cek dengan Debugbar:**
```bash
composer require barryvdh/laravel-debugbar --dev
```

**Fix dengan Eager Loading:**
```php
$posts = Post::with(['category', 'tags', 'user'])->paginate(10);
```

---

## 📚 Pelajaran yang Dipelajari

Dari project ini, kamu belajar:

✅ **One-to-Many Relationship** (Post → Category)
✅ **Many-to-Many Relationship** (Post ↔ Tags)
✅ **Image Upload & Storage**
✅ **Authorization dengan Policies**
✅ **Pagination**
✅ **Search Functionality**
✅ **Slug Auto-generation**
✅ **Eager Loading** untuk performa
✅ **Flash Messages** untuk feedback
✅ **Form Validation** advanced

---

## 🚀 Next Steps

Setelah menguasai Blog app ini:

### Level Up Features:

1. **Comments System**
   - Users can comment on posts
   - Reply to comments (nested)

2. **Like/Unlike Posts**
   - Many-to-Many (User ↔ Post)
   - Display like count

3. **User Profiles**
   - Show user's posts
   - Avatar upload
   - Bio

4. **Soft Deletes**
   - Don't permanently delete posts
   - Trash & restore feature

5. **Draft vs Published**
   - Save as draft
   - Schedule publish date

6. **SEO Features**
   - Meta title, description
   - Open Graph tags
   - Sitemap.xml

7. **Admin Panel**
   - Manage all posts/users
   - Dashboard statistics
   - Roles (admin/editor/author)

### Lanjut Belajar:

- **Chapter 20:** Database Relationships (deep dive)
- **Chapter 21:** Authentication (customize Breeze)
- **Chapter 22:** Authorization (Gates & Policies)

---

## 📖 Referensi

- [Chapter 19: Project Blog CRUD](../../docs/19-project-blog.md)
- [Laravel File Storage](https://laravel.com/docs/12.x/filesystem)
- [Eloquent Relationships](https://laravel.com/docs/12.x/eloquent-relationships)
- [Authorization](https://laravel.com/docs/12.x/authorization)
- [Pagination](https://laravel.com/docs/12.x/pagination)

---

**Happy Coding!** 💻✨

Build something amazing with Laravel! 🚀
