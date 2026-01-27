<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Idea;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str; // Thêm dòng này để dùng hàm cắt chữ

class IdeaController extends Controller
{
    // 1. Trang danh sách Ideas (Hiển thị 5 cái/trang)
    public function index(Request $request)
    {
        $query = Idea::with(['user', 'category', 'likes', 'comments.user']);

        // Xử lý bộ lọc nếu cần
        if ($request->sort == 'popular') {
            $query->withCount('likes')->orderBy('likes_count', 'desc');
        } else {
            $query->latest();
        }

        // PHÂN TRANG: 6 ideas trên 1 trang
        $ideas = $query->paginate(6);

        return view('home', compact('ideas'));
    }

    // 2. Trang chi tiết Idea (Hiển thị toàn bộ nội dung)
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
    // 1. Kiểm tra dữ liệu đầu vào
    $request->validate([
        'title' => 'required|max:255',
        'content' => 'required',
        'documents.*' => 'nullable|mimes:pdf,docx,jpg,png|max:2048', // Validate từng file trong mảng
    ]);

    $filePaths = [];

    // 2. Kiểm tra nếu có file được gửi lên
    if($request->hasFile('documents')) {
        foreach($request->file('documents') as $file) {
            // Lưu file vào thư mục storage/app/public/ideas
            $path = $file->store('ideas', 'public');
            $filePaths[] = $path; // Thêm đường dẫn vào mảng
        }
    }

    // 3. Lưu vào Database
    $idea = new Idea();
    $idea->user_id = \Illuminate\Support\Facades\Auth::id();
    $idea->title = $request->title;
    $idea->content = $request->content; // Đảm bảo name trong form là 'content'
    $idea->category_id = $request->category_id;
    $idea->is_anonymous = $request->has('is_anonymous');

    // Lưu mảng đường dẫn file (Laravel sẽ tự chuyển thành JSON nhờ bước 1)
    // Lưu mảng đường dẫn vào cột document
    $idea->document = $filePaths;

    $idea->save();

    return redirect()->route('home')->with('success', 'Idea submitted successfully!');
}
}
