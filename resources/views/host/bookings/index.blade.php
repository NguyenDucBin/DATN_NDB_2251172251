<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Quản lý Booking</h2>
    </x-slot>

    @if (session('success'))
        <div class="p-4 mb-6 text-sm text-emerald-700 bg-emerald-100 border border-emerald-200 rounded-lg shadow-sm" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 mb-6 text-sm text-red-700 bg-red-100 border border-red-200 rounded-lg shadow-sm" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white border border-gray-100 shadow-sm rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                        <th class="px-6 py-4">Mã Booking</th>
                        <th class="px-6 py-4">Khách hàng</th>
                        <th class="px-6 py-4">Tour</th>
                        <th class="px-6 py-4">Ngày tham gia</th>
                        <th class="px-6 py-4">Số khách</th>
                        <th class="px-6 py-4">Tổng tiền</th>
                        <th class="px-6 py-4">Trạng thái</th>
                        <th class="px-6 py-4">Thanh toán</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($bookings as $booking)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center text-sm">
                                    <div class="relative w-8 h-8 mr-3 rounded-full">
                                        <img class="object-cover w-full h-full rounded-full" src="{{ $booking->user->avatarUrl(64) }}" alt="{{ $booking->user->name }}" loading="lazy" />
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $booking->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $booking->user->email }}</p>
                                        <p class="text-xs text-gray-500">{{ $booking->user->phone ?? 'Không có SĐT' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ Str::limit($booking->tour->name, 30) }}</div>
                                <a href="{{ route('tours.show', $booking->tour->slug) }}" target="_blank" class="text-xs text-emerald-600 hover:underline">Xem tour</a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($booking->start_date)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $booking->number_of_people }} người
                            </td>
                            <td class="px-6 py-4 font-semibold text-emerald-600">
                                {{ number_format($booking->total_price, 0, ',', '.') }} ₫
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full border {{ $booking->statusBadgeClasses() }}">
                                    {{ $booking->statusLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full border {{ $booking->paymentStatusBadgeClasses() }}">
                                    {{ $booking->paymentStatusLabel() }}
                                </span>
                            </td>
                            @php
                                $canConfirm = $booking->canBeConfirmedByHost();
                                $canComplete = $booking->canBeCompletedByHost();
                                $canCancel = $booking->canBeCancelledByHost();
                            @endphp
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($canConfirm)
                                        <form action="{{ route('host.bookings.confirm', $booking->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 rounded-md hover:bg-emerald-700 transition-colors">
                                                Xác nhận
                                            </button>
                                        </form>
                                    @endif

                                    @if($canComplete)
                                        <form action="{{ route('host.bookings.complete', $booking->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors">
                                                Hoàn tất
                                            </button>
                                        </form>
                                    @endif

                                    @if($canCancel)
                                        <form action="{{ route('host.bookings.cancel', $booking->id) }}" method="POST" class="delete-form" data-confirm-message="Bạn có chắc muốn hủy booking này?">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-red-700 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 transition-colors">
                                                Hủy
                                            </button>
                                        </form>
                                    @endif

                                    @if(! $canConfirm && ! $canComplete && ! $canCancel)
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                Chưa có khách hàng nào đặt tour của bạn.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($bookings->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</x-dashboard-layout>
