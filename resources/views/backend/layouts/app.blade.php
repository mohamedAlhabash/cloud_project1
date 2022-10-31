<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('backend/css/normalize.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/style.css') }}">

    <title>cloud</title>
</head>

<body>
    <!-- Start Header -->
    <header>
        <nav class="container">
            <ul class="links">
                <li class="{{ request()->routeIs('index') ? 'active' : '' }}  "><a href="{{route('index')}}">Upload</a></li>
                <li class="{{ request()->routeIs('image') ? 'active' : '' }}  "><a href="{{route('image')}}">Images</a></li>
                <li class="{{ request()->routeIs('keys') ? 'active' : '' }}  "><a href="{{route('keys')}}">Keys</a></li>
                <li class="{{ request()->routeIs('cache-config') ? 'active' : '' }}  "><a href="{{route('cache-config')}}">Cache Setting </a></li>
                <li class="{{ request()->routeIs('cacheStatus') ? 'active' : '' }}  "><a href="{{route('cacheStatus')}}">Statistics</a></li>
            </ul>
        </nav>
    </header>
    <!-- End Header -->
    @yield('content')
    <script src="{{ asset('backend/js/main.js') }}"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="{{ asset('build/assets/app.afe1e09a.js') }}"></script>
    @yield('script')
</body>

</html>
