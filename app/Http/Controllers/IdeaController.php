<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Idea;
use App\Models\Category;
use App\Models\AcademicYear;
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
        $currentYear = AcademicYear::where('start_date', '<=', now())
                               ->where('final_closure_date', '>=', now())
                               ->first();
        return view('createideapage', compact('categories', 'currentYear'));
    }

    public function store(Request $request)
    {
        // --- 1. LOGIC CHECK DEADLINE (MỚI) ---
        // Tìm kỳ học hiện tại (đang diễn ra)
        $currentYear = AcademicYear::where('start_date', '<=', now())
                                ->where('final_closure_date', '>=', now()) 
                                ->first();

        // Check 1: Nếu không có kỳ học nào đang mở
        if (!$currentYear) {
            return redirect()->back()->with('error', 'Lỗi: Hiện tại không có kỳ học nào (hoặc đã đóng hoàn toàn).');
        }

        // Check 2: Nếu đã quá hạn nộp bài (Closure Date)
        if (now() > $currentYear->closure_date) {
            return redirect()->back()->with('error', 'Rất tiếc, đã quá hạn nộp ý tưởng cho kỳ học này!');
        }
        // -------------------------------------


        // 2. Validate dữ liệu (Code cũ của bạn)
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'documents.*' => 'nullable|mimes:pdf,docx,jpg,png|max:2048',
        ]);

        // 3. Xử lý file (Code cũ của bạn)
        $filePaths = [];
        if($request->hasFile('documents')) {
            foreach($request->file('documents') as $file) {
                $path = $file->store('ideas', 'public');
                $filePaths[] = $path;
            }
        }

        // 4. Lưu vào Database
        $idea = new Idea();
        $idea->user_id = Auth::id(); // Đã rút gọn
        $idea->title = $request->title;
        $idea->content = $request->input('content');
        $idea->category_id = $request->category_id;
        $idea->is_anonymous = $request->has('is_anonymous');
        
        // --- QUAN TRỌNG: Gán Idea vào kỳ học hiện tại ---
        $idea->academic_year_id = $currentYear->id; 
        // ------------------------------------------------

        $idea->document = $filePaths; // Model tự cast mảng này thành JSON

        $idea->save();

        return redirect()->route('home')->with('success', 'Nộp ý tưởng thành công!');
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

    // --- TÍNH NĂNG MỚI: DOWNLOAD ZIP (Sprint 3) ---
    public function downloadZip()
    {
        $zip = new \ZipArchive;
        $fileName = 'all_documents_' . date('Y-m-d_H-i') . '.zip';
        
        // Tạo file zip tạm thời trong thư mục storage/app/public
        $zipPath = storage_path('app/public/' . $fileName);

        // Mở file Zip để ghi
        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            // Lấy các Idea có tài liệu đính kèm
            // Lưu ý: Cần check cột 'document' không được null
            $ideas = Idea::whereNotNull('document')->get();

            foreach ($ideas as $idea) {
                // Giải mã JSON vì cột document lưu dạng mảng đường dẫn: ["ideas/file1.pdf", "ideas/file2.docx"]
                $documents = is_array($idea->document) ? $idea->document : json_decode($idea->document, true);

                if (!empty($documents)) {
                    foreach ($documents as $filePath) {
                        // Đường dẫn thực tế trên ổ cứng server
                        $fullPath = storage_path('app/public/' . $filePath);

                        // Kiểm tra file có tồn tại không rồi mới add vào zip
                        if (file_exists($fullPath)) {
                            // Cấu trúc tên file trong Zip: "Idea_[ID]_[Tên gốc]" để tránh trùng tên
                            $nameInZip = 'Idea_' . $idea->id . '_' . basename($filePath);
                            $zip->addFile($fullPath, $nameInZip);
                        }
                    }
                }
            }
            $zip->close();
        }

        // Trả về file Zip và tự động xóa file tạm sau khi tải xong (deleteFileAfterSend)
        if (file_exists($zipPath)) {
            return response()->download($zipPath)->deleteFileAfterSend(true);
        } else {
            return redirect()->back()->with('error', 'Không có tài liệu nào để tải hoặc lỗi tạo file Zip.');
        }
    }

}
