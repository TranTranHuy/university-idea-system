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
        // Kiểm tra dữ liệu đầu vào
        $request->validate([
            'full_name' => 'required',
            'email' => 'required|email|unique:user',
            'password' => 'required|min:6',
        ]);

        // Tạo User mới
        User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 1,       // Đảm bảo trong DB đã có Role ID 1
            'department_id' => 1, // Đảm bảo trong DB đã có Dept ID 1
            'is_agreed_terms' => 1
        ]);

        // 👇 ĐÃ SỬA: Chuyển hướng về trang login thay vì hiện JSON
        return redirect()->route('login')->with('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
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
