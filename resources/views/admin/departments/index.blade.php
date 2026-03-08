@extends('layouts.admin')

@section('admin_content')
<div class="container py-4">

    {{-- Header & Nút Thêm Mới --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="bi bi-building text-primary me-2"></i> Department Management</h3>
            <p class="text-muted mb-0">Manage university departments, facilities, and staff allocations.</p>
        </div>

        {{-- NÚT MỞ POPUP --}}
        <button type="button" class="btn text-white shadow-sm rounded-pill px-4" style="background-color: #2f80ed;" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
            <i class="bi bi-plus-lg me-2"></i> Add Department
        </button>
    </div>

    {{-- Hiển thị thông báo LỖI (Ví dụ: Nhập trùng tên) --}}
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Hiển thị thông báo THÀNH CÔNG --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Bảng Danh Sách Department --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary text-uppercase small fw-bold">
                    <tr>
                        <th class="ps-4 py-3" style="width: 10%;">ID</th>
                        <th class="py-3" style="width: 60%;">Department Name</th>
                        <th class="py-3 text-center" style="width: 15%;">Staff Count</th>
                        <th class="py-3 text-end pe-4" style="width: 15%;">Actions</th>
                    </tr>
                </thead>
                <tbody>

                    {{-- VÒNG LẶP ĐỔ DỮ LIỆU TỪ DATABASE --}}
                    @forelse ($departments as $department)
                    <tr>
                        <td class="ps-4 fw-bold text-muted">#{{ $department->id }}</td>
                        <td>
                            <span class="fw-bold text-dark fs-6">{{ $department->department_name }}</span>
                        </td>
                        <td class="text-center">
                            {{-- Đếm số lượng Staff tự động --}}
                            <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill px-3 py-1">
                                <i class="bi bi-people-fill me-1"></i> {{ $department->users_count }} Staff
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                {{-- Nút Edit (Sẽ làm sau) --}}
                                <a href="#" class="btn btn-sm btn-outline-primary" title="Edit Department">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                {{-- Nút Delete --}}
                                <form action="{{ route('admin.departments.destroy', $department->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Are you sure you want to delete this department?')"
                                            title="Delete Department">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="bi bi-building-slash fs-1 d-block mb-2"></i>
                            No departments found. Please add a new one.
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ============================================== --}}
{{-- MODAL (POPUP) THÊM DEPARTMENT --}}
{{-- ============================================== --}}
<div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-labelledby="addDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            {{-- Tiêu đề Popup & Nút X (Đóng) --}}
            <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="modal-title fw-bold text-dark" id="addDepartmentModalLabel">
                    <i class="bi bi-building-add text-primary me-2"></i> Add New Department
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Nội dung Popup (Form) --}}
            <div class="modal-body p-4">
                {{-- Đã gắn route lưu dữ liệu --}}
                <form action="{{ route('admin.departments.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="departmentName" class="form-label fw-bold text-secondary">Department Name</label>
                        {{-- Quan trọng: name="department_name" --}}
                        <input type="text" class="form-control form-control-lg bg-light" id="departmentName" name="department_name" placeholder="e.g. IT Department" required>
                    </div>

                    <div class="d-grid mt-2">
                        <button type="submit" class="btn text-white btn-lg fw-bold shadow-sm" style="background-color: #2f80ed;">
                            Save Department
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
