<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{asset('css/normalize.css')}}" />
    <link rel="stylesheet" href="{{asset('css/nav_n_top.css')}}" />
    <link rel="stylesheet" href="{{asset('css/main.css')}}" />
    <link rel="stylesheet" href="{{asset('css/pagination.css')}}" />
    <link rel="stylesheet" href="{{asset('css/post.css')}}" />
    <link rel="stylesheet" href="{{asset('css/profile.css')}}" />
    <link rel="stylesheet" href="{{asset('css/profile_edit.css')}}" />
    <link rel="stylesheet" href="{{asset('css/dropdown.css')}}" />
    <link rel="stylesheet" href="{{asset('css/create_edit_post.css')}}" />
    <link rel="stylesheet" href="{{asset('css/simple-text.css')}}" />
    <link rel="stylesheet" href="{{asset('css/login-form.css')}}" />
    <link rel="stylesheet" href="{{asset('css/category-list.css')}}" />
    <link rel="stylesheet" href="{{asset('css/my_art.css')}}" />
    <link rel="stylesheet" href="{{asset('css/admin.css')}}" />
    {{-- DM window styles --}}
    <link rel="stylesheet" href="{{asset('css/conversation.css')}}" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css ">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css "
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite('resources/css/app.css')

    <script src="//unpkg.com/alpinejs" defer></script>

    <title>{{$title ?? 'Love it OR throw it'}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body data-auth-id="{{ auth()->id() }}">
    <x-header />
    @if(request()->is('browse') || request()->is('search/posts') || 
        request()->is('search/users') || request()->is('category/*')
        || request()->is('myfeed'))
        <x-search-bar/>
    @endif
    <main>
        <!-- Display alert messages -->
        @if(session('success'))
            <div class="alert"><x-alert type="success" message="{{session('success')}}" /></div>
        @endif
        @if(session('error'))
            <div class="alert"><x-alert type="error" message="{{session('error')}}" /></div>
        @endif
        {{ $slot }}

        <!-- =====  DM CHAT  ===== -->
        @auth
        {{-- 1.  BAR (click to toggle)  --}}
        <div id="dm-bar" class="dm-bar">
            <i class="fa-solid fa-chevron-up" id="dm-chevron" onclick="toggleChat()"></i>
            <span id="dm-bar-name" onclick="toggleChat()"></span>
            <i class="fa-solid fa-xmark close-btn" onclick="closeChat(event)"></i>
        </div>

        {{-- 2.  PANEL --}}
        <div id="dm-overlay" class="dm-panel" style="display:none">

            <!-- hidden node that openDM fills -->
            <span id="dm-title" style="display:none;"></span>

            <div id="dm-messages" class="dm-messages"></div>
            <form id="dm-form" class="dm-composer" onsubmit="sendDM(event)">
                <input id="dm-input" type="text" placeholder="Type…" required>
                <button>Send</button>
            </form>
        </div>
        @endauth
    </main>

{{-- DM core script --}}
<script>
    /* ----------  CONFIG  ---------- */
    const API      = "{{ url('api') }}";          // /api
    const CSRF     = "{{ csrf_token() }}";
    let   authToken= null;                        // in-memory only
    let   currentConv= null;
    let   pollTimer  = null;
    const POLL_MS  = 2000;

    /* ----------  UI HELPERS  ---------- */
    const $ = s => document.querySelector(s);
    const msgBox = $('#dm-messages');
    const input  = $('#dm-input');
    const mine = Number(document.body.dataset.authId);
</script>
<script src="{{ asset('js/messaging.js') }}"></script>
</body>
</html>