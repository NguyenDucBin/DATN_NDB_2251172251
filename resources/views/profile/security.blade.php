@extends('layouts.client')

@section('title', 'Bảo mật tài khoản - Rẻo Cao Journeys')

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
                    <i class="fa-solid fa-shield-halved mr-1"></i> Bảo mật & mật khẩu
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
                
                <!-- Update Password Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-[#1E3F20] font-serif mb-2">Đổi mật khẩu</h2>
                        <p class="text-sm text-gray-500">Đảm bảo tài khoản của bạn sử dụng mật khẩu dài, ngẫu nhiên để an toàn.</p>
                    </div>

                    @if (session('status') === 'password-updated')
                        <div class="mb-6 p-4 bg-green-50 text-green-700 text-sm rounded-xl font-medium flex items-center gap-2 border border-green-100">
                            <i class="fa-solid fa-check-circle"></i> Đổi mật khẩu thành công!
                        </div>
                    @endif

                    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                        @csrf
                        @method('put')

                        <div class="max-w-xl space-y-6">
                            <div>
                                <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fa-solid fa-key mr-1 text-gray-400"></i> Mật khẩu hiện tại
                                </label>
                                <div class="relative" x-data="{ show: false }">
                                    <input id="current_password" name="current_password" :type="show ? 'text' : 'password'" required 
                                           class="w-full px-4 py-3 pr-12 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#D4AF37] focus:border-transparent outline-none transition-all" />
                                    <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                        <i class="fa-regular" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                @error('current_password', 'updatePassword') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fa-solid fa-lock mr-1 text-gray-400"></i> Mật khẩu mới
                                </label>
                                <div class="relative" x-data="{ show: false }">
                                    <input id="password" name="password" :type="show ? 'text' : 'password'" required 
                                           class="w-full px-4 py-3 pr-12 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#D4AF37] focus:border-transparent outline-none transition-all" />
                                    <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                        <i class="fa-regular" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                @error('password', 'updatePassword') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fa-solid fa-shield-halved mr-1 text-gray-400"></i> Xác nhận mật khẩu mới
                                </label>
                                <div class="relative" x-data="{ show: false }">
                                    <input id="password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" required 
                                           class="w-full px-4 py-3 pr-12 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#D4AF37] focus:border-transparent outline-none transition-all" />
                                    <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                        <i class="fa-regular" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end">
                            <button type="submit" class="bg-gray-900 hover:bg-black text-white font-semibold py-3 px-8 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5">
                                <i class="fa-solid fa-lock mr-2"></i> Cập nhật mật khẩu
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Security Tips Card -->
                <div class="bg-gradient-to-br from-[#1E3F20] to-[#2A5A2E] rounded-2xl shadow-md p-6 md:p-8 text-white">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-lightbulb text-xl text-[#D4AF37]"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg mb-3">Mẹo bảo mật tài khoản</h3>
                            <ul class="space-y-2 text-sm text-white/80">
                                <li class="flex items-start gap-2">
                                    <i class="fa-solid fa-check text-[#D4AF37] mt-0.5 flex-shrink-0"></i>
                                    <span>Sử dụng mật khẩu ít nhất 8 ký tự, kết hợp chữ hoa, chữ thường, số và ký tự đặc biệt.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fa-solid fa-check text-[#D4AF37] mt-0.5 flex-shrink-0"></i>
                                    <span>Không chia sẻ mật khẩu với bất kỳ ai và không sử dụng lại mật khẩu cũ.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fa-solid fa-check text-[#D4AF37] mt-0.5 flex-shrink-0"></i>
                                    <span>Đổi mật khẩu định kỳ mỗi 3-6 tháng để tăng cường an toàn.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
