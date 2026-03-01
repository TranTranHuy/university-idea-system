<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $years = AcademicYear::latest()->paginate(10);
        return view('admin.academic_years.index', compact('years'));
    }

    public function create()
    {
        return view('admin.academic_years.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:academic_years',
            'start_date' => 'required|date',
            'closure_date' => 'required|date|after:start_date',
            'final_closure_date' => 'required|date|after:closure_date',
        ], [
            'closure_date.after' => 'Ngày đóng nộp Idea phải diễn ra sau ngày bắt đầu!',
            'final_closure_date.after' => 'Ngày đóng tương tác phải là ngày cuối cùng!',
        ]);

        // --- RÀNG BUỘC TRÙNG NGÀY (OVERLAP) ---
        // Một kỳ học mới không được đè lên khoảng thời gian của kỳ học cũ
        $overlap = AcademicYear::where('start_date', '<=', $request->final_closure_date)
                               ->where('final_closure_date', '>=', $request->start_date)
                               ->exists();

        if ($overlap) {
            return back()->withInput()->with('error', 'Lỗi: Khoảng thời gian này bị trùng lặp với một Năm học khác đã tồn tại!');
        }

        AcademicYear::create($request->all());

        return redirect()->route('admin.academic-years.index')
                         ->with('success', 'Tạo kỳ học mới thành công!');
    }

    public function edit($id)
    {
        $academicYear = AcademicYear::findOrFail($id);
        return view('admin.academic_years.edit', compact('academicYear'));
    }

    public function update(Request $request, $id)
    {
        $year = AcademicYear::findOrFail($id);

        // 1. VALIDATE CƠ BẢN (Giữ nguyên)
        $request->validate([
            'name' => 'required|string|unique:academic_years,name,'.$id,
            'start_date' => 'required|date',
            'closure_date' => 'required|date|after:start_date',
            'final_closure_date' => 'required|date|after:closure_date',
        ]);

        // --- RÀNG BUỘC TRÙNG NGÀY KHI SỬA ---
        // Loại trừ chính nó (id hiện tại) ra khỏi vòng kiểm tra
        $overlap = AcademicYear::where('id', '!=', $id)
                               ->where('start_date', '<=', $request->final_closure_date)
                               ->where('final_closure_date', '>=', $request->start_date)
                               ->exists();

        if ($overlap) {
            return back()->withInput()->with('error', 'Lỗi: Thời gian cập nhật bị đè lên một Năm học khác!');
        }

        $year->update($request->all());

        return redirect()->route('admin.academic-years.index')
                         ->with('success', 'Cập nhật thành công!');
    }

    public function destroy($id)
    {
        $year = AcademicYear::findOrFail($id);

        // --- RÀNG BUỘC XÓA ---
        // Nếu năm học này đã có Idea nộp vào thì chặn không cho xóa
        if ($year->ideas()->exists()) {
            return back()->with('error', 'Không thể xóa kỳ học "' . $year->name . '" vì đã có sinh viên nộp bài trong thời gian này!');
        }

        $year->delete();
        return back()->with('success', 'Đã xóa kỳ học thành công!');
    }
}
