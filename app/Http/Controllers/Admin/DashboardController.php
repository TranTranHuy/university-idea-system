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

        // 3. Lấy 5 ý tưởng phổ biến nhất (theo likes)
        $popularIdeas = Idea::with(['user.department'])
            ->withCount(['likes', 'comments'])
            ->orderBy('likes_count', 'desc')
            ->take(5)
            ->get();

        // 4. Ý tưởng có comment gần nhất
        $recentlyCommentedIdeas = Idea::with(['user.department'])
            ->withMax('comments', 'created_at')
            ->has('comments')
            ->orderByDesc('comments_max_created_at')
            ->take(5)
            ->get();

        // 5. Ý tưởng có lượt xem cao nhất
        $mostViewedIdeas = Idea::with(['user.department'])
            ->orderBy('view_count', 'desc')
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
