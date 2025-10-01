<?php

/**
 * ============================================
 * SOFT DELETES
 * ============================================
 *
 * Soft Delete = Data tidak benar-benar dihapus
 * Hanya ditandai sebagai "deleted"
 *
 * Benefits:
 * - Data recovery (undo delete)
 * - Audit trail (track who deleted when)
 * - Safe delete (prevent accidental loss)
 * - "Recycle Bin" feature
 */

use App\Models\Post;
use Illuminate\Database\Eloquent\SoftDeletes;

// ============================================
// SETUP SOFT DELETES
// ============================================

// Step 1: Add column to migration
// database/migrations/xxxx_create_posts_table.php
public function up()
{
    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('body');
        $table->timestamps();
        $table->softDeletes(); // ← Add this!
        // Creates: deleted_at (TIMESTAMP NULL)
    });
}

// Step 2: Add trait to Model
// app/Models/Post.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes; // ← Add this trait!

    protected $fillable = ['title', 'body'];
}

// ============================================
// HOW IT WORKS
// ============================================

// Database before delete:
/*
+----+---------+---------------------+------------+
| id | title   | created_at          | deleted_at |
+----+---------+---------------------+------------+
| 1  | Post 1  | 2024-01-01 10:00:00 | NULL       | ← Active
| 2  | Post 2  | 2024-01-02 11:00:00 | NULL       | ← Active
+----+---------+---------------------+------------+
*/

// Delete post:
$post = Post::find(1);
$post->delete();

// Database after delete:
/*
+----+---------+---------------------+---------------------+
| id | title   | created_at          | deleted_at          |
+----+---------+---------------------+---------------------+
| 1  | Post 1  | 2024-01-01 10:00:00 | 2024-01-15 14:30:00 | ← Soft deleted
| 2  | Post 2  | 2024-01-02 11:00:00 | NULL                | ← Active
+----+---------+---------------------+---------------------+
*/

// 🗣️ Row masih ada! Hanya deleted_at diisi timestamp

// ============================================
// QUERYING DATA
// ============================================

// Normal query (excludes soft deleted)
$posts = Post::all();
// 🗣️ Returns only: Post 2
// Post 1 hidden (karena deleted_at not null)

$post = Post::find(1);
// 🗣️ Returns: null (Post 1 treated as not found)

// Include soft deleted
$posts = Post::withTrashed()->get();
// 🗣️ Returns: Post 1 AND Post 2

$post = Post::withTrashed()->find(1);
// 🗣️ Returns: Post 1 (even though deleted)

// Only soft deleted
$posts = Post::onlyTrashed()->get();
// 🗣️ Returns: Post 1 only

// ============================================
// DELETE OPERATIONS
// ============================================

// Soft delete (normal delete)
$post = Post::find(2);
$post->delete();
// Sets deleted_at = now()
// Row still in database

// Force delete (permanent!)
$post = Post::withTrashed()->find(1);
$post->forceDelete();
// ⚠️ Row completely removed from database!
// Cannot be recovered!

// Restore deleted post
$post = Post::withTrashed()->find(1);
$post->restore();
// Sets deleted_at = NULL
// Post active again!

// ============================================
// CHECKING STATUS
// ============================================

$post = Post::withTrashed()->find(1);

// Check if soft deleted
if ($post->trashed()) {
    echo "This post is deleted";
}

// Check if active
if (!$post->trashed()) {
    echo "This post is active";
}

// ============================================
// CONTROLLER EXAMPLES
// ============================================

// Example 1: Soft delete post
public function destroy(Post $post)
{
    $post->delete(); // Soft delete

    return redirect()->route('posts.index')
        ->with('success', 'Post moved to trash');
}

// Example 2: Show trash page
public function trash()
{
    $posts = Post::onlyTrashed()
                 ->latest('deleted_at')
                 ->paginate(15);

    return view('posts.trash', compact('posts'));
}

// Example 3: Restore from trash
public function restore($id)
{
    $post = Post::onlyTrashed()->findOrFail($id);
    $post->restore();

    return redirect()->route('posts.index')
        ->with('success', 'Post restored!');
}

// Example 4: Permanent delete
public function forceDelete($id)
{
    $post = Post::onlyTrashed()->findOrFail($id);
    $post->forceDelete(); // ⚠️ Permanent!

    return redirect()->route('posts.trash')
        ->with('success', 'Post permanently deleted');
}

// Example 5: Empty trash (delete all)
public function emptyTrash()
{
    Post::onlyTrashed()->forceDelete(); // ⚠️ Delete all soft-deleted posts!

    return redirect()->route('posts.trash')
        ->with('success', 'Trash emptied');
}

// ============================================
// ROUTES
// ============================================

// routes/web.php
Route::resource('posts', PostController::class);

Route::prefix('posts')->name('posts.')->group(function () {
    Route::get('trash', [PostController::class, 'trash'])
         ->name('trash');

    Route::post('{id}/restore', [PostController::class, 'restore'])
         ->name('restore');

    Route::delete('{id}/force-delete', [PostController::class, 'forceDelete'])
         ->name('force-delete');

    Route::delete('trash/empty', [PostController::class, 'emptyTrash'])
         ->name('empty-trash');
});

// ============================================
// BLADE VIEWS
// ============================================

// posts/index.blade.php
<div>
    <a href="{{ route('posts.trash') }}" class="btn">
        View Trash ({{ Post::onlyTrashed()->count() }})
    </a>
</div>

@foreach($posts as $post)
    <div>
        <h3>{{ $post->title }}</h3>
        <form action="{{ route('posts.destroy', $post) }}" method="POST">
            @csrf
            @method('DELETE')
            <button>Move to Trash</button>
        </form>
    </div>
@endforeach

// posts/trash.blade.php
<h1>Trash</h1>

@foreach($posts as $post)
    <div class="deleted-post">
        <h3>{{ $post->title }}</h3>
        <small>Deleted: {{ $post->deleted_at->diffForHumans() }}</small>

        <form action="{{ route('posts.restore', $post) }}" method="POST">
            @csrf
            <button>Restore</button>
        </form>

        <form action="{{ route('posts.force-delete', $post) }}" method="POST"
              onsubmit="return confirm('Permanently delete? Cannot be undone!')">
            @csrf
            @method('DELETE')
            <button class="danger">Delete Forever</button>
        </form>
    </div>
@endforeach

<form action="{{ route('posts.empty-trash') }}" method="POST"
      onsubmit="return confirm('Empty trash? All deleted posts will be gone forever!')">
    @csrf
    @method('DELETE')
    <button>Empty Trash</button>
</form>

// ============================================
// RELATIONSHIPS & SOFT DELETES
// ============================================

// If Post has Comments, and Post is soft deleted:

// Option 1: Keep comments (default)
$post->delete(); // Post soft deleted
$post->comments; // Still accessible

// Option 2: Cascade soft delete to comments
// Post Model:
public function comments()
{
    return $this->hasMany(Comment::class);
}

// In Post delete event:
protected static function booted()
{
    static::deleting(function ($post) {
        $post->comments()->delete(); // Soft delete comments too
    });

    static::restoring(function ($post) {
        $post->comments()->restore(); // Restore comments too
    });
}

// ============================================
// BEST PRACTICES
// ============================================

/*
✅ Use for user-generated content
   (Posts, comments, files)

✅ Add trash page UI
   Let users recover mistakes

✅ Auto-cleanup old trash
   Permanent delete after 30 days

✅ Log deletions
   Track who deleted what when

⚠️ Don't use for everything
   Not needed for lookup tables (categories, tags)

⚠️ Performance consideration
   Queries get WHERE deleted_at IS NULL clause
   Add index: $table->index('deleted_at');
*/

// Auto cleanup old trash (scheduled task):
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        // Delete posts in trash > 30 days
        Post::onlyTrashed()
            ->where('deleted_at', '<', now()->subDays(30))
            ->forceDelete();
    })->daily();
}

echo "\n✅ Soft Deletes mastered!\n";
