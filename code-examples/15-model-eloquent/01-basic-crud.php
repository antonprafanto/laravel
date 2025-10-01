<?php

/**
 * ============================================
 * ELOQUENT BASIC CRUD OPERATIONS
 * ============================================
 *
 * File ini mendemonstrasikan operasi dasar Eloquent:
 * - CREATE: Membuat data baru
 * - READ: Membaca/query data
 * - UPDATE: Mengubah data existing
 * - DELETE: Menghapus data
 */

use App\Models\Post;

// ============================================
// CREATE - Membuat Data Baru
// ============================================

// Method 1: create() - Mass Assignment (Recommended)
$post = Post::create([
    'title' => 'Tutorial Laravel Eloquent',
    'slug' => 'tutorial-laravel-eloquent',
    'body' => 'Eloquent adalah ORM yang powerful di Laravel...',
    'is_published' => true,
]);

echo "Post created with ID: {$post->id}\n";

// Method 2: new + save()
$post2 = new Post();
$post2->title = 'Belajar PHP 8';
$post2->slug = 'belajar-php-8';
$post2->body = 'PHP 8 membawa banyak fitur baru...';
$post2->save();

echo "Post2 created with ID: {$post2->id}\n";

// Method 3: firstOrCreate() - Create jika belum ada
$post3 = Post::firstOrCreate(
    ['slug' => 'unique-slug'],  // Cari by slug
    [
        'title' => 'Post Unik',
        'body' => 'Ini akan dibuat jika slug belum ada',
    ]
);

// Method 4: updateOrCreate() - Update jika ada, create jika tidak
$post4 = Post::updateOrCreate(
    ['slug' => 'tutorial-vue'],
    [
        'title' => 'Tutorial Vue.js',
        'body' => 'Vue.js adalah framework JavaScript...',
    ]
);

// ============================================
// READ - Query Data
// ============================================

// Get ALL posts
$allPosts = Post::all();
echo "Total posts: " . $allPosts->count() . "\n";

// Get by ID
$post = Post::find(1);
if ($post) {
    echo "Found post: {$post->title}\n";
}

// Get by ID or fail (throws 404 if not found)
$post = Post::findOrFail(1);

// Get first result
$firstPost = Post::first();
echo "First post: {$firstPost->title}\n";

// Get with WHERE condition
$publishedPosts = Post::where('is_published', true)->get();
echo "Published posts: " . $publishedPosts->count() . "\n";

// Find by specific column
$post = Post::where('slug', 'tutorial-laravel')->first();

// Multiple WHERE conditions
$posts = Post::where('is_published', true)
             ->where('created_at', '>=', now()->subDays(7))
             ->get();

// WHERE with operators
$popularPosts = Post::where('views', '>', 1000)->get();
$recentPosts = Post::where('created_at', '>=', now()->subMonth())->get();

// ORDER BY
$latestPosts = Post::orderBy('created_at', 'desc')->get();
$oldestPosts = Post::orderBy('created_at', 'asc')->get();

// Shorthand untuk orderBy created_at
$latestPosts = Post::latest()->get();
$oldestPosts = Post::oldest()->get();

// LIMIT & OFFSET
$top5 = Post::limit(5)->get();
$skip10take5 = Post::skip(10)->take(5)->get();

// Get single value (pluck)
$titles = Post::pluck('title');
$slugs = Post::pluck('slug', 'id'); // Key-value pair

// COUNT, SUM, AVG, MIN, MAX
$count = Post::count();
$totalViews = Post::sum('views');
$avgViews = Post::avg('views');
$minViews = Post::min('views');
$maxViews = Post::max('views');

echo "Total posts: {$count}\n";
echo "Average views: {$avgViews}\n";

// EXISTS - Check if record exists
$exists = Post::where('slug', 'tutorial-laravel')->exists();
if ($exists) {
    echo "Post exists!\n";
}

// ============================================
// UPDATE - Mengubah Data
// ============================================

// Method 1: find() + update()
$post = Post::find(1);
$post->update([
    'title' => 'Updated Title',
    'body' => 'Updated content...',
]);

// Method 2: find() + assign + save()
$post = Post::find(1);
$post->title = 'Another Update';
$post->save();

// Method 3: Mass update (multiple records)
Post::where('is_published', false)
    ->update(['is_published' => true]);

// Increment/Decrement
$post = Post::find(1);
$post->increment('views'); // views + 1
$post->decrement('likes'); // likes - 1
$post->increment('views', 5); // views + 5

// ============================================
// DELETE - Menghapus Data
// ============================================

// Method 1: find() + delete()
$post = Post::find(1);
$post->delete();

// Method 2: destroy() by ID
Post::destroy(1);

// Delete multiple IDs
Post::destroy([1, 2, 3]);

// Delete with WHERE
Post::where('is_published', false)->delete();

// ============================================
// QUERY CHAINING
// ============================================

// Combine multiple methods
$posts = Post::where('is_published', true)
             ->where('views', '>', 100)
             ->orderBy('created_at', 'desc')
             ->limit(10)
             ->get();

// Complex query
$posts = Post::where('created_at', '>=', now()->subWeek())
             ->orWhere('views', '>', 1000)
             ->orderBy('views', 'desc')
             ->take(5)
             ->get();

// ============================================
// HELPFUL TIPS
// ============================================

/*
1. Selalu gunakan fillable di Model untuk Mass Assignment
2. Gunakan findOrFail() untuk auto 404 error
3. Gunakan exists() untuk check existence (lebih efisien dari count())
4. Chain methods untuk query yang clean & readable
5. Gunakan latest()/oldest() shorthand
*/

echo "\n✅ Basic CRUD operations completed!\n";
