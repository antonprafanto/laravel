<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ isset($category) ? 'Posts in ' . $category->name : (isset($tag) ? 'Posts tagged with ' . $tag->name : 'All Posts') }}
            </h2>
            @auth
                <a href="{{ route('posts.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create Post
                </a>
            @endauth
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Search Form --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('posts.index') }}" class="flex gap-2">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search posts..."
                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        >
                        <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Search
                        </button>
                        @if(request('search'))
                            <a href="{{ route('posts.index') }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Clear
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Posts Grid --}}
            @if($posts->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        No posts found.
                        @auth
                            <a href="{{ route('posts.create') }}" class="text-blue-500 hover:underline">Create your first post!</a>
                        @endauth
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($posts as $post)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition">
                            @if($post->image)
                                <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gradient-to-r from-blue-400 to-purple-500"></div>
                            @endif

                            <div class="p-6">
                                <div class="flex items-center gap-2 mb-2">
                                    <a href="{{ route('categories.show', $post->category->slug) }}" class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                        {{ $post->category->name }}
                                    </a>
                                    @if(!$post->is_published)
                                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Draft</span>
                                    @endif
                                </div>

                                <a href="{{ route('posts.show', $post) }}">
                                    <h3 class="text-xl font-bold text-gray-800 hover:text-blue-600 mb-2">
                                        {{ $post->title }}
                                    </h3>
                                </a>

                                <p class="text-gray-600 text-sm mb-4">{{ $post->excerpt }}</p>

                                <div class="flex flex-wrap gap-1 mb-4">
                                    @foreach($post->tags as $tag)
                                        <a href="{{ route('tags.show', $tag->slug) }}" class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded hover:bg-gray-200">
                                            #{{ $tag->name }}
                                        </a>
                                    @endforeach
                                </div>

                                <div class="flex items-center justify-between text-sm text-gray-500">
                                    <span>By {{ $post->user->name }}</span>
                                    <span>{{ $post->reading_time }}</span>
                                </div>

                                <div class="flex gap-2 mt-4">
                                    <a href="{{ route('posts.show', $post) }}" class="bg-gray-500 hover:bg-gray-700 text-white text-sm font-bold py-1 px-3 rounded">
                                        Read More
                                    </a>

                                    @can('update', $post)
                                        <a href="{{ route('posts.edit', $post) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white text-sm font-bold py-1 px-3 rounded">
                                            Edit
                                        </a>
                                    @endcan

                                    @can('delete', $post)
                                        <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white text-sm font-bold py-1 px-3 rounded">
                                                Delete
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
