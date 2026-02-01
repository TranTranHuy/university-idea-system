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
    // $idea->content = $request->content; // Đảm bảo name trong form là 'content'
    $idea->content = $request->input('content');
    $idea->category_id = $request->category_id;
    $idea->is_anonymous = $request->has('is_anonymous');

    // Lưu mảng đường dẫn file (Laravel sẽ tự chuyển thành JSON nhờ bước 1)
    // Lưu mảng đường dẫn vào cột document
    $idea->document = $filePaths;

    $idea->save();

    return redirect()->route('home')->with('success', 'Idea submitted successfully!');
}

// Hàm hiển thị danh sách cho Admin
public function adminIndex()
{
    // Lấy tất cả ý tưởng, kèm thông tin người đăng và danh mục
    $ideas = \App\Models\Idea::with(['user', 'category'])->latest()->paginate(15);
    return view('admin.ideas_manage', compact('ideas'));
}

// Hàm xử lý xóa ý tưởng
public function adminDestroy($id)
{
    $idea = \App\Models\Idea::findOrFail($id);

    // Kiểm tra và xóa file đính kèm trong storage để tránh rác server
    if ($idea->document) {
        $files = is_array($idea->document) ? $idea->document : json_decode($idea->document, true);
        if ($files) {
            foreach ($files as $file) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
            }
        }
    }

    $idea->delete();
    return redirect()->back()->with('success', 'Xóa ý tưởng thành công!');
}

// --- TÍNH NĂNG MỚI: EXPORT CSV (Dành cho QA Manager) ---
    public function exportCsv()
    {
        $fileName = 'ideas_export_' . date('Y-m-d_H-i') . '.csv';

        // 1. Lấy dữ liệu: Kèm theo User (và Department của User đó), Category
        // Lưu ý: 'user.department' yêu cầu trong Model User phải có function department()
        $ideas = Idea::with(['user.department', 'category'])->latest()->get();

        // 2. Cấu hình Header để trình duyệt hiểu đây là file tải về
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // 3. Tạo luồng ghi dữ liệu (Stream)
        $callback = function() use($ideas) {
            $file = fopen('php://output', 'w');

            // Hàng tiêu đề (Header Row)
            fputcsv($file, ['ID', 'Title', 'Content', 'Author Name', 'Department', 'Category', 'Submission Date']);

            // Vòng lặp dữ liệu
            foreach ($ideas as $idea) {
                fputcsv($file, [
                    $idea->id,
                    $idea->title,
                    $idea->content,
                    // Xử lý logic Ẩn danh: Nếu anonymous thì Admin/QAM có thấy tên thật không? 
                    // Thường báo cáo nội bộ thì vẫn hiện tên thật, tùy bạn quyết định.
                    $idea->is_anonymous ? $idea->user->full_name . ' (Anonymous)' : $idea->user->full_name,
                    
                    // Lấy tên phòng ban (Dùng toán tử ?? để tránh lỗi nếu null)
                    $idea->user->department->department_name ?? 'No Dept',
                    
                    // Lấy tên category (Check kỹ trong Model Category của bạn là 'name' hay 'category_name')
                    $idea->category->name ?? $idea->category->category_name ?? 'Uncategorized',
                    
                    $idea->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

}
