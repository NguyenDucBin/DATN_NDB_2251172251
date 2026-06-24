<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Booking;
use App\Models\Coupon;
use App\Models\Favorite;
use App\Models\Tour;
use App\Services\BookingWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;

class ProfileController extends Controller
{
    public function __construct(private readonly BookingWorkflowService $bookingWorkflow)
    {
    }

    /**
     * Trang Tổng quan: Lời chào, thống kê booking, danh sách tour gần đây
     */
    public function overview(Request $request): View
    {
        $user = $request->user();
        $user->loadCount([
            'bookings as total_bookings_count',
            'bookings as pending_bookings_count' => fn ($query) => $query->where('status', 'pending'),
            'bookings as confirmed_bookings_count' => fn ($query) => $query->where('status', 'confirmed'),
            'bookings as completed_bookings_count' => fn ($query) => $query->where('status', 'completed'),
            'bookings as cancelled_bookings_count' => fn ($query) => $query->where('status', 'cancelled'),
        ]);

        $stats = [
            'total' => $user->total_bookings_count,
            'pending' => $user->pending_bookings_count,
            'confirmed' => $user->confirmed_bookings_count,
            'completed' => $user->completed_bookings_count,
            'cancelled' => $user->cancelled_bookings_count,
        ];

        $recentBookings = $user->bookings()
            ->with(['tour', 'refunds'])
            ->latest()
            ->paginate(8, ['*'], 'booking_page')
            ->withQueryString();

        return view('profile.overview', compact('user', 'stats', 'recentBookings'));
    }

    /**
     * Trang Thông tin cá nhân
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Cập nhật thông tin cá nhân
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.overview')->with('status', 'profile-updated');
    }

    /**
     * Upload avatar
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $user = $request->user();

        // Xóa avatar cũ nếu có
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Lưu avatar mới
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'avatar-updated');
    }

    /**
     * Trang Danh sách Tour yêu thích
     */
    public function favorites(Request $request): View
    {
        $favorites = $request->user()->favoriteTours()
            ->with('images')
            ->latest('favorites.created_at')
            ->paginate(12);
        return view('profile.favorites', compact('favorites'));
    }

    /**
     * Toggle yêu thích Tour
     */
    public function toggleFavorite(Request $request, Tour $tour): RedirectResponse
    {
        $user = $request->user();

        if ($tour->status !== 'approved' || ! $tour->is_active) {
            return back()->with('error', 'Tour này hiện không thể thêm vào danh sách yêu thích.');
        }

        $existing = Favorite::where('user_id', $user->id)->where('tour_id', $tour->id)->first();

        if ($existing) {
            $existing->delete();
            $message = 'Đã bỏ yêu thích tour "' . $tour->name . '"';
        } else {
            Favorite::create(['user_id' => $user->id, 'tour_id' => $tour->id]);
            $message = 'Đã thêm tour "' . $tour->name . '" vào danh sách yêu thích';
        }

        return back()->with('status', $message);
    }

    /**
     * Trang Mã giảm giá
     */
    public function coupons(Request $request): View
    {
        $coupons = Coupon::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('valid_until')
                      ->orWhere('valid_until', '>=', now());
            })
            ->where(function ($query) {
                $query->whereNull('usage_limit')
                      ->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->latest()
            ->get();

        return view('profile.coupons', compact('coupons'));
    }

    /**
     * Trang Bảo mật tài khoản
     */
    public function security(Request $request): View
    {
        return view('profile.security');
    }

    public function requestRefund(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('requestRefund', $booking);

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->bookingWorkflow->requestRefund(
                $booking,
                $validated['reason'] ?: 'Khách hàng yêu cầu hoàn tiền từ tài khoản.',
            );
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Đã gửi yêu cầu hoàn tiền. Admin sẽ xử lý yêu cầu của bạn.');
    }

    /**
     * Xóa tài khoản
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->bookings()->exists() || $user->hostedTours()->exists()) {
            return Redirect::route('profile.security')->with('error', 'Tài khoản đã có lịch sử tour hoặc booking nên không thể xóa. Bạn có thể liên hệ Admin để khóa tài khoản.');
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
