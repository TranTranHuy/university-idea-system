<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <--- Nhớ use cái này để chạy Query

class DashboardController extends Controller
{
    public function index()
    {
        // --- THỐNG KÊ 1: Số lượng Idea theo từng Phòng Ban (Department) ---
        // Logic: Join bảng Ideas -> Users -> Departments để đếm
        $ideasByDept = DB::table('ideas')
            ->join('users', 'ideas.user_id', '=', 'users.id')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->select('departments.name', DB::raw('count(*) as total'))
            ->groupBy('departments.name')
            ->get();

        // Chuẩn bị dữ liệu cho Frontend (Tách thành 2 mảng riêng biệt)
        $deptLabels = $ideasByDept->pluck('name'); // ['IT', 'Business', ...]
        $deptData   = $ideasByDept->pluck('total'); // [10, 5, ...]


        // --- THỐNG KÊ 2: (Nâng cao) Số người đóng góp theo từng phòng ban ---
        // Logic: Đếm distinct user_id trong bảng ideas
        $contributorsByDept = DB::table('ideas')
            ->join('users', 'ideas.user_id', '=', 'users.id')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->select('departments.name', DB::raw('count(DISTINCT ideas.user_id) as total_users'))
            ->groupBy('departments.name')
            ->get();
            
        $contributorData = $contributorsByDept->pluck('total_users');


        // Trả dữ liệu về View
        return view('admin.dashboard', compact(
            'deptLabels', 
            'deptData', 
            'contributorData'
        ));
    }
}