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
    <link rel="stylesheet" href="{{asset('css/dropdown.css')}}" />
    <link rel="stylesheet" href="{{asset('css/create_edit_post.css')}}" />
    <link rel="stylesheet" href="{{asset('css/simple-text.css')}}" />
    <link rel="stylesheet" href="{{asset('css/login-form.css')}}" />
    <link rel="stylesheet" href="{{asset('css/category-list.css')}}" />
    <link rel="stylesheet" href="{{asset('css/my_art.css')}}" />


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite('resources/css/app.css')

    <script src="//unpkg.com/alpinejs" defer></script>

    <title>{{$title ?? 'Love it OR throw it'}}</title>
</head>

<body>
    <x-header />

    @if(request()->is('/') || request()->is('/main') || request()->is('/browse') 
        || request()->is('/search') || request()->is('/search_user'))
        <x-search-bar/>
    @endif
    <main class="container">
        {{-- Display alert messages --}}
        @if(session('success'))
            <x-alert type="success" message="{{session('success')}}" />
        @endif
        @if(session('error'))
            <x-alert type="error" message="{{session('error')}}" />
        @endif
        {{ $slot }}
    </main>

    </div>

</body>

</html>