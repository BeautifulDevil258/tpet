@extends('layouts.adminapp')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10">
            <!-- Thông tin đơn hàng -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="m-0"><i class="fas fa-shopping-cart"></i> Chi tiết đơn hàng #{{ $order->order_code }}</h4>
                </div>
                <div class="card-body">
                    <p><strong><i class="fas fa-calendar-alt"></i> Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong><i class="fas fa-money-bill-wave"></i> Tổng tiền:</strong> {{ number_format($order->total_price, 0, ',', '.') }} VND</p>
                    <p><strong><i class="fas fa-map-marker-alt"></i> Địa chỉ giao hàng:</strong> {{ $order->shipping_address }}</p>
                    <p>
                        <strong><i class="fas fa-exclamation-circle"></i> Trạng thái:</strong> 
                        <span class="badge {{ $order->status == 'Chờ lấy hàng' ? 'bg-warning' : 'bg-success' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- Danh sách sản phẩm -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light">
                    <h5 class="m-0"><i class="fas fa-paw"></i> Sản phẩm trong đơn hàng</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-success">
                            <tr>
                                <th>#</th>
                                <th>Hình ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Giá</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderItems as $item)
                            <tr>
                                <td>{{ $item->product->id }}</td>
                                <td>
                                    <img src="{{ asset('images/' . $item->product->image) }}" alt="{{ $item->product->name }}" width="60" height="60" class="rounded">
                                </td>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->price, 0, ',', '.') }} VND</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Nút hành động -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.order.index') }}" class="btn btn-outline-secondary px-4 py-2"><i class="fas fa-arrow-left"></i> Quay lại</a>
                <a href="#" class="btn btn-danger px-4 py-2"><i class="fas fa-trash-alt"></i> Hủy đơn</a>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
/* Tùy chỉnh bảng */
.table-bordered th, .table-bordered td {
    vertical-align: middle;
}

.table img {
    border-radius: 8px;
}

/* Hiệu ứng hover */
.card:hover {
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

/* Badge trạng thái */
.badge {
    font-size: 0.9rem;
    padding: 8px 12px;
    border-radius: 20px;
}

/* Responsive */
@media (max-width: 767px) {
    .table th, .table td {
        font-size: 0.9rem;
    }
    .btn {
        font-size: 0.9rem;
        padding: 10px 20px;
    }
}
</style>
