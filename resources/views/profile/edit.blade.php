@extends('layouts.client')

@section('title', 'Thông tin cá nhân - Rẻo Cao Journeys')

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
                    {{ $user->name }}
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
                
                <!-- Avatar Upload Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-[#1E3F20] font-serif mb-2">Ảnh đại diện</h2>
                        <p class="text-sm text-gray-500">Tải lên ảnh đại diện mới của bạn. Định dạng: JPG, PNG, WEBP (tối đa 2MB).</p>
                    </div>

                    @if (session('status') === 'avatar-updated')
                        <div class="mb-6 p-4 bg-green-50 text-green-700 text-sm rounded-xl font-medium flex items-center gap-2 border border-green-100">
                            <i class="fa-solid fa-check-circle"></i> Cập nhật ảnh đại diện thành công!
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-center gap-6">
                        @csrf
                        <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-dashed border-gray-300 flex-shrink-0 relative group">
                            <img id="avatar-preview" src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="w-full h-full object-cover" />
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-full cursor-pointer"
                                 onclick="document.getElementById('avatar-input').click()">
                                <i class="fa-solid fa-camera text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="flex-1 space-y-3">
                            <input type="file" id="avatar-input" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden"
                                   onchange="previewAvatar(this)">
                            <div class="flex gap-3">
                                <button type="button" onclick="document.getElementById('avatar-input').click()" 
                                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors text-sm">
                                    <i class="fa-solid fa-upload"></i> Chọn ảnh
                                </button>
                                <button type="submit" 
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1E3F20] hover:bg-[#2A5A2E] text-white font-semibold rounded-xl shadow-sm transition-all text-sm">
                                    <i class="fa-solid fa-check"></i> Lưu ảnh
                                </button>
                            </div>
                            @error('avatar') <span class="text-xs text-red-500 block">{{ $message }}</span> @enderror
                        </div>
                    </form>
                </div>

                <!-- Profile Information Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-[#1E3F20] font-serif mb-2">Hồ sơ cá nhân</h2>
                        <p class="text-sm text-gray-500">Cập nhật thông tin tài khoản của bạn.</p>
                    </div>

                    @if (session('status') === 'profile-updated')
                        <div class="mb-6 p-4 bg-green-50 text-green-700 text-sm rounded-xl font-medium flex items-center gap-2 border border-green-100">
                            <i class="fa-solid fa-check-circle"></i> Cập nhật thông tin thành công!
                        </div>
                    @endif

                    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                        @csrf
                        @method('patch')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Họ và tên -->
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fa-regular fa-user mr-1 text-gray-400"></i> Họ và tên
                                </label>
                                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required 
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#D4AF37] focus:border-transparent outline-none transition-all" />
                                @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Email (readonly) -->
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fa-regular fa-envelope mr-1 text-gray-400"></i> Email
                                    <span class="text-xs text-gray-400 font-normal ml-1">(không thể thay đổi)</span>
                                </label>
                                <div class="relative">
                                    <input id="email" type="email" value="{{ $user->email }}" disabled readonly
                                           class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-gray-500 cursor-not-allowed" />
                                    <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                        <i class="fa-solid fa-lock text-gray-400 text-sm"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Số điện thoại -->
                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fa-solid fa-phone mr-1 text-gray-400"></i> Số điện thoại
                                </label>
                                <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" 
                                       placeholder="VD: 0912 345 678"
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#D4AF37] focus:border-transparent outline-none transition-all" />
                                @error('phone') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Ngày sinh -->
                            <div>
                                <label for="birthday" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fa-solid fa-cake-candles mr-1 text-gray-400"></i> Ngày sinh
                                </label>
                                <input id="birthday" name="birthday" type="date" 
                                       value="{{ old('birthday', $user->birthday ? $user->birthday->format('Y-m-d') : '') }}" 
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#D4AF37] focus:border-transparent outline-none transition-all" />
                                @error('birthday') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Địa chỉ -->
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fa-solid fa-location-dot mr-1 text-gray-400"></i> Địa chỉ
                                </label>
                                <input id="address" name="address" type="text" value="{{ old('address', $user->address) }}" 
                                       placeholder="VD: 123 Đường Láng, Đống Đa, Hà Nội"
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#D4AF37] focus:border-transparent outline-none transition-all" />
                                @error('address') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end">
                            <button type="submit" class="bg-[#1E3F20] hover:bg-[#2A5A2E] text-white font-semibold py-3 px-8 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5">
                                <i class="fa-solid fa-floppy-disk mr-2"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
