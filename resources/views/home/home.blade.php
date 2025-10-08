<x-layout>
<div class="content-layout-home">
    <h1 style="text-align: center;">Welcome!</h1>

    <x-art-banner :art_images="$art_images"/>

    <div id="intro">
        <div class="simple-text-intro">
            <h2>Welcome to my site!</h2>
            <p>I made this by myself, pretty cool right?<br>
                <br>I am learning to code by making this site.<br>
                Looking for work and excited to continue learning how to code in depth<br>
                <br>Please check out my site and socials<br>
            </p>
        </div>
        <div class="simple-text-intro" style="display:flex; justify-content:space-between">
            <div>
                <h2>Find me on linkedin!</h2>
                <p>
                    <a href="https://www.linkedin.com/"><i class="fab fa-linkedin"></i>LinkedIn</a>
                </p>
            </div>
            <div style="margin-right:2rem">
                <h2>Email me!</h2>
                <p>annikoulini001@gmail.com</p>
            </div>
        </div>
    </div>

    <div id="social-info">
        <div class="simple-text-banner">
            <h2>Visit my GitHub!</h2>
            <a href="https://github.com/AnnaDiv"><i class="fab fa-github" aria-hidden="true"></i>AnnaDiv</a>
            <span class="gh-stats">
                <small>🌟 3 repos</small>
            </span>
        </div>
        <div class="simple-text-banner">
            <h2>PHP Vanilla Repo</h2>
            <a href="https://github.com/AnnaDiv/site-blog-type">PHP Vanilla Site</a>
        </div>
        <div class="simple-text-banner">
            <h2>Laravel 12 Repo</h2>
            <a href="https://github.com/AnnaDiv/blog-site-laravel">Laravel 12 Site</a>
        </div>
    </div>

</div>

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