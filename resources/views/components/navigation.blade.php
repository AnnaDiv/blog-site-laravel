<nav>
    <a href="{{route('home')}}"> Browse</a>
    @auth
        <a href="">My Homepage</a>
    @endauth
    <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'myart', 'page' => 1]); ?>">My art</a>
    <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'categories', 'page' => 1 ]); ?>">Categories</a>
    <a href="{{route('contact_us')}}">Contact us</a>
    @if(Auth::id()=='1')
        <a href="index.php?<?php echo http_build_query(['route' => 'admin' , 'pages' => 'control']); ?>">Control Panel</a>
    @endif
        <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'create']); ?>">Create Post</a>
    @auth
        <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'profile', 'nickname' => $_SESSION['nickname'], 'page' => 1]); ?>">My profile</a>
        <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'liked_posts', 'page' => 1]); ?>">My likes</a>
        <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'following', 'page' => 1]); ?>">Following</a>
        <a href="index.php?<?php echo http_build_query(['route' => 'client' , 'pages' => 'followers', 'page' => 1]); ?>">Followers</a>
    @endauth
</nav>