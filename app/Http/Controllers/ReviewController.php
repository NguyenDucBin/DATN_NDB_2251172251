<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function store(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $tour, $validated) {
            Tour::whereKey($tour->id)->lockForUpdate()->firstOrFail();

            $hasCompletedBooking = Booking::where('user_id', $request->user()->id)
                ->where('tour_id', $tour->id)
                ->where('status', 'completed')
                ->exists();

            abort_unless($hasCompletedBooking, 403, 'Bạn chỉ có thể đánh giá tour đã hoàn thành.');

            Review::updateOrCreate(
                ['user_id' => $request->user()->id, 'tour_id' => $tour->id],
                $validated,
            );
        }, 3);

        return back()->with('success', 'Đánh giá của bạn đã được lưu.');
    }
}
