@extends('layouts.client')

@section('title', $post->title . ' - Cẩm Nang Rẻo Cao')

@section('content')
<div class="h-[75px] w-full bg-white"></div>

<article class="bg-white min-h-screen pb-24">
    <!-- Hero Image for Post -->
    <div class="relative w-full h-[50vh] min-h-[400px]">
        <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" decoding="async" fetchpriority="high" class="w-full h-full object-cover" />
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
        <div class="absolute bottom-0 left-0 w-full p-8 md:p-16 text-center">
            <span class="inline-block py-1 px-3 rounded-full bg-white/20 backdrop-blur-sm text-[#D4AF37] text-xs font-bold tracking-widest uppercase mb-4 border border-[#D4AF37]/30">
                Tạp chí văn hóa
            </span>
            <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold text-white max-w-4xl mx-auto font-serif leading-tight drop-shadow-md">
                {{ $post->title }}
            </h1>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 -mt-8 relative z-10">
        <!-- Meta Info Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 flex flex-col md:flex-row items-center justify-between gap-4 mb-12">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-[#1E3F20] rounded-full flex items-center justify-center text-[#D4AF37] font-bold text-xl mr-4 shadow-inner">
                    RC
                </div>
                <div>
                    <p class="font-bold text-gray-900">Ban Biên Tập Rẻo Cao</p>
                    <p class="text-sm text-gray-500 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Xuất bản ngày {{ $post->created_at->format('d/m/Y') }}
                    </p>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <span class="text-sm text-gray-500 font-medium mr-2">Chia sẻ:</span>
                <button class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                </button>
                <button class="w-8 h-8 rounded-full bg-blue-800 text-white flex items-center justify-center hover:bg-blue-900 transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                </button>
            </div>
        </div>

        <!-- Post Content -->
        <div class="prose prose-lg md:prose-xl max-w-none prose-headings:font-serif prose-headings:text-gray-900 prose-p:text-gray-700 prose-p:leading-relaxed prose-a:text-[#1E3F20] font-serif text-justify">
            <!-- First letter styling for the actual content -->
            <div class="first-letter:text-7xl first-letter:font-bold first-letter:text-[#1E3F20] first-letter:mr-3 first-letter:float-left first-letter:leading-[0.8] first-letter:mt-2">
                {!! nl2br(e($post->content)) !!}
            </div>
            
            <p class="mt-8 text-gray-600 italic border-l-4 border-[#D4AF37] pl-4">
                Trải dài theo các triền đồi dốc đứng, văn hóa canh tác và lối sống mộc mạc của đồng bào vùng cao luôn ẩn chứa sức hút nguyên bản kỳ lạ. Chuyến đi không chỉ mở ra không gian thiên nhiên hùng vĩ mà còn là dịp lắng đọng tâm hồn, chạm tay vào những giá trị lịch sử độc bản chưa từng bị mai một.
            </p>
        </div>

        <!-- Tags & Navigation -->
        <div class="mt-16 pt-8 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex gap-2">
                    <span class="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-full font-medium">#TayBac</span>
                    <span class="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-full font-medium">#VanHoa</span>
                    <span class="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-full font-medium">#ReoCao</span>
                </div>
                
                <a href="{{ route('magazine.index') }}" class="inline-flex items-center text-[#1E3F20] font-bold hover:text-[#D4AF37] transition-colors group">
                    <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Tất cả bài viết
                </a>
            </div>
        </div>
    </div>
</article>
@endsection
