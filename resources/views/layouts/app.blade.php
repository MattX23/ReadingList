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
</head>
<body>
    <div id="app">
        @include('includes.navbar')
        <main class="py-4">
            @yield('content')
            <single-input-modal></single-input-modal>
            <selection-modal></selection-modal>
            <confirmation-modal></confirmation-modal>
            <side-bar></side-bar>
            <alert></alert>
        </main>
    </div>
</body>
</html>
