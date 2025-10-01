<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Apply auth middleware except for index & show
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of posts with search & pagination
     */
    public function index(Request $request)
    {
        $posts = Post::with(['category', 'tags', 'user'])
                     ->search($request->search)
                     ->latest()
                     ->paginate(10)
                     ->withQueryString(); // Keep search query in pagination links

        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new post
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('posts.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created post in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'nullable|unique:posts,slug',
            'body' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'is_published' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        // Auto-assign user_id
        $validated['user_id'] = auth()->id();

        // Set published_at if published
        if ($request->is_published) {
            $validated['published_at'] = now();
        }

        // Create post
        $post = Post::create($validated);

        // Attach tags (many-to-many)
        if ($request->tags) {
            $post->tags()->attach($request->tags);
        }

        return redirect()->route('posts.index')
                         ->with('success', 'Post berhasil dibuat! 🎉');
    }

    /**
     * Display the specified post
     */
    public function show(Post $post)
    {
        // Eager load relationships
        $post->load(['category', 'tags', 'user']);

        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified post
     */
    public function edit(Post $post)
    {
        // Check authorization
        $this->authorize('update', $post);

        $categories = Category::all();
        $tags = Tag::all();

        return view('posts.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Update the specified post in storage
     */
    public function update(Request $request, Post $post)
    {
        // Check authorization
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'nullable|unique:posts,slug,' . $post->id,
            'body' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'is_published' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }

            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        // Update published_at if status changed
        if ($request->is_published && !$post->is_published) {
            $validated['published_at'] = now();
        }

        // Update post
        $post->update($validated);

        // Sync tags (replace all tags)
        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        } else {
            $post->tags()->detach(); // Remove all tags
        }

        return redirect()->route('posts.index')
                         ->with('success', 'Post berhasil diupdate! ✅');
    }

    /**
     * Remove the specified post from storage
     */
    public function destroy(Post $post)
    {
        // Check authorization
        $this->authorize('delete', $post);

        try {
            // Delete image if exists
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }

            // Detach all tags
            $post->tags()->detach();

            // Delete post
            $post->delete();

            return redirect()->route('posts.index')
                             ->with('success', 'Post berhasil dihapus! 🗑️');
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Gagal menghapus post: ' . $e->getMessage());
        }
    }
}
