<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('admin.coupons.index') }}" class="mr-4 text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="text-xl font-semibold text-gray-800">Tạo Mã Giảm Giá</h2>
        </div>
    </x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-3xl mx-auto">
        <form action="{{ route('admin.coupons.store') }}" method="POST" class="p-6 md:p-8">
            @csrf

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="col-span-2 md:col-span-1">
                    <label for="code" class="block text-sm font-medium text-gray-700">Mã Code <span class="text-red-500">*</span></label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" class="mt-1 block w-full uppercase rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="VD: SUMMER2026" required>
                    @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label for="is_active" class="block text-sm font-medium text-gray-700">Trạng thái</label>
                    <select name="is_active" id="is_active" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                        <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Kích hoạt</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Vô hiệu hóa</option>
                    </select>
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label for="discount_type" class="block text-sm font-medium text-gray-700">Loại giảm giá <span class="text-red-500">*</span></label>
                    <select name="discount_type" id="discount_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                        <option value="percent" {{ old('discount_type') == 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                        <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Số tiền cố định (VNĐ)</option>
                    </select>
                    @error('discount_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label for="discount_amount" class="block text-sm font-medium text-gray-700">Mức giảm <span class="text-red-500">*</span></label>
                    <input type="number" name="discount_amount" id="discount_amount" value="{{ old('discount_amount') }}" min="0" step="any" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required>
                    @error('discount_amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label for="usage_limit" class="block text-sm font-medium text-gray-700">Giới hạn số lần sử dụng (Bỏ trống = không giới hạn)</label>
                    <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit') }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                    @error('usage_limit') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="col-span-2 md:col-span-1"></div> <!-- Empty column -->

                <div class="col-span-2 md:col-span-1">
                    <label for="valid_from" class="block text-sm font-medium text-gray-700">Ngày bắt đầu hiệu lực</label>
                    <input type="datetime-local" name="valid_from" id="valid_from" value="{{ old('valid_from') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                    @error('valid_from') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label for="valid_until" class="block text-sm font-medium text-gray-700">Ngày hết hạn</label>
                    <input type="datetime-local" name="valid_until" id="valid_until" value="{{ old('valid_until') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                    @error('valid_until') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('admin.coupons.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                    Hủy bỏ
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                    Lưu mã giảm giá
                </button>
            </div>
        </form>
    </div>
</x-dashboard-layout>
