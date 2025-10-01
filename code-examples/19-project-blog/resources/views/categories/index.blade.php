@extends('layouts.app')

@section('title', 'All Categories')

@section('content')
<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="page-title mb-0">
                <i class="fas fa-folder-open"></i> All Categories
            </h2>
            <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Posts
            </a>
        </div>

        @if($categories->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i>
                No categories available yet.
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach($categories as $category)
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm hover-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-folder text-primary"></i>
                                        {{ $category->name }}
                                    </h5>
                                    <span class="badge bg-primary rounded-pill">
                                        {{ $category->posts_count }}
                                        {{ Str::plural('post', $category->posts_count) }}
                                    </span>
                                </div>

                                <p class="card-text text-muted small">
                                    <i class="fas fa-link"></i>
                                    <code>{{ $category->slug }}</code>
                                </p>

                                @if($category->posts_count > 0)
                                    <a href="{{ route('categories.show', $category) }}"
                                       class="btn btn-sm btn-gradient w-100">
                                        <i class="fas fa-eye"></i>
                                        View Posts
                                    </a>
                                @else
                                    <button class="btn btn-sm btn-secondary w-100" disabled>
                                        <i class="fas fa-inbox"></i>
                                        No Posts Yet
                                    </button>
                                @endif
                            </div>

                            @if($category->posts_count > 0)
                                <div class="card-footer bg-light border-0">
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i>
                                        Latest:
                                        {{ $category->posts()->latest()->first()->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Category Statistics --}}
            <div class="row mt-5">
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-chart-bar"></i> Category Statistics
                            </h5>
                            <div class="row text-center mt-4">
                                <div class="col-md-4">
                                    <h3 class="text-primary">{{ $categories->count() }}</h3>
                                    <p class="text-muted">Total Categories</p>
                                </div>
                                <div class="col-md-4">
                                    <h3 class="text-success">{{ $categories->sum('posts_count') }}</h3>
                                    <p class="text-muted">Total Posts</p>
                                </div>
                                <div class="col-md-4">
                                    <h3 class="text-info">
                                        {{ $categories->where('posts_count', '>', 0)->count() }}
                                    </h3>
                                    <p class="text-muted">Active Categories</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .hover-card {
        transition: all 0.3s ease;
    }

    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }
</style>
@endpush
@endsection
