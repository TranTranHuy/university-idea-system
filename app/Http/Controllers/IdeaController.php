<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Idea;
use App\Models\Category;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class IdeaController extends Controller
{
    // 1. Trang danh sách Ideas (Hiển thị 6 cái/trang)
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
        $currentYear = AcademicYear::where('start_date', '<=', now())
                                 ->where('final_closure_date', '>=', now())
                                 ->first();

        if (!$currentYear) {
            return redirect()->back()->with('error', 'Lỗi: Hiện tại không có kỳ học nào đang mở.');
        }

        if (now() > $currentYear->closure_date) {
            return redirect()->back()->with('error', 'Rất tiếc, đã quá hạn nộp ý tưởng!');
        }

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
        $idea->academic_year_id = $currentYear->id;
        $idea->document = $filePaths;
        $idea->save();

        // LOGIC GỬI MAIL
        $studentDeptId = Auth::user()->department_id;
        $coordinators = User::where('department_id', $studentDeptId)
                            ->whereHas('role', function($q) {
                                $q->where('role_name', 'QA Coordinator');
                            })->get();

        foreach ($coordinators as $coord) {
            try {
                \Illuminate\Support\Facades\Mail::to($coord->email)
                     ->send(new \App\Mail\NewIdeaNotification($idea));
            } catch (\Exception $e) {
                // Log lỗi nếu cần
            }
        }

        return redirect()->route('home')->with('success', 'Nộp ý tưởng thành công!');
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
            if ($files) {
                foreach ($files as $file) {
                    Storage::disk('public')->delete($file);
                }
            }
        }
        $idea->delete();
        return redirect()->back()->with('success', 'Xóa ý tưởng thành công!');
    }

    public function show($id)
    {
        $idea = Idea::with(['user', 'category', 'comments.user'])->findOrFail($id);
        return view('ideas.show', compact('idea'));
    }

    public function exportCsv()
    {
        $fileName = 'ideas_export_' . date('Y-m-d_H-i') . '.csv';
        $ideas = Idea::with(['user.department', 'category'])->latest()->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($ideas) {
            $file = fopen('php://output', 'w');
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

    // --- TẢI TẤT CẢ FILE DƯỚI DẠNG ZIP ---
    public function downloadZip()
    {
        $zip = new ZipArchive;
        $fileName = 'all_attachments_' . date('Ymd_His') . '.zip';
        $zipPath = storage_path('app/public/' . $fileName);

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            $ideas = Idea::whereNotNull('document')->get();
            $count = 0;

            foreach ($ideas as $idea) {
                $documents = is_array($idea->document) ? $idea->document : json_decode($idea->document, true);
                if (!empty($documents)) {
                    foreach ($documents as $filePath) {
                        $fullPath = storage_path('app/public/' . $filePath);
                        if (file_exists($fullPath)) {
                            $nameInZip = 'Idea_' . $idea->id . '_' . basename($filePath);
                            $zip->addFile($fullPath, $nameInZip);
                            $count++;
                        }
                    }
                }
            }
            $zip->close();

            if ($count === 0) {
                return redirect()->back()->with('error', 'Không có tài liệu nào để nén.');
            }
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    // --- TẢI FILE ZIP CỦA MỘT Ý TƯỞNG CHỈ ĐỊNH ---
    public function downloadSingleZip($id)
    {
        $idea = Idea::findOrFail($id);
        $documents = is_array($idea->document) ? $idea->document : json_decode($idea->document, true);

        if (empty($documents)) {
            return redirect()->back()->with('error', 'Ý tưởng này không có tệp đính kèm.');
        }

        $zip = new ZipArchive;
        $fileName = 'Idea_' . $idea->id . '_attachments.zip';
        $zipPath = storage_path('app/public/' . $fileName);

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($documents as $filePath) {
                $fullPath = storage_path('app/public/' . $filePath);
                if (file_exists($fullPath)) {
                    $zip->addFile($fullPath, basename($filePath));
                }
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    // --- TẢI FILE ZIP THEO NĂM HỌC ---
    public function downloadZipByYear($year_id)
    {
        $year = \App\Models\AcademicYear::findOrFail($year_id);
        $zip = new \ZipArchive;

        // Tên file ZIP tải về: VD: Documents_spring-2026.zip
        $fileName = 'Documents_' . \Illuminate\Support\Str::slug($year->name) . '.zip';
        $zipPath = storage_path('app/public/' . $fileName);

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {

            // Lấy các Idea thuộc Năm học này và CÓ TÀI LIỆU
            $ideas = \App\Models\Idea::where('academic_year_id', $year_id)
                        ->whereNotNull('document')
                        ->with(['user', 'category'])
                        ->get();

            if ($ideas->isEmpty()) {
                $zip->close();
                return back()->with('error', 'Kỳ học này chưa có ý tưởng nào chứa tài liệu đính kèm!');
            }

            $hasFiles = false;

            foreach ($ideas as $idea) {
                // Xử lý dữ liệu cột document
                $documents = $idea->document;
                if (is_string($documents)) {
                    $documents = json_decode($documents, true) ?? [$documents];
                }

                if (!is_array($documents)) continue;

                foreach ($documents as $filePathRaw) {
                    $fullPath = storage_path('app/public/' . $filePathRaw);

                    if (file_exists($fullPath)) {
                        $hasFiles = true;
                        // Phân loại thư mục trong ZIP: Tên_Danh_mục / Tên_Tác_giả_TênFile
                        $folderName = \Illuminate\Support\Str::slug($idea->category->name ?? 'Uncategorized');
                        $studentName = \Illuminate\Support\Str::slug($idea->user->email ?? 'Anonymous');
                        $fileNameInZip = basename($fullPath);

                        $zipInternalPath = $folderName . '/' . $studentName . '_' . $fileNameInZip;
                        $zip->addFile($fullPath, $zipInternalPath);
                    }
                }
            }

            $zip->close();

            if (!$hasFiles) {
                if (file_exists($zipPath)) @unlink($zipPath);
                return back()->with('error', 'Các file đính kèm đã bị thất lạc khỏi máy chủ!');
            }
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
