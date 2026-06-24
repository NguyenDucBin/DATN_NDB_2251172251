@extends('layouts.client')

@section('title', 'Rẻo Cao Journeys - Chạm vào nguyên bản | Khám phá Tây Bắc')

@push('styles')
    <link rel="preload" as="image" href="{{ asset('images/static/home-hero.jpg') }}" fetchpriority="high">
@endpush

@section('content')
    <!-- 1. Hero Banner -->
    <section class="relative h-screen min-h-[680px] overflow-hidden flex items-center justify-center bg-cover bg-center" 
        style="background-image: url('{{ asset('images/static/home-hero.jpg') }}');">
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-black/70"></div>
        
        <div class="relative z-10 mx-auto mt-16 w-full min-w-0 max-w-5xl px-5 text-center sm:px-6">
            <span class="inline-block py-1 px-3 rounded-full bg-white/20 backdrop-blur-sm text-white text-xs sm:text-sm font-medium tracking-widest uppercase mb-5 sm:mb-6 animate-fade-in-up border border-white/30">
                Khám Phá Tây Bắc
            </span>
            <h1 class="text-3xl sm:text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-5 sm:mb-6 drop-shadow-lg leading-tight">
                <span class="block">Tây Bắc Gọi Mời</span>
                <span class="mt-1 block text-transparent bg-clip-text bg-gradient-to-r from-[#D4AF37] to-[#F3E5AB]">Chạm Vào Linh Hồn Của Đá Và Mây</span>
            </h1>
            <p class="mx-auto mb-8 max-w-3xl text-base leading-relaxed text-gray-200 drop-shadow-md sm:mb-10 md:text-xl">
                Một hành trình ngược lên non cao, nơi những cung đường đèo uốn lượn ôm trọn bản làng bình yên, nơi tiếng khèn réo rắt và nụ cười hồn nhiên sưởi ấm cả miền sương cước.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-12">
                <a href="#tours" class="group relative w-full max-w-[250px] sm:w-auto px-8 py-4 bg-[#1E3F20] text-white font-semibold rounded-full overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <span class="relative z-10 flex items-center">
                        Bắt Đầu Hành Trình
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-[#2A5A2E] to-[#1E3F20] opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <!-- Hoạ tiết dân tộc mờ -->
                    <div class="absolute inset-0 opacity-10" style="background-image: url('{{ asset('images/static/pattern-arabesque.png') }}')"></div>
                </a>
                
                <a href="{{ route('magazine.index') }}" class="w-full max-w-[250px] sm:w-auto px-8 py-4 bg-white/10 backdrop-blur-md text-white font-semibold rounded-full border border-white/30 hover:bg-white/20 transition-all duration-300">
                    Đọc Câu Chuyện
                </a>
            </div>


        </div>
        
        <!-- Scroll indicator -->
        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 flex flex-col items-center animate-bounce text-white/70">
            <span class="text-xs uppercase tracking-widest mb-2 font-medium">Khám phá</span>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
        </div>
    </section>

    <!-- 2. Câu Chuyện Vùng Đất -->
    <section class="py-24 bg-[#FAF9F6] relative overflow-hidden">
        <!-- Decoration element -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-[#1E3F20]/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-[#D4AF37]/5 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="order-2 lg:order-1 relative">
                    <div class="relative rounded-2xl overflow-hidden shadow-2xl aspect-[4/5] transform lg:-rotate-2 transition-transform duration-500 hover:rotate-0">
                        <img src="{{ asset('images/static/culture-highland.webp') }}" alt="Văn hóa vùng cao" loading="lazy" decoding="async" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <div class="absolute bottom-8 left-8 right-8">
                            <p class="text-white text-lg font-serif italic">"Đi là để trở về với những nguyên sơ nhất..."</p>
                        </div>
                    </div>
                    <!-- Decor image -->
                    <div class="absolute -bottom-10 -right-10 w-48 h-48 rounded-2xl overflow-hidden shadow-xl border-4 border-white hidden md:block transform rotate-6">
                        <img src="{{ asset('images/static/brocade.jpg') }}" alt="Thổ cẩm" loading="lazy" decoding="async" class="w-full h-full object-cover">
                    </div>
                </div>
                
                <div class="order-1 lg:order-2">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="h-px w-12 bg-[#D4AF37]"></div>
                        <span class="text-[#D4AF37] uppercase tracking-widest text-sm font-bold">Câu Chuyện Vùng Đất</span>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-8 leading-tight">Nhịp Đập Nơi Rẻo Cao</h2>
                    <div class="prose prose-lg text-gray-600 mb-10">
                        <p class="leading-relaxed">
                            Núi rừng phía Bắc không chỉ có cảnh sắc hùng vĩ mà còn là cái nôi ấp ủ những giá trị văn hóa ngàn đời của 54 dân tộc anh em. Từ nếp nhà sàn đơn sơ vương khói bếp, điệu múa xòe hoa uyển chuyển của cô gái Thái, đến tiếng chày giã gạo thâu đêm. 
                        </p>
                        <p class="leading-relaxed mt-4">
                            Đi là để trải nghiệm, để lắng nghe hơi thở của đại ngàn và tìm về với những giá trị Việt thuần khiết nhất.
                        </p>
                    </div>
                    
                    <a href="/magazine" class="inline-flex items-center text-[#1E3F20] font-semibold hover:text-[#2A5A2E] group pb-1 border-b-2 border-[#1E3F20] transition-colors">
                        Khám phá văn hóa bản địa
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 2.5 Khám Phá Điểm Đến -->
    <section id="destinations" class="py-20 bg-[#FAF9F6] border-y border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-12">
                <div class="max-w-2xl">
                    <span class="text-[#D4AF37] uppercase tracking-widest text-sm font-bold block mb-3">Vùng Đất Kỳ Thú</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Khám Phá Điểm Đến</h2>
                </div>
                <div class="hidden md:block">
                    <a href="{{ route('magazine.index') }}" class="text-[#1E3F20] font-semibold hover:text-[#D4AF37] transition-colors flex items-center gap-2 group">
                        Xem tất cả
                        <i class="fa-solid fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>

            <div x-data="{ activeIndex: 0 }" class="flex w-full h-[350px] md:h-[450px] overflow-hidden gap-2 rounded-2xl shadow-xl mt-4">
                
                <!-- Option 1: Sa Pa -->
                <div @click="activeIndex = 0"
                     class="relative flex flex-col justify-end overflow-hidden transition-all duration-700 ease-out cursor-pointer group"
                     :class="activeIndex === 0 ? 'flex-[6] md:flex-[5] shadow-2xl z-10 rounded-2xl' : 'flex-1 shadow-md opacity-80 hover:opacity-100 rounded-xl'">
                    <img src="{{ asset('images/static/destination-sa-pa.jpg') }}" alt="Sa Pa" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover">
                    
                    <div class="absolute inset-0 transition-opacity duration-700 bg-gradient-to-t from-black/90 via-black/30 to-transparent"
                         :class="activeIndex === 0 ? 'opacity-100' : 'opacity-60 group-hover:opacity-80'"></div>
                    
                    <div class="absolute left-0 right-0 bottom-4 md:bottom-6 flex items-center px-4 w-full"
                         :class="activeIndex === 0 ? '' : 'justify-center md:justify-start'">
                         
                         <!-- Icon -->
                         <div class="w-10 h-10 md:w-12 md:h-12 flex-shrink-0 rounded-full backdrop-blur-md flex items-center justify-center transition-all duration-700 border"
                              :class="activeIndex === 0 ? 'bg-[#D4AF37]/90 border-[#D4AF37] shadow-lg' : 'bg-white/20 border-white/30'">
                             <i class="fa-solid fa-cloud text-white text-lg md:text-xl"></i>
                         </div>
                         
                         <!-- Text -->
                         <div class="text-white whitespace-nowrap overflow-hidden transition-all duration-700 ml-4"
                              :class="activeIndex === 0 ? 'opacity-100 max-w-full' : 'opacity-0 max-w-0 !ml-0'">
                             <h3 class="font-bold text-xl md:text-2xl font-serif drop-shadow-md">Sa Pa</h3>
                             <p class="text-sm text-gray-200 hidden md:block">Thị trấn trong sương</p>
                         </div>
                         <a href="{{ route('destinations.show', 'sa-pa') }}" class="ml-auto hidden md:inline-flex items-center gap-2 px-4 py-2 bg-[#D4AF37] text-white text-sm font-semibold rounded-full hover:bg-[#c29f2f] transition-all shadow-lg"
                            :class="activeIndex === 0 ? 'opacity-100 scale-100' : 'opacity-0 scale-75 pointer-events-none'" style="transition: opacity 0.5s, transform 0.5s;">
                             Khám phá <i class="fa-solid fa-arrow-right text-xs"></i>
                         </a>
                    </div>
                </div>

                <!-- Option 2: Hà Giang -->
                <div @click="activeIndex = 1"
                     class="relative flex flex-col justify-end overflow-hidden transition-all duration-700 ease-out cursor-pointer group"
                     :class="activeIndex === 1 ? 'flex-[6] md:flex-[5] shadow-2xl z-10 rounded-2xl' : 'flex-1 shadow-md opacity-80 hover:opacity-100 rounded-xl'">
                    <img src="{{ asset('images/static/destination-ha-giang.jpg') }}" alt="Hà Giang" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover">
                    
                    <div class="absolute inset-0 transition-opacity duration-700 bg-gradient-to-t from-black/90 via-black/30 to-transparent"
                         :class="activeIndex === 1 ? 'opacity-100' : 'opacity-60 group-hover:opacity-80'"></div>
                    
                    <div class="absolute left-0 right-0 bottom-4 md:bottom-6 flex items-center px-4 w-full"
                         :class="activeIndex === 1 ? '' : 'justify-center md:justify-start'">
                         
                         <!-- Icon -->
                         <div class="w-10 h-10 md:w-12 md:h-12 flex-shrink-0 rounded-full backdrop-blur-md flex items-center justify-center transition-all duration-700 border"
                              :class="activeIndex === 1 ? 'bg-[#D4AF37]/90 border-[#D4AF37] shadow-lg' : 'bg-white/20 border-white/30'">
                             <i class="fa-solid fa-mountain text-white text-lg md:text-xl"></i>
                         </div>
                         
                         <!-- Text -->
                         <div class="text-white whitespace-nowrap overflow-hidden transition-all duration-700 ml-4"
                              :class="activeIndex === 1 ? 'opacity-100 max-w-full' : 'opacity-0 max-w-0 !ml-0'">
                             <h3 class="font-bold text-xl md:text-2xl font-serif drop-shadow-md">Hà Giang</h3>
                             <p class="text-sm text-gray-200 hidden md:block">Cao nguyên đá hùng vĩ</p>
                         </div>
                         <a href="{{ route('destinations.show', 'ha-giang') }}" class="ml-auto hidden md:inline-flex items-center gap-2 px-4 py-2 bg-[#D4AF37] text-white text-sm font-semibold rounded-full hover:bg-[#c29f2f] transition-all shadow-lg"
                            :class="activeIndex === 1 ? 'opacity-100 scale-100' : 'opacity-0 scale-75 pointer-events-none'" style="transition: opacity 0.5s, transform 0.5s;">
                             Khám phá <i class="fa-solid fa-arrow-right text-xs"></i>
                         </a>
                    </div>
                </div>

                <!-- Option 3: Mù Cang Chải -->
                <div @click="activeIndex = 2"
                     class="relative flex flex-col justify-end overflow-hidden transition-all duration-700 ease-out cursor-pointer group"
                     :class="activeIndex === 2 ? 'flex-[6] md:flex-[5] shadow-2xl z-10 rounded-2xl' : 'flex-1 shadow-md opacity-80 hover:opacity-100 rounded-xl'">
                    <img src="{{ asset('images/static/destination-mu-cang-chai.jpg') }}" alt="Mù Cang Chải" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover">
                    
                    <div class="absolute inset-0 transition-opacity duration-700 bg-gradient-to-t from-black/90 via-black/30 to-transparent"
                         :class="activeIndex === 2 ? 'opacity-100' : 'opacity-60 group-hover:opacity-80'"></div>
                    
                    <div class="absolute left-0 right-0 bottom-4 md:bottom-6 flex items-center px-4 w-full"
                         :class="activeIndex === 2 ? '' : 'justify-center md:justify-start'">
                         
                         <!-- Icon -->
                         <div class="w-10 h-10 md:w-12 md:h-12 flex-shrink-0 rounded-full backdrop-blur-md flex items-center justify-center transition-all duration-700 border"
                              :class="activeIndex === 2 ? 'bg-[#D4AF37]/90 border-[#D4AF37] shadow-lg' : 'bg-white/20 border-white/30'">
                             <i class="fa-solid fa-leaf text-white text-lg md:text-xl"></i>
                         </div>
                         
                         <!-- Text -->
                         <div class="text-white whitespace-nowrap overflow-hidden transition-all duration-700 ml-4"
                              :class="activeIndex === 2 ? 'opacity-100 max-w-full' : 'opacity-0 max-w-0 !ml-0'">
                             <h3 class="font-bold text-xl md:text-2xl font-serif drop-shadow-md">Mù Cang Chải</h3>
                             <p class="text-sm text-gray-200 hidden md:block">Mùa vàng non cao</p>
                         </div>
                         <a href="{{ route('destinations.show', 'mu-cang-chai') }}" class="ml-auto hidden md:inline-flex items-center gap-2 px-4 py-2 bg-[#D4AF37] text-white text-sm font-semibold rounded-full hover:bg-[#c29f2f] transition-all shadow-lg"
                            :class="activeIndex === 2 ? 'opacity-100 scale-100' : 'opacity-0 scale-75 pointer-events-none'" style="transition: opacity 0.5s, transform 0.5s;">
                             Khám phá <i class="fa-solid fa-arrow-right text-xs"></i>
                         </a>
                    </div>
                </div>

                <!-- Option 4: Cao Bằng -->
                <div @click="activeIndex = 3"
                     class="relative flex flex-col justify-end overflow-hidden transition-all duration-700 ease-out cursor-pointer group hidden sm:flex"
                     :class="activeIndex === 3 ? 'flex-[6] md:flex-[5] shadow-2xl z-10 rounded-2xl' : 'flex-1 shadow-md opacity-80 hover:opacity-100 rounded-xl'">
                    <img src="{{ asset('images/static/destination-cao-bang.webp') }}" alt="Cao Bằng" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover">
                    
                    <div class="absolute inset-0 transition-opacity duration-700 bg-gradient-to-t from-black/90 via-black/30 to-transparent"
                         :class="activeIndex === 3 ? 'opacity-100' : 'opacity-60 group-hover:opacity-80'"></div>
                    
                    <div class="absolute left-0 right-0 bottom-4 md:bottom-6 flex items-center px-4 w-full"
                         :class="activeIndex === 3 ? '' : 'justify-center md:justify-start'">
                         
                         <!-- Icon -->
                         <div class="w-10 h-10 md:w-12 md:h-12 flex-shrink-0 rounded-full backdrop-blur-md flex items-center justify-center transition-all duration-700 border"
                              :class="activeIndex === 3 ? 'bg-[#D4AF37]/90 border-[#D4AF37] shadow-lg' : 'bg-white/20 border-white/30'">
                             <i class="fa-solid fa-water text-white text-lg md:text-xl"></i>
                         </div>
                         
                         <!-- Text -->
                         <div class="text-white whitespace-nowrap overflow-hidden transition-all duration-700 ml-4"
                              :class="activeIndex === 3 ? 'opacity-100 max-w-full' : 'opacity-0 max-w-0 !ml-0'">
                             <h3 class="font-bold text-xl md:text-2xl font-serif drop-shadow-md">Cao Bằng</h3>
                             <p class="text-sm text-gray-200 hidden md:block">Tuyệt tác thác Bản Giốc</p>
                         </div>
                         <a href="{{ route('destinations.show', 'cao-bang') }}" class="ml-auto hidden md:inline-flex items-center gap-2 px-4 py-2 bg-[#D4AF37] text-white text-sm font-semibold rounded-full hover:bg-[#c29f2f] transition-all shadow-lg"
                            :class="activeIndex === 3 ? 'opacity-100 scale-100' : 'opacity-0 scale-75 pointer-events-none'" style="transition: opacity 0.5s, transform 0.5s;">
                             Khám phá <i class="fa-solid fa-arrow-right text-xs"></i>
                         </a>
                    </div>
                </div>

                <!-- Option 5: Mộc Châu -->
                <div @click="activeIndex = 4"
                     class="relative flex flex-col justify-end overflow-hidden transition-all duration-700 ease-out cursor-pointer group hidden md:flex"
                     :class="activeIndex === 4 ? 'flex-[6] md:flex-[5] shadow-2xl z-10 rounded-2xl' : 'flex-1 shadow-md opacity-80 hover:opacity-100 rounded-xl'">
                    <img src="{{ asset('images/static/destination-moc-chau.jpg') }}" alt="Mộc Châu" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover">
                    
                    <div class="absolute inset-0 transition-opacity duration-700 bg-gradient-to-t from-black/90 via-black/30 to-transparent"
                         :class="activeIndex === 4 ? 'opacity-100' : 'opacity-60 group-hover:opacity-80'"></div>
                    
                    <div class="absolute left-0 right-0 bottom-4 md:bottom-6 flex items-center px-4 w-full"
                         :class="activeIndex === 4 ? '' : 'justify-center md:justify-start'">
                         
                         <!-- Icon -->
                         <div class="w-10 h-10 md:w-12 md:h-12 flex-shrink-0 rounded-full backdrop-blur-md flex items-center justify-center transition-all duration-700 border"
                              :class="activeIndex === 4 ? 'bg-[#D4AF37]/90 border-[#D4AF37] shadow-lg' : 'bg-white/20 border-white/30'">
                             <i class="fa-solid fa-tree text-white text-lg md:text-xl"></i>
                         </div>
                         
                         <!-- Text -->
                         <div class="text-white whitespace-nowrap overflow-hidden transition-all duration-700 ml-4"
                              :class="activeIndex === 4 ? 'opacity-100 max-w-full' : 'opacity-0 max-w-0 !ml-0'">
                             <h3 class="font-bold text-xl md:text-2xl font-serif drop-shadow-md">Mộc Châu</h3>
                             <p class="text-sm text-gray-200 hidden md:block">Thảo nguyên vẫy gọi</p>
                         </div>
                         <a href="{{ route('destinations.show', 'moc-chau') }}" class="ml-auto hidden md:inline-flex items-center gap-2 px-4 py-2 bg-[#D4AF37] text-white text-sm font-semibold rounded-full hover:bg-[#c29f2f] transition-all shadow-lg"
                            :class="activeIndex === 4 ? 'opacity-100 scale-100' : 'opacity-0 scale-75 pointer-events-none'" style="transition: opacity 0.5s, transform 0.5s;">
                             Khám phá <i class="fa-solid fa-arrow-right text-xs"></i>
                         </a>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 text-center md:hidden">
                <a href="{{ route('magazine.index') }}" class="inline-block border-2 border-[#1E3F20] text-[#1E3F20] font-semibold py-2 px-6 rounded-full hover:bg-[#1E3F20] hover:text-white transition-colors">
                    Xem tất cả điểm đến
                </a>
            </div>
        </div>
    </section>

    <!-- 3. Trải Nghiệm Độc Bản (Tours) -->
    <section id="tours" class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-[#D4AF37] uppercase tracking-widest text-sm font-bold block mb-4">Gợi Ý Hành Trình</span>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">Trải Nghiệm Độc Bản</h2>
                <p class="text-gray-600 text-lg">Những cung đường được thiết kế tinh tế, đưa bạn len lỏi vào trái tim của đá và mây.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                @forelse($tours ?? [] as $index => $tour)
                    @php
                        $tourImage = ($tour->images && $tour->images->count() > 0) 
                            ? $tour->images->first()->url() 
                            : asset('images/static/destination-sa-pa.jpg');
                    @endphp
                    <div class="group bg-white rounded-3xl overflow-hidden shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 flex flex-col h-full">
                        <div class="relative h-64 overflow-hidden">
                            <img src="{{ $tourImage }}" alt="{{ $tour->name }}" loading="lazy" decoding="async" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-80"></div>
                            
                            <!-- Badges -->
                            <div class="absolute top-4 left-4 flex gap-2">
                                <span class="bg-white/90 backdrop-blur text-[#1E3F20] text-xs font-bold px-3 py-1.5 rounded-full shadow-sm">
                                    {{ $tour->duration_days ?? '?' }} ngày {{ $tour->duration_nights ?? '?' }} đêm
                                </span>
                            </div>
                            
                            <div class="absolute bottom-4 left-4 right-4">
                                <h3 class="text-xl font-bold text-white mb-1 font-serif leading-snug group-hover:text-[#D4AF37] transition-colors">
                                    <a href="/tours/{{ $tour->slug }}" class="before:absolute before:inset-0">{{ $tour->name }}</a>
                                </h3>
                                <p class="text-gray-300 text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Điểm đến: {{ Str::limit($tour->location ?? 'Tây Bắc', 25) }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <p class="text-gray-600 text-sm mb-6 line-clamp-3">
                                {{ $tour->description }}
                            </p>
                            
                            <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-100">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Chỉ từ</p>
                                    <p class="text-xl font-bold text-[#1E3F20]">{{ number_format($tour->price, 0, ',', '.') }} ₫</p>
                                </div>
                                <span class="w-10 h-10 rounded-full bg-[#FAF9F6] flex items-center justify-center text-[#1E3F20] group-hover:bg-[#1E3F20] group-hover:text-white transition-colors duration-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Placeholder cards if no data -->
                    @foreach(['Săn Mây Cổng Trời', 'Bản Tình Ca Sông Núi', 'Sống Cùng Bản Làng'] as $index => $title)
                        <div class="group bg-white rounded-3xl overflow-hidden shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 flex flex-col h-full">
                            <div class="relative h-64 overflow-hidden">
                                <img src="{{ asset('images/static/culture-story.jpg') }}" alt="Câu chuyện văn hóa vùng cao" loading="lazy" decoding="async" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-80"></div>
                                <div class="absolute bottom-4 left-4 right-4">
                                    <h3 class="text-xl font-bold text-white mb-1 font-serif leading-snug">{{ $title }}</h3>
                                    <p class="text-gray-300 text-sm">Chinh phục những đỉnh núi bồng bềnh</p>
                                </div>
                            </div>
                            <div class="p-6">
                                <p class="text-gray-600 text-sm mb-6">Trải nghiệm cung đường Tây Bắc nguyên sơ với những khoảnh khắc không thể nào quên.</p>
                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <p class="text-xl font-bold text-[#1E3F20]">3.500.000 ₫</p>
                                    <span class="text-[#D4AF37] font-semibold text-sm">Xem chi tiết</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforelse
            </div>
            
            <div class="text-center mt-12">
                <a href="/tours" class="inline-flex items-center justify-center px-8 py-3 border border-[#1E3F20] text-[#1E3F20] font-semibold rounded-full hover:bg-[#1E3F20] hover:text-white transition-colors duration-300">
                    Xem Tất Cả Hành Trình
                </a>
            </div>
        </div>
    </section>

    <!-- 4. Hương Vị Đại Ngàn -->
    <section class="py-24 bg-[#1E3F20] relative overflow-hidden">
        <!-- Pattern Overlay -->
        <div class="absolute inset-0 opacity-5" style="background-image: url('{{ asset('images/static/pattern-cubes.png') }}')"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col lg:flex-row gap-16 items-center">
                <div class="lg:w-1/3">
                    <span class="text-[#D4AF37] uppercase tracking-widest text-sm font-bold block mb-4">Khám Phá Đặc Sản</span>
                    <h2 class="text-4xl md:text-5xl font-bold text-white mb-6 leading-tight">Mang Chút Tình Vùng Cao Về Phố</h2>
                    <p class="text-gray-300 text-lg leading-relaxed mb-8">
                        Gói trọn tinh hoa của núi rừng qua những dải thịt trâu gác bếp đậm vị sương gió, những búp chè Shan tuyết cổ thụ ngậm sương mai, hay hương mắc khén, hạt dổi nồng nàn.
                    </p>
                    <p class="text-gray-300 text-lg leading-relaxed mb-10">
                        Hãy nếm thử và mang theo hương vị đặc biệt này làm quà cho những người thân yêu.
                    </p>
                    <a href="#" class="inline-flex items-center justify-center px-8 py-4 bg-[#D4AF37] text-white font-semibold rounded-full shadow-lg hover:bg-[#b89528] transition-colors duration-300">
                        Ghé Phiên Chợ Vùng Cao
                    </a>
                </div>
                
                <div class="lg:w-2/3">
                    <!-- Masonry-like grid for products -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6">
                        <!-- Item 1 -->
                        <div class="col-span-2 md:col-span-2 row-span-2 rounded-2xl overflow-hidden relative group">
                            <img src="{{ asset('images/static/cuisine-buffalo.jpg') }}" alt="Thịt trâu gác bếp" loading="lazy" decoding="async" class="w-full h-full object-cover min-h-[300px] transform group-hover:scale-105 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                            <div class="absolute bottom-4 left-4">
                                <h3 class="text-white font-bold text-xl">Thịt trâu gác bếp</h3>
                                <p class="text-[#D4AF37] text-sm">Đặc sản Sơn La</p>
                            </div>
                        </div>
                        
                        <!-- Item 2 -->
                        <div class="rounded-2xl overflow-hidden relative group">
                            <img src="{{ asset('images/static/cuisine-tea.jpg') }}" alt="Chè Shan Tuyết" loading="lazy" decoding="async" class="w-full h-full object-cover min-h-[150px] transform group-hover:scale-105 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                            <div class="absolute bottom-3 left-3">
                                <h3 class="text-white font-semibold">Chè Shan Tuyết</h3>
                            </div>
                        </div>
                        
                        <!-- Item 3 -->
                        <div class="rounded-2xl overflow-hidden relative group">
                            <img src="{{ asset('images/static/cuisine-spices.jpg') }}" alt="Gia vị Tây Bắc" loading="lazy" decoding="async" class="w-full h-full object-cover min-h-[150px] transform group-hover:scale-105 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                            <div class="absolute bottom-3 left-3">
                                <h3 class="text-white font-semibold">Mắc khén, Hạt dổi</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
