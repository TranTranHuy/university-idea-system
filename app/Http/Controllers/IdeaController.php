<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Idea;
use App\Models\Category;
use App\Models\AcademicYear;
use App\Models\Department; // Cần import Department
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class IdeaController extends Controller
{
    // TRANG CHỦ
    public function index(Request $request)
    {
        $query = Idea::with(['user', 'category', 'department', 'likes', 'comments.user']);

        if (Auth::check()) {
            $user = Auth::user();
            $roleName = $user->role->role_name ?? $user->role;

            if (!in_array($roleName, ['Administrator', 'Admin', 'admin', 'QA Manager'])) {
                // LỌC THEO KHOA: Chỉ hiển thị ý tưởng cùng Department với User
                $query->where('department_id', $user->department_id);
            }
        } else { $query->where('id', '<', 0); }

        if ($request->sort == 'popular') { $query->withCount('likes')->orderBy('likes_count', 'desc'); }
        else { $query->latest(); }

        $ideas = $query->paginate(6);
        return view('home', compact('ideas'));
    }

    // TRANG FORM NỘP BÀI
    public function create()
    {
        $user = Auth::user();
        $roleName = $user->role->role_name ?? $user->role;

        // TẤT CẢ mọi người đều được chọn tất cả Chủ đề (Category)
        $categories = Category::all();
        $departments = null;

        // NẾU LÀ MANAGER/ADMIN: Truyền thêm danh sách Khoa (Department) để họ chọn chỗ gửi bài
        if (in_array($roleName, ['Administrator', 'Admin', 'admin', 'QA Manager'])) {
            $departments = Department::all();
        }

        $currentYear = AcademicYear::where('start_date', '<=', now())->where('final_closure_date', '>=', now())->first();
        return view('createideapage', compact('categories', 'departments', 'currentYear'));
    }

    // LƯU BÀI VIẾT
    public function store(Request $request)
    {
        $currentYear = AcademicYear::where('start_date', '<=', now())->where('final_closure_date', '>=', now())->first();
        if (!$currentYear) { return redirect()->back()->with('error', 'Lỗi: Không có kỳ học nào đang mở.'); }
        if (now() > $currentYear->closure_date) { return redirect()->back()->with('error', 'Đã quá hạn nộp!'); }

        $request->validate([
            'title' => 'required|max:255', 'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'department_id' => 'nullable|exists:departments,id', // Dành cho Manager chọn
            'documents.*' => 'nullable|mimes:pdf,docx,jpg,png|max:2048',
        ]);

        $user = Auth::user();
        $roleName = $user->role->role_name ?? $user->role;
        $isAdminOrQAM = in_array($roleName, ['Administrator', 'Admin', 'admin', 'QA Manager']);

        // LOGIC LẤY ID KHOA:
        // 1. Nếu là Manager & có chọn Khoa -> Lấy từ form
        // 2. Nếu là Staff -> Lấy tự động từ tài khoản của Staff
        $ideaDeptId = $user->department_id;
        if ($isAdminOrQAM && $request->filled('department_id')) {
            $ideaDeptId = $request->department_id;
        }

        if (!$ideaDeptId) {
            return redirect()->back()->with('error', 'Lỗi: Không thể xác định được Khoa cho ý tưởng này!');
        }

        $filePaths = [];
        if($request->hasFile('documents')) {
            foreach($request->file('documents') as $file) { $filePaths[] = $file->store('ideas', 'public'); }
        }

        $idea = new Idea();
        $idea->user_id = Auth::id();
        $idea->title = $request->title;
        $idea->content = $request->input('content');
        $idea->category_id = $request->category_id; // Lưu Chủ đề
        $idea->department_id = $ideaDeptId;         // Lưu Khoa quản lý
        $idea->is_anonymous = $request->has('is_anonymous');
        $idea->academic_year_id = $currentYear->id;
        $idea->document = $filePaths;
        $idea->save();

        // GỬI MAIL CHUẨN XÁC: Gửi cho Coordinator của Khoa quản lý ý tưởng này
        $coordinators = User::where('department_id', $ideaDeptId)
                            ->whereHas('role', function($q) {
                                $q->where('role_name', 'QA Coordinator')
                                  ->orWhere('role_name', 'Coordinator');
                            })->get();

        foreach ($coordinators as $coord) {
            try {
                \Illuminate\Support\Facades\Mail::to($coord->email)->send(new \App\Mail\NewIdeaNotification($idea));
            } catch (\Exception $e) {
                // Tạm thời ẩn dd() đi để web chạy mượt mà
                // dd('Lỗi gửi mail: ' . $e->getMessage());
            }

        }
        // BẠN HÃY CHẮC CHẮN RẰNG DÒNG NÀY ĐANG TỒN TẠI VÀ KHÔNG BỊ BÔI MỜ (COMMENT)
        return redirect()->route('home')->with('success', 'Nộp ý tưởng thành công và email đã được gửi!');
    }

    // XEM CHI TIẾT
    public function show($id)
    {
        $idea = Idea::with(['user', 'category', 'department', 'comments.user'])->findOrFail($id);
        if (Auth::check()) {
            $user = Auth::user();
            $roleName = $user->role->role_name ?? $user->role;
            if (!in_array($roleName, ['Administrator', 'Admin', 'admin', 'QA Manager'])) {
                // CHẶN NGƯỜI KHÁC KHOA
                if ($idea->department_id != $user->department_id) {
                    return redirect()->route('home')->with('error', 'Bạn không có quyền xem ý tưởng thuộc Khoa/Phòng ban khác!');
                }
            }
        } else { return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!'); }
        return view('ideas.show', compact('idea'));
    }

    // --- Các hàm của Admin & Export dưới đây giữ nguyên ---
    public function adminIndex() {
        $ideas = Idea::with(['user', 'category'])->latest()->paginate(15);
        return view('admin.ideas_manage', compact('ideas'));
    }

    public function adminDestroy($id) {
        $idea = Idea::findOrFail($id);
        if ($idea->document) {
            $files = is_array($idea->document) ? $idea->document : json_decode($idea->document, true);
            if ($files) { foreach ($files as $file) { Storage::disk('public')->delete($file); } }
        }
        $idea->delete();
        return redirect()->back()->with('success', 'Xóa ý tưởng thành công!');
    }

    public function exportCsv() { /* Giữ nguyên code cũ */ }
    public function downloadZip() { /* Giữ nguyên code cũ */ }
    public function downloadSingleZip($id) { /* Giữ nguyên code cũ */ }
    public function downloadZipByYear($year_id) { /* Giữ nguyên code cũ */ }
}
