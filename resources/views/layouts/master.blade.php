<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Idea System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .footer { background-color: #343a40; color: white; padding: 20px 0; margin-top: 50px; }
        .hero-section { background-color: #e9ecef; border-radius: 0.3rem; }
        /* Style cho nút Admin nổi bật hơn */
        .nav-admin { background-color: #ffc107 !important; color: #000 !important; font-weight: bold; border-radius: 5px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">UIS SYSTEM</a>
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
                        <a class="nav-link nav-admin ms-lg-2 px-3" href="{{ route('admin.categories.index') }}">Admin Panel</a>
                    </li>

                    @guest
                        <li class="nav-item">
                            <a class="nav-link btn btn-light text-primary ms-lg-3 px-4" href="/login">Login</a>
                        </li>
                    @else
                        <li class="nav-item dropdown ms-lg-3">
                            <a class="nav-link dropdown-toggle btn btn-outline-light px-3" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Hi, {{ Auth::user()->full_name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="#">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">Logout</button>
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
