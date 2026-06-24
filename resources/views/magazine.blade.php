@extends('layouts.client')

@section('title', 'Cẩm nang văn hóa - Rẻo Cao Journeys')

@section('content')
<div class="h-[75px] w-full bg-[#FAF9F6]"></div>

<div class="bg-[#FAF9F6] min-h-screen pb-24">
    <!-- Header -->
    <div class="relative py-20 bg-[#1E3F20] overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: url('{{ asset('images/static/pattern-arabesque.png') }}')"></div>
        <div class="container relative z-10 mx-auto px-4 text-center">
            <span class="inline-block py-1 px-3 rounded-full bg-white/20 backdrop-blur-sm text-[#D4AF37] text-sm font-bold tracking-widest uppercase mb-6 border border-[#D4AF37]/30">
                Tạp chí văn hóa
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 font-serif text-white">
                Cẩm Nang Rẻo Cao
            </h1>
            <p class="text-gray-200 text-lg max-w-2xl mx-auto font-light leading-relaxed">
                Những câu chuyện bản địa, kiến trúc truyền thống và vẻ đẹp nguyên bản của vùng cao Tây Bắc được Ban Biên Tập ghi chép lại.
            </p>
        </div>
    </div>

    <!-- Magazine Grid -->
    <div class="container mx-auto px-4 py-16 max-w-7xl">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            @forelse($posts as $index => $post)
                <article class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full border border-gray-100">
                    <div class="relative overflow-hidden aspect-[16/10]">
                        <img src="{{ $post->imageUrl() }}"
                             alt="{{ $post->title }}" 
                             loading="lazy" decoding="async" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    
                    <div class="p-8 flex flex-col flex-1">
                        <span class="text-xs font-bold uppercase tracking-widest text-[#D4AF37] mb-3 block">
                            Đời sống & Văn hóa
                        </span>
                        
                        <h2 class="text-2xl font-bold mb-4 font-serif leading-tight text-gray-900 group-hover:text-[#1E3F20] transition-colors">
                            <a href="{{ route('magazine.show', $post->slug) }}" class="before:absolute before:inset-0">
                                {{ $post->title }}
                            </a>
                        </h2>
                        
                        <p class="text-gray-600 text-sm mb-8 line-clamp-3 leading-relaxed">
                            {{ Str::limit(strip_tags($post->content), 150) }}
                        </p>
                        
                        <div class="mt-auto pt-5 border-t border-gray-100 flex items-center justify-between">
                            <div class="flex items-center text-xs font-medium text-gray-400">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ $post->created_at->format('d/m/Y') }}
                            </div>
                            <span class="text-sm font-bold text-[#1E3F20] group-hover:text-[#D4AF37] transition-colors flex items-center">
                                Đọc tiếp
                                <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </span>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-32 text-center bg-white rounded-3xl border border-dashed border-gray-300">
                    <svg class="w-20 h-20 text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <h3 class="text-2xl font-serif font-bold text-gray-900 mb-2">Chưa có ấn phẩm</h3>
                    <p class="text-gray-500 max-w-md mx-auto">Ban biên tập đang trong quá trình chuẩn bị những câu chuyện mới. Hãy quay lại sau nhé!</p>
                </div>
            @endforelse
        </div>

        @if($posts->hasPages())
        <div class="mt-20 flex justify-center">
            {{ $posts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
