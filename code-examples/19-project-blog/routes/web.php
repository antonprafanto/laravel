<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Blog Application
|--------------------------------------------------------------------------
*/

// Homepage redirect ke posts
Route::get('/', function () {
    return redirect()->route('posts.index');
});

// Post routes (Public: index & show, Auth: create, edit, delete)
Route::resource('posts', PostController::class);

// Category routes
Route::get('/categories', [CategoryController::class, 'index'])
     ->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])
     ->name('categories.show');

// Tag routes
Route::get('/tags', [TagController::class, 'index'])
     ->name('tags.index');
Route::get('/tags/{tag:slug}', [TagController::class, 'show'])
     ->name('tags.show');

// Dashboard (from Breeze)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes (from Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Auth routes (handled by Breeze)
require __DIR__.'/auth.php';
