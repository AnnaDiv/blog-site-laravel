<x-layout>
<div class="simple-text">
    <h2> Choose something you want to have an overwiew on: </h2>
    <ol class="ol-buttons">
        <li><a class="button" href="{{ route('admin.categories') }}">View Categories</a></li>
        <li><a class="button" href="{{ route('admin.users') }}">View Users</a></li>
        <li><a class="button" href="{{ route('admin.deleted.posts') }}"> View Deleted Posts</a></li>
    </ol>

</div>
</x-layout>