<x-layout>
<div class="top-bar">
    <div class="search-bar">
         <form method="GET" action="{{ route('admin.deletedPost.search') }}">
            <input type="text" name="search_q" id="search_q"
                   value="{{old('search_q') ?? request('search_q')}}" 
                   placeholder="Search Posts"/>
            <button type="submit" class="user-search-btn">Posts</button>
        </form>
    </div>
</div>
<h2 class="item-title"><a href="{{ route('admin.deleted.posts') }}">All Deleted Posts</a></h2>
<div class="content-layout">
    <table class="item-container">
        <thead>
            <tr>
                <th>Post ID</th>
                <th>User</th>
                <th>Picture</th>
                <th>Title</th>
                <th>Content</th>
                <th>Categories</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <div class="category-admin">
                @foreach ($posts as $post)
                    <tr class="category-item">
                        <td> {{ $post->id }} </td>
                        <td> {{ $post->user_nickname }} </td>
                        <td>
                            <img class="table-image" src="{{asset('storage/' . $post->image_folder)}}" alt="Post image">
                        </td>
                        <td> {{ $post->title }} </td>
                        <td> {{ $post->description }} </td>
                        <td>  
                            @forelse($post->categories as $category)
                                {{ $category->title }}
                                @if(!$loop->last), @endif
                            @empty
                                No categories attached
                            @endforelse
                        </td>
                        <td> {{ $post->status }}
                        <td>
                            <a href="{{ route('post.view', $post) }}"><button>View</button></a>
                            <a href="{{ route('post.edit', $post) }}"><button>Edit</button></a>
                            <form method="POST" action="{{ route('post.reinstate', $post) }}">
                                @csrf
                                @method('PUT')
                                <button>Reinstate</button>
                            </form>
                            <form method="POST" action="{{ route('post.permaDelete', $post) }}">
                                @csrf
                                @method('DELETE')
                                <button>Perma Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </div>
        </tbody>
    </table>
</div>

{{ $posts->links() }}
</x-layout>