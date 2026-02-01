<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    // 1. Lấy danh sách để hiển thị
    public function index()
    {
        $years = AcademicYear::latest()->paginate(10);
        // Trả về View rỗng kèm dữ liệu. Hạnh sẽ code giao diện ở đây.
        return view('admin.academic-years.index', compact('years'));
    }

    // 2. Xử lý logic Thêm mới (Store)
    public function store(Request $request)
    {
        // VALIDATE: Phần quan trọng nhất của Backend
        $request->validate([
            'name' => 'required|unique:academic_years',
            'start_date' => 'required|date',
            // Logic: Ngày đóng nộp Idea phải sau ngày bắt đầu
            'closure_date' => 'required|date|after:start_date', 
            // Logic: Ngày đóng Comment phải sau ngày đóng Idea
            'final_closure_date' => 'required|date|after:closure_date', 
        ], [
            // Custom thông báo lỗi tiếng Việt cho dễ hiểu (Optional)
            'closure_date.after' => 'Ngày đóng nộp Idea phải diễn ra sau ngày bắt đầu!',
            'final_closure_date.after' => 'Ngày đóng tương tác phải là ngày cuối cùng!',
        ]);

        // Lưu vào Database
        AcademicYear::create($request->all());

        return redirect()->back()->with('success', 'Đã tạo kỳ học mới thành công!');
    }

    // 3. Xử lý logic Xóa
    public function destroy($id)
    {
        // Logic: Có thể thêm check nếu kỳ học đã có Idea thì không cho xóa
        $year = AcademicYear::findOrFail($id);
        $year->delete();
        
        return redirect()->back()->with('success', 'Đã xóa thành công!');
    }
}