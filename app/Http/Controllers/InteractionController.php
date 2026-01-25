<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InteractionController extends Controller
{
    // Xử lý Like/Unlike
    public function like($id)
    {
        $idea = Idea::findOrFail($id);
        $userId = Auth::id();

        $existingLike = Like::where('idea_id', $id)->where('user_id', $userId)->first();

        if ($existingLike) {
            $existingLike->delete(); // Nếu đã like rồi thì xóa (Unlike)
        } else {
            Like::create([
                'user_id' => $userId,
                'idea_id' => $id
            ]);
        }

        return back();
    }

    // Xử lý gửi bình luận
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
