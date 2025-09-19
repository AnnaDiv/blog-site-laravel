@props(['post'])

<div class="masonry-item">
    <a href="{{route('post.view', $post->id)}}">
        <img class="item_image" src="{{asset('storage/' . $post->image_folder)}}" alt="Post image">
    </a>
    <a href="{{route('post.view', $post)}}">
        <h4 class="post-title">{{$post->title}}</h4>
    </a>
    <p class="post-description">{{$post->content}}</p>
    <div class="post-categories">
        @foreach($post->categories as $category)
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
            {{ $post->user_nickname }}
            </a>
        </div>
        <div class="hover-wrapper">
            <img class="dots" src="{{asset('storage/post/3dots_.png')}}" alt="Options">
            <div class="hover-info">
                <span>Likes: {{ $post->likes_count }}</span><br>
                <span>Comments: {{ $post->comments_count }}</span><br>
                <span>Time: {{ $post->time }}</span>
            </div>
        </div>
    </div>
</div>