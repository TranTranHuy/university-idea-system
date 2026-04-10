<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Idea;
use App\Models\Category;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use ZipArchive;
use App\Mail\StaffIdeaSubmitted;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class IdeaController extends Controller
{
    // --- 1. TRANG DANH SÁCH IDEAS (TRANG CHỦ) ---
    public function index(Request $request)
    {
        // Lấy tham số 'sort' từ URL, mặc định là 'latest'
        $sortQuery = $request->input('sort', 'latest');

        // Tạo query cơ bản
        $query = Idea::with(['user.department', 'category']);

        switch ($sortQuery) {
            case 'popular':
                // MOST POPULAR: Điểm = (Tổng Like - Tổng Dislike)
                $ideas = $query->withCount([
                                    'likes as upvotes' => function ($q) { $q->where('type', 1); },
                                    'likes as downvotes' => function ($q) { $q->where('type', 0); }
                               ])
                               ->orderByRaw('(upvotes - downvotes) DESC') // Sắp xếp theo điểm giảm dần
                               ->paginate(10);
                $sortTitle = 'Most Popular Ideas';
                break;

            case 'newest_comments':
                // NEWEST COMMENTS: Có comment và ưu tiên comment gần đây nhất
                $ideas = $query->withMax('comments', 'created_at')
                               ->has('comments')
                               ->orderByDesc('comments_max_created_at') // Sắp xếp theo thời gian comment giảm dần
                               ->paginate(10);
                $sortTitle = 'Newest Comments';
                break;

            case 'viewed':
                // MOST VIEWED: Lượt xem giảm dần (thêm logic an toàn cho database)
                $ideas = $query->orderByRaw('COALESCE(ideas.view_count, 0) DESC')
                               ->orderByDesc('created_at') // Nếu lượt xem bằng nhau thì bài nào mới hơn sẽ lên trước
                               ->paginate(10);
                $sortTitle = 'Most Viewed Ideas';
                break;

            case 'latest':
                // LATEST IDEAS
                $ideas = $query->latest()->paginate(10);
                $sortTitle = 'Latest Ideas';
                break;

            case 'default':
            default:
                // DEFAULT
                $ideas = $query->latest()->paginate(10);
                $sortTitle = 'All Ideas';
                break;
        }

        // Đính kèm tham số bộ lọc vào link phân trang để khi bấm trang 2, 3 không bị lỗi
        $ideas->appends(['sort' => $sortQuery]);

        return view('home', compact('ideas', 'sortQuery', 'sortTitle'));
    }

    // --- 2. TRANG ĐĂNG IDEA MỚI ---
    public function create()
    {
        $categories = Category::all();
        $currentYear = AcademicYear::where('start_date', '<=', now())
                               ->where('final_closure_date', '>=', now())
                               ->first();
        return view('createideapage', compact('categories', 'currentYear'));
    }

    // --- 3. LƯU IDEA MỚI VÀ GỬI MAIL CHO QAC ---
    public function store(Request $request)
    {
        $currentYear = AcademicYear::where('start_date', '<=', now())
                                 ->where('final_closure_date', '>=', now())
                                 ->first();

        if (!$currentYear) {
            return redirect()->back()->with('error', 'Error: There is no academic year currently open.');
        }

        if (now() > $currentYear->closure_date) {
            return redirect()->back()->with('error', 'Unfortunately, the deadline for submitting ideas has passed!');
        }

        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
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
        $idea->academic_year_id = $currentYear->id;
        $idea->document = $filePaths;
        $idea->save();

        // LOGIC GỬI MAIL: Tự động tìm QAC cùng khoa
        $studentDeptId = Auth::user()->department_id;
        $coordinators = User::where('department_id', $studentDeptId)
                            ->whereHas('role', function($q) {
                                $q->where('role_name', 'QA Coordinator');
                            })->get();

        foreach ($coordinators as $coord) {
            try {
                // Sửa lỗi: Đã đổi sang gửi StaffIdeaSubmitted cho đúng chức năng
                Mail::to($coord->email)->send(new StaffIdeaSubmitted($idea));
            } catch (\Exception $e) {
                Log::error("Email QAC error: " . $e->getMessage());
            }
        }

        return redirect()->route('home')->with('success', 'Idea submission successful!');
    }

    // --- 4. ADMIN XEM DANH SÁCH ---
    public function adminIndex()
    {
        $ideas = Idea::with(['user', 'category'])->latest()->paginate(15);
        return view('admin.ideas_manage', compact('ideas'));
    }

    // --- 5. ADMIN XÓA IDEA ---
    public function adminDestroy($id)
    {
        $idea = Idea::findOrFail($id);
        if ($idea->document) {
            $files = is_array($idea->document) ? $idea->document : json_decode($idea->document, true);
            if ($files) {
                foreach ($files as $file) {
                    Storage::disk('public')->delete($file);
                }
            }
        }
        $idea->delete();
        return redirect()->back()->with('success', 'Idea deleted successfully!');
    }

    // --- 6. CHI TIẾT 1 IDEA ---
    public function show($id)
    {
        // 1. Tải Idea và các dữ liệu liên quan
        $idea = Idea::with(['user', 'category', 'likes', 'comments.user'])->findOrFail($id);

        // 2. TĂNG VIEW TRỰC TIẾP (Bỏ kiểm tra Session, cứ F5 là cộng)
        $idea->increment('view_count');

        // 3. Làm mới dữ liệu (Refresh) để hiển thị ngay con số vừa cộng lên view
        $idea->refresh();

        return view('ideas.show', compact('idea'));
    }


    // --- 7. TẢI FILE CSV (CÓ LỌC PHÒNG BAN) ---
    public function exportCsv()
    {
        $fileName = 'ideas_export_' . date('Y-m-d_H-i') . '.csv';
        $query = Idea::with(['user.department', 'category'])->latest();

        if (Auth::check()) {
            /** @var \App\Models\User $currentUser */
            $currentUser = Auth::user();
            if ($currentUser->hasRole(['QA Coordinator', 'Staff'])) {
                $query->whereHas('user', function ($q) use ($currentUser) {
                    $q->where('department_id', $currentUser->department_id);
                });
            }
        }

        $ideas = $query->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($ideas) {
            $file = fopen('php://output', 'w');
            fputs($file, (chr(0xEF) . chr(0xBB) . chr(0xBF))); // Fix font tiếng Việt
            fputcsv($file, ['ID', 'Title', 'Content', 'Author Name', 'Department', 'Category', 'Submission Date']);
            foreach ($ideas as $idea) {
                fputcsv($file, [
                    $idea->id,
                    $idea->title,
                    $idea->content,
                    $idea->is_anonymous ? $idea->user->full_name . ' (Anonymous)' : $idea->user->full_name,
                    $idea->user->department->department_name ?? 'No Dept',
                    $idea->category->name ?? 'Uncategorized',
                    $idea->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    // --- 8. TẢI ZIP TẤT CẢ FILE (CÓ LỌC PHÒNG BAN) ---
    public function downloadZip()
    {
        $zip = new ZipArchive;
        $fileName = 'all_attachments_' . date('Ymd_His') . '.zip';
        $zipPath = storage_path('app/public/' . $fileName);

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $query = Idea::whereNotNull('document');

            if (Auth::check()) {
                /** @var \App\Models\User $currentUser */
                $currentUser = Auth::user();
                if ($currentUser->hasRole(['QA Coordinator', 'Staff'])) {
                    $query->whereHas('user', function ($q) use ($currentUser) {
                        $q->where('department_id', $currentUser->department_id);
                    });
                }
            }

            $ideas = $query->get();
            $count = 0;

            foreach ($ideas as $idea) {
                $documents = $idea->document;
                if (is_string($documents)) {
                    $decoded = json_decode($documents, true);
                    $documents = is_array($decoded) ? $decoded : [$documents];
                }

                if (is_array($documents)) {
                    foreach ($documents as $filePath) {
                        if ($filePath === 'Array') continue;
                        $fullPath = storage_path('app/public/' . $filePath);
                        if (file_exists($fullPath)) {
                            $zip->addFile($fullPath, 'Idea_' . $idea->id . '/' . basename($filePath));
                            $count++;
                        }
                    }
                }
            }
            $zip->close();

            if ($count === 0) {
                if(file_exists($zipPath)) @unlink($zipPath);
                return redirect()->back()->with('error', 'No documents found for your department.');
            }
        }
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    // --- 9. TẢI ZIP CỦA 1 Ý TƯỞNG ---
    public function downloadSingleZip($id)
    {
        $idea = Idea::findOrFail($id);
        $documents = $idea->document;
        if (is_string($documents)) {
            $decoded = json_decode($documents, true);
            $documents = is_array($decoded) ? $decoded : [$documents];
        }

        if (empty($documents)) return redirect()->back()->with('error', 'No attached files found.');

        $zip = new ZipArchive;
        $fileName = 'Idea_' . $idea->id . '_files.zip';
        $zipPath = storage_path('app/public/' . $fileName);

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($documents as $filePath) {
                $fullPath = storage_path('app/public/' . $filePath);
                if (file_exists($fullPath)) $zip->addFile($fullPath, basename($filePath));
            }
            $zip->close();
        }
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    // --- 10. TẢI ZIP THEO NĂM HỌC (CÓ LỌC PHÒNG BAN) ---
    public function downloadZipByYear($year_id)
    {
        $year = AcademicYear::findOrFail($year_id);
        $zip = new ZipArchive;
        $fileName = 'Docs_' . Str::slug($year->name) . '.zip';
        $zipPath = storage_path('app/public/' . $fileName);

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $query = Idea::where('academic_year_id', $year_id)->whereNotNull('document')->with(['user', 'category']);

            if (Auth::check()) {
                /** @var \App\Models\User $currentUser */
                $currentUser = Auth::user();
                if ($currentUser->hasRole(['QA Coordinator', 'Staff'])) {
                    $query->whereHas('user', function ($q) use ($currentUser) {
                        $q->where('department_id', $currentUser->department_id);
                    });
                }
            }

            $ideas = $query->get();
            $hasFiles = false;

            foreach ($ideas as $idea) {
                $documents = is_string($idea->document) ? json_decode($idea->document, true) : $idea->document;
                if (!is_array($documents)) continue;

                foreach ($documents as $filePath) {
                    $fullPath = storage_path('app/public/' . $filePath);
                    if (file_exists($fullPath)) {
                        $hasFiles = true;
                        $folder = Str::slug($idea->category->name ?? 'Uncategorized');
                        $zip->addFile($fullPath, $folder . '/' . basename($filePath));
                    }
                }
            }
            $zip->close();
            if (!$hasFiles) return back()->with('error', 'No files found.');
        }
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    // --- 11. MỞ FORM CHỈNH SỬA ---
    public function edit($id)
    {
        $idea = Idea::with('academicYear')->findOrFail($id);
        if ($idea->user_id !== Auth::id()) return redirect()->back()->with('error', 'Access denied.');
        if ($idea->academicYear && now() > $idea->academicYear->closure_date) return redirect()->back()->with('error', 'Deadline for editing has passed.');

        $categories = Category::all();
        return view('ideas.edit', compact('idea', 'categories'));
    }

    // --- 12. CẬP NHẬT Ý TƯỞNG ---
    public function update(Request $request, $id)
    {
        $idea = Idea::with('academicYear')->findOrFail($id);

        if ($idea->user_id !== Auth::id() || ($idea->academicYear && now() > $idea->academicYear->closure_date)) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            // Thêm validate cho file nếu có
            'documents.*' => 'nullable|mimes:pdf,docx,jpg,png|max:2048',
        ]);

        $idea->title = $request->title;
        $idea->content = $request->input('content');
        $idea->category_id = $request->category_id;

        // CẬP NHẬT LOGIC LƯU FILE TƯƠNG TỰ HÀM STORE
        if ($request->hasFile('documents')) {
            $filePaths = [];
            foreach($request->file('documents') as $file) {
                // Bạn có thể cân nhắc xóa file cũ ở Storage trước khi up file mới
                // để tiết kiệm dung lượng server nhé
                $path = $file->store('ideas', 'public');
                $filePaths[] = $path;
            }
            $idea->document = $filePaths;
        }

        $idea->save();
        return redirect()->route('staff.profile')->with('success', 'Idea updated successfully!');
    }
}
