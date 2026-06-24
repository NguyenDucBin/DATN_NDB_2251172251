<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tour;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy 2 bài viết mới nhất để hiển thị ở khối "Cẩm nang văn hóa"
        $posts = Post::where('status', 'published')->latest()->take(2)->get();

        // Lấy các Tour đang hoạt động và ĐÃ ĐƯỢC DUYỆT để hiển thị ở khối "Tour trải nghiệm"
        $tours = Tour::with('images')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('is_active', true)
            ->where('status', 'approved')
            ->latest()
            ->take(3)
            ->get();

        // Truyền mảng dữ liệu này sang view home.blade.php
        return view('home', compact('posts', 'tours'));
    }
}
