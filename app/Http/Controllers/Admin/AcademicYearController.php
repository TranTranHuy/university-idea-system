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
        return view('admin.academic_years.index', compact('years'));
    }

    // 2. Hiện form thêm mới
    public function create()
    {
        return view('admin.academic_years.create');
    }

    // 2. Xử lý logic Thêm mới (Store)
    public function store(Request $request)
    {
        // VALIDATE: Phần quan trọng nhất của Backend
        $request->validate([
            'name' => 'required|string|unique:academic_years',
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

        return redirect()->route('admin.academic-years.index')
                         ->with('success', 'Tạo kỳ học mới thành công!');
    }

    // 4. Hiện form sửa
    public function edit($id)
    {
        $academicYear = AcademicYear::findOrFail($id);
        return view('admin.academic_years.edit', compact('academicYear'));
    }

    // 5. Xử lý cập nhật
    public function update(Request $request, $id)
    {
        $year = AcademicYear::findOrFail($id);

        $request->validate([
            'name' => 'required|string|unique:academic_years,name,'.$id, // Cho phép trùng tên chính nó
            'start_date' => 'required|date',
            'closure_date' => 'required|date|after:start_date',
            'final_closure_date' => 'required|date|after:closure_date',
        ]);

        $year->update($request->all());

        return redirect()->route('admin.academic-years.index')
                         ->with('success', 'Cập nhật thành công!');
    }

    // 3. Xử lý logic Xóa
    public function destroy($id)
    {
        // Logic: Có thể thêm check nếu kỳ học đã có Idea thì không cho xóa
        $year = AcademicYear::findOrFail($id);
        if ($year->ideas()->count() > 0) {
            return back()->with('error', 'Không thể xóa kỳ học này vì đã có sinh viên nộp bài!');
        }

        $year->delete();
        return back()->with('success', 'Đã xóa kỳ học!');
    }
}