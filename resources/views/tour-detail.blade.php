@extends('layouts.client')

@section('title', $tour->name . ' - Rẻo Cao Journeys')

@section('content')
<div class="h-[75px] w-full bg-white"></div>

<!-- Custom styles for hide/show tabs -->
<style>
    [x-cloak] { display: none !important; }
</style>

<div class="bg-[#FAF9F6] min-h-screen pb-36 lg:pb-24">
    
    <!-- Image Gallery Section -->
    @php
        $imageUrls = $tour->images ? $tour->images->map(fn ($img) => $img->url())->toJson() : '[]';
    @endphp
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 mb-12" x-data="{ 
        showGallery: false, 
        activeIndex: 0, 
        images: {{ $imageUrls }},
        next() { this.activeIndex = (this.activeIndex + 1) % this.images.length; },
        prev() { this.activeIndex = (this.activeIndex - 1 + this.images.length) % this.images.length; }
    }">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 h-[60vh] min-h-[400px] max-h-[600px] rounded-3xl overflow-hidden relative">
            @if($tour->images && $tour->images->count() > 0)
                <!-- Main Image -->
                <div class="{{ $tour->images->count() > 1 ? 'md:col-span-3' : 'md:col-span-4' }} relative h-full group cursor-pointer overflow-hidden" @click="showGallery = true; activeIndex = 0">
                    <img src="{{ $tour->images->first()->url() }}" 
                         alt="{{ $tour->name }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
                    <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors duration-300"></div>
                </div>
                <!-- Sub Images -->
                @if($tour->images->count() > 1)
                    <div class="hidden md:grid grid-rows-2 gap-4 h-full">
                        @foreach($tour->images->skip(1)->take(2) as $index => $image)
                            <div class="relative h-full group cursor-pointer overflow-hidden {{ $index === 0 ? 'rounded-tr-3xl' : 'rounded-br-3xl' }}" @click="showGallery = true; activeIndex = {{ $index + 1 }}">
                                <img src="{{ $image->url() }}" alt="View {{ $index + 2 }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                                <div class="absolute inset-0 bg-black/20 group-hover:bg-transparent transition-colors duration-300"></div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- View All Photos Button -->
                @if($tour->images->count() > 1)
                    <button @click="showGallery = true; activeIndex = 0" class="absolute bottom-5 right-5 z-10 bg-white/95 backdrop-blur-sm hover:bg-white text-gray-800 font-semibold px-5 py-2.5 rounded-xl shadow-lg transition-all duration-200 hover:shadow-xl hover:-translate-y-0.5 flex items-center gap-2 text-sm border border-gray-200">
                        <i class="fa-solid fa-images"></i> Xem tất cả {{ $tour->images->count() }} ảnh
                    </button>
                @endif
            @else
                <!-- Fallback Image -->
                <div class="md:col-span-4 relative h-full group cursor-pointer overflow-hidden bg-gray-200 flex items-center justify-center">
                    <i class="fa-solid fa-image text-6xl text-gray-400"></i>
                </div>
            @endif
        </div>

        <!-- Fullscreen Gallery Modal -->
        <div x-show="showGallery" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 z-[9998] bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 md:p-8" @keydown.escape.window="showGallery = false" @keydown.arrow-right.window="next()" @keydown.arrow-left.window="prev()" x-cloak>
            
            <div class="bg-[#F8F9FA] rounded-2xl w-full max-w-6xl h-full max-h-[90vh] flex flex-col relative overflow-hidden shadow-2xl" @click.stop>
                
                <!-- Close Button -->
                <button @click="showGallery = false" class="absolute top-4 right-4 z-50 w-10 h-10 rounded-full bg-gray-800/50 hover:bg-gray-800 text-white flex items-center justify-center transition-colors shadow-lg">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>

                <!-- Main Image Area -->
                <div class="flex-1 relative flex items-center justify-center p-8 bg-gray-100">
                    <!-- Prev Button -->
                    <button @click="prev()" class="absolute left-4 z-20 w-12 h-12 rounded-full bg-white shadow-lg text-gray-800 hover:bg-gray-100 flex items-center justify-center transition-transform hover:scale-105">
                        <i class="fa-solid fa-chevron-left text-xl"></i>
                    </button>

                    <img :src="images[activeIndex]" class="max-w-full max-h-full object-contain rounded-xl shadow-md transition-opacity duration-300" alt="Tour image" x-transition />

                    <!-- Next Button -->
                    <button @click="next()" class="absolute right-4 z-20 w-12 h-12 rounded-full bg-white shadow-lg text-gray-800 hover:bg-gray-100 flex items-center justify-center transition-transform hover:scale-105">
                        <i class="fa-solid fa-chevron-right text-xl"></i>
                    </button>
                </div>

                <!-- Thumbnails Area -->
                <div class="h-32 bg-white border-t border-gray-200 px-6 py-4 flex flex-col">
                    <div class="text-[#0052CC] font-bold text-sm mb-3">Tất cả ảnh (<span x-text="images.length"></span>)</div>
                    <div class="flex-1 overflow-x-auto no-scrollbar flex gap-3 items-center">
                        <template x-for="(img, index) in images" :key="index">
                            <button @click="activeIndex = index" 
                                    class="relative h-16 min-w-[100px] flex-shrink-0 rounded-lg overflow-hidden transition-all duration-200 border-2"
                                    :class="activeIndex === index ? 'border-[#0052CC] scale-105 shadow-md' : 'border-transparent hover:border-gray-300 opacity-70 hover:opacity-100'">
                                <img :src="img" class="w-full h-full object-cover" />
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-12 relative">
            
            <!-- Main Content (Left) -->
            <div class="lg:w-2/3">
                
                <!-- Tour Header Info -->
                <div class="mb-10 border-b border-gray-200 pb-8">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-3 py-1 bg-[#1E3F20]/10 text-[#1E3F20] text-xs font-bold uppercase tracking-wider rounded-full">
                            Trải Nghiệm Bản Địa
                        </span>
                        <span class="px-3 py-1 bg-[#D4AF37]/10 text-[#b89528] text-xs font-bold uppercase tracking-wider rounded-full flex items-center">
                            <i class="fa-solid fa-circle-check mr-1"></i> Tour đã được duyệt
                        </span>
                    </div>
                    <div class="flex items-start justify-between mb-6">
                        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 font-serif leading-tight pr-4">
                            {{ $tour->name }}
                        </h1>
                        
                        @auth
                            <form action="{{ route('profile.favorites.toggle', $tour->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-12 h-12 flex items-center justify-center rounded-full border-2 {{ $isFavorite ? 'border-red-500 bg-red-50' : 'border-gray-200 bg-white hover:border-red-300 hover:bg-red-50' }} transition-colors shadow-sm shrink-0" title="{{ $isFavorite ? 'Bỏ yêu thích' : 'Thêm vào yêu thích' }}">
                                    <i class="{{ $isFavorite ? 'fa-solid' : 'fa-regular' }} fa-heart text-xl {{ $isFavorite ? 'text-red-500' : 'text-gray-400' }}"></i>
                                </button>
                            </form>
                        @endauth
                    </div>
                    <div class="flex flex-wrap items-center gap-6 text-sm font-medium text-gray-600">
                        <div class="flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-full shadow-sm border border-gray-100">
                            <i class="fa-solid fa-star text-[#D4AF37]"></i>
                            @if($tour->reviews_count)
                                <span class="text-gray-900 font-bold">{{ number_format($tour->reviews_avg_rating, 1) }}</span>
                                <span class="text-gray-400">({{ $tour->reviews_count }} đánh giá)</span>
                            @else
                                <span class="text-gray-500">Chưa có đánh giá</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-full shadow-sm border border-gray-100">
                            <i class="fa-solid fa-location-dot text-red-500"></i>
                            <span>{{ $tour->location ?? 'Chưa cập nhật' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Host Profile -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-start gap-5 mb-10 transform hover:-translate-y-1 transition-transform duration-300">
                    <div>
                        <img src="{{ $tour->host->avatarUrl(96) }}" 
                             alt="{{ $tour->host->name }}" class="w-16 h-16 rounded-full object-cover border-2 border-[#D4AF37] p-0.5" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Được dẫn dắt bởi {{ $tour->host->name }}</h3>
                        <p class="text-xs font-bold uppercase tracking-wider text-[#D4AF37] mb-2 flex items-center">
                            <i class="fa-solid fa-shield-halved mr-1"></i> Host đã được phê duyệt
                        </p>
                        <p class="text-sm leading-relaxed text-gray-600">
                            Chào mừng bạn đến với bản làng của chúng tôi! Tôi sinh ra và lớn lên ở vùng đất này, rất hân hạnh được đồng hành và chia sẻ những câu chuyện văn hóa nguyên bản nhất cùng bạn.
                        </p>
                    </div>
                    <a href="{{ route('messages.show', $tour->host) }}" class="shrink-0 w-12 h-12 flex items-center justify-center bg-gray-50 border border-gray-200 rounded-full hover:bg-[#1E3F20] hover:text-white hover:border-[#1E3F20] transition-colors text-gray-600 shadow-sm" title="Nhắn tin cho Host">
                        <i class="fa-regular fa-comment-dots text-lg"></i>
                    </a>
                </div>

                <!-- Description -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold mb-6 font-serif text-gray-900 flex items-center gap-2">
                        <i class="fa-solid fa-leaf text-[#1E3F20]"></i> Về chuyến đi này
                    </h2>
                    <p class="leading-relaxed text-gray-600 text-lg text-justify font-serif">
                        {{ $tour->description }}
                    </p>
                </div>

                <!-- Alpine.js Tabs for JSON Data -->
                <div x-data="{ activeTab: 'itinerary' }" class="mb-12 bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    
                    <!-- Tab Headers -->
                    <div class="flex border-b border-gray-200 bg-gray-50/50 overflow-x-auto no-scrollbar">
                        <button @click="activeTab = 'itinerary'" :class="{'border-[#1E3F20] text-[#1E3F20] bg-white': activeTab === 'itinerary', 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50': activeTab !== 'itinerary'}" class="flex-1 py-4 px-6 text-center border-b-2 font-semibold text-sm transition-colors whitespace-nowrap">
                            <i class="fa-regular fa-map mr-2"></i>Lịch trình
                        </button>
                        <button @click="activeTab = 'highlights'" :class="{'border-[#1E3F20] text-[#1E3F20] bg-white': activeTab === 'highlights', 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50': activeTab !== 'highlights'}" class="flex-1 py-4 px-6 text-center border-b-2 font-semibold text-sm transition-colors whitespace-nowrap">
                            <i class="fa-solid fa-star mr-2"></i>Điểm nổi bật
                        </button>
                        <button @click="activeTab = 'included'" :class="{'border-[#1E3F20] text-[#1E3F20] bg-white': activeTab === 'included', 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50': activeTab !== 'included'}" class="flex-1 py-4 px-6 text-center border-b-2 font-semibold text-sm transition-colors whitespace-nowrap">
                            <i class="fa-solid fa-box-open mr-2"></i>Bao gồm & Không
                        </button>
                    </div>

                    <!-- Tab Contents -->
                    <div class="p-8">
                        
                        <!-- Lịch trình Tab -->
                        <div x-show="activeTab === 'itinerary'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-cloak>
                            <div class="relative border-l-2 border-[#1E3F20]/20 ml-3 md:ml-4 space-y-10 py-2">
                                @php
                                    $itinerary = is_string($tour->itinerary) ? json_decode($tour->itinerary, true) : $tour->itinerary;
                                    $itinerary = $itinerary ?? [];
                                @endphp
                                
                                @foreach($itinerary as $item)
                                    <div class="relative pl-8 md:pl-10">
                                        <div class="absolute -left-3.5 top-0.5 bg-[#1E3F20] text-white w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold border-4 border-white shadow-sm z-10">
                                            {{ $item['day'] ?? $loop->iteration }}
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900 mb-2">Ngày {{ $item['day'] ?? $loop->iteration }}: {{ $item['title'] ?? 'Lịch trình' }}</h3>
                                        <p class="text-gray-600 leading-relaxed bg-gray-50 p-4 rounded-xl border border-gray-100">
                                            {{ $item['description'] ?? 'Chưa có thông tin chi tiết cho ngày này.' }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Điểm nổi bật Tab -->
                        <div x-show="activeTab === 'highlights'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-cloak>
                            @php
                                $highlights = is_string($tour->highlights) ? json_decode($tour->highlights, true) : $tour->highlights;
                                $highlights = $highlights ?? [];
                            @endphp
                            <ul class="grid md:grid-cols-2 gap-4">
                                @foreach($highlights as $highlight)
                                    <li class="flex items-start gap-3 bg-gray-50 p-4 rounded-xl border border-gray-100">
                                        <div class="mt-0.5 text-[#D4AF37]">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <span class="text-gray-700 leading-relaxed">{{ is_array($highlight) ? ($highlight['title'] ?? '') : $highlight }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Bao gồm Tab -->
                        <div x-show="activeTab === 'included'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-cloak>
                            @php
                                $includedRaw = is_string($tour->included) ? json_decode($tour->included, true) : $tour->included;
                                $includedRaw = $includedRaw ?? [];
                                $includes = $includedRaw['includes'] ?? [];
                                $excludes = $includedRaw['excludes'] ?? [];
                            @endphp
                            
                            <div class="grid md:grid-cols-2 gap-8">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                        <i class="fa-solid fa-circle-check text-green-500"></i> Bao gồm
                                    </h3>
                                    <ul class="space-y-3">
                                        @foreach($includes as $inc)
                                            <li class="flex items-start gap-3 text-gray-600 text-sm">
                                                <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                {{ $inc }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                        <i class="fa-solid fa-circle-xmark text-red-500"></i> Không bao gồm
                                    </h3>
                                    <ul class="space-y-3">
                                        @foreach($excludes as $exc)
                                            <li class="flex items-start gap-3 text-gray-600 text-sm">
                                                <svg class="w-5 h-5 text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                {{ $exc }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <section class="mb-12" id="reviews">
                    <div class="mb-6 flex items-end justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-bold font-serif text-gray-900">Đánh giá từ du khách</h2>
                            <p class="mt-1 text-sm text-gray-500">Chỉ hiển thị đánh giá từ khách đã hoàn thành tour.</p>
                        </div>
                        @if($tour->reviews_count)
                            <div class="whitespace-nowrap text-sm font-bold text-gray-800">
                                <i class="fa-solid fa-star mr-1 text-[#D4AF37]"></i>{{ number_format($tour->reviews_avg_rating, 1) }}/5
                            </div>
                        @endif
                    </div>

                    @if(session('success'))
                        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    @auth
                        @if($canReview)
                            <form action="{{ route('tours.reviews.store', $tour) }}" method="POST" class="mb-6 rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                                @csrf
                                <h3 class="mb-4 font-bold text-gray-900">{{ $userReview ? 'Cập nhật đánh giá của bạn' : 'Viết đánh giá' }}</h3>
                                <div class="grid gap-4 md:grid-cols-[180px_1fr]">
                                    <div>
                                        <label for="rating" class="mb-2 block text-sm font-medium text-gray-700">Số sao</label>
                                        <select id="rating" name="rating" class="w-full rounded-lg border-gray-300 focus:border-[#1E3F20] focus:ring-[#1E3F20]" required>
                                            @for($rating = 5; $rating >= 1; $rating--)
                                                <option value="{{ $rating }}" @selected((int) old('rating', $userReview?->rating ?? 5) === $rating)>{{ $rating }} sao</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div>
                                        <label for="comment" class="mb-2 block text-sm font-medium text-gray-700">Nhận xét</label>
                                        <textarea id="comment" name="comment" rows="3" maxlength="2000" class="w-full rounded-lg border-gray-300 focus:border-[#1E3F20] focus:ring-[#1E3F20]" placeholder="Chia sẻ trải nghiệm thực tế của bạn">{{ old('comment', $userReview?->comment) }}</textarea>
                                    </div>
                                </div>
                                @error('rating')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                                @error('comment')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                                <button type="submit" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-[#1E3F20] px-5 py-2.5 text-sm font-bold text-white hover:bg-[#2A5A2E]">
                                    <i class="fa-solid fa-paper-plane"></i> Lưu đánh giá
                                </button>
                            </form>
                        @endif
                    @endauth

                    <div class="space-y-4">
                        @forelse($tour->reviews->sortByDesc('created_at') as $review)
                            <article class="rounded-lg border border-gray-200 bg-white p-5">
                                <div class="mb-3 flex items-center gap-3">
                                    <img src="{{ $review->user->avatarUrl(48) }}" alt="{{ $review->user->name }}" class="h-10 w-10 rounded-full object-cover">
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate font-bold text-gray-900">{{ $review->user->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $review->created_at?->format('d/m/Y') }}</p>
                                    </div>
                                    <span class="text-sm font-bold text-gray-800"><i class="fa-solid fa-star mr-1 text-[#D4AF37]"></i>{{ $review->rating }}/5</span>
                                </div>
                                @if($review->comment)
                                    <p class="leading-relaxed text-gray-600">{{ $review->comment }}</p>
                                @endif
                            </article>
                        @empty
                            <div class="rounded-lg border border-dashed border-gray-300 bg-white px-5 py-8 text-center text-sm text-gray-500">Chưa có đánh giá cho tour này.</div>
                        @endforelse
                    </div>
                </section>

            </div>

            <!-- Sticky Checkout Sidebar (Right) -->
            <div class="lg:w-1/3">
                <div class="sticky top-24 bg-white rounded-3xl shadow-xl border border-gray-100 p-8 transform transition-transform duration-300 hover:shadow-2xl z-20">
                    
                    <div class="flex items-end gap-2 mb-6 pb-6 border-b border-gray-100">
                        <span class="text-4xl font-bold text-[#1E3F20] font-serif tracking-tight">
                            {{ number_format($tour->price, 0, ',', '.') }}<span class="text-2xl">₫</span>
                        </span>
                        <span class="text-gray-500 font-medium mb-1">/ người</span>
                    </div>

                    <!-- Tour quick info -->
                    <div class="bg-gray-50 rounded-2xl p-4 mb-8 space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2 text-gray-600">
                                <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-gray-400 shadow-sm">
                                    <i class="fa-solid fa-clock"></i>
                                </div>
                                <span class="font-medium">Thời lượng</span>
                            </div>
                            <span class="font-bold text-gray-900">{{ $tour->duration_days ?? '?' }} ngày {{ $tour->duration_nights ?? '?' }} đêm</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2 text-gray-600">
                                <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-gray-400 shadow-sm">
                                    <i class="fa-solid fa-user-group"></i>
                                </div>
                                <span class="font-medium">Sức chứa</span>
                            </div>
                            <span class="font-bold text-gray-900">Tối đa {{ $tour->capacity }} khách</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <a href="{{ route('booking.checkout', $tour->slug) }}" class="block w-full text-center py-4 px-6 bg-gradient-to-r from-[#D4AF37] to-[#e5c158] text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:from-[#c29f2f] hover:to-[#d4af37] transition-all duration-300 transform hover:-translate-y-0.5 text-lg">
                            Đặt Tour Ngay
                        </a>
                        <p class="text-center text-sm text-gray-400 flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-shield-halved"></i> Thanh toán an toàn, không tính phí bây giờ
                        </p>
                    </div>

                    <div class="mt-6">
                        <div class="flex justify-between items-center text-sm text-gray-600 mb-2">
                            <span class="underline decoration-dotted cursor-help" title="Giá tour cơ bản cho 1 người lớn">Chi phí (1 khách)</span>
                            <span class="font-medium">{{ number_format($tour->price, 0, ',', '.') }} ₫</span>
                        </div>
                        <div class="flex justify-between items-center text-sm text-gray-600 mb-4">
                            <span class="underline decoration-dotted cursor-help" title="Phí dịch vụ nền tảng">Phí dịch vụ</span>
                            <span class="text-green-600 font-medium">Miễn phí</span>
                        </div>
                        
                        <div class="flex justify-between items-end text-gray-900 font-bold pt-4 border-t border-gray-100">
                            <span class="text-lg">Tổng cộng</span>
                            <span class="text-2xl text-[#1E3F20]">{{ number_format($tour->price, 0, ',', '.') }} ₫</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>
</div>

<!-- Mobile booking bar -->
<div class="fixed inset-x-0 bottom-0 z-40 border-t border-gray-200 bg-white/95 backdrop-blur px-4 py-3 shadow-[0_-8px_24px_rgba(0,0,0,0.08)] lg:hidden">
    <div class="flex items-center justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs text-gray-500">Chỉ từ</p>
            <p class="text-lg font-bold text-[#1E3F20] whitespace-nowrap">{{ number_format($tour->price, 0, ',', '.') }} ₫</p>
        </div>
        <a href="{{ route('booking.checkout', $tour->slug) }}" class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-[#D4AF37] px-5 py-3 text-sm font-bold text-white shadow-md transition-colors hover:bg-[#c29f2f]">
            Đặt Tour Ngay
            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>
</div>
@endsection
