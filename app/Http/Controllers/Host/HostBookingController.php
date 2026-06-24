<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingWorkflowService;
use RuntimeException;

class HostBookingController extends Controller
{
    public function __construct(private readonly BookingWorkflowService $workflow) {}

    public function index()
    {
        $hostId = auth()->id();

        $bookings = Booking::whereHas('tour', function ($q) use ($hostId) {
            $q->where('host_id', $hostId);
        })
            ->with(['user', 'tour', 'refunds'])
            ->latest()
            ->paginate(15);

        return view('host.bookings.index', compact('bookings'));
    }

    public function confirm(Booking $booking)
    {
        $booking->loadMissing('tour');
        $this->authorize('manage', $booking);

        try {
            $this->workflow->confirm($booking);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Đã xác nhận booking cho khách.');
    }

    public function complete(Booking $booking)
    {
        $booking->loadMissing('tour');
        $this->authorize('manage', $booking);

        try {
            $this->workflow->complete($booking);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Đã đánh dấu booking hoàn tất.');
    }

    public function cancel(Booking $booking)
    {
        $booking->loadMissing('tour');
        $this->authorize('manage', $booking);

        try {
            $refundCreated = $this->workflow->cancel($booking, 'Host đã hủy booking. Yêu cầu Admin hoàn tiền cho khách.');
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', $refundCreated
            ? 'Đã hủy booking và tạo yêu cầu hoàn tiền cho khách.'
            : 'Đã hủy booking.');
    }
}
