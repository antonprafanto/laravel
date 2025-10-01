<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - To-Do List App
|--------------------------------------------------------------------------
|
| Routes untuk aplikasi To-Do List sederhana
|
*/

// Homepage redirect ke tasks
Route::get('/', function () {
    return redirect()->route('tasks.index');
});

// Resource routes untuk Task CRUD
Route::resource('tasks', TaskController::class);

// Custom route untuk toggle completed status
Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggle'])
     ->name('tasks.toggle');
