<!-- Comments section below content -->
<div id="comments-section">
    <h3>Comments</h3>
    <div id="comments-list"></div>

    @auth
        Comment as: {{auth()->user()->nickname}}
        <form id="comment-form">
            @if (auth()->user()->image_folder)
                <img id="profile-pic-comments" src="{{asset('storage/' . auth()->user()->image_folder)}}" alt="Profile Picture">
            @else
                <img id="profile-pic-comments" src="{{asset('storage/user/alt/blank.jpeg')}}" alt="Profile Picture">
            @endif
            
            <textarea id="comment-input" placeholder="Write a comment..." required></textarea>
            <button type="submit">Post</button>
        </form>
    @endauth
</div>
