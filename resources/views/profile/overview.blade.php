@extends('layouts.client')

@section('title', 'Tổng quan tài khoản - Rẻo Cao Journeys')

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
                <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="w-full h-full object-cover" />
            </div>
            <div class="text-center md:text-left pb-2">
                <h1 class="text-3xl md:text-4xl font-bold mb-1 font-serif text-gray-900 md:text-white drop-shadow-md">
                    Xin chào, {{ $user->name }}!
                </h1>
                <p class="text-gray-600 md:text-gray-200 font-medium md:drop-shadow-sm">
                    <i class="fa-regular fa-calendar mr-1"></i> Thành viên từ {{ $user->created_at->format('m/Y') }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                @include('profile.partials.sidebar')
            </div>

            <!-- Content -->
            <div class="lg:col-span-3 space-y-8">
                @if (session('success'))
                    <div class="p-4 text-sm text-emerald-700 bg-emerald-100 border border-emerald-200 rounded-xl shadow-sm" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="p-4 text-sm text-red-700 bg-red-100 border border-red-200 rounded-xl shadow-sm" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center group hover:shadow-md transition-all hover:-translate-y-1 duration-300">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                            <i class="fa-solid fa-suitcase-rolling text-xl text-blue-600"></i>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                        <p class="text-sm text-gray-500 mt-1">Tổng chuyến đi</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center group hover:shadow-md transition-all hover:-translate-y-1 duration-300">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-amber-50 flex items-center justify-center group-hover:bg-amber-100 transition-colors">
                            <i class="fa-solid fa-clock text-xl text-amber-600"></i>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
                        <p class="text-sm text-gray-500 mt-1">Chờ xử lý</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center group hover:shadow-md transition-all hover:-translate-y-1 duration-300">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-emerald-50 flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                            <i class="fa-solid fa-circle-check text-xl text-emerald-600"></i>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['completed'] }}</p>
                        <p class="text-sm text-gray-500 mt-1">Hoàn thành</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center group hover:shadow-md transition-all hover:-translate-y-1 duration-300">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-red-50 flex items-center justify-center group-hover:bg-red-100 transition-colors">
                            <i class="fa-solid fa-ban text-xl text-red-500"></i>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['cancelled'] }}</p>
                        <p class="text-sm text-gray-500 mt-1">Đã hủy</p>
                    </div>
                </div>

                <!-- Recent Bookings -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-[#1E3F20] font-serif">Lịch sử chuyến đi</h2>
                            <p class="text-sm text-gray-500 mt-1">Danh sách các tour bạn đã đặt</p>
                        </div>
                    </div>

                    @if($recentBookings->count() > 0)
                        <div class="divide-y divide-gray-50">
                            @foreach($recentBookings as $booking)
                                @php
                                    $canRequestRefund = $booking->canRequestRefund();
                                @endphp
                                <div class="p-5 hover:bg-[#FAF9F6] transition-colors group">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div class="flex items-start gap-4 flex-1">
                                            <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-emerald-100 to-teal-50 flex items-center justify-center flex-shrink-0">
                                                <i class="fa-solid fa-mountain-sun text-2xl text-emerald-600"></i>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <h3 class="font-semibold text-gray-900 truncate group-hover:text-[#1E3F20] transition-colors">
                                                    {{ $booking->tour->name ?? 'Tour đã bị xóa' }}
                                                </h3>
                                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500 mt-1">
                                                    <span><i class="fa-regular fa-calendar mr-1"></i> {{ \Carbon\Carbon::parse($booking->start_date)->format('d/m/Y') }}</span>
                                                    <span><i class="fa-solid fa-users mr-1"></i> {{ $booking->number_of_people }} khách</span>
                                                    <span class="font-semibold text-emerald-600">{{ number_format($booking->total_price, 0, ',', '.') }} ₫</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 sm:w-56">
                                            <div class="flex flex-col items-start sm:items-end gap-2">
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full border {{ $booking->statusBadgeClasses() }}">
                                                    {{ $booking->statusLabel() }}
                                                </span>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full border {{ $booking->paymentStatusBadgeClasses() }}">
                                                    {{ $booking->paymentStatusLabel() }}
                                                </span>

                                                @if($canRequestRefund)
                                                    <form action="{{ route('profile.refunds.store', $booking->id) }}" method="POST" class="w-full space-y-2">
                                                        @csrf
                                                        <input type="text" name="reason" maxlength="500" placeholder="Lý do hoàn tiền" class="w-full rounded-lg border-gray-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                                        <button type="submit" class="w-full inline-flex justify-center items-center px-3 py-2 text-xs font-semibold text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                                            Yêu cầu hoàn tiền
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($recentBookings->hasPages())
                            <div class="border-t border-gray-100 bg-gray-50 px-5 py-4">
                                {{ $recentBookings->links() }}
                            </div>
                        @endif
                    @else
                        <div class="p-12 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-50 flex items-center justify-center">
                                <i class="fa-solid fa-suitcase-rolling text-3xl text-gray-300"></i>
                            </div>
                            <h3 class="font-semibold text-gray-700 mb-2">Chưa có chuyến đi nào</h3>
                            <p class="text-sm text-gray-500 mb-4">Hãy khám phá các tour hấp dẫn tại Tây Bắc!</p>
                            <a href="{{ route('home') }}#tours" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1E3F20] text-white font-semibold rounded-xl hover:bg-[#2A5A2E] transition-colors shadow-sm">
                                <i class="fa-solid fa-compass"></i> Khám phá ngay
                            </a>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
