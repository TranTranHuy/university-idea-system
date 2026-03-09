@extends('layouts.admin')

@section('admin_content')
<div class="container py-4">

    {{-- Phần Header & Nút Thêm User --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="bi bi-people-fill text-primary me-2"></i> User Management</h3>
            <p class="text-muted mb-0">Manage accounts, roles, and track user activities (including anonymous posts).</p>
        </div>
        <button type="button" class="btn text-white shadow-sm rounded-pill px-4" style="background-color: #2f80ed;" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus-fill me-2"></i> Add New User
        </button>
    </div>

    {{-- Bộ Lọc Tìm Kiếm --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 bg-light" placeholder="Search by name or email...">
                    </div>
                </div>

<div class="col-md-3">
    <select name="role_id" id="mainFilterRole" class="form-select bg-light">
        <option value="">All Roles</option>
        @foreach($roles as $role)
            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                {{ $role->role_name }}
            </option>
        @endforeach
    </select>
</div>


<div class="col-md-3">
    <select name="department_id" id="mainFilterDept" class="form-select bg-light"
            {{ in_array(request('role_id'), [1, 2]) ? 'disabled' : '' }}>
        <option value="">All Departments</option>
        @foreach($departments as $department)
            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                {{ $department->department_name }}
            </option>
        @endforeach
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
                    @forelse ($users as $user)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                                    {{ strtoupper(substr($user->full_name, 0, 2)) }}
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">{{ $user->full_name }}</h6>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                            </div>
                        </td>


                        <td>
    @php

        $roleColor = 'secondary'; // Mặc định là Staff
        if ($user->role_id == 1) $roleColor = 'primary';      // Admin
        elseif ($user->role_id == 2) $roleColor = 'info';     // QA Manager
        elseif ($user->role_id == 3) $roleColor = 'warning';  // QA Coordinator
    @endphp

    <span class="badge bg-{{ $roleColor }} bg-opacity-10 text-{{ $roleColor }} border border-{{ $roleColor }} mb-1">
        {{ $user->role->role_name ?? 'No Role' }}
    </span>
    <br>
    <div class="small text-muted fw-medium">
        <i class="bi bi-building me-1"></i>
        {{-- Đã thay đổi fallback thành General Management theo ý ní --}}
        {{ $user->department->department_name ?? 'General Management' }}
    </div>
</td>

                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark mb-1">Total Posts: {{ $user->ideas->count() }}</span>
                                @php $anonymousCount = $user->ideas->where('is_anonymous', 1)->count(); @endphp
                                @if($anonymousCount > 0)
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger d-inline-block" style="width: fit-content;">
                                        <i class="bi bi-incognito me-1"></i> {{ $anonymousCount }} Anonymous Ideas
                                    </span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success d-inline-block" style="width: fit-content;">
                                        <i class="bi bi-check-circle me-1"></i> 0 Anonymous
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td><span class="badge bg-success rounded-pill px-3 py-2">Active</span></td>
                        <td class="text-end pe-4">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><a class="dropdown-item" href="{{ route('admin.users.ideas', $user->id) }}"><i class="bi bi-eye text-primary me-2"></i> View User Ideas</a></li>
                                    <li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#transferModal{{ $user->id }}">
                                        <i class="bi bi-person-gear text-warning me-2"></i> Update Permissions
                                    </button></li>
                                    <li><hr class="dropdown-divider"></li>

                                    {{-- ========================================= --}}
                                    {{-- ĐÃ SỬA CHỖ NÀY: LOGIC XÓA TÀI KHOẢN       --}}
                                    {{-- ========================================= --}}
                                    <li>
                                        @php
                                            $ideaCount = $user->ideas->count();
                                            // Khóa tài khoản có role: 1 (Admin), 2 (QAM), 3 (QAC)
                                            $isProtectedRole = in_array($user->role_id, [1, 2, 3]);
                                        @endphp

                                        @if($ideaCount > 0 || $isProtectedRole)
                                            {{-- Nút Xóa Bị Khóa + Hiện Alert Giải Thích (Như cũ) --}}
                                            @php
                                                $alertMsg = $ideaCount > 0
                                                    ? "Cannot delete: This user has posted {$ideaCount} idea(s)."
                                                    : "Cannot delete: This account belongs to QAM, QAC, or Admin.";
                                            @endphp
                                            <button type="button" class="dropdown-item text-muted opacity-50" style="cursor: not-allowed;" onclick="alert('{{ $alertMsg }}')">
                                                <i class="bi bi-trash-fill me-2"></i> Delete User
                                            </button>
                                        @else
                                            {{-- ĐÃ SỬA: Thay form confirm bằng Nút Mở Modal Popup --}}
                                            <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}">
                                                <i class="bi bi-trash-fill me-2"></i> Delete User
                                            </button>
                                        @endif
                                    </li>
                                    {{-- KẾT THÚC LOGIC XÓA --}}

                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-end">{{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
</div>

{{-- MODAL KHU VỰC --}}
@foreach ($users as $user)

{{-- MODAL CẬP NHẬT QUYỀN HẠN (GIỮ NGUYÊN) --}}
<div class="modal fade" id="transferModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-person-gear text-primary me-2"></i> Update Permissions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Assign Role</label>
                        <select name="role_id" class="form-select bg-light border-0 role-edit-select" data-user-id="{{ $user->id }}" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ $role->role_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-secondary">Department Assignment</label>
                        <select name="department_id" id="editDeptSelect{{ $user->id }}" class="form-select bg-light border-0"
                                {{ in_array($user->role_id, [1, 2]) ? 'disabled' : '' }}>
                            <option value="">No Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ $user->department_id == $department->id ? 'selected' : '' }}>{{ $department->department_name }}</option>
                            @endforeach
                        </select>
                        <small class="text-danger d-none" id="editDeptHint{{ $user->id }}" style="font-size: 0.75rem;">* Admin & QAM don't belong to a department.</small>
                    </div>
                    <div class="d-grid"><button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm" style="background-color: #2f80ed;">Save Changes</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ========================================= --}}
{{-- ĐÃ THÊM: MODAL XÓA TÀI KHOẢN (POPUP Ở GIỮA) --}}
{{-- ========================================= --}}
@php
    $ideaCountModal = $user->ideas->count();
    $isProtectedRoleModal = in_array($user->role_id, [1, 2, 3]);
@endphp
@if(!$isProtectedRoleModal && $ideaCountModal == 0)
<div class="modal fade text-start" id="deleteUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body p-5 text-center">
                <i class="bi bi-exclamation-triangle-fill text-danger mb-3" style="font-size: 4rem;"></i>
                <h4 class="fw-bold text-dark mb-3">Are you sure?</h4>
                <p class="text-muted mb-4">Do you really want to delete user <strong>{{ $user->full_name }}</strong>? This action cannot be undone.</p>

                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="m-0">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn btn-light px-4 py-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger px-4 py-2">Yes, Delete it</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endforeach
{{-- KẾT THÚC MODAL KHU VỰC --}}


{{-- MODAL THÊM USER MỚI (GIỮ NGUYÊN) --}}
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-person-plus-fill text-primary me-2"></i> Create New Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-secondary">Full Name</label>
                            <input type="text" name="full_name" class="form-control bg-light border-0" placeholder="Enter full name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Email Address</label>
                            <input type="email" name="email" class="form-control bg-light border-0" placeholder="Enter email address" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Password</label>
                            <input type="password" name="password" class="form-control bg-light border-0" placeholder="••••••••" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Role</label>
                            <select name="role_id" id="modalRoleSelect" class="form-select bg-light border-0" required>
                                <option value="" selected disabled>Choose Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Department</label>
                            <select name="department_id" id="modalDeptSelect" class="form-select bg-light border-0">
                                <option value="">No Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                                @endforeach
                            </select>
                            <small id="modalDeptHint" class="text-danger d-none mt-1" style="font-size: 0.75rem;">Admin & QA Manager don't belong to any department.</small>
                        </div>
                    </div>
                    <div class="d-grid mt-4"><button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm rounded-3 py-3" style="background-color: #2f80ed;">Save Account</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Logic khóa Department cho ADD modal
document.getElementById('modalRoleSelect').addEventListener('change', function() {
    const deptSelect = document.getElementById('modalDeptSelect');
    const deptHint = document.getElementById('modalDeptHint');
    if (this.value == "1" || this.value == "2") {
        deptSelect.value = ""; deptSelect.disabled = true;
        deptSelect.classList.add('bg-secondary', 'bg-opacity-10');
        deptHint.classList.remove('d-none');
    } else {
        deptSelect.disabled = false; deptSelect.classList.remove('bg-secondary', 'bg-opacity-10');
        deptHint.classList.add('d-none');
    }
});

// Logic khóa Department cho EDIT modals
document.querySelectorAll('.role-edit-select').forEach(select => {
    select.addEventListener('change', function() {
        const userId = this.getAttribute('data-user-id');
        const deptSelect = document.getElementById('editDeptSelect' + userId);
        const deptHint = document.getElementById('editDeptHint' + userId);
        if (this.value == "1" || this.value == "2") {
            deptSelect.value = ""; deptSelect.disabled = true;
            deptSelect.classList.add('bg-secondary', 'bg-opacity-10');
            deptHint.classList.remove('d-none');
        } else {
            deptSelect.disabled = false; deptSelect.classList.remove('bg-secondary', 'bg-opacity-10');
            deptHint.classList.add('d-none');
        }
    });
});
</script>
@endsection
