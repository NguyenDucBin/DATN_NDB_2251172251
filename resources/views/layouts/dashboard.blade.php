<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Reo Cao Journeys') }} - Dashboard</title>

    @include('layouts.partials.font-preload')

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-gray-900 overflow-hidden"
      x-data="dashboardLayout()"
      @submit="interceptSubmit($event)"
      @keydown.escape.window="confirmOpen ? closeConfirmation() : null">
    <div class="flex h-screen bg-gray-50">
        <!-- Mobile sidebar backdrop -->
        <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-20 transition-opacity bg-gray-900 bg-opacity-50 lg:hidden" @click="sidebarOpen = false"></div>

        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'" class="fixed inset-y-0 left-0 z-30 w-64 overflow-y-auto transition duration-300 transform bg-white border-r border-gray-200 lg:translate-x-0 lg:static lg:inset-0">
            <div class="flex items-center justify-center h-16 bg-white border-b border-gray-200">
                <a href="/" class="text-2xl font-bold text-emerald-600">Reo Cao</a>
            </div>

            <nav class="p-4 space-y-1">
                @hasrole('admin')
                <div class="text-xs font-semibold tracking-wider text-gray-400 uppercase mt-4 mb-2">Admin Panel</div>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-gray-700 transition-colors rounded-lg hover:bg-emerald-50 hover:text-emerald-600 {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-50 text-emerald-600' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Tổng quan
                </a>
                <a href="{{ route('admin.posts.index') }}" class="flex items-center px-4 py-2 text-gray-700 transition-colors rounded-lg hover:bg-emerald-50 hover:text-emerald-600 {{ request()->routeIs('admin.posts.*') ? 'bg-emerald-50 text-emerald-600' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-.586-1.414l-4.5-4.5A2 2 0 0012.586 3H12"></path></svg>
                    Quản lý Bài viết
                </a>
                <a href="{{ route('admin.approvals') }}" class="flex items-center px-4 py-2 text-gray-700 transition-colors rounded-lg hover:bg-emerald-50 hover:text-emerald-600 {{ request()->routeIs('admin.approvals') ? 'bg-emerald-50 text-emerald-600' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Duyệt Tour
                </a>
                <a href="{{ route('admin.transactions') }}" class="flex items-center px-4 py-2 text-gray-700 transition-colors rounded-lg hover:bg-emerald-50 hover:text-emerald-600 {{ request()->routeIs('admin.transactions') ? 'bg-emerald-50 text-emerald-600' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Giao dịch & Doanh thu
                </a>
                <a href="{{ route('admin.coupons.index') }}" class="flex items-center px-4 py-2 text-gray-700 transition-colors rounded-lg hover:bg-emerald-50 hover:text-emerald-600 {{ request()->routeIs('admin.coupons.*') ? 'bg-emerald-50 text-emerald-600' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    Mã giảm giá
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-2 text-gray-700 transition-colors rounded-lg hover:bg-emerald-50 hover:text-emerald-600 {{ request()->routeIs('admin.users.*') ? 'bg-emerald-50 text-emerald-600' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Người dùng
                </a>
                <a href="{{ route('admin.refunds') }}" class="flex items-center px-4 py-2 text-gray-700 transition-colors rounded-lg hover:bg-emerald-50 hover:text-emerald-600 {{ request()->routeIs('admin.refunds') ? 'bg-emerald-50 text-emerald-600' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Hoàn tiền
                </a>
                @endhasrole

                @hasrole('host')
                <div class="text-xs font-semibold tracking-wider text-gray-400 uppercase mt-4 mb-2">Host Panel</div>
                <a href="{{ route('host.dashboard') }}" class="flex items-center px-4 py-2 text-gray-700 transition-colors rounded-lg hover:bg-emerald-50 hover:text-emerald-600 {{ request()->routeIs('host.dashboard') ? 'bg-emerald-50 text-emerald-600' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Tổng quan
                </a>
                <a href="{{ route('host.tours.index') }}" class="flex items-center px-4 py-2 text-gray-700 transition-colors rounded-lg hover:bg-emerald-50 hover:text-emerald-600 {{ request()->routeIs('host.tours.*') ? 'bg-emerald-50 text-emerald-600' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Quản lý Tour
                </a>
                <a href="{{ route('host.bookings') }}" class="flex items-center px-4 py-2 text-gray-700 transition-colors rounded-lg hover:bg-emerald-50 hover:text-emerald-600 {{ request()->routeIs('host.bookings') ? 'bg-emerald-50 text-emerald-600' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    Quản lý Booking
                </a>
                <a href="{{ route('host.inbox') }}" class="flex items-center px-4 py-2 text-gray-700 transition-colors rounded-lg hover:bg-emerald-50 hover:text-emerald-600 {{ request()->routeIs('host.inbox') ? 'bg-emerald-50 text-emerald-600' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    Tin nhắn
                </a>
                @endhasrole
            </nav>
        </div>

        <!-- Main content -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Header -->
            <header class="flex items-center justify-between px-6 py-4 bg-white border-b border-gray-200">
                <div class="flex items-center">
                    <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    
                    @isset($header)
                        <h2 class="ml-4 text-xl font-semibold text-gray-800 lg:ml-0">{{ $header }}</h2>
                    @endisset
                </div>

                <div class="flex items-center">
                    <div class="relative" x-data="{ dropdownOpen: false }">
                        <button @click="dropdownOpen = !dropdownOpen" class="flex items-center focus:outline-none">
                            <img class="object-cover w-8 h-8 rounded-full ring-2 ring-emerald-500" src="{{ auth()->user()->avatarUrl(64) }}" alt="{{ auth()->user()->name }}">
                            <span class="ml-2 text-sm font-medium text-gray-700 hidden sm:block">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4 ml-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-show="dropdownOpen" x-cloak @click.away="dropdownOpen = false" class="absolute right-0 z-20 w-48 mt-2 overflow-hidden bg-white rounded-md shadow-xl ring-1 ring-black ring-opacity-5">
                            <a href="{{ route('profile.overview') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    <div x-show="confirmOpen" x-cloak class="fixed inset-0 z-50" role="dialog" aria-modal="true" aria-labelledby="confirm-title">
        <div class="fixed inset-0 bg-gray-900/60" @click="closeConfirmation()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div x-show="confirmOpen"
                 x-transition
                 class="relative w-full max-w-md overflow-hidden rounded-lg bg-white shadow-xl">
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </span>
                        <div>
                            <h2 id="confirm-title" class="text-base font-semibold text-gray-900">Xác nhận thao tác</h2>
                            <p class="mt-2 text-sm leading-6 text-gray-600" x-text="confirmMessage"></p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 bg-gray-50 px-6 py-4">
                    <button type="button" @click="closeConfirmation()" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                        Hủy
                    </button>
                    <button type="button" @click="confirmAction()" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                        Đồng ý
                    </button>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
