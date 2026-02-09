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

        // ============================================================
        // 2. LOGIC KIỂM TRA TRÙNG LỊCH (MỚI THÊM VÀO)
        // ============================================================
        $start = $request->start_date;
        $end = $request->final_closure_date;

        // Kiểm tra xem khoảng thời gian [Start -> Final Closure] có dính dáng đến kỳ nào khác không
        $overlap = AcademicYear::where(function ($query) use ($start, $end) {
            $query->whereBetween('start_date', [$start, $end])
                  ->orWhereBetween('final_closure_date', [$start, $end])
                  ->orWhere(function ($q) use ($start, $end) {
                      $q->where('start_date', '<=', $start)
                        ->where('final_closure_date', '>=', $end);
                  });
        })->exists();

        // Nếu trùng ($overlap = true) -> Báo lỗi và trả về form cũ
        if ($overlap) {
            return back()->withErrors(['start_date' => 'Lỗi: Khoảng thời gian này bị trùng với một kỳ học đã có! Vui lòng chọn ngày khác.'])
                         ->withInput();
        }
        // ============================================================

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

        // 1. VALIDATE CƠ BẢN (Giữ nguyên)
        $request->validate([
            'name' => 'required|string|unique:academic_years,name,'.$id, // Cho phép trùng tên chính nó
            'start_date' => 'required|date',
            'closure_date' => 'required|date|after:start_date',
            'final_closure_date' => 'required|date|after:closure_date',
        ]);

        // ============================================================
        // 2. LOGIC KIỂM TRA TRÙNG LỊCH (MỚI THÊM VÀO)
        // ============================================================
        $start = $request->start_date;
        $end = $request->final_closure_date;

        // Kiểm tra xem có kỳ nào khác bị trùng không
        // QUAN TRỌNG: Phải có ->where('id', '!=', $id) để nó không tự so sánh với chính nó
        $overlap = AcademicYear::where('id', '!=', $id) 
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                      ->orWhereBetween('final_closure_date', [$start, $end])
                      ->orWhere(function ($q) use ($start, $end) {
                          $q->where('start_date', '<=', $start)
                            ->where('final_closure_date', '>=', $end);
                      });
            })->exists();

        // Nếu trùng -> Báo lỗi và trả lại form
        if ($overlap) {
            return back()->withErrors(['start_date' => 'Lỗi: Thời gian cập nhật bị trùng với một kỳ học KHÁC!'])
                         ->withInput();
        }
        // ============================================================

        // 3. CẬP NHẬT (Giữ nguyên)
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