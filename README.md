# Rẻo Cao Journeys

XÂY DỰNG WEBSITE DU LỊCH TRẢI NGHIỆM VĂN HÓA NÔNG NGHIỆP VÙNG NÚI VÀ TRUNG DU PHÍA BẮC, xây dựng bằng Laravel 12, MySQL, Blade, Tailwind CSS, Alpine.js và Vite.

## Yêu cầu

- PHP 8.2 trở lên và Composer
- MySQL/MariaDB
- Node.js và npm
- Các extension PHP thường dùng của Laravel: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`

## Cài đặt

1. Chạy `composer install` và `npm install`.
2. Sao chép `.env.example` thành `.env`, sau đó chạy `php artisan key:generate`.
3. Tạo database tên `reo_cao_db` với collation `utf8mb4_unicode_ci`.
4. Import nguyên file `reo_cao_db (1).sql` bằng phpMyAdmin, chọn bộ ký tự UTF-8.
5. Cấu hình `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` trong `.env`.
6. Chạy `php artisan storage:link`, `npm run build` và `php artisan optimize:clear`.
7. Khởi động bằng `php artisan serve --port=8000 --no-reload`.

> Không chạy `php artisan migrate` cho database dùng trong báo cáo. Project chủ động giữ nguyên cấu trúc bảng và cột của file `reo_cao_db (1).sql`.

## Chế độ thanh toán thử nghiệm

`PAYMENT_TEST_MODE=true` sẽ đánh dấu chuyển khoản ngân hàng là đã thanh toán ngay để kiểm thử booking và hoàn tiền. Khi trình bày luồng thực tế hoặc triển khai, đặt biến này thành `false`.

VNPay sử dụng các biến `VNP_TMN_CODE`, `VNP_HASH_SECRET`, `VNP_URL` và `VNP_RETURN_URL` trong `.env`.

## Trợ lý Gemini

Tạo API key tại [Google AI Studio](https://aistudio.google.com/app/apikey) và cấu hình trong `.env`:

```env
GEMINI_API_KEY=your-gemini-api-key
GEMINI_MODEL=gemini-2.5-flash-lite
GEMINI_BASE_URL=https://generativelanguage.googleapis.com/v1beta
GEMINI_TIMEOUT=30
```

API key chỉ được Laravel sử dụng ở backend. Không đưa key vào Blade, JavaScript hoặc commit lên Git. Sau khi thay đổi `.env`, chạy `php artisan optimize:clear` và khởi động lại server.

Gemini Free Tier có giới hạn sử dụng và dữ liệu gửi đến API có thể được Google dùng để cải thiện sản phẩm. Không nhập mật khẩu, mã OTP, số thẻ, thông tin thanh toán hoặc dữ liệu nhạy cảm vào chatbot.

## Đăng nhập Google

Tạo OAuth Client loại **Web application** trong Google Cloud Console. Khai báo redirect URI chính xác là `http://127.0.0.1:8000/auth/google/callback`, sau đó cấu hình trong `.env`:

```env
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

Nếu OAuth Consent Screen đang ở chế độ Testing, hãy thêm các Gmail dùng thử vào danh sách Test users. Sau khi thay đổi `.env`, chạy `php artisan optimize:clear`. Client Secret chỉ được lưu trong `.env` và không được commit lên Git.

Google chỉ đăng nhập các email đã có trong bảng `users`; hệ thống không tự tạo tài khoản, không lưu token và không thay đổi role hiện tại.

## Email thông báo Host

Khi Admin duyệt hoặc từ chối yêu cầu Host, Laravel gửi email thông báo qua Gmail SMTP. Bật xác minh hai bước cho tài khoản Gmail, tạo App Password và cấu hình trực tiếp trong `.env`:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-account@gmail.com
MAIL_PASSWORD=your-16-character-app-password
MAIL_FROM_ADDRESS=your-account@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

`MAIL_FROM_ADDRESS` nên giống `MAIL_USERNAME`. Không dùng mật khẩu Gmail thông thường và không commit `.env` hoặc App Password lên Git. Sau khi thay đổi cấu hình, chạy `php artisan optimize:clear`.

Nếu Gmail tạm thời gặp lỗi, thao tác duyệt hoặc từ chối vẫn được lưu; Admin sẽ nhận cảnh báo và lỗi gửi mail được ghi vào log.

## Kiểm thử

```bash
php artisan test
npm run build
php artisan route:list
```

Các thao tác duyệt Host, khóa tài khoản và trạng thái tài khoản được lưu bằng dữ liệu role/permission có sẵn; không cần thêm cột vào bảng `users`.
