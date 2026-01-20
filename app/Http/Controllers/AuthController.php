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
            'email' => 'required|email|unique:user', // Check trùng email
            'password' => 'required|min:6',
        ]);

        // Tạo User mới vào Database
        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Mã hóa password
            'role_id' => 1,       // Mặc định là Admin (vì bạn vừa tạo ID 1 là Admin)
            'department_id' => 1, // Mặc định là IT (vì bạn vừa tạo ID 1 là IT)
            'is_agreed_terms' => 1 
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Register Success! User ID: ' . $user->id,
            'data' => $user
        ]);
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
            return response()->json([
                'status' => 'success',
                'message' => 'Đăng nhập thành công!',
                'user' => Auth::user()
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Sai email hoặc mật khẩu!',
        ], 401);
    }

    // --- 3. ĐĂNG XUẤT ---
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => 'Đã đăng xuất!']);
    }
}