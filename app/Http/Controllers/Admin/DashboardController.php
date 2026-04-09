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
        // Mình vẫn dùng latest(), giao diện sẽ tự bắt cột view_count nếu bạn đã tạo trong DB
        $latestIdeas = Idea::with('user')
            ->latest()
            ->take(5)
            ->get();

        // 3. Lấy 5 ý tưởng phổ biến nhất (Dựa trên số lượt Like/Upvote)
        // Chú ý: Cập nhật lại query này để đếm số Like và Comment
        $popularIdeas = Idea::with(['user.department'])
            ->withCount([
                // Đếm số likes (Giả sử bạn có relationship 'likes')
                'likes',
                // HOẶC nếu bạn dùng bảng reactions như comment cũ thì mở comment dòng dưới và xóa dòng trên:
                // 'reactions as likes_count' => function($query) { $query->where('reaction_type', 'like'); },

                // Đếm số comments
                'comments'
            ])
            // Sắp xếp giảm dần theo số lượng Like (cột likes_count vừa được withCount tạo ra)
            ->orderBy('likes_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalIdeas', 'totalUsers', 'totalDepts', 'totalAcademicYears',
            'latestIdeas', 'popularIdeas'
        ));
    }
}
