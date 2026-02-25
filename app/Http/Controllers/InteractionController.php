<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use App\Models\Like;
use App\Models\Comment;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Mail\InteractionNotification; // Khai báo Mailable
use Illuminate\Support\Facades\Mail; // Khai báo Facade Mail

class InteractionController extends Controller
{
    public function like(Request $request, $id, $type): RedirectResponse
    {
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tương tác!');
        }

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
            // Tạo tương tác mới
            Like::create([
                'user_id' => $userId,
                'idea_id' => $id,
                'type'    => $type
            ]);
            $msg = ($type == 1) ? "Đã thích ý tưởng!" : "Đã không thích ý tưởng!";

            // --- BẮT ĐẦU CODE GỬI MAIL ---
            $idea = Idea::with('user')->find($id);
            $sender = Auth::user();
            $action = ($type == 1) ? "đã thích" : "đã không thích";

            // Kiểm tra an toàn: Đảm bảo idea có user, user có email và tránh gửi cho chính mình
            if ($idea && $idea->user && $idea->user->email && $idea->user_id != $sender->id) {
                Mail::to($idea->user->email)->send(new InteractionNotification($idea, $sender, $action));
            }
            // --- KẾT THÚC CODE GỬI MAIL ---
        }

        return back()->with('success', $msg);
    }

    public function comment(Request $request, $id)
    {
        $request->validate(['content' => 'required|max:1000']);

        $idea = Idea::findOrFail($id);

        // --- BẮT ĐẦU ĐOẠN CODE CHECK DEADLINE ---
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

        // --- BẮT ĐẦU CODE GỬI MAIL ---
        // Sử dụng load() để lấy thêm quan hệ 'user' thay vì query lại $idea từ DB
        $idea->load('user');
        $sender = Auth::user();

        // Kiểm tra an toàn trước khi gửi
        if ($idea->user && $idea->user->email && $idea->user_id != $sender->id) {
            Mail::to($idea->user->email)->send(
                new InteractionNotification($idea, $sender, "đã bình luận", $request->input('content'))
            );
        }
        // --- KẾT THÚC CODE GỬI MAIL ---

        return back()->with('success', 'Bình luận thành công!');
    }
}
