<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tour;

class TourController extends Controller
{
    public function index(Request $request)
    {
        $query = Tour::with('images')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('status', 'approved')
            ->where('is_active', true);

        // Search by keyword
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('location', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%'.$request->string('location')->trim().'%');
        }

        // Filter by duration
        if ($request->filled('duration')) {
            $duration = $request->duration;
            if ($duration == '1-3') {
                $query->where('duration_days', '<=', 3);
            } elseif ($duration == '4-7') {
                $query->whereBetween('duration_days', [4, 7]);
            } elseif ($duration == '8+') {
                $query->where('duration_days', '>=', 8);
            }
        }

        // Filter by price range
        if ($request->filled('min_price') && is_numeric($request->min_price)) {
            $query->where('price', '>=', max(0, (float) $request->min_price));
        }
        if ($request->filled('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', max(0, (float) $request->max_price));
        }

        // Sort
        if ($request->filled('sort')) {
            if ($request->sort == 'price_asc') {
                $query->orderBy('price', 'asc');
            } elseif ($request->sort == 'price_desc') {
                $query->orderBy('price', 'desc');
            }
        } else {
            $query->latest();
        }

        $tours = $query->paginate(12)->withQueryString();

        return view('tours.index', compact('tours'));
    }

    public function show($slug)
    {
        // Lấy thông tin tour kèm theo thông tin Chủ nhà (Host)
        // Sử dụng firstOrFail() để tự động văng lỗi 404 nếu không tìm thấy tour
        $tour = Tour::with(['host', 'images', 'reviews.user'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('slug', $slug)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->firstOrFail();

        $isFavorite = auth()->check()
            && auth()->user()->favoriteTours()->whereKey($tour->id)->exists();

        $canReview = auth()->check() && auth()->user()->bookings()
            ->where('tour_id', $tour->id)
            ->where('status', 'completed')
            ->exists();
        $userReview = auth()->check()
            ? $tour->reviews->firstWhere('user_id', auth()->id())
            : null;

        return view('tour-detail', compact('tour', 'isFavorite', 'canReview', 'userReview'));
    }
}
