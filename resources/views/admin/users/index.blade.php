@extends('layouts.admin')

@section('admin_content')
<div class="container py-4">

    {{-- Phần Header & Nút Thêm User --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="bi bi-people-fill text-primary me-2"></i> User Management</h3>
            <p class="text-muted mb-0">Manage accounts, roles, and track user activities (including anonymous posts).</p>
        </div>
        {{-- BACKEND: Nhớ gắn route('admin.users.create') vào href --}}
        <a href="#" class="btn text-white shadow-sm rounded-pill px-4" style="background-color: #2f80ed;">
            <i class="bi bi-person-plus-fill me-2"></i> Add New User
        </a>
    </div>

    {{-- Bộ Lọc Tìm Kiếm (Giao diện giữ nguyên, Backend sẽ viết logic action form) --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form class="row g-3 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="Search by name or email...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select bg-light">
                        <option value="">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="qam">QA Manager</option>
                        <option value="coordinator">QA Coordinator</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
                <div class="col-md-3">
                    {{-- BACKEND: Đổ vòng lặp các Departments vào thẻ option này --}}
                    <select name="department_id" class="form-select bg-light">
                        <option value="">All Departments</option>
                        <option value="1">[Department Name 1]</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-secondary">Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Bảng Danh Sách User --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary text-uppercase small fw-bold">
                    <tr>
                        <th class="ps-4 py-3">User Info</th>
                        <th class="py-3">Role & Department</th>
                        <th class="py-3">Engagement (Ideas)</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- ========================================== --}}
                    {{-- BACKEND: Dùng vòng lặp @forelse ($users as $user) ở đây --}}
                    {{-- ========================================== --}}

                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                {{-- Avatar: Có thể dùng substr($user->name, 0, 2) để lấy 2 chữ cái đầu --}}
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                                    [AV]
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">[User Full Name]</h6>
                                    <small class="text-muted">[user@email.com]</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary mb-1">[Role Name]</span>
                            <div class="small text-muted fw-medium"><i class="bi bi-building me-1"></i> [Department Name]</div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark mb-1">Total Posts: [0]</span>
                                {{-- CHỈ ADMIN MỚI THẤY DÒNG NÀY: Dùng biến đếm số bài is_anonymous = 1 của user này --}}
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger d-inline-block" style="width: fit-content;" title="Admin view only">
                                    <i class="bi bi-incognito me-1"></i> [0] Anonymous Ideas
                                </span>
                            </div>
                        </td>
                        <td>
                            {{-- BACKEND: Dùng if/else để đổi màu status (Active/Suspended) --}}
                            <span class="badge bg-success rounded-pill px-3 py-2">[Status]</span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    {{-- BACKEND: Gắn link route tương ứng vào các thẻ <a> dưới đây --}}
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-eye text-primary me-2"></i> View User Ideas</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-pencil-square text-warning me-2"></i> Edit Account</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        {{-- LƯU Ý CHO BACKEND: Nếu làm chức năng khóa tài khoản/xóa thì đổi thẻ <a> này thành <form> + method DELETE/PUT --}}
                                        <a class="dropdown-item text-danger" href="#"><i class="bi bi-lock-fill me-2"></i> Suspend User</a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>

                    {{-- ========================================== --}}
                    {{-- BACKEND: Dùng @empty khi không có dữ liệu --}}
                    {{-- ========================================== --}}
                    {{--
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-people-fill fs-1 d-block mb-2 text-light"></i>
                            No users found.
                        </td>
                    </tr>
                    --}}
                    {{-- BACKEND: Kết thúc vòng lặp @endforelse --}}

                </tbody>
            </table>
        </div>

        {{-- Phân trang (Pagination) --}}
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-end">
                {{-- BACKEND: Chỉ cần gọi hàm {{ $users->links() }} ở đây là Laravel tự động gen ra thanh trang (nhớ dùng pagination bootstrap) --}}
                [Pagination Block]
            </div>
        </div>
    </div>
</div>
@endsection
