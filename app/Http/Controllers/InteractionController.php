<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use App\Models\Like;
use App\Models\Comment;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Đã có
use Illuminate\Http\RedirectResponse; // Thêm dòng này nếu chưa có

class InteractionController extends Controller
{
    public function like(Request $request, $id, $type): RedirectResponse

    {
        // 1. Dùng Auth::check() thay vì auth()->check() để hết bôi đỏ
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tương tác!');
        }

        // 2. Dùng Auth::id() thay vì auth()->id()
        $userId = Auth::id();

        $existingInteraction = Like::where('user_id', $userId)
                                   ->where('idea_id', $id)
                                   ->first();

        if ($existingInteraction) {
            if ($existingInteraction->type == $type) {
                $existingInteraction->delete();
                $msg = "Đã hủy tương tác!";
            } else {
                $existingInteraction->update(['type' => $type]);
                $msg = ($type == 1) ? "Đã chuyển sang Thích!" : "Đã chuyển sang Không thích!";
            }
        } else {
            // Dòng 34 gây lỗi Error 500 nếu $userId rỗng
            Like::create([
                'user_id' => $userId,
                'idea_id' => $id,
                'type'    => $type
            ]);
            $msg = ($type == 1) ? "Đã thích ý tưởng!" : "Đã không thích ý tưởng!";
        }

        return back()->with('success', $msg);


    }



    public function comment(Request $request, $id)
    {
        $request->validate(['content' => 'required|max:1000']);

        $idea = Idea::findOrFail($id);

        // --- BẮT ĐẦU ĐOẠN CODE CHECK DEADLINE MỚI ---
        // Kiểm tra xem Idea này có thuộc năm học nào không
        if ($idea->academic_year_id) {
            $year = AcademicYear::find($idea->academic_year_id);
            
            // Nếu tìm thấy năm học và ngày hiện tại đã vượt quá Final Closure Date
            if ($year && now() > $year->final_closure_date) {
                return back()->with('error', 'Rất tiếc, kỳ học đã đóng hoàn toàn. Không thể bình luận nữa!');
            }
        }
        // --- KẾT THÚC ĐOẠN CODE CHECK DEADLINE ---

        $idea->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
            'is_anonymous' => $request->has('is_anonymous'),
        ]);

        return back()->with('success', 'Bình luận thành công!');
    }
}
