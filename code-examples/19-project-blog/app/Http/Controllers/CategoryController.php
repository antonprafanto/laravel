<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of all categories
     */
    public function index()
    {
        $categories = Category::withCount('posts')
                              ->orderBy('name')
                              ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Display posts by category
     */
    public function show(Category $category)
    {
        $posts = $category->posts()
                          ->with(['user', 'tags'])
                          ->latest()
                          ->paginate(10);

        return view('posts.index', compact('posts', 'category'));
    }
}
