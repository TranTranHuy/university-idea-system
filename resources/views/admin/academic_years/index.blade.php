{{-- <h1>Danh sách Năm học (Academic Years)</h1>

<a href="{{ route('admin.academic-years.create') }}">Creating New Academic Year</a>

@if(session('success'))
    <div style="color: green; font-weight: bold; margin: 10px 0;">
        {{ session('success') }}
    </div>
@endif

<table border="1" cellpadding="10" style="border-collapse: collapse; margin-top: 20px;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Start Date</th>
            <th>Closure Date (Idea)</th>
            <th>Final Closure Date (Comment)</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($years as $year)
        <tr>
            <td>{{ $year->id }}</td>
            <td>{{ $year->name }}</td>
            <td>{{ $year->start_date }}</td>
            <td>{{ $year->closure_date }}</td>
            <td>{{ $year->final_closure_date }}</td>
            <td>
                {{-- Nút sửa --}}
                {{-- <a href="{{ route('admin.academic-years.edit', $year->id) }}">Edit</a> --}}

                {{-- Nút xóa --}}
                {{-- <form action="{{ route('admin.academic-years.destroy', $year->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Chắc chắn xóa?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table> --}}
@extends('layouts.master')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-1">📅 Academic Years</h2>
            <p class="text-muted mb-0">Manage university semesters and deadlines.</p>
        </div>
        <a href="{{ route('admin.academic-years.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4">
            <i class="bi bi-plus-lg me-2"></i> Create New Year
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary text-uppercase small fw-bold">
                        <tr>
                            <th class="ps-4 py-3">ID</th>
                            <th class="py-3">Name</th>
                            <th class="py-3">Timeline</th>
                            <th class="py-3">Status</th> <th class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($years as $year)
                        <tr>
                            <td class="ps-4 fw-bold text-muted">#{{ $year->id }}</td>
                            <td>
                                <span class="fw-bold text-dark">{{ $year->name }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column small">
                                    <span class="text-muted">Start: <span class="text-dark fw-bold">{{ $year->start_date }}</span></span>
                                    <span class="text-primary">Idea Deadline: <strong>{{ $year->closure_date }}</strong></span>
                                    <span class="text-danger">Final Close: <strong>{{ $year->final_closure_date }}</strong></span>
                                </div>
                            </td>
                            <td>
                                {{-- Logic hiển thị trạng thái chi tiết --}}
                                @php
                                    $now = now();
                                    // Mặc định là Đóng (Closed)
                                    $statusClass = 'bg-secondary';
                                    $statusLabel = 'Closed';

                                    // 1. Chưa bắt đầu
                                    if ($now < $year->start_date) {
                                        $statusClass = 'bg-info text-dark';
                                        $statusLabel = 'Upcoming';
                                    } 
                                    // 2. Đang trong thời gian NỘP IDEA (Open)
                                    elseif ($now >= $year->start_date && $now <= $year->closure_date) {
                                        $statusClass = 'bg-success'; // Màu xanh lá
                                        $statusLabel = 'Open for Submission';
                                    } 
                                    // 3. Hết hạn nộp Idea, nhưng vẫn cho COMMENT (Partial Open)
                                    elseif ($now > $year->closure_date && $now <= $year->final_closure_date) {
                                        $statusClass = 'bg-warning text-dark'; // Màu vàng
                                        $statusLabel = 'Submission Closed';
                                    }
                                @endphp

                                <span class="badge {{ $statusClass }} rounded-pill px-3 py-2">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    {{-- Nút Sửa --}}
                                    <a href="{{ route('admin.academic-years.edit', $year->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    {{-- Nút Xóa --}}
                                    <form action="{{ route('admin.academic-years.destroy', $year->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this Academic Year? This action cannot be undone.')" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                No academic years found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
