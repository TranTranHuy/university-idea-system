@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold"><i class="bi bi-people text-primary me-2"></i> User Management</h3>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Full Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $user->full_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role_id == 1) <span class="badge bg-danger">Admin</span>
                                @elseif($user->role_id == 2) <span class="badge bg-warning text-dark">QA Manager</span>
                                @elseif($user->role_id == 3) <span class="badge bg-info text-dark">QA Coordinator</span>
                                @else <span class="badge bg-secondary">Staff</span>
                                @endif
                            </td>
                            <td>
                                @if($user->department)
                                    <span class="badge border border-primary text-primary">{{ $user->department->department_name ?? $user->department->name }}</span>
                                @else
                                    <span class="text-muted fst-italic">No Department</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary shadow-sm"><i class="bi bi-pencil-square"></i> Edit</a>

                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm" {{ $user->id === auth()->id() ? 'disabled' : '' }}><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
