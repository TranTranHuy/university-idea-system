<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CoordinatorController extends Controller
{
    // TRANG DASHBOARD & DANH SÁCH IDEAS (Yêu cầu 1, 3, 5)
    public function index()
    {
        // 1. Lấy thông tin User đang đăng nhập
        $user = Auth::user();

        // Giả sử trong bảng users có cột 'department_id'
        // Nếu quan hệ user -> department khác, bạn chỉnh lại nhé
        $deptId = $user->department_id;

        if (!$deptId) {
            return redirect()->route('home')->with('error', 'Bạn chưa được phân vào Khoa nào!');
        }

        // 2. Logic Lọc Idea: Chỉ lấy Idea có user thuộc cùng Department
        // Dùng whereHas để truy vấn sang bảng quan hệ 'user'
        $ideas = Idea::whereHas('user', function($query) use ($deptId) {
            $query->where('department_id', $deptId);
        })
        ->with(['user', 'category', 'comments']) // Eager load để query nhanh hơn
        ->latest()
        ->paginate(10);

        // 3. Logic Thống kê (Yêu cầu 3)
        // Đếm tổng số Idea trong khoa
        $totalIdeas = Idea::whereHas('user', function($query) use ($deptId) {
            $query->where('department_id', $deptId);
        })->count();

        // Đếm số nhân viên (contributors) đã đóng góp ít nhất 1 idea trong khoa
        $activeContributors = User::where('department_id', $deptId)
            ->whereHas('ideas')
            ->count();

        // Trả về View Dashboard của Coordinator
        return view('coordinator.dashboard', compact('ideas', 'totalIdeas', 'activeContributors'));
    }

    // TÍNH NĂNG EXPORT CSV (Yêu cầu 6)
    public function exportCsv()
    {
        $deptId = Auth::user()->department_id;
        $filename = "ideas-department-$deptId-" . date('Y-m-d') . ".csv";

        // Lấy dữ liệu giống logic trên nhưng get() hết chứ không paginate
        $ideas = Idea::whereHas('user', function($query) use ($deptId) {
            $query->where('department_id', $deptId);
        })->with(['user', 'category'])->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($ideas) {
            $file = fopen('php://output', 'w');

            // Ghi dòng tiêu đề
            fputcsv($file, ['ID', 'Title', 'Category', 'Author', 'Email', 'Created At']);

            // Ghi dữ liệu
            foreach ($ideas as $idea) {
                fputcsv($file, [
                    $idea->id,
                    $idea->title,
                    $idea->category->name ?? 'N/A',
                    $idea->is_anonymous ? 'Anonymous' : $idea->user->name,
                    $idea->is_anonymous ? 'N/A' : $idea->user->email,
                    $idea->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function downloadZip()
    {
        $deptId = Auth::user()->department_id;

        // 1. Lấy danh sách Idea của khoa có đính kèm file
        // Giả sử cột lưu file là 'file_path'. Nếu database bạn tên khác thì sửa lại nhé.
        $ideas = Idea::whereHas('user', function($q) use ($deptId) {
            $q->where('department_id', $deptId);
        })->whereNotNull('document')->get();

        if ($ideas->isEmpty()) {
            return back()->with('error', 'No documents found in this department to download.');
        }

        // 2. Tạo tên file Zip tạm thời
        $zipFileName = 'department_' . $deptId . '_documents_' . date('Y-m-d') . '.zip';
        $zipPath = storage_path('app/public/' . $zipFileName); // Lưu tạm vào storage

        // 3. Khởi tạo ZipArchive (Thư viện có sẵn của PHP)
        $zip = new \ZipArchive;

        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            foreach ($ideas as $idea) {

                // 1. Lấy dữ liệu từ cột document
                $docs = $idea->document;

                // 2. Kiểm tra: Nếu nó là chuỗi (1 file) -> biến thành mảng để xử lý
                if (is_string($docs)) {
                    $docs = [$docs];
                }

                // 3. Nếu nó là mảng (nhiều file) -> Lặp qua từng file để nén
                if (is_array($docs)) {
                    foreach ($docs as $file) {
                        // Đường dẫn file
                        $filePath = storage_path('app/public/' . $file);

                        // Kiểm tra file có thật trong ổ cứng không
                        if (file_exists($filePath)) {
                            // Thêm vào ZIP (Đặt tên file trong ZIP kèm ID để ko trùng)
                            $zip->addFile($filePath, 'Idea_' . $idea->id . '_' . basename($filePath));
                        }
                    }
                }
            }
            $zip->close();
        }

        // 4. Trả về file Zip và xóa file tạm sau khi tải xong
        if (file_exists($zipPath)) {
            return response()->download($zipPath)->deleteFileAfterSend(true);
        } else {
            return back()->with('error', 'Could not create ZIP file. Please try again.');
        }
    }
}
