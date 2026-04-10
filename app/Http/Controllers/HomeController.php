<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Lấy tham số 'sort' từ URL, mặc định là 'default'
        $sortQuery = $request->input('sort', 'default');

        // Tạo query cơ bản chung cho tất cả các bộ lọc
        $query = Idea::with(['user.department', 'category']);

        // Xử lý logic lọc dựa trên tham số 'sort'
        switch ($sortQuery) {
            case 'popular':
                // MOST POPULAR: Lọc theo điểm (Like = +1, Dislike = -1)
                // Đếm số like (type = 1) và số dislike (type = 0)
                $ideas = $query->withCount([
                                    'likes as likes_count' => function ($q) { $q->where('type', 1); },
                                    'likes as dislikes_count' => function ($q) { $q->where('type', 0); }
                               ])
                               // Sắp xếp theo công thức: Điểm = (Tổng Like - Tổng Dislike) giảm dần
                               ->orderByRaw('(likes_count - dislikes_count) DESC')
                               ->paginate(10);
                $sortTitle = 'Most Popular Ideas';
                break;

            case 'newest_comments':
                // NEWEST COMMENTS (Đổi tên từ Recently Active)
                // Lấy các bài viết có comment, sắp xếp theo thời gian của comment mới nhất
                $ideas = $query->withMax('comments', 'created_at')
                               ->has('comments')
                               ->orderByDesc('comments_max_created_at')
                               ->paginate(10);
                $sortTitle = 'Newest Comments';
                break;

            case 'viewed':
                // MOST VIEWED: Sắp xếp theo lượt xem giảm dần
                $ideas = $query->orderByDesc('view_count')
                               ->paginate(10);
                $sortTitle = 'Most Viewed Ideas';
                break;

            case 'latest':
                // Mới nhất
                $ideas = $query->latest()->paginate(10);
                $sortTitle = 'Latest Ideas';
                break;

            case 'default':
            default:
                // Mặc định (Default)
                $ideas = $query->latest()->paginate(10);
                $sortTitle = 'All Ideas';
                break;
        }

        // Truyền thêm query string vào link phân trang để không bị lỗi khi qua trang 2, 3
        $ideas->appends(['sort' => $sortQuery]);

        return view('home', compact('ideas', 'sortQuery', 'sortTitle'));
    }
}
