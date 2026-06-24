<div x-data="{ open: false }" @open-search.window="open = true" @keydown.escape.window="open = false" class="relative z-[100]" x-cloak>
    <!-- Background backdrop -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm transition-opacity" 
         @click="open = false"></div>

    <!-- Modal panel -->
    <div x-show="open" class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl p-8 border border-gray-100">
                
                <!-- Close Button -->
                <button @click="open = false" class="absolute top-6 right-6 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 hover:text-gray-700 transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>

                <!-- Header -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-3 font-serif">
                        <i class="fa-solid fa-magnifying-glass text-[#D4AF37]"></i> Tìm kiếm Tour
                    </h2>
                    <p class="text-gray-500 mt-2 text-sm">Nhập từ khóa hoặc chọn bộ lọc để tìm tour phù hợp</p>
                </div>

                <form action="{{ route('tours.index') }}" method="GET" id="searchModalForm">
                    <!-- Tên Tour -->
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-keyboard text-[#1E3F20]"></i> TÊN TOUR
                        </label>
                        <div class="relative">
                            <input type="text" name="q" placeholder="Nhập tên tour, địa điểm..." 
                                   class="w-full border-2 border-gray-200 rounded-xl px-4 py-3.5 focus:ring-4 focus:ring-[#1E3F20]/10 focus:border-[#1E3F20] outline-none transition-all pr-12 text-gray-700 font-medium placeholder-gray-400">
                            <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#D4AF37] transition-colors">
                                <i class="fa-solid fa-magnifying-glass text-xl"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2 flex items-center gap-2">
                                <i class="fa-solid fa-location-dot text-[#1E3F20]"></i> ĐỊA ĐIỂM
                            </label>
                            <div class="relative">
                                <select name="location" class="w-full appearance-none border border-gray-200 rounded-xl px-4 py-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1E3F20] focus:border-transparent cursor-pointer bg-white">
                                    <option value="">Tất cả địa điểm</option>
                                    <option value="Sapa">Sapa</option>
                                    <option value="Hà Giang">Hà Giang</option>
                                    <option value="Mộc Châu">Mộc Châu</option>
                                    <option value="Mù Cang Chải">Mù Cang Chải</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                    <i class="fa-solid fa-caret-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2 flex items-center gap-2">
                                <i class="fa-solid fa-clock text-[#1E3F20]"></i> THỜI GIAN
                            </label>
                            <div class="relative">
                                <select name="duration" class="w-full appearance-none border border-gray-200 rounded-xl px-4 py-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1E3F20] focus:border-transparent cursor-pointer bg-white">
                                    <option value="">Tất cả</option>
                                    <option value="1-3">1-3 ngày</option>
                                    <option value="4-7">4-7 ngày</option>
                                    <option value="8+">Hơn 7 ngày</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                    <i class="fa-solid fa-caret-down text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-100 my-6"></div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row items-center gap-4 mb-8">
                        <button type="submit" class="w-full sm:w-auto flex-1 bg-[#D4AF37] hover:bg-[#c29f2f] text-white font-bold py-3.5 px-6 rounded-xl shadow-md transition-all duration-300 transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm
                        </button>
                        
                        <a href="{{ route('tours.index') }}" class="w-full sm:w-auto flex-1 bg-white hover:bg-gray-50 text-[#1E3F20] border-2 border-[#1E3F20] font-bold py-3 px-6 rounded-xl transition-all duration-300 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-list-ul"></i> Tất cả tours
                        </a>
                        
                        <button type="reset" class="w-full sm:w-auto px-4 py-2 text-gray-500 hover:text-gray-800 font-medium transition-colors flex items-center justify-center gap-2" onclick="document.getElementById('searchModalForm').reset();">
                            <i class="fa-solid fa-arrow-rotate-right"></i> Đặt lại
                        </button>
                    </div>

                    <!-- Quick Suggestions -->
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <span class="text-gray-600 font-medium mr-1">Tìm nhanh:</span>
                        @foreach(['Hà Giang', 'Sapa', 'Mộc Châu', 'Y Tý', 'Mù Cang Chải'] as $suggestion)
                            <a href="{{ route('tours.index', ['q' => $suggestion]) }}" class="px-4 py-1.5 bg-[#1E3F20]/5 text-[#1E3F20] border border-[#1E3F20]/20 hover:bg-[#1E3F20]/10 rounded-full text-xs font-bold transition-colors">
                                {{ $suggestion }}
                            </a>
                        @endforeach
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
