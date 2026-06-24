<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Tin nhắn</h2>
    </x-slot>

    <div class="grid min-h-[620px] overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm md:grid-cols-[300px_1fr]">
        <aside class="border-b border-gray-200 bg-gray-50 md:border-b-0 md:border-r">
            <div class="border-b border-gray-200 bg-white px-4 py-4">
                <h3 class="font-bold text-gray-900">Cuộc hội thoại</h3>
            </div>
            <div class="max-h-[240px] overflow-y-auto p-2 md:max-h-[560px]">
                @forelse($contacts as $contact)
                    <a href="{{ route('host.inbox', ['contact' => $contact->id]) }}"
                       class="mb-1 flex items-center gap-3 rounded-lg px-3 py-3 transition-colors {{ $activeContact?->is($contact) ? 'bg-emerald-100 text-emerald-900' : 'hover:bg-gray-100' }}">
                        <img src="{{ $contact->avatarUrl(64) }}" alt="{{ $contact->name }}" class="h-10 w-10 rounded-full object-cover">
                        <span class="truncate text-sm font-semibold">{{ $contact->name }}</span>
                    </a>
                @empty
                    <p class="px-3 py-8 text-center text-sm text-gray-500">Chưa có tin nhắn nào.</p>
                @endforelse
            </div>
        </aside>

        <section class="flex min-w-0 flex-col">
            @if($activeContact)
                <header class="flex items-center gap-3 border-b border-gray-200 px-5 py-4">
                    <img src="{{ $activeContact->avatarUrl(64) }}" alt="{{ $activeContact->name }}" class="h-10 w-10 rounded-full object-cover">
                    <div>
                        <p class="font-bold text-gray-900">{{ $activeContact->name }}</p>
                        <p class="text-xs text-gray-500">Hội thoại trực tiếp</p>
                    </div>
                </header>

                <div class="flex-1 space-y-3 overflow-y-auto bg-gray-50/60 p-4 md:p-6">
                    @foreach($conversation as $message)
                        <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[82%] rounded-2xl px-4 py-2.5 shadow-sm {{ $message->sender_id === auth()->id() ? 'bg-emerald-700 text-white' : 'border border-gray-200 bg-white text-gray-800' }}">
                                <p class="whitespace-pre-wrap break-words text-sm">{{ $message->content }}</p>
                                <p class="mt-1 text-right text-[10px] {{ $message->sender_id === auth()->id() ? 'text-emerald-200' : 'text-gray-400' }}">{{ $message->created_at?->format('H:i d/m') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <form action="{{ route('messages.store', $activeContact) }}" method="POST" class="border-t border-gray-200 bg-white p-4">
                    @csrf
                    <div class="flex items-end gap-3">
                        <textarea name="content" rows="2" maxlength="2000" required class="min-h-[48px] flex-1 resize-none rounded-lg border-gray-300 text-sm focus:border-emerald-600 focus:ring-emerald-600" placeholder="Nhập tin nhắn..."></textarea>
                        <button type="submit" class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-emerald-700 text-white hover:bg-emerald-800" title="Gửi tin nhắn">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                    @error('content')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </form>
            @else
                <div class="flex flex-1 flex-col items-center justify-center p-8 text-center text-gray-500">
                    <i class="fa-regular fa-comments mb-4 text-5xl text-gray-300"></i>
                    <p>Hộp thư sẽ hiển thị khi du khách nhắn tin cho bạn.</p>
                </div>
            @endif
        </section>
    </div>
</x-dashboard-layout>
