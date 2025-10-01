@extends('layouts.app')

@section('title', 'Create New Post')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="page-title mb-0">
                <i class="fas fa-pen-fancy"></i> Create New Post
            </h2>
            <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Title --}}
            <div class="mb-3">
                <label for="title" class="form-label fw-bold">
                    Title <span class="text-danger">*</span>
                </label>
                <input
                    type="text"
                    name="title"
                    id="title"
                    value="{{ old('title') }}"
                    class="form-control @error('title') is-invalid @enderror"
                    required
                    placeholder="Enter post title"
                >
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">
                    <i class="fas fa-info-circle"></i> A catchy title for your post
                </small>
            </div>

            {{-- Slug (Optional) --}}
            <div class="mb-3">
                <label for="slug" class="form-label fw-bold">
                    Slug <span class="text-muted">(Optional)</span>
                </label>
                <input
                    type="text"
                    name="slug"
                    id="slug"
                    value="{{ old('slug') }}"
                    class="form-control @error('slug') is-invalid @enderror"
                    placeholder="auto-generated-from-title"
                >
                @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">
                    <i class="fas fa-info-circle"></i> Leave empty to auto-generate from title
                </small>
            </div>

            {{-- Body --}}
            <div class="mb-3">
                <label for="body" class="form-label fw-bold">
                    Content <span class="text-danger">*</span>
                </label>
                <textarea
                    name="body"
                    id="body"
                    rows="15"
                    class="form-control @error('body') is-invalid @enderror"
                    required
                    placeholder="Write your post content here..."
                >{{ old('body') }}</textarea>
                @error('body')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">
                    <i class="fas fa-info-circle"></i> The main content of your post
                </small>
            </div>

            {{-- Image Upload --}}
            <div class="mb-3">
                <label for="image" class="form-label fw-bold">
                    Featured Image
                </label>
                <input
                    type="file"
                    name="image"
                    id="image"
                    accept="image/*"
                    class="form-control @error('image') is-invalid @enderror"
                >
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">
                    <i class="fas fa-info-circle"></i> Max 2MB - JPG, PNG, GIF, JPEG
                </small>
            </div>

            {{-- Image Preview --}}
            <div id="imagePreview" class="mb-3" style="display: none;">
                <label class="form-label">Preview:</label>
                <div>
                    <img id="previewImg" src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                </div>
            </div>

            {{-- Category --}}
            <div class="mb-3">
                <label for="category_id" class="form-label fw-bold">
                    Category <span class="text-danger">*</span>
                </label>
                <select
                    name="category_id"
                    id="category_id"
                    class="form-select @error('category_id') is-invalid @enderror"
                    required
                >
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Tags (Multiple Select) --}}
            <div class="mb-3">
                <label for="tags" class="form-label fw-bold">
                    Tags <span class="text-muted">(Optional)</span>
                </label>
                <select
                    name="tags[]"
                    id="tags"
                    multiple
                    size="5"
                    class="form-select @error('tags') is-invalid @enderror"
                >
                    @foreach($tags as $tag)
                        <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', [])) ? 'selected' : '' }}>
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </select>
                @error('tags')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">
                    <i class="fas fa-info-circle"></i> Hold Ctrl (Windows) or Cmd (Mac) to select multiple
                </small>
            </div>

            {{-- Published Checkbox --}}
            <div class="mb-4">
                <div class="form-check">
                    <input
                        type="checkbox"
                        name="is_published"
                        id="is_published"
                        value="1"
                        {{ old('is_published') ? 'checked' : '' }}
                        class="form-check-input"
                    >
                    <label class="form-check-label fw-bold" for="is_published">
                        Publish immediately
                    </label>
                </div>
                <small class="form-text text-muted">
                    Uncheck to save as draft
                </small>
            </div>

            {{-- Submit Buttons --}}
            <div class="d-flex gap-2 pt-3 border-top">
                <button type="submit" class="btn btn-gradient">
                    <i class="fas fa-paper-plane"></i> Create Post
                </button>
                <a href="{{ route('posts.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Image Preview
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('imagePreview').style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            document.getElementById('imagePreview').style.display = 'none';
        }
    });

    // Auto-generate slug from title
    document.getElementById('title').addEventListener('input', function(e) {
        const slugInput = document.getElementById('slug');
        if (slugInput.value === '') {
            const slug = e.target.value
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugInput.value = slug;
        }
    });
</script>
@endpush
@endsection
