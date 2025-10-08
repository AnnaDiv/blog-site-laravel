<header>
    <h1 class="site-title"><a href="{{ route('home') }}"> Love it OR Throw it </a></h1>
    <div class="user">
            @auth
                <div class="dropdown-container">
                    <button id="dropdownToggle" class="notification-button">🔔</button>
                    <ul id="dropdownList" class="dropdown-list"></ul>
                </div>
            @endauth
        <div id="user-status" class="user-status">
            @auth
                <form action="{{route('logout')}}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            @endauth
            @guest
                <a href="{{route('login')}}">Login</a>
            @endguest
        </div>
        <div class="profile-pic">
            @auth
                @php
                    $user = auth()->user();
                @endphp
                @if ($user->image_folder)
                    <img id="profile-toggle" src="{{asset('storage/' . $user->image_folder)}}" alt="Avatar"/>
                @else
                    <img id="profile-toggle" src="{{asset('storage/user/alt/blank.jpeg')}}" alt="Avatar">
                @endif
                <nav id="side-nav-profile">
                    <a href="{{route('post.createView')}}"><i class="fa fa-plus"></i> Create Post</a>
                    <a href="{{route('profile.public', $user->nickname)}}"><i class="fa fa-user-circle"></i> My profile</a>
                    <a><form method="POST" action="{{route('logout')}}">
                        @csrf
                        <button type="submit">
                            <i class="fa fa-sign-out"></i> Logout    
                        </button>
                    </form></a>
                </nav>
            @endauth
            @guest
                <a id="side-nav-profile2" href="{{route('register')}}">Register</a>
            @endguest
        </div>
    </div>

    <div x-data="{ open: false }" class="relative">
          <button
                type="button"
                @click="open = !open"
                class="hamburger"
                aria-label="Open menu">
                &#9776;
            </button>

        <div id="side-nav"
            x-show="open"
            x-cloak
            @click.away = "open = false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed top-0 left-0 h-full w-64 bg-white shadow-lg z-50 p-4">
            <div class="flex justify-between items-center mb-4">
                <x-navigation />
            </div>
        </div> 
    </div>

</header>
<script>
    const dropdown = document.getElementById('dropdownList');
    const toggleBtn = document.getElementById('dropdownToggle');
</script>
<script src="{{asset('js/notifications.js')}}"></script>
<script src="{{asset('js/nav.js')}}"></script>