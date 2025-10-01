# Bab 25: Debugging & Error Handling 🐛

[⬅️ Bab 24: Session & Flash Messages](24-session-flash.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 26: Best Practices ➡️](26-best-practices.md)

---

## 🎯 Learning Objectives

- ✅ Menguasai debugging tools Laravel
- ✅ Bisa gunakan dd(), dump(), ray()
- ✅ Implement error handling dengan try-catch
- ✅ Mengenal common errors & solutions
- ✅ Tips debugging untuk pemula

---

## 🐛 Bagian 1: Debugging Tools

### 1. dd() - Die and Dump

```php
$users = User::all();

dd($users); // Tampilkan data + STOP execution
```

**Output:** Formatted data + stack trace

**Gunanya:** Debug value cepat!

---

### 2. dump() - Dump Only

```php
$users = User::all();
dump($users); // Tampilkan data, LANJUT execution

$posts = Post::all();
dump($posts);

return view('dashboard');
```

**Output:** Multiple dumps before page loads

---

### 3. ddd() - Die, Dump, and Debug

```php
ddd($query->toSql(), $query->getBindings());
```

**Better formatted** than dd()!

---

### 4. ray() - Debug dengan Ray App

```bash
composer require spatie/laravel-ray --dev
```

```php
ray('Hello World');
ray($user)->blue();
ray($posts)->table();
```

**Gunanya:** Debug tanpa ganggu UI! Data muncul di Ray app.

---

## 🔍 Bagian 2: Query Debugging

### toSql() - Lihat SQL Query

```php
$query = Post::where('is_published', true)
             ->orderBy('created_at', 'desc');

dd($query->toSql());
// Output: "select * from `posts` where `is_published` = ? order by `created_at` desc"

dd($query->getBindings());
// Output: [true]
```

---

### DB Query Log

```php
use Illuminate\Support\Facades\DB;

DB::enableQueryLog();

$posts = Post::with('category')->get();

dd(DB::getQueryLog());
// Output: Array of all queries with SQL, bindings, time
```

---

### Laravel Debugbar

```bash
composer require barryvdh/laravel-debugbar --dev
```

**Auto shows:**
- All queries
- Execution time
- Memory usage
- Session data
- Views loaded

**Bottom toolbar** di browser! 🎯

---

## ⚠️ Bagian 3: Error Handling

### Try-Catch

```php
public function destroy(Post $post)
{
    try {
        // Attempt to delete
        $post->delete();

        return redirect()->route('posts.index')
                         ->with('success', 'Post deleted!');

    } catch (\Exception $e) {
        // Handle error
        return redirect()->back()
                         ->with('error', 'Failed to delete: ' . $e->getMessage());
    }
}
```

---

### Custom Exception Messages

```php
try {
    if ($post->user_id !== auth()->id()) {
        throw new \Exception('Unauthorized! You can only delete your own posts.');
    }

    $post->delete();

} catch (\Exception $e) {
    return redirect()->back()->with('error', $e->getMessage());
}
```

---

### Specific Exception Types

```php
use Illuminate\Database\QueryException;

try {
    Post::create($validated);
} catch (QueryException $e) {
    if ($e->getCode() === '23000') {
        // Duplicate entry
        return back()->with('error', 'Slug already exists!');
    }

    return back()->with('error', 'Database error: ' . $e->getMessage());
}
```

---

## 🔥 Bagian 4: Common Errors & Solutions

### 1. Class Not Found

**Error:**
```
Class 'App\Models\Post' not found
```

**Penyebab:** Lupa import atau typo

**Solusi:**
```php
use App\Models\Post; // Add this!

$posts = Post::all();
```

---

### 2. Route Not Found

**Error:**
```
Route [posts.index] not defined.
```

**Solusi:**
```bash
# Cek routes
php artisan route:list

# Pastikan route ada dan punya name
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
```

---

### 3. CSRF Token Mismatch

**Error:**
```
419 | Page Expired
```

**Solusi:**
```blade
<form method="POST">
    @csrf {{-- JANGAN LUPA INI! --}}
</form>
```

---

### 4. Mass Assignment Exception

**Error:**
```
Add [title] to fillable property to allow mass assignment on [App\Models\Post]
```

**Solusi:**
```php
// Model
protected $fillable = ['title', 'slug', 'body'];
```

---

### 5. N+1 Query Problem

**Error:** Slow page load (100+ queries!)

**Debug:**
```php
DB::enableQueryLog();
$posts = Post::all();
foreach ($posts as $post) {
    echo $post->category->name; // N+1 problem!
}
dd(DB::getQueryLog()); // 101 queries! 😱
```

**Solusi:**
```php
$posts = Post::with('category')->get(); // Eager loading
// Only 2 queries! ✅
```

---

### 6. Undefined Variable

**Error:**
```
Undefined variable $posts
```

**Solusi:**
```php
// Controller: Pastikan compact() punya variable
return view('posts.index', compact('posts'));

// Atau array
return view('posts.index', ['posts' => $posts]);
```

---

### 7. Method Not Allowed

**Error:**
```
405 | Method Not Allowed
```

**Penyebab:** Route method tidak sesuai form method

**Solusi:**
```blade
{{-- Form DELETE/PUT harus pakai @method --}}
<form method="POST" action="{{ route('posts.destroy', $post) }}">
    @csrf
    @method('DELETE') {{-- ADD THIS! --}}
    <button type="submit">Delete</button>
</form>
```

---

## 💡 Tips Debugging untuk Pemula

### 1. Read Error Messages Carefully

**Error message memberitahu:**
- File mana yang error
- Line berapa
- Apa masalahnya

**Example:**
```
ErrorException
Undefined variable $posts

/var/www/html/resources/views/posts/index.blade.php:15
```

→ Check line 15 di `index.blade.php`!

---

### 2. Use dd() Strategically

```php
public function index()
{
    $posts = Post::all();
    dd($posts); // Check: Apakah data ke-fetch?

    return view('posts.index', compact('posts'));
}
```

**Move dd() gradually:**
```php
dd($posts); // Step 1: Check query
// dd(compact('posts')); // Step 2: Check data passing
// dd($posts->toArray()); // Step 3: Check array structure
```

---

### 3. Check Routes

```bash
php artisan route:list --name=posts
```

Pastikan:
- Route exists
- Method correct (GET/POST/PUT/DELETE)
- Name correct

---

### 4. Clear Cache

```bash
# Nuclear option (clear all cache)
php artisan optimize:clear

# Specific
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

### 5. Check .env

**Common issues:**
- Database credentials wrong
- APP_DEBUG=true (development)
- APP_URL correct

**After change .env:**
```bash
php artisan config:clear
```

---

### 6. Tail Logs

```bash
# Watch log real-time
tail -f storage/logs/laravel.log

# Windows (PowerShell)
Get-Content storage/logs/laravel.log -Wait -Tail 50
```

---

## 📖 Summary

- ✅ **dd() & dump()**: Quick debugging tools
- ✅ **Query Debugging**: toSql(), DB::enableQueryLog()
- ✅ **Debugbar**: Visual debugging toolbar
- ✅ **Try-Catch**: Error handling gracefully
- ✅ **Common Errors**: Know solutions for frequent errors
- ✅ **Tips**: Read errors, use dd(), clear cache

**Debugging adalah skill penting developer!** 🐛✅

---

[⬅️ Bab 24: Session & Flash Messages](24-session-flash.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 26: Best Practices ➡️](26-best-practices.md)