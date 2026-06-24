<x-dashboard-layout>
    <x-slot name="header">
        Tổng quan Quản trị viên
    </x-slot>

    <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
        <!-- Card 1 -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center">
            <div class="p-3 mr-4 text-emerald-500 bg-emerald-100 rounded-full">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600">Tổng doanh thu</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($totalRevenue, 0, ',', '.') }} ₫</p>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center">
            <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600">Tổng Bookings</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalBookings }}</p>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center">
            <div class="p-3 mr-4 text-purple-500 bg-purple-100 rounded-full">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600">Tổng User</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalUsers }}</p>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center">
            <div class="p-3 mr-4 text-orange-500 bg-orange-100 rounded-full">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600">Tour chờ duyệt</p>
                <p class="text-2xl font-bold text-gray-800">{{ $pendingToursCount }}</p>
            </div>
        </div>
    </div>

    <!-- Charts and Tables -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Revenue Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Biểu đồ doanh thu {{ date('Y') }}</h3>
            <div class="relative h-72 w-full">
                <canvas id="revenueChart" data-revenue-chart data-values='@json($chartData)'></canvas>
            </div>
        </div>

        <!-- Quick Actions or Pending Tours -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-gray-800">Kiểm duyệt nhanh</h3>
                <a href="{{ route('admin.approvals') }}" class="text-sm text-emerald-600 hover:text-emerald-700">Xem tất cả</a>
            </div>
            <p class="text-sm text-gray-600 mb-4">Hệ thống đang có <span class="font-bold text-orange-600">{{ $pendingToursCount }}</span> tour chờ phê duyệt từ các Chủ nhà.</p>
            @if($pendingToursCount > 0)
                <a href="{{ route('admin.approvals') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Đi tới trang Kiểm duyệt
                </a>
            @else
                <div class="p-4 bg-gray-50 rounded-lg text-center text-gray-500 text-sm">
                    Tất cả các tour đã được kiểm duyệt.
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/admin-dashboard.js')
    @endpush
</x-dashboard-layout>
