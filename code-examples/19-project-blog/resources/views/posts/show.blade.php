@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        {{-- Back Button --}}
        <div class="mb-4">
            <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Posts
            </a>

            {{-- Edit & Delete Buttons (only for post owner) --}}
            @can('update', $post)
                <a href="{{ route('posts.edit', $post) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endcan

            @can('delete', $post)
                <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Are you sure you want to delete this post?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            @endcan
        </div>

        {{-- Post Content --}}
        <article class="post-detail">
            {{-- Post Image --}}
            @if($post->image)
                <div class="mb-4">
                    <img src="{{ asset('storage/' . $post->image) }}"
                         alt="{{ $post->title }}"
                         class="img-fluid rounded shadow"
                         style="width: 100%; max-height: 400px; object-fit: cover;">
                </div>
            @endif

            {{-- Post Title --}}
            <h1 class="display-4 fw-bold mb-3">{{ $post->title }}</h1>

            {{-- Post Meta Information --}}
            <div class="d-flex flex-wrap align-items-center gap-3 mb-4 pb-3 border-bottom">
                {{-- Author --}}
                <div class="text-muted">
                    <i class="fas fa-user"></i>
                    <strong>{{ $post->user->name }}</strong>
                </div>

                {{-- Date --}}
                <div class="text-muted">
                    <i class="fas fa-calendar"></i>
                    {{ $post->created_at->format('d M Y') }}
                </div>

                {{-- Reading Time --}}
                <div class="text-muted">
                    <i class="fas fa-clock"></i>
                    {{ $post->reading_time }}
                </div>

                {{-- Published Status --}}
                @if($post->is_published)
                    <span class="badge bg-success">
                        <i class="fas fa-check-circle"></i> Published
                    </span>
                @else
                    <span class="badge bg-secondary">
                        <i class="fas fa-clock"></i> Draft
                    </span>
                @endif
            </div>

            {{-- Category & Tags --}}
            <div class="mb-4">
                {{-- Category --}}
                <div class="mb-2">
                    <strong class="text-muted">Category:</strong>
                    <a href="{{ route('categories.show', $post->category) }}" class="badge-category">
                        {{ $post->category->name }}
                    </a>
                </div>

                {{-- Tags --}}
                @if($post->tags->count() > 0)
                    <div>
                        <strong class="text-muted">Tags:</strong>
                        @foreach($post->tags as $tag)
                            <a href="{{ route('tags.show', $tag) }}" class="badge-tag">
                                #{{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Post Body --}}
            <div class="post-body mt-4 mb-5">
                <div class="fs-5 lh-lg">
                    {!! nl2br(e($post->body)) !!}
                </div>
            </div>

            {{-- Post Footer --}}
            <div class="border-top pt-4 mt-5">
                <div class="row">
                    <div class="col-md-6">
                        <p class="text-muted mb-0">
                            <small>
                                <i class="fas fa-calendar-plus"></i>
                                Created: {{ $post->created_at->format('d M Y, H:i') }}
                            </small>
                        </p>
                        @if($post->created_at != $post->updated_at)
                            <p class="text-muted mb-0">
                                <small>
                                    <i class="fas fa-edit"></i>
                                    Last updated: {{ $post->updated_at->format('d M Y, H:i') }}
                                </small>
                            </p>
                        @endif
                    </div>
                    <div class="col-md-6 text-md-end">
                        {{-- Share Buttons (Optional) --}}
                        <p class="text-muted mb-0">
                            <small>Share this post:</small>
                        </p>
                        <div class="mt-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                               target="_blank" class="btn btn-sm btn-outline-primary me-1"
                               title="Share on Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}"
                               target="_blank" class="btn btn-sm btn-outline-info me-1"
                               title="Share on Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($post->title . ' ' . request()->url()) }}"
                               target="_blank" class="btn btn-sm btn-outline-success"
                               title="Share on WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </article>

        {{-- Related Posts (Optional - Future Enhancement) --}}
        {{--
        <div class="mt-5">
            <h4 class="mb-4">Related Posts</h4>
            <div class="row">
                {{-- Loop related posts by category --}}
            </div>
        </div>
        --}}
    </div>
</div>

@push('styles')
<style>
    .post-body {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #374151;
    }

    .post-detail h1 {
        color: #1f2937;
        line-height: 1.2;
    }

    .post-detail img {
        border: 3px solid #e5e7eb;
    }
</style>
@endpush
@endsection
