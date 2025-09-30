<x-layout>

<div id="edit-post-title"> Lets edit our post </div>

<form method="POST" autocomplete="off" enctype="multipart/form-data" action="{{ route('post.edit', $post) }}">
    
    @csrf

    @method('PUT')

    <div class="post-container-create">
        <div class="post-layout-flex-create">

            <!-- LEFT: image -->
            <div class="post-image-create">
                <div class="image-preview-wrapper">
                    <img id="post_image" src="{{asset('storage/' . $post->image_folder)}}">
                    <input type="file" name="image" id="image">

                    <!-- Spinner -->
                    <div class="image-spinner" id="image-spinner">
                        <div class="spinner"></div>
                    </div>

                    <!-- Progress bar -->
                    <div id="upload-progress-bar"><div class="bar"></div></div>
                </div>
            </div>
            <!-- RIGHT: title + description -->
            <div class="post-content-create">
                <div class="post-form-group">
                    <h2 class="post-title-create">
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title" value="{{old('title') ?? $post->title }}" required/>
                    </h2>

                    <div class="post-description-create">
                        <label for="description" class="description-label">Description</label> 
                        <textarea name="description" id="description" required>{{ old('description') ?? $post->content }}</textarea>
                    </div>

                    @php
                        $post_t = $post->status;
                        $post_t = ($post_t === 'public') ? 'public' : 'private';
                    @endphp

                    <div class="post-visibility-create">
                        <label for="post_status">Post visibility:</label>
                        <select id="post_status" name="post_status">
                            <option value="public" @php $post_t === 'public' ? 'selected' : ''; @endphp>Public</option>
                            <option value="private" @php $post_t === 'private' ? 'selected' : ''; @endphp>Private</option>
                        </select>
                    </div>

                    <div class="post-categories-create">
                        <label for="categories">Categories</label>
                        <ul id="cats" style="list-style: none; padding: 0; display: flex; flex-wrap: wrap; gap: 8px;"></ul>
                        <input type="hidden" name="categories" id="categories" />
                    </div>

                    <div class="post-categories-create">
                        <label for="category">Enter Category</label>
                        <div style="display:flex" class="autocomplete" style="width:300px;">
                            <input type="text" name="category" id="category"><button onclick="transport_value(event)">Enter</button>
                        </div>
                    </div>

                    <input type="submit" name="submit" value="Update"/>
                </div>

            </div>

        </div>
    </div>

</form>
</x-layout>
<?php foreach ($errors AS $error) {
    echo $error;
}
?>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const userID = @json(auth()->user()->id ?? null);
const spinner = document.getElementById('image-spinner');
const progressBarWrapper = document.getElementById('upload-progress-bar');
const progressBar = progressBarWrapper.querySelector('.bar');
const imageInput = document.getElementById('image');
const previewImage = document.querySelector('#post_image');
</script>
<script src="{{asset('js/imageCreator.js')}}" defer></script>
<script src="{{asset('js/categories.js')}}"></script>
<script src="{{ asset('js/autocomplete.js') }}"></script>
@php 
$category_titles = $post->categories->pluck('title')->toArray();
@endphp

<script>
var categories_values = @json($category_titles);

display_values();

var category = [];

@foreach($allcategories as $allcategory)
    category.push("{{ $allcategory }}");
@endforeach

autocomplete(document.getElementById("category"), category);
</script>