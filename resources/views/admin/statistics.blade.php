@extends('layouts.admin')

@section('admin_content')
<div id="statistics-section" class="mb-5">

    {{-- HEADER TRANG THỐNG KÊ --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h4 class="mb-0 fw-bold text-dark">Statistical & Exception Reports</h4>
        <div>
            <button class="btn btn-outline-secondary btn-sm me-2">
                <i class="bi bi-file-earmark-spreadsheet"></i> Download CSV Data
            </button>
            <button class="btn text-white btn-sm" style="background-color: #2f80ed; border: none;">
                <i class="bi bi-file-earmark-zip"></i> Download Docs (ZIP)
            </button>
        </div>
    </div>

    {{-- KHUNG BIỂU ĐỒ (CHARTS) --}}
    <div class="row g-4 mb-4">
        {{-- Biểu đồ cột --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 h-100 rounded-4">
                <h6 class="fw-bold mb-4 text-center text-secondary">Number of Ideas by Department</h6>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Biểu đồ tròn --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 h-100 rounded-4">
                <h6 class="fw-bold mb-4 text-center text-secondary">Percentage of Ideas</h6>
                <div style="position: relative; height: 250px; width: 100%; display: flex; justify-content: center;">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- CÁC DANH SÁCH CHI TIẾT --}}
    <div class="row g-4">
        {{-- Bảng đếm số nhân viên tham gia theo Khoa --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h6 class="fw-bold mb-0 text-dark">Contributors by Dept</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless align-middle">
                        <thead class="text-muted small border-bottom">
                            <tr>
                                <th>Department</th>
                                <th class="text-end">Staff Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- BACKEND: Đổ dữ liệu thật vào bảng --}}
                            @foreach($contributorsByDept as $dept)
                            <tr>
                                <td>{{ $dept->department_name }}</td>
                                <td class="text-end fw-medium">{{ $dept->users_count ?? 0 }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Danh sách ý tưởng không có ai comment --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 border-warning border-start border-4">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h6 class="fw-bold mb-0 text-warning"><i class="bi bi-exclamation-circle me-1"></i> Ideas Without Comment</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        {{-- BACKEND: Vòng lặp lấy ý tưởng chưa ai bình luận --}}
                        @forelse($ideasWithoutComments as $idea)
                        <li class="list-group-item px-0 border-0 small py-1 text-truncate">
                            <a href="#" class="text-decoration-none text-dark hover-primary">{{ $idea->title }}</a>
                        </li>
                        @empty
                        <li class="list-group-item px-0 border-0 small py-1 text-muted">
                            All ideas have comments! Amazing!
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        {{-- Thống kê ẩn danh --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 border-info border-start border-4">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h6 class="fw-bold mb-0 text-info"><i class="bi bi-incognito me-1"></i> Anonymous Activities</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        {{-- BACKEND: Đổ biến đếm tổng vào đây --}}
                        <li class="list-group-item px-0 border-0 small py-2 d-flex justify-content-between align-items-center">
                            <span class="text-secondary">Anonymous Ideas</span>
                            <span class="badge bg-light text-dark border px-2 py-1">{{ $anonymousIdeasCount }}</span>
                        </li>
                        <li class="list-group-item px-0 border-0 small py-2 d-flex justify-content-between align-items-center">
                            <span class="text-secondary">Anonymous Comments</span>
                            <span class="badge bg-light text-dark border px-2 py-1">{{ $anonymousCommentsCount }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT VẼ BIỂU ĐỒ (CHART.JS) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ==========================================
        // BACKEND CHUYỂN DỮ LIỆU TỪ PHP SANG JAVASCRIPT
        // ==========================================
        const depts = {!! json_encode($deptNamesArray) !!};
        const ideasData = {!! json_encode($ideasDataArray) !!};

        // Màu sắc mặc định cho biểu đồ tròn (Pie Chart)
        const bgColors = [
            'rgba(13, 110, 253, 0.7)', 'rgba(25, 135, 84, 0.7)',
            'rgba(220, 53, 69, 0.7)', 'rgba(255, 193, 7, 0.7)',
            'rgba(13, 202, 240, 0.7)', 'rgba(111, 66, 193, 0.7)'
        ];

        // 1. Khởi tạo Bar Chart (Biểu đồ cột)
        const barCtx = document.getElementById('barChart');
        if (barCtx) {
            new Chart(barCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: depts,
                    datasets: [{
                        label: 'Total Ideas',
                        data: ideasData,
                        backgroundColor: '#2f80ed', // Dùng màu xanh chủ đạo
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 } // Chỉnh stepSize để cột y hiển thị số nguyên
                        }
                    }
                }
            });
        }

        // 2. Khởi tạo Doughnut Chart (Biểu đồ tròn)
        const pieCtx = document.getElementById('pieChart');
        if (pieCtx) {
            new Chart(pieCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: depts,
                    datasets: [{
                        data: ideasData,
                        backgroundColor: bgColors,
                        borderWidth: 2,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right', labels: { boxWidth: 12, font: {family: 'Inter'} } }
                    },
                    cutout: '65%' // Độ rỗng ở giữa biểu đồ
                }
            });
        }
    });
</script>
@endsection
