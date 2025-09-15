<x-layout>
<div class="content-layout">

    <?php /* art banner here */ ?>

    <div class="masonry-wrapper">
        <div class="masonry-container">
            @foreach($entries as $entry)
                <x-post-card :post="$entry"/>
            @endforeach
        </div>
    </div>

</div>

{{ $entries->links() }}
</x-layout>