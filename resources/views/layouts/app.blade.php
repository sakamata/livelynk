<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/livelynk.css') }}" rel="stylesheet">
    <!-- favicon -->
    <link rel="shortcut icon" type="image/x-icon"  href="{{asset("img/icon/favicon_.ico")}}">
</head>
<body>
    <div id="app">
        <header>
            <div class="menu">
            </div>
            <div class="logo">
                <a class="navbar-brand" href="{{ env("INDEX_PATH") }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
            </div>
            <div class="action">
            </div>
        </header>
        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
