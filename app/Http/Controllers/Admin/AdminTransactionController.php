<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingWorkflowService;
use RuntimeException;

class AdminTransactionController extends Controller
{
    public function __construct(private readonly BookingWorkflowService $workflow) {}

    public function index()
    {
        $transactions = Booking::with(['user', 'tour', 'refunds'])->latest()->paginate(20);

        $totalRevenue = Booking::where('payment_status', 'paid')
            ->whereIn('status', ['confirmed', 'completed'])
            ->sum('total_price');

        $totalPending = Booking::where(function ($query) {
            $query->where('status', 'pending')
                ->orWhereIn('payment_status', ['pending', 'unpaid']);
        })
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->sum('total_price');

        return view('admin.transactions.index', compact('transactions', 'totalRevenue', 'totalPending'));
    }

    public function markPaid(Booking $booking)
    {
        try {
            $updated = $this->workflow->markPaid($booking);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        if (! $updated) {
            return back()->with('success', 'Booking này đã được ghi nhận thanh toán trước đó.');
        }

        return back()->with('success', 'Đã xác nhận booking đã thanh toán. Host có thể xác nhận lịch cho khách.');
    }

    public function cancel(Booking $booking)
    {
        try {
            $refundCreated = $this->workflow->cancel($booking, 'Admin đã hủy booking. Yêu cầu hoàn tiền cho khách.');
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', $refundCreated
            ? 'Đã hủy booking và tạo yêu cầu hoàn tiền.'
            : 'Đã hủy booking.');
    }
}
