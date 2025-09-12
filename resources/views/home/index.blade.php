<x-layout>
<div class="content-layout">

    <?php /* art banner here */ ?>

    <div class="masonry-wrapper">
        <div class="masonry-container">
            <?php foreach ($entries as $entry): ?>
                <div class="masonry-item">
                    <a href="#link_for_showing_post">
                        <img class="item_image" src="{{$entry->image_folder}}" alt="Post image">
                    </a>
                    <a href="#link_for_showing_post">
                        <h4 class="post-title">{{$entry->title}}</h4>
                    </a>
                    <p class="post-description">{{$entry->content}}</p>
                    <div class="post-categories">
                        @foreach($entry->categories as $category)
                            @if ($category->title !== 'none')
                                <a class="category-badge" href="#category_title_page">
                                    {{ $category->title }}
                                </a>
                            @endif
                            @if(!$loop->last), @endif
                        @endforeach
                    </div>
                    <div class="post-categories2">
                        <div class="post-owner">
                            By: <a href="#link_taking_to_owners_profile">
                            {{ $entry->user_nickname }}
                            </a>
                        </div>
                        <div class="hover-wrapper">
                            <img class="dots" src="./content/post/3dots_.png" alt="Options">
                            <div class="hover-info">
                                <span>Likes: {{ $entry->likes }}</span><br>
                                <span>Comments: {{ $entry->comments }}</span><br>
                                <span>Time: {{ $entry->time }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>

{{ $entries->links() }}
</x-layout>