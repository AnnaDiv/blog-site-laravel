<x-layout>
<div class="content-layout">

    <?php /* art banner here */ ?>

    <div class="masonry-wrapper">
        <div class="masonry-container">
            @forelse ($entries as $entry)
                <x-post-card :post="$entry"/>
            @empty
                <div class="bg-white">
                    Sorry, there are no posts.
                </div>
            @endforelse
        </div>
    </div>

</div>

{{ $entries->links() }}
</x-layout>