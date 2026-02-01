<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Idea;
use App\Models\Category;
use App\Models\Comment; // Thêm để code ngắn gọn hơn
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IdeaController extends Controller
{
    // 1. Trang danh sách Ideas
    public function index(Request $request)
    {
        $query = Idea::with(['user', 'category', 'likes', 'comments.user']);

        if ($request->sort == 'popular') {
            $query->withCount('likes')->orderBy('likes_count', 'desc');
        } else {
            $query->latest();
        }

        $ideas = $query->paginate(6);
        return view('home', compact('ideas'));
    }

    // 2. Trang chi tiết Idea
    public function show($id)
    {
        $idea = Idea::with(['user', 'category', 'comments.user'])->findOrFail($id);
        return view('ideas_show', compact('idea'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('createideapage', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'documents.*' => 'nullable|mimes:pdf,docx,jpg,png|max:2048',
        ]);

        $filePaths = [];
        if($request->hasFile('documents')) {
            foreach($request->file('documents') as $file) {
                $path = $file->store('ideas', 'public');
                $filePaths[] = $path;
            }
        }

        $idea = new Idea();
        $idea->user_id = Auth::id();
        $idea->title = $request->title;
        $idea->content = $request->input('content');
        $idea->category_id = $request->category_id;
        $idea->is_anonymous = $request->has('is_anonymous');
        $idea->document = $filePaths;
        $idea->save();

        return redirect()->route('home')->with('success', 'Idea submitted successfully!');
    }

    public function adminIndex()
    {
        $ideas = Idea::with(['user', 'category'])->latest()->paginate(15);
        return view('admin.ideas_manage', compact('ideas'));
    }

    public function adminDestroy($id)
    {
        $idea = Idea::findOrFail($id);

        if ($idea->document) {
            $files = is_array($idea->document) ? $idea->document : json_decode($idea->document, true);
            if (is_array($files)) {
                foreach ($files as $file) {
                    Storage::disk('public')->delete($file);
                }
            }
        }

        $idea->delete();
        return redirect()->back()->with('success', 'Xóa ý tưởng thành công!');
    }

    // --- PHẦN SỬA LỖI TẠI ĐÂY ---
    public function storeComment(Request $request, $ideaId)
    {
        // 1. Kiểm tra đăng nhập TRƯỚC KHI thực hiện bất kỳ việc gì
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập trước!');
        }

        // 2. Kiểm tra dữ liệu đầu vào
        $request->validate([
            'content' => 'required|max:1000',
        ]);

        // 3. Lưu bình luận
        $comment = new Comment();
        $comment->content = $request->input('content');
        $comment->user_id = Auth::id();
        $comment->idea_id = $ideaId;
        $comment->is_anonymous = $request->has('is_anonymous') ? 1 : 0;
        $comment->save();

        // 4. Trở về trang cũ sau khi đã lưu xong
        return back()->with('success', 'Bình luận thành công!');
    }
// Hàm xử lý Like/Dislike với thông báo yêu cầu đăng nhập
    public function like(Request $request, $id, $type)
    {
        // 1. Kiểm tra đăng nhập ĐẦU TIÊN
        if (!Auth::check()) {
            // Trả về trang login kèm thông báo lỗi
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để thực hiện hành động này!');
        }

        // ... (Logic xử lý Like/Dislike hiện tại của bạn ở đây) ...

        // Trả về thông báo thành công sau khi xử lý xong logic
        $status = ($type == 1) ? 'Đã thích ý tưởng!' : 'Đã không thích ý tưởng!';
        return back()->with('success', $status);
    }

}
