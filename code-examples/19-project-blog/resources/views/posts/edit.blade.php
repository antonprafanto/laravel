@extends('layouts.app')

@section('title', 'Edit Post: ' . $post->title)

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="page-title mb-0">
                <i class="fas fa-edit"></i> Edit Post
            </h2>
            <div>
                <a href="{{ route('posts.show', $post) }}" class="btn btn-outline-info">
                    <i class="fas fa-eye"></i> View
                </a>
                <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Title --}}
            <div class="mb-3">
                <label for="title" class="form-label fw-bold">
                    Title <span class="text-danger">*</span>
                </label>
                <input
                    type="text"
                    name="title"
                    id="title"
                    value="{{ old('title', $post->title) }}"
                    class="form-control @error('title') is-invalid @enderror"
                    required
                    placeholder="Enter post title"
                >
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Slug --}}
            <div class="mb-3">
                <label for="slug" class="form-label fw-bold">
                    Slug
                </label>
                <input
                    type="text"
                    name="slug"
                    id="slug"
                    value="{{ old('slug', $post->slug) }}"
                    class="form-control @error('slug') is-invalid @enderror"
                    placeholder="auto-generated-from-title"
                >
                @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">
                    <i class="fas fa-info-circle"></i> URL-friendly version of the title
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
                >{{ old('body', $post->body) }}</textarea>
                @error('body')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Current Image --}}
            @if($post->image)
                <div class="mb-3">
                    <label class="form-label fw-bold">Current Image:</label>
                    <div class="position-relative" style="max-width: 300px;">
                        <img src="{{ asset('storage/' . $post->image) }}"
                             alt="Current image"
                             class="img-fluid rounded border"
                             id="currentImage">
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-image"></i> {{ basename($post->image) }}
                            </small>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Image Upload (Replace) --}}
            <div class="mb-3">
                <label for="image" class="form-label fw-bold">
                    {{ $post->image ? 'Replace Image' : 'Add Image' }}
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
                    @if($post->image)
                        <br><strong class="text-warning">⚠️ Uploading a new image will replace the current one</strong>
                    @endif
                </small>
            </div>

            {{-- Image Preview --}}
            <div id="imagePreview" class="mb-3" style="display: none;">
                <label class="form-label fw-bold">New Image Preview:</label>
                <div>
                    <img id="previewImg" src="" alt="Preview" class="img-fluid rounded border" style="max-height: 200px;">
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
                        <option value="{{ $category->id }}"
                            {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
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
                        <option value="{{ $tag->id }}"
                            {{ in_array($tag->id, old('tags', $post->tags->pluck('id')->toArray())) ? 'selected' : '' }}>
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
                        {{ old('is_published', $post->is_published) ? 'checked' : '' }}
                        class="form-check-input"
                    >
                    <label class="form-check-label fw-bold" for="is_published">
                        Published
                    </label>
                </div>
                <small class="form-text text-muted">
                    @if($post->is_published)
                        This post is currently <strong class="text-success">published</strong>
                    @else
                        This post is currently a <strong class="text-warning">draft</strong>
                    @endif
                </small>
            </div>

            {{-- Submit Buttons --}}
            <div class="d-flex gap-2 pt-3 border-top">
                <button type="submit" class="btn btn-gradient">
                    <i class="fas fa-save"></i> Update Post
                </button>
                <a href="{{ route('posts.show', $post) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <form action="{{ route('posts.destroy', $post) }}" method="POST" class="ms-auto"
                      onsubmit="return confirm('Are you sure you want to delete this post? This action cannot be undone!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete Post
                    </button>
                </form>
            </div>
        </form>

        {{-- Post Metadata --}}
        <div class="mt-4 p-3 bg-light rounded">
            <h6 class="fw-bold mb-2">Post Information:</h6>
            <small class="text-muted d-block">
                <i class="fas fa-calendar-plus"></i> Created: {{ $post->created_at->format('d M Y, H:i') }}
            </small>
            <small class="text-muted d-block">
                <i class="fas fa-edit"></i> Last Updated: {{ $post->updated_at->format('d M Y, H:i') }}
            </small>
            <small class="text-muted d-block">
                <i class="fas fa-user"></i> Author: {{ $post->user->name }}
            </small>
            @if($post->published_at)
                <small class="text-muted d-block">
                    <i class="fas fa-check-circle"></i> Published: {{ $post->published_at->format('d M Y, H:i') }}
                </small>
            @endif
        </div>
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

    // Auto-generate slug from title (only if slug is empty or matches old title)
    const originalSlug = '{{ $post->slug }}';
    document.getElementById('title').addEventListener('input', function(e) {
        const slugInput = document.getElementById('slug');
        // Only auto-update if slug hasn't been manually changed
        if (slugInput.value === originalSlug || slugInput.value === '') {
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
