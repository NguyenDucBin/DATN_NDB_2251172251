@props([
    'messages' => [],
    'title' => 'Thông tin chưa hợp lệ',
    'theme' => 'light',
])

@php
    $items = collect($messages)->flatten()->filter()->unique()->values();
    $isDark = $theme === 'dark';
@endphp

@if($items->isNotEmpty())
    <div x-data="{ visible: true }"
         x-show="visible"
         x-transition.opacity.duration.200ms
         role="alert"
         aria-live="assertive"
         @class([
             'mb-6 flex items-start gap-3 rounded-lg border p-4 shadow-sm',
             'border-red-200 bg-red-50 text-red-950' => ! $isDark,
             'border-red-400/30 bg-red-500/15 text-red-100' => $isDark,
         ])>
        <span @class([
            'inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full',
            'bg-red-100 text-red-600' => ! $isDark,
            'bg-red-400/20 text-red-300' => $isDark,
        ])>
            <i class="fa-solid fa-triangle-exclamation"></i>
        </span>

        <div class="min-w-0 flex-1">
            <p class="text-sm font-bold">{{ $title }}</p>
            @if($items->count() === 1)
                <p @class(['mt-1 text-sm leading-5', 'text-red-700' => ! $isDark, 'text-red-200' => $isDark])>
                    {{ $items->first() }}
                </p>
            @else
                <ul @class(['mt-2 space-y-1 text-sm leading-5', 'text-red-700' => ! $isDark, 'text-red-200' => $isDark])>
                    @foreach($items as $message)
                        <li class="flex items-start gap-2">
                            <span class="mt-2 h-1 w-1 shrink-0 rounded-full bg-current"></span>
                            <span>{{ $message }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <button type="button"
                @click="visible = false"
                @class([
                    'inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-md transition-colors',
                    'text-red-500 hover:bg-red-100 hover:text-red-700' => ! $isDark,
                    'text-red-300 hover:bg-white/10 hover:text-white' => $isDark,
                ])
                aria-label="Đóng thông báo">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
@endif
