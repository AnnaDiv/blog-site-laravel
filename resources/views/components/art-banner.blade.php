@props(['art_images'])

<a href="{{ route('myart') }}">
    <div class="banner">
        <div class="banner-title">My art</div>
            @foreach($art_images as $index => $art_image)
                <img class="art_image <?php echo $index === 0 ? 'active' : ''; ?>" 
                    src="{{asset('storage/' . $art_image->image_folder)}}" 
                    alt="Art image">
            @endforeach
        <div class="banner-desc">^^^^<br>Check my art page out</div>
    </div>
</a>