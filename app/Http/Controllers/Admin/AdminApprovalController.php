<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Support\Facades\DB;

class AdminApprovalController extends Controller
{
    public function index()
    {
        $pendingTours = Tour::where('status', 'pending')->with('host')->latest()->paginate(15, ['*'], 'pending_page');
        $approvedTours = Tour::where('status', '!=', 'pending')->with('host')->latest()->paginate(15, ['*'], 'history_page');
        
        return view('admin.approvals.index', compact('pendingTours', 'approvedTours'));
    }

    public function approve(Tour $tour)
    {
        $updated = DB::transaction(function () use ($tour) {
            $locked = Tour::whereKey($tour->id)->lockForUpdate()->firstOrFail();

            if ($locked->status !== 'pending') {
                return false;
            }

            $locked->update(['status' => 'approved', 'is_active' => true]);
            return true;
        }, 3);

        if (! $updated) {
            return back()->with('error', 'Tour này đã được xử lý trước đó.');
        }

        return redirect()->back()->with('success', "Đã phê duyệt tour: {$tour->name}");
    }

    public function reject(Tour $tour)
    {
        $updated = DB::transaction(function () use ($tour) {
            $locked = Tour::whereKey($tour->id)->lockForUpdate()->firstOrFail();

            if ($locked->status !== 'pending') {
                return false;
            }

            $locked->update(['status' => 'rejected', 'is_active' => false]);
            return true;
        }, 3);

        if (! $updated) {
            return back()->with('error', 'Tour này đã được xử lý trước đó.');
        }

        return redirect()->back()->with('success', "Đã từ chối tour: {$tour->name}");
    }
}
