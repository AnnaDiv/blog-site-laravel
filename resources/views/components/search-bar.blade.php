<div class="top-bar">
    <div class="search-bar">
        <form id="searchForm" method="GET">
            <input type="text" pattern=".{3,}" name="search_q" id="search_q"
                   value="{{old('search_q') ?? request('search_q')}}"
                   required title="3 characters minimum" placeholder="Search for..." />

                <button type="submit" class="user-search-btn" data-target="posts">Posts</button>
                <button type="submit" class="user-search-btn" data-target="users">Users</button>
        </form>
    </div>
</div>
<script>
    window.searchRoutes = {
        posts: "{{ route('search.posts') }}",
        users: "{{ route('search.users') }}"
    };
</script>
<script src="{{asset('js/search_selector.js')}}" defer></script>