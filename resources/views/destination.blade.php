@extends('layouts.client')

@section('title', $destination['name'] . ' - Điểm đến | Rẻo Cao Journeys')

@section('content')
<div class="h-[75px] w-full bg-white"></div>

<!-- Hero Banner -->
<section class="relative h-[55vh] min-h-[380px] max-h-[520px] overflow-hidden">
    <img src="{{ $destination['image'] }}" alt="{{ $destination['name'] }}"
         decoding="async" fetchpriority="high" class="w-full h-full object-cover" />
    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/10"></div>
    
    <div class="absolute inset-0 flex items-end">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pb-12 md:pb-16">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm text-white/70 mb-5">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors">
                    <i class="fa-solid fa-house text-xs"></i> Trang chủ
                </a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-white font-medium">{{ $destination['name'] }}</span>
            </nav>

            <div class="flex items-center gap-4 mb-4">
                <div class="w-14 h-14 rounded-2xl bg-[#D4AF37]/90 backdrop-blur flex items-center justify-center shadow-lg">
                    <i class="{{ $destination['icon'] }} text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white font-serif drop-shadow-lg">
                        {{ $destination['name'] }}
                    </h1>
                    <p class="text-lg text-[#D4AF37] font-medium mt-1">{{ $destination['subtitle'] }}</p>
                </div>
            </div>
            
            <p class="text-white/80 max-w-2xl text-base md:text-lg leading-relaxed">
                {{ $destination['description'] }}
            </p>
        </div>
    </div>
</section>

<!-- Tours Section -->
<section class="bg-[#FAF9F6] py-16 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
            <div>
                <span class="text-[#D4AF37] uppercase tracking-widest text-sm font-bold block mb-2">Tour trải nghiệm</span>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 font-serif">
                    Khám phá {{ $destination['name'] }} cùng Rẻo Cao
                </h2>
                <p class="text-gray-500 mt-2">Tìm thấy <span class="font-semibold text-[#1E3F20]">{{ $tours->total() }}</span> tour tại {{ $destination['name'] }}</p>
            </div>
            <a href="{{ route('home') }}#destinations" class="inline-flex items-center gap-2 text-[#1E3F20] font-semibold hover:text-[#D4AF37] transition-colors group">
                <i class="fa-solid fa-arrow-left text-sm group-hover:-translate-x-1 transition-transform"></i>
                Tất cả điểm đến
            </a>
        </div>

        @if($tours->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($tours as $tour)
                    <a href="{{ route('tours.show', $tour->slug) }}" 
                       class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300">
                        
                        <!-- Tour Image -->
                        <div class="relative h-52 overflow-hidden">
                            @if($tour->images && $tour->images->count() > 0)
                                <img src="{{ $tour->images->first()->url() }}" 
                                     alt="{{ $tour->name }}" 
                                     loading="lazy" decoding="async" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-[#1E3F20]/20 to-[#D4AF37]/20 flex items-center justify-center">
                                    <i class="fa-solid fa-mountain-sun text-4xl text-[#1E3F20]/30"></i>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            
                            <!-- Duration Badge -->
                            @if($tour->duration_days)
                                <div class="absolute top-3 left-3">
                                    <span class="bg-white/95 backdrop-blur text-[#1E3F20] text-xs font-bold px-2.5 py-1.5 rounded-lg shadow-sm">
                                        <i class="fa-regular fa-clock mr-1"></i>{{ $tour->duration_days }}N{{ $tour->duration_nights }}Đ
                                    </span>
                                </div>
                            @endif

                            <!-- Favorite badge -->
                            @auth
                                @php $isFav = in_array($tour->id, $favoriteTourIds, true); @endphp
                                @if($isFav)
                                    <div class="absolute top-3 right-3">
                                        <span class="w-8 h-8 flex items-center justify-center bg-white/95 backdrop-blur rounded-full shadow-sm">
                                            <i class="fa-solid fa-heart text-red-500 text-xs"></i>
                                        </span>
                                    </div>
                                @endif
                            @endauth
                        </div>

                        <!-- Tour Info -->
                        <div class="p-5">
                            <div class="flex items-center gap-1.5 text-xs text-gray-500 mb-2">
                                <i class="fa-solid fa-location-dot text-red-400"></i>
                                <span>{{ $tour->location ?? $destination['name'] }}</span>
                            </div>
                            <h3 class="font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-[#1E3F20] transition-colors leading-snug">
                                {{ $tour->name }}
                            </h3>
                            
                            <!-- Host -->
                            <div class="flex items-center gap-2 mb-3 pb-3 border-b border-gray-100">
                                <img src="{{ $tour->host->avatarUrl(32) }}" 
                                     alt="{{ $tour->host->name }}" class="w-6 h-6 rounded-full object-cover" />
                                <span class="text-xs text-gray-500">Bởi <span class="font-medium text-gray-700">{{ $tour->host->name }}</span></span>
                            </div>

                            <!-- Price -->
                            <div class="flex items-end justify-between">
                                <div>
                                    <span class="text-xl font-extrabold text-emerald-600">{{ number_format($tour->price, 0, ',', '.') }}₫</span>
                                    <span class="text-xs text-gray-400 ml-1">/ người</span>
                                </div>
                                <span class="text-xs font-semibold text-[#D4AF37] bg-[#D4AF37]/10 px-2 py-1 rounded-lg">
                                    Chi tiết →
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-10">
                {{ $tours->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-20">
                <div class="w-28 h-28 mx-auto mb-6 rounded-full bg-[#1E3F20]/5 flex items-center justify-center">
                    <i class="{{ $destination['icon'] }} text-5xl text-[#1E3F20]/20"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-700 mb-3 font-serif">Chưa có tour tại {{ $destination['name'] }}</h3>
                <p class="text-gray-500 max-w-md mx-auto mb-8">
                    Hiện tại chưa có tour nào được đăng ký tại {{ $destination['name'] }}. Hãy quay lại sau hoặc khám phá các điểm đến khác nhé!
                </p>
                <a href="{{ route('home') }}#destinations" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-[#1E3F20] text-white font-semibold rounded-xl hover:bg-[#2A5A2E] transition-colors shadow-md">
                    <i class="fa-solid fa-compass"></i> Khám phá điểm đến khác
                </a>
            </div>
        @endif
    </div>
</section>
@endsection
