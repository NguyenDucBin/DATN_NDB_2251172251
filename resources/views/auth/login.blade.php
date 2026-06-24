@extends('layouts.client')

@section('title', 'Đăng nhập - Rẻo Cao Journeys')

@section('content')
<div class="h-[75px] w-full bg-white"></div>

<div class="min-h-[calc(100vh-75px)] flex flex-col md:flex-row-reverse bg-[#FAF9F6]">
    
    <!-- Image Section -->
    <div class="hidden md:block md:w-1/2 relative bg-gray-900">
        <img src="{{ asset('images/static/auth-login.jpg') }}"
             alt="Rice terraces" 
             decoding="async" fetchpriority="high" class="absolute inset-0 w-full h-full object-cover opacity-80" />
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
        <div class="absolute bottom-0 left-0 p-12 text-white">
            <h2 class="text-4xl font-serif font-bold mb-4 drop-shadow-md">Chào mừng trở lại</h2>
            <p class="text-lg text-gray-200 drop-shadow-md max-w-md leading-relaxed">
                Tiếp tục hành trình khám phá những vẻ đẹp nguyên bản của vùng cao Tây Bắc.
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

            <h1 class="text-3xl font-bold text-gray-900 mb-2 font-serif">Đăng nhập</h1>
            <p class="text-gray-500 mb-8">Vui lòng điền thông tin để truy cập tài khoản</p>

            <x-auth-error-alert
                title="Đăng nhập chưa thành công"
                :messages="$errors->all()"
            />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-regular fa-envelope text-gray-400"></i>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="email@example.com" 
                               aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                               @class([
                                   'block w-full rounded-xl border bg-white py-3 pl-11 pr-4 text-gray-900 shadow-sm transition-all placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent',
                                   'border-gray-200 focus:ring-[#D4AF37]' => ! $errors->has('email'),
                                   'border-red-300 bg-red-50/50 focus:ring-red-200' => $errors->has('email'),
                               ]) />
                    </div>
                    <x-auth-field-error :messages="$errors->get('email')" />
                </div>

                <!-- Password -->
                <div x-data="{ show: false }">
                    <div class="flex justify-between items-center mb-1.5">
                        <label for="password" class="block text-sm font-medium text-gray-700">Mật khẩu</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-medium text-[#1E3F20] hover:text-[#D4AF37] hover:underline transition-colors">
                                Quên mật khẩu?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                        <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password" placeholder="••••••••" 
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
                </div>

                <!-- Remember Me -->
                <div class="flex items-center pt-1">
                    <input id="remember_me" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} class="w-4 h-4 border border-gray-300 rounded bg-white checked:bg-[#1E3F20] checked:border-[#1E3F20] focus:ring-2 focus:ring-[#1E3F20] focus:ring-offset-1 transition-colors cursor-pointer" />
                    <label for="remember_me" class="ml-2 text-sm text-gray-600 cursor-pointer">
                        Ghi nhớ đăng nhập
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 px-4 bg-[#1E3F20] hover:bg-[#2A5A2E] text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5 mt-2">
                    Đăng Nhập
                </button>
            </form>

            <div class="mt-8 relative">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm font-medium leading-6">
                    <span class="bg-[#FAF9F6] px-4 text-gray-500">Hoặc tiếp tục với</span>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-4">
                <a href="{{ route('auth.google.redirect') }}" class="flex w-full items-center justify-center gap-3 rounded-xl bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:ring-transparent transition-colors">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    <span class="text-sm font-semibold leading-6">Google</span>
                </a>
                <button type="button" class="flex w-full items-center justify-center gap-3 rounded-xl bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:ring-transparent transition-colors">
                    <svg class="h-5 w-5 text-[#1877F2]" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span class="text-sm font-semibold leading-6">Facebook</span>
                </button>
            </div>

            <div class="mt-8 text-center text-sm text-gray-600">
                Chưa có tài khoản? 
                <a href="{{ route('register') }}" class="font-bold text-[#1E3F20] hover:text-[#D4AF37] transition-colors underline decoration-dotted">
                    Đăng ký ngay
                </a>
            </div>
            
        </div>
    </div>
</div>
@endsection
