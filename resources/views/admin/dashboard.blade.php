@extends('layouts.adminapp')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">📊 Dashboard - Thống kê</h1>

    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">📦 Sản phẩm</h5>
                    <p class="card-text display-4">{{ $totalProducts }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">👥 Nhân viên</h5>
                    <p class="card-text display-4">{{ $totalEmployees }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">🛍 Khách hàng</h5>
                    <p class="card-text display-4">{{ $totalCustomers }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">📝 Bài viết</h5>
                    <p class="card-text display-4">{{ $totalPosts }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">📦 Đơn hàng</h5>
                    <p class="card-text display-4">{{ $totalOrders }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
