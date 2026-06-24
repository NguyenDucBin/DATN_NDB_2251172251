<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Services\GeminiChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class AIChatController extends Controller
{
    public function __invoke(Request $request, GeminiChatService $gemini): JsonResponse
    {
        $validated = $request->validate([
            'messages' => ['required', 'array', 'min:1', 'max:10'],
            'messages.*.role' => ['required', 'in:user,assistant'],
            'messages.*.content' => ['required', 'string', 'max:1500'],
        ]);

        $messages = collect($validated['messages'])->values();

        if ($messages->last()['role'] !== 'user') {
            return response()->json(['message' => 'Tin nhắn cuối cùng phải là câu hỏi của người dùng.'], 422);
        }

        $tourContext = Tour::query()
            ->where('status', 'approved')
            ->where('is_active', true)
            ->latest()
            ->limit(20)
            ->get(['name', 'location', 'price', 'duration_days', 'duration_nights', 'capacity', 'description'])
            ->map(fn (Tour $tour) => sprintf(
                '- %s | %s | %s VNĐ/người | %d ngày %d đêm | tối đa %d khách | %s',
                $tour->name,
                $tour->location ?: 'Chưa cập nhật địa điểm',
                number_format((float) $tour->price, 0, ',', '.'),
                (int) $tour->duration_days,
                (int) $tour->duration_nights,
                (int) $tour->capacity,
                str($tour->description)->squish()->limit(220),
            ))
            ->implode("\n");

        try {
            $reply = $gemini->reply($messages->all(), $tourContext ?: '- Hiện chưa có tour nào đang mở bán.');
        } catch (RuntimeException $exception) {
            [$message, $status] = match ($exception->getCode()) {
                401, 403 => ['Trợ lý AI chưa được cấu hình đúng. Vui lòng liên hệ quản trị viên.', 503],
                429 => ['Trợ lý AI đang tạm đạt giới hạn sử dụng miễn phí. Vui lòng thử lại sau.', 429],
                422 => ['Trợ lý AI không thể trả lời nội dung này. Bạn hãy thử diễn đạt câu hỏi khác.', 422],
                503 => ['Gemini đang bận hoặc quá tải tạm thời, bạn thử gửi lại sau vài giây nhé.', 503],
                default => ['Trợ lý AI hiện chưa thể phản hồi. Vui lòng thử lại sau.', 503],
            };

            return response()->json(['message' => $message], $status);
        }

        return response()->json(['reply' => $reply]);
    }
}
