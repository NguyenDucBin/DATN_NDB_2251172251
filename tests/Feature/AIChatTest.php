<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AIChatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.gemini.api_key' => 'test-secret-key',
            'services.gemini.model' => 'gemini-2.5-flash-lite',
            'services.gemini.base_url' => 'https://generativelanguage.googleapis.com/v1beta',
            'services.gemini.timeout' => 30,
        ]);
    }

    public function test_chatbox_uses_gemini_with_tour_context_and_conversation_history(): void
    {
        $host = User::factory()->create();
        Tour::create([
            'host_id' => $host->id,
            'name' => 'Tour Sa Pa kiểm thử',
            'slug' => 'tour-sa-pa-kiem-thu',
            'location' => 'Sa Pa',
            'description' => 'Khám phá bản làng và ruộng bậc thang.',
            'price' => 1500000,
            'duration_days' => 2,
            'duration_nights' => 1,
            'capacity' => 10,
            'itinerary' => [],
            'status' => 'approved',
            'is_active' => true,
        ]);

        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/*' => Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [['text' => 'Tour Sa Pa hiện có giá 1.500.000 VNĐ mỗi người.']],
                    ],
                ]],
            ]),
        ]);

        $this->postJson(route('ai.chat'), [
            'messages' => [
                ['role' => 'user', 'content' => 'Bạn có tour Sa Pa không?'],
                ['role' => 'assistant', 'content' => 'Có, bạn muốn hỏi thông tin gì?'],
                ['role' => 'user', 'content' => 'Tour đó giá bao nhiêu?'],
            ],
        ])->assertOk()->assertJson([
            'reply' => 'Tour Sa Pa hiện có giá 1.500.000 VNĐ mỗi người.',
        ]);

        Http::assertSent(function (Request $request) {
            $contents = $request['contents'];
            $instructions = $request['system_instruction']['parts'][0]['text'];

            return $request->url() === 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent'
                && $request->hasHeader('x-goog-api-key', 'test-secret-key')
                && $contents[0]['role'] === 'user'
                && $contents[1]['role'] === 'model'
                && $contents[2]['role'] === 'user'
                && $contents[1]['parts'][0]['text'] === 'Có, bạn muốn hỏi thông tin gì?'
                && $request['generationConfig']['maxOutputTokens'] === 400
                && str_contains($instructions, 'Tour Sa Pa kiểm thử')
                && str_contains($instructions, '1.500.000 VNĐ/người');
        });
    }

    public function test_chatbox_returns_a_configuration_error_when_api_key_is_missing(): void
    {
        config(['services.gemini.api_key' => null]);
        Http::fake();

        $this->postJson(route('ai.chat'), [
            'messages' => [
                ['role' => 'user', 'content' => 'Xin chào'],
            ],
        ])->assertStatus(503)->assertJson([
            'message' => 'Trợ lý AI chưa được cấu hình đúng. Vui lòng liên hệ quản trị viên.',
        ]);

        Http::assertNothingSent();
    }

    public function test_chatbox_reports_free_tier_quota_errors(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/*' => Http::response([], 429),
        ]);

        $this->postJson(route('ai.chat'), [
            'messages' => [
                ['role' => 'user', 'content' => 'Gợi ý tour cho tôi'],
            ],
        ])->assertStatus(429)->assertJson([
            'message' => 'Trợ lý AI đang tạm đạt giới hạn sử dụng miễn phí. Vui lòng thử lại sau.',
        ]);
    }

    public function test_chatbox_retries_once_when_gemini_is_temporarily_unavailable(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/*' => Http::sequence()
                ->push([], 503)
                ->push([
                    'candidates' => [[
                        'content' => [
                            'parts' => [['text' => 'Tour Sa Pa hiện có thời lượng 3 ngày 2 đêm.']],
                        ],
                    ]],
                ]),
        ]);

        $this->postJson(route('ai.chat'), [
            'messages' => [
                ['role' => 'user', 'content' => 'Nếu tôi đi Sa Pa 1 ngày thôi có được không?'],
            ],
        ])->assertOk()->assertJson([
            'reply' => 'Tour Sa Pa hiện có thời lượng 3 ngày 2 đêm.',
        ]);

        Http::assertSentCount(2);
    }

    public function test_chatbox_reports_a_clear_message_when_gemini_stays_unavailable(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/*' => Http::response([], 503),
        ]);

        $this->postJson(route('ai.chat'), [
            'messages' => [
                ['role' => 'user', 'content' => 'Tư vấn tour cho tôi'],
            ],
        ])->assertStatus(503)->assertJson([
            'message' => 'Gemini đang bận hoặc quá tải tạm thời, bạn thử gửi lại sau vài giây nhé.',
        ]);

        Http::assertSentCount(2);
    }

    public function test_chatbox_reports_a_blocked_response_safely(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/*' => Http::response([
                'promptFeedback' => ['blockReason' => 'SAFETY'],
            ]),
        ]);

        $this->postJson(route('ai.chat'), [
            'messages' => [
                ['role' => 'user', 'content' => 'Câu hỏi bị chặn'],
            ],
        ])->assertStatus(422)->assertJson([
            'message' => 'Trợ lý AI không thể trả lời nội dung này. Bạn hãy thử diễn đạt câu hỏi khác.',
        ]);
    }

    public function test_chatbox_returns_a_safe_error_for_an_empty_response(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/*' => Http::response([
                'candidates' => [],
            ]),
        ]);

        $this->postJson(route('ai.chat'), [
            'messages' => [
                ['role' => 'user', 'content' => 'Xin chào'],
            ],
        ])->assertStatus(503)->assertJson([
            'message' => 'Gemini đang bận hoặc quá tải tạm thời, bạn thử gửi lại sau vài giây nhé.',
        ]);
    }

    public function test_chatbox_requires_the_last_message_to_belong_to_the_user(): void
    {
        $this->postJson(route('ai.chat'), [
            'messages' => [
                ['role' => 'assistant', 'content' => 'Tôi có thể giúp gì?'],
            ],
        ])->assertUnprocessable()->assertJson([
            'message' => 'Tin nhắn cuối cùng phải là câu hỏi của người dùng.',
        ]);
    }
}
