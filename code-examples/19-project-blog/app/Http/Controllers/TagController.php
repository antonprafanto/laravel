<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of all tags
     */
    public function index()
    {
        $tags = Tag::withCount('posts')
                   ->orderBy('name')
                   ->get();

        return view('tags.index', compact('tags'));
    }

    /**
     * Display posts by tag
     */
    public function show(Tag $tag)
    {
        $posts = $tag->posts()
                     ->with(['category', 'user', 'tags'])
                     ->latest()
                     ->paginate(10);

        return view('posts.index', compact('posts', 'tag'));
    }
}
