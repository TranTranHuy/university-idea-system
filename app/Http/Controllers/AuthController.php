<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // --- 1. XỬ LÝ ĐĂNG KÝ ---
    public function register(Request $request)
    {
        // 1. Chỉ nhận và kiểm tra các dữ liệu cơ bản (Bỏ qua hoàn toàn role_id và department_id)
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:user,email', // Đảm bảo bảng của bạn là 'user'
            'password' => 'required|string|min:6|confirmed', // Tự động so sánh với password_confirmation
        ]);

        // 2. Tạo tài khoản mới
        $user = new \App\Models\User();
        $user->full_name = $request->full_name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);

        // 🛡️ CHỐT CHẶN BẢO MẬT: ÉP BUỘC QUYỀN MẶC ĐỊNH LÀ STAFF 🛡️
        $user->role_id = 4; // 4 là ID của Staff
        $user->department_id = null; // Chưa thuộc khoa nào, đợi Admin phân công

        $user->is_agreed_terms = $request->has('agree') ? 1 : 0;

        // 3. Lưu vào Database
        $user->save();

        // 4. Chuyển hướng về trang Đăng nhập kèm thông báo chờ duyệt
        return redirect()->route('login')->with('success', 'Đăng ký thành công! Vui lòng chờ Admin phân công phòng ban để bắt đầu nộp ý tưởng.');
    }

    // --- 2. XỬ LÝ ĐĂNG NHẬP ---
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            // 👇 ĐÃ SỬA: Chuyển hướng về trang chủ
            return redirect()->route('home');
        }

        // 👇 ĐÃ SỬA: Trả về trang cũ kèm lỗi
        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ]);
    }

    // --- 3. ĐĂNG XUẤT ---
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // Quay về trang login
        return redirect()->route('login');
    }

}
