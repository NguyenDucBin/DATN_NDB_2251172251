<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('host.tours.index') }}" class="mr-4 text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="text-xl font-semibold text-gray-800">Chỉnh sửa Tour: {{ $tour->name }}</h2>
        </div>
    </x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-4xl mx-auto">
        <form action="{{ route('host.tours.update', $tour->id) }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8">
            @csrf
            @method('PUT')

            <!-- Section 1: Basic Info -->
            <div class="mb-8 border-b border-gray-200 pb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Thông tin cơ bản</h3>
                
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="col-span-1">
                        <label for="name" class="block text-sm font-medium text-gray-700">Tên Tour <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $tour->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required>
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="col-span-1">
                        <label for="location" class="block text-sm font-medium text-gray-700">Địa điểm / Khu vực <span class="text-red-500">*</span></label>
                        <input type="text" name="location" id="location" value="{{ old('location', $tour->location) }}" placeholder="VD: Hà Giang, Sapa..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required>
                        @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Mô tả tổng quan <span class="text-red-500">*</span></label>
                        <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required>{{ old('description', $tour->description) }}</textarea>
                        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">Giá tour (VNĐ) <span class="text-red-500">*</span></label>
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <input type="number" name="price" id="price" value="{{ old('price', round($tour->price)) }}" min="0" step="1000" class="block w-full rounded-md border-gray-300 pl-4 pr-12 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">VND</span>
                            </div>
                        </div>
                        @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="duration_days" class="block text-sm font-medium text-gray-700">Số ngày <span class="text-red-500">*</span></label>
                        <input type="number" name="duration_days" id="duration_days" value="{{ old('duration_days', $tour->duration_days) }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required>
                        @error('duration_days') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="duration_nights" class="block text-sm font-medium text-gray-700">Số đêm <span class="text-red-500">*</span></label>
                        <input type="number" name="duration_nights" id="duration_nights" value="{{ old('duration_nights', $tour->duration_nights) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required>
                        @error('duration_nights') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="capacity" class="block text-sm font-medium text-gray-700">Sức chứa tối đa (Khách) <span class="text-red-500">*</span></label>
                        <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $tour->capacity) }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required>
                        @error('capacity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label for="is_active" class="block text-sm font-medium text-gray-700 mb-2">Trạng thái mở bán</label>
                        <select name="is_active" id="is_active" @if($tour->status !== 'approved') disabled @endif class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm rounded-md disabled:bg-gray-100 disabled:text-gray-500">
                            <option value="1" {{ old('is_active', $tour->is_active) == 1 ? 'selected' : '' }}>Mở bán</option>
                            <option value="0" {{ old('is_active', $tour->is_active) == 0 ? 'selected' : '' }}>Tạm dừng (Đóng)</option>
                        </select>
                        @if($tour->status !== 'approved')
                            <p class="mt-2 text-xs text-gray-500">Tour cần được admin duyệt trước khi mở bán.</p>
                        @endif
                    </div>

                    <div class="col-span-2" x-data="tourImageManager(@json(old('deleted_images', [])))">
                        <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh Tour (Chọn để thêm ảnh mới)</label>
                        @if($tour->images && $tour->images->count() > 0)
                            <div class="flex flex-wrap gap-4 mb-4">
                                @foreach($tour->images as $image)
                                    <div x-show="!deletedImages.includes({{ $image->id }})"
                                         x-cloak
                                         class="relative h-24 w-24 overflow-hidden rounded-lg border border-gray-200 bg-gray-100">
                                        <img src="{{ $image->url() }}" alt="Ảnh tour" loading="lazy" class="h-full w-full object-cover">
                                        <button type="button"
                                                @click="markExistingImageForDeletion({{ $image->id }})"
                                                title="Xóa ảnh"
                                                aria-label="Xóa ảnh này"
                                                class="absolute right-1.5 top-1.5 inline-flex h-7 w-7 items-center justify-center rounded-full bg-red-600 text-white shadow-md transition-colors hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                            <i class="fa-solid fa-xmark text-sm"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <template x-for="imageId in deletedImages" :key="imageId">
                            <input type="hidden" name="deleted_images[]" :value="imageId">
                        </template>

                        <input x-ref="imageInput" @change="addFiles($event)" type="file" name="images[]" id="images" multiple accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">

                        <div x-show="newImages.length" x-cloak class="mt-4">
                            <p class="mb-2 text-xs font-medium text-gray-500">Ảnh mới đã chọn</p>
                            <div class="flex flex-wrap gap-4">
                                <template x-for="(image, index) in newImages" :key="image.key">
                                    <div class="relative h-24 w-24 overflow-hidden rounded-lg border border-emerald-200 bg-gray-100">
                                        <img :src="image.url" :alt="image.file.name" class="h-full w-full object-cover">
                                        <button type="button"
                                                @click="removeNewImage(index)"
                                                title="Bỏ ảnh đã chọn"
                                                aria-label="Bỏ ảnh đã chọn"
                                                class="absolute right-1.5 top-1.5 inline-flex h-7 w-7 items-center justify-center rounded-full bg-red-600 text-white shadow-md transition-colors hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                            <i class="fa-solid fa-xmark text-sm"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        @error('images') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        @error('images.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        @error('deleted_images.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Itinerary (AlpineJS) -->
            <div class="mb-8 border-b border-gray-200 pb-8" x-data="{
                days: {{ json_encode(old('itinerary', is_array($tour->itinerary) ? $tour->itinerary : [])) }} || [ { title: '', description: '' } ],
                addDay() {
                    this.days.push({ title: '', description: '' });
                },
                removeDay(index) {
                    this.days.splice(index, 1);
                }
            }">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Lịch trình chi tiết <span class="text-red-500">*</span></h3>
                    <button type="button" @click="addDay" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-emerald-700 bg-emerald-100 hover:bg-emerald-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Thêm ngày
                    </button>
                </div>

                <template x-for="(day, index) in days" :key="index">
                    <div class="p-4 mb-4 bg-gray-50 border border-gray-200 rounded-lg relative">
                        <button type="button" @click="removeDay(index)" x-show="days.length > 1" class="absolute top-4 right-4 text-gray-400 hover:text-red-500">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                        
                        <div class="grid grid-cols-1 gap-4 pr-8">
                            <div>
                                <label :for="'itinerary_title_'+index" class="block text-sm font-medium text-gray-700">Tiêu đề ngày <span x-text="index + 1"></span></label>
                                <input type="text" x-model="day.title" :name="'itinerary['+index+'][title]'" :id="'itinerary_title_'+index" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required>
                            </div>
                            <div>
                                <label :for="'itinerary_desc_'+index" class="block text-sm font-medium text-gray-700">Nội dung hoạt động</label>
                                <textarea x-model="day.description" :name="'itinerary['+index+'][description]'" :id="'itinerary_desc_'+index" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required></textarea>
                            </div>
                        </div>
                    </div>
                </template>
                @error('itinerary') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Section 3: Highlights (AlpineJS) -->
            @php
                $highlightsData = is_array($tour->highlights) ? $tour->highlights : [];
                if (empty($highlightsData)) $highlightsData = [''];
            @endphp
            <div class="mb-8 border-b border-gray-200 pb-8" x-data="{
                items: {{ json_encode(old('highlights', $highlightsData)) }},
                addItem() { this.items.push(''); },
                removeItem(index) { this.items.splice(index, 1); }
            }">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Điểm nổi bật</h3>
                    <button type="button" @click="addItem" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-emerald-700 bg-emerald-100 hover:bg-emerald-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Thêm mục
                    </button>
                </div>
                <template x-for="(item, index) in items" :key="index">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-emerald-500 flex-shrink-0"><i class="fa-solid fa-check-circle"></i></span>
                        <input type="text" x-model="items[index]" :name="'highlights['+index+']'" placeholder="VD: Khám phá văn hóa bản địa" class="flex-1 block rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                        <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="text-gray-400 hover:text-red-500 flex-shrink-0">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </template>
                @error('highlights') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Section 4: Included / Excluded (AlpineJS) -->
            @php
                $includedData = is_array($tour->included) ? $tour->included : [];
                $includesData = $includedData['includes'] ?? [''];
                $excludesData = $includedData['excludes'] ?? [''];
                if (empty($includesData)) $includesData = [''];
                if (empty($excludesData)) $excludesData = [''];
            @endphp
            <div class="mb-8 border-b border-gray-200 pb-8" x-data="{
                includes: {{ json_encode(old('included.includes', $includesData)) }},
                excludes: {{ json_encode(old('included.excludes', $excludesData)) }},
                addInclude() { this.includes.push(''); },
                removeInclude(i) { this.includes.splice(i, 1); },
                addExclude() { this.excludes.push(''); },
                removeExclude(i) { this.excludes.splice(i, 1); }
            }">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Bao gồm & Không bao gồm</h3>

                <!-- Includes -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-semibold text-green-700 flex items-center gap-1"><i class="fa-solid fa-circle-check"></i> Bao gồm</h4>
                        <button type="button" @click="addInclude" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">+ Thêm</button>
                    </div>
                    <template x-for="(item, i) in includes" :key="'inc_'+i">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-green-400 flex-shrink-0 text-sm"><i class="fa-solid fa-plus"></i></span>
                            <input type="text" x-model="includes[i]" :name="'included[includes]['+i+']'" placeholder="VD: Ăn uống, chỗ ở" class="flex-1 block rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                            <button type="button" @click="removeInclude(i)" x-show="includes.length > 1" class="text-gray-400 hover:text-red-500 flex-shrink-0">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Excludes -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-semibold text-red-600 flex items-center gap-1"><i class="fa-solid fa-circle-xmark"></i> Không bao gồm</h4>
                        <button type="button" @click="addExclude" class="text-xs text-red-500 hover:text-red-700 font-medium">+ Thêm</button>
                    </div>
                    <template x-for="(item, i) in excludes" :key="'exc_'+i">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-red-400 flex-shrink-0 text-sm"><i class="fa-solid fa-minus"></i></span>
                            <input type="text" x-model="excludes[i]" :name="'included[excludes]['+i+']'" placeholder="VD: Chi phí cá nhân" class="flex-1 block rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                            <button type="button" @click="removeExclude(i)" x-show="excludes.length > 1" class="text-gray-400 hover:text-red-500 flex-shrink-0">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </template>
                </div>
                @error('included') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Submit -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('host.tours.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                    Hủy bỏ
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                    Cập nhật Tour
                </button>
            </div>
        </form>
    </div>
</x-dashboard-layout>
