<x-layout>
    <div class="masonry-container">
        @forelse ($posts as $post)
            <x-post-card :post="$post"/>
         @empty
            <div class="bg-white">
                Sorry, there are no posts.
            </div>
        @endforelse
    </div>
</x-layout>