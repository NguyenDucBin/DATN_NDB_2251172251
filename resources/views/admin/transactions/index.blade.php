<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Quản lý Giao dịch</h2>
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

    <!-- Stats -->
    <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-3">
        <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Tổng doanh thu hệ thống</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalRevenue, 0, ',', '.') }} ₫</p>
                </div>
                <div class="p-3 bg-emerald-100 rounded-full">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Giao dịch đang xử lý</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalPending, 0, ',', '.') }} ₫</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-full">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
        
        <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Tổng số lượng giao dịch</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $transactions->total() }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-100 shadow-sm rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                        <th class="px-6 py-4">Mã GD</th>
                        <th class="px-6 py-4">Khách hàng</th>
                        <th class="px-6 py-4">Tour</th>
                        <th class="px-6 py-4">Số tiền</th>
                        <th class="px-6 py-4">Thời gian</th>
                        <th class="px-6 py-4">Booking</th>
                        <th class="px-6 py-4">Thanh toán</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($transactions as $transaction)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                #{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-800">{{ $transaction->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $transaction->user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ Str::limit($transaction->tour->name, 30) }}</div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-emerald-600">
                                {{ number_format($transaction->total_price, 0, ',', '.') }} ₫
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full border {{ $transaction->statusBadgeClasses() }}">
                                    {{ $transaction->statusLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1.5">
                                    <span class="inline-flex w-fit items-center px-2.5 py-1 text-xs font-semibold rounded-full border {{ $transaction->paymentStatusBadgeClasses() }}">
                                        {{ $transaction->paymentStatusLabel() }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ match ($transaction->payment_method) {
                                            'bank_transfer' => 'Chuyển khoản ngân hàng',
                                            'vnpay' => 'VNPay',
                                            default => 'Chưa chọn phương thức',
                                        } }}
                                    </span>
                                </div>
                            </td>
                            @php
                                $canMarkPaid = $transaction->payment_status !== 'paid' && !in_array($transaction->status, ['cancelled', 'refunded'], true);
                                $canCancel = !in_array($transaction->status, ['completed', 'cancelled', 'refunded'], true)
                                    && ! $transaction->hasPendingRefundRequest();
                            @endphp
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($canMarkPaid)
                                        <form action="{{ route('admin.transactions.mark-paid', $transaction->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-md hover:bg-emerald-100 transition-colors">
                                                Đã nhận tiền
                                            </button>
                                        </form>
                                    @endif

                                    @if($canCancel)
                                        <form action="{{ route('admin.transactions.cancel', $transaction->id) }}" method="POST" class="delete-form" data-confirm-message="Bạn có chắc muốn hủy booking này?">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-red-700 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 transition-colors">
                                                Hủy
                                            </button>
                                        </form>
                                    @endif

                                    @if(! $canMarkPaid && ! $canCancel)
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                Chưa có giao dịch nào trên hệ thống.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($transactions->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</x-dashboard-layout>
