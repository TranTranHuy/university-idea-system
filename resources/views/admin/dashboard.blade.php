@extends('layouts.admin')

@section('admin_content')
<div class="mb-5">
    <h4 class="mb-4 fw-bold text-dark">Administrative Dashboard</h4>

    <style>
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .stat-card { transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-3px); }
        .dashboard-list .list-group-item { padding: 1rem 0; border-color: #f1f5f9; }
        .dashboard-list .list-group-item:last-child { border-bottom: none; }
    </style>

    {{-- 4 THẺ THỐNG KÊ --}}
    <div class="row g-4 mb-5">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3 me-3"><i class="bi bi-chat-square-text fs-4"></i></div>
                    <div><div class="text-muted small">Total Ideas</div><div class="h4 mb-0 fw-bold">{{ $totalIdeas }}</div></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 bg-success bg-opacity-10 text-success p-3 me-3"><i class="bi bi-people fs-4"></i></div>
                    <div><div class="text-muted small">Total Users</div><div class="h4 mb-0 fw-bold">{{ $totalUsers }}</div></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 bg-warning bg-opacity-10 text-warning p-3 me-3"><i class="bi bi-building fs-4"></i></div>
                    <div><div class="text-muted small">Departments</div><div class="h4 mb-0 fw-bold">{{ $totalDepts }}</div></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 bg-info bg-opacity-10 text-info p-3 me-3"><i class="bi bi-mortarboard fs-4"></i></div>
                    <div><div class="text-muted small">Academic Years</div><div class="h4 mb-0 fw-bold">{{ $totalAcademicYears }}</div></div>
                </div>
            </div>
        </div>
    </div>

    {{-- DANH SÁCH IDEAS --}}
    <div class="row g-4 mb-4">

        {{-- Khung Latest Ideas --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-lightning-charge text-warning me-2"></i>Latest Ideas</h6>

                    {{-- CHỨC NĂNG VIEW ALL (Sử dụng url('/') cho an toàn) --}}
                    {{-- Ní có thể đổi '/home' thành đường dẫn đúng của trang danh sách Idea --}}
                    <a href="{{ url('/') }}" class="text-decoration-none small fw-bold">View all</a>
                </div>

                <ul class="list-group list-group-flush dashboard-list">
                    @forelse($latestIdeas as $idea)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-medium text-dark">{{ Str::limit($idea->title, 40) }}</h6>
                                <small class="text-muted">By {{ $idea->user->full_name ?? 'Anonymous' }} • {{ $idea->created_at->diffForHumans() }}</small>
                            </div>

                            {{-- NÚT DẤU + CHO LATEST IDEAS --}}
                            <a href="{{ route('ideas.show', $idea->id) }}" class="btn btn-sm btn-warning bg-opacity-10 text-warning rounded-circle d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;" title="View Details">
                                <i class="bi bi-plus-lg fw-bold"></i>
                            </a>
                        </div>
                    </li>
                    @empty
                    <p class="text-muted small py-3">No ideas posted yet.</p>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- Khung Most Popular Ideas --}}
        {{-- Khung Most Popular Ideas --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-fire text-danger me-2"></i>Most Popular Ideas</h6>

                    {{-- THÊM NÚT VIEW ALL CHO BÊN POPULAR --}}
                    <a href="{{ url('/') }}" class="text-decoration-none small fw-bold">View all</a>
                </div>
                <ul class="list-group list-group-flush dashboard-list">
                    @forelse($popularIdeas as $idea)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-medium text-dark">{{ Str::limit($idea->title, 40) }}</h6>
                                <small class="text-muted">{{ $idea->user->department->department_name ?? 'N/A' }}</small>
                            </div>

                            {{-- NÚT DẤU + ĐỂ XEM CHI TIẾT --}}
                            <a href="{{ route('ideas.show', $idea->id) }}" class="btn btn-sm btn-success bg-opacity-10 text-success rounded-circle d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;" title="View Details">
                                <i class="bi bi-plus-lg fw-bold"></i>
                            </a>
                        </div>
                    </li>
                    @empty
                    <p class="text-muted small py-3">No popular ideas yet.</p>
                    @endforelse
                </ul>
            </div>
        </div>

    </div>
</div>
@endsection
