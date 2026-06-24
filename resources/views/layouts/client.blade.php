<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Rẻo Cao Journeys - Chạm vào nguyên bản')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="overflow-x-hidden">

    @include('layouts.partials.client-header')

    <main>
        @yield('content')
    </main>

    @include('layouts.partials.ai-chatbot')

    @include('layouts.partials.client-footer')

    @include('layouts.partials.search-modal')

    @stack('scripts')
</body>
</html>
