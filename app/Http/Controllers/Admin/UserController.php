<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;        // <--- Bổ sung 3 dòng này
use App\Models\Role;
use App\Models\Department;
use App\Models\Idea;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
{
    // Lấy dữ liệu cho các thẻ select
    $departments = Department::all();
    $roles = Role::where('role_name', '!=', 'Admin')->get();

    // Khởi tạo Query
    $query = User::with(['role', 'department', 'ideas']);

    // 1. Chức năng Search (Tên hoặc Email)
    $query->when($request->search, function ($q) use ($request) {
        $q->where(function ($sub) use ($request) {
            $sub->where('full_name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        });
    });

    // 2. Chức năng Filter by Role
    $query->when($request->role_id, function ($q) use ($request) {
        $q->where('role_id', $request->role_id);
    });

    // 3. Chức năng Filter by Department (Chỉ áp dụng cho QAC và Staff)
    $query->when($request->department_id, function ($q) use ($request) {
        // Nếu đang chọn Role là Admin(1) hoặc QAM(2) thì bỏ qua filter này
        if (!in_array($request->role_id, [1, 2])) {
            $q->where('department_id', $request->department_id);
        }
    });

    // Sắp xếp sếp lên đầu trang và Phân trang
    $users = $query->orderBy('role_id', 'asc')
                   ->latest()
                   ->paginate(10);

    return view('admin.users.index', compact('users', 'roles', 'departments'));
}

// HÀM XỬ LÝ LƯU USER MỚI
    public function store(Request $request)
    {
        // 1. Kiểm tra dữ liệu đầu vào
        $rules = [
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|unique:user,email', // Dự án của ní bảng tên là 'user'
            'password'  => 'required|min:6',
            'role_id'   => 'required|exists:roles,id',
        ];

        // Logic: Nếu là QAC (3) hoặc Staff (4) thì bắt buộc chọn khoa
        if (in_array($request->role_id, [3, 4])) {
            $rules['department_id'] = 'required|exists:departments,id';
        } else {
            $rules['department_id'] = 'nullable';
        }

        $request->validate($rules);

        // 2. Ép Department về NULL nếu là Admin (1) hoặc QA Manager (2)
        $deptId = in_array($request->role_id, [1, 2]) ? null : $request->department_id;

        // 3. Tạo User
        User::create([
            'full_name'     => $request->full_name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password), // Mã hóa mật khẩu
            'role_id'       => $request->role_id,
            'department_id' => $deptId,
            'is_agreed_terms' => 1,
            'email_verified_at' => now(), // Admin tạo thì xác thực luôn
        ]);

        // 4. Quay lại trang danh sách kèm thông báo
        return back()->with('success', 'User ' . $request->full_name . ' has been created successfully!');
    }

// HÀM LẤY DANH SÁCH IDEAS CỦA 1 USER CỤ THỂ
    public function showIdeas($id)
    {
        // Tìm user theo ID
        $user = User::findOrFail($id);

        // Lấy tất cả Idea của User này (sắp xếp mới nhất lên đầu)
        // Dùng with('category') để lấy tên danh mục ra badge
        $ideas = Idea::with('category')->where('user_id', $id)->latest()->paginate(12);

        return view('admin.users.ideas', compact('user', 'ideas'));
    }
    // HÀM XỬ LÝ KHI ADMIN ĐỔI PHÒNG BAN CHO USER
    public function update(Request $request, $id)
{
    // 1. Validate dữ liệu
    $request->validate([
        'role_id'       => 'required|exists:roles,id',
        'department_id' => 'nullable|exists:departments,id'
    ]);

    $user = User::findOrFail($id);

    // 2. Logic nghiệp vụ: Admin (1) và QAM (2) thì không thuộc phòng ban nào
    // QAC (3) và Staff (4) thì bắt buộc phải có phòng ban
    $newDeptId = in_array($request->role_id, [1, 2]) ? null : $request->department_id;

    // Nếu là QAC/Staff mà Admin quên chọn phòng ban thì báo lỗi nhẹ
    if (in_array($request->role_id, [3, 4]) && !$request->department_id) {
        return back()->withErrors(['Please select a department for this role!']);
    }

    // 3. Cập nhật dữ liệu
    $user->update([
        'role_id'       => $request->role_id,
        'department_id' => $newDeptId
    ]);

    return back()->with('success', 'User permissions updated successfully!');
}

public function destroy($id)
    {
        $user = \App\Models\User::findOrFail($id);

        // 1. Kiểm tra bảo mật kép: Không cho xóa Admin, QAM, QAC (phòng hờ bị hack qua URL)
        if (in_array($user->role_id, [1, 2, 3])) {
            return back()->with('error', 'Security Alert: Cannot delete Admin, QAM, or QAC accounts!');
        }

        // 2. Kiểm tra bảo mật kép: Không cho xóa user đã đăng Idea
        if ($user->ideas()->count() > 0) {
            return back()->with('error', 'Cannot delete user! This account has associated ideas.');
        }

        // 3. Đủ điều kiện an toàn -> Tiến hành xóa
        $user->delete();

        return back()->with('success', 'User completely deleted from the system!');
    }

}
