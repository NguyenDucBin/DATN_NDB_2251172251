@extends('layouts.client')

@section('title', 'Tất cả hành trình - Rẻo Cao Journeys')

@section('content')
<div class="h-[75px] w-full bg-white"></div>

<div class="bg-[#FAF9F6] min-h-screen pb-24">
    <!-- Header Section -->
    <section class="bg-[#1E3F20] py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: url('{{ asset('images/static/pattern-arabesque.png') }}')"></div>
        <div class="max-w-7xl mx-auto relative z-10 text-center">
            <h1 class="text-3xl md:text-5xl font-bold text-white mb-4 font-serif">Khám phá hành trình của bạn</h1>
            <p class="text-white/80 max-w-2xl mx-auto text-lg">Tìm kiếm và trải nghiệm những chuyến đi độc đáo, mang đậm bản sắc văn hóa vùng miền cùng Rẻo Cao Journeys.</p>
        </div>
    </section>

    <!-- Tour List Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900 font-serif">Tất cả hành trình ({{ $tours->total() }})</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @forelse($tours as $tour)
                <a href="{{ route('tours.show', $tour->slug) }}" class="group block bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 transform hover:-translate-y-1">
                    <div class="relative aspect-[4/3] overflow-hidden">
                        @if($tour->images->count() > 0)
                            <img src="{{ $tour->images->first()->url() }}" alt="{{ $tour->name }}" loading="lazy" decoding="async" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <i class="fa-solid fa-image text-gray-400 text-3xl"></i>
                            </div>
                        @endif
                        <div class="absolute top-3 left-3 px-3 py-1 bg-white/90 backdrop-blur-sm text-gray-900 text-xs font-bold rounded-full">
                            <i class="fa-solid fa-star text-[#D4AF37] mr-1"></i>
                            {{ $tour->reviews_count ? number_format($tour->reviews_avg_rating, 1) : 'Mới' }}
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center text-xs text-gray-500 mb-2 gap-2">
                            <span><i class="fa-solid fa-location-dot text-red-400 mr-1"></i>{{ $tour->location ?? 'Chưa cập nhật' }}</span>
                            <span>&bull;</span>
                            <span>{{ $tour->duration_days ?? '?' }} ngày</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3 line-clamp-2 leading-tight group-hover:text-[#1E3F20] transition-colors">
                            {{ $tour->name }}
                        </h3>
                        <div class="flex justify-between items-end">
                            <div class="text-sm text-gray-500">Từ</div>
                            <div class="text-lg font-bold text-[#1E3F20]">{{ number_format($tour->price, 0, ',', '.') }} ₫</div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-12 text-center text-gray-500">
                    <i class="fa-solid fa-box-open text-4xl mb-4 text-gray-300"></i>
                    <p>Không tìm thấy hành trình nào phù hợp với tìm kiếm của bạn.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $tours->links() }}
        </div>
    </section>
</div>
@endsection
