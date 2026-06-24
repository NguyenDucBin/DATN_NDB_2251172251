<x-dashboard-layout>
    <x-slot name="header">
        Quản lý Mã Giảm Giá
    </x-slot>

    @if (session('success'))
        <div class="p-4 mb-6 text-sm text-emerald-700 bg-emerald-100 rounded-lg shadow-sm" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.coupons.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-emerald-600 border border-transparent rounded-md hover:bg-emerald-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
            <i class="fa-solid fa-plus mr-2"></i>
            Tạo mã mới
        </a>
    </div>

    <div class="bg-white border border-gray-100 shadow-sm rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                        <th class="px-6 py-4">Mã Code</th>
                        <th class="px-6 py-4">Giảm giá</th>
                        <th class="px-6 py-4">Giới hạn sử dụng</th>
                        <th class="px-6 py-4">Hiệu lực</th>
                        <th class="px-6 py-4">Trạng thái</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($coupons as $coupon)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 tracking-wider">{{ $coupon->code }}</div>
                            </td>
                            <td class="px-6 py-4 text-emerald-600 font-semibold">
                                @if($coupon->discount_type == 'percent')
                                    {{ $coupon->discount_amount }}%
                                @else
                                    {{ number_format($coupon->discount_amount, 0, ',', '.') }} ₫
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                Đã dùng: {{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if($coupon->valid_from || $coupon->valid_until)
                                    <div>Từ: {{ $coupon->valid_from ? \Carbon\Carbon::parse($coupon->valid_from)->format('d/m/Y H:i') : 'Bất kỳ' }}</div>
                                    <div>Đến: {{ $coupon->valid_until ? \Carbon\Carbon::parse($coupon->valid_until)->format('d/m/Y H:i') : 'Bất kỳ' }}</div>
                                @else
                                    Vô thời hạn
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($coupon->is_active)
                                    <span class="px-2 py-1 text-xs font-semibold text-emerald-700 bg-emerald-100 rounded-full">Kích hoạt</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded-full">Vô hiệu hóa</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="text-emerald-600 hover:text-emerald-900 transition-colors" title="Sửa">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="inline-block delete-form" data-confirm-message="Bạn có chắc chắn muốn xóa mã giảm giá này?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="Xóa">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                Chưa có mã giảm giá nào được tạo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($coupons->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>
</x-dashboard-layout>
