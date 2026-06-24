@extends('layouts.client')

@section('title', 'Đăng ký tài khoản - Rẻo Cao Journeys')

@section('content')
<div class="h-[75px] w-full bg-white"></div>

<div class="min-h-[calc(100vh-75px)] flex flex-col md:flex-row bg-[#FAF9F6]">
    
    <!-- Image Section -->
    <div class="hidden md:block md:w-1/2 relative bg-gray-900">
        <img src="{{ asset('images/static/auth-register.jpg') }}"
             alt="Traditional house" 
             decoding="async" fetchpriority="high" class="absolute inset-0 w-full h-full object-cover opacity-80" />
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
        <div class="absolute bottom-0 left-0 p-12 text-white">
            <h2 class="text-4xl font-serif font-bold mb-4 drop-shadow-md">Bắt đầu hành trình của bạn</h2>
            <p class="text-lg text-gray-200 drop-shadow-md max-w-md leading-relaxed">
                Tham gia cộng đồng những người yêu thích văn hóa và thiên nhiên núi rừng phía Bắc. Cùng nhau khám phá những trải nghiệm độc bản.
            </p>
        </div>
    </div>

    <!-- Form Section -->
    <div class="w-full md:w-1/2 flex items-center justify-center p-6 md:p-12 lg:p-20 overflow-y-auto">
        <div class="w-full max-w-md">
            
            <div class="mb-8 flex justify-center md:justify-start">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 group">
                    <div class="w-10 h-10 bg-[#1E3F20] rounded-lg flex items-center justify-center shadow-md group-hover:bg-[#2A5A2E] transition-colors">
                        <i class="fa-solid fa-mountain-sun text-white"></i>
                    </div>
                    <span class="font-serif text-2xl font-bold text-[#1E3F20]">Rẻo Cao</span>
                </a>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-2 font-serif">Tạo tài khoản</h1>
            <p class="text-gray-500 mb-8">Đăng ký để bắt đầu khám phá những trải nghiệm độc đáo</p>

            <x-auth-error-alert
                title="Đăng ký chưa hoàn tất"
                :messages="$errors->all()"
            />

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                @php
                    $selectedAccountType = old('account_type', request('role') === 'host' ? 'host' : 'tourist');
                @endphp

                <!-- Account Type -->
                <div x-data="{ accountType: '{{ $selectedAccountType }}' }">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Loại tài khoản <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label :class="accountType === 'tourist' ? 'border-[#1E3F20] ring-2 ring-[#1E3F20]/15' : 'border-gray-200'" class="relative flex cursor-pointer rounded-xl border bg-white p-4 shadow-sm transition hover:border-[#D4AF37]">
                            <input type="radio" name="account_type" value="tourist" x-model="accountType" class="sr-only" {{ $selectedAccountType === 'tourist' ? 'checked' : '' }}>
                            <span class="flex items-start gap-3">
                                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-700">
                                    <i class="fa-solid fa-suitcase-rolling"></i>
                                </span>
                                <span>
                                    <span class="block text-sm font-semibold text-gray-900">Khách du lịch</span>
                                    <span class="mt-1 block text-xs leading-5 text-gray-500">Đặt tour và quản lý chuyến đi.</span>
                                </span>
                            </span>
                        </label>

                        <label :class="accountType === 'host' ? 'border-[#1E3F20] ring-2 ring-[#1E3F20]/15' : 'border-gray-200'" class="relative flex cursor-pointer rounded-xl border bg-white p-4 shadow-sm transition hover:border-[#D4AF37]">
                            <input type="radio" name="account_type" value="host" x-model="accountType" class="sr-only" {{ $selectedAccountType === 'host' ? 'checked' : '' }}>
                            <span class="flex items-start gap-3">
                                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                                    <i class="fa-solid fa-house-user"></i>
                                </span>
                                <span>
                                    <span class="block text-sm font-semibold text-gray-900">Đăng ký Host</span>
                                    <span class="mt-1 block text-xs leading-5 text-gray-500">Chờ admin duyệt trước khi mở kênh Host.</span>
                                </span>
                            </span>
                        </label>
                    </div>
                    <x-auth-field-error :messages="$errors->get('account_type')" />
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Họ và tên <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-regular fa-user text-gray-400"></i>
                        </div>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Ví dụ: Nguyễn Văn A" 
                               aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                               @class([
                                   'block w-full rounded-xl border bg-white py-3 pl-11 pr-4 text-gray-900 shadow-sm transition-all placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent',
                                   'border-gray-200 focus:ring-[#D4AF37]' => ! $errors->has('name'),
                                   'border-red-300 bg-red-50/50 focus:ring-red-200' => $errors->has('name'),
                               ]) />
                    </div>
                    <x-auth-field-error :messages="$errors->get('name')" />
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-regular fa-envelope text-gray-400"></i>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="email@example.com" 
                               aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                               @class([
                                   'block w-full rounded-xl border bg-white py-3 pl-11 pr-4 text-gray-900 shadow-sm transition-all placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent',
                                   'border-gray-200 focus:ring-[#D4AF37]' => ! $errors->has('email'),
                                   'border-red-300 bg-red-50/50 focus:ring-red-200' => $errors->has('email'),
                               ]) />
                    </div>
                    <x-auth-field-error :messages="$errors->get('email')" />
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Số điện thoại <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-mobile-screen text-gray-400"></i>
                        </div>
                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel" placeholder="0912345678" 
                               aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}"
                               @class([
                                   'block w-full rounded-xl border bg-white py-3 pl-11 pr-4 text-gray-900 shadow-sm transition-all placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent',
                                   'border-gray-200 focus:ring-[#D4AF37]' => ! $errors->has('phone'),
                                   'border-red-300 bg-red-50/50 focus:ring-red-200' => $errors->has('phone'),
                               ]) />
                    </div>
                    <x-auth-field-error :messages="$errors->get('phone')" />
                </div>

                <!-- Password -->
                <div x-data="{ show: false }">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Mật khẩu <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                        <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="new-password" placeholder="••••••••" 
                               aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                               @class([
                                   'block w-full rounded-xl border bg-white py-3 pl-11 pr-12 text-gray-900 shadow-sm transition-all placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent',
                                   'border-gray-200 focus:ring-[#D4AF37]' => ! $errors->has('password'),
                                   'border-red-300 bg-red-50/50 focus:ring-red-200' => $errors->has('password'),
                               ]) />
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                            <i class="fa-regular" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    <x-auth-field-error :messages="$errors->get('password')" />
                    <div class="mt-2 text-xs text-gray-500 flex flex-wrap gap-x-3 gap-y-1">
                        <span class="flex items-center gap-1"><i class="fa-solid fa-check text-[#D4AF37]"></i> Tối thiểu 8 ký tự</span>
                        <span class="flex items-center gap-1"><i class="fa-solid fa-check text-[#D4AF37]"></i> Chữ in hoa</span>
                        <span class="flex items-center gap-1"><i class="fa-solid fa-check text-[#D4AF37]"></i> Chữ in thường</span>
                        <span class="flex items-center gap-1"><i class="fa-solid fa-check text-[#D4AF37]"></i> Chữ số</span>
                        <span class="flex items-center gap-1"><i class="fa-solid fa-check text-[#D4AF37]"></i> Ký tự đặc biệt</span>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div x-data="{ show: false }">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Xác nhận mật khẩu <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-shield-halved text-gray-400"></i>
                        </div>
                        <input id="password_confirmation" :type="show ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" 
                               aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                               @class([
                                   'block w-full rounded-xl border bg-white py-3 pl-11 pr-12 text-gray-900 shadow-sm transition-all placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent',
                                   'border-gray-200 focus:ring-[#D4AF37]' => ! $errors->has('password'),
                                   'border-red-300 bg-red-50/50 focus:ring-red-200' => $errors->has('password'),
                               ]) />
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                            <i class="fa-regular" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <!-- Terms -->
                <div class="flex items-start gap-3 pt-2">
                    <div class="flex items-center h-5">
                        <input id="terms" type="checkbox" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} required class="w-4 h-4 border border-gray-300 rounded bg-white checked:bg-[#1E3F20] checked:border-[#1E3F20] focus:ring-2 focus:ring-[#1E3F20] focus:ring-offset-1 transition-colors cursor-pointer" />
                    </div>
                    <label for="terms" class="text-sm text-gray-600 leading-relaxed cursor-pointer">
                        Tôi đồng ý với 
                        <a href="#" class="font-semibold text-[#1E3F20] hover:text-[#D4AF37] transition-colors underline decoration-dotted">Điều khoản dịch vụ</a> 
                        và 
                        <a href="#" class="font-semibold text-[#1E3F20] hover:text-[#D4AF37] transition-colors underline decoration-dotted">Chính sách bảo mật</a>
                    </label>
                </div>
                <x-auth-field-error :messages="$errors->get('terms')" />

                <button type="submit" class="w-full py-3.5 px-4 bg-[#1E3F20] hover:bg-[#2A5A2E] text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5 mt-4">
                    Đăng Ký Tài Khoản
                </button>
            </form>

            <div class="mt-8 relative">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm font-medium leading-6">
                    <span class="bg-[#FAF9F6] px-4 text-gray-500">Hoặc đăng ký với</span>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-4">
                <button type="button" class="flex w-full items-center justify-center gap-3 rounded-xl bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:ring-transparent transition-colors">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    <span class="text-sm font-semibold leading-6">Google</span>
                </button>
                <button type="button" class="flex w-full items-center justify-center gap-3 rounded-xl bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:ring-transparent transition-colors">
                    <svg class="h-5 w-5 text-[#1877F2]" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span class="text-sm font-semibold leading-6">Facebook</span>
                </button>
            </div>

            <div class="mt-8 text-center text-sm text-gray-600">
                Đã có tài khoản? 
                <a href="{{ route('login') }}" class="font-bold text-[#1E3F20] hover:text-[#D4AF37] transition-colors underline decoration-dotted">
                    Đăng nhập ngay
                </a>
            </div>
            
        </div>
    </div>
</div>
@endsection
