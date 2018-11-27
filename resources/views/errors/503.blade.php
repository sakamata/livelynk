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
    <script src="{{ asset('js/livelynk.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">
    <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">

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
                <div id="nav-drawer">
                    <input id="nav-input" type="checkbox" class="nav-unshown">
                    <label id="nav-open" for="nav-input"><span></span></label>
                    <label class="nav-unshown" id="nav-close" for="nav-input"></label>
                    <div id="nav-content">
                        <div class="head">
                            <label class="nav-unshown" for="nav-input">MENU</label>
                        </div>
                        <nav>
                            @component('components.header_menu')
                            @endcomponent
                            @component('components.header_menu_extra')
                            @endcomponent
                        </nav>
                    </div>
                </div>
            </div>
            <div class="logo">
                @guest
                <a class="navbar-brand" href="/index">
                @else
                <a class="navbar-brand" href="/">
                @endguest
                    {{ config('app.name', 'Laravel') }}
                </a>
            </div>
            <div class="action">
            </div>
        </header>
    @section('content')
@component('components.header_menu')

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header"><h2>503 Livelynkは現在メンテナンス中です</h2></div>
                        <div class="card-body">
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif
                            <p>現在メンテナンスを行っています。復旧まで今しばらくおまちください。</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

