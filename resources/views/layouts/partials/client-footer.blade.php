<footer class="bg-[#1E3F20] text-gray-300 py-16 border-t border-[#2A5A2E]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            <!-- Brand -->
            <div>
                <h3 class="text-white text-2xl font-serif font-bold mb-4">Rẻo Cao Journeys</h3>
                <p class="italic text-[#D4AF37] mb-4 font-serif">"Chạm vào nguyên bản"</p>
                <p class="text-sm leading-relaxed text-gray-400">Khám phá, gìn giữ và tôn vinh những giá trị văn hóa nguyên bản của đồng bào vùng cao phía Bắc Việt Nam.</p>
                <div class="mt-6 flex space-x-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-[#D4AF37] hover:text-white transition-colors">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-[#D4AF37] hover:text-white transition-colors">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-[#D4AF37] hover:text-white transition-colors">
                        <i class="fa-brands fa-youtube"></i>
                    </a>
                </div>
            </div>

            <!-- Links -->
            <div>
                <h4 class="text-white text-lg font-bold mb-6">Khám Phá Hệ Thống</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('magazine.index') }}" class="text-gray-400 hover:text-[#D4AF37] transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> Tạp chí văn hóa</a></li>
                    <li><a href="{{ route('home') }}#tours" class="text-gray-400 hover:text-[#D4AF37] transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> Tour trải nghiệm</a></li>
                    <li><a href="{{ route('login') }}" class="text-gray-400 hover:text-[#D4AF37] transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> Không gian Chủ nhà (Host)</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h4 class="text-white text-lg font-bold mb-6">Liên Hệ Với Chúng Tôi</h4>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-envelope text-[#D4AF37] mt-1"></i>
                        <span class="text-sm">contact@reocaojourneys.vn</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-location-dot text-[#D4AF37] mt-1"></i>
                        <span class="text-sm">Hà Giang - Sa Pa - Cao Bằng,<br>Việt Nam</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-phone text-[#D4AF37] mt-1"></i>
                        <span class="text-sm">+84 123 456 789</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-16 pt-8 border-t border-white/10 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} Rẻo Cao Journeys. Tất cả các quyền được bảo hộ.
        </div>
    </div>
</footer>