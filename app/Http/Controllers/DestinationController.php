<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    /**
     * Danh sách điểm đến cố định với metadata.
     */
    private function getDestinations()
    {
        return [
            'sa-pa' => [
                'name' => 'Sa Pa',
                'subtitle' => 'Thị trấn trong sương',
                'description' => 'Sa Pa nổi tiếng với những thửa ruộng bậc thang tuyệt đẹp, khí hậu mát mẻ quanh năm và nền văn hóa đa dạng của các dân tộc H\'Mông, Dao, Tày. Đây là điểm đến lý tưởng cho những ai yêu thích thiên nhiên và trải nghiệm văn hóa bản địa.',
                'image' => asset('images/static/destination-sa-pa.jpg'),
                'icon' => 'fa-solid fa-cloud',
                'keywords' => ['sa pa', 'sapa', 'Sa Pa', 'Sapa'],
            ],
            'ha-giang' => [
                'name' => 'Hà Giang',
                'subtitle' => 'Cao nguyên đá hùng vĩ',
                'description' => 'Hà Giang – vùng đất địa đầu Tổ quốc với Cao nguyên đá Đồng Văn được UNESCO công nhận là Công viên địa chất toàn cầu. Cung đường đèo Mã Pí Lèng huyền thoại, sông Nho Quế xanh ngọc bích và những bản làng nguyên sơ chờ bạn khám phá.',
                'image' => asset('images/static/destination-ha-giang.jpg'),
                'icon' => 'fa-solid fa-mountain',
                'keywords' => ['hà giang', 'ha giang', 'Hà Giang', 'Ha Giang'],
            ],
            'mu-cang-chai' => [
                'name' => 'Mù Cang Chải',
                'subtitle' => 'Mùa vàng non cao',
                'description' => 'Mù Cang Chải là thiên đường ruộng bậc thang với những thửa ruộng trải dài trên sườn đồi, đẹp nhất vào mùa lúa chín tháng 9-10. Nơi đây còn giữ nguyên nét hoang sơ và văn hóa truyền thống của người H\'Mông.',
                'image' => asset('images/static/destination-mu-cang-chai.jpg'),
                'icon' => 'fa-solid fa-leaf',
                'keywords' => ['mù cang chải', 'mu cang chai', 'Mù Cang Chải', 'Mu Cang Chai'],
            ],
            'cao-bang' => [
                'name' => 'Cao Bằng',
                'subtitle' => 'Tuyệt tác thác Bản Giốc',
                'description' => 'Cao Bằng nổi tiếng với thác Bản Giốc – ngọn thác tự nhiên lớn nhất Đông Nam Á, cùng động Ngườm Ngao kỳ bí. Vùng đất biên cương này còn lưu giữ nhiều di tích lịch sử cách mạng và văn hóa dân tộc Tày, Nùng.',
                'image' => asset('images/static/destination-cao-bang.webp'),
                'icon' => 'fa-solid fa-water',
                'keywords' => ['cao bằng', 'cao bang', 'Cao Bằng', 'Cao Bang', 'bản giốc', 'ban gioc'],
            ],
            'moc-chau' => [
                'name' => 'Mộc Châu',
                'subtitle' => 'Thảo nguyên vẫy gọi',
                'description' => 'Mộc Châu – cao nguyên xanh mướt với những đồi chè bát ngát, vườn mận trắng muốt mùa xuân và thảo nguyên bao la. Đây là nơi lý tưởng để nghỉ dưỡng, cắm trại và khám phá văn hóa Thái, H\'Mông.',
                'image' => asset('images/static/destination-moc-chau.jpg'),
                'icon' => 'fa-solid fa-tree',
                'keywords' => ['mộc châu', 'moc chau', 'Mộc Châu', 'Moc Chau'],
            ],
        ];
    }

    /**
     * Hiển thị trang điểm đến với các tour liên quan.
     */
    public function show(string $slug)
    {
        $destinations = $this->getDestinations();

        if (!isset($destinations[$slug])) {
            abort(404);
        }

        $destination = $destinations[$slug];

        // Tìm tours có location chứa bất kỳ từ khóa nào
        $tours = Tour::with('images', 'host')
            ->where('status', 'approved')
            ->where('is_active', true)
            ->where(function ($query) use ($destination) {
                foreach ($destination['keywords'] as $keyword) {
                    $query->orWhere('location', 'LIKE', '%' . $keyword . '%');
                }
            })
            ->latest()
            ->paginate(12);

        $favoriteTourIds = auth()->check()
            ? auth()->user()->favoriteTours()->whereKey($tours->pluck('id'))->pluck('tours.id')->all()
            : [];

        return view('destination', compact('destination', 'tours', 'slug', 'favoriteTourIds'));
    }
}
