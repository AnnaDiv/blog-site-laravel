<x-layout>
<div class="content-layout">

    <?php /* art banner here */ ?>

    <div class="masonry-wrapper">
        <div class="masonry-container">
            @forelse ($posts as $post)
                <x-post-card :post="$post"/>
            @empty
                <div class="bg-white">
                    Sorry, there are no posts.
                </div>
            @endforelse
        </div>
    </div>

</div>

{{ $posts->links() }}
</x-layout>