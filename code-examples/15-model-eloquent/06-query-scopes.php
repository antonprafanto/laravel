<?php

/**
 * ============================================
 * ELOQUENT QUERY SCOPES
 * ============================================
 *
 * Query Scopes memungkinkan kita membuat query yang:
 * - Reusable (bisa dipakai berulang kali)
 * - Chainable (bisa digabung dengan scope lain)
 * - Readable (code lebih mudah dibaca)
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// ============================================
// MODEL dengan LOCAL SCOPES
// ============================================

class Post extends Model
{
    protected $fillable = ['title', 'slug', 'body', 'is_published', 'views', 'user_id'];

    /**
     * Scope: Filter only published posts
     *
     * Naming: scope + PascalCase
     * Usage: Post::published()->get()
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope: Filter draft posts
     */
    public function scopeDraft($query)
    {
        return $query->where('is_published', false);
    }

    /**
     * Scope: Filter popular posts (views > 1000)
     */
    public function scopePopular($query)
    {
        return $query->where('views', '>', 1000);
    }

    /**
     * Scope: Filter recent posts (last 7 days)
     */
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subDays(7));
    }

    /**
     * Scope: Filter posts by author
     *
     * Dynamic scope dengan parameter
     */
    public function scopeByAuthor($query, $authorId)
    {
        return $query->where('user_id', $authorId);
    }

    /**
     * Scope: Filter posts by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope: Search posts by keyword
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function($q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
              ->orWhere('body', 'like', "%{$keyword}%");
        });
    }

    /**
     * Scope: Order by most viewed
     */
    public function scopePopularFirst($query)
    {
        return $query->orderBy('views', 'desc');
    }

    /**
     * Scope: Filter featured posts
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}

// ============================================
// USAGE EXAMPLES - Controller/Tinker
// ============================================

use App\Models\Post;

// Basic usage
$publishedPosts = Post::published()->get();
$draftPosts = Post::draft()->get();
$popularPosts = Post::popular()->get();

// Chain multiple scopes
$posts = Post::published()
             ->popular()
             ->recent()
             ->get();

// Scopes dengan parameter
$userPosts = Post::byAuthor(1)->get();
$categoryPosts = Post::byCategory(5)->get();
$searchResults = Post::search('Laravel')->get();

// Combine scopes dengan query methods
$posts = Post::published()
             ->popular()
             ->orderBy('created_at', 'desc')
             ->limit(10)
             ->get();

// Complex chaining
$posts = Post::published()
             ->byCategory(3)
             ->search('tutorial')
             ->popularFirst()
             ->paginate(15);

// Conditional scopes
$query = Post::published();

if ($request->has('author')) {
    $query->byAuthor($request->author);
}

if ($request->has('search')) {
    $query->search($request->search);
}

$posts = $query->latest()->paginate(10);

// ============================================
// GLOBAL SCOPES
// ============================================

/**
 * Global Scope diterapkan ke SEMUA query secara otomatis
 *
 * Use case: Filter data by tenant, hide archived posts, etc.
 */

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

// 1. Create Global Scope Class
class PublishedScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('is_published', true);
    }
}

// 2. Apply di Model
class Post extends Model
{
    protected static function booted()
    {
        static::addGlobalScope(new PublishedScope);
    }

    // Atau inline global scope
    protected static function booted()
    {
        static::addGlobalScope('published', function (Builder $builder) {
            $builder->where('is_published', true);
        });
    }
}

// 3. Usage
$posts = Post::all(); // Automatically filters published posts only!

// Remove global scope jika perlu
$allPosts = Post::withoutGlobalScope(PublishedScope::class)->get();
$allPosts = Post::withoutGlobalScope('published')->get();

// ============================================
// ADVANCED SCOPE PATTERNS
// ============================================

class Post extends Model
{
    /**
     * Scope: Filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope: Filter by multiple tags
     */
    public function scopeWithTags($query, array $tagIds)
    {
        return $query->whereHas('tags', function($q) use ($tagIds) {
            $q->whereIn('tags.id', $tagIds);
        });
    }

    /**
     * Scope: Order by custom logic
     */
    public function scopeOrderByPopularity($query)
    {
        return $query->orderByRaw('(views + likes * 2 + comments * 3) DESC');
    }

    /**
     * Scope: Filter trending posts (popular + recent)
     */
    public function scopeTrending($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days))
                     ->where('views', '>', 100)
                     ->orderBy('views', 'desc');
    }
}

// Usage
$posts = Post::dateRange('2024-01-01', '2024-12-31')->get();
$posts = Post::withTags([1, 2, 3])->get();
$posts = Post::orderByPopularity()->limit(10)->get();
$trendingPosts = Post::trending(30)->get();

// ============================================
// REAL-WORLD EXAMPLE
// ============================================

// Controller method
public function index(Request $request)
{
    $posts = Post::published()
                 ->when($request->category, function($query) use ($request) {
                     $query->byCategory($request->category);
                 })
                 ->when($request->search, function($query) use ($request) {
                     $query->search($request->search);
                 })
                 ->when($request->author, function($query) use ($request) {
                     $query->byAuthor($request->author);
                 })
                 ->latest()
                 ->paginate(15);

    return view('posts.index', compact('posts'));
}

// ============================================
// BENEFITS OF SCOPES
// ============================================

/*
✅ GOOD - With Scopes:
$posts = Post::published()->popular()->recent()->get();

❌ BAD - Without Scopes:
$posts = Post::where('is_published', true)
             ->where('views', '>', 1000)
             ->where('created_at', '>=', now()->subDays(7))
             ->get();

Benefits:
1. More readable code
2. Reusable logic
3. Easier to test
4. Chainable methods
5. Less code duplication
*/

echo "\n✅ Query Scopes mastered!\n";
