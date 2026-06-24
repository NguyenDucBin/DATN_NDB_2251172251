<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('code') - @yield('title')</title>
    @include('layouts.partials.font-preload')
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-[#FAF9F6] text-gray-900">
    <main class="flex min-h-screen items-center justify-center px-6 py-16">
        <div class="w-full max-w-xl text-center">
            <a href="{{ route('home') }}" class="mb-10 inline-flex items-center gap-3 text-[#1E3F20]">
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-[#1E3F20] text-white"><i class="fa-solid fa-mountain-sun"></i></span>
                <span class="font-serif text-2xl font-bold">Rẻo Cao Journeys</span>
            </a>
            <p class="font-serif text-7xl font-bold text-[#D4AF37]">@yield('code')</p>
            <h1 class="mt-4 font-serif text-3xl font-bold text-[#1E3F20]">@yield('title')</h1>
            <p class="mx-auto mt-4 max-w-md leading-relaxed text-gray-600">@yield('message')</p>
            <a href="{{ route('home') }}" class="mt-8 inline-flex items-center gap-2 rounded-lg bg-[#1E3F20] px-6 py-3 font-bold text-white hover:bg-[#2A5A2E]">
                <i class="fa-solid fa-house"></i> Về trang chủ
            </a>
        </div>
    </main>
</body>
</html>
