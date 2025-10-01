# Bab 26: Best Practices & Tips ⭐

[⬅️ Bab 25: Debugging](25-debugging.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 27: Next Steps ➡️](27-next-steps.md)

---

## 🎯 Learning Objectives

- ✅ Menguasai naming conventions Laravel
- ✅ Memahami security best practices
- ✅ Performance optimization tips
- ✅ Code organization yang baik
- ✅ Common mistakes & how to avoid them

---

## 📝 Bagian 1: Naming Conventions

### 1. Model Names

```php
// ✅ GOOD: Singular, PascalCase
class Post extends Model
class Category extends Model
class OrderItem extends Model

// ❌ BAD
class Posts extends Model       // Plural
class category extends Model    // lowercase
class order_item extends Model  // snake_case
```

---

### 2. Table Names

```php
// ✅ GOOD: Plural, snake_case
posts
categories
order_items

// ❌ BAD
Post                    // Singular
Categories_Table        // PascalCase
orderItems             // camelCase
```

---

### 3. Controller Names

```php
// ✅ GOOD: Singular + Controller
PostController
CategoryController
OrderItemController

// ❌ BAD
PostsController        // Plural (meskipun banyak yang pakai ini)
post_controller        // snake_case
PostCont              // Abbreviated
```

---

### 4. Method Names

```php
class PostController
{
    // ✅ GOOD: Descriptive, camelCase
    public function index()
    public function create()
    public function store()
    public function show(Post $post)
    public function getUserPosts()
    public function sendWelcomeEmail()

    // ❌ BAD
    public function Index()              // PascalCase
    public function get_user_posts()     // snake_case
    public function x()                  // Not descriptive
}
```

---

### 5. Variable Names

```php
// ✅ GOOD: Descriptive, camelCase
$posts = Post::all();
$publishedPosts = Post::published()->get();
$currentUser = auth()->user();
$totalRevenue = Order::sum('total');

// ❌ BAD
$p = Post::all();                 // Too short
$Posts = Post::all();             // PascalCase
$published_posts = ...            // snake_case
$x = auth()->user();              // Not descriptive
```

---

### 6. Route Names

```php
// ✅ GOOD: Dot notation, descriptive
Route::get('/posts', ...)->name('posts.index');
Route::get('/posts/{post}', ...)->name('posts.show');
Route::post('/posts', ...)->name('posts.store');

// Admin routes
Route::get('/admin/dashboard', ...)->name('admin.dashboard');

// ❌ BAD
->name('PostsIndex')           // PascalCase
->name('posts_index')          // snake_case
->name('p1')                   // Not descriptive
```

---

## 🔐 Bagian 2: Security Best Practices

### 1. Always Validate Input

```php
// ✅ GOOD: Validate everything!
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|max:255',
        'email' => 'required|email|unique:users',
        'content' => 'required|min:10',
    ]);

    Post::create($validated);
}

// ❌ BAD: No validation (dangerous!)
public function store(Request $request)
{
    Post::create($request->all()); // SQL injection risk!
}
```

---

### 2. Use Mass Assignment Protection

```php
// ✅ GOOD: Define $fillable
class Post extends Model
{
    protected $fillable = ['title', 'slug', 'body'];
}

// ❌ BAD: Unguarded (security risk!)
class Post extends Model
{
    protected $guarded = []; // Allow everything!
}
```

---

### 3. Hash Passwords

```php
// ✅ GOOD: Always hash!
use Illuminate\Support\Facades\Hash;

$user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
]);

// ❌ BAD: Plain text password!
$user = User::create([
    'password' => $request->password, // NEVER do this!
]);
```

---

### 4. Protect Against XSS

```blade
{{-- ✅ GOOD: Blade auto-escape --}}
<p>{{ $post->title }}</p>

{{-- ❌ BAD: Unescaped HTML (XSS risk!) --}}
<p>{!! $post->title !!}</p>

{{-- Only use {!! !!} for trusted HTML content --}}
<div>{!! $post->body !!}</div> {{-- OK if body is sanitized --}}
```

---

### 5. CSRF Protection

```blade
<!-- ✅ GOOD: Always use @csrf -->
<form method="POST" action="/posts">
    @csrf
    <input type="text" name="title">
</form>

<!-- ❌ BAD: No CSRF token (vulnerable!) -->
<form method="POST" action="/posts">
    <input type="text" name="title">
</form>
```

---

### 6. Use Authorization

```php
// ✅ GOOD: Check authorization
public function edit(Post $post)
{
    $this->authorize('update', $post);
    return view('posts.edit', compact('post'));
}

// ❌ BAD: No authorization check
public function edit(Post $post)
{
    return view('posts.edit', compact('post'));
}
```

---

## ⚡ Bagian 3: Performance Optimization

### 1. Eager Loading (Solve N+1)

```php
// ✅ GOOD: Eager load relationships
$posts = Post::with(['category', 'tags', 'user'])->get();

foreach ($posts as $post) {
    echo $post->category->name; // No extra query!
}

// ❌ BAD: N+1 queries (slow!)
$posts = Post::all();

foreach ($posts as $post) {
    echo $post->category->name; // +1 query per post!
}
```

---

### 2. Select Only Needed Columns

```php
// ✅ GOOD: Select specific columns
$posts = Post::select('id', 'title', 'created_at')->get();

// ❌ BAD: Select all columns (unnecessary data)
$posts = Post::all(); // SELECT *
```

---

### 3. Use Pagination

```php
// ✅ GOOD: Paginate large datasets
$posts = Post::latest()->paginate(15);

// ❌ BAD: Load all data at once
$posts = Post::all(); // 10,000 posts = slow!
```

---

### 4. Cache Expensive Queries

```php
// ✅ GOOD: Cache for 1 hour
$popularPosts = Cache::remember('popular-posts', 3600, function () {
    return Post::orderBy('views', 'desc')->limit(10)->get();
});

// ❌ BAD: Query every time
$popularPosts = Post::orderBy('views', 'desc')->limit(10)->get();
```

---

### 5. Use Chunking for Large Datasets

```php
// ✅ GOOD: Process in chunks
Post::chunk(100, function ($posts) {
    foreach ($posts as $post) {
        // Process post
    }
});

// ❌ BAD: Load all at once
$posts = Post::all(); // 100,000 posts = memory overflow!
```

---

## 📁 Bagian 4: Code Organization

### 1. Keep Controllers Thin

```php
// ✅ GOOD: Move logic to Service/Action class
class PostController extends Controller
{
    public function store(StorePostRequest $request)
    {
        $post = (new CreatePostAction)->execute($request->validated());
        return redirect()->route('posts.show', $post);
    }
}

// ❌ BAD: Fat controller (100+ lines of logic)
public function store(Request $request)
{
    // Validation logic
    // Business logic
    // Image processing
    // Email sending
    // ... (too much!)
}
```

---

### 2. Use Form Requests

```php
// ✅ GOOD: Separate validation
class StorePostRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'required|max:255',
            'body' => 'required',
        ];
    }
}

public function store(StorePostRequest $request)
{
    Post::create($request->validated());
}

// ❌ BAD: Validation in controller
public function store(Request $request)
{
    $request->validate([
        'title' => 'required|max:255',
        'body' => 'required',
    ]);
}
```

---

### 3. Use Scopes for Reusable Queries

```php
// ✅ GOOD: Reusable scope
class Post extends Model
{
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}

$posts = Post::published()->latest()->get();

// ❌ BAD: Repeat query everywhere
$posts = Post::where('is_published', true)->latest()->get();
```

---

### 4. Use Constants for Magic Numbers

```php
// ✅ GOOD: Use constants
class Post extends Model
{
    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;
    const STATUS_ARCHIVED = 2;
}

$post->status = Post::STATUS_PUBLISHED;

// ❌ BAD: Magic numbers
$post->status = 1; // What does 1 mean?
```

---

## ⚠️ Bagian 5: Common Mistakes

### 1. Forgetting @csrf

```blade
<!-- ❌ BAD: Will get 419 error -->
<form method="POST">
    <button type="submit">Submit</button>
</form>

<!-- ✅ GOOD -->
<form method="POST">
    @csrf
    <button type="submit">Submit</button>
</form>
```

---

### 2. Not Using Route Model Binding

```php
// ❌ BAD: Manual fetching
public function show($id)
{
    $post = Post::findOrFail($id);
    return view('posts.show', compact('post'));
}

// ✅ GOOD: Route Model Binding
public function show(Post $post)
{
    return view('posts.show', compact('post'));
}
```

---

### 3. Mixing Business Logic in Blade

```blade
<!-- ❌ BAD: Logic in view -->
@php
    $discountPrice = $product->price * 0.8;
    $formattedPrice = number_format($discountPrice);
@endphp
<p>{{ $formattedPrice }}</p>

<!-- ✅ GOOD: Logic in Controller/Model -->
<p>{{ $product->discounted_price }}</p>
```

---

### 4. Not Handling Errors

```php
// ❌ BAD: No error handling
public function destroy(Post $post)
{
    $post->delete();
    return redirect()->route('posts.index');
}

// ✅ GOOD: With error handling
public function destroy(Post $post)
{
    try {
        $post->delete();
        return redirect()->route('posts.index')
                         ->with('success', 'Post deleted!');
    } catch (\Exception $e) {
        return redirect()->back()
                         ->with('error', 'Failed to delete post.');
    }
}
```

---

## 📖 Summary

- ✅ **Naming**: Follow Laravel conventions
- ✅ **Security**: Validate, hash, authorize, CSRF
- ✅ **Performance**: Eager loading, pagination, caching
- ✅ **Organization**: Thin controllers, Form Requests, Scopes
- ✅ **Mistakes**: Common pitfalls to avoid

**Best practices membuat code profesional & maintainable!** ⭐✅

---

[⬅️ Bab 25: Debugging](25-debugging.md) | [🏠 Daftar Isi](../README.md) | [Lanjut ke Bab 27: Next Steps ➡️](27-next-steps.md)