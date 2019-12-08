<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="api-token" content="{{ auth()->user() ? auth()->user()->api_token : ''}}">

    <title>{{ config('app.name', 'ReadingList') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Work+Sans&display=swap" rel="stylesheet">
</head>
<body>
    <div id="app">
        @include('includes.navbar')
        <main class="py-4 main-content">
            @yield('content')
            <alert></alert>
            <archive-modal></archive-modal>
            <confirmation-modal></confirmation-modal>
            <side-bar></side-bar>
            <single-input-modal></single-input-modal>
        </main>
    </div>
</body>
</html>
