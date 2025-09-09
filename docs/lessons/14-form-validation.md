# Pelajaran 14: Form Validation and Error Messages

Dalam pelajaran ini, kita akan mendalami form validation di Laravel, membuat custom validation rules, dan menangani error messages dengan cara yang user-friendly.

## 🎯 Tujuan Pembelajaran

Setelah menyelesaikan pelajaran ini, Anda akan:
- ✅ Menguasai Laravel validation yang advanced
- ✅ Membuat custom validation rules
- ✅ Menangani error messages dengan baik
- ✅ Implementasi client-side validation
- ✅ Membuat form requests yang reusable

## 🔒 Laravel Validation Fundamentals

### Basic Validation Rules

Laravel menyediakan banyak validation rules built-in:

```php
$validated = $request->validate([
    'title' => ['required', 'string', 'max:255'],
    'email' => ['required', 'email', 'unique:users,email'],
    'password' => ['required', 'min:8', 'confirmed'],
    'age' => ['nullable', 'integer', 'between:18,100'],
    'website' => ['nullable', 'url'],
    'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
]);
```

### Conditional Validation

```php
$rules = [
    'title' => ['required', 'string', 'max:255'],
    'status' => ['required', 'in:draft,published'],
];

// Add rule only if status is published
if ($request->status === 'published') {
    $rules['published_at'] = ['required', 'date', 'after_or_equal:today'];
}

$validated = $request->validate($rules);
```

## 📝 Create Form Requests

### Step 1: Generate Form Requests

```bash
php artisan make:request StorePostRequest
php artisan make:request UpdatePostRequest
php artisan make:request StoreCategoryRequest
php artisan make:request UpdateCategoryRequest
```

### Step 2: Create StorePostRequest

Edit `app/Http/Requests/StorePostRequest.php`:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->canManagePosts();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable', 
                'string', 
                'max:255', 
                'alpha_dash',
                'unique:posts,slug'
            ],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string', 'min:100'],
            'featured_image' => [
                'nullable', 
                'image', 
                'mimes:jpeg,png,jpg,gif,webp', 
                'max:2048'
            ],
            'category_id' => [
                'required', 
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    $category = \App\Models\Category::find($value);
                    if ($category && !$category->is_active) {
                        $fail('The selected category is not active.');
                    }
                },
            ],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
            'status' => ['required', 'in:draft,published,archived'],
            'is_featured' => ['boolean'],
            'meta_title' => ['nullable', 'string', 'max:60'],
            'meta_description' => ['nullable', 'string', 'max:160'],
        ];

        // Conditional rules based on status
        if ($this->status === 'published') {
            $rules['published_at'] = [
                'nullable', 
                'date', 
                'after_or_equal:' . now()->subMinutes(5)->format('Y-m-d H:i:s')
            ];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Post title is required.',
            'title.max' => 'Post title cannot be longer than 255 characters.',
            'slug.alpha_dash' => 'Slug may only contain letters, numbers, dashes and underscores.',
            'slug.unique' => 'This slug has already been taken. Try: :suggestion',
            'content.required' => 'Post content cannot be empty.',
            'content.min' => 'Post content must be at least 100 characters long.',
            'featured_image.image' => 'Featured image must be a valid image file.',
            'featured_image.max' => 'Featured image cannot be larger than 2MB.',
            'category_id.required' => 'Please select a category for this post.',
            'category_id.exists' => 'Selected category does not exist.',
            'tags.*.exists' => 'One or more selected tags do not exist.',
            'status.in' => 'Post status must be either draft, published, or archived.',
            'meta_title.max' => 'Meta title should not exceed 60 characters for better SEO.',
            'meta_description.max' => 'Meta description should not exceed 160 characters for better SEO.',
            'published_at.after_or_equal' => 'Published date cannot be in the past.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'category_id' => 'category',
            'is_featured' => 'featured status',
            'published_at' => 'publish date',
            'meta_title' => 'SEO title',
            'meta_description' => 'SEO description',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Add custom logic here if needed
        // For example, log validation failures
        
        parent::failedValidation($validator);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean and prepare data before validation
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            'slug' => $this->slug ? \Illuminate\Support\Str::slug($this->slug) : null,
        ]);
    }

    /**
     * Get validated data with additional processing
     */
    public function getValidatedData(): array
    {
        $validated = $this->validated();

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']);
        }

        // Auto-generate excerpt if not provided
        if (empty($validated['excerpt'])) {
            $validated['excerpt'] = \Illuminate\Support\Str::limit(
                strip_tags($validated['content']), 
                160
            );
        }

        // Set author
        $validated['user_id'] = auth()->id();

        // Handle published_at
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        return $validated;
    }
}
```

### Step 3: Create Custom Validation Rules

```bash
php artisan make:rule SlugAvailable
php artisan make:rule ValidImageDimensions
```

Edit `app/Rules/SlugAvailable.php`:

```php
<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Post;

class SlugAvailable implements ValidationRule
{
    protected $ignoreId;
    protected $table;

    public function __construct($table = 'posts', $ignoreId = null)
    {
        $this->table = $table;
        $this->ignoreId = $ignoreId;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = Post::where('slug', $value);
        
        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        if ($query->exists()) {
            $fail('The :attribute has already been taken.');
        }
    }
}
```

Edit `app/Rules/ValidImageDimensions.php`:

```php
<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidImageDimensions implements ValidationRule
{
    protected $minWidth;
    protected $minHeight;
    protected $maxWidth;
    protected $maxHeight;

    public function __construct($minWidth = 800, $minHeight = 600, $maxWidth = 2000, $maxHeight = 2000)
    {
        $this->minWidth = $minWidth;
        $this->minHeight = $minHeight;
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value || !$value->isValid()) {
            return;
        }

        $dimensions = getimagesize($value->getPathname());
        
        if (!$dimensions) {
            $fail('The :attribute must be a valid image.');
            return;
        }

        [$width, $height] = $dimensions;

        if ($width < $this->minWidth || $height < $this->minHeight) {
            $fail("The :attribute must be at least {$this->minWidth}x{$this->minHeight} pixels.");
        }

        if ($width > $this->maxWidth || $height > $this->maxHeight) {
            $fail("The :attribute cannot be larger than {$this->maxWidth}x{$this->maxHeight} pixels.");
        }
    }
}
```

## 🎨 Enhanced Error Display

### Step 4: Create Error Components

Buat `resources/views/components/form-error.blade.php`:

```html
@props(['errors', 'name'])

@if($errors->has($name))
    <div {{ $attributes->merge(['class' => 'mt-1']) }}>
        @foreach($errors->get($name) as $error)
            <p class="text-sm text-red-600 flex items-center">
                <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $error }}
            </p>
        @endforeach
    </div>
@endif
```

Buat `resources/views/components/form-input.blade.php`:

```html
@props([
    'label' => '',
    'name' => '',
    'type' => 'text',
    'required' => false,
    'help' => '',
    'value' => ''
])

<div {{ $attributes->merge(['class' => 'space-y-1']) }}>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    @if($type === 'textarea')
        <textarea 
            id="{{ $name }}" 
            name="{{ $name }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm' . ($errors->has($name) ? ' border-red-300' : '')]) }}
        >{{ old($name, $value) }}</textarea>
    @elseif($type === 'select')
        <select 
            id="{{ $name }}" 
            name="{{ $name }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm' . ($errors->has($name) ? ' border-red-300' : '')]) }}
        >
            {{ $slot }}
        </select>
    @else
        <input 
            type="{{ $type }}" 
            id="{{ $name }}" 
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm' . ($errors->has($name) ? ' border-red-300' : '')]) }}
        >
    @endif

    <x-form-error :errors="$errors" :name="$name" />

    @if($help)
        <p class="text-sm text-gray-500">{{ $help }}</p>
    @endif
</div>
```

## 🌐 Client-Side Validation

### Step 5: Add JavaScript Validation

Buat `resources/js/form-validation.js`:

```javascript
class FormValidator {
    constructor(form) {
        this.form = form;
        this.errors = new Map();
        this.init();
    }

    init() {
        this.form.addEventListener('submit', (e) => {
            if (!this.validateForm()) {
                e.preventDefault();
            }
        });

        // Real-time validation
        this.form.querySelectorAll('input, textarea, select').forEach(field => {
            field.addEventListener('blur', () => this.validateField(field));
            field.addEventListener('input', () => this.clearFieldError(field));
        });
    }

    validateForm() {
        let isValid = true;
        this.clearAllErrors();

        this.form.querySelectorAll('[required]').forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        // Custom validations
        this.validateEmail();
        this.validateSlug();
        this.validateImageSize();

        return isValid;
    }

    validateField(field) {
        const value = field.value.trim();
        const fieldName = field.name;

        // Required validation
        if (field.hasAttribute('required') && !value) {
            this.setFieldError(field, `${this.getFieldLabel(field)} is required.`);
            return false;
        }

        // Length validation
        if (field.hasAttribute('maxlength') && value.length > parseInt(field.getAttribute('maxlength'))) {
            this.setFieldError(field, `${this.getFieldLabel(field)} is too long.`);
            return false;
        }

        if (field.hasAttribute('minlength') && value.length > 0 && value.length < parseInt(field.getAttribute('minlength'))) {
            this.setFieldError(field, `${this.getFieldLabel(field)} is too short.`);
            return false;
        }

        return true;
    }

    validateEmail() {
        const emailField = this.form.querySelector('input[type="email"]');
        if (!emailField) return true;

        const email = emailField.value.trim();
        if (email && !this.isValidEmail(email)) {
            this.setFieldError(emailField, 'Please enter a valid email address.');
            return false;
        }
        return true;
    }

    validateSlug() {
        const slugField = this.form.querySelector('input[name="slug"]');
        if (!slugField) return true;

        const slug = slugField.value.trim();
        if (slug && !this.isValidSlug(slug)) {
            this.setFieldError(slugField, 'Slug can only contain letters, numbers, hyphens, and underscores.');
            return false;
        }
        return true;
    }

    validateImageSize() {
        const imageField = this.form.querySelector('input[type="file"][accept*="image"]');
        if (!imageField || !imageField.files.length) return true;

        const file = imageField.files[0];
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (file.size > maxSize) {
            this.setFieldError(imageField, 'Image size cannot exceed 2MB.');
            return false;
        }
        return true;
    }

    setFieldError(field, message) {
        this.errors.set(field.name, message);
        
        // Add error styling
        field.classList.add('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
        field.classList.remove('border-gray-300', 'focus:border-primary-500', 'focus:ring-primary-500');

        // Show error message
        let errorDiv = field.parentNode.querySelector('.js-error-message');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'js-error-message mt-1 text-sm text-red-600 flex items-center';
            field.parentNode.appendChild(errorDiv);
        }
        
        errorDiv.innerHTML = `
            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            ${message}
        `;
    }

    clearFieldError(field) {
        this.errors.delete(field.name);
        
        // Remove error styling
        field.classList.remove('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
        field.classList.add('border-gray-300', 'focus:border-primary-500', 'focus:ring-primary-500');

        // Remove error message
        const errorDiv = field.parentNode.querySelector('.js-error-message');
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    clearAllErrors() {
        this.form.querySelectorAll('input, textarea, select').forEach(field => {
            this.clearFieldError(field);
        });
    }

    getFieldLabel(field) {
        const label = this.form.querySelector(`label[for="${field.id}"]`);
        return label ? label.textContent.replace('*', '').trim() : field.name;
    }

    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    isValidSlug(slug) {
        return /^[a-zA-Z0-9_-]+$/.test(slug);
    }
}

// Auto-initialize form validation
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('form[data-validate]').forEach(form => {
        new FormValidator(form);
    });

    // Auto-generate slug from title
    const titleField = document.querySelector('input[name="title"]');
    const slugField = document.querySelector('input[name="slug"]');
    
    if (titleField && slugField) {
        let slugManuallyEdited = false;
        
        slugField.addEventListener('input', () => {
            slugManuallyEdited = true;
        });
        
        titleField.addEventListener('input', (e) => {
            if (!slugManuallyEdited) {
                const slug = e.target.value
                    .toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .trim();
                slugField.value = slug;
            }
        });
    }
});
```

Tambahkan ke `resources/js/app.js`:

```javascript
import './form-validation';
```

## 📱 Enhanced Post Create Form

### Step 6: Update Post Create Form

Update `resources/views/admin/posts/create.blade.php` dengan validation yang enhanced:

```html
@extends('layouts.admin')

@section('title', 'Create New Post')
@section('page-title', 'Create New Post')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush

@section('content')
<form method="POST" 
      action="{{ route('admin.posts.store') }}" 
      enctype="multipart/form-data" 
      class="space-y-8"
      data-validate>
    @csrf

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Title -->
            <x-form-input 
                label="Post Title"
                name="title"
                required
                placeholder="Enter a compelling title..."
                maxlength="255"
                help="This will be the main headline of your post"
            />

            <!-- Slug -->
            <x-form-input 
                label="URL Slug"
                name="slug"
                placeholder="auto-generated-from-title"
                help="Leave empty to auto-generate from title"
            />

            <!-- Content -->
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700">
                    Content <span class="text-red-500">*</span>
                </label>
                <div class="mt-1">
                    <textarea 
                        id="content" 
                        name="content" 
                        rows="20"
                        required
                        minlength="100"
                        placeholder="Write your post content here..."
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm {{ $errors->has('content') ? 'border-red-300' : '' }}">{{ old('content') }}</textarea>
                </div>
                <x-form-error :errors="$errors" name="content" />
                <p class="mt-1 text-sm text-gray-500">
                    Minimum 100 characters. You can use Markdown formatting.
                </p>
            </div>

            <!-- Excerpt -->
            <x-form-input 
                type="textarea"
                label="Excerpt"
                name="excerpt"
                rows="3"
                maxlength="500"
                placeholder="Brief summary of your post..."
                help="Leave empty to auto-generate from content"
            />
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Publish Settings -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Publish Settings</h3>
                
                <!-- Status -->
                <x-form-input 
                    type="select"
                    label="Status"
                    name="status"
                    required
                    class="mb-4"
                >
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </x-form-input>

                <!-- Published Date -->
                <x-form-input 
                    type="datetime-local"
                    label="Publish Date"
                    name="published_at"
                    value="{{ old('published_at') }}"
                    help="Leave empty to publish immediately"
                />

                <!-- Featured -->
                <div class="mt-4">
                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            id="is_featured" 
                            name="is_featured" 
                            value="1"
                            {{ old('is_featured') ? 'checked' : '' }}
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="is_featured" class="ml-2 block text-sm text-gray-900">
                            Featured Post
                        </label>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        Featured posts appear prominently on the homepage
                    </p>
                </div>
            </div>

            <!-- Category & Tags -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Organization</h3>
                
                <!-- Category -->
                <x-form-input 
                    type="select"
                    label="Category"
                    name="category_id"
                    required
                    class="mb-4"
                >
                    <option value="">Select a category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </x-form-input>

                <!-- Tags -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                    <div class="max-h-40 overflow-y-auto border border-gray-300 rounded-md p-3 space-y-2">
                        @foreach($tags as $tag)
                            <div class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    id="tag_{{ $tag->id }}" 
                                    name="tags[]" 
                                    value="{{ $tag->id }}"
                                    {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}
                                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <label for="tag_{{ $tag->id }}" class="ml-2 block text-sm text-gray-900">
                                    {{ $tag->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <x-form-error :errors="$errors" name="tags" />
                </div>
            </div>

            <!-- Featured Image -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Featured Image</h3>
                
                <div x-data="{ preview: null }">
                    <input 
                        type="file" 
                        id="featured_image" 
                        name="featured_image"
                        accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                        @change="const file = $event.target.files[0]; preview = file ? URL.createObjectURL(file) : null"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    
                    <div x-show="preview" class="mt-4">
                        <img :src="preview" alt="Preview" class="w-full h-48 object-cover rounded-lg">
                    </div>
                    
                    <x-form-error :errors="$errors" name="featured_image" />
                    <p class="mt-1 text-sm text-gray-500">
                        JPG, PNG, GIF, or WebP. Max size: 2MB. Recommended: 1200x630px.
                    </p>
                </div>
            </div>

            <!-- SEO Settings -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">SEO Settings</h3>
                
                <x-form-input 
                    label="SEO Title"
                    name="meta_title"
                    maxlength="60"
                    placeholder="Optimized title for search engines..."
                    help="Recommended: 50-60 characters"
                    class="mb-4"
                />

                <x-form-input 
                    type="textarea"
                    label="SEO Description"
                    name="meta_description"
                    rows="3"
                    maxlength="160"
                    placeholder="Brief description for search engine results..."
                    help="Recommended: 150-160 characters"
                />
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
        <a href="{{ route('admin.posts.index') }}" 
           class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            Cancel
        </a>
        <button type="submit" 
                name="action" 
                value="save_draft"
                class="bg-gray-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            Save as Draft
        </button>
        <button type="submit" 
                name="action" 
                value="publish"
                class="bg-primary-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            Publish Post
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Real-time character counting
document.addEventListener('DOMContentLoaded', function() {
    function addCharacterCounter(input, maxLength) {
        const counter = document.createElement('div');
        counter.className = 'text-xs text-gray-500 mt-1';
        input.parentNode.appendChild(counter);
        
        function updateCounter() {
            const remaining = maxLength - input.value.length;
            counter.textContent = `${input.value.length}/${maxLength} characters`;
            
            if (remaining < 10) {
                counter.className = 'text-xs text-red-500 mt-1';
            } else if (remaining < 30) {
                counter.className = 'text-xs text-yellow-500 mt-1';
            } else {
                counter.className = 'text-xs text-gray-500 mt-1';
            }
        }
        
        input.addEventListener('input', updateCounter);
        updateCounter();
    }
    
    // Add counters to fields with maxlength
    document.querySelectorAll('[maxlength]').forEach(input => {
        const maxLength = parseInt(input.getAttribute('maxlength'));
        addCharacterCounter(input, maxLength);
    });
});
</script>
@endpush
```

## 🎯 Kesimpulan Pelajaran 14

Selamat! Anda telah menguasai:
- ✅ Advanced Laravel validation dengan Form Requests
- ✅ Custom validation rules untuk kasus khusus
- ✅ Enhanced error handling dan display
- ✅ Client-side validation dengan JavaScript
- ✅ Reusable form components
- ✅ Real-time validation feedback

Form validation sekarang sudah robust dan user-friendly dengan feedback yang clear untuk users.

---

**Selanjutnya:** [Pelajaran 15: What to Learn Next](15-what-to-learn-next.md)

*Advanced form validation mastered! ✅*