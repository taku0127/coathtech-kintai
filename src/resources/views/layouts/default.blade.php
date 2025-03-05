<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title','勤怠管理アプリ')</title>
    <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    @stack('css')
</head>
<body>
    @include('partials.header')
    <main class="l-container">
        @yield('content')
    </main>
    @stack('javascript')
</body>
</html>
