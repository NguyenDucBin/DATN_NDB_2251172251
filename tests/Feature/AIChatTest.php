<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
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

    public function test_chatbox_answers_sa_pa_price_from_database_without_calling_gemini(): void
    {
        $this->createTour([
            'name' => 'Tour săn mây Sa Pa',
            'slug' => 'tour-san-may-sa-pa',
            'location' => 'Sa Pa',
            'price' => 1200000,
            'duration_days' => 3,
            'duration_nights' => 2,
            'capacity' => 10,
        ]);

        Http::fake();

        $this->postJson(route('ai.chat'), [
            'messages' => [
                ['role' => 'user', 'content' => 'Tour Sa Pa giá bao nhiêu?'],
            ],
        ])->assertOk()
            ->assertJsonPath('reply', fn (string $reply): bool => str_contains($reply, 'Tour săn mây Sa Pa')
                && str_contains($reply, '1.200.000 VNĐ/người')
                && str_contains($reply, '3 ngày 2 đêm'));

        Http::assertNothingSent();
    }

    public function test_chatbox_answers_ha_giang_price_from_database_without_mixing_places(): void
    {
        $this->createTour([
            'name' => 'Tour bản làng Hà Giang',
            'slug' => 'tour-ban-lang-ha-giang',
            'location' => 'Hà Giang',
            'price' => 1000000,
            'duration_days' => 2,
            'duration_nights' => 1,
            'capacity' => 5,
        ]);

        Http::fake();

        $this->postJson(route('ai.chat'), [
            'messages' => [
                ['role' => 'user', 'content' => 'giá tour Hà Giang hiện tại đang là bao nhiêu'],
            ],
        ])->assertOk()
            ->assertJsonPath('reply', fn (string $reply): bool => str_contains($reply, 'Tour bản làng Hà Giang')
                && str_contains($reply, '1.000.000 VNĐ/người')
                && ! str_contains($reply, 'Sa Pa'));

        Http::assertNothingSent();
    }

    public function test_chatbox_explains_when_requested_days_are_shorter_than_available_tour(): void
    {
        $this->createTour([
            'name' => 'Tour săn mây Sa Pa',
            'slug' => 'tour-san-may-sa-pa',
            'location' => 'Sa Pa',
            'duration_days' => 3,
            'duration_nights' => 2,
        ]);

        Http::fake();

        $this->postJson(route('ai.chat'), [
            'messages' => [
                ['role' => 'user', 'content' => 'tôi muốn đi tour Sa Pa 1 ngày thôi có được không'],
            ],
        ])->assertOk()
            ->assertJsonPath('reply', fn (string $reply): bool => str_contains($reply, '3 ngày 2 đêm')
                && str_contains($reply, 'chưa có lựa chọn 1 ngày'));

        Http::assertNothingSent();
    }

    public function test_chatbox_answers_switching_between_sa_pa_one_day_and_ha_giang_from_database(): void
    {
        $this->createTour([
            'name' => 'Tour săn mây Sa Pa',
            'slug' => 'tour-san-may-sa-pa',
            'location' => 'Sa Pa',
            'price' => 1200000,
            'duration_days' => 3,
            'duration_nights' => 2,
        ]);
        $this->createTour([
            'name' => 'Tour bản làng Hà Giang',
            'slug' => 'tour-ban-lang-ha-giang',
            'location' => 'Hà Giang',
            'price' => 1000000,
            'duration_days' => 2,
            'duration_nights' => 1,
        ]);

        Http::fake();

        $this->postJson(route('ai.chat'), [
            'messages' => [
                ['role' => 'user', 'content' => 'tôi muốn đi tour sapa 1 ngày và chuyển sang đi tour hà giang thì thế nào'],
            ],
        ])->assertOk()
            ->assertJsonPath('reply', fn (string $reply): bool => str_contains($reply, 'Sa Pa')
                && str_contains($reply, 'chưa có lựa chọn 1 ngày')
                && str_contains($reply, 'Tour bản làng Hà Giang')
                && str_contains($reply, '1.000.000 VNĐ/người'));

        Http::assertNothingSent();
    }

    public function test_chatbox_does_not_invent_a_combined_sa_pa_ha_giang_tour(): void
    {
        $this->createTour([
            'name' => 'Tour săn mây Sa Pa',
            'slug' => 'tour-san-may-sa-pa',
            'location' => 'Sa Pa',
        ]);
        $this->createTour([
            'name' => 'Tour bản làng Hà Giang',
            'slug' => 'tour-ban-lang-ha-giang',
            'location' => 'Hà Giang',
        ]);

        Http::fake();

        $this->postJson(route('ai.chat'), [
            'messages' => [
                ['role' => 'user', 'content' => 'kết hợp Sa Pa và Hà Giang được không'],
            ],
        ])->assertOk()
            ->assertJsonPath('reply', fn (string $reply): bool => str_contains($reply, 'chưa có tour kết hợp')
                && str_contains($reply, 'Tour săn mây Sa Pa')
                && str_contains($reply, 'Tour bản làng Hà Giang'));

        Http::assertNothingSent();
    }

    public function test_chatbox_answers_a_specific_tour_name_without_listing_all_tours(): void
    {
        $this->createTour([
            'name' => 'Trải nghiệm làm cốm Tú Lệ',
            'slug' => 'trai-nghiem-lam-com-tu-le',
            'location' => 'Tú Lệ, Yên Bái',
            'price' => 1590000,
            'duration_days' => 2,
            'duration_nights' => 1,
            'capacity' => 20,
        ]);
        $this->createTour([
            'name' => 'Tắm lá thuốc người Dao đỏ',
            'slug' => 'tam-la-thuoc-nguoi-dao-do',
            'location' => 'Nghĩa Lộ, Yên Bái',
            'price' => 1690000,
            'duration_days' => 2,
            'duration_nights' => 1,
            'capacity' => 20,
        ]);
        $this->createTour([
            'name' => 'Thu hoạch chè Shan Tuyết cổ thụ',
            'slug' => 'thu-hoach-che-shan-tuyet-co-thu',
            'location' => 'Suối Giàng, Yên Bái',
            'price' => 1790000,
            'duration_days' => 2,
            'duration_nights' => 2,
            'capacity' => 15,
        ]);

        Http::fake();

        $this->postJson(route('ai.chat'), [
            'messages' => [
                ['role' => 'user', 'content' => 'cho tôi tour Tắm lá thuốc người Dao đỏ'],
            ],
        ])->assertOk()
            ->assertJsonPath('reply', fn (string $reply): bool => str_contains($reply, 'Tắm lá thuốc người Dao đỏ')
                && str_contains($reply, 'Nghĩa Lộ, Yên Bái')
                && str_contains($reply, '1.690.000 VNĐ/người')
                && str_contains($reply, '2 ngày 1 đêm')
                && ! str_contains($reply, 'Trải nghiệm làm cốm Tú Lệ')
                && ! str_contains($reply, 'Thu hoạch chè Shan Tuyết cổ thụ'));

        Http::assertNothingSent();
    }

    public function test_chatbox_lists_only_tours_matching_a_location(): void
    {
        $this->createTour([
            'name' => 'Trải nghiệm làm cốm Tú Lệ',
            'slug' => 'trai-nghiem-lam-com-tu-le',
            'location' => 'Tú Lệ, Yên Bái',
            'price' => 1590000,
        ]);
        $this->createTour([
            'name' => 'Tắm lá thuốc người Dao đỏ',
            'slug' => 'tam-la-thuoc-nguoi-dao-do',
            'location' => 'Nghĩa Lộ, Yên Bái',
            'price' => 1690000,
        ]);
        $this->createTour([
            'name' => 'Tour bản làng Hà Giang',
            'slug' => 'tour-ban-lang-ha-giang',
            'location' => 'Hà Giang',
            'price' => 1000000,
        ]);

        Http::fake();

        $this->postJson(route('ai.chat'), [
            'messages' => [
                ['role' => 'user', 'content' => 'tour Yên Bái có những tour nào'],
            ],
        ])->assertOk()
            ->assertJsonPath('reply', fn (string $reply): bool => str_contains($reply, 'Trải nghiệm làm cốm Tú Lệ')
                && str_contains($reply, 'Tắm lá thuốc người Dao đỏ')
                && ! str_contains($reply, 'Tour bản làng Hà Giang'));

        Http::assertNothingSent();
    }

    public function test_chatbox_uses_previous_user_tour_context_for_short_follow_up(): void
    {
        $this->createTour([
            'name' => 'Tắm lá thuốc người Dao đỏ',
            'slug' => 'tam-la-thuoc-nguoi-dao-do',
            'location' => 'Nghĩa Lộ, Yên Bái',
            'price' => 1690000,
            'duration_days' => 2,
            'duration_nights' => 1,
            'capacity' => 20,
        ]);
        $this->createTour([
            'name' => 'Tour bản làng Hà Giang',
            'slug' => 'tour-ban-lang-ha-giang',
            'location' => 'Hà Giang',
            'price' => 1000000,
        ]);

        Http::fake();

        $this->postJson(route('ai.chat'), [
            'messages' => [
                ['role' => 'user', 'content' => 'cho tôi tour Tắm lá thuốc người Dao đỏ'],
                ['role' => 'assistant', 'content' => 'Tour Tắm lá thuốc người Dao đỏ hiện đang mở bán tại Nghĩa Lộ, Yên Bái.'],
                ['role' => 'user', 'content' => 'tour này giá bao nhiêu'],
            ],
        ])->assertOk()
            ->assertJsonPath('reply', fn (string $reply): bool => str_contains($reply, 'Tắm lá thuốc người Dao đỏ')
                && str_contains($reply, '1.690.000 VNĐ/người')
                && ! str_contains($reply, 'Tour bản làng Hà Giang'));

        Http::assertNothingSent();
    }

    public function test_chatbox_uses_gemini_with_tour_context_and_conversation_history_for_open_questions(): void
    {
        $this->createTour([
            'name' => 'Tour Sa Pa kiểm thử',
            'slug' => 'tour-sa-pa-kiem-thu',
            'location' => 'Sa Pa',
            'description' => 'Khám phá bản làng và ruộng bậc thang.',
            'price' => 1500000,
            'duration_days' => 2,
            'duration_nights' => 1,
            'capacity' => 10,
        ]);

        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/*' => Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [['text' => 'Bạn có thể chọn tour Sa Pa kiểm thử nếu thích văn hóa bản làng.']],
                    ],
                ]],
            ]),
        ]);

        $this->postJson(route('ai.chat'), [
            'messages' => [
                ['role' => 'user', 'content' => 'Tôi đi cùng gia đình.'],
                ['role' => 'assistant', 'content' => 'Bạn muốn ưu tiên nghỉ dưỡng hay trải nghiệm văn hóa?'],
                ['role' => 'user', 'content' => 'Tôi thích văn hóa bản làng, hãy tư vấn thêm.'],
            ],
        ])->assertOk()->assertJson([
            'reply' => 'Bạn có thể chọn tour Sa Pa kiểm thử nếu thích văn hóa bản làng.',
        ]);

        Http::assertSent(function (Request $request) {
            $contents = $request['contents'];
            $instructions = $request['system_instruction']['parts'][0]['text'];

            return $request->url() === 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent'
                && $request->hasHeader('x-goog-api-key', 'test-secret-key')
                && $contents[0]['role'] === 'user'
                && $contents[1]['role'] === 'model'
                && $contents[2]['role'] === 'user'
                && $contents[1]['parts'][0]['text'] === 'Bạn muốn ưu tiên nghỉ dưỡng hay trải nghiệm văn hóa?'
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
                ['role' => 'user', 'content' => 'Bạn kể một lời chào thân thiện cho khách du lịch.'],
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
                            'parts' => [['text' => 'Bạn nên chuẩn bị áo khoác nhẹ, giày dễ đi và giấy tờ cá nhân.']],
                        ],
                    ]],
                ]),
        ]);

        $this->postJson(route('ai.chat'), [
            'messages' => [
                ['role' => 'user', 'content' => 'Tôi cần chuẩn bị hành lý như thế nào cho chuyến đi vùng cao?'],
            ],
        ])->assertOk()->assertJson([
            'reply' => 'Bạn nên chuẩn bị áo khoác nhẹ, giày dễ đi và giấy tờ cá nhân.',
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
                ['role' => 'user', 'content' => 'Bạn gợi ý tôi nên chuẩn bị tâm lý gì trước chuyến đi?'],
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

    private function createTour(array $overrides = []): Tour
    {
        $host = User::factory()->create();

        return Tour::create(array_merge([
            'host_id' => $host->id,
            'name' => 'Tour kiểm thử',
            'slug' => 'tour-kiem-thu-'.Str::random(6),
            'location' => 'Sa Pa',
            'description' => 'Khám phá bản làng và ruộng bậc thang.',
            'price' => 1500000,
            'duration_days' => 2,
            'duration_nights' => 1,
            'capacity' => 10,
            'itinerary' => [],
            'status' => 'approved',
            'is_active' => true,
        ], $overrides));
    }
}
