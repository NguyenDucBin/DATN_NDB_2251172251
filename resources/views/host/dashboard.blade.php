<x-dashboard-layout>
    <x-slot name="header">
        Tổng quan Chủ nhà
    </x-slot>

    <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-3">
        <!-- Card 1 -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center">
            <div class="p-3 mr-4 text-emerald-500 bg-emerald-100 rounded-full">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600">Tổng số Tour</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalTours }}</p>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center">
            <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600">Tổng Bookings</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalBookings }}</p>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center">
            <div class="p-3 mr-4 text-orange-500 bg-orange-100 rounded-full">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600">Doanh thu đã xác nhận</p>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($totalRevenue, 0, ',', '.') }} ₫</p>
            </div>
        </div>
    </div>

    <!-- Recent Bookings Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">Booking Mới Nhất</h3>
            <a href="{{ route('host.bookings') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">Xem tất cả &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead class="bg-white border-b border-gray-100">
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                        <th class="px-6 py-4">Khách hàng</th>
                        <th class="px-6 py-4">Tour</th>
                        <th class="px-6 py-4">Ngày bắt đầu</th>
                        <th class="px-6 py-4">Số lượng</th>
                        <th class="px-6 py-4">Tổng tiền</th>
                        <th class="px-6 py-4">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($recentBookings as $booking)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center text-sm">
                                <div class="relative w-8 h-8 mr-3 rounded-full">
                                    <img class="object-cover w-full h-full rounded-full" src="{{ $booking->user->avatarUrl(64) }}" alt="{{ $booking->user->name }}" loading="lazy" />
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $booking->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $booking->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ Str::limit($booking->tour->name, 30) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ \Carbon\Carbon::parse($booking->start_date)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $booking->number_of_people }} khách
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">
                            {{ number_format($booking->total_price, 0, ',', '.') }} ₫
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full border {{ $booking->statusBadgeClasses() }}">
                                {{ $booking->statusLabel() }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 text-sm">
                            Chưa có booking nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-dashboard-layout>
