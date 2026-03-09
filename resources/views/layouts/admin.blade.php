<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - UIMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f9; color: #334155; overflow-x: hidden; }
        .navbar { background-color: #ffffff; border-bottom: 1px solid #e2e8f0; z-index: 1060; position: sticky; top: 0; }
        .navbar-brand { font-weight: 700; color: #0d6efd; letter-spacing: -0.5px; }

        /* SIDEBAR CỐ ĐỊNH CHIỀU RỘNG */
        .sidebar {
            width: 260px;
            background-color: #ffffff;
            border-right: 1px solid #e2e8f0;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 70px;
            transition: all 0.3s ease;
            z-index: 1050;
        }

        .sidebar .nav-link { color: #64748b; font-weight: 500; padding: 0.75rem 1.5rem; transition: all 0.2s; border-right: 3px solid transparent; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #0d6efd; background-color: #f0f7ff; border-right-color: #0d6efd; }
        .sidebar .nav-link i { margin-right: 10px; }

        /* MAIN CONTENT: Fix lỗi dãn tràn trên laptop */
        .main-content {
            margin-left: 260px;
            padding: 2rem 0; /* Chỉ padding trên dưới, trái phải dùng container */
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        /* LỚP PHỦ MOBILE */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
        }

        @media (max-width: 991.98px) {
            .sidebar { left: -260px; }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0; }
            .sidebar-overlay.active { display: block; }
        }
    </style>
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <nav class="navbar navbar-expand-lg shadow-sm">
        <div class="container-fluid px-3 px-md-4">
            <button class="btn btn-light d-lg-none me-2 border" type="button" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>

            <a class="navbar-brand me-auto" href="/"><i class="bi bi-mortarboard-fill me-2"></i>UIMS</a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="bi bi-three-dots-vertical"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-lg-3 me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Admin Panel</a></li>
                    <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                </ul>

                <div class="dropdown">
                    <a class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" href="#" data-bs-toggle="dropdown">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                            {{ strtoupper(substr(Auth::user()->full_name ?? Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="fw-medium d-none d-sm-inline">{{ Auth::user()->full_name ?? Auth::user()->name ?? 'Admin' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid p-0">
        <nav class="sidebar" id="sidebarMenu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                        <i class="bi bi-people"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}" href="{{ route('admin.departments.index') }}">
                        <i class="bi bi-building"></i> Departments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.academic-years.*') ? 'active' : '' }}" href="{{ route('admin.academic-years.index') }}">
                        <i class="bi bi-calendar3"></i> Academic Years
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.statistics') ? 'active' : '' }}" href="{{ route('admin.statistics') }}">
                        <i class="bi bi-graph-up"></i> Statistics
                    </a>
                </li>
            </ul>
        </nav>

        <main class="main-content">
            <div class="container-fluid px-4 px-lg-5">
                @yield('admin_content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebarMenu');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    </script>
</body>
</html>
