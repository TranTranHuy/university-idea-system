<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Department;

class AuthController extends Controller
{
    // --- 1. XỬ LÝ ĐĂNG KÝ ---
    // 1. HÀM HIỂN THỊ FORM ĐĂNG KÝ (Chỉ lấy Department và show giao diện)
    public function showRegister()
    {
        $departments = Department::all();
        return view('register', compact('departments'));
    }

    // 2. HÀM XỬ LÝ LƯU DỮ LIỆU KHI BẤM NÚT ĐĂNG KÝ
    public function register(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào (Thêm validate cho cái department_id)
        $request->validate([
            'full_name' => 'required',
            'email' => 'required|email|unique:user', // Bảng user của ní không có 's'
            'password' => 'required|min:6',
            'department_id' => 'required' // Bắt buộc người dùng phải chọn phòng ban
        ]);

        // Tạo User mới
        User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 1,
            'department_id' => $request->department_id, // 👇 ĐÃ SỬA: Lấy đúng ID phòng ban mà người dùng chọn ở Form
            'is_agreed_terms' => 1
        ]);

        // Chuyển hướng về trang login
        return redirect()->route('login')->with('success', 'Registration successful! Please log in.');
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
