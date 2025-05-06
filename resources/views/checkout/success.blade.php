@extends('layouts.app')

@section('title', 'Thanh Toán Thành Công')

@section('content')
<div class="container py-5">
    <!-- Lời cảm ơn -->
    <div class="text-center mb-5">
        <h2 class="text-success">Cảm ơn bạn đã mua hàng!</h2>
        <p class="lead">Đơn hàng của bạn đã được xác nhận thành công. Chúng tôi sẽ xử lý và giao hàng sớm nhất.</p>
        <div class="mt-4">
            <img src="https://img.icons8.com/ios/452/shopping-cart.png" alt="order success" class="img-fluid"
                style="max-width: 150px;">
        </div>
    </div>

    @if(session('order'))
    <?php $order = session('order'); ?>

    <!-- Thông tin đơn hàng -->
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card shadow-lg border-light rounded p-4">
                <h4 class="text-center text-primary">Thông tin đơn hàng</h4>
                <div class="order-details mt-4">
                    <p><strong>Mã đơn hàng:</strong> <span class="text-info">{{ $order->order_code }}</span></p>
                    <p><strong>Người nhận:</strong> {{ $order->recipient_name }}</p>
                    <p><strong>Địa chỉ giao hàng:</strong> {{ $order->shipping_address }}</p>
                    <p><strong>Tổng tiền:</strong> {{ number_format($order->total_price) }} VND</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Nút điều hướng -->
    <div class="text-center mt-5">
        <a href="{{ route('home') }}" class="btn btn-md btn-primary px-5 py-3 mb-3">Trang Chủ</a>
        <a href="{{ route('cart.index') }}" class="btn btn-md btn-outline-secondary px-5 py-3 mb-3">Tiếp Tục Mua
            Hàng</a>
        <a href="{{ route('orders.show', ['order' => $order->id]) }}" class="btn btn-md btn-success px-5 py-3 mb-3">Xem
            Đơn Hàng</a>
    </div>
    @else
    <div class="text-center">
        <p class="text-danger">Không tìm thấy thông tin đơn hàng.</p>
    </div>
    @endif
</div>
@endsection
<style>
/* Chỉnh sửa để đảm bảo giao diện đẹp trên điện thoại */
.text-center h2 {
    font-size: 2.2rem;
    font-weight: bold;
}

.text-center p {
    font-size: 1rem;
    color: #6c757d;
}

.card {
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.card h4 {
    font-size: 1.5rem;
    color: #007bff;
}

.order-details p {
    font-size: 1rem;
    color: #555;
}

.order-details span {
    font-weight: bold;
}

.btn-lg {
    font-size: 1rem;
    padding: 12px 30px;
    border-radius: 30px;
}

.btn-primary {
    background-color: #28a745;
    border-color: #28a745;
}

.btn-outline-secondary {
    border-color: #6c757d;
}

.btn-success {
    background-color: #007bff;
    border-color: #007bff;
}

.btn:hover {
    opacity: 0.9;
    transition: all 0.3s ease;
}

/* Media Query cho điện thoại */
@media (max-width: 768px) {

    .text-center h2 {
        font-size: 1.8rem;
    }

    .text-center p {
        font-size: 0.9rem;
    }

    .btn-md {
        font-size: 0.5rem;
        padding: 10px 20px;
    }

    .card {
        padding: 20px;
    }
}
</style>