<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Xử lý Yêu cầu Hoàn tiền</h2>
    </x-slot>

    @if (session('success'))
        <div class="p-4 mb-6 text-sm text-emerald-700 bg-emerald-100 rounded-lg shadow-sm" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 mb-6 text-sm text-red-700 bg-red-100 rounded-lg shadow-sm" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white border border-gray-100 shadow-sm rounded-xl overflow-hidden" x-data="{ openModal: false, selectedRefund: null }">
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                        <th class="px-6 py-4">Mã Booking</th>
                        <th class="px-6 py-4">Khách hàng yêu cầu</th>
                        <th class="px-6 py-4">Số tiền hoàn</th>
                        <th class="px-6 py-4">Lý do</th>
                        <th class="px-6 py-4">Trạng thái</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($refunds as $refund)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                #{{ str_pad($refund->booking_id, 6, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-800">{{ $refund->booking->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($refund->created_at)->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-orange-600">
                                {{ number_format($refund->amount, 0, ',', '.') }} ₫
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-700 max-w-xs truncate" title="{{ $refund->reason }}">{{ $refund->reason }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($refund->status == 'pending')
                                    <span class="px-2 py-1 text-xs font-semibold text-orange-700 bg-orange-100 rounded-full">Chờ xử lý</span>
                                @elseif($refund->status === 'processed')
                                    <span class="px-2 py-1 text-xs font-semibold text-emerald-700 bg-emerald-100 rounded-full">Đã hoàn tiền</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">Từ chối</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                @if($refund->status == 'pending')
                                    <button @click="openModal = true; selectedRefund = {{ $refund->id }};" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-emerald-700 bg-emerald-100 hover:bg-emerald-200 focus:outline-none">
                                        Xử lý
                                    </button>
                                @else
                                    <span class="text-gray-400 text-xs">
                                        Đã xử lý ({{ $refund->updated_at->format('d/m/Y') }})
                                    </span>
                                @endif
                            </td>
                        </tr>
                        
                        <!-- Modal for processing refund (alpinejs bound to this row if selectedRefund == id) -->
                        <div x-show="openModal && selectedRefund === {{ $refund->id }}" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div x-show="openModal" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                <div x-show="openModal" x-transition class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                    <form action="{{ route('admin.refunds.process', $refund->id) }}" method="POST">
                                        @csrf
                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <div class="sm:flex sm:items-start">
                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                        Xử lý yêu cầu hoàn tiền #{{ str_pad($refund->id, 5, '0', STR_PAD_LEFT) }}
                                                    </h3>
                                                    <div class="mt-4 text-sm text-gray-600">
                                                        <p><strong>Khách hàng:</strong> {{ $refund->booking->user->name }}</p>
                                                        <p><strong>Số tiền:</strong> <span class="text-orange-600 font-semibold">{{ number_format($refund->amount, 0, ',', '.') }} ₫</span></p>
                                                        <p class="mt-2"><strong>Lý do của khách:</strong></p>
                                                        <div class="p-3 bg-gray-50 rounded mt-1 border text-gray-700">
                                                            {{ $refund->reason }}
                                                        </div>
                                                    </div>

                                                    <div class="mt-4">
                                                        <label for="status" class="block text-sm font-medium text-gray-700">Quyết định xử lý</label>
                                                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required>
                                                            <option value="processed">Chấp nhận hoàn tiền</option>
                                                            <option value="rejected">Từ chối hoàn tiền</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                                Lưu quyết định
                                            </button>
                                            <button type="button" @click="openModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                Hủy
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                Không có yêu cầu hoàn tiền nào đang chờ xử lý.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($refunds->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $refunds->links() }}
            </div>
        @endif
    </div>
</x-dashboard-layout>
