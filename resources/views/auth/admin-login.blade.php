@extends('layouts.client')

@section('title', 'Đăng nhập Quản trị - Rẻo Cao Journeys')

@section('content')
<div class="h-[75px] w-full bg-white"></div>

<div class="min-h-[calc(100vh-75px)] flex items-center justify-center bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 relative overflow-hidden">
    
    <!-- Background decoration -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-96 h-96 bg-red-500 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-blue-500 rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
    </div>

    <div class="w-full max-w-md px-6 relative z-10">
        
        <!-- Card -->
        <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl p-8 md:p-10">
            
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-red-500/20 rounded-2xl flex items-center justify-center border border-red-500/30">
                    <i class="fa-solid fa-shield-halved text-red-400 text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-white font-serif">Hệ thống Quản trị</h1>
                <p class="text-slate-400 text-sm mt-2">Đăng nhập tài khoản Admin để quản lý hệ thống</p>
            </div>

            <x-auth-error-alert
                title="Không thể đăng nhập Admin"
                :messages="$errors->all()"
                theme="dark"
            />

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf

                <div class="space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-300 mb-1.5">Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500"><i class="fa-solid fa-envelope text-sm"></i></span>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" 
                                   aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                                   @class([
                                       'block w-full rounded-xl border bg-white/5 py-3 pl-10 pr-4 text-white transition-all placeholder-slate-500 focus:ring-2 focus:bg-white/10 sm:text-sm',
                                       'border-white/10 focus:border-red-500/50 focus:ring-red-500/30' => ! $errors->has('email'),
                                       'border-red-400/60 focus:border-red-400 focus:ring-red-500/30' => $errors->has('email'),
                                   ])
                                   placeholder="admin@reocao.vn" required autofocus>
                        </div>
                        <x-auth-field-error :messages="$errors->get('email')" theme="dark" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-300 mb-1.5">Mật khẩu</label>
                        <div class="relative" x-data="{ show: false }">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500"><i class="fa-solid fa-lock text-sm"></i></span>
                            <input :type="show ? 'text' : 'password'" name="password" id="password" 
                                   aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                   @class([
                                       'block w-full rounded-xl border bg-white/5 py-3 pl-10 pr-12 text-white transition-all placeholder-slate-500 focus:ring-2 focus:bg-white/10 sm:text-sm',
                                       'border-white/10 focus:border-red-500/50 focus:ring-red-500/30' => ! $errors->has('password'),
                                       'border-red-400/60 focus:border-red-400 focus:ring-red-500/30' => $errors->has('password'),
                                   ])
                                   placeholder="••••••••" required>
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-500 hover:text-slate-300 transition-colors">
                                <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-sm"></i>
                            </button>
                        </div>
                        <x-auth-field-error :messages="$errors->get('password')" theme="dark" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label for="admin_remember" class="flex items-center gap-2 text-sm text-slate-400 cursor-pointer">
                            <input id="admin_remember" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} class="rounded border-white/20 bg-white/5 text-red-500 focus:ring-red-500/30">
                            Ghi nhớ đăng nhập
                        </label>
                    </div>

                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold rounded-xl hover:from-red-700 hover:to-red-800 transition-all shadow-lg shadow-red-500/25 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập Admin
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm text-slate-500 hover:text-slate-300 transition-colors">
                    ← Quay lại đăng nhập thường
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
