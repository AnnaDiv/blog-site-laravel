<a href="#my_art_link">
    <div class="banner">
        <div class="banner-title">My art</div>
            <?php foreach($art_images as $index => $art_image) : ?>
                <img class="art_image <?php echo $index === 0 ? 'active' : ''; ?>" 
                    src="{{$art_image->image_folder}}" 
                    alt="Post image">
            <?php endforeach; ?>
        <div class="banner-desc">^^^^<br>Check my art page out</div>
    </div>
</a>