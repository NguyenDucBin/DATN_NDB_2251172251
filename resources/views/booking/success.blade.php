@extends('layouts.client')
@section('title', 'Đặt Tour Thành Công')

@section('content')
@php
    $isPaid = $booking->payment_status === 'paid';
    $isFailed = $booking->payment_status === 'failed' || $booking->status === 'cancelled';
    $isBankTransfer = $booking->payment_method === 'bank_transfer';

    $title = match (true) {
        $isFailed => 'Thanh toán chưa hoàn tất',
        $isPaid => 'Thanh toán thành công!',
        $isBankTransfer => 'Đã gửi yêu cầu đặt tour',
        default => 'Đã ghi nhận booking',
    };

    $message = match (true) {
        $isFailed => 'Thanh toán của bạn chưa thành công hoặc booking đã bị hủy. Bạn có thể đặt lại tour khi sẵn sàng.',
        $isPaid => 'Thanh toán đã được ghi nhận. Booking đang chờ Host xác nhận lịch tham gia.',
        $isBankTransfer => 'Admin sẽ kiểm tra chuyển khoản của bạn. Sau khi thanh toán được xác nhận, Host sẽ xác nhận lịch tham gia.',
        default => 'Booking của bạn đang chờ xử lý. Chúng tôi sẽ cập nhật trạng thái trong tài khoản của bạn.',
    };

    $iconWrap = $isFailed ? 'bg-red-100' : ($isPaid ? 'bg-green-100' : 'bg-amber-100');
    $iconClass = $isFailed ? 'fa-circle-xmark text-red-600' : ($isPaid ? 'fa-circle-check text-green-600' : 'fa-clock text-amber-600');
@endphp

<div class="bg-[#FAF9F6] pt-28 pb-20 min-h-[75vh] flex items-center justify-center px-6">
    <div class="max-w-md w-full text-center bg-white border border-gray-100 rounded-2xl shadow-sm p-8">
        <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 {{ $iconWrap }}">
            <i class="fa-solid {{ $iconClass }} text-5xl"></i>
        </div>

        <h1 class="text-3xl mb-4 font-bold font-serif text-[#1E3F20]">
            {{ $title }}
        </h1>

        <p class="text-gray-600 mb-8 leading-relaxed">
            Cảm ơn bạn đã đặt tour <strong>{{ $booking->tour->name }}</strong>.
            Mã đơn hàng của bạn là <strong>#RCJ{{ $booking->id }}</strong>.
            {{ $message }}
        </p>

        <div class="mb-8 grid gap-3 text-left">
            <div class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                <span class="text-sm text-gray-500">Booking</span>
                <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full border {{ $booking->statusBadgeClasses() }}">
                    {{ $booking->statusLabel() }}
                </span>
            </div>
            <div class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                <span class="text-sm text-gray-500">Thanh toán</span>
                <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full border {{ $booking->paymentStatusBadgeClasses() }}">
                    {{ $booking->paymentStatusLabel() }}
                </span>
            </div>
        </div>

        <div class="space-y-3">
            <a href="{{ route('home') }}" class="block w-full py-3 text-white rounded-xl transition-colors font-bold bg-[#1E3F20] hover:bg-[#2A5A2E]">
                Về trang chủ
            </a>
            <a href="{{ route('profile.overview') }}" class="block w-full py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors font-semibold text-gray-700">
                Xem chi tiết booking
            </a>
        </div>
    </div>
</div>
@endsection
