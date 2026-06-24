<x-dashboard-layout>
    <x-slot name="header">
        Quản lý Người dùng
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

    @if (session('warning'))
        <div class="flex items-center gap-3 p-4 mb-6 text-sm text-amber-800 bg-amber-100 border border-amber-200 rounded-lg shadow-sm" role="alert">
            <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
            <span>{{ session('warning') }}</span>
        </div>
    @endif

    @php
        $filters = [
            'all' => ['label' => 'Tất cả', 'count' => $stats['all'], 'icon' => 'fa-users'],
            'pending_hosts' => ['label' => 'Host chờ duyệt', 'count' => $stats['pending_hosts'], 'icon' => 'fa-user-clock'],
            'approved_hosts' => ['label' => 'Host đã duyệt', 'count' => $stats['approved_hosts'], 'icon' => 'fa-house-user'],
            'locked' => ['label' => 'Đã khóa', 'count' => $stats['locked'], 'icon' => 'fa-user-lock'],
        ];

        $hostStatusLabels = [
            'pending' => 'Chờ duyệt',
            'approved' => 'Đã duyệt',
            'rejected' => 'Đã từ chối',
            'none' => 'Không yêu cầu',
        ];

        $hostStatusClasses = [
            'pending' => 'text-amber-700 bg-amber-100',
            'approved' => 'text-emerald-700 bg-emerald-100',
            'rejected' => 'text-red-700 bg-red-100',
            'none' => 'text-gray-600 bg-gray-100',
        ];
    @endphp

    <div class="grid grid-cols-1 gap-3 mb-6 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($filters as $key => $item)
            <a href="{{ $key === 'all' ? route('admin.users.index') : route('admin.users.index', ['filter' => $key]) }}"
               class="flex items-center justify-between rounded-lg border px-4 py-3 transition-colors {{ $filter === $key ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-gray-100 bg-white text-gray-700 hover:border-emerald-200 hover:bg-emerald-50/60' }}">
                <span class="flex items-center gap-3">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-white shadow-sm">
                        <i class="fa-solid {{ $item['icon'] }}"></i>
                    </span>
                    <span class="text-sm font-semibold">{{ $item['label'] }}</span>
                </span>
                <span class="text-lg font-bold">{{ $item['count'] }}</span>
            </a>
        @endforeach
    </div>

    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-emerald-600 border border-transparent rounded-md hover:bg-emerald-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
            <i class="fa-solid fa-plus mr-2"></i>
            Thêm người dùng mới
        </a>
    </div>

    <div class="bg-white border border-gray-100 shadow-sm rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                        <th class="px-6 py-4">Người dùng</th>
                        <th class="px-6 py-4">Vai trò</th>
                        <th class="px-6 py-4">Yêu cầu Host</th>
                        <th class="px-6 py-4">Tài khoản</th>
                        <th class="px-6 py-4">Ngày tham gia</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($users as $user)
                        @php
                            $hostStatus = $user->hostApprovalStatus();
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center text-sm">
                                    <div class="relative w-10 h-10 mr-3 rounded-full">
                                        <img class="object-cover w-full h-full rounded-full" src="{{ $user->avatarUrl(64) }}" alt="{{ $user->name }}" loading="lazy" />
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->hasRole('admin'))
                                    <span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">Admin</span>
                                @elseif($user->hasRole('host'))
                                    <span class="px-2 py-1 text-xs font-semibold text-emerald-700 bg-emerald-100 rounded-full">Host</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">Tourist</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                     <span class="w-fit px-2 py-1 text-xs font-semibold rounded-full {{ $hostStatusClasses[$hostStatus] ?? $hostStatusClasses['none'] }}">
                                         {{ $hostStatusLabels[$hostStatus] ?? $hostStatusLabels['none'] }}
                                     </span>
                                 </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->isLocked())
                                    <span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">Đã khóa</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold text-emerald-700 bg-emerald-100 rounded-full">Đang hoạt động</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3">
                                    @if($hostStatus === 'pending')
                                        <form action="{{ route('admin.users.approve-host', $user->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="text-emerald-600 hover:text-emerald-900 transition-colors" title="Duyệt Host">
                                                <i class="fa-solid fa-check text-lg"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.users.reject-host', $user->id) }}" method="POST" class="inline-block delete-form" data-confirm-message="Bạn có chắc muốn từ chối yêu cầu Host của người dùng này?">
                                            @csrf
                                            <button type="submit" class="text-amber-600 hover:text-amber-900 transition-colors" title="Từ chối Host">
                                                <i class="fa-solid fa-xmark text-lg"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($user->id !== auth()->id() && !$user->hasRole('admin'))
                                        @if($user->isLocked())
                                            <form action="{{ route('admin.users.unlock', $user->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="text-blue-600 hover:text-blue-900 transition-colors" title="Mở khóa tài khoản">
                                                    <i class="fa-solid fa-unlock text-lg"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.users.lock', $user->id) }}" method="POST" class="inline-block delete-form" data-confirm-message="Bạn có chắc muốn khóa tài khoản này? Người dùng sẽ không thể đăng nhập cho tới khi được mở khóa.">
                                                @csrf
                                                <button type="submit" class="text-slate-600 hover:text-slate-900 transition-colors" title="Khóa tài khoản">
                                                    <i class="fa-solid fa-lock text-lg"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif

                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="text-emerald-600 hover:text-emerald-900 transition-colors" title="Sửa">
                                        <i class="fa-solid fa-pen-to-square text-lg"></i>
                                    </a>

                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block delete-form" data-confirm-message="Bạn có chắc chắn muốn xóa người dùng này? Thao tác này sẽ xóa mọi dữ liệu liên quan.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="Xóa">
                                                <i class="fa-solid fa-trash-can text-lg"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                Chưa có người dùng nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-dashboard-layout>
