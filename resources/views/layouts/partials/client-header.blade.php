<header class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-200 transition-all duration-300" x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center gap-2 h-20">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-2 text-decoration-none group sm:gap-3">
            <div class="w-10 h-10 bg-[#1E3F20] rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm group-hover:bg-[#2A5A2E] transition-colors">
                <svg viewBox="0 0 24 24" class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M3 20h18M4 16l4-8 4 4 4-6 4 2v8H4z" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <span class="whitespace-nowrap font-serif text-xl font-bold text-[#1E3F20] sm:text-2xl">Rẻo Cao <span class="text-xs italic font-normal text-[#D4AF37] sm:text-sm">Journeys</span></span>
        </a>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex gap-8 font-medium">
            <a href="{{ route('magazine.index') }}" class="text-[#1E3F20] hover:text-[#D4AF37] transition-colors">Cẩm nang văn hóa</a>
            <a href="{{ route('home') }}#destinations" class="text-gray-700 hover:text-[#D4AF37] transition-colors">Điểm đến</a>
            <a href="{{ route('home') }}#tours" class="text-gray-700 hover:text-[#D4AF37] transition-colors">Tour trải nghiệm</a>
            @auth
                @if(auth()->user()->hasRole('host'))
                    <a href="{{ route('host.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1"><i class="fa-solid fa-house-user"></i> Kênh Chủ Nhà</a>
                @elseif(auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.dashboard') }}" class="text-red-600 hover:text-red-800 transition-colors flex items-center gap-1"><i class="fa-solid fa-gauge"></i> Hệ thống Admin</a>
                @endif
            @endauth
        </nav>

        <!-- User Actions -->
        <div class="flex shrink-0 items-center gap-2 sm:gap-4">
            <button type="button" @click="mobileOpen = !mobileOpen" class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 bg-gray-50 text-gray-700 shadow-sm hover:bg-gray-100 md:hidden" :aria-expanded="mobileOpen" aria-label="Mở menu">
                <i class="fa-solid" :class="mobileOpen ? 'fa-xmark' : 'fa-bars'"></i>
            </button>

            <!-- Search Icon Toggle -->
            <button x-data @click="$dispatch('open-search')" class="hidden w-10 h-10 rounded-full items-center justify-center text-gray-600 hover:text-[#00A8FF] hover:bg-blue-50 transition-colors sm:flex">
                <i class="fa-solid fa-magnifying-glass text-lg"></i>
            </button>

            @guest
                <a href="{{ route('login') }}" class="hidden px-6 py-2.5 bg-[#D4AF37] text-white font-semibold rounded-full hover:bg-[#c29f2f] transition-colors shadow-sm sm:inline-flex">Đăng nhập</a>
            @else
                <div class="relative" x-data="{ open: false }">
                    <!-- Dropdown Toggle Button -->
                    <button @click="open = !open" @click.away="open = false" class="flex items-center gap-3 focus:outline-none group">
                        <img src="{{ auth()->user()->avatarUrl(80) }}" 
                             alt="{{ auth()->user()->name }}"
                             class="w-10 h-10 rounded-full object-cover border-2 border-transparent group-hover:border-[#D4AF37] transition-all shadow-sm">
                        <span class="font-semibold text-gray-800 hidden sm:block group-hover:text-[#D4AF37] transition-colors">{{ auth()->user()->name }}</span>
                        <i class="fa-solid fa-chevron-down text-xs text-gray-400 group-hover:text-[#D4AF37] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-lg ring-1 ring-black ring-opacity-5 overflow-hidden z-50 divide-y divide-gray-100"
                         style="display: none;">
                        
                        <div class="px-4 py-3">
                            <p class="text-sm text-gray-500">Đăng nhập với tư cách</p>
                            <p class="text-sm font-bold text-gray-900 truncate">{{ auth()->user()->email }}</p>
                        </div>

                        <div class="py-1">
                            <a href="{{ route('profile.overview') }}" class="group flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-[#FAF9F6] hover:text-[#1E3F20] transition-colors">
                                <i class="fa-solid fa-house-chimney w-5 text-center mr-3 text-gray-400 group-hover:text-[#1E3F20]"></i> Tổng quan
                            </a>
                            <a href="{{ route('profile.overview') }}" class="group flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-[#FAF9F6] hover:text-[#1E3F20] transition-colors">
                                <i class="fa-solid fa-suitcase-rolling w-5 text-center mr-3 text-gray-400 group-hover:text-[#1E3F20]"></i> Lịch sử chuyến đi
                            </a>
                            <a href="{{ route('profile.favorites') }}" class="group flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-[#FAF9F6] hover:text-[#1E3F20] transition-colors">
                                <i class="fa-regular fa-heart w-5 text-center mr-3 text-gray-400 group-hover:text-[#1E3F20]"></i> Danh sách yêu thích
                            </a>
                            <a href="{{ route('profile.coupons') }}" class="group flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-[#FAF9F6] hover:text-[#1E3F20] transition-colors">
                                <i class="fa-solid fa-ticket w-5 text-center mr-3 text-gray-400 group-hover:text-[#1E3F20]"></i> Mã giảm giá
                            </a>
                        </div>

                        <div class="py-1">
                            <form action="{{ route('logout') }}" method="POST" class="m-0 block">
                                @csrf
                                <button type="submit" class="w-full text-left group flex items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center mr-3 text-red-400 group-hover:text-red-600"></i> Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endguest
        </div>
    </div>

    <div x-show="mobileOpen" x-transition x-cloak @click.outside="mobileOpen = false" class="border-t border-gray-200 bg-white px-4 py-4 shadow-lg md:hidden">
        <nav class="mx-auto grid max-w-7xl gap-1 text-sm font-semibold text-gray-700">
            <a href="{{ route('magazine.index') }}" class="rounded-lg px-3 py-3 hover:bg-gray-50">Cẩm nang văn hóa</a>
            <a href="{{ route('home') }}#destinations" @click="mobileOpen = false" class="rounded-lg px-3 py-3 hover:bg-gray-50">Điểm đến</a>
            <a href="{{ route('tours.index') }}" class="rounded-lg px-3 py-3 hover:bg-gray-50">Tour trải nghiệm</a>
            <button x-data @click="$dispatch('open-search'); mobileOpen = false" class="flex items-center gap-3 rounded-lg px-3 py-3 text-left hover:bg-gray-50">
                <i class="fa-solid fa-magnifying-glass w-5 text-center"></i> Tìm kiếm
            </button>

            @guest
                <a href="{{ route('login') }}" class="mt-2 rounded-lg bg-[#D4AF37] px-4 py-3 text-center text-white">Đăng nhập</a>
            @else
                <div class="my-2 border-t border-gray-200"></div>
                @if(auth()->user()->hasRole('host'))
                    <a href="{{ route('host.dashboard') }}" class="rounded-lg px-3 py-3 text-indigo-700 hover:bg-indigo-50"><i class="fa-solid fa-house-user mr-2"></i>Kênh Chủ Nhà</a>
                @elseif(auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.dashboard') }}" class="rounded-lg px-3 py-3 text-red-700 hover:bg-red-50"><i class="fa-solid fa-gauge mr-2"></i>Hệ thống Admin</a>
                @endif
                <a href="{{ route('profile.overview') }}" class="rounded-lg px-3 py-3 hover:bg-gray-50"><i class="fa-regular fa-user mr-2"></i>Tài khoản</a>
                <a href="{{ route('profile.favorites') }}" class="rounded-lg px-3 py-3 hover:bg-gray-50"><i class="fa-regular fa-heart mr-2"></i>Yêu thích</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full rounded-lg px-3 py-3 text-left text-red-600 hover:bg-red-50"><i class="fa-solid fa-arrow-right-from-bracket mr-2"></i>Đăng xuất</button>
                </form>
            @endguest
        </nav>
    </div>
</header>
