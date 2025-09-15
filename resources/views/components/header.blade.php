<header>
    <h1 class="site-title"> Love it OR Throw it </h1>
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
                <img id="profile-toggle" src="" alt="Profile Picture">
                <nav id="side-nav-profile">
                    <a href="#create_post">Create Post</a>
                    <a href="#link_to_profile">My profile</a>
                    <a href="{{route('logout')}}">Logout</a>
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