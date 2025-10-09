<x-layout>
<div class="content-layout">
    
    <x-art-banner :art_images="$art_images"/>
    
    <div class="masonry-wrapper">
        <div class="masonry-container">
            @forelse ($posts as $post)
                <x-post-card :post="$post"/>
            @empty
                <div class="bg-white">
                    Sorry, there are no posts.
                </div>
            @endforelse
        </div>
    </div>

</div>

{{ $posts->links() }}
</x-layout>
<script>
  const images = document.querySelectorAll('.banner .art_image');
  let current = 0;

  function showNextImage() {
    images[current].classList.remove('active');
    current = (current + 1) % images.length;
    images[current].classList.add('active');
  }

  setInterval(showNextImage, 3000); // Change every 3 seconds
</script>