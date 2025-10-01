@extends('layouts.app')

@section('title', 'All Tags')

@section('content')
<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="page-title mb-0">
                <i class="fas fa-tags"></i> All Tags
            </h2>
            <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Posts
            </a>
        </div>

        @if($tags->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i>
                No tags available yet.
            </div>
        @else
            {{-- Tag Cloud Style --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-cloud"></i> Tag Cloud
                    </h5>
                    <div class="tag-cloud">
                        @foreach($tags as $tag)
                            @php
                                // Calculate font size based on posts count
                                $minSize = 1;
                                $maxSize = 2.5;
                                $maxPosts = $tags->max('posts_count') ?: 1;
                                $fontSize = $minSize + (($tag->posts_count / $maxPosts) * ($maxSize - $minSize));
                            @endphp
                            <a href="{{ route('tags.show', $tag) }}"
                               class="tag-cloud-item"
                               style="font-size: {{ $fontSize }}rem;"
                               title="{{ $tag->posts_count }} {{ Str::plural('post', $tag->posts_count) }}">
                                #{{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Tag List --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-list"></i> All Tags
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Tag Name</th>
                                    <th>Slug</th>
                                    <th width="15%" class="text-center">Posts</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tags as $index => $tag)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <span class="badge-tag">
                                                #{{ $tag->name }}
                                            </span>
                                        </td>
                                        <td>
                                            <code>{{ $tag->slug }}</code>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary rounded-pill">
                                                {{ $tag->posts_count }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($tag->posts_count > 0)
                                                <a href="{{ route('tags.show', $tag) }}"
                                                   class="btn btn-sm btn-gradient">
                                                    <i class="fas fa-eye"></i> View Posts
                                                </a>
                                            @else
                                                <span class="text-muted small">
                                                    <i class="fas fa-inbox"></i> No posts
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Tag Statistics --}}
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-chart-pie"></i> Tag Statistics
                            </h5>
                            <div class="row text-center mt-4">
                                <div class="col-md-3">
                                    <h3 class="text-primary">{{ $tags->count() }}</h3>
                                    <p class="text-muted">Total Tags</p>
                                </div>
                                <div class="col-md-3">
                                    <h3 class="text-success">{{ $tags->sum('posts_count') }}</h3>
                                    <p class="text-muted">Total Posts Tagged</p>
                                </div>
                                <div class="col-md-3">
                                    <h3 class="text-info">{{ $tags->where('posts_count', '>', 0)->count() }}</h3>
                                    <p class="text-muted">Active Tags</p>
                                </div>
                                <div class="col-md-3">
                                    @if($tags->where('posts_count', '>', 0)->count() > 0)
                                        <h3 class="text-warning">
                                            {{ number_format($tags->avg('posts_count'), 1) }}
                                        </h3>
                                        <p class="text-muted">Avg Posts/Tag</p>
                                    @else
                                        <h3 class="text-muted">-</h3>
                                        <p class="text-muted">Avg Posts/Tag</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Top Tags --}}
                            @if($tags->where('posts_count', '>', 0)->count() > 0)
                                <hr class="my-4">
                                <h6 class="fw-bold mb-3">
                                    <i class="fas fa-trophy text-warning"></i> Top Tags:
                                </h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($tags->sortByDesc('posts_count')->take(10) as $tag)
                                        @if($tag->posts_count > 0)
                                            <a href="{{ route('tags.show', $tag) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                #{{ $tag->name }}
                                                <span class="badge bg-primary">{{ $tag->posts_count }}</span>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .tag-cloud {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .tag-cloud-item {
        text-decoration: none;
        color: #3b82f6;
        transition: all 0.3s ease;
        font-weight: 600;
    }

    .tag-cloud-item:hover {
        color: #8b5cf6;
        transform: scale(1.1);
    }

    .table-hover tbody tr:hover {
        background-color: #f3f4f6;
    }
</style>
@endpush
@endsection
