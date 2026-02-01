<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use App\Models\Like;
use App\Models\Comment;
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

    $idea->comments()->create([
        'user_id' => Auth::id(),
        'content' => $request->input('content'),
        'is_anonymous' => $request->has('is_anonymous'), // Laravel sẽ tự hiểu true/false là 1/0
    ]);

    return back()->with('success', 'Bình luận thành công!');
}
}
