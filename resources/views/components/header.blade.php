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
                <a href="">Logout</a>
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
                    <a href="#logout">Logout</a>
                </nav>
            @endauth
            @guest
                <a id="side-nav-profile2" href="{{route('register')}}">Register</a>
            @endguest
        </div>
    </div>

    <!-- Hamburger menu button -->
    <button id="menu-toggle" class="hamburger">&#9776;</button>

    <!-- Sidebar nav -->
    <div id="side-nav">
        <x-navigation />
    </div>


</header>
<script>
    const dropdown = document.getElementById('dropdownList');
    const toggleBtn = document.getElementById('dropdownToggle');
</script>
<script src="{{asset('js/notifications.js')}}"></script>
<script src="{{asset('js/nav.js')}}"></script>