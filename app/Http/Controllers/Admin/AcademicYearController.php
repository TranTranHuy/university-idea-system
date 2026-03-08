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
        // Đã sửa 'academic years' thành 'academic_years'
        return view('admin.academic_years.index', compact('years'));
    }

    public function create()
    {
        // Đã sửa 'academic years' thành 'academic_years'
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
            'closure_date.after' => 'The idea submission deadline must be after the start date.!',
            'final_closure_date.after' => 'The final closure date must be after the idea submission deadline.!',
        ]);

        // --- RÀNG BUỘC TRÙNG NGÀY (OVERLAP) ---
        // Một kỳ học mới không được đè lên khoảng thời gian của kỳ học cũ
        $overlap = AcademicYear::where('start_date', '<=', $request->final_closure_date)
                               ->where('final_closure_date', '>=', $request->start_date)
                               ->exists();

        if ($overlap) {
            return back()->withInput()->with('error', 'Error: This time period overlaps with an existing Academic Year!');
        }

        AcademicYear::create($request->all());

        // Đã sửa 'academic years' thành 'academic_years'
        return redirect()->route('admin.academic-years.index')
                         ->with('success', 'New Academic Year created successfully!');
    }

    public function edit($id)
    {
        $academicYear = AcademicYear::findOrFail($id);
        // Đã sửa 'academic years' thành 'academic_years'
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
            return back()->withInput()->with('error', 'Error: The updated time period overlaps with an existing Academic Year!');
        }

        $year->update($request->all());

        return redirect()->route('admin.academic-years.index')
                         ->with('success', 'Update successful!');
    }

    public function destroy($id)
    {
        $year = AcademicYear::findOrFail($id);

        // --- RÀNG BUỘC XÓA ---
        // Nếu năm học này đã có Idea nộp vào thì chặn không cho xóa
        if ($year->ideas()->exists()) {
            return back()->with('error', 'Error: Cannot delete this Academic Year as there are ideas submitted within this period!');
        }

        $year->delete();
        return back()->with('success', 'New Academic Year deleted successfully!');
    }
}
