<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Idea;
use App\Models\AcademicYear;

class QAManagerController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $ideas = Idea::with('user')->latest()->paginate(10);
        return view('qam.dashboard', compact('categories', 'ideas'));
    }

    // --- HIỂN THỊ TRANG QUẢN LÝ DEADLINES ---
    public function deadlinesIndex()
    {
        $years = AcademicYear::latest()->paginate(10);
        return view('qamanager.deadlines', compact('years'));
    }

    // --- XỬ LÝ CẬP NHẬT DEADLINES ---
    public function deadlinesUpdate(Request $request, $id)
    {
        $year = AcademicYear::findOrFail($id);

        $request->validate([
            'closure_date' => 'required|date',
            'final_closure_date' => 'required|date|after:closure_date',
        ], [
            'final_closure_date.after' => 'Ngày đóng Comment phải diễn ra sau ngày đóng nộp Idea!',
        ]);

        // Ép kiểu thời gian chuẩn để DB so sánh không bị sai lệch
        $newFinalClosure = \Carbon\Carbon::parse($request->final_closure_date)->format('Y-m-d H:i:s');

        // Kiểm tra trùng lặp thời gian với các năm học khác
        $overlap = AcademicYear::where('id', '!=', $id)
                               ->where('start_date', '<=', $newFinalClosure)
                               ->where('final_closure_date', '>=', $year->start_date)
                               ->exists();

        if ($overlap) {
            return back()->with('error', 'Lỗi: Thời gian gia hạn bị đè lên khoảng thời gian của một Năm học khác!');
        }

        // Kiểm tra: Deadline mới không được nhỏ hơn Start Date của chính năm học đó
        if (\Carbon\Carbon::parse($request->closure_date)->lt(\Carbon\Carbon::parse($year->start_date))) {
            return back()->with('error', 'Lỗi: Deadline không được thiết lập trước ngày bắt đầu của năm học ('.\Carbon\Carbon::parse($year->start_date)->format('d/m/Y').')');
        }

        $year->update([
            'closure_date' => $request->closure_date,
            'final_closure_date' => $request->final_closure_date,
        ]);

        return back()->with('success', 'Đã cập nhật Deadlines cho kỳ học: ' . $year->name);
    }
}
