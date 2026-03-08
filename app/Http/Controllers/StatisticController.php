<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Idea;
use App\Models\Department;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class StatisticController extends Controller
{
    public function index()
    {
        // BẢO MẬT: Chỉ Admin (1) và QA Manager (2) mới được truy cập
        if (!in_array(Auth::user()->role_id, [1, 2])) {
            return redirect()->route('home')->with('error', 'Access Denied: Bạn không có quyền xem trang Thống kê!');
        }

        // 1. TỔNG SỐ Ý TƯỞNG
        $totalIdeas = Idea::count();

        // 2. THỐNG KÊ THEO PHÒNG BAN (Số lượng, Tỷ lệ %, Số người đóng góp)
        $departmentsData = Department::withCount('ideas')->get()->map(function($dept) use ($totalIdeas) {
            $percentage = $totalIdeas > 0 ? round(($dept->ideas_count / $totalIdeas) * 100, 1) : 0;

            // Đếm số lượng người dùng khác nhau (unique) đã đăng bài trong khoa này
            $contributors = Idea::where('department_id', $dept->id)->distinct('user_id')->count('user_id');

            return (object) [
                'name' => $dept->department_name ?? $dept->name,
                'ideas_count' => $dept->ideas_count,
                'percentage' => $percentage,
                'contributors' => $contributors
            ];
        });

        // 3. BÁO CÁO NGOẠI LỆ
        // a. Ý tưởng không có bình luận
        $ideasWithoutComments = Idea::doesntHave('comments')->with(['user', 'department'])->latest()->get();

        // b. Ý tưởng ẩn danh
        $anonymousIdeas = Idea::where('is_anonymous', 1)->with(['user', 'department'])->latest()->get();

        // c. Bình luận ẩn danh
        $anonymousComments = Comment::where('is_anonymous', 1)->with(['user', 'idea'])->latest()->get();

        return view('admin.statistics', compact(
            'totalIdeas', 'departmentsData', 'ideasWithoutComments', 'anonymousIdeas', 'anonymousComments'
        ));
    }
}
