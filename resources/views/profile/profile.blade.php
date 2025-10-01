<x-layout>
<div class="user_Profile">
    <div class="user_Profile-info">
        <div class="user_Profile-info__name">
            <h2 class="username">User: {{$profile_owner->nickname}}</h2>
            @if ($profile_owner->image_folder)
                <img class="user_Profile-info__image" src="{{asset('storage/' . $profile_owner->image_folder)}}" alt=""/>
            @else
                <img class="user_Profile-info__image" src="{{asset('storage/user/alt/blank.jpeg')}}" alt=""/>
            @endif
        </div>
        @if($profile_owner->motto)
            <div class="user_Profile-info__desc">
                <p class="motto">Motto: {{$profile_owner->motto }}</p>
            </div>
        @endif
    </div>
    @can('update', $profile)
        <a class="user_Profile-update" href="{{ route('profile.edit', $profile) }}">
            <button>Update your profile</button>
        </a>
    @endcan
    @guest
        <div class="follow-wrapper">
            <div id="followers_text">Followers:&nbsp</div><span id="follow-count">0</span> <br><br><br><br>
        </div>
    @endguest
    @auth
        @if (auth()->user()->id != $profile->user_id)
            <div class="follow-wrapper">
                <div id="followers_text"> Followers:&nbsp</div><span id="follow-count">0</span> <br><br><br><br>
                <button id="follow-toggle">
                    <img class="like-button" src="{{asset('storage/post/not_follow.png')}}" alt="follow-image" />
                </button>
            </div>
        @endif
        @if ((auth()->user()->id != $profile->user_id) && ($profile->user_id != 1))
            <div class="follow-wrapper">
                <button id="block-toggle">
                    <img class="like-button" src="{{asset('storage/post/unblocked.png')}}" alt="block-image" />
                </button>
            </div>
        @endif
    @endauth
</div>

<div class="content-layout-profile">
    <div class="profile-status-switch">    
        @can('update', $profile)
            <div class="dropdown-div">
                <div>Current Profile view: @if (request()->is('profile/' . $profile_owner->nickname)) Public @else Private @endif</div>
                <div class="dropdown">
                    <button onclick="dropdown_function()" class="dropbtn">Switch</button>
                    <div id="postDropdown" class="dropdown-content">
                        <a href="{{route('profile.public', $profile_owner->nickname)}}">Public Posts</a>
                        <a href="{{route('profile.private', $profile_owner->nickname)}}">Private Posts</a>
                    </div>
                </div> 
            </div>
        @endcan
    </div>

    <div class="masonry-container-profile">
        @forelse ($posts as $post)
            <x-post-card :post="$post"/>
        @empty
            <div class="bg-white">
                Sorry, there are no posts.
            </div>
        @endforelse
    </div>

    {{ $posts->links() }}
</div>

<script>
const profileUserNickname = @json($profile_owner->nickname);
const currentUserNickname = @json(auth()->user()->nickname ?? null);
/*console.log("Using postId:", postId); */
const FollowToggleButton = document.getElementById('follow-toggle');
const FollowCountDisplay = document.getElementById('follow-count');
let isFollowing = 0;

const BlockToggleButton = document.getElementById('block-toggle');

const FollowImgUrl = @json(asset('storage/post/follow.png'));
const NotFollowImgUrl = @json(asset('storage/post/not_follow.png'));

const BlockedImgUrl = @json(asset('storage/post/blocked.png'));
const UnblockedImgUrl = @json(asset('storage/post/unblocked.png'));
</script>
<script src="{{ asset('js/follow.js') }}"></script>
<script src="{{ asset('js/block.js') }}"></script>
<script src="{{ asset('js/dropdown.js') }}"></script>
</x-layout>
