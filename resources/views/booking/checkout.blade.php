@extends('layouts.client')

@section('title', 'Thanh toán Đặt Tour')

@section('content')
<div class="bg-[#FAF9F6] pt-28 pb-24 min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('tours.show', $tour->slug) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-[#1E3F20] mb-5 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                Quay lại tour
            </a>
            <div class="flex flex-col gap-2">
                <span class="text-[#D4AF37] uppercase tracking-widest text-sm font-bold">Hoàn tất đặt tour</span>
                <h1 class="text-3xl md:text-4xl font-bold font-serif text-[#1E3F20]" id="step-title">
                    Thông tin đặt tour
                </h1>
                <p class="text-gray-500" id="step-desc">Vui lòng điền đầy đủ thông tin để hoàn tất đặt tour.</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-100 text-sm text-red-700 rounded-xl shadow-sm">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                            <span>{{ $error }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('booking.store', $tour->id) }}" method="POST" id="booking-form">
            @csrf
            <div class="grid lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <div id="step-1">
                        <div class="grid grid-cols-2 gap-3 mb-6">
                            <div class="rounded-xl border-2 border-[#1E3F20] bg-white px-4 py-3 shadow-sm">
                                <div class="text-xs font-bold uppercase tracking-wider text-[#1E3F20]">Bước 1</div>
                                <div class="text-sm text-gray-600">Thông tin chuyến đi</div>
                            </div>
                            <div class="rounded-xl border border-gray-200 bg-white px-4 py-3">
                                <div class="text-xs font-bold uppercase tracking-wider text-gray-400">Bước 2</div>
                                <div class="text-sm text-gray-500">Thanh toán</div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 shadow-sm space-y-6">
                            <h2 class="text-xl font-bold font-serif text-gray-900">Thông tin liên hệ</h2>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Họ và tên *</label>
                                <input type="text" value="{{ auth()->user()->name }}" readonly class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-700" />
                            </div>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                    <input type="email" value="{{ auth()->user()->email }}" readonly class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-700" />
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại *</label>
                                    <input type="tel" name="phone" id="phone" value="{{ old('phone', auth()->user()->phone) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-[#1E3F20]/30 focus:border-[#1E3F20]" placeholder="0912345678" />
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 shadow-sm space-y-6 mt-6">
                            <h2 class="text-xl font-bold font-serif text-gray-900">Chi tiết chuyến đi</h2>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Ngày khởi hành *</label>
                                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-[#1E3F20]/30 focus:border-[#1E3F20]" min="{{ date('Y-m-d') }}" />
                                </div>
                                <div>
                                    <label for="guests" class="block text-sm font-medium text-gray-700 mb-2">Số lượng khách *</label>
                                    <select name="number_of_people" id="guests" class="w-full px-4 py-3 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-[#1E3F20]/30 focus:border-[#1E3F20]" onchange="calculateTotal()">
                                        @for ($i = 1; $i <= $tour->capacity; $i++)
                                            <option value="{{ $i }}" {{ (int) old('number_of_people', 1) === $i ? 'selected' : '' }}>{{ $i }} khách</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="button" onclick="nextStep()" class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-6 bg-[#1E3F20] hover:bg-[#2A5A2E] text-white rounded-xl transition-all font-bold shadow-md hover:shadow-lg">
                                Tiếp tục
                                <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <div id="step-2" class="hidden">
                        <div class="grid grid-cols-2 gap-3 mb-6">
                            <div class="rounded-xl border border-gray-200 bg-white px-4 py-3">
                                <div class="text-xs font-bold uppercase tracking-wider text-gray-400">Bước 1</div>
                                <div class="text-sm text-gray-500">Thông tin chuyến đi</div>
                            </div>
                            <div class="rounded-xl border-2 border-[#D4AF37] bg-white px-4 py-3 shadow-sm">
                                <div class="text-xs font-bold uppercase tracking-wider text-[#b89528]">Bước 2</div>
                                <div class="text-sm text-gray-600">Thanh toán</div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 shadow-sm space-y-6">
                            <h2 class="text-xl mb-2 font-bold font-serif text-gray-900">Phương thức thanh toán</h2>
                            <div class="space-y-3">
                                <label class="flex items-center gap-4 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#D4AF37] transition-colors">
                                    <input type="radio" name="payment_method" value="vnpay" checked class="w-4 h-4 text-[#D4AF37] focus:ring-[#D4AF37]">
                                    <span class="font-semibold text-gray-800"><i class="fa-solid fa-credit-card mr-2 text-[#D4AF37]"></i>VNPay</span>
                                </label>
                                <label class="flex items-center gap-4 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#D4AF37] transition-colors">
                                    <input type="radio" name="payment_method" value="bank_transfer" class="w-4 h-4 text-[#D4AF37] focus:ring-[#D4AF37]">
                                    <span class="font-semibold text-gray-800"><i class="fa-solid fa-money-bill-transfer mr-2 text-[#D4AF37]"></i>Chuyển khoản ngân hàng</span>
                                </label>
                            </div>

                            <div class="border-t border-gray-100 pt-5">
                                <label for="coupon_code" class="block text-sm font-medium text-gray-700 mb-2">Mã giảm giá</label>
                                <div class="relative">
                                    <i class="fa-solid fa-ticket absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" name="coupon_code" id="coupon_code" value="{{ old('coupon_code') }}"
                                           class="w-full rounded-xl border border-gray-300 py-3 pl-11 pr-4 uppercase outline-none focus:border-[#1E3F20] focus:ring-2 focus:ring-[#1E3F20]/20"
                                           maxlength="50" placeholder="Nhập mã (nếu có)">
                                </div>
                                <p class="mt-2 text-xs text-gray-500">Mức giảm hợp lệ sẽ được áp dụng khi xác nhận booking.</p>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-4 mt-6">
                            <button type="button" onclick="prevStep()" class="sm:w-auto px-6 py-3.5 border border-gray-300 rounded-xl hover:bg-white transition-colors font-semibold text-gray-700">
                                Quay lại
                            </button>
                            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 py-3.5 px-6 bg-[#D4AF37] hover:bg-[#c29f2f] text-white rounded-xl transition-all font-bold shadow-md hover:shadow-lg">
                                Xác nhận thanh toán
                                <i class="fa-solid fa-lock"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="border border-gray-100 rounded-2xl p-6 bg-white shadow-sm sticky top-24">
                        <h3 class="mb-4 font-bold text-xl font-serif text-gray-900">Tóm tắt booking</h3>
                        <h4 class="mb-4 text-lg font-semibold text-[#1E3F20]">{{ $tour->name }}</h4>
                        
                        <div class="space-y-3 text-sm mb-6 pb-6 border-b border-gray-200">
                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Giá tour:</span>
                                <span class="font-medium text-gray-900">{{ number_format($tour->price, 0, ',', '.') }} ₫</span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Số khách:</span>
                                <span class="font-medium text-gray-900" id="summary-guests">1 người</span>
                            </div>
                        </div>

                        <div class="pt-2 flex justify-between items-end gap-4">
                            <strong class="text-gray-900">Tổng cộng</strong>
                            <strong class="text-2xl text-[#1E3F20]" id="summary-total">
                                {{ number_format($tour->price, 0, ',', '.') }} ₫
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const pricePerPerson = {{ (float) $tour->price }};
    
    function calculateTotal() {
        const guests = Number(document.getElementById('guests').value || 1);
        const total = pricePerPerson * guests;
        document.getElementById('summary-guests').innerText = guests + ' người';
        document.getElementById('summary-total').innerText = total.toLocaleString('vi-VN') + ' ₫';
    }

    function nextStep() {
        const startDate = document.getElementById('start_date');
        const phone = document.getElementById('phone');

        if (!phone.value.trim()) {
            phone.reportValidity();
            return;
        }

        if (!startDate.value) {
            startDate.reportValidity();
            return;
        }

        document.getElementById('step-1').classList.add('hidden');
        document.getElementById('step-2').classList.remove('hidden');
        document.getElementById('step-title').innerText = 'Thanh toán';
        document.getElementById('step-desc').innerText = 'Chọn phương thức thanh toán để hoàn tất booking.';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function prevStep() {
        document.getElementById('step-2').classList.add('hidden');
        document.getElementById('step-1').classList.remove('hidden');
        document.getElementById('step-title').innerText = 'Thông tin đặt tour';
        document.getElementById('step-desc').innerText = 'Vui lòng điền đầy đủ thông tin để hoàn tất đặt tour.';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    calculateTotal();
</script>
@endsection
