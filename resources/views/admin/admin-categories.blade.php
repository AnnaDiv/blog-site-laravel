<x-layout>
<div class="top-bar">
    <div class="search-bar">
        <form method="GET" action="{{ route('categories.search.admin') }}">
            @csrf
            <input type="text" pattern=".{3,}" name="search_q"
                   value="{{ old('search_q') ?? request('search_q')}}" required pattern=".{3,}"
                   required title="3 characters minimum" placeholder="Search categories" />
            <button type="submit" class="user-search-btn">Search Categories</button>
        </form>
    </div>
</div>
<h2 class="item-title"><a href="{{ route('admin.categories') }}">All Categories</a></h2>
<!-- Desktop layout -->
<div class="category-grid-desktop-admin">
        <table class="item-container">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <div class="category-admin">
                @foreach ($categories as $category)
                    <div class="category-cell">
                        @if ($category)
                        <tr class="category-item">
                            <td> {{ $category->id }} </td>
                            <td> {{ $category->title }} </td>
                            <td> @if($category->description != ' ') {{ $category->description }} @else Auto Created @endif </td>
                            <td>
                                <a href="{{ route('category', $category->id) }}"><button>View</button></a>
                                <a href="{{ route('category.view', $category) }}"><button>Edit</button></a>
                                <a href="{{ route('category.delete', $category) }}"><button>Perma Delete</button></a>
                            </td>
                        </tr>
                        @endif
                    </div>
                @endforeach
            </div>
        </tbody>
    </table>
</div>

<!-- Mobile layout -->
<div class="category-grid-mobile">
    @foreach ($categories as $category)
        <div class="category-cell">
            <a href="{{ route('category', $category->id) }}">
                {{ $category->title }}
            </a>
        </div>
    @endforeach
</div>

{{ $categories->links() }}
</x-layout>