<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Services\BookingWorkflowService;
use Illuminate\Http\Request;
use RuntimeException;

class RefundController extends Controller
{
    public function __construct(private readonly BookingWorkflowService $workflow)
    {
    }

    public function index()
    {
        $refunds = Refund::with(['booking.user', 'booking.tour'])->latest()->paginate(15);
        return view('admin.refunds.index', compact('refunds'));
    }

    public function process(Request $request, Refund $refund)
    {
        $validated = $request->validate([
            'status' => 'required|in:processed,rejected',
        ]);

        try {
            $this->workflow->processRefund($refund, $validated['status']);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->back()->with('success', 'Đã xử lý yêu cầu hoàn tiền thành công.');
    }
}
