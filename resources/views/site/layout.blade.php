<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-130005437-1"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-130005437-1');
        </script>

        <title>{{ config('app.name', 'Laravel') }}</title>
        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        <script src="{{ asset('js/livelynk.js') }}" defer></script>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link href="{{ asset('css/site.css') }}" rel="stylesheet">
        <!-- Styles -->
    </head>
    <body>
        <div class="flex-center position-ref">
            <div class="content">
                <div class="title">
                    {{ config('app.name', 'Laravel') }}<br>@ Geek Office
                </div>
                <div class="message">
                    @component('components.message')
                    @endcomponent
                </div>
                <p class="lead">会員の方は専用のURLより閲覧してください。</p>
                <div class="summary-wp">
                    @yield('content')
                </div>
                <div class="footer">
                    <a href="/home">HOME</a> | <a href="/terms">利用規約</a> | <a href="/privacy">プライバシーポリシー</a>
                </div>
            </div>
        </div>
    </body>
</html>
