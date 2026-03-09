@extends('layouts.admin')
@section('admin_content')
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
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end pe-4">Actions</th>
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
                                    $statusClass = 'bg-secondary';
                                    $statusLabel = 'Closed';

                                    if ($now < $year->start_date) {
                                        $statusClass = 'bg-info text-dark';
                                        $statusLabel = 'Upcoming';
                                    } elseif ($now >= $year->start_date && $now <= $year->closure_date) {
                                        $statusClass = 'bg-success';
                                        $statusLabel = 'Open for Submission';
                                    } elseif ($now > $year->closure_date && $now <= $year->final_closure_date) {
                                        $statusClass = 'bg-warning text-dark';
                                        $statusLabel = 'Submission Closed';
                                    }
                                @endphp

                                <span class="badge {{ $statusClass }} rounded-pill px-3 py-2">
                                    {{ $statusLabel }}
                                </span>
                            </td>

                            {{-- CỘT ACTIONS --}}
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    {{-- Nút Sửa --}}
                                    <a href="{{ route('admin.academic-years.edit', $year->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    {{-- Nút Xóa (Mở Popup Modal) --}}
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteYearModal{{ $year->id }}" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- POPUP CẢNH BÁO XÓA (DELETE) ACADEMIC YEAR --}}
                        <div class="modal fade" id="deleteYearModal{{ $year->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg rounded-4">
                                    <div class="modal-body p-5 text-center">
                                        <i class="bi bi-exclamation-triangle-fill text-danger mb-3" style="font-size: 4rem;"></i>
                                        <h4 class="fw-bold text-dark mb-3">Are you sure?</h4>
                                        <p class="text-muted mb-4">Do you really want to delete <strong>{{ $year->name }}</strong>? This process cannot be undone.</p>

                                        {{-- Báo đỏ và KHÓA nút xóa nếu Năm học này đã có Ý tưởng (Ideas) --}}
                                        @php
                                            // Lấy số lượng Idea thuộc về năm học này
                                            // Đảm bảo trong Model AcademicYear có hàm ideas() nha ní!
                                            $ideasCount = $year->ideas()->count();
                                        @endphp

                                        @if($ideasCount > 0)
                                            <div class="alert alert-danger mb-4 text-start">
                                                <i class="bi bi-x-circle-fill me-2"></i> <strong>Cannot delete:</strong> There are {{ $ideasCount }} idea(s) submitted in this academic year.
                                            </div>
                                        @endif

                                        <form action="{{ route('admin.academic-years.destroy', $year->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="d-flex justify-content-center gap-3">
                                                <button type="button" class="btn btn-light px-4 py-2" data-bs-dismiss="modal">Cancel</button>
                                                {{-- Disable nút nếu có idea --}}
                                                <button type="submit" class="btn btn-danger px-4 py-2" {{ $ideasCount > 0 ? 'disabled' : '' }}>
                                                    Yes, Delete it
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- END POPUP --}}

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
