<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. TẠO ROLES (Quyền hạn)
        // Lưu lại biến để lấy ID gán cho user bên dưới
        $adminRole = Role::create(['role_name' => 'Admin']);
        $qamRole   = Role::create(['role_name' => 'QA Manager']);
        $qacRole   = Role::create(['role_name' => 'QA Coordinator']);
        $staffRole = Role::create(['role_name' => 'Staff']);

        // 2. TẠO DEPARTMENTS (Phòng ban)
        $itDept = Department::create(['department_name' => 'IT Support']);
        $bizDept = Department::create(['department_name' => 'Business']);
        $designDept = Department::create(['department_name' => 'Graphic Design']);

        // 3. TẠO TÀI KHOẢN MẪU (Để đăng nhập test)

        // --- A. Tài khoản ADMIN ---
        User::create([
            'full_name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123456'), // Mật khẩu chung
            'role_id' => $adminRole->id,
            'department_id' => null, // Admin không thuộc phòng nào
            'email_verified_at' => now(),
        ]);

        // --- B. Tài khoản QA MANAGER (Quản lý chất lượng) ---
        User::create([
            'full_name' => 'QA Manager Boss',
            'email' => 'qam@gmail.com',
            'password' => Hash::make('123456'),
            'role_id' => $qamRole->id,
            'department_id' => null, // QAM quản lý toàn trường
            'email_verified_at' => now(),
        ]);

        // --- C. Tài khoản QA COORDINATOR (Khoa IT) ---
        User::create([
            'full_name' => 'QA Coordinator IT',
            'email' => 'qac_it@gmail.com',
            'password' => Hash::make('123456'),
            'role_id' => $qacRole->id,
            'department_id' => $itDept->id, // Chỉ quản lý khoa IT
            'email_verified_at' => now(),
        ]);

        // --- D. Tài khoản STAFF (Nhân viên Khoa IT) ---
        User::create([
            'full_name' => 'Nguyen Van Staff',
            'email' => 'staff_it@gmail.com',
            'password' => Hash::make('123456'),
            'role_id' => $staffRole->id,
            'department_id' => $itDept->id, // Thuộc khoa IT
            'email_verified_at' => now(),
        ]);
        
        // --- E. Tài khoản STAFF (Nhân viên Khoa Business - Để test QAC IT không nhìn thấy ông này) ---
        User::create([
            'full_name' => 'Tran Thi Business',
            'email' => 'staff_biz@gmail.com',
            'password' => Hash::make('123456'),
            'role_id' => $staffRole->id,
            'department_id' => $bizDept->id, // Thuộc khoa Business
            'email_verified_at' => now(),
        ]);
    }
}