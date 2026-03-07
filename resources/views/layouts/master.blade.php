<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Idea Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5; /* Màu nền xám nhạt */
            color: #334155;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ----- Navbar Styling (Design mới) ----- */
        .navbar {
            background-color: #ffffff !important; /* Đổi nền thành màu trắng */
            border-bottom: 1px solid #e2e8f0;
            z-index: 1050 !important;
            padding: 0 !important;
            position: sticky;
            top: 0;
        }
        .navbar-brand {
            font-weight: 800;
            color: #0d6efd !important; /* Logo màu xanh */
            letter-spacing: -0.5px;
            font-size: 1.5rem;
            padding: 1rem 0;
        }
        .navbar-nav {
            margin-bottom: 0 !important;
        }
        .navbar-nav .nav-link {
            padding: 1.25rem 1.5rem !important;
            border-right: 3px solid transparent;
            color: #64748b !important; /* Text link màu xám */
            font-weight: 500;
        }
        .navbar-nav .nav-link:hover {
            color: #0f172a !important;
            background-color: #f8f9fa;
        }
        .navbar-nav .nav-link.active {
            color: #0f172a !important; /* Text trang hiện tại giữ màu tối */
            font-weight: 600;
            background-color: #f0f7ff; /* Nền của tab active */
            border-right: 3px solid #0d6efd; /* Đường kẻ xanh báo hiệu active */
        }

        /* Căn chỉnh Navbar bên phải */
        .navbar .d-flex.align-items-center {
            padding: 0.75rem 0;
        }

        .dropdown-menu {
            z-index: 2000 !important;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        /* Login Button Styling */
        .login-btn {
            color: #ffffff !important;
            background-color: #0d6efd;
            font-weight: 500;
            padding: 0.5rem 1.5rem !important;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .login-btn:hover {
            background-color: #0b5ed7;
        }

        /* User Dropdown Button Styling */
        .user-dropdown-btn {
            color: #0f172a !important;
            font-weight: 500;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
        }
        .user-dropdown-btn:hover {
            background-color: #f8f9fa;
        }

        /* Management Panel Button */
        .nav-admin {
            background-color: #ffc107 !important;
            color: #000 !important;
            font-weight: bold;
            border-radius: 5px;
            transition: 0.3s;
            padding: 0.5rem 1rem !important;
            margin-top: 0.6rem;
            margin-bottom: 0.6rem;
            display: inline-flex;
            align-items: center;
        }
        .nav-admin:hover {
            background-color: #e0a800 !important;
        }

        /* ----- Custom Input Bar Styling ----- */
        .idea-input-wrapper {
            background-color: #ffffff;
            border-radius: 50px;
            padding: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            transition: box-shadow 0.2s;
        }
        .idea-input-wrapper:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .idea-input-field {
            background-color: #f8f9fa;
            border: none;
            border-radius: 50px;
            padding: 12px 20px;
            font-size: 0.95rem;
        }
        .idea-input-field:focus {
            box-shadow: none;
            background-color: #f1f5f9;
        }
        .user-avatar-circle {
            width: 48px;
            height: 48px;
            font-size: 1.1rem;
            font-weight: 500;
        }

        .footer {
    flex-shrink: 0;
    padding: 20px 0;
    margin-top: auto; /* Thuộc tính này kết hợp với flexbox của body sẽ ép footer xuống đáy */
}
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="/">
                <i class="bi bi-mortarboard-fill me-2"></i>UIMS
            </a>
            <button class="navbar-toggler my-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
    <li class="nav-item">
        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/">Home</a>
    </li>

    {{-- <li class="nav-item">
        <a class="nav-link {{ request()->is('ideas*') || request()->is('all-ideas') ? 'active' : '' }}" href="/ideas">All Ideas</a>
    </li> --}}

    <li class="nav-item">
        <a class="nav-link {{ request()->is('create-idea') || request()->routeIs('ideas.create') ? 'active' : '' }}" href="{{ route('ideas.create') }}">Submit Idea</a>
    </li>
</ul>

                <ul class="navbar-nav ms-auto align-items-center pe-2">
                    {{-- --- BẮT ĐẦU LOGIC MANAGEMENT PANEL --- --}}
                    @auth
                        @php
                            $panelRoute = '#';

                            // Lấy User hiện tại
                            $user = Auth::user();

                            // Lấy tên Role từ trong Object
                            $roleName = $user->role->role_name ?? $user->role;

                            // 1. Check Admin
                            // if ($roleName == 'Administrator' || $roleName == 'admin' || $roleName == 'Admin') {
                            //     $panelRoute = route('admin.academic-years.index');
                            // }
                            // Ở đoạn check quyền Admin:
                            if ($roleName == 'Administrator' || $roleName == 'admin' || $roleName == 'Admin') {
                            $panelRoute = route('admin.dashboard'); // Nhảy thẳng vào Dashboard mới tạo
                            }

                            // 2. Check QA Manager
                            elseif ($roleName == 'QA Manager') {
                                $panelRoute = route('qam.categories.index');
                            }

                            // 3. Check Coordinator
                            elseif ($roleName == 'Coordinator' || $roleName == 'QA Coordinator') {
                                $panelRoute = route('coordinator.dashboard');
                            }
                        @endphp

                        {{-- Hiển thị nút --}}
                        @if(in_array(Auth::user()->role_id, [1, 2, 3]))
                            @if($roleName == 'QA Manager')
                                {{-- Menu Dropdown dành riêng cho QA Manager --}}
                                <li class="nav-item dropdown ms-lg-3 d-flex align-items-center">
                                    <a class="btn user-dropdown-btn px-3 py-2 dropdown-toggle d-flex align-items-center" href="#" id="qamMenu" role="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-speedometer2 me-2 text-primary"></i> Management Panel
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                        <li><a class="dropdown-item" href="{{ route('qam.categories.index') }}"><i class="bi bi-tags me-2 text-primary"></i>Manage Categories</a></li>
                                        <li><a class="dropdown-item" href="{{ route('qam.deadlines.index') }}"><i class="bi bi-calendar-event me-2 text-danger"></i>Manage Deadlines</a></li>
                                    </ul>
                                </li>
                            @else
                                {{-- Nút mặc định cho Admin và Coordinator --}}
                                <li class="nav-item ms-lg-3 d-flex align-items-center">
                                    <a class="btn user-dropdown-btn px-3 py-2 d-flex align-items-center" href="{{ $panelRoute }}">
                                        <i class="bi bi-speedometer2 me-2 text-primary"></i> Management Panel
                                    </a>
                                </li>
                            @endif
                        @endif
                    @endauth
                    {{-- --- KẾT THÚC LOGIC --- --}}

                    {{-- @guest
                        <li class="nav-item d-flex align-items-center ms-lg-3">
    <a class="nav-link" href="/login">Login</a>
</li>
                    @else
                        <li class="nav-item dropdown ms-lg-2 d-flex align-items-center">
                            <a class="dropdown-toggle btn user-dropdown-btn px-3 py-2" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; font-size: 0.75rem;">
                                    {{ substr((Auth::user()->full_name ?? Auth::user()->name), 0, 1) }}
                                </div>
                                <span class="fw-medium">{{ Auth::user()->full_name ?? Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
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
                    @endguest --}}
                    @guest
    <li class="nav-item d-flex align-items-center ms-lg-3">
        <a class="nav-link" href="/login">Login</a>
    </li>
@else
    <li class="nav-item dropdown ms-lg-2 d-flex align-items-center">
        <a class="dropdown-toggle btn user-dropdown-btn px-3 py-2" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; font-size: 0.75rem;">
                {{ substr((Auth::user()->full_name ?? Auth::user()->name), 0, 1) }}
            </div>
            <span class="fw-medium">{{ Auth::user()->full_name ?? Auth::user()->name }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">

            {{-- CHỈ HIỂN THỊ PROFILE CHO USER BÌNH THƯỜNG (role_id không phải 1, 2, hoặc 3) --}}
            @if(!in_array(Auth::user()->role_id, [1, 2, 3]))
                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                <li><hr class="dropdown-divider"></li>
            @endif

            {{-- NÚT LOGOUT LUÔN HIỂN THỊ CHO TẤT CẢ MỌI NGƯỜI --}}
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



        {{-- Nơi chèn content từ file blade khác (nếu cần) --}}
        @yield('content')
    </div>

    <div class="footer text-center">
    <div class="container">
        <p class="mb-0 text-dark fw-medium">&copy; 2026 University Idea System. All rights reserved.</p>
        <small class="text-muted">Designed for Greenwich Vietnam</small>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
