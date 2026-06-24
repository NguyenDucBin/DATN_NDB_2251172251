@extends('layouts.client')

@section('title', 'Mã giảm giá - Rẻo Cao Journeys')

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
                    <i class="fa-solid fa-ticket mr-1"></i> Mã giảm giá có thể sử dụng
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
                
                @if($coupons->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach($coupons as $coupon)
                            <div class="relative bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-lg transition-all duration-300" x-data="{ copied: false }">
                                
                                <!-- Decorative left strip -->
                                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-[#D4AF37] to-[#1E3F20]"></div>

                                <div class="p-5 pl-7">
                                    <!-- Discount Amount -->
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <div class="text-3xl font-extrabold text-[#1E3F20]">
                                                @if($coupon->discount_type == 'percent')
                                                    {{ $coupon->discount_amount }}%
                                                @else
                                                    {{ number_format($coupon->discount_amount, 0, ',', '.') }}₫
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-500 font-medium">
                                                @if($coupon->discount_type == 'percent')
                                                    Giảm {{ $coupon->discount_amount }}% trên tổng đơn
                                                @else
                                                    Giảm {{ number_format($coupon->discount_amount, 0, ',', '.') }}₫
                                                @endif
                                            </p>
                                        </div>
                                        <div class="w-12 h-12 rounded-xl bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-tag text-xl text-[#D4AF37]"></i>
                                        </div>
                                    </div>

                                    <!-- Coupon Code -->
                                    <div class="flex items-center gap-3 mb-4">
                                        <div class="flex-1 bg-gray-50 border border-dashed border-gray-300 rounded-xl px-4 py-2.5 font-mono font-bold text-lg text-center tracking-widest text-gray-800">
                                            {{ $coupon->code }}
                                        </div>
                                        <button @click="navigator.clipboard.writeText('{{ $coupon->code }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                                class="flex-shrink-0 w-11 h-11 rounded-xl bg-[#1E3F20] hover:bg-[#2A5A2E] text-white flex items-center justify-center transition-colors shadow-sm"
                                                :title="copied ? 'Đã sao chép!' : 'Sao chép mã'">
                                            <i class="fa-regular fa-copy text-lg" x-show="!copied"></i>
                                            <i class="fa-solid fa-check text-lg" x-show="copied" x-cloak></i>
                                        </button>
                                    </div>

                                    <!-- Info -->
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500">
                                        @if($coupon->valid_until)
                                            <span class="flex items-center gap-1">
                                                <i class="fa-regular fa-clock"></i> 
                                                HSD: {{ $coupon->valid_until->format('d/m/Y') }}
                                            </span>
                                        @else
                                            <span class="flex items-center gap-1">
                                                <i class="fa-solid fa-infinity"></i> 
                                                Vô thời hạn
                                            </span>
                                        @endif
                                        @if($coupon->usage_limit)
                                            <span class="flex items-center gap-1">
                                                <i class="fa-solid fa-users"></i> 
                                                Còn {{ $coupon->usage_limit - $coupon->used_count }} lượt
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Copied Toast -->
                                <div x-show="copied" x-transition class="absolute top-3 right-3 bg-emerald-500 text-white text-xs font-semibold px-3 py-1.5 rounded-full shadow-lg" x-cloak>
                                    <i class="fa-solid fa-check mr-1"></i> Đã sao chép!
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                        <div class="w-24 h-24 mx-auto mb-5 rounded-full bg-amber-50 flex items-center justify-center">
                            <i class="fa-solid fa-ticket text-4xl text-amber-300"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-700 mb-2">Chưa có mã giảm giá</h3>
                        <p class="text-sm text-gray-500 max-w-sm mx-auto">Hiện tại chưa có mã giảm giá nào đang hoạt động. Hãy quay lại sau nhé!</p>
                    </div>
                @endif

            </div>
        </div>
    </div>

</div>
@endsection
