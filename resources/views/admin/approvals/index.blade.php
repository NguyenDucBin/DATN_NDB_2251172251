<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Kiểm duyệt Tour</h2>
    </x-slot>

    @if (session('success'))
        <div class="p-4 mb-6 text-sm text-emerald-700 bg-emerald-100 rounded-lg shadow-sm" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div x-data="{ tab: 'pending' }" class="mb-8">
        <!-- Tabs -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button @click="tab = 'pending'" :class="tab === 'pending' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Chờ duyệt ({{ $pendingTours->total() }})
                </button>
                <button @click="tab = 'approved'" :class="tab === 'approved' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Đã xử lý ({{ $approvedTours->total() }})
                </button>
            </nav>
        </div>

        <!-- Pending Tours -->
        <div x-show="tab === 'pending'">
            <div class="bg-white border border-gray-100 shadow-sm rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full whitespace-nowrap">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                                <th class="px-6 py-4">Tour</th>
                                <th class="px-6 py-4">Chủ nhà (Host)</th>
                                <th class="px-6 py-4">Thông tin</th>
                                <th class="px-6 py-4 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($pendingTours as $tour)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ Str::limit($tour->name, 40) }}</div>
                                        <div class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($tour->created_at)->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center text-sm">
                                            <div class="relative w-8 h-8 mr-3 rounded-full">
                                                <img class="object-cover w-full h-full rounded-full" src="{{ $tour->host->avatarUrl(64) }}" alt="{{ $tour->host->name }}" loading="lazy" />
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800">{{ $tour->host->name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-semibold text-emerald-600">{{ number_format($tour->price, 0, ',', '.') }} ₫</p>
                                        <p class="text-xs text-gray-600">Sức chứa: {{ $tour->capacity }} khách</p>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <form action="{{ route('admin.approvals.approve', $tour->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                                    Duyệt
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.approvals.reject', $tour->id) }}" method="POST" class="delete-form" data-confirm-message="Bạn có chắc muốn từ chối tour này?">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                    Từ chối
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                        Không có tour nào đang chờ duyệt.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($pendingTours->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                        {{ $pendingTours->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Approved/Rejected Tours -->
        <div x-show="tab === 'approved'" style="display: none;">
            <div class="bg-white border border-gray-100 shadow-sm rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full whitespace-nowrap">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                                <th class="px-6 py-4">Tour</th>
                                <th class="px-6 py-4">Chủ nhà (Host)</th>
                                <th class="px-6 py-4">Trạng thái</th>
                                <th class="px-6 py-4 text-right">Cập nhật</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($approvedTours as $tour)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ Str::limit($tour->name, 40) }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-semibold text-gray-800">{{ $tour->host->name }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($tour->status == 'approved')
                                            <span class="px-2 py-1 text-xs font-semibold text-emerald-700 bg-emerald-100 rounded-full">Đã duyệt</span>
                                        @elseif($tour->status == 'rejected')
                                            <span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">Từ chối</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($tour->updated_at)->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                        Chưa có lịch sử xử lý.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($approvedTours->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                        {{ $approvedTours->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-dashboard-layout>
