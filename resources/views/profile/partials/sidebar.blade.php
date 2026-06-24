{{-- Profile Sidebar Navigation --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sticky top-24">
    <nav class="space-y-1">
        <a href="{{ route('profile.overview') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors font-medium {{ request()->routeIs('profile.overview') ? 'bg-[#1E3F20]/5 text-[#1E3F20] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <i class="fa-solid fa-house-chimney w-5 text-center"></i>
            Tổng quan
        </a>
        <a href="{{ route('profile.edit') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors font-medium {{ request()->routeIs('profile.edit') ? 'bg-[#1E3F20]/5 text-[#1E3F20] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <i class="fa-regular fa-id-badge w-5 text-center"></i>
            Thông tin cá nhân
        </a>
        <a href="{{ route('profile.favorites') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors font-medium {{ request()->routeIs('profile.favorites') ? 'bg-[#1E3F20]/5 text-[#1E3F20] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <i class="fa-regular fa-heart w-5 text-center"></i>
            Danh sách yêu thích
        </a>
        <a href="{{ route('profile.coupons') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors font-medium {{ request()->routeIs('profile.coupons') ? 'bg-[#1E3F20]/5 text-[#1E3F20] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <i class="fa-solid fa-ticket w-5 text-center"></i>
            Mã giảm giá
        </a>
        <a href="{{ route('profile.security') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors font-medium {{ request()->routeIs('profile.security') ? 'bg-[#1E3F20]/5 text-[#1E3F20] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <i class="fa-solid fa-shield-halved w-5 text-center"></i>
            Bảo mật tài khoản
        </a>
    </nav>
</div>
