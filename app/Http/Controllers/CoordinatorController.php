<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use ZipArchive;

class CoordinatorController extends Controller
{
    public function index()
    {
        // Lấy đúng Department của Coordinator
        $deptId = Auth::user()->department_id;

        if (!$deptId) {
            return redirect()->route('home')->with('error', 'Tài khoản của bạn chưa được cấp department_id!');
        }

        // Lấy Idea theo department_id
        $ideas = Idea::where('department_id', $deptId)
            ->with(['user', 'category', 'comments'])
            ->latest()
            ->paginate(10);

        $totalIdeas = Idea::where('department_id', $deptId)->count();
        $activeContributors = Idea::where('department_id', $deptId)->distinct('user_id')->count('user_id');

        return view('coordinator.dashboard', compact('ideas', 'totalIdeas', 'activeContributors'));
    }

    public function exportCsv()
    {
        $deptId = Auth::user()->department_id;
        $filename = "ideas-department-$deptId-" . date('Y-m-d') . ".csv";

        $ideas = Idea::where('department_id', $deptId)->with(['user', 'category'])->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($ideas) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Title', 'Category', 'Author', 'Email', 'Created At']);
            foreach ($ideas as $idea) {
                fputcsv($file, [
                    $idea->id, $idea->title, $idea->category->name ?? 'N/A',
                    $idea->is_anonymous ? 'Anonymous' : ($idea->user->full_name ?? $idea->user->name),
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
        $ideas = Idea::where('department_id', $deptId)->whereNotNull('document')->get();

        if ($ideas->isEmpty()) { return back()->with('error', 'Không tìm thấy tài liệu nào trong khoa của bạn để tải về.'); }

        $zipFileName = 'department_' . $deptId . '_documents_' . date('Y-m-d') . '.zip';
        $zipPath = storage_path('app/public/' . $zipFileName);
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($ideas as $idea) {
                $docs = $idea->document;
                if (is_string($docs)) {
                    $decoded = json_decode($docs, true);
                    $docs = is_array($decoded) ? $decoded : [$docs];
                }
                if (is_array($docs)) {
                    foreach ($docs as $file) {
                        $filePath = storage_path('app/public/' . $file);
                        if (file_exists($filePath)) {
                            $studentEmail = \Illuminate\Support\Str::slug($idea->user->email ?? 'Anonymous');
                            $zip->addFile($filePath, $studentEmail . '/Idea_' . $idea->id . '_' . basename($filePath));
                        }
                    }
                }
            }
            $zip->close();
        }
        if (file_exists($zipPath)) { return response()->download($zipPath)->deleteFileAfterSend(true); }
        else { return back()->with('error', 'Lỗi khi tạo file ZIP.'); }
    }
}
