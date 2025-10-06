<x-layout>
<div class="top-bar">
    <div class="search-bar">
        <form method="GET" action="{{ route('admin.user.search') }}">
            @csrf
            <input type="text" pattern=".{3,}" name="search_q"
                   value="{{ old('search_q') ?? request('search_q')}}" required
                   placeholder="Search Users" />
            <button type="submit" class="user-search-btn">Search Users</button>
        </form>
    </div>
</div>

<div class="content-layout">
    <h2 class="item-title"><a href="{{ route('admin.users') }}">All Users</a></h2>

    <table class="item-container">
        <thead>
            <tr>
                <th>ID</th>
                <th>Profile Picture</th>
                <th>Nickname</th>
                <th>Motto</th>
                <th>Info</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <x-user-card-admin :user="$user"/>
            @empty
                <tr>
                    <td colspan="3" class="bg-white text-center">
                        Sorry, there are no users to be displayed.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $users->links() }}
</div>
</x-layout>