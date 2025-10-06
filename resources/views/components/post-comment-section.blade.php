<!-- Comments section below content -->
<div id="comments-section">
    <h3>Comments:</h3>
    <div id="comments-list" style="margin-left:1rem"></div>
    <br>
    @auth
        <div style="margin-left:1rem"> Comment as: {{auth()->user()->nickname}}</div>
        <form id="comment-form" style="margin-left:1rem">
            @if (auth()->user()->image_folder)
                <a href="{{route('profile.public', auth()->user()->nickname)}}"><img id="profile-pic-comments" src="{{asset('storage/' . auth()->user()->image_folder)}}" alt="Profile Picture"></a>
            @else
                <a href="{{route('profile.public', auth()->user()->nickname)}}"><img id="profile-pic-comments" src="{{asset('storage/user/alt/blank.jpeg')}}" alt="Profile Picture"></a>
            @endif
            <textarea id="comment-input" placeholder="Write a comment..." required></textarea>
            <button type="submit">Post</button>
        </form>
    @endauth
</div>
