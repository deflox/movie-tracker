<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Movies') }}</title>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chartist.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <script>
        window.MovieTracker = {!! json_encode([
            'baseUrl' => URL::to('/')
        ]) !!};
    </script>

</head>
<body>
    @yield('content')
</body>

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/bootbox.min.js') }}"></script>
<script src="{{ asset('js/chartist.min.js') }}"></script>
<script src="{{ asset('js/api.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
@yield('js')

</html>