<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Idea; // Import Model để code gọn hơn

class HomeController extends Controller
{
    public function index()
    {
        // QUAN TRỌNG: Thêm 'comments.user' và 'likes' vào hàm with
        $ideas = Idea::with([
            'user',
            'category',
            'likes',
            'comments.user' // Lấy thông tin người đã bình luận
        ])
        ->latest()
        ->paginate(10);

        return view('home', compact('ideas'));
    }
}
