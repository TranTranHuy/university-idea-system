<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Idea System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* Tùy chỉnh màu sắc và kích thước cho thanh gạt */
.form-check-input:checked {
    background-color: #0d6efd; /* Màu xanh chuẩn Bootstrap */
    border-color: #0d6efd;
}

.form-switch .form-check-input {
    width: 2.5em; /* Kéo dài thanh gạt một chút */
    cursor: pointer;
}

.form-check-label {
    padding-left: 5px;
    vertical-align: middle;
}
        body { background-color: #f8f9fa; }

        /* Cố định Navbar ở trên cùng và ưu tiên hiển thị cao nhất */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 1050 !important; /* Cao hơn sticky-top của sidebar (1020) */
        }

        /* Đảm bảo menu con không bị bất kỳ thứ gì đè lên */
        .dropdown-menu {
            z-index: 2000 !important;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .footer { background-color: #343a40; color: white; padding: 20px 0; margin-top: 50px; }
        .hero-section { background-color: #e9ecef; border-radius: 0.3rem; }

        /* Style cho nút Admin */
        .nav-admin {
            background-color: #ffc107 !important;
            color: #000 !important;
            font-weight: bold;
            border-radius: 5px;
            transition: 0.3s;
        }
        .nav-admin:hover { background-color: #e0a800 !important; }

        /* Sửa lỗi Sidebar có thể đè lên dropdown */
        .sticky-top {
            z-index: 1000 !important; /* Thấp hơn navbar */
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="bi bi-lightbulb-fill"></i> UIS SYSTEM
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link active" href="/">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">All Ideas</a></li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('ideas.create') }}">Submit Idea</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link nav-admin ms-lg-2 px-3" href="{{ route('admin.categories.index') }}">
                            <i class="bi bi-shield-lock"></i> Admin Panel
                        </a>
                    </li>

                    @guest
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary text-white ms-lg-3 px-4" href="/login">Login</a>
                        </li>
                    @else
                        <li class="nav-item dropdown ms-lg-3">
                            <a class="nav-link dropdown-toggle btn btn-outline-light px-3" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i> Hi, {{ Auth::user()->full_name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4" style="min-height: 70vh;">
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
<div class="container mt-3">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>


        @yield('content')
    </div>

    <div class="footer text-center">
        <div class="container">
            <p class="mb-0">&copy; 2026 University Idea System. All rights reserved.</p>
            <small class="text-muted">Designed for Greenwich Vietnam</small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
