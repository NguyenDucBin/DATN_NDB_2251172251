@extends('layouts.client')

@section('title', 'Đăng nhập Chủ nhà - Rẻo Cao Journeys')

@section('content')
<div class="h-[75px] w-full bg-white"></div>

<div class="min-h-[calc(100vh-75px)] flex items-center justify-center bg-gradient-to-br from-emerald-900 via-emerald-800 to-teal-900 relative overflow-hidden">
    
    <!-- Background decoration -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-400 rounded-full blur-3xl translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-teal-400 rounded-full blur-3xl -translate-x-1/2 translate-y-1/2"></div>
    </div>

    <div class="w-full max-w-md px-6 relative z-10">
        
        <!-- Card -->
        <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl p-8 md:p-10">
            
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-emerald-500/20 rounded-2xl flex items-center justify-center border border-emerald-500/30">
                    <i class="fa-solid fa-house-user text-emerald-400 text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-white font-serif">Kênh Chủ Nhà</h1>
                <p class="text-emerald-300/60 text-sm mt-2">Đăng nhập tài khoản Host để quản lý tour và đặt chỗ</p>
            </div>

            <x-auth-error-alert
                title="Không thể đăng nhập Host"
                :messages="$errors->all()"
                theme="dark"
            />

            <form method="POST" action="{{ route('host.login.submit') }}">
                @csrf

                <div class="space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-medium text-emerald-200/80 mb-1.5">Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-emerald-500/50"><i class="fa-solid fa-envelope text-sm"></i></span>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" 
                                   aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                                   @class([
                                       'block w-full rounded-xl border bg-white/5 py-3 pl-10 pr-4 text-white transition-all placeholder-emerald-500/40 focus:ring-2 focus:bg-white/10 sm:text-sm',
                                       'border-white/10 focus:border-emerald-500/50 focus:ring-emerald-500/30' => ! $errors->has('email'),
                                       'border-red-400/60 focus:border-red-400 focus:ring-red-500/30' => $errors->has('email'),
                                   ])
                                   placeholder="host@reocao.vn" required autofocus>
                        </div>
                        <x-auth-field-error :messages="$errors->get('email')" theme="dark" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-emerald-200/80 mb-1.5">Mật khẩu</label>
                        <div class="relative" x-data="{ show: false }">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-emerald-500/50"><i class="fa-solid fa-lock text-sm"></i></span>
                            <input :type="show ? 'text' : 'password'" name="password" id="password" 
                                   aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                   @class([
                                       'block w-full rounded-xl border bg-white/5 py-3 pl-10 pr-12 text-white transition-all placeholder-emerald-500/40 focus:ring-2 focus:bg-white/10 sm:text-sm',
                                       'border-white/10 focus:border-emerald-500/50 focus:ring-emerald-500/30' => ! $errors->has('password'),
                                       'border-red-400/60 focus:border-red-400 focus:ring-red-500/30' => $errors->has('password'),
                                   ])
                                   placeholder="••••••••" required>
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-emerald-500/50 hover:text-emerald-300 transition-colors">
                                <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-sm"></i>
                            </button>
                        </div>
                        <x-auth-field-error :messages="$errors->get('password')" theme="dark" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label for="host_remember" class="flex items-center gap-2 text-sm text-emerald-300/50 cursor-pointer">
                            <input id="host_remember" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} class="rounded border-white/20 bg-white/5 text-emerald-500 focus:ring-emerald-500/30">
                            Ghi nhớ đăng nhập
                        </label>
                    </div>

                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-xl hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg shadow-emerald-500/25 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập Host
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center space-y-3">
                <a href="{{ route('register', ['role' => 'host']) }}" class="inline-flex items-center justify-center gap-2 text-sm font-semibold text-emerald-200 hover:text-white transition-colors">
                    <i class="fa-solid fa-user-plus"></i>
                    Đăng ký làm Host
                </a>
                <br>
                <a href="{{ route('login') }}" class="text-sm text-emerald-500/40 hover:text-emerald-300 transition-colors">
                    ← Quay lại đăng nhập thường
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
