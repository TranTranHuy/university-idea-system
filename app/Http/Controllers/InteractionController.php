<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use App\Models\Like;
use App\Models\Comment;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Mail\InteractionNotification;
use Illuminate\Support\Facades\Mail;

class InteractionController extends Controller
{
    public function like(Request $request, $id, $type): RedirectResponse
    {
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to interact!');
        }

        $userId = Auth::id();
        // Ép chữ thành số
        $typeValue = ($type === 'like') ? 1 : 0;

        $existingInteraction = Like::where('user_id', $userId)
                                   ->where('idea_id', $id)
                                   ->first();

        $shouldSendEmail = false;
        $action = "";

        if ($existingInteraction) {
            // Nếu bấm lại nút cũ -> Hủy tương tác (Không gửi mail)
            if ($existingInteraction->type == $typeValue) {
                $existingInteraction->delete();
                $msg = "Interaction canceled!";
            } else {
                // Chuyển từ Like sang Dislike hoặc ngược lại -> Cập nhật và Gửi mail
                $existingInteraction->update(['type' => $typeValue]);
                $msg = ($typeValue == 1) ? "Changed to Like!" : "Changed to Dislike!";

                $shouldSendEmail = true;
                $action = ($typeValue == 1) ? "liked" : "disliked";
            }
        } else {
            // Tương tác hoàn toàn mới -> Lưu và Gửi mail
            Like::create([
                'user_id' => $userId,
                'idea_id' => $id,
                'type'    => $typeValue
            ]);
            $msg = ($typeValue == 1) ? "Liked the idea!" : "Disliked the idea!";

            $shouldSendEmail = true;
            $action = ($typeValue == 1) ? "liked" : "disliked";
        }

        // --- BẮT ĐẦU CODE GỬI MAIL ---
        if ($shouldSendEmail) {
            $idea = Idea::with('user')->find($id);
            $sender = Auth::user();

            // ĐIỀU KIỆN VÀNG: Idea có tác giả, có email và người Like KHÔNG PHẢI là tác giả
            if ($idea && $idea->user && $idea->user->email && $idea->user_id != $sender->id) {
                try {
                    Mail::to($idea->user->email)->send(new InteractionNotification($idea, $sender, $action));
                } catch (\Exception $e) {
                    // Nếu gửi mail thất bại, ghi vào log chứ không làm sập web
                    \Illuminate\Support\Facades\Log::error("Email Like error: " . $e->getMessage());
                }
            }
        }
        // --- KẾT THÚC CODE GỬI MAIL ---

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
                return back()->with('error', 'Sorry, the academic year is closed. You cannot comment anymore!');
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
                new InteractionNotification($idea, $sender, "commented", $request->input('content'))
            );
        }
        // --- KẾT THÚC CODE GỬI MAIL ---

        return back()->with('success', 'Comment added successfully!');
    }
}
