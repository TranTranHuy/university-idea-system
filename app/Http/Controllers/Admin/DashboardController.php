<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Idea;
use App\Models\User;
use App\Models\Department;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Thống kê tổng quan (4 thẻ trên đầu)
        $totalIdeas = Idea::count();
        $totalUsers = User::count();
        $totalDepts = Department::count();
        $totalAcademicYears = AcademicYear::count();

        // 2. Lấy 5 ý tưởng mới nhất (Latest Ideas)
        $latestIdeas = Idea::with('user')->latest()->take(5)->get();

        // 3. Lấy 5 ý tưởng phổ biến nhất (Dựa trên số lượt Upvote/Like)
        // Chỗ này tui giả sử ní có bảng reactions/votes, nếu chưa có thì lấy theo view hoặc comment
        $popularIdeas = Idea::with(['user.department'])
            // ->withCount(['reactions as upvotes_count' => function($query) {
            //     // $query->where('reaction_type', 'like'); // Hoặc logic tính điểm của ní
            // }])
            // ->orderBy('upvotes_count', 'desc')
            // ->take(5)
            // ->get();
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalIdeas', 'totalUsers', 'totalDepts', 'totalAcademicYears',
            'latestIdeas', 'popularIdeas'
        ));
    }
}
