@extends('layouts.client')

@section('title', 'Tin nhắn với ' . $contact->name)

@section('content')
<div class="min-h-screen bg-[#FAF9F6] px-4 pb-20 pt-28 sm:px-6">
    <div class="mx-auto flex min-h-[620px] max-w-3xl flex-col overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <header class="flex items-center gap-3 border-b border-gray-200 px-5 py-4">
            <a href="{{ url()->previous() }}" class="flex h-9 w-9 items-center justify-center rounded-full text-gray-500 hover:bg-gray-100" title="Quay lại">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <img src="{{ $contact->avatarUrl(64) }}" alt="{{ $contact->name }}" class="h-11 w-11 rounded-full object-cover">
            <div>
                <p class="font-bold text-gray-900">{{ $contact->name }}</p>
                <p class="text-xs text-emerald-700">Host đã được phê duyệt</p>
            </div>
        </header>

        <div class="flex-1 space-y-3 overflow-y-auto bg-gray-50/60 p-4 sm:p-6">
            @forelse($conversation as $message)
                <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[85%] rounded-2xl px-4 py-2.5 shadow-sm {{ $message->sender_id === auth()->id() ? 'bg-[#1E3F20] text-white' : 'border border-gray-200 bg-white text-gray-800' }}">
                        <p class="whitespace-pre-wrap break-words text-sm">{{ $message->content }}</p>
                        <p class="mt-1 text-right text-[10px] {{ $message->sender_id === auth()->id() ? 'text-emerald-200' : 'text-gray-400' }}">{{ $message->created_at?->format('H:i d/m') }}</p>
                    </div>
                </div>
            @empty
                <div class="flex h-full items-center justify-center text-center text-sm text-gray-500">Hãy gửi lời chào để bắt đầu cuộc trò chuyện.</div>
            @endforelse
        </div>

        <form action="{{ route('messages.store', $contact) }}" method="POST" class="border-t border-gray-200 bg-white p-4">
            @csrf
            <div class="flex items-end gap-3">
                <textarea name="content" rows="2" maxlength="2000" required class="min-h-[48px] flex-1 resize-none rounded-lg border-gray-300 text-sm focus:border-[#1E3F20] focus:ring-[#1E3F20]" placeholder="Nhập tin nhắn..."></textarea>
                <button type="submit" class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-[#1E3F20] text-white hover:bg-[#2A5A2E]" title="Gửi tin nhắn">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
            @error('content')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </form>
    </div>
</div>
@endsection
