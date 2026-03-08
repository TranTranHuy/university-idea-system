{{-- @extends('layouts.admin')

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

    <div class="row g-4 mb-5">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3 me-3"><i class="bi bi-chat-square-text fs-4"></i></div>
                    <div><div class="text-muted small">Total Ideas</div><div class="h4 mb-0 fw-bold">1,284</div></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 bg-success bg-opacity-10 text-success p-3 me-3"><i class="bi bi-people fs-4"></i></div>
                    <div><div class="text-muted small">Total Users</div><div class="h4 mb-0 fw-bold">452</div></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 bg-warning bg-opacity-10 text-warning p-3 me-3"><i class="bi bi-building fs-4"></i></div>
                    <div><div class="text-muted small">Departments</div><div class="h4 mb-0 fw-bold">12</div></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 bg-info bg-opacity-10 text-info p-3 me-3"><i class="bi bi-mortarboard fs-4"></i></div>
                    <div><div class="text-muted small">Academic Years</div><div class="h4 mb-0 fw-bold">5</div></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-lightning-charge text-warning me-2"></i>Latest Ideas</h6>
                    <a href="#" class="text-decoration-none small">View all</a>
                </div>
                <ul class="list-group list-group-flush dashboard-list">
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div><h6 class="mb-1 fw-medium">Smart Parking System using IoT</h6><small class="text-muted">By Alice Smith</small></div>
                            <small class="text-muted">10 mins ago</small>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-fire text-danger me-2"></i>Most Popular Ideas</h6>
                </div>
                <ul class="list-group list-group-flush dashboard-list">
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div><h6 class="mb-1 fw-medium">Extend Library 24/7 during Finals</h6><small class="text-muted">Student Services</small></div>
                            <div class="bg-success bg-opacity-10 text-success fw-bold px-3 py-1 rounded-pill small">+145</div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection --}}
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
                    <div><div class="text-muted small">Total Ideas</div><div class="h4 mb-0 fw-bold">0</div></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 bg-success bg-opacity-10 text-success p-3 me-3"><i class="bi bi-people fs-4"></i></div>
                    <div><div class="text-muted small">Total Users</div><div class="h4 mb-0 fw-bold">0</div></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 bg-warning bg-opacity-10 text-warning p-3 me-3"><i class="bi bi-building fs-4"></i></div>
                    <div><div class="text-muted small">Departments</div><div class="h4 mb-0 fw-bold">0</div></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 bg-info bg-opacity-10 text-info p-3 me-3"><i class="bi bi-mortarboard fs-4"></i></div>
                    <div><div class="text-muted small">Academic Years</div><div class="h4 mb-0 fw-bold">0</div></div>
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
                    <a href="#" class="text-decoration-none small">View all</a>
                </div>
                <ul class="list-group list-group-flush dashboard-list">
                    {{-- Khung <li> mẫu để backend dùng vòng lặp --}}
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-medium">[Idea Title]</h6>
                                <small class="text-muted">By [Author Name]</small>
                            </div>
                            <small class="text-muted">[Time Ago]</small>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Khung Most Popular Ideas --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-fire text-danger me-2"></i>Most Popular Ideas</h6>
                </div>
                <ul class="list-group list-group-flush dashboard-list">
                    {{-- Khung <li> mẫu để backend dùng vòng lặp --}}
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-medium">[Popular Idea Title]</h6>
                                <small class="text-muted">[Department Name]</small>
                            </div>
                            <div class="bg-success bg-opacity-10 text-success fw-bold px-3 py-1 rounded-pill small">
                                +[0]
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
