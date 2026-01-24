<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Idea;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class IdeaController extends Controller
{
    public function create()
    {
        // Lấy tất cả danh mục để hiện thị trong dropdown chọn Category
        $categories = Category::all();
        return view('createideapage', compact('categories'));
    }

    public function store(Request $request)
    {
        // 1. Validation: Đảm bảo dữ liệu gửi lên đúng yêu cầu
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required', // Đây là tên từ thẻ <textarea name="description">
            'category_id' => 'required|exists:categories,id',
            'terms' => 'accepted', // Bắt buộc tích vào Agree to Terms
            'document' => 'nullable|mimes:pdf,doc,docx,zip|max:2048', // Giới hạn file 2MB
        ]);

        // 2. Tạo Idea mới
        $idea = new Idea();
        $idea->title = $request->title;

        // QUAN TRỌNG: Cột trong Database của bạn là 'content'
        // nên phải gán bằng $request->description từ Form gửi lên
        $idea->content = $request->description;

        $idea->category_id = $request->category_id;
        $idea->user_id = Auth::id();

        // Xử lý ẩn danh (Check ẩn danh: Nếu nút gạt bật thì giá trị là 1)
        $idea->is_anonymous = $request->has('is_anonymous') ? 1 : 0;

        // 3. Xử lý Upload file (nếu có)
        if ($request->hasFile('document')) {
            // Lưu vào thư mục storage/app/public/documents
            $path = $request->file('document')->store('documents', 'public');
            // Gán vào cột 'document' trong Database
            $idea->document = $path;
        }

        // 4. Lưu vào Database
        $idea->save();

        // 5. Chuyển hướng kèm thông báo thành công
        return redirect()->back()->with('success', 'Your idea has been submitted successfully!');
    }
}
