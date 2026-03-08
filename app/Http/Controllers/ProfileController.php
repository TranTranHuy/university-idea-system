<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        // 1. Lấy thông tin staff đang đăng nhập
        $user = Auth::user(); 

        // 2. Lấy danh sách Ideas do Staff này đăng (có phân trang)
        $myIdeas = $user->ideas()->latest()->paginate(5); 

        // 3. Lấy lịch sử hoạt động (Ví dụ: 10 bình luận gần nhất)
        $recentComments = $user->comments()->with('idea')->latest()->take(10)->get();

        return view('staff.profile', compact('user', 'myIdeas', 'recentComments'));
    }

    public function update(Request $request)
    {
        // 1. Kiểm tra dữ liệu đầu vào (Validation)
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            // Tùy chỉnh câu thông báo lỗi cho thân thiện
            'name.required' => 'Full name is required.',
            'name.max' => 'Full name cannot exceed 255 characters.',
        ]);

        // 2. Lấy User đang đăng nhập hiện tại
        /** @var \App\Models\User $user */
        $user = Auth::user(); 

        // 3. Cập nhật dữ liệu
        $user->full_name = $request->name;
        
        // 4. Lưu xuống Database
        $user->save();

        // 5. Quay lại trang cũ và mang theo một thông báo thành công
        return back()->with('success', 'Your profile has been updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        // 1. Kiểm tra lính gác (Validation)
        $request->validate([
        'current_password' => 'required|current_password', 
        'new_password' => 'required|string|min:8|confirmed', 
        ], [
        'current_password.required' => 'Current password is required.',
        'current_password.current_password' => 'The current password you entered is incorrect.',
        'new_password.required' => 'New password is required.',
        'new_password.min' => 'New password must be at least 8 characters long.',
        'new_password.confirmed' => 'New password confirmation does not match.',
        ]);

        // 2. Lấy User đang đăng nhập
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 3. Mã hóa mật khẩu mới và lưu vào DB
        $user->password = Hash::make($request->new_password);
        $user->save();

        // 4. Báo cáo thành công
        return back()->with('success', 'Your password has been changed securely!');
    }
}
