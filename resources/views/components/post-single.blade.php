@props(['post'])

<div class="post-container-single">
    <div class="post-layout-flex-single">
    
        <!-- LEFT: image -->
        <div class="post-image-single">

            <img src="{{asset('storage/' . $post->image_folder)}}" alt="Post image" />
            <br>
            <div class="post-actions-single">
                <div>
                    @if(!auth()->user()->admin)
                        @can('update', $post)
                            <div class="action-buttons-single">
                                <a href="{{ route('post.editView', $post->id) }}"><button>Edit</button></a>
                                <form method="POST" action="{{ route('post.remove', $post) }}">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit">Delete</button>
                                </form>
                            </div>
                        @endcan
                    @endif
                    @admin
                        <div class="action-buttons-single">
                            <!-- <a href="#admin_edit"><button>Edit</button></a> -->
                            @if($post->deleted == 0)
                                <form method="POST" action="{{ route('post.remove', $post) }}">
                                    @csrf
                                    @method('PUT')
                                    <button>Delete(put it in delete bin)</button>
                                </form>
                            @endif
                            @if($post->deleted == 1)
                                <form method="POST" action="{{ route('post.reinstate', $post) }}">
                                    @csrf
                                    @method('PUT')
                                    <button>Reinstate</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('post.permaDelete', $post) }}">
                                @csrf
                                @method('DELETE')
                                <button>Delete(perma delete)</button>
                            </form>
                    @endadmin
                    </div>
                </div>
                <div class="like-wrapper">
                    <span id="like-count">0</span>
                    <button id="like-toggle">
                        <img class="like-button" src="{{asset('storage/post/nolikeheart.png')}}" alt="like-image" />
                    </button>
                </div>
            </div>
        </div>

        <!-- RIGHT: title + description + categories + comments -->
        <div class="post-content-single">
        
            <h2 class="post-title-single">{{$post->title}}</h2>
            <p class="post-description-single">{{$post->content}}</p>
            <p class="post-description-single">
                Post Owner: <a href="{{route('profile.public', $post->user_nickname)}}">{{$post->user_nickname}}</a>
            </p>
        
            <div class="post-categories-single">
                <span class="category-label">Categories:</span>
                <div class="category-tags">
                    @foreach($post->categories as $category)
                        @if ($category->title !== 'none')
                            <a class="category-badge" href="#category_title_page">
                                {{ $category->title }}
                            </a>
                        @endif
                        @if(!$loop->last), @endif
                    @endforeach
                </div>
            </div>

            <x-post-comment-section />

        </div>

    </div>
</div>
<script>
const postId = {{ (int) $post->id }};
const postOwner = @json($post->user_nickname);
const userId = @json(auth()->user()->id ?? null);
const list = document.getElementById('comments-list');
const form = document.getElementById('comment-form');
const input = document.getElementById('comment-input');
//const isAdmin = '';
const likeImg = document.querySelector('#like-toggle img');
const likeToggleButton = document.getElementById('like-toggle');
const likeCountDisplay = document.getElementById('like-count');
let userLiked = 0; 
</script>
<script src="{{ asset('js/likes.js')}}"></script>
<script src="{{ asset('js/comments.js')}}"></script>