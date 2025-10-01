# 🗄️ Eloquent ORM - Code Examples

> **Chapter 15: Model & Eloquent Dasar**
> Contoh kode untuk operasi Eloquent ORM dengan berbagai use cases

---

## 🎯 Apa yang Dipelajari?

Folder ini berisi contoh-contoh kode untuk:

✅ **CRUD Operations** - Create, Read, Update, Delete
✅ **Query Builder** - where(), orderBy(), limit(), etc.
✅ **Mass Assignment** - fillable & guarded
✅ **Route Model Binding** - Automatic model injection
✅ **Accessors & Mutators** - Get & set attributes
✅ **Query Scopes** - Reusable query logic
✅ **Timestamps** - created_at & updated_at
✅ **Soft Deletes** - Trash & restore

---

## 📁 Struktur File

```
code-examples/15-model-eloquent/
├── 01-basic-crud.php           # Create, Read, Update, Delete
├── 02-query-builder.php        # where, orderBy, limit, etc.
├── 03-mass-assignment.php      # fillable & guarded
├── 04-route-binding.php        # Controller examples
├── 05-accessors-mutators.php   # Get & set attributes
├── 06-query-scopes.php         # Local & global scopes
├── 07-soft-deletes.php         # Trash & restore
└── README.md                   # File ini
```

---

## 🚀 Cara Menggunakan

### Setup Project

1. Buat project Laravel baru:
```bash
composer create-project laravel/laravel eloquent-demo
cd eloquent-demo
```

2. Setup database di `.env`:
```env
DB_DATABASE=eloquent_db
```

3. Buat database:
```sql
CREATE DATABASE eloquent_db;
```

4. Buat model & migration:
```bash
php artisan make:model Post -m
```

5. Edit migration `database/migrations/xxxx_create_posts_table.php`:
```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('slug')->unique();
    $table->text('body');
    $table->boolean('is_published')->default(false);
    $table->timestamps();
    $table->softDeletes(); // For soft delete examples
});
```

6. Run migration:
```bash
php artisan migrate
```

---

## 📝 Code Examples

### 01. Basic CRUD

```php
// CREATE
$post = Post::create([
    'title' => 'Tutorial Laravel',
    'slug' => 'tutorial-laravel',
    'body' => 'Content here...',
]);

// READ
$posts = Post::all();
$post = Post::find(1);
$post = Post::where('slug', 'tutorial-laravel')->first();

// UPDATE
$post->update(['title' => 'Updated Title']);

// DELETE
$post->delete();
```

**File:** `01-basic-crud.php`

---

### 02. Query Builder

```php
// WHERE conditions
$posts = Post::where('is_published', true)->get();
$posts = Post::where('views', '>', 100)->get();

// ORDER BY
$posts = Post::orderBy('created_at', 'desc')->get();
$posts = Post::latest()->get(); // Shorthand for orderBy created_at desc

// LIMIT & OFFSET
$posts = Post::limit(5)->get();
$posts = Post::skip(10)->take(5)->get();

// COUNT, SUM, AVG
$count = Post::count();
$total = Post::sum('views');
$avg = Post::avg('views');
```

**File:** `02-query-builder.php`

---

### 03. Mass Assignment

```php
// Model with fillable
class Post extends Model
{
    protected $fillable = ['title', 'slug', 'body'];
}

// Create with mass assignment
$post = Post::create($request->all()); // Safe!

// Without fillable = MassAssignmentException!
```

**File:** `03-mass-assignment.php`

---

### 04. Route Model Binding

```php
// Route
Route::get('/posts/{post}', [PostController::class, 'show']);

// Controller - Automatic injection!
public function show(Post $post)
{
    return view('posts.show', compact('post'));
}

// Laravel automatically:
// $post = Post::findOrFail($id);
```

**File:** `04-route-binding.php`

---

### 05. Accessors & Mutators

```php
class Post extends Model
{
    // Accessor: Get attribute
    public function getTitleUppercaseAttribute()
    {
        return strtoupper($this->title);
    }

    // Mutator: Set attribute
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = ucfirst($value);
    }
}

// Usage
$post->title = 'hello world';  // Stored as "Hello world"
echo $post->title_uppercase;   // Output: "HELLO WORLD"
```

**File:** `05-accessors-mutators.php`

---

### 06. Query Scopes

```php
class Post extends Model
{
    // Local scope
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopePopular($query)
    {
        return $query->where('views', '>', 1000);
    }
}

// Usage
$posts = Post::published()->get();
$posts = Post::published()->popular()->latest()->get();
```

**File:** `06-query-scopes.php`

---

### 07. Soft Deletes

```php
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;
}

// Delete (soft)
$post->delete(); // Moves to trash

// Query only trashed
$posts = Post::onlyTrashed()->get();

// Include trashed
$posts = Post::withTrashed()->get();

// Restore
$post->restore();

// Force delete (permanent)
$post->forceDelete();
```

**File:** `07-soft-deletes.php`

---

## 🧪 Testing Examples

### Test in Tinker

```bash
php artisan tinker
```

```php
// Create post
$post = Post::create([
    'title' => 'Test Post',
    'slug' => 'test-post',
    'body' => 'This is test content',
]);

// Find by ID
$post = Post::find(1);

// Update
$post->update(['title' => 'Updated Test']);

// Delete
$post->delete();

// Check if exists
Post::where('slug', 'test-post')->exists(); // true/false

// First or Create
$post = Post::firstOrCreate(
    ['slug' => 'unique-slug'],
    ['title' => 'New Post', 'body' => 'Content...']
);
```

---

## 💡 Tips & Best Practices

### 1. Always Use Mass Assignment Protection

```php
// ✅ GOOD
protected $fillable = ['title', 'slug', 'body'];

// ❌ BAD
protected $guarded = []; // Too permissive!
```

---

### 2. Use Query Scopes for Reusability

```php
// ✅ GOOD
$posts = Post::published()->latest()->get();

// ❌ BAD
$posts = Post::where('is_published', true)
             ->orderBy('created_at', 'desc')
             ->get();
```

---

### 3. Use Route Model Binding

```php
// ✅ GOOD
public function show(Post $post)
{
    return view('posts.show', compact('post'));
}

// ❌ BAD
public function show($id)
{
    $post = Post::findOrFail($id);
    return view('posts.show', compact('post'));
}
```

---

### 4. Eager Loading to Avoid N+1

```php
// ✅ GOOD
$posts = Post::with('category')->get();

// ❌ BAD
$posts = Post::all();
foreach ($posts as $post) {
    echo $post->category->name; // N+1 problem!
}
```

---

## 📚 Referensi

- [Chapter 15: Model & Eloquent](../../docs/15-model-eloquent.md)
- [Laravel Eloquent Docs](https://laravel.com/docs/12.x/eloquent)
- [Query Builder](https://laravel.com/docs/12.x/queries)

---

## 🎯 Latihan

### Latihan 1: CRUD Lengkap

Buat aplikasi sederhana dengan CRUD lengkap:
1. Model `Product` dengan fields: name, price, description
2. Migration dengan timestamps
3. Controller dengan semua method (index, create, store, edit, update, destroy)
4. Views untuk semua operations

### Latihan 2: Query Scopes

Tambahkan 3 scope ke model Post:
1. `scopePopular()` - views > 1000
2. `scopeRecent()` - created in last 7 days
3. `scopeByAuthor($authorId)` - filter by author

### Latihan 3: Soft Deletes

Implementasi soft deletes:
1. Tambah SoftDeletes trait ke model
2. Buat "Trash" page untuk melihat deleted posts
3. Tambah button "Restore" & "Permanent Delete"

---

**Happy Learning!** 💻✨

Master Eloquent dan kamu akan sangat produktif dengan Laravel! 🚀
