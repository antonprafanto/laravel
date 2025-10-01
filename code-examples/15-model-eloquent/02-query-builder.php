<?php

/**
 * ============================================
 * ELOQUENT QUERY BUILDER
 * ============================================
 *
 * Query Builder adalah tool untuk build SQL queries
 * dengan PHP syntax yang mudah dan readable.
 *
 * Keuntungan:
 * - No raw SQL needed
 * - Chainable methods
 * - Database agnostic
 * - Injection-safe
 */

use App\Models\Post;

// ============================================
// WHERE CLAUSES
// ============================================

// Simple WHERE
$posts = Post::where('is_published', true)->get();
// 🗣️ SELECT * FROM posts WHERE is_published = 1

$posts = Post::where('title', 'Laravel Tips')->get();
// 🗣️ SELECT * FROM posts WHERE title = 'Laravel Tips'

// WHERE with operator
$posts = Post::where('views', '>', 1000)->get();
// 🗣️ SELECT * FROM posts WHERE views > 1000

$posts = Post::where('created_at', '>=', now()->subDays(7))->get();
// 🗣️ Posts created in last 7 days

// Multiple WHERE (AND)
$posts = Post::where('is_published', true)
             ->where('views', '>', 100)
             ->get();
// 🗣️ WHERE is_published = 1 AND views > 100

// WHERE OR
$posts = Post::where('category_id', 1)
             ->orWhere('category_id', 2)
             ->get();
// 🗣️ WHERE category_id = 1 OR category_id = 2

// WHERE with closure (grouping)
$posts = Post::where('is_published', true)
             ->where(function($query) {
                 $query->where('category_id', 1)
                       ->orWhere('category_id', 2);
             })
             ->get();
// 🗣️ WHERE is_published = 1 AND (category_id = 1 OR category_id = 2)

// ============================================
// WHERE ADVANCED
// ============================================

// WHERE IN
$posts = Post::whereIn('category_id', [1, 2, 3])->get();
// 🗣️ WHERE category_id IN (1, 2, 3)

// WHERE NOT IN
$posts = Post::whereNotIn('id', [5, 10, 15])->get();
// 🗣️ WHERE id NOT IN (5, 10, 15)

// WHERE BETWEEN
$posts = Post::whereBetween('views', [100, 1000])->get();
// 🗣️ WHERE views BETWEEN 100 AND 1000

// WHERE NOT BETWEEN
$posts = Post::whereNotBetween('views', [0, 50])->get();
// 🗣️ WHERE views NOT BETWEEN 0 AND 50

// WHERE NULL
$posts = Post::whereNull('published_at')->get();
// 🗣️ WHERE published_at IS NULL (drafts)

// WHERE NOT NULL
$posts = Post::whereNotNull('published_at')->get();
// 🗣️ WHERE published_at IS NOT NULL (published)

// WHERE DATE
$posts = Post::whereDate('created_at', '2024-01-01')->get();
// 🗣️ Posts created on specific date

// WHERE MONTH
$posts = Post::whereMonth('created_at', 12)->get();
// 🗣️ Posts created in December

// WHERE YEAR
$posts = Post::whereYear('created_at', 2024)->get();
// 🗣️ Posts created in 2024

// WHERE LIKE (Search)
$posts = Post::where('title', 'LIKE', '%Laravel%')->get();
// 🗣️ WHERE title LIKE '%Laravel%'

// ============================================
// ORDER BY
// ============================================

// ORDER BY ascending
$posts = Post::orderBy('title', 'asc')->get();
// 🗣️ ORDER BY title ASC (A to Z)

// ORDER BY descending
$posts = Post::orderBy('created_at', 'desc')->get();
// 🗣️ ORDER BY created_at DESC (newest first)

// Shorthand: latest()
$posts = Post::latest()->get();
// 🗣️ Same as: orderBy('created_at', 'desc')

// Shorthand: oldest()
$posts = Post::oldest()->get();
// 🗣️ Same as: orderBy('created_at', 'asc')

// latest() with custom column
$posts = Post::latest('published_at')->get();
// 🗣️ ORDER BY published_at DESC

// Multiple ORDER BY
$posts = Post::orderBy('is_featured', 'desc')
             ->orderBy('created_at', 'desc')
             ->get();
// 🗣️ Featured posts first, then by date

// Random order
$posts = Post::inRandomOrder()->limit(5)->get();
// 🗣️ 5 random posts

// ============================================
// LIMIT & OFFSET
// ============================================

// LIMIT
$posts = Post::limit(10)->get();
// 🗣️ SELECT * FROM posts LIMIT 10

// Shorthand: take()
$posts = Post::take(5)->get();
// 🗣️ Same as limit(5)

// OFFSET + LIMIT
$posts = Post::offset(10)->limit(5)->get();
// 🗣️ Skip 10, take 5 (rows 11-15)

// Shorthand: skip() + take()
$posts = Post::skip(10)->take(5)->get();
// 🗣️ Same as above

// ============================================
// AGGREGATES
// ============================================

// COUNT
$count = Post::count();
// 🗣️ How many posts total?
echo "Total posts: {$count}";

// COUNT with WHERE
$publishedCount = Post::where('is_published', true)->count();
echo "Published: {$publishedCount}";

// SUM
$totalViews = Post::sum('views');
// 🗣️ Total views across all posts
echo "Total views: {$totalViews}";

// AVG (Average)
$avgViews = Post::avg('views');
// 🗣️ Average views per post
echo "Average views: " . round($avgViews, 2);

// MIN
$minViews = Post::min('views');
// 🗣️ Lowest view count
echo "Minimum views: {$minViews}";

// MAX
$maxViews = Post::max('views');
// 🗣️ Highest view count
echo "Maximum views: {$maxViews}";

// ============================================
// SELECT SPECIFIC COLUMNS
// ============================================

// Select all columns (default)
$posts = Post::get(); // or all()
// 🗣️ SELECT * FROM posts

// Select specific columns
$posts = Post::select('id', 'title', 'created_at')->get();
// 🗣️ SELECT id, title, created_at FROM posts

// Select with alias
$posts = Post::select('id', 'title as post_title')->get();
// 🗣️ Column 'title' becomes 'post_title'

// Add more columns to selection
$posts = Post::select('id', 'title')
             ->addSelect('body')
             ->get();

// ============================================
// DISTINCT
// ============================================

// Get unique values
$categories = Post::distinct()->pluck('category_id');
// 🗣️ SELECT DISTINCT category_id

// ============================================
// GROUP BY & HAVING
// ============================================

// Group by category, count posts
$stats = Post::select('category_id')
             ->selectRaw('COUNT(*) as total')
             ->groupBy('category_id')
             ->get();
// 🗣️ Posts count per category

// With HAVING
$popularCategories = Post::select('category_id')
                         ->selectRaw('COUNT(*) as total')
                         ->groupBy('category_id')
                         ->having('total', '>', 10)
                         ->get();
// 🗣️ Categories with more than 10 posts

// ============================================
// PLUCK (Get column values)
// ============================================

// Get array of titles
$titles = Post::pluck('title');
// 🗣️ Returns: ['Title 1', 'Title 2', 'Title 3']

// Get key-value pairs
$postTitles = Post::pluck('title', 'id');
// 🗣️ Returns: [1 => 'Title 1', 2 => 'Title 2', 3 => 'Title 3']

// Usage
foreach ($postTitles as $id => $title) {
    echo "{$id}: {$title}\n";
}

// ============================================
// CHUNK (Process large datasets)
// ============================================

// Process in chunks of 100
Post::chunk(100, function($posts) {
    foreach ($posts as $post) {
        // Process each post
        echo $post->title . "\n";
    }
});
// 🗣️ Bagus untuk table dengan jutaan rows
// Memory efficient!

// Chunk with WHERE
Post::where('is_published', true)
    ->chunk(100, function($posts) {
        // Process published posts
    });

// ============================================
// CURSOR (For huge datasets)
// ============================================

// Memory-efficient iteration
foreach (Post::cursor() as $post) {
    echo $post->title . "\n";
}
// 🗣️ Hanya load 1 row at a time
// Perfect untuk millions of rows

// ============================================
// EXISTS / DOESN'T EXIST
// ============================================

// Check if exists
$exists = Post::where('slug', 'laravel-tips')->exists();
// 🗣️ Returns true/false
// More efficient than count() > 0

if ($exists) {
    echo "Post exists!";
}

// Check if doesn't exist
$notExists = Post::where('slug', 'nonexistent')->doesntExist();
// 🗣️ Opposite of exists()

// ============================================
// WHEN (Conditional Query)
// ============================================

// Apply condition based on variable
$search = request('search');

$posts = Post::when($search, function($query) use ($search) {
    $query->where('title', 'LIKE', "%{$search}%");
})->get();

// 🗣️ Jika $search ada → apply WHERE
// Jika $search null/empty → skip WHERE

// With else
$posts = Post::when($isPublished, function($query) {
    $query->where('is_published', true);
}, function($query) {
    // Else clause
    $query->where('is_published', false);
})->get();

// ============================================
// REAL-WORLD EXAMPLES
// ============================================

// Example 1: Blog post listing with filters
function getBlogPosts($request)
{
    $query = Post::query();

    // Filter by category
    if ($request->has('category')) {
        $query->where('category_id', $request->category);
    }

    // Search
    if ($request->has('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
              ->orWhere('body', 'LIKE', "%{$search}%");
        });
    }

    // Only published
    $query->where('is_published', true);

    // Order by featured first, then by date
    $query->orderBy('is_featured', 'desc')
          ->orderBy('published_at', 'desc');

    // Paginate
    return $query->paginate(15);
}

// Example 2: Dashboard statistics
function getDashboardStats()
{
    return [
        'total_posts' => Post::count(),
        'published' => Post::where('is_published', true)->count(),
        'drafts' => Post::where('is_published', false)->count(),
        'total_views' => Post::sum('views'),
        'avg_views' => Post::avg('views'),
        'popular_posts' => Post::orderBy('views', 'desc')->limit(5)->get(),
        'recent_posts' => Post::latest()->limit(5)->get(),
    ];
}

// Example 3: Monthly post count
function getMonthlyPostCount($year = 2024)
{
    return Post::whereYear('created_at', $year)
               ->selectRaw('MONTH(created_at) as month')
               ->selectRaw('COUNT(*) as total')
               ->groupBy('month')
               ->orderBy('month')
               ->get();
}

// ============================================
// PERFORMANCE TIPS
// ============================================

/*
1. ✅ Use select() untuk columns yang diperlukan saja
   BAD: Post::all(); // Load semua columns
   GOOD: Post::select('id', 'title')->get(); // Hanya yang perlu

2. ✅ Use exists() instead of count() > 0
   BAD: Post::where(...)->count() > 0
   GOOD: Post::where(...)->exists()

3. ✅ Use chunk() atau cursor() untuk large datasets
   BAD: Post::all(); // Load 1M rows ke memory!
   GOOD: Post::chunk(1000, function($posts) {...});

4. ✅ Add indexes untuk columns yang sering di-WHERE
   Migration: $table->index('category_id');

5. ✅ Eager load relationships (akan dibahas di chapter relationships)
   BAD: Post::all(); // N+1 problem
   GOOD: Post::with('category')->get();
*/

// ============================================
// DEBUGGING QUERIES
// ============================================

// Method 1: toSql()
$sql = Post::where('is_published', true)
           ->orderBy('created_at', 'desc')
           ->toSql();
echo $sql;
// Output: select * from `posts` where `is_published` = ? order by `created_at` desc

// Method 2: dd() the query
Post::where('is_published', true)->dd();
// Dumps SQL dan bindings

// Method 3: Enable query log
DB::enableQueryLog();
$posts = Post::where('is_published', true)->get();
dd(DB::getQueryLog());
// Shows all queries executed

echo "\n✅ Query Builder mastered!\n";
