<div id="ai-chatbot-widget" x-data="chatbotApp()" x-cloak style="position: fixed; bottom: 24px; right: 24px; z-index: 9999;">
    
    <!-- Chat Window -->
    <div x-show="isOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         style="width: 380px; max-width: calc(100vw - 48px); margin-bottom: 16px;">
        
        <div style="background: white; border-radius: 20px; box-shadow: 0 8px 40px rgba(0,0,0,0.18); border: 1px solid #e5e7eb; overflow: hidden; display: flex; flex-direction: column; height: 520px;">
            
            <!-- Header -->
            <div style="background: linear-gradient(135deg, #1E3F20 0%, #2A5A2E 100%); color: white; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-robot" style="font-size: 1.2rem;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.95rem;">Trợ lý Rẻo Cao</div>
                        <div style="font-size: 0.7rem; opacity: 0.8; display: flex; align-items: center; gap: 4px;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #4ade80; display: inline-block;"></span>
                            <span x-text="isTyping ? 'Đang trả lời...' : 'Sẵn sàng hỗ trợ'"></span>
                        </div>
                    </div>
                </div>
                <button @click="isOpen = false" style="background: rgba(255,255,255,0.1); border: none; color: white; cursor: pointer; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Messages Area -->
            <div x-ref="messagesContainer" style="flex: 1; padding: 16px; overflow-y: auto; background: #f9fafb; display: flex; flex-direction: column; gap: 12px;">
                
                <template x-for="(msg, i) in messages" :key="i">
                    <div :style="msg.sender === 'bot' ? 'align-self: flex-start; max-width: 85%;' : 'align-self: flex-end; max-width: 85%;'">
                        <div class="chatbot-message-text" :style="msg.sender === 'bot'
                            ? 'background: white; color: #1f2937; padding: 12px 16px; border-radius: 16px; border-bottom-left-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); font-size: 0.875rem; line-height: 1.5;' 
                            : 'background: linear-gradient(135deg, #1E3F20, #2A5A2E); color: white; padding: 12px 16px; border-radius: 16px; border-bottom-right-radius: 4px; font-size: 0.875rem; line-height: 1.5;'"
                            x-text="msg.text">
                        </div>
                        <div :style="msg.sender === 'bot' ? 'font-size: 0.65rem; color: #9ca3af; margin-top: 4px;' : 'font-size: 0.65rem; color: #9ca3af; margin-top: 4px; text-align: right;'" x-text="msg.time"></div>
                    </div>
                </template>

                <!-- Typing indicator -->
                <div x-show="isTyping" style="align-self: flex-start;">
                    <div style="background: white; padding: 12px 20px; border-radius: 16px; border-bottom-left-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); display: flex; gap: 4px; align-items: center;">
                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #9ca3af; animation: bounce 1.4s infinite ease-in-out; animation-delay: 0s;"></span>
                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #9ca3af; animation: bounce 1.4s infinite ease-in-out; animation-delay: 0.2s;"></span>
                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #9ca3af; animation: bounce 1.4s infinite ease-in-out; animation-delay: 0.4s;"></span>
                    </div>
                </div>
            </div>

            <!-- Quick Suggestions (only show at start) -->
            <div x-show="messages.length <= 1" style="padding: 0 16px 8px; background: #f9fafb; display: flex; flex-wrap: wrap; gap: 6px; flex-shrink: 0;">
                <button @click="sendQuick('Tour Sa Pa giá bao nhiêu?')" style="background: white; border: 1px solid #e5e7eb; border-radius: 20px; padding: 6px 12px; font-size: 0.75rem; color: #374151; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#D4AF37';this.style.color='#1E3F20'" onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151'">
                    💰 Giá tour Sa Pa?
                </button>
                <button @click="sendQuick('Làm sao để đặt tour?')" style="background: white; border: 1px solid #e5e7eb; border-radius: 20px; padding: 6px 12px; font-size: 0.75rem; color: #374151; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#D4AF37';this.style.color='#1E3F20'" onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151'">
                    📋 Đặt tour thế nào?
                </button>
                <button @click="sendQuick('Tour Hà Giang có gì hay?')" style="background: white; border: 1px solid #e5e7eb; border-radius: 20px; padding: 6px 12px; font-size: 0.75rem; color: #374151; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#D4AF37';this.style.color='#1E3F20'" onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151'">
                    🏔️ Tour Hà Giang?
                </button>
                <button @click="sendQuick('Có chính sách hủy tour không?')" style="background: white; border: 1px solid #e5e7eb; border-radius: 20px; padding: 6px 12px; font-size: 0.75rem; color: #374151; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#D4AF37';this.style.color='#1E3F20'" onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151'">
                    ❌ Chính sách hủy?
                </button>
            </div>

            <!-- Input Area -->
            <div style="padding: 12px 16px; border-top: 1px solid #e5e7eb; background: white; display: flex; gap: 8px; align-items: center; flex-shrink: 0;">
                <input x-ref="chatInput" x-model="userInput" @keydown.enter.prevent="sendMessage()" :disabled="isTyping" type="text" placeholder="Nhập câu hỏi của bạn..."
                       style="flex: 1; border: 1px solid #e5e7eb; border-radius: 24px; padding: 10px 18px; font-size: 0.85rem; outline: none; transition: border-color 0.2s; background: #f9fafb;"
                       onfocus="this.style.borderColor='#D4AF37';this.style.background='white'" onblur="this.style.borderColor='#e5e7eb';this.style.background='#f9fafb'">
                <button @click="sendMessage()" :disabled="isTyping || !userInput.trim()"
                        style="width: 40px; height: 40px; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; flex-shrink: 0;"
                        :style="!isTyping && userInput.trim() ? 'background: linear-gradient(135deg, #1E3F20, #2A5A2E); color: white; box-shadow: 0 2px 8px rgba(30,63,32,0.3);' : 'background: #e5e7eb; color: #9ca3af; cursor: not-allowed;'">
                    <i class="fa-solid fa-paper-plane" style="font-size: 0.85rem;"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Toggle Button -->
    <div style="display: flex; justify-content: flex-end; align-items: flex-end; gap: 10px;">
        <div x-show="!isOpen" x-transition
             style="background: white; color: #1E3F20; border: 1px solid rgba(30,63,32,0.12); border-radius: 999px; padding: 9px 14px; box-shadow: 0 10px 30px rgba(15,23,42,0.14); font-size: 0.8rem; font-weight: 700; white-space: nowrap;">
            Hỗ trợ
        </div>

        <button @click="isOpen = !isOpen"
                aria-label="Mở trợ lý Rẻo Cao"
                class="chatbot-toggle-button"
                :class="isOpen ? 'is-open' : ''">
            <span class="chatbot-toggle-glow" x-show="!isOpen"></span>
            <span class="chatbot-toggle-icon">
                <i :class="isOpen ? 'fa-solid fa-xmark' : 'fa-solid fa-message'"></i>
            </span>
            <span x-show="!isOpen" class="chatbot-toggle-badge">AI</span>
            <span x-show="!isOpen && !hasOpened" class="chatbot-notification-dot"></span>
        </button>
    </div>
</div>

<style>
.chatbot-message-text {
    white-space: pre-wrap;
    overflow-wrap: anywhere;
}

.chatbot-toggle-button {
    width: 68px;
    height: 68px;
    border-radius: 22px;
    border: 3px solid #ffffff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: visible;
    color: white;
    background: linear-gradient(135deg, #1E3F20 0%, #2A5A2E 48%, #D4AF37 100%);
    box-shadow: 0 18px 36px rgba(30, 63, 32, 0.34), 0 6px 16px rgba(212, 175, 55, 0.32);
    transition: transform 0.22s ease, box-shadow 0.22s ease, background 0.22s ease;
}

.chatbot-toggle-button:hover {
    transform: translateY(-3px) scale(1.04);
    box-shadow: 0 22px 44px rgba(30, 63, 32, 0.42), 0 8px 18px rgba(212, 175, 55, 0.36);
}

.chatbot-toggle-button.is-open {
    background: #374151;
    box-shadow: 0 14px 30px rgba(15, 23, 42, 0.28);
}

.chatbot-toggle-icon {
    width: 44px;
    height: 44px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.16);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.28);
    font-size: 1.35rem;
    position: relative;
    z-index: 2;
}

.chatbot-toggle-glow {
    position: absolute;
    inset: -8px;
    border-radius: 28px;
    background: rgba(212, 175, 55, 0.22);
    animation: chatGlow 2.4s infinite ease-in-out;
    z-index: 0;
}

.chatbot-toggle-badge {
    position: absolute;
    right: -4px;
    bottom: -4px;
    min-width: 26px;
    height: 26px;
    padding: 0 6px;
    border-radius: 999px;
    background: #ffffff;
    color: #1E3F20;
    border: 2px solid #D4AF37;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.72rem;
    font-weight: 800;
    line-height: 1;
    z-index: 3;
}

.chatbot-notification-dot {
    position: absolute;
    top: -4px;
    right: -4px;
    width: 18px;
    height: 18px;
    background: #ef4444;
    border-radius: 50%;
    border: 3px solid white;
    animation: pulse 2s infinite;
    z-index: 4;
}

@keyframes chatGlow {
    0%, 100% { transform: scale(0.94); opacity: 0.55; }
    50% { transform: scale(1.06); opacity: 0.18; }
}

@keyframes bounce {
    0%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-6px); }
}
@keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.2); opacity: 0.7; }
    100% { transform: scale(1); opacity: 1; }
}
</style>

<script>
function chatbotApp() {
    return {
        isOpen: false,
        hasOpened: false,
        userInput: '',
        isTyping: false,
        messages: [
            {
                sender: 'bot',
                text: 'Xin chào! Tôi là trợ lý AI của Rẻo Cao Journeys. Tôi có thể giúp bạn tìm hiểu tour, điểm đến và cách đặt chỗ. Hãy hỏi tôi nhé!',
                time: new Date().toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'})
            }
        ],

        // FAQ Knowledge Base
        faqData: [
            {
                keywords: ['đặt tour', 'đặt chỗ', 'booking', 'đặt', 'mua tour', 'đăng ký tour', 'cách đặt', 'làm sao đặt'],
                answer: '📋 <b>Cách đặt tour tại Rẻo Cao Journeys:</b><br><br>1. Chọn tour bạn yêu thích<br>2. Bấm nút <b>"Đặt Tour Ngay"</b><br>3. Điền thông tin và chọn ngày khởi hành<br>4. Xác nhận và thanh toán<br><br>Rất đơn giản! Bạn cần hỗ trợ thêm gì không?'
            },
            {
                keywords: ['giá', 'bao nhiêu', 'chi phí', 'phí', 'tiền', 'thanh toán', 'trả tiền'],
                answer: '💰 <b>Về giá tour:</b><br><br>Giá tour tại Rẻo Cao Journeys dao động từ <b>500.000₫ - 5.000.000₫/người</b> tùy vào điểm đến và thời lượng. Giá đã bao gồm:<br>• Ăn uống, chỗ ở<br>• Hướng dẫn viên bản địa<br>• Hoạt động trải nghiệm<br><br>Bạn có thể xem giá cụ thể ở trang chi tiết mỗi tour nhé!'
            },
            {
                keywords: ['hủy', 'hoàn tiền', 'hủy tour', 'hoàn', 'refund', 'cancel', 'đổi ngày'],
                answer: '❌ <b>Chính sách hủy tour:</b><br><br>• Hủy trước <b>7 ngày</b>: Hoàn tiền 100%<br>• Hủy trước <b>3-7 ngày</b>: Hoàn 50%<br>• Hủy trong <b>3 ngày</b>: Không hoàn tiền<br>• Có thể <b>đổi ngày</b> miễn phí 1 lần<br><br>Liên hệ chúng tôi nếu bạn cần hỗ trợ!'
            },
            {
                keywords: ['sa pa', 'sapa', 'lào cai'],
                answer: '🏔️ <b>Tour Sa Pa:</b><br><br>Sa Pa – thị trấn trong sương với ruộng bậc thang tuyệt đẹp! Các tour phổ biến:<br>• Trekking bản Cát Cát, Tả Van<br>• Chinh phục đỉnh Fansipan<br>• Homestay cùng người H\'Mông<br><br>👉 <a href="/destinations/sa-pa" style="color:#D4AF37;font-weight:600;">Xem tất cả tour Sa Pa →</a>'
            },
            {
                keywords: ['hà giang', 'ha giang', 'đồng văn', 'mã pí lèng'],
                answer: '⛰️ <b>Tour Hà Giang:</b><br><br>Hà Giang – cao nguyên đá hùng vĩ với cung đường đèo huyền thoại! Trải nghiệm nổi bật:<br>• Cung đường Mã Pí Lèng<br>• Sông Nho Quế xanh ngọc<br>• Phố cổ Đồng Văn<br>• Dinh thự Vua Mèo<br><br>👉 <a href="/destinations/ha-giang" style="color:#D4AF37;font-weight:600;">Xem tất cả tour Hà Giang →</a>'
            },
            {
                keywords: ['mù cang chải', 'mu cang chai', 'ruộng bậc thang'],
                answer: '🌾 <b>Tour Mù Cang Chải:</b><br><br>Thiên đường ruộng bậc thang! Đẹp nhất tháng 9-10 mùa lúa chín. Trải nghiệm:<br>• Ngắm ruộng bậc thang La Pán Tẩn<br>• Lễ hội Khèn Mông<br>• Paragliding ngắm thung lũng<br><br>👉 <a href="/destinations/mu-cang-chai" style="color:#D4AF37;font-weight:600;">Xem tất cả tour Mù Cang Chải →</a>'
            },
            {
                keywords: ['cao bằng', 'cao bang', 'bản giốc', 'ban gioc', 'thác'],
                answer: '💧 <b>Tour Cao Bằng:</b><br><br>Cao Bằng – vùng đất biên cương với thác Bản Giốc hùng vĩ! Điểm nhấn:<br>• Thác Bản Giốc – thác tự nhiên lớn nhất ĐNA<br>• Động Ngườm Ngao<br>• Hồ Thang Hen<br><br>👉 <a href="/destinations/cao-bang" style="color:#D4AF37;font-weight:600;">Xem tất cả tour Cao Bằng →</a>'
            },
            {
                keywords: ['mộc châu', 'moc chau', 'sơn la'],
                answer: '🌸 <b>Tour Mộc Châu:</b><br><br>Mộc Châu – thảo nguyên xanh mát với đồi chè bát ngát! Trải nghiệm:<br>• Thảo nguyên Mộc Châu<br>• Đồi chè trái tim<br>• Thác Dải Yếm<br>• Mùa hoa mận tháng 1-2<br><br>👉 <a href="/destinations/moc-chau" style="color:#D4AF37;font-weight:600;">Xem tất cả tour Mộc Châu →</a>'
            },
            {
                keywords: ['thời gian', 'mùa nào', 'khi nào', 'tháng nào', 'thời tiết', 'nên đi'],
                answer: '🗓️ <b>Thời gian tốt nhất để đi Tây Bắc:</b><br><br>• <b>Tháng 9-11:</b> Mùa lúa chín – ruộng bậc thang vàng rực<br>• <b>Tháng 1-3:</b> Mùa hoa đào, mận, cải<br>• <b>Tháng 4-5:</b> Mùa nước đổ, ruộng bậc thang lung linh<br><br>Tránh tháng 6-8 vì thường có mưa lũ. Tuy nhiên mỗi mùa đều có vẻ đẹp riêng!'
            },
            {
                keywords: ['liên hệ', 'hỗ trợ', 'hotline', 'điện thoại', 'email', 'gọi'],
                answer: '📞 <b>Liên hệ hỗ trợ:</b><br><br>• Hotline: <b>1900 xxxx</b><br>• Email: <b>hello@reocao.vn</b><br>• Fanpage: Rẻo Cao Journeys<br>• Giờ làm việc: 8:00 - 22:00 hàng ngày<br><br>Hoặc bạn có thể nhắn tin trực tiếp cho Host trong trang chi tiết tour!'
            },
            {
                keywords: ['host', 'chủ nhà', 'hướng dẫn', 'guide'],
                answer: '🏠 <b>Về Host (Chủ nhà):</b><br><br>Host là những người dân bản địa am hiểu văn hóa và địa phương. Họ sẽ:<br>• Dẫn dắt và đồng hành cùng bạn<br>• Chia sẻ câu chuyện văn hóa nguyên bản<br>• Đảm bảo an toàn suốt chuyến đi<br><br>Bạn có thể nhắn tin trực tiếp cho Host ở trang chi tiết tour!'
            },
            {
                keywords: ['xin chào', 'hello', 'hi', 'chào', 'alo'],
                answer: 'Xin chào! 😊 Rất vui được gặp bạn! Tôi có thể giúp bạn:<br><br>• 🏔️ Tìm tour theo điểm đến<br>• 💰 Tư vấn giá và thanh toán<br>• 📋 Hướng dẫn đặt tour<br>• ❌ Chính sách hủy/hoàn tiền<br><br>Bạn muốn hỏi gì nhé?'
            },
            {
                keywords: ['cảm ơn', 'thank', 'thanks', 'tks'],
                answer: 'Không có chi! 😊 Rất vui được giúp bạn. Nếu có thêm câu hỏi nào, đừng ngại hỏi tôi nhé! Chúc bạn có một chuyến đi Tây Bắc thật tuyệt vời! 🏔️✨'
            }
        ],

        init() {
            this.$watch('isOpen', (val) => {
                if (val) {
                    this.hasOpened = true;
                    this.$nextTick(() => this.scrollToBottom());
                }
            });
        },

        sendQuick(text) {
            this.userInput = text;
            this.sendMessage();
        },

        async sendMessage() {
            const text = this.userInput.trim();
            if (!text || this.isTyping) return;

            const requestMessages = this.messages
                .slice(1)
                .slice(-8)
                .map(message => ({
                    role: message.sender === 'bot' ? 'assistant' : 'user',
                    content: message.text
                }));

            requestMessages.push({ role: 'user', content: text });

            this.messages.push({
                sender: 'user',
                text: text,
                time: new Date().toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'})
            });
            this.userInput = '';
            this.scrollToBottom();
            this.isTyping = true;
            this.scrollToBottom();

            try {
                const response = await fetch({{ Js::from(route('ai.chat')) }}, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ messages: requestMessages })
                });
                const data = await response.json().catch(() => ({}));

                if (!response.ok || !data.reply) {
                    throw new Error(data.message || 'Trợ lý AI hiện chưa thể phản hồi.');
                }

                this.messages.push({
                    sender: 'bot',
                    text: data.reply,
                    time: new Date().toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'})
                });
            } catch (error) {
                this.messages.push({
                    sender: 'bot',
                    text: error.message || 'Không thể kết nối trợ lý AI. Vui lòng thử lại sau.',
                    time: new Date().toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'})
                });
            } finally {
                this.isTyping = false;
                this.scrollToBottom();
                this.$nextTick(() => this.$refs.chatInput?.focus());
            }
        },

        getResponse(input) {
            const lowerInput = input.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");

            // Search through FAQ data
            let bestMatch = null;
            let bestScore = 0;

            for (const faq of this.faqData) {
                let score = 0;
                for (const keyword of faq.keywords) {
                    const normalizedKeyword = keyword.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                    if (lowerInput.includes(normalizedKeyword)) {
                        score += normalizedKeyword.length; // Longer matches = better
                    }
                }
                if (score > bestScore) {
                    bestScore = score;
                    bestMatch = faq;
                }
            }

            if (bestMatch && bestScore > 0) {
                return bestMatch.answer;
            }

            // Default response
            return '🤔 Xin lỗi, tôi chưa hiểu rõ câu hỏi của bạn. Bạn có thể hỏi tôi về:<br><br>• <b>Điểm đến</b>: Sa Pa, Hà Giang, Mù Cang Chải...<br>• <b>Đặt tour</b>: Cách đặt, giá cả, thanh toán<br>• <b>Chính sách</b>: Hủy tour, hoàn tiền<br>• <b>Thời gian</b>: Mùa nào nên đi<br><br>Hoặc liên hệ hotline <b>1900 xxxx</b> để được hỗ trợ trực tiếp!';
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        }
    }
}
</script>
