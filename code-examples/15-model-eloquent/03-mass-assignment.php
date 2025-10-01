<?php

/**
 * ============================================
 * MASS ASSIGNMENT
 * ============================================
 *
 * Mass assignment adalah assign multiple attributes sekaligus
 * menggunakan array.
 *
 * Contoh:
 * $post->create(['title' => '...', 'body' => '...']);
 *
 * Tapi ini ada SECURITY RISK jika tidak di-protect!
 * Laravel punya 2 cara protect: $fillable & $guarded
 */

use App\Models\Post;
use Illuminate\Http\Request;

// ============================================
// WHAT IS MASS ASSIGNMENT?
// ============================================

// ❌ WITHOUT Mass Assignment (tedious!)
$post = new Post();
$post->title = 'Laravel Tips';
$post->slug = 'laravel-tips';
$post->body = 'Content here...';
$post->is_published = true;
$post->save();
// 🗣️ Banyak lines, repetitive

// ✅ WITH Mass Assignment (clean!)
$post = Post::create([
    'title' => 'Laravel Tips',
    'slug' => 'laravel-tips',
    'body' => 'Content here...',
    'is_published' => true,
]);
// 🗣️ One method, semua attributes sekaligus!

// ============================================
// SECURITY RISK: Mass Assignment Vulnerability
// ============================================

/*
Imagine this code di Controller:

public function store(Request $request)
{
    Post::create($request->all()); // ⚠️ DANGEROUS!
}

Kenapa dangerous?
User bisa inject data yang tidak seharusnya!

Normal request:
{
    "title": "My Post",
    "body": "Content..."
}

Malicious request:
{
    "title": "My Post",
    "body": "Content...",
    "user_id": 999,          ← Claim sebagai admin!
    "is_featured": true,      ← Auto-feature sendiri!
    "views": 999999           ← Fake views!
}

Tanpa protection, semua field ini akan di-save!
*/

// ============================================
// SOLUTION 1: $fillable (Whitelist)
// ============================================

/**
 * Di Model, define columns yang ALLOWED untuk mass assignment
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * $fillable = whitelist
     * Hanya columns dalam array ini yang boleh mass-assigned
     */
    protected $fillable = [
        'title',
        'slug',
        'body',
        'is_published',
    ];
    // 🗣️ Hanya 4 fields ini yang boleh!
    // user_id, is_featured, views → BLOCKED!
}

// Usage:
$post = Post::create([
    'title' => 'Laravel Tips',
    'slug' => 'laravel-tips',
    'body' => 'Content...',
    'is_published' => true,
    'user_id' => 999,        // ← IGNORED! (not in $fillable)
    'views' => 999999,       // ← IGNORED!
]);
// 🗣️ Hanya title, slug, body, is_published yang di-save
// user_id & views diabaikan (silently ignored)

// ============================================
// SOLUTION 2: $guarded (Blacklist)
// ============================================

class Post extends Model
{
    /**
     * $guarded = blacklist
     * Semua columns BOLEH, KECUALI yang di-list
     */
    protected $guarded = [
        'id',        // Never allow user to set ID
        'user_id',   // We set this manually
        'views',     // Calculated, not user input
    ];
    // 🗣️ Semua boleh, kecuali 3 ini
}

// Or block nothing (allow all):
protected $guarded = [];
// ⚠️ DANGEROUS in production! Hanya untuk prototyping

// ============================================
// FILLABLE vs GUARDED: Which to use?
// ============================================

/*
$fillable (Whitelist) - RECOMMENDED ✅
- More secure (default deny)
- Explicit about allowed fields
- Easier to audit
- Best for production

$guarded (Blacklist) - Use with caution ⚠️
- Less secure (default allow)
- Easy to forget protecting a field
- Good for rapid prototyping
- Need careful review

BEST PRACTICE:
Always use $fillable in production!
*/

// ============================================
// MASS ASSIGNMENT METHODS
// ============================================

// Method 1: create() - Insert new record
$post = Post::create([
    'title' => 'New Post',
    'slug' => 'new-post',
    'body' => 'Content',
]);
// 🗣️ INSERT INTO posts ... VALUES ...
// Returns Model instance

// Method 2: update() - Update existing record
$post->update([
    'title' => 'Updated Title',
    'is_published' => true,
]);
// 🗣️ UPDATE posts SET title = ..., is_published = ...
// Returns boolean

// Method 3: fill() - Fill attributes (not save yet!)
$post = new Post();
$post->fill([
    'title' => 'Draft Post',
    'body' => 'Content...',
]);
// 🗣️ Only fills attributes, doesn't save
$post->save(); // Now save

// Method 4: firstOrCreate() - Find or create
$post = Post::firstOrCreate(
    ['slug' => 'unique-slug'],  // Search by this
    [
        'title' => 'New Post',   // If not found, create with this
        'body' => 'Content...',
    ]
);
// 🗣️ Cari dulu, kalau tidak ada baru create

// Method 5: updateOrCreate() - Update or create
$post = Post::updateOrCreate(
    ['slug' => 'my-post'],      // Search by this
    [
        'title' => 'Updated Title',  // Update/create with this
        'body' => 'New content',
    ]
);
// 🗣️ Kalau ada → update, kalau tidak → create

// ============================================
// FORCEFILL: Bypass Protection
// ============================================

// forceFill() ignores $fillable/$guarded
$post = new Post();
$post->forceFill([
    'user_id' => 5,   // Even if not in $fillable
    'views' => 1000,  // Even if guarded
])->save();
// ⚠️ Use only when you know what you're doing!
// Example: Seeding, admin operations

// ============================================
// REAL-WORLD CONTROLLER EXAMPLES
// ============================================

// Example 1: Safe create with validation
class PostController extends Controller
{
    public function store(Request $request)
    {
        // Step 1: Validate
        $validated = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'required|unique:posts',
            'body' => 'required',
            'is_published' => 'boolean',
        ]);

        // Step 2: Add fields yang tidak dari user
        $validated['user_id'] = auth()->id(); // Current user
        $validated['views'] = 0; // Default

        // Step 3: Create
        $post = Post::create($validated);

        return redirect()->route('posts.show', $post);
    }
}
// ✅ SAFE: Hanya validated fields yang di-save
// ✅ user_id dari auth, bukan dari user input

// Example 2: Partial update
public function update(Request $request, Post $post)
{
    $validated = $request->validate([
        'title' => 'sometimes|required|max:255',
        'body' => 'sometimes|required',
    ]);

    // Only update provided fields
    $post->update($validated);

    return redirect()->back();
}
// 🗣️ Kalau title tidak di-submit, tidak di-update

// Example 3: Admin vs User permissions
public function store(Request $request)
{
    $data = $request->validate([
        'title' => 'required',
        'body' => 'required',
    ]);

    // Auto-assign user_id
    $data['user_id'] = auth()->id();

    // Admin bisa set featured, user tidak
    if (auth()->user()->isAdmin()) {
        $data['is_featured'] = $request->boolean('is_featured');
    }

    $post = Post::create($data);

    return redirect()->route('posts.show', $post);
}

// ============================================
// COMMON PATTERNS
// ============================================

// Pattern 1: Merge with defaults
$data = array_merge([
    'views' => 0,
    'is_published' => false,
    'is_featured' => false,
], $request->validated());

$post = Post::create($data);
// 🗣️ Provide defaults, overridden by user input

// Pattern 2: Only specific fields
$post = Post::create($request->only([
    'title', 'slug', 'body'
]));
// 🗣️ Explicitly pick fields from request

// Pattern 3: Except specific fields
$post = Post::create($request->except([
    'admin_only_field', 'dangerous_field'
]));
// ⚠️ Less safe than only()

// ============================================
// TESTING MASS ASSIGNMENT
// ============================================

// Test in Tinker
php artisan tinker

// Without $fillable (will error!)
>>> $post = new App\Models\Post;
>>> $post->create(['title' => 'Test']);
Illuminate\Database\Eloquent\MassAssignmentException

// After adding $fillable
>>> $post = App\Models\Post::create(['title' => 'Test', 'body' => 'Content']);
=> App\Models\Post {#...}

// Check what's fillable
>>> $post->getFillable();
=> ["title", "slug", "body", "is_published"]

// Check what's guarded
>>> $post->getGuarded();
=> ["*"] // or specific columns

// ============================================
// TIMESTAMPS & MASS ASSIGNMENT
// ============================================

// created_at & updated_at NEVER need to be in $fillable
// Laravel handles them automatically!

class Post extends Model
{
    protected $fillable = ['title', 'body'];
    // created_at & updated_at excluded
    // But will still be filled automatically!
}

$post = Post::create(['title' => 'Test']);
echo $post->created_at; // ✅ Filled automatically!

// Disable timestamps if not needed
public $timestamps = false;

// ============================================
// BEST PRACTICES
// ============================================

/*
1. ✅ ALWAYS use $fillable in production
   protected $fillable = ['title', 'body', ...];

2. ✅ NEVER use $request->all() directly
   BAD:  Post::create($request->all());
   GOOD: Post::create($request->validated());

3. ✅ Validate before mass assign
   $validated = $request->validate([...]);
   Post::create($validated);

4. ✅ Set sensitive fields manually
   $data['user_id'] = auth()->id();
   $data['ip_address'] = $request->ip();

5. ✅ Review $fillable regularly
   When adding new columns, update $fillable!

6. ⚠️ Never put these in $fillable:
   - id
   - created_at, updated_at
   - user_id (set manually)
   - admin flags
   - calculated fields

7. ✅ Use Form Requests untuk complex validation
   php artisan make:request StorePostRequest
*/

// ============================================
// FORM REQUEST EXAMPLE (Advanced)
// ============================================

// app/Http/Requests/StorePostRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'title' => 'required|max:255',
            'slug' => 'required|unique:posts',
            'body' => 'required|min:100',
            'category_id' => 'required|exists:categories,id',
        ];
    }

    // Hanya return safe data
    public function safeData()
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'body' => $this->body,
            'category_id' => $this->category_id,
            'user_id' => auth()->id(), // Auto-add
        ];
    }
}

// Controller
public function store(StorePostRequest $request)
{
    $post = Post::create($request->safeData());
    return redirect()->route('posts.show', $post);
}
// ✅ Very secure! Validation + Safe mass assignment

echo "\n✅ Mass Assignment secured!\n";
