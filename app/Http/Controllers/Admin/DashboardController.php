<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Idea;
use App\Models\User;
use App\Models\Department;
use App\Models\AcademicYear;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Thống kê tổng quan
        $totalIdeas = Idea::count();
        $totalUsers = User::count();
        $totalDepts = Department::count();
        $totalAcademicYears = AcademicYear::count();

        // 2. Lấy 5 ý tưởng mới nhất
        $latestIdeas = Idea::with('user')
            ->latest()
            ->take(5)
            ->get();

        // 3. Lấy 5 ý tưởng phổ biến nhất (Most Popular)
        // Tính theo công thức điểm: Like (+1) và Dislike (-1)
        $popularIdeas = Idea::with(['user.department'])
            ->withCount([
                'likes as likes_count' => function ($q) { $q->where('type', 1); },
                'likes as dislikes_count' => function ($q) { $q->where('type', 0); },
                'comments' // Vẫn đếm số lượng comment để hiển thị ra View
            ])
            ->orderByRaw('(likes_count - dislikes_count) DESC') // Sắp xếp theo điểm giảm dần
            ->take(5)
            ->get();

        // 4. Ý tưởng có comment gần nhất (Recently Active)
        $recentlyCommentedIdeas = Idea::with(['user.department'])
            ->withMax('comments', 'created_at')
            ->has('comments')
            ->orderByDesc('comments_max_created_at')
            ->take(5)
            ->get();

        // 5. Ý tưởng có lượt xem cao nhất (Most Viewed)
        $mostViewedIdeas = Idea::with(['user.department'])
            ->orderByRaw('COALESCE(view_count, 0) DESC') // COALESCE để tránh lỗi khi view_count là null
            ->orderByDesc('created_at') // Nếu lượt xem bằng nhau thì ưu tiên bài mới hơn
            ->take(5)
            ->get();

        // Return view
        return view('admin.dashboard', compact(
            'totalIdeas',
            'totalUsers',
            'totalDepts',
            'totalAcademicYears',
            'latestIdeas',
            'popularIdeas',
            'recentlyCommentedIdeas',
            'mostViewedIdeas'
        ));
    }
}
