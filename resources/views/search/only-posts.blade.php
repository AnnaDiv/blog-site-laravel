<x-layout>
    @if(request()->is('myart'))
        <div id="myart-title">
            <div class="textDiv">
                <div class="layer">
                My art page
                </div>
            </div>
        </div>
    @endif
    <h2 class="item-title">@if($title) {{$title ?? ''}} @endif </h2>
    <div class="masonry-container posts-container">
            @forelse ($posts as $post)
                <x-post-card :post="$post"/>
            @empty
                <div class="bg-white">
                    Sorry, there are no posts.
                </div>
            @endforelse
    </div>
</x-layout>