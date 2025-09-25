# Pelajaran 11: Categories CRUD: Index, Create, Update, Delete

Sekarang kita akan membangun fitur CRUD (Create, Read, Update, Delete) lengkap untuk Categories. Ini adalah implementasi CRUD pertama di admin area yang akan menjadi foundation untuk fitur lainnya.

## 🎯 Tujuan Pembelajaran

Setelah menyelesaikan pelajaran ini, Anda akan:
- ✅ Membangun CRUD operations lengkap untuk Categories
- ✅ Membuat form handling yang proper
- ✅ Implementasi validation dan error handling
- ✅ Mengerti resource controllers di Laravel
- ✅ Membuat UI admin yang user-friendly

## 📋 Resource Controller untuk Categories

### Step 1: Create Categories Resource Controller

```bash
php artisan make:controller Admin/CategoryController --resource
```

Edit `app/Http/Controllers/Admin/CategoryController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $categories = Category::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->withCount(['posts', 'publishedPosts'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.categories.index', compact('categories', 'search'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'color' => ['required', 'string', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        // Generate slug jika tidak diisi
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Set default values
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $category = Category::create($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Category '{$category->name}' berhasil dibuat!");
    }

    /**
     * Display the specified category
     */
    public function show(Category $category)
    {
        $category->loadCount(['posts', 'publishedPosts']);
        
        $recentPosts = $category->posts()
            ->with(['author'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.categories.show', compact('category', 'recentPosts'));
    }

    /**
     * Show the form for editing category
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category)],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('categories')->ignore($category)],
            'description' => ['nullable', 'string', 'max:1000'],
            'color' => ['required', 'string', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        // Generate slug jika kosong
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle boolean
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $category->update($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Category '{$category->name}' berhasil diupdate!");
    }

    /**
     * Remove the specified category
     */
    public function destroy(Category $category)
    {
        // Check if category has posts
        if ($category->posts()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', "Cannot delete category '{$category->name}' because it has posts. Please move or delete the posts first.");
        }

        $categoryName = $category->name;
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Category '{$categoryName}' berhasil dihapus!");
    }

    /**
     * Toggle category status
     */
    public function toggle(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);
        
        $status = $category->is_active ? 'activated' : 'deactivated';
        
        return redirect()
            ->back()
            ->with('success', "Category '{$category->name}' has been {$status}!");
    }
}
```

### Step 2: Add Categories Routes

**⚠️ PENTING: Verifikasi Dependencies Terlebih Dahulu**

Sebelum menambahkan routes, pastikan semua dependencies sudah ada:

```bash
# 1. Verifikasi controller sudah dibuat dan tidak kosong
php -l app/Http/Controllers/Admin/CategoryController.php
# Expected: "No syntax errors detected"

# 2. Cek apakah DashboardController sudah ada (required untuk admin.dashboard route)
ls -la app/Http/Controllers/Admin/DashboardController.php

# 3. Verifikasi AppServiceProvider sudah memiliki gates
grep -n "manage-posts" app/Providers/AppServiceProvider.php
```

**Jika ada file yang missing, ikuti troubleshooting di bawah.**

Edit `routes/web.php` untuk menambahkan categories routes:

```php
// Admin routes (requires auth + admin/author role)
Route::middleware(['auth', 'can:manage-posts'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Categories management
    Route::resource('categories', CategoryController::class);
    Route::patch('/categories/{category}/toggle', [CategoryController::class, 'toggle'])->name('categories.toggle');
});
```

#### 🔧 Troubleshooting Dependencies

**Jika mengalami error saat menambahkan routes:**

##### Error: "Class 'App\Http\Controllers\Admin\DashboardController' not found"

**Penyebab**: DashboardController belum dibuat.

**Solusi**:
```bash
php artisan make:controller Admin/DashboardController
```

Edit `app/Http/Controllers/Admin/DashboardController.php`:
```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_posts' => Post::count(),
            'published_posts' => Post::where('status', 'published')->count(),
            'draft_posts' => Post::where('status', 'draft')->count(),
            'total_categories' => Category::count(),
            'total_tags' => Tag::count(),
            'total_users' => User::count(),
        ];

        $recentPosts = Post::with(['author', 'category'])
                          ->orderBy('created_at', 'desc')
                          ->limit(5)
                          ->get();

        $popularPosts = Post::published()
                           ->with(['author', 'category'])
                           ->orderBy('views_count', 'desc')
                           ->limit(5)
                           ->get();

        return view('admin.dashboard', compact('stats', 'recentPosts', 'popularPosts'));
    }
}
```

##### Error: "Gate [manage-posts] is not defined"

**Penyebab**: Authorization gates belum didefinisikan di AppServiceProvider.

**Solusi**: Edit `app/Providers/AppServiceProvider.php`:
```php
<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Authorization gates
        Gate::define('manage-posts', function (User $user) {
            return $user->canManagePosts();
        });

        Gate::define('admin-access', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('author-access', function (User $user) {
            return $user->isAuthor();
        });
    }
}
```

**Pastikan User model memiliki methods yang diperlukan** (jika belum ada):
```php
// Di User model, tambahkan methods ini jika belum ada:

public function isAdmin(): bool
{
    return $this->role === 'admin';
}

public function isAuthor(): bool
{
    return in_array($this->role, ['admin', 'author']);
}

public function canManagePosts(): bool
{
    return $this->isAuthor();
}
```

##### Error: "Call to undefined method App\Models\Category::publishedPosts()"

**Penyebab**: Relationship `publishedPosts` belum didefinisikan di Category model.

**Solusi**: Edit `app/Models/Category.php`, tambahkan relationship:
```php
public function publishedPosts()
{
    return $this->posts()->where('status', 'published')
                        ->where('published_at', '<=', now());
}
```

## 🎨 Create Categories Views

### Step 3: Categories Index View

**⚠️ CRITICAL: Verifikasi Directory Structure**

Sebelum membuat views, pastikan directory structure sudah benar:

```bash
# 1. Periksa apakah admin directory sudah ada
ls -la resources/views/admin/ 2>/dev/null || echo "Admin views directory does not exist"

# 2. Buat directory structure jika belum ada
mkdir -p resources/views/admin/categories

# 3. Verifikasi struktur yang dibuat
ls -la resources/views/admin/categories/
# Expected: directory kosong, siap untuk views
```

**Jika mengalami error "View [admin.categories.index] not found", pastikan semua steps ini dijalankan terlebih dahulu.**

Buat `resources/views/admin/categories/index.blade.php`:

```html
@extends('layouts.app')

@section('title', 'Manage Categories - Admin')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Categories</h1>
            <p class="text-gray-600">Manage blog categories</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" 
           class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            Add New Category
        </a>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}"
                       placeholder="Search categories..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" 
                        class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    Search
                </button>
                @if($search)
                    <a href="{{ route('admin.categories.index') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($categories->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Posts
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Order
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($categories as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 rounded-full mr-3" 
                                         style="background-color: {{ $category->color }}"></div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $category->name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            /{{ $category->slug }}
                                        </div>
                                        @if($category->description)
                                            <div class="text-sm text-gray-600 mt-1 max-w-xs truncate">
                                                {{ $category->description }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $category->published_posts_count }} published
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $category->posts_count }} total
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.categories.toggle', $category) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transition-colors
                                           {{ $category->is_active 
                                              ? 'bg-green-100 text-green-800 hover:bg-green-200' 
                                              : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $category->sort_order }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $category->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('admin.categories.show', $category) }}" 
                                       class="text-gray-600 hover:text-gray-900">
                                        View
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $category) }}" 
                                       class="text-primary-600 hover:text-primary-900">
                                        Edit
                                    </a>
                                    <form method="POST" 
                                          action="{{ route('admin.categories.destroy', $category) }}"
                                          class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-900 {{ $category->posts_count > 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                {{ $category->posts_count > 0 ? 'disabled' : '' }}>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $categories->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No categories found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if($search)
                        No categories match your search criteria.
                    @else
                        Get started by creating your first category.
                    @endif
                </p>
                @if(!$search)
                    <div class="mt-6">
                        <a href="{{ route('admin.categories.create') }}" 
                           class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            Add New Category
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
```

### Step 4: Create Category Form

Buat `resources/views/admin/categories/create.blade.php`:

```html
@extends('layouts.app')

@section('title', 'Add New Category - Admin')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-4 mb-4">
            <a href="{{ route('admin.categories.index') }}" 
               class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Add New Category</h1>
        </div>
        <p class="text-gray-600">Create a new category to organize your blog posts</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="p-6 space-y-6">
            @csrf

            <!-- Category Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Category Name *
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Slug -->
            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                    Slug
                    <span class="text-gray-500 text-sm">(Leave empty to auto-generate)</span>
                </label>
                <input type="text" 
                       id="slug" 
                       name="slug" 
                       value="{{ old('slug') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('slug') border-red-500 @enderror">
                @error('slug')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">
                    URL-friendly version of the name. Will be auto-generated if left empty.
                </p>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Color -->
            <div>
                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                    Color *
                </label>
                <div class="flex items-center space-x-4">
                    <input type="color" 
                           id="color" 
                           name="color" 
                           value="{{ old('color', '#3B82F6') }}"
                           class="w-16 h-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <input type="text" 
                           id="color-text" 
                           value="{{ old('color', '#3B82F6') }}"
                           readonly
                           class="px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600">
                </div>
                @error('color')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">
                    Choose a color to represent this category
                </p>
            </div>

            <!-- Sort Order -->
            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                    Sort Order
                </label>
                <input type="number" 
                       id="sort_order" 
                       name="sort_order" 
                       value="{{ old('sort_order', 0) }}"
                       min="0"
                       class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('sort_order') border-red-500 @enderror">
                @error('sort_order')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">
                    Lower numbers appear first
                </p>
            </div>

            <!-- Status -->
            <div>
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="is_active" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Active
                    </label>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Inactive categories won't be shown on the blog
                </p>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.categories.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    Create Category
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorInput = document.getElementById('color');
    const colorText = document.getElementById('color-text');
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');

    // Sync color picker dengan text input
    colorInput.addEventListener('input', function() {
        colorText.value = this.value.toUpperCase();
    });

    // Auto-generate slug dari name
    nameInput.addEventListener('input', function() {
        if (!slugInput.dataset.manual) {
            const slug = this.value
                .toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .trim();
            slugInput.value = slug;
        }
    });

    // Mark slug sebagai manual jika user mengedit
    slugInput.addEventListener('input', function() {
        slugInput.dataset.manual = 'true';
    });
});
</script>
@endsection
```

### Step 5: Edit Category Form

Buat `resources/views/admin/categories/edit.blade.php`:

```html
@extends('layouts.app')

@section('title', 'Edit Category: ' . $category->name . ' - Admin')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-4 mb-4">
            <a href="{{ route('admin.categories.index') }}" 
               class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Edit Category</h1>
        </div>
        <p class="text-gray-600">Update category: {{ $category->name }}</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Category Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Category Name *
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', $category->name) }}"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Slug -->
            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                    Slug *
                </label>
                <input type="text" 
                       id="slug" 
                       name="slug" 
                       value="{{ old('slug', $category->slug) }}"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('slug') border-red-500 @enderror">
                @error('slug')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">
                    Current URL: <span class="font-medium">{{ route('blog.category', $category->slug) }}</span>
                </p>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('description') border-red-500 @enderror">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Color -->
            <div>
                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                    Color *
                </label>
                <div class="flex items-center space-x-4">
                    <input type="color" 
                           id="color" 
                           name="color" 
                           value="{{ old('color', $category->color) }}"
                           class="w-16 h-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <input type="text" 
                           id="color-text" 
                           value="{{ old('color', $category->color) }}"
                           readonly
                           class="px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600">
                </div>
                @error('color')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sort Order -->
            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                    Sort Order
                </label>
                <input type="number" 
                       id="sort_order" 
                       name="sort_order" 
                       value="{{ old('sort_order', $category->sort_order) }}"
                       min="0"
                       class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('sort_order') border-red-500 @enderror">
                @error('sort_order')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="is_active" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Active
                    </label>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Inactive categories won't be shown on the blog
                </p>
            </div>

            <!-- Stats Info -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Category Statistics</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Total Posts:</span>
                        <span class="font-medium">{{ $category->posts_count ?? 0 }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Published:</span>
                        <span class="font-medium">{{ $category->published_posts_count ?? 0 }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Created:</span>
                        <span class="font-medium">{{ $category->created_at->format('M d, Y') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Updated:</span>
                        <span class="font-medium">{{ $category->updated_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between pt-6 border-t border-gray-200">
                <div>
                    @if($category->posts_count == 0)
                        <form method="POST" 
                              action="{{ route('admin.categories.destroy', $category) }}"
                              class="inline"
                              onsubmit="return confirm('Are you sure you want to delete this category?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="text-red-600 hover:text-red-800 font-medium">
                                Delete Category
                            </button>
                        </form>
                    @else
                        <span class="text-gray-400 text-sm">
                            Cannot delete - category has {{ $category->posts_count }} posts
                        </span>
                    @endif
                </div>
                
                <div class="flex space-x-4">
                    <a href="{{ route('admin.categories.index') }}" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        Update Category
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorInput = document.getElementById('color');
    const colorText = document.getElementById('color-text');

    colorInput.addEventListener('input', function() {
        colorText.value = this.value.toUpperCase();
    });
});
</script>
@endsection
```

## ✅ Flash Messages Component

### Step 6: Create Flash Messages

Tambahkan flash messages di layout. Edit `resources/views/layouts/app.blade.php`:

```html
<!-- Setelah <main> tag, tambahkan: -->

<!-- Flash Messages -->
@if(session('success') || session('error') || session('info'))
    <div x-data="{ show: true }" x-show="show" x-transition class="fixed top-4 right-4 z-50 max-w-sm">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg">
                <div class="flex justify-between items-center">
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="ml-4 text-green-700 hover:text-green-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-lg">
                <div class="flex justify-between items-center">
                    <span>{{ session('error') }}</span>
                    <button @click="show = false" class="ml-4 text-red-700 hover:text-red-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif
    </div>
    
    <script>
        setTimeout(() => {
            document.querySelector('[x-data*="show"]').__x.$data.show = false;
        }, 5000);
    </script>
@endif
```

## 🔗 Update Admin Dashboard

### Step 7: Add Categories Link to Dashboard

Edit `resources/views/admin/dashboard.blade.php` untuk menambahkan link ke categories:

```html
<!-- Update Quick Actions section -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <a href="#" class="flex items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary-500 transition-colors">
        <div class="text-center">
            <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span class="text-sm text-gray-600">New Post</span>
        </div>
    </a>
    <a href="{{ route('admin.categories.index') }}" class="flex items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary-500 transition-colors">
        <div class="text-center">
            <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            <span class="text-sm text-gray-600">Manage Categories</span>
        </div>
    </a>
    <!-- ... rest of quick actions ... -->
</div>
```

## 🧪 Testing Categories CRUD

### Step 8: Test All CRUD Operations

**⚠️ PENTING: Pre-Testing Verification**

Sebelum testing, verifikasi semua components sudah siap:

```bash
# 1. Test route registration
php artisan route:list --name=admin.categories
# Expected: Harus menampilkan 7 routes (index, create, store, show, edit, update, destroy)

# 2. Test controller methods tidak kosong
php artisan tinker
>>> app(App\Http\Controllers\Admin\CategoryController::class)->index(request());
>>> exit
# Expected: Tidak boleh error "Call to undefined method"

# 3. Verifikasi all views ada
ls -la resources/views/admin/categories/
# Expected: index.blade.php, create.blade.php, edit.blade.php harus ada

# 4. Test database connection
php artisan tinker
>>> Category::count();
>>> exit
# Expected: Mengembalikan angka (bisa 0)
```

**Jika ada error pada verifikasi di atas, perbaiki terlebih dahulu sebelum melanjutkan testing.**

Test semua functionality:

```bash
php artisan serve
```

Test URLs secara berurutan:
1. **`http://localhost:8000/admin/categories`** - List categories
   - ✅ Expected: Halaman admin categories dengan table dan search
   - ❌ Error: Jika 404, periksa routes. Jika 500, periksa controller method dan views.

2. **`http://localhost:8000/admin/categories/create`** - Create new category
   - ✅ Expected: Form create category dengan fields name, slug, description, color
   - ❌ Error: Jika view not found, periksa create.blade.php ada.

3. **Test create new category** - Submit form create
   - ✅ Expected: Redirect ke index dengan success message
   - ❌ Error: Jika validation error, periksa required fields. Jika 500, periksa database connection.

4. **`http://localhost:8000/admin/categories/{id}/edit`** - Edit category
   - ✅ Expected: Form edit dengan data category terpopulate
   - ❌ Error: Jika 404, pastikan category dengan ID tersebut ada di database.

5. **Test toggle active/inactive** - Click status badge di index
   - ✅ Expected: Status berubah dan redirect dengan success message
   - ❌ Error: Jika route not found, periksa categories.toggle route terdaftar.

6. **Test category deletion** - Click delete button (only if no posts)
   - ✅ Expected: Category terhapus jika tidak ada posts, error message jika ada posts
   - ❌ Error: Jika JS confirm tidak muncul, periksa JavaScript di browser.

#### 🚨 Common Testing Errors & Solutions

##### Error: "Route [admin.categories.index] not defined"
**Solusi**:
```bash
# Pastikan routes terdaftar dengan benar
php artisan route:clear
php artisan route:cache
php artisan route:list --name=admin
```

##### Error: "View [admin.categories.index] not found"
**Solusi**:
```bash
# Pastikan directory dan file ada
mkdir -p resources/views/admin/categories
# Kemudian buat file index.blade.php, create.blade.php, edit.blade.php
```

##### Error: "Call to undefined method index()"
**Solusi**: Pastikan CategoryController methods tidak kosong dan sesuai dengan implementasi di lesson.

##### Error: "SQLSTATE[HY000] [2002] Connection refused"
**Solusi**:
```bash
# Pastikan database server berjalan dan konfigurasi benar
php artisan config:clear
php artisan migrate --seed
```

## 🎯 Kesimpulan

Selamat! Anda telah berhasil membangun:
- ✅ CRUD operations lengkap untuk Categories
- ✅ Resource controller dengan proper validation
- ✅ UI yang user-friendly dengan search dan filtering
- ✅ Form handling dengan error messages
- ✅ Toggle functionality untuk status
- ✅ Soft constraints (tidak bisa delete jika ada posts)

Categories CRUD sekarang sudah functional dan siap digunakan.

### 💡 Troubleshooting Summary

**Jika mengalami masalah saat implementasi:**

1. **Selalu verifikasi dependencies terlebih dahulu** sebelum menambahkan routes
2. **Gunakan step-by-step testing approach** untuk mengidentifikasi error lebih cepat
3. **Periksa file permissions** di Linux/Mac jika ada error write access
4. **Gunakan `php artisan route:list` dan `php artisan tinker`** untuk debugging
5. **Pastikan semua imports dan namespaces benar** di controller dan model

**Lesson ini telah diuji dan diperbaiki untuk error-prevention.** Di pelajaran selanjutnya, kita akan membahas middleware dan route groups lebih dalam.

---

**Selanjutnya:** [Pelajaran 12: Admin User, Route Groups, Middleware](12-admin-middleware.md)

*CRUD Categories ready! 📋*