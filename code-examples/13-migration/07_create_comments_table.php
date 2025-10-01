<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================
 * MIGRATION: Create Comments Table
 * ============================================
 *
 * Comments table dengan Polymorphic Relationship.
 *
 * Polymorphic = "Banyak bentuk"
 * Artinya: Comments bisa untuk berbagai model
 * - Comment untuk Post
 * - Comment untuk Video
 * - Comment untuk Photo
 * - dll.
 *
 * Tanpa perlu buat table terpisah untuk masing-masing!
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Comment Content
            $table->text('body');
            // 🗣️ Isi comment dari user
            // TEXT karena bisa panjang

            // Author (User yang comment)
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');
            // 🗣️ Siapa yang buat comment ini?
            // Jika user dihapus → comments ikut terhapus

            // Polymorphic Relationship Columns
            $table->morphs('commentable');
            // 🗣️ Magic method! Creates 2 columns:
            // 1. commentable_id (BIGINT UNSIGNED)
            // 2. commentable_type (VARCHAR 255)
            //
            // commentable_id = ID dari model (1, 2, 3, ...)
            // commentable_type = Nama model ('App\Models\Post', 'App\Models\Video')

            // Alternative manual way (sama aja):
            // $table->unsignedBigInteger('commentable_id');
            // $table->string('commentable_type');
            // $table->index(['commentable_id', 'commentable_type']);

            // Timestamps
            $table->timestamps();

            // Indexes untuk performa
            // morphs() sudah auto create index, tapi kita explicit
            $table->index(['commentable_type', 'commentable_id']);
            // 🗣️ Untuk query: Get all comments untuk Post ID 5
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};

/**
 * ============================================
 * APA ITU POLYMORPHIC RELATIONSHIP?
 * ============================================
 *
 * Polymorphic = 1 table bisa relate ke BANYAK models berbeda.
 *
 * Analogi Sederhana:
 * Seperti "Like" button di Facebook
 * - Bisa like Post
 * - Bisa like Photo
 * - Bisa like Comment
 * - Bisa like Video
 * → 1 table "likes" untuk semua!
 *
 * Tanpa Polymorphic (❌ BAD):
 * - post_comments table
 * - video_comments table
 * - photo_comments table
 * → Banyak table dengan struktur sama!
 *
 * Dengan Polymorphic (✅ GOOD):
 * - comments table (satu untuk semua!)
 *
 * ============================================
 * CARA KERJA POLYMORPHIC
 * ============================================
 *
 * Data di comments table:
 *
 * +----+-------------+---------+------------------+-------------------+
 * | id | body        | user_id | commentable_id   | commentable_type  |
 * +----+-------------+---------+------------------+-------------------+
 * | 1  | Nice post!  | 5       | 1                | App\Models\Post   |
 * | 2  | Great video!| 3       | 1                | App\Models\Video  |
 * | 3  | Cool pic!   | 7       | 2                | App\Models\Photo  |
 * | 4  | Amazing!    | 5       | 1                | App\Models\Post   |
 * +----+-------------+---------+------------------+-------------------+
 *
 * Baca tabel di atas:
 * Row 1: Comment untuk Post #1
 * Row 2: Comment untuk Video #1
 * Row 3: Comment untuk Photo #2
 * Row 4: Comment untuk Post #1 (post yang sama, user berbeda)
 *
 * commentable_id → ID dari model
 * commentable_type → Nama class model
 *
 * ============================================
 * MORPHS() METHOD
 * ============================================
 *
 * $table->morphs('commentable');
 *
 * Ini shortcut untuk:
 * 1. Buat 2 columns:
 *    - commentable_id (BIGINT UNSIGNED)
 *    - commentable_type (VARCHAR 255)
 *
 * 2. Buat index:
 *    - Index pada (commentable_type, commentable_id)
 *
 * Nama 'commentable' bisa apa saja:
 * $table->morphs('taggable');   → taggable_id, taggable_type
 * $table->morphs('likeable');   → likeable_id, likeable_type
 * $table->morphs('reviewable'); → reviewable_id, reviewable_type
 *
 * Best practice naming:
 * - Akhiri dengan 'able'
 * - Describe the action/relationship
 * - commentable = "bisa di-comment"
 * - likeable = "bisa di-like"
 *
 * ============================================
 * MODEL SETUP
 * ============================================
 *
 * // Comment Model
 * class Comment extends Model
 * {
 *     protected $fillable = ['body', 'user_id'];
 *
 *     // Polymorphic relationship
 *     public function commentable()
 *     {
 *         return $this->morphTo();
 *     }
 *
 *     // Author relationship
 *     public function user()
 *     {
 *         return $this->belongsTo(User::class);
 *     }
 * }
 *
 * // Post Model
 * class Post extends Model
 * {
 *     public function comments()
 *     {
 *         return $this->morphMany(Comment::class, 'commentable');
 *     }
 * }
 *
 * // Video Model
 * class Video extends Model
 * {
 *     public function comments()
 *     {
 *         return $this->morphMany(Comment::class, 'commentable');
 *     }
 * }
 *
 * // Photo Model
 * class Photo extends Model
 * {
 *     public function comments()
 *     {
 *         return $this->morphMany(Comment::class, 'commentable');
 *     }
 * }
 *
 * ============================================
 * USAGE EXAMPLES
 * ============================================
 *
 * // Get comments untuk Post
 * $post = Post::find(1);
 * $comments = $post->comments; // Collection of Comment models
 *
 * // Get comments untuk Video
 * $video = Video::find(1);
 * $comments = $video->comments;
 *
 * // Create comment untuk Post
 * $post->comments()->create([
 *     'body' => 'This is a great post!',
 *     'user_id' => auth()->id(),
 * ]);
 *
 * // Create comment untuk Video
 * $video->comments()->create([
 *     'body' => 'Love this video!',
 *     'user_id' => auth()->id(),
 * ]);
 *
 * // Get what the comment belongs to
 * $comment = Comment::find(1);
 * $item = $comment->commentable; // Returns Post or Video or Photo
 *
 * // Check type
 * if ($comment->commentable_type === 'App\Models\Post') {
 *     echo "This is a comment on a post";
 * }
 *
 * // Or better:
 * if ($comment->commentable instanceof Post) {
 *     echo "This is a comment on a post";
 * }
 *
 * ============================================
 * CONTROLLER EXAMPLE
 * ============================================
 *
 * // CommentController.php
 * public function store(Request $request, Post $post)
 * {
 *     $validated = $request->validate([
 *         'body' => 'required|min:3',
 *     ]);
 *
 *     // Create comment for this post
 *     $post->comments()->create([
 *         'body' => $validated['body'],
 *         'user_id' => auth()->id(),
 *     ]);
 *
 *     return back()->with('success', 'Comment added!');
 * }
 *
 * // Works for any model!
 * public function storeForVideo(Request $request, Video $video)
 * {
 *     $validated = $request->validate([
 *         'body' => 'required|min:3',
 *     ]);
 *
 *     $video->comments()->create([
 *         'body' => $validated['body'],
 *         'user_id' => auth()->id(),
 *     ]);
 *
 *     return back()->with('success', 'Comment added!');
 * }
 *
 * ============================================
 * BLADE TEMPLATE EXAMPLE
 * ============================================
 *
 * {{-- Display comments --}}
 * <div class="comments">
 *     <h3>Comments ({{ $post->comments->count() }})</h3>
 *
 *     @forelse($post->comments as $comment)
 *         <div class="comment">
 *             <strong>{{ $comment->user->name }}</strong>
 *             <span class="text-muted">
 *                 {{ $comment->created_at->diffForHumans() }}
 *             </span>
 *             <p>{{ $comment->body }}</p>
 *         </div>
 *     @empty
 *         <p>No comments yet. Be the first to comment!</p>
 *     @endforelse
 * </div>
 *
 * {{-- Comment form --}}
 * <form action="{{ route('posts.comments.store', $post) }}" method="POST">
 *     @csrf
 *     <textarea name="body" rows="3" required></textarea>
 *     <button type="submit">Post Comment</button>
 * </form>
 *
 * ============================================
 * ADVANCED: NESTED COMMENTS (REPLIES)
 * ============================================
 *
 * Untuk comment replies (comment pada comment):
 *
 * // Add to migration
 * $table->foreignId('parent_id')->nullable()->constrained('comments');
 *
 * // Comment Model
 * public function replies()
 * {
 *     return $this->hasMany(Comment::class, 'parent_id');
 * }
 *
 * public function parent()
 * {
 *     return $this->belongsTo(Comment::class, 'parent_id');
 * }
 *
 * // Usage
 * $comment->replies; // Get all replies
 * $reply->parent; // Get parent comment
 *
 * ============================================
 * WHY USE POLYMORPHIC?
 * ============================================
 *
 * Advantages:
 * ✅ DRY (Don't Repeat Yourself)
 * ✅ Single source of truth
 * ✅ Easy to add new commentable models
 * ✅ Consistent structure
 * ✅ Less database tables
 *
 * Disadvantages:
 * ⚠️ Slightly more complex queries
 * ⚠️ Cannot use traditional foreign key constraints
 * ⚠️ Type stored as string (not ideal for performance)
 *
 * When to use:
 * ✅ Feature applies to multiple models
 * ✅ Structure is identical across models
 * ✅ Examples: comments, likes, tags, media, reviews
 *
 * When NOT to use:
 * ❌ Only 1-2 models need it
 * ❌ Different structure per model
 * ❌ Complex queries needed
 *
 * ============================================
 * EAGER LOADING (Performance)
 * ============================================
 *
 * // ❌ N+1 Problem
 * $posts = Post::all();
 * foreach ($posts as $post) {
 *     echo $post->comments->count(); // Query per post!
 * }
 *
 * // ✅ Solution: Eager load
 * $posts = Post::with('comments')->get();
 * foreach ($posts as $post) {
 *     echo $post->comments->count(); // No extra queries!
 * }
 *
 * // With user info too
 * $posts = Post::with(['comments.user'])->get();
 *
 * // Count only
 * $posts = Post::withCount('comments')->get();
 * echo $posts[0]->comments_count; // No loading comments, just count
 *
 * ============================================
 * BEST PRACTICES
 * ============================================
 *
 * 1. ✅ Use morphs() method
 *    Cleaner than manual columns
 *
 * 2. ✅ Name wisely (use 'able' suffix)
 *    commentable, likeable, taggable
 *
 * 3. ✅ Add cascade delete
 *    If user deleted → delete their comments
 *
 * 4. ✅ Eager load to avoid N+1
 *    ->with('comments.user')
 *
 * 5. ✅ Add validation
 *    Minimum length, no empty comments
 *
 * 6. ✅ Consider soft deletes
 *    Allow comment restoration
 *
 * 7. ⚠️ Add moderation for public sites
 *    Spam protection, approval workflow
 */
