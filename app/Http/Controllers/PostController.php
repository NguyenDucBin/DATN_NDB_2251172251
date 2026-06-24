<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Hiển thị danh sách toàn bộ Cẩm nang văn hóa
     */
    public function index()
    {
        // Lấy danh sách các bài viết đã xuất bản, sắp xếp mới nhất, phân trang 9 bài / trang
        $posts = Post::where('status', 'published')
                     ->latest()
                     ->paginate(9);

        return view('magazine', compact('posts'));
    }

    /**
     * Hiển thị chi tiết 1 bài viết
     */
    public function show($slug)
    {
        $post = Post::where('slug', $slug)
                    ->where('status', 'published')
                    ->firstOrFail();

        return view('post-detail', compact('post'));
    }
}