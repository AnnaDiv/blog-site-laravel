<nav space-y-2>
    <a href="{{route('home')}}"> Browse</a>
    @auth
        <a href="">My Homepage</a>
    @endauth
    <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'myart', 'page' => 1]); ?>">My art</a>
    <a href="{{route('categories')}}">Categories</a>
    <a href="{{route('contact_us')}}">Contact us</a>
    @if(Auth::id()=='1')
        <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'control']); ?>">Control Panel</a>
    @endif
        <a href="{{route('post.createView')}}">Create Post</a>
    @auth
        <a href="#link_to_profile">My profile</a>
        <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'liked_posts', 'page' => 1]); ?>">My likes</a>
        <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'following', 'page' => 1]); ?>">Following</a>
        <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'followers', 'page' => 1]); ?>">Followers</a>
    @endauth
</nav>