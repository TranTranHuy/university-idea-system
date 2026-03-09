<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Idea;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function index()
    {
        // 1. Data cho Biểu đồ (Đếm Idea theo từng Khoa)
        // Yêu cầu: Model Department phải có hàm ideas()
        $departments = Department::withCount('ideas')->get();
        $deptNamesArray = $departments->pluck('department_name')->toArray();
        $ideasDataArray = $departments->pluck('ideas_count')->toArray();

        // 2. Data cho Bảng: Contributors by Dept (Đếm số User trong từng Khoa)
        // Yêu cầu: Model Department phải có hàm users()
        $contributorsByDept = Department::withCount('users')->get();

        // 3. Ideas Without Comment (Lấy 5 ý tưởng chưa có ai bình luận)
        // Yêu cầu: Model Idea phải có hàm comments()
        // Dùng tạm whereDoesntHave, nếu ní chưa có bảng Comment thì comment đoạn này lại để tránh lỗi 500
        $ideasWithoutComments = Idea::doesntHave('comments')->latest()->take(5)->get();

        // 4. Thống kê ẩn danh
        $anonymousIdeasCount = Idea::where('is_anonymous', 1)->count();

        // Nếu ní đã tạo Model Comment và có cột is_anonymous thì mở dòng dưới ra
        // $anonymousCommentsCount = \App\Models\Comment::where('is_anonymous', 1)->count();
        $anonymousCommentsCount = 0; // Tạm để 0 cho khỏi lỗi

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
