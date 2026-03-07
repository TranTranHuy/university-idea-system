<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class PasswordResetController extends Controller
{
    // 1. Hiển thị form nhập email
    public function showLinkRequestForm()
    {
        return view('auth.forgot_password');
    }

    // 2. Xử lý khi bấm nút "Send" (Bỏ qua bước gửi mail)
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        // Nếu email không tồn tại trong DB
        if (!$user) {
            return back()->withErrors(['email' => 'We cannot find a user with that email address.']);
        }

        // Tạo Token ảo và chuyển thẳng sang trang đổi pass
        $token = app('auth.password.broker')->createToken($user);

        return redirect()->route('password.reset', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    // 3. Hiển thị form tạo mật khẩu mới
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset_password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    // 4. Lưu mật khẩu mới vào Database
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Invalid email.']);
        }

        // Đổi pass và lưu lại
        $user->password = Hash::make($request->password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        return redirect()->route('login')->with('success', 'Your password has been reset successfully! Please login with your new password.');
    }
}
