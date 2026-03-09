<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        // Hàm withCount('users') sẽ tự động đếm số lượng tài khoản trong bảng 'user'
        // dựa vào mối quan hệ ní đã tạo, và sinh ra biến $department->users_count
        $departments = Department::withCount('users')->orderBy('id', 'asc')->get();
        return view('admin.departments.index', compact('departments'));
    }

    public function store(Request $request)
    {
        // Kiểm tra dữ liệu: bắt buộc nhập và không được trùng tên
        $request->validate([
            'department_name' => 'required|string|max:255|unique:departments,department_name'
        ]);

        // Lưu vào Database
        Department::create([
            'department_name' => $request->department_name
        ]);

        return back()->with('success', 'New Department added successfully!');
    }

    // HÀM XỬ LÝ LƯU KHI EDIT
    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        // Validate: Không được trùng tên với các phòng khác (ngoại trừ chính nó)
        $request->validate([
            'department_name' => 'required|string|max:255|unique:departments,department_name,' . $id
        ]);

        $department->update([
            'department_name' => $request->department_name
        ]);

        return back()->with('success', 'Department updated successfully!');
    }

    // HÀM XỬ LÝ KHI BẤM XÓA
    public function destroy($id)
    {
        $department = Department::findOrFail($id);

        // RÀNG BUỘC: Nếu phòng ban đang có nhân viên (users > 0) thì chặn lại và báo lỗi
        if ($department->users()->count() > 0) {
            return back()->withErrors(['Hệ thống chặn: Không thể xóa phòng ban này vì đang có tài khoản Staff bên trong!']);
        }

        // Nếu an toàn (0 staff) thì cho phép xóa
        $department->delete();

        return back()->with('success', 'Department deleted successfully!');
    }
}
