<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InteractionController extends Controller
{
    // Xử lý Like và Dislike chung trong một hàm
    public function like($id, $type)
    {
        $idea = Idea::findOrFail($id);
        $userId = Auth::id();

        // Kiểm tra xem người dùng đã từng Like hoặc Dislike bài này chưa
        $existingInteraction = Like::where('idea_id', $id)
                                   ->where('user_id', $userId)
                                   ->first();

        if ($existingInteraction) {
            // Nếu bấm lại cùng một nút (ví dụ đang like mà bấm lại like) thì XÓA
            if ($existingInteraction->type == $type) {
                $existingInteraction->delete();
            } else {
                // Nếu đang Like mà bấm Dislike (hoặc ngược lại) thì CẬP NHẬT loại
                $existingInteraction->update(['type' => $type]);
            }
        } else {
            // Nếu chưa có tương tác nào thì TẠO MỚI
            Like::create([
                'user_id' => $userId,
                'idea_id' => $id,
                'type' => $type // 1 cho Like, 0 cho Dislike
            ]);
        }

        return back();
    }

    // Giữ nguyên hàm comment của bạn
    public function comment(Request $request, $id)
    {
        $request->validate(['content' => 'required']);

        Comment::create([
            'user_id' => Auth::id(),
            'idea_id' => $id,
            'content' => $request->content
        ]);

        return back()->with('success', 'Bình luận thành công!');
    }
}
