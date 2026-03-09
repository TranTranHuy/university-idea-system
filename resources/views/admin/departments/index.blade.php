@extends('layouts.admin')

@section('admin_content')
<div class="container py-4">

    {{-- Header & Nút Thêm Mới --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="bi bi-building text-primary me-2"></i> Department Management</h3>
            <p class="text-muted mb-0">Manage university departments, facilities, and staff allocations.</p>
        </div>

        {{-- NÚT MỞ POPUP THÊM MỚI --}}
        <button type="button" class="btn text-white shadow-sm rounded-pill px-4" style="background-color: #2f80ed;" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
            <i class="bi bi-plus-lg me-2"></i> Add Department
        </button>
    </div>

    {{-- Hiển thị thông báo LỖI --}}
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
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary text-uppercase small fw-bold">
                    <tr>
                        <th class="ps-4 py-3" style="width: 10%;">ID</th>
                        <th class="py-3" style="width: 50%;">Department Name</th>
                        <th class="py-3 text-center" style="width: 20%;">Staff Count</th>
                        <th class="py-3 text-end pe-4" style="width: 20%;">Actions</th>
                    </tr>
                </thead>
                <tbody>

                    @forelse ($departments as $department)
                    <tr>
                        <td class="ps-4 fw-bold text-muted">#{{ $department->id }}</td>
                        <td>
                            <span class="fw-bold text-dark fs-6">{{ $department->department_name }}</span>
                        </td>
                        <td class="text-center">
                            {{-- NÚT XEM DANH SÁCH STAFF --}}
                            <button type="button" class="btn btn-sm badge bg-info bg-opacity-10 text-info border border-info rounded-pill px-3 py-1 shadow-none"
                                    data-bs-toggle="modal" data-bs-target="#viewStaffModal{{ $department->id }}"
                                    style="transition: all 0.2s;">
                                <i class="bi bi-people-fill me-1"></i> {{ $department->users_count }} Staff
                            </button>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">

                                {{-- Nút mở Popup Edit --}}
                                <button type="button" class="btn btn-sm btn-outline-primary" title="Edit Department" data-bs-toggle="modal" data-bs-target="#editDepartmentModal{{ $department->id }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                {{-- Nút mở Popup Delete --}}
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Department" data-bs-toggle="modal" data-bs-target="#deleteDepartmentModal{{ $department->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>

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

{{-- ============================================================== --}}
{{-- KHU VỰC CHỨA CÁC POPUP (NẰM NGOÀI BẢNG ĐỂ KHÔNG BỊ VỠ LAYOUT) --}}
{{-- ============================================================== --}}

@foreach ($departments as $department)
    {{-- 1. POPUP HIỂN THỊ DANH SÁCH STAFF --}}
    <div class="modal fade" id="viewStaffModal{{ $department->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-people text-primary me-2"></i> Staff List: {{ $department->department_name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    @if($department->users->count() > 0)
                        <div class="table-responsive border rounded-3">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-secondary small fw-bold">
                                    <tr>
                                        <th class="ps-4 py-3">Full Name</th>
                                        <th class="py-3">Email Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($department->users as $user)
                                        <tr>
                                            <td class="ps-4 fw-medium text-dark py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 35px; height: 35px;">
                                                        {{ substr($user->full_name, 0, 1) }}
                                                    </div>
                                                    {{ $user->full_name }}
                                                </div>
                                            </td>
                                            <td class="text-muted">{{ $user->email }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-person-x fs-1 text-muted d-block mb-3"></i>
                            <p class="text-muted mb-0">No staff accounts found in this department.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 2. POPUP SỬA (EDIT) DEPARTMENT --}}
    <div class="modal fade" id="editDepartmentModal{{ $department->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-pencil-square text-primary me-2"></i> Edit Department
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="{{ route('admin.departments.update', $department->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Department Name</label>
                            <input type="text" class="form-control form-control-lg bg-light" name="department_name" value="{{ $department->department_name }}" required>
                        </div>
                        <div class="d-grid mt-2">
                            <button type="submit" class="btn text-white btn-lg fw-bold shadow-sm" style="background-color: #2f80ed;">
                                Update Department
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. POPUP CẢNH BÁO XÓA (DELETE) DEPARTMENT --}}
    <div class="modal fade" id="deleteDepartmentModal{{ $department->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-body p-5 text-center">
                    <i class="bi bi-exclamation-triangle-fill text-danger mb-3" style="font-size: 4rem;"></i>
                    <h4 class="fw-bold text-dark mb-3">Are you sure?</h4>
                    <p class="text-muted mb-4">Do you really want to delete <strong>{{ $department->department_name }}</strong>? This process cannot be undone.</p>

                    {{-- Báo đỏ và KHÓA nút xóa nếu có nhân viên --}}
                    @if($department->users_count > 0)
                        <div class="alert alert-danger mb-4 text-start">
                            <i class="bi bi-x-circle-fill me-2"></i> <strong>Cannot delete:</strong> There are {{ $department->users_count }} staff member(s) registered in this department.
                        </div>
                    @endif

                    <form action="{{ route('admin.departments.destroy', $department->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn btn-light px-4 py-2" data-bs-dismiss="modal">Cancel</button>
                            {{-- Disable nút nếu users_count > 0 --}}
                            <button type="submit" class="btn btn-danger px-4 py-2" {{ $department->users_count > 0 ? 'disabled' : '' }}>
                                Yes, Delete it
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

{{-- 4. MODAL THÊM DEPARTMENT MỚI (CHỈ CÓ 1 CÁI NÊN ĐỂ RIÊNG DƯỚI CÙNG) --}}
<div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-building-add text-primary me-2"></i> Add New Department
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('admin.departments.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="departmentName" class="form-label fw-bold text-secondary">Department Name</label>
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
