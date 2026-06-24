@props(['messages', 'theme' => 'light'])

@if($messages)
    <p {{ $attributes->class([
        'mt-2 flex items-start gap-1.5 text-xs font-medium leading-5',
        'text-red-600' => $theme === 'light',
        'text-red-300' => $theme === 'dark',
    ]) }} role="alert">
        <i class="fa-solid fa-circle-exclamation mt-1 shrink-0 text-[10px]"></i>
        <span>{{ collect($messages)->first() }}</span>
    </p>
@endif
