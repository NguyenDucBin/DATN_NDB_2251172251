<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Services\GeminiChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
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

        $tours = $this->availableTours();
        $databaseReply = $this->replyFromDatabase($messages, $tours);

        if ($databaseReply !== null) {
            return response()->json(['reply' => $databaseReply]);
        }

        try {
            $reply = $gemini->reply($messages->all(), $this->buildTourContext($tours));
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

    private function availableTours(): Collection
    {
        return Tour::query()
            ->where('status', 'approved')
            ->where('is_active', true)
            ->latest()
            ->limit(20)
            ->get(['id', 'name', 'slug', 'location', 'price', 'duration_days', 'duration_nights', 'capacity', 'description']);
    }

    private function replyFromDatabase(Collection $messages, Collection $tours): ?string
    {
        $question = (string) $messages->last()['content'];
        $normalizedQuestion = $this->normalizeText($question);
        $previousUserText = $this->normalizeText(
            $messages
                ->take(max(0, $messages->count() - 1))
                ->where('role', 'user')
                ->pluck('content')
                ->implode(' ')
        );
        $conversationText = $this->normalizeText($messages->pluck('content')->implode(' '));
        $requestedDays = $this->requestedDays($normalizedQuestion);
        $hasCombinationKeyword = $this->containsAny($normalizedQuestion, [
            'ket hop', 'ghep', 'di ca', 'ca hai', 'chuyen sang', 'doi sang',
        ]);

        $latestTourCandidates = $this->tourCandidatesInText($normalizedQuestion, $tours);

        if (! $hasCombinationKeyword) {
            $singleTour = $this->singleStrongTourCandidate($latestTourCandidates);

            if ($singleTour) {
                return $this->replyForSingleTour($singleTour, $normalizedQuestion, $requestedDays);
            }

            if ($latestTourCandidates->count() > 1) {
                return $this->replyWithMatchedTours($latestTourCandidates->pluck('tour'), $normalizedQuestion);
            }

            if ($this->isContextualTourQuestion($normalizedQuestion)) {
                $previousTour = $this->singleStrongTourCandidate($this->tourCandidatesInText($previousUserText, $tours));

                if ($previousTour) {
                    return $this->replyForSingleTour($previousTour, $normalizedQuestion, $requestedDays);
                }
            }
        }

        $mentionedPlaces = $this->mentionedPlaces($conversationText, $tours);
        $latestPlaces = $this->mentionedPlaces($normalizedQuestion, $tours);

        if ($latestPlaces !== []) {
            $mentionedPlaces = $latestPlaces;
        }

        $hasTourIntent = $this->containsAny($normalizedQuestion, [
            'tour', 'gia', 'bao nhieu', 'thoi luong', 'may ngay', 'may dem', 'suc chua',
            'toi da', 'bao nhieu khach', 'con cho', 'mo ban', 'dat cho', 'dat tour', 'ket hop',
            'ghep', 'di ca', 'chuyen sang', 'doi sang',
        ]);

        if (! $hasTourIntent && $mentionedPlaces === []) {
            return null;
        }

        $isCombinationQuestion = count($mentionedPlaces) > 1 || $hasCombinationKeyword;

        if ($mentionedPlaces === [] && $hasTourIntent) {
            if ($this->looksLikeSpecificTourLookup($normalizedQuestion)) {
                return "Mình chưa tìm thấy tour phù hợp với tên bạn vừa hỏi.\n\nBạn kiểm tra lại tên tour hoặc mở mục Tour trải nghiệm để chọn đúng hành trình nhé.";
            }

            return $this->replyWithAvailableTours($tours);
        }

        if ($isCombinationQuestion) {
            $requestedDaysOnlyAppliesToFirstPlace = $this->containsAny($normalizedQuestion, ['chuyen sang', 'doi sang']);

            return $this->replyForCombination($mentionedPlaces, $tours, $requestedDays, $requestedDaysOnlyAppliesToFirstPlace);
        }

        $place = $mentionedPlaces[0] ?? null;
        $matchingTours = $place ? $this->toursForPlace($tours, $place) : collect();

        if ($matchingTours->isEmpty()) {
            return sprintf(
                "Hiện tại Rẻo Cao Journeys chưa có tour %s đang mở bán.\n\n%s",
                $place,
                $this->suggestAvailableTours($tours)
            );
        }

        $tour = $matchingTours->first();

        if ($requestedDays !== null && $requestedDays < (int) $tour->duration_days) {
            return sprintf(
                "Tour %s hiện có thời lượng %d ngày %d đêm, nên hệ thống chưa có lựa chọn %d ngày cho %s.\n\nBạn có thể xem tour hiện có hoặc liên hệ Host/Admin để được tư vấn lịch trình riêng.",
                $tour->name,
                (int) $tour->duration_days,
                (int) $tour->duration_nights,
                $requestedDays,
                $place
            );
        }

        return sprintf(
            "Hiện tại Rẻo Cao Journeys có %s tại %s.\n\n%s\n\nBạn có thể mở trang Tour trải nghiệm để xem chi tiết và đặt tour nếu lịch trình phù hợp nhé.",
            $tour->name,
            $tour->location ?: $place,
            $this->tourSummary($tour)
        );
    }

    private function replyForSingleTour(Tour $tour, string $normalizedQuestion, ?int $requestedDays = null): string
    {
        if ($requestedDays !== null && $requestedDays < (int) $tour->duration_days) {
            return sprintf(
                "Tour %s hiện có thời lượng %d ngày %d đêm, nên hệ thống chưa có lựa chọn %d ngày cho tour này.\n\nBạn có thể xem tour hiện có hoặc liên hệ Host/Admin để được tư vấn lịch trình riêng.",
                $tour->name,
                (int) $tour->duration_days,
                (int) $tour->duration_nights,
                $requestedDays
            );
        }

        if ($this->containsAny($normalizedQuestion, ['gia', 'bao nhieu', 'chi phi'])) {
            return sprintf(
                "Tour %s hiện có giá %s VNĐ/người.\n\nThời lượng: %d ngày %d đêm.\nĐịa điểm: %s.",
                $tour->name,
                number_format((float) $tour->price, 0, ',', '.'),
                (int) $tour->duration_days,
                (int) $tour->duration_nights,
                $tour->location ?: 'chưa cập nhật địa điểm'
            );
        }

        if ($this->containsAny($normalizedQuestion, ['thoi luong', 'may ngay', 'may dem', 'lich trinh'])) {
            return sprintf(
                "Tour %s có thời lượng %d ngày %d đêm.",
                $tour->name,
                (int) $tour->duration_days,
                (int) $tour->duration_nights
            );
        }

        if ($this->containsAny($normalizedQuestion, ['suc chua', 'toi da', 'bao nhieu khach', 'so khach'])) {
            return sprintf(
                "Tour %s có sức chứa tối đa %d khách.",
                $tour->name,
                (int) $tour->capacity
            );
        }

        if ($this->containsAny($normalizedQuestion, ['dat tour', 'dat cho', 'dat lich', 'muon dat'])) {
            return sprintf(
                "Bạn có thể mở chi tiết tour %s, chọn ngày khởi hành, số khách rồi bấm đặt tour để tiếp tục thanh toán.",
                $tour->name
            );
        }

        return sprintf(
            "Tour %s hiện đang mở bán tại %s.\n\nGiá: %s VNĐ/người\nThời lượng: %d ngày %d đêm\nSức chứa tối đa: %d khách\n\nBạn có thể mở chi tiết tour để xem lịch trình và đặt chỗ.",
            $tour->name,
            $tour->location ?: 'chưa cập nhật địa điểm',
            number_format((float) $tour->price, 0, ',', '.'),
            (int) $tour->duration_days,
            (int) $tour->duration_nights,
            (int) $tour->capacity
        );
    }

    private function replyWithMatchedTours(Collection $tours, string $normalizedQuestion): string
    {
        $intro = $this->containsAny($normalizedQuestion, ['gia', 'bao nhieu', 'chi phi'])
            ? 'Mình tìm thấy các tour phù hợp với câu hỏi của bạn:'
            : 'Mình tìm thấy các tour đang mở bán phù hợp với yêu cầu của bạn:';

        return $intro."\n\n"
            .$tours->take(5)->map(fn (Tour $tour) => $this->tourSummary($tour))->implode("\n")
            ."\n\nBạn muốn xem kỹ tour nào thì hãy nhắn đúng tên tour đó nhé.";
    }

    private function replyWithAvailableTours(Collection $tours): string
    {
        if ($tours->isEmpty()) {
            return 'Hiện tại Rẻo Cao Journeys chưa có tour nào đang mở bán. Bạn vui lòng quay lại sau hoặc liên hệ Admin để được hỗ trợ.';
        }

        return "Các tour đang mở bán hiện tại gồm:\n\n"
            .$tours->take(5)->map(fn (Tour $tour) => $this->tourSummary($tour))->implode("\n")
            ."\n\nBạn muốn mình tư vấn kỹ tour nào hơn?";
    }

    private function replyForCombination(
        array $places,
        Collection $tours,
        ?int $requestedDays,
        bool $requestedDaysOnlyAppliesToFirstPlace = false
    ): string {
        $lines = [];
        $available = collect();

        foreach ($places as $index => $place) {
            $matchingTours = $this->toursForPlace($tours, $place);

            if ($matchingTours->isEmpty()) {
                $lines[] = "- {$place}: hiện chưa có tour đang mở bán.";
                continue;
            }

            $tour = $matchingTours->first();
            $available->push($tour);

            $shouldCheckRequestedDays = $requestedDays !== null
                && (! $requestedDaysOnlyAppliesToFirstPlace || $index === 0);

            if ($shouldCheckRequestedDays && $requestedDays < (int) $tour->duration_days) {
                $lines[] = sprintf(
                    '- %s: có %s nhưng thời lượng hiện tại là %d ngày %d đêm, chưa có lựa chọn %d ngày.',
                    $place,
                    $tour->name,
                    (int) $tour->duration_days,
                    (int) $tour->duration_nights,
                    $requestedDays
                );
                continue;
            }

            $lines[] = $this->tourSummary($tour);
        }

        $hasCombinedTour = $available->contains(function (Tour $tour) use ($places): bool {
            $haystack = $this->normalizeText($tour->name.' '.$tour->location.' '.$tour->description);

            return collect($places)->every(fn (string $place) => str_contains($haystack, $this->normalizeText($place)));
        });

        $intro = $hasCombinedTour
            ? 'Mình tìm thấy tour phù hợp với các điểm bạn nhắc tới:'
            : 'Hiện tại Rẻo Cao Journeys chưa có tour kết hợp các điểm này trong một lịch trình.';

        $tail = $available->isNotEmpty()
            ? 'Bạn có thể đặt từng tour riêng theo dữ liệu hiện có, hoặc liên hệ Host/Admin để hỏi lịch trình ghép riêng.'
            : $this->suggestAvailableTours($tours);

        return $intro."\n\n".implode("\n", $lines)."\n\n".$tail;
    }

    private function toursForPlace(Collection $tours, string $place): Collection
    {
        $normalizedPlace = $this->normalizeText($place);

        return $tours->filter(function (Tour $tour) use ($normalizedPlace): bool {
            $haystack = $this->normalizeText($tour->name.' '.$tour->location);

            return str_contains($haystack, $normalizedPlace);
        })->values();
    }

    private function tourCandidatesInText(string $normalizedText, Collection $tours): Collection
    {
        if (trim($normalizedText) === '') {
            return collect();
        }

        return $tours
            ->map(fn (Tour $tour): array => [
                'tour' => $tour,
                'score' => $this->tourMatchScore($normalizedText, $tour),
            ])
            ->filter(fn (array $candidate): bool => $candidate['score'] >= 45)
            ->sortByDesc('score')
            ->values();
    }

    private function singleStrongTourCandidate(Collection $candidates): ?Tour
    {
        if ($candidates->isEmpty()) {
            return null;
        }

        if ($candidates->count() === 1) {
            return $candidates->first()['tour'];
        }

        $first = $candidates->first();
        $second = $candidates->skip(1)->first();

        if ($first['score'] >= 80 && $first['score'] >= $second['score'] + 20) {
            return $first['tour'];
        }

        return null;
    }

    private function tourMatchScore(string $normalizedText, Tour $tour): int
    {
        $score = 0;
        $normalizedName = $this->normalizeText((string) $tour->name);
        $normalizedSlug = $this->normalizeText(str_replace('-', ' ', (string) $tour->slug));

        if ($normalizedName !== '' && str_contains($normalizedText, $normalizedName)) {
            $score = max($score, 100);
        }

        if ($normalizedSlug !== '' && str_contains($normalizedText, $normalizedSlug)) {
            $score = max($score, 95);
        }

        $nameScore = $this->tokenOverlapScore($normalizedText, $normalizedName);

        if ($nameScore >= 45) {
            $score = max($score, $nameScore);
        }

        foreach ($this->locationCandidates((string) $tour->location) as $locationCandidate) {
            if (str_contains($normalizedText, $locationCandidate)) {
                $score = max($score, 60);
            }
        }

        return $score;
    }

    private function tokenOverlapScore(string $normalizedText, string $normalizedName): int
    {
        $nameTokens = $this->meaningfulTokens($normalizedName);

        if ($nameTokens === []) {
            return 0;
        }

        $textTokens = array_flip($this->meaningfulTokens($normalizedText));
        $matched = 0;

        foreach ($nameTokens as $token) {
            if (isset($textTokens[$token])) {
                $matched++;
            }
        }

        if ($matched < min(3, count($nameTokens))) {
            return 0;
        }

        return (int) floor(($matched / count($nameTokens)) * 100);
    }

    private function meaningfulTokens(string $normalizedText): array
    {
        $stopWords = [
            'tour', 'cho', 'toi', 'minh', 'ban', 've', 'nay', 'do', 'hien', 'tai',
            'gia', 'bao', 'nhieu', 'thong', 'tin', 'muon', 'xem', 'can', 'hoi',
        ];

        return collect(preg_split('/\s+/', $normalizedText) ?: [])
            ->filter(fn (string $token): bool => strlen($token) >= 2 && ! in_array($token, $stopWords, true))
            ->unique()
            ->values()
            ->all();
    }

    private function locationCandidates(string $location): array
    {
        $normalizedLocation = $this->normalizeText($location);

        if ($normalizedLocation === '') {
            return [];
        }

        return collect(array_merge([$normalizedLocation], explode(',', $location)))
            ->map(fn (string $part): string => $this->normalizeText($part))
            ->filter(fn (string $part): bool => strlen($part) >= 4)
            ->unique()
            ->values()
            ->all();
    }

    private function mentionedPlaces(string $normalizedText, Collection $tours): array
    {
        $places = [];
        $knownPlaces = [
            'Sa Pa' => ['sa pa', 'sapa'],
            'Hà Giang' => ['ha giang', 'hagiang'],
        ];

        foreach ($knownPlaces as $display => $aliases) {
            foreach ($aliases as $alias) {
                if (str_contains($normalizedText, $alias)) {
                    $places[$this->normalizeText($display)] = $display;
                    break;
                }
            }
        }

        foreach ($tours as $tour) {
            $location = trim((string) $tour->location);

            if ($location === '') {
                continue;
            }

            foreach ($this->locationCandidates($location) as $locationCandidate) {
                if (str_contains($normalizedText, $locationCandidate)) {
                    $places[$locationCandidate] = $locationCandidate;
                }
            }
        }

        return array_values($places);
    }

    private function requestedDays(string $normalizedQuestion): ?int
    {
        if (preg_match('/\b(\d{1,2})\s*ngay\b/', $normalizedQuestion, $matches) === 1) {
            return (int) $matches[1];
        }

        $words = [
            'mot ngay' => 1,
            'hai ngay' => 2,
            'ba ngay' => 3,
            'bon ngay' => 4,
            'nam ngay' => 5,
        ];

        foreach ($words as $word => $days) {
            if (str_contains($normalizedQuestion, $word)) {
                return $days;
            }
        }

        return null;
    }

    private function buildTourContext(Collection $tours): string
    {
        if ($tours->isEmpty()) {
            return '- Hiện chưa có tour nào đang mở bán.';
        }

        return $tours
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
    }

    private function suggestAvailableTours(Collection $tours): string
    {
        if ($tours->isEmpty()) {
            return 'Hiện hệ thống chưa có tour nào đang mở bán.';
        }

        return "Bạn có thể tham khảo các tour đang mở bán:\n"
            .$tours->take(4)->map(fn (Tour $tour) => $this->tourSummary($tour))->implode("\n");
    }

    private function tourSummary(Tour $tour): string
    {
        return sprintf(
            '- %s (%s): %s VNĐ/người, %d ngày %d đêm, tối đa %d khách.',
            $tour->name,
            $tour->location ?: 'chưa cập nhật địa điểm',
            number_format((float) $tour->price, 0, ',', '.'),
            (int) $tour->duration_days,
            (int) $tour->duration_nights,
            (int) $tour->capacity
        );
    }

    private function containsAny(string $text, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($text, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function isContextualTourQuestion(string $normalizedQuestion): bool
    {
        return $this->containsAny($normalizedQuestion, [
            'tour nay', 'tour do', 'tour vua roi', 'hanh trinh nay', 'gia bao nhieu',
            'may ngay', 'may dem', 'suc chua', 'dat nhu the nao', 'dat tour the nao',
        ]);
    }

    private function looksLikeSpecificTourLookup(string $normalizedQuestion): bool
    {
        if ($this->containsAny($normalizedQuestion, ['danh sach', 'tat ca', 'cac tour', 'nhung tour', 'dang mo ban'])) {
            return false;
        }

        return $this->containsAny($normalizedQuestion, [
            'cho toi tour', 'toi muon tour', 'tim tour', 'co tour', 'gioi thieu tour',
            'thong tin tour', 'tour ten',
        ]);
    }

    private function normalizeText(string $text): string
    {
        return Str::of($text)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9\s]+/', ' ')
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->toString();
    }
}
