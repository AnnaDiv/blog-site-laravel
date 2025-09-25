@props(['post'])

<div class="post-container-single">
    <div class="post-layout-flex-single">
    
        <!-- LEFT: image -->
        <div class="post-image-single">

            <img src="{{asset('storage/' . $post->image_folder)}}" alt="Post image" />
            <div class="post-actions-single">
                @can('update', $post)
                    <div>
                        <div class="action-buttons-single">
                            <a href="{{ route('post.editView', $post->id) }}"><button>Edit</button></a>
                            <form method="POST" action="{{ route('post.remove', $post) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Delete</button>
                            </form>
                        </div>
                    <?php /* if($isadmin === true) : ?>
                    <div class="action-buttons-single">
                        <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'edit/post', 'post_id' => $post['posts_id']]); ?>"><button>Edit</button></a>
                        <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'delete/post', 'post_id' => $post['posts_id']]); ?>"><button>Delete(put it in delete bin)</button></a>
                        <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'perma_delete/post', 'post_id' => $post['posts_id']]); ?>"><button>Delete(perma delete)</button></a>
                        <?php if($post['deleted'] == 1): ?>
                            <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'reinstate/post', 'post_id' => $post['posts_id']]); ?>"><button>Reinstate</button></a>
                        <?php endif; ?>
                    </div>
                    <?php endif; */ ?>
                    </div>
                @endcan
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
                Post Owner: <a href="#owner_profile">{{$post->user_nickname}}</a>
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
<meta name="csrf-token" content="{{ csrf_token() }}">
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