<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TPet Admin</title>
    <!-- Thêm CSS của Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Thêm FontAwesome để sử dụng icon lịch -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
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
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">TPet Admin</a>
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
            <li class="nav-item">
                <a class="nav-link @if(request()->is('products*') || request()->is('import-history*')) active @endif"
                    href="#productCollapse" data-bs-toggle="collapse" data-bs-target="#productCollapse" role="button"
                    aria-expanded="{{ request()->is('products*') || request()->is('import-history*') ? 'true' : 'false' }}"
                    aria-controls="productCollapse">
                    <i class="fas fa-box"></i> Sản phẩm
                </a>
                <div class="collapse @if(request()->is('products*') || request()->is('import-history*')) show @endif"
                    id="productCollapse">
                    <ul class="nav flex-column ms-0">
                        <li class="nav-item">
                            <a class="nav-link @if(request()->is('products*')) active @endif"
                                href="{{ route('products.index') }}" style="padding-left: 30px;">
                                <i class="fas fa-cogs"></i> Quản lý sản phẩm
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if(request()->is('import-history*')) active @endif"
                                href="{{ route('import_history.index') }}" style="padding-left: 30px;">
                                <i class="fas fa-history"></i> Lịch sử nhập hàng
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link @if(request()->is('large-categories*') || request()->is('small-categories*')) active @endif"
                    href="#categoriesCollapse" data-bs-toggle="collapse" data-bs-target="#categoriesCollapse"
                    role="button"
                    aria-expanded="{{ request()->is('large-categories*') || request()->is('small-categories*') ? 'true' : 'false' }}"
                    aria-controls="categoriesCollapse">
                    <i class="fas fa-th-large"></i> Danh mục
                </a>
                <div class="collapse @if(request()->is('large-categories*') || request()->is('small-categories*')) show @endif"
                    id="categoriesCollapse">
                    <ul class="nav flex-column ms-0">
                        <li class="nav-item">
                            <a class="nav-link @if(request()->is('large-categories*')) active @endif"
                                href="{{ route('large_categories.index') }}" style="padding-left: 30px;">
                                <i class="fas fa-list-alt"></i> Danh mục lớn
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if(request()->is('small-categories*')) active @endif"
                                href="{{ route('small_categories.index') }}" style="padding-left: 30px;">
                                <i class="fas fa-list"></i> Danh mục nhỏ
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
            <a class="nav-link @if(request()->is('customers*')) active @endif" href="{{ route('customers.index') }}">
                    <i class="fas fa-users"></i> Khách hàng
                </a>
            </li>

            <!-- Nhân viên -->
            @if(Auth::guard('admin')->user()->role == 'admin' || Auth::guard('admin')->user()->role == 'nhanvien')
            <li class="nav-item">
                <a class="nav-link @if(request()->is('employees*') || request()->is('checkincheckout*')) active @endif"
                    href="#employeeCollapse" data-bs-toggle="collapse" role="button" aria-expanded="false"
                    aria-controls="employeeCollapse">
                    <i class="fas fa-users-cog"></i> Nhân viên
                </a>
                <div class="collapse @if(request()->is('employees*') || request()->is('checkincheckout*')) show @endif"
                    id="employeeCollapse">
                    <ul class="nav flex-column ms-0">
                        @if(Auth::guard('admin')->user()->role == 'admin')
                        <li class="nav-item">
                            <a class="nav-link @if(request()->is('employees*')) active @endif"
                                href="{{ route('employees.index') }}" style="padding-left: 30px;">
                                <i class="fas fa-user-cog"></i> Quản lý nhân viên
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link @if(request()->is('checkincheckout*')) active @endif"
                                href="{{ route('checkincheckout.index') }}" style="padding-left: 30px;">
                                <i class="fas fa-clock"></i> Chấm công
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endif

            <li class="nav-item">
            <a class="nav-link @if(request()->is('admin/voucher*')) active @endif" href="{{ route('vouchers.index') }}">
                    <i class="fas fa-gift"></i> Voucher
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link @if(request()->is('admin/order*')) active @endif" href="/admin/order">
                    <i class="fas fa-shopping-cart"></i> Đơn hàng
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link @if(request()->is('posts*') || request()->is('article-categories*')) active @endif"
                    href="#articleCollapse" data-bs-toggle="collapse" role="button" aria-expanded="false"
                    aria-controls="articleCollapse">
                    <i class="fas fa-file-alt"></i> Bài viết
                </a>
                <div class="collapse @if(request()->is('posts*') || request()->is('article-categories*')) show @endif"
                    id="articleCollapse">
                    <ul class="nav flex-column ms-0">
                        <li class="nav-item">
                            <a class="nav-link @if(request()->is('posts*')) active @endif" href="/posts"
                                style="padding-left: 30px;">
                                <i class="fas fa-pen"></i> Quản lý bài viết
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if(request()->is('article-categories*')) active @endif"
                                href="/article-categories" style="padding-left: 30px;">
                                <i class="fas fa-tags"></i> Danh mục bài viết
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            @if(Auth::guard('admin')->user()->role == 'admin')
            <li class="nav-item">
                <a class="nav-link @if(request()->is('admin/statistics*') || request()->is('report*')) active @endif"
                    href="#reportCollapse" data-bs-toggle="collapse" role="button" aria-expanded="false"
                    aria-controls="reportCollapse">
                    <i class="fas fa-chart-line"></i> Báo cáo
                </a>
                <div class="collapse @if(request()->is('admin/statistics*') || request()->is('report*')) show @endif"
                    id="reportCollapse">
                    <ul class="nav flex-column ms-0">
                        <li class="nav-item">
                            <a class="nav-link @if(request()->is('admin/statistics*')) active @endif"
                                href="{{ route('statistics.index') }}" style="padding-left: 30px;">
                                <i class="fas fa-dollar-sign"></i> Doanh thu
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if(request()->is('report*')) active @endif"
                                href="{{ route('reports.checkin_checkout') }}" style="padding-left: 30px;">
                                <i class="fas fa-calendar-check"></i> Báo cáo chấm công
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endif
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                let customerId = this.getAttribute('data-id');
                let form = this.closest('.delete-form');

                Swal.fire({
                    title: 'Bạn có chắc chắn muốn xóa?',
                    text: "Hành động này không thể hoàn tác!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
    </script>

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
    <!-- Thêm TinyMCE Script -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script src="https://cdn.tiny.cloud/1/w0zp5zddow2o7apqx5b3mr2cf6m8d80fnj2hyd65983vak45/tinymce/5/tinymce.min.js">
    </script>
    <script>
    tinymce.init({
        selector: 'textarea.tinymce',
        height: 300,
        menubar: false,
        plugins: ['image'],
        toolbar: 'undo redo | styleselect | bold italic | link image aligncenter', // Đảm bảo chỉ có 1 toolbar
        image_advtab: true,
        file_picker_types: 'image',
        images_upload_url: '/upload-image', // Cập nhật URL tải ảnh ở đây
        images_upload_handler: function(blobInfo, success, failure) {
            var formData = new FormData();
            formData.append('image', blobInfo.blob());

            axios.post('/upload-image', formData)
                .then(function(response) {
                    success(response.data.location);
                })
                .catch(function(error) {
                    failure('Error: ' + error);
                });
        }
    });
    </script>
</body>

</html>