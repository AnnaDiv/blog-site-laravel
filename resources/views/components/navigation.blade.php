<nav space-y-2>
    @admin
        <a href="{{ route('admin.panel') }}">Control Panel <i class="fa fa-cog"></i></a>
    @endadmin
    <a href="{{route('home')}}">Browse <i class="fa fa-list-alt"></i></a>
    @auth
        <a href="#myhome">My Homepage <i class="fa fa-home"></i></a>
    @endauth
    <a href="#my_art">My art <i class="fa fa-paint-brush"></i></a>
    <a href="{{route('categories')}}">Categories <i class="fa fa-list-ol"></i></a>
    <a href="{{route('contact_us')}}">Contact us <i class="fa fa-envelope-open"></i></a>
    <a href="{{route('account.help')}}">Account Help <i class="fa fa-envelope-open"></i></a>
    <a href="{{route('post.createView')}}">Create Post <i class="fa fa-plus"></i></a>
    @auth
        <a href="{{route('profile.public', auth()->user()->nickname)}}">My profile <i class="fa fa-user-circle"></i></a>
        <a href="{{ route('mylikes') }}">My likes <i class="fa fa-heart"></i></a>
        <a href="{{ route('following') }}">Following <i class="fa fa-user-plus"></i></a>
        <a href="{{ route('followers') }}">Followers <i class="fa fa-users"></i></a>
    @endauth
</nav>