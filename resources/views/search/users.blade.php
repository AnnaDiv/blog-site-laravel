<x-layout>

<div class="content-layout">

    <div class="masonry-container">
        @forelse ($users as $user)
            <x-user-card :user="$user"/>
        @empty
            <div class="bg-white">
                Sorry, there are no users to be displayed.
            </div>
        @endforelse
    </div>

</div>

{{ $users->links() }}

</x-layout>