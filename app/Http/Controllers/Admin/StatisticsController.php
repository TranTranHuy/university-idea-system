<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Idea;
use App\Models\Department;
use App\Models\User;
use App\Models\Comment; // Nhớ thêm dòng use này ở trên cùng nhé
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function index()
    {
        // 1. Data cho Biểu đồ (Đếm Idea theo từng Khoa)
        $departments = Department::withCount('ideas')->get();
        $deptNamesArray = $departments->pluck('department_name')->toArray();
        $ideasDataArray = $departments->pluck('ideas_count')->toArray();

        // 2. Data cho Bảng: Contributors by Dept (Đếm số User trong từng Khoa)
        $contributorsByDept = Department::withCount('users')->get();

        // 3. Ideas Without Comment (Lấy 5 ý tưởng chưa có ai bình luận)
        $ideasWithoutComments = Idea::doesntHave('comments')->latest()->take(5)->get();

        // 4. Thống kê ẩn danh
        $anonymousIdeasCount = Idea::where('is_anonymous', 1)->count();

        // ĐÃ SỬA: Mở khóa truy vấn đếm số comment ẩn danh
        $anonymousCommentsCount = Comment::where('is_anonymous', 1)->count();

        return view('admin.statistics', compact(
            'deptNamesArray',
            'ideasDataArray',
            'contributorsByDept',
            'ideasWithoutComments',
            'anonymousIdeasCount',
            'anonymousCommentsCount'
        ));
    }
}
