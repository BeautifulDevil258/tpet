<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Thêm CSS của Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Thêm FontAwesome để sử dụng icon lịch -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/profile.css">
    <style>
        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1030;
            transition: top 0.3s;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background-color: #343a40;
            color: #fff;
            border-right: 1px solid #444;
            overflow-y: auto;
            padding-top: 56px;
        }

        .sidebar .nav-link {
            color: #ddd;
            font-weight: 500;
            transition: background-color 0.2s, color 0.2s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #495057;
            color: #fff;
        }

        /* Content Area */
        .content-area {
            margin-left: 250px;
            padding: 20px;
            margin-top: 56px;
        }

        /* Toast Customization */
        .toast-success {
            background-color: #28a745;
            color: white;
        }

        .toast-error {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Đăng nhập</a>
                    </li>
                    @endguest

                    @auth('admin')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{asset('storage/' . Auth::guard('admin')->user()->profile_picture)}}"
                                alt="Avatar" width="30" height="30" class="rounded-circle">
                            {{Auth::guard('admin')->user()->name}}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ route('admin.adminprofile.index') }}">Xem Hồ Sơ
                                    Admin</a></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Đăng xuất</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <nav class="sidebar">
        <ul class="nav flex-column py-3">
            <li class="nav-item"><a class="nav-link active" href="{{ route('products.index') }}">Sản phẩm</a></li>

            <!-- Danh mục -->
            <li class="nav-item">
                <a class="nav-link" href="#categoryCollapse" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="categoryCollapse">
                    Danh mục
                </a>
                <div class="collapse" id="categoryCollapse">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item"><a class="nav-link" href="{{ route('large_categories.index') }}">Danh mục lớn</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('small_categories.index') }}">Danh mục nhỏ</a></li>
                    </ul>
                </div>
            </li>

            <li class="nav-item"><a class="nav-link" href="{{ route('admin.brands') }}">Thương hiệu</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.employees') }}">Nhân viên</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.revenue') }}">Doanh thu</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.reports') }}">Báo cáo</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main class="content-area">
        @yield('content')
    </main>

    <!-- Toast Notification -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        @if(session('success'))
        <div id="toastSuccess" class="toast toast-success show" role="alert">
            <div class="toast-header">
                <strong class="me-auto">Thành công!</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                {{ session('success') }}
            </div>
        </div>
        @elseif(session('error'))
        <div id="toastError" class="toast toast-error show" role="alert">
            <div class="toast-header">
                <strong class="me-auto">Lỗi!</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                {{ session('error') }}
            </div>
        </div>
        @endif
    </div>

    <script src="/js/pr.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tự động ẩn thông báo sau 3 giây
        window.onload = function() {
            const successToast = document.getElementById('toastSuccess');
            const errorToast = document.getElementById('toastError');

            if (successToast) {
                setTimeout(() => {
                    const toast = new bootstrap.Toast(successToast);
                    toast.hide();
                }, 3000);
            }

            if (errorToast) {
                setTimeout(() => {
                    const toast = new bootstrap.Toast(errorToast);
                    toast.hide();
                }, 3000);
            }
        }
    </script>
</body>

</html>
