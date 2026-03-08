
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
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f9; color: #334155; }
        .navbar { background-color: #ffffff; border-bottom: 1px solid #e2e8f0; z-index: 1030; }
        .navbar-brand { font-weight: 700; color: #0d6efd; letter-spacing: -0.5px; }
        .sidebar { width: 260px; background-color: #ffffff; border-right: 1px solid #e2e8f0; height: calc(100vh - 56px); position: sticky; top: 56px; padding-top: 1.5rem; }
        .sidebar .nav-link { color: #64748b; font-weight: 500; padding: 0.75rem 1.5rem; border-radius: 0; transition: all 0.2s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #0d6efd; background-color: #f0f7ff; border-right: 3px solid #0d6efd; }
        .sidebar .nav-link i { margin-right: 10px; }
        .main-content { padding: 2rem; }
        @media (max-width: 991.98px) { .sidebar { display: none; } }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="/"><i class="bi bi-mortarboard-fill me-2"></i>UIMS</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="{{ route('admin.dashboard') }}">Admin Panel</a></li>
                    <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                </ul>
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <a class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" href="#" data-bs-toggle="dropdown">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                {{ substr((Auth::user()->full_name ?? Auth::user()->name ?? 'A'), 0, 1) }}
                            </div>
                            <span class="fw-medium">{{ Auth::user()->full_name ?? Auth::user()->name ?? 'Admin' }}</span>
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
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky">
                    <ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>

    {{-- MENU QUẢN LÝ USER --}}
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
            <i class="bi bi-people"></i> Users
        </a>
    </li>

    {{-- MENU QUẢN LÝ DEPARTMENT --}}
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
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                @yield('admin_content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
