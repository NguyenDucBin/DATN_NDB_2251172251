<x-dashboard-layout>
    <x-slot name="header">
        Quản lý Tạp chí / Bài viết
    </x-slot>

    @if (session('success'))
        <div class="p-4 mb-6 text-sm text-emerald-700 bg-emerald-100 rounded-lg shadow-sm" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.posts.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-emerald-600 border border-transparent rounded-md hover:bg-emerald-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
            <i class="fa-solid fa-plus mr-2"></i>
            Viết bài mới
        </a>
    </div>

    <div class="bg-white border border-gray-100 shadow-sm rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                        <th class="px-6 py-4">Tiêu đề bài viết</th>
                        <th class="px-6 py-4">Tác giả</th>
                        <th class="px-6 py-4">Ngày đăng</th>
                        <th class="px-6 py-4">Trạng thái</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($posts as $post)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ Str::limit($post->title, 50) }}</div>
                                <div class="text-xs text-gray-500">{{ $post->slug }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $post->user->name ?? 'Admin' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($post->created_at)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($post->status == 'published')
                                    <span class="px-2 py-1 text-xs font-semibold text-emerald-700 bg-emerald-100 rounded-full">Đã xuất bản</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded-full">Bản nháp</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('magazine.show', $post->slug) }}" target="_blank" class="text-blue-600 hover:text-blue-900 transition-colors" title="Xem trên web">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <a href="{{ route('admin.posts.edit', $post->id) }}" class="text-emerald-600 hover:text-emerald-900 transition-colors" title="Sửa">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="inline-block delete-form" data-confirm-message="Bạn có chắc chắn muốn xóa bài viết này?">
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
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                Chưa có bài viết nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($posts->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</x-dashboard-layout>
