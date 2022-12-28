<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />


    <link rel="stylesheet" href="{{ asset('backend/admin/css/style.css') }}">
    <title>manager-app</title>
    
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script> --}}
</head>

<body>
    <!-- Start Header -->
    <header>
        <nav class="container">
            <ul class="links">
                <li class=""><a href="{{ route('cacheStatus') }}">Statstics</a></li>
                <li class=""><a href="{{ route('poolResizing') }}">Pool resizing</a></li>
                <li class=""><a href="{{ route('cache-config') }}">Cache configiration</a></li>
            </ul>
        </nav>
    </header>
    <!-- End Header -->

    @yield('content')

    <script src="{{ asset('backend/admin/js/main.js') }}"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    @yield('script')
</body>

</html>
