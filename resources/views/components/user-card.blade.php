@props(['user'])

<div class="masonry-item">
    <a href="{{ route('profile.public', $user->nickname) }}">
        @if($user->image_folder)
            <img class="item_image" src="{{asset('storage/' . $user->image_folder)}}" alt="User image">
        @else
            <img class="item_image" src="{{asset('storage/user/alt/blank.jpeg')}}" alt="User image"/>
        @endif
    </a>
    <a href="{{ route('profile.public', $user->nickname) }}">
        <h4 class="post-title">{{ $user->nickname }}</h4>
    </a>
    <p class="post-description">
        Motto: {{ $user->motto }}
    </p>
    <div class="post-categories">
        Posts: {{ $user->posts_count }} <br>
        Comments: {{ $user->comments_count }}<br>
        Likes: {{ $user->likes_count }}
    </div>
</div>