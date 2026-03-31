<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // 1. Xem danh sách người dùng
    public function index()
    {
        // Lấy danh sách user kèm theo tên Quyền và tên Khoa
        $users = User::with(['role', 'department'])->latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    // 2. Mở Form chỉnh sửa cấp quyền
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $departments = Department::all();

        return view('admin.users.edit', compact('user', 'roles', 'departments'));
    }

    // 3. Xử lý lưu quyền & khoa mới
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:user,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $user->full_name = $request->full_name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;
        $user->department_id = $request->department_id;

        // Nếu admin muốn đổi mật khẩu cho user
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật tài khoản thành công!');
    }

    // 4. Xóa tài khoản
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Không cho phép Admin tự xóa chính mình
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Bạn không thể tự xóa tài khoản của chính mình!');
        }

        $user->delete();
        return back()->with('success', 'Đã xóa tài khoản thành công!');
    }
}
