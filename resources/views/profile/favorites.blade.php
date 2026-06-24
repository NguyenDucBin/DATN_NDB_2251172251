@extends('layouts.client')

@section('title', 'Danh sách yêu thích - Rẻo Cao Journeys')

@section('content')
<div class="h-[75px] w-full bg-white"></div>

<div class="bg-[#FAF9F6] min-h-screen pb-20">
    
    <!-- Cover Image -->
    <div class="h-52 md:h-64 w-full relative bg-gray-900">
        <img src="{{ asset('images/static/destination-sa-pa.jpg') }}"
             decoding="async" class="absolute inset-0 w-full h-full object-cover opacity-70" alt="Cover" />
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-10">
        
        <!-- User Info Header -->
        <div class="flex flex-col md:flex-row items-center md:items-end gap-5 mb-10">
            <div class="w-28 h-28 md:w-36 md:h-36 rounded-full border-4 border-white shadow-xl overflow-hidden bg-white flex-shrink-0">
                <img src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover" />
            </div>
            <div class="text-center md:text-left pb-2">
                <h1 class="text-3xl md:text-4xl font-bold mb-1 font-serif text-gray-900 md:text-white drop-shadow-md">
                    {{ auth()->user()->name }}
                </h1>
                <p class="text-gray-600 md:text-gray-200 font-medium md:drop-shadow-sm">
                    <i class="fa-regular fa-heart mr-1"></i> Danh sách tour yêu thích
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                @include('profile.partials.sidebar')
            </div>

            <!-- Content -->
            <div class="lg:col-span-3">
                
                @if(session('status'))
                    <div class="mb-6 p-4 bg-green-50 text-green-700 text-sm rounded-xl font-medium flex items-center gap-2 border border-green-100">
                        <i class="fa-solid fa-check-circle"></i> {{ session('status') }}
                    </div>
                @endif

                @if($favorites->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($favorites as $tour)
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                                <!-- Tour Image -->
                                <div class="relative h-48 overflow-hidden">
                                    @php
                                        $fallbackImages = [
                                            asset('images/static/destination-sa-pa.jpg'),
                                            asset('images/static/destination-ha-giang.jpg'),
                                            asset('images/static/destination-mu-cang-chai.jpg'),
                                        ];
                                        $bgImg = $tour->images->first()?->url() ?? $fallbackImages[$loop->index % 3];
                                    @endphp
                                    <img src="{{ $bgImg }}" alt="{{ $tour->name }}" loading="lazy" decoding="async" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                    
                                    <!-- Remove Favorite -->
                                    <form action="{{ route('profile.favorites.toggle', $tour->id) }}" method="POST" class="absolute top-3 right-3">
                                        @csrf
                                        <button type="submit" class="w-10 h-10 bg-white/90 backdrop-blur rounded-full flex items-center justify-center hover:bg-red-50 transition-colors shadow-sm group/btn" title="Bỏ yêu thích">
                                            <i class="fa-solid fa-heart text-red-500 group-hover/btn:scale-125 transition-transform"></i>
                                        </button>
                                    </form>

                                    <!-- Duration Badge -->
                                    <div class="absolute bottom-3 left-3">
                                        <span class="bg-white/90 backdrop-blur text-[#1E3F20] text-xs font-bold px-3 py-1.5 rounded-full">
                                            {{ $tour->duration_days ?? '?' }} ngày {{ $tour->duration_nights ?? '?' }} đêm
                                        </span>
                                    </div>
                                </div>

                                <!-- Tour Info -->
                                <div class="p-5">
                                    <h3 class="font-bold text-gray-900 text-lg mb-2 line-clamp-1 group-hover:text-[#1E3F20] transition-colors">
                                        {{ $tour->name }}
                                    </h3>
                                    <div class="flex items-center justify-between">
                                        <div class="text-lg font-bold text-emerald-600">
                                            {{ number_format($tour->price, 0, ',', '.') }} ₫
                                            <span class="text-xs font-normal text-gray-400">/ người</span>
                                        </div>
                                        <a href="{{ route('tours.show', $tour->slug) }}" 
                                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#1E3F20] text-white text-sm font-semibold rounded-xl hover:bg-[#2A5A2E] transition-colors shadow-sm">
                                            Xem chi tiết <i class="fa-solid fa-arrow-right text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                        <div class="w-24 h-24 mx-auto mb-5 rounded-full bg-red-50 flex items-center justify-center">
                            <i class="fa-regular fa-heart text-4xl text-red-300"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-700 mb-2">Chưa có tour yêu thích</h3>
                        <p class="text-sm text-gray-500 mb-5 max-w-sm mx-auto">Bạn chưa thêm tour nào vào danh sách yêu thích. Hãy khám phá và lưu lại những tour bạn yêu thích nhé!</p>
                        <a href="{{ route('home') }}#tours" class="inline-flex items-center gap-2 px-6 py-3 bg-[#1E3F20] text-white font-semibold rounded-xl hover:bg-[#2A5A2E] transition-colors shadow-sm">
                            <i class="fa-solid fa-compass"></i> Khám phá tour
                        </a>
                    </div>
                @endif

                @if($favorites->hasPages())
                    <div class="mt-8">
                        {{ $favorites->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>

</div>
@endsection
