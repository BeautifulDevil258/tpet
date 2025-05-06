@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-success mb-4 text-center">Quản lý đơn hàng</h1>

    <div class="swiper-container mb-3 d-sm-none">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <a href="{{ route('orders.index', ['status' => 'all']) }}"
                    class="btn btn-outline-success {{ $status === 'all' ? 'active' : '' }} w-100">Tất cả</a>
            </div>
            <div class="swiper-slide">
                <a href="{{ route('orders.index', ['status' => 'pending']) }}"
                    class="btn btn-outline-success {{ $status === 'pending' ? 'active' : '' }} w-100">Chờ lấy hàng</a>
            </div>
            <div class="swiper-slide">
                <a href="{{ route('orders.index', ['status' => 'shipped']) }}"
                    class="btn btn-outline-success {{ $status === 'shipping' ? 'active' : '' }} w-100">Đang giao</a>
            </div>
            <div class="swiper-slide">
                <a href="{{ route('orders.index', ['status' => 'completed']) }}"
                    class="btn btn-outline-success {{ $status === 'completed' ? 'active' : '' }} w-100">Đã giao</a>
            </div>
            <div class="swiper-slide">
                <a href="{{ route('orders.index', ['status' => 'canceled']) }}"
                    class="btn btn-outline-success {{ $status === 'canceled' ? 'active' : '' }} w-100">Đã hủy</a>
            </div>
        </div>
        <!-- Thêm thanh cuộn cho Swiper -->
        <div class="swiper-pagination" style="display: none;"></div>
    </div>
    <!-- Các nút lọc hiển thị khi màn hình lớn hơn -->
    <div class="btn-group mb-3 d-none d-sm-flex w-100">
        <a href="{{ route('orders.index', ['status' => 'all']) }}"
            class="btn btn-outline-success {{ $status === 'all' ? 'active' : '' }}">Tất cả</a>
        <a href="{{ route('orders.index', ['status' => 'failed']) }}"
            class="btn btn-outline-success {{ $status === 'failed' ? 'active' : '' }}">Chưa thanh toán</a>
        <a href="{{ route('orders.index', ['status' => 'pending']) }}"
            class="btn btn-outline-success {{ $status === 'pending' ? 'active' : '' }}">Chờ lấy hàng</a>
        <a href="{{ route('orders.index', ['status' => 'shipped']) }}"
            class="btn btn-outline-success {{ $status === 'shipping' ? 'active' : '' }}">Đang giao</a>
        <a href="{{ route('orders.index', ['status' => 'completed']) }}"
            class="btn btn-outline-success {{ $status === 'completed' ? 'active' : '' }}">Đã giao</a>
        <a href="{{ route('orders.index', ['status' => 'canceled']) }}"
            class="btn btn-outline-success {{ $status === 'canceled' ? 'active' : '' }}">Đã hủy</a>
    </div>

    @if ($orders->isEmpty())
    <p class="text-center text-success">Không có đơn hàng nào.</p>
    @else
    <div class="row">
        @foreach ($orders as $order)
        <div class="col-12 col-md-6 col-lg-4 mb-4">
            <a href="{{ route('orders.show', $order->id) }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 rounded-3" style="border-color: #28a745;">
                    <div class="card-body">
                        <h5 class="card-title text-success">Đơn hàng {{ $order->order_code }}</h5>
                        <p class="card-text">Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        <p class="card-text">Trạng thái: <strong>{{ ucfirst($order->status) }}</strong></p>
                        <p class="card-text">Tổng tiền: {{ number_format($order->total_price, 0, ',', '.') }} VND</p>

                        <div class="d-flex justify-content-between mt-3">
                            <span class="btn btn-success btn-sm">Mua lại</span>
                            @if ($order->status === 'Đã giao' && $order->rate === '0')
                            <a href="{{ route('reviews.create', $order->id) }}" class="btn btn-sm btn-warning ms-2">Đánh giá</a>
                            @endif
                            @if ($order->status === 'Chưa thanh toán')
                            <form action="{{ route('checkout.retryPayment', $order->id) }}" method="get">
                                <button type="submit" class="btn btn-primary">Tiếp tục thanh toán</button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
    <div class="d-flex justify-content-center">
        {{ $orders->links('pagination::bootstrap-4') }}
    </div>
    @endif
</div>
<script>
// Khởi tạo Swiper sau khi trang được tải
document.addEventListener('DOMContentLoaded', function() {
    var swiper = new Swiper('.swiper-container', {
        direction: 'horizontal', // Hướng vuốt ngang
        loop: false, // Không lặp lại khi vuốt
        slidesPerView: 2, // Hiển thị 2 phần tử cùng lúc
        spaceBetween: 10, // Khoảng cách giữa các phần tử
        freeMode: true, // Cho phép vuốt tự do (không bị kẹt ở các slide)
        pagination: {
            el: '.swiper-pagination', // Hiển thị các chỉ báo vuốt
            clickable: true, // Cho phép bấm vào các chỉ báo
        },
        breakpoints: {
            // Khi màn hình nhỏ hơn 768px (tablet hoặc điện thoại)
            768: {
                slidesPerView: 1, // Trên màn hình nhỏ, chỉ hiển thị 1 phần tử
            },
        },
    });
});
</script>
@endsection
<style>
.swiper-container {
    max-width: 100%;
    /* Giới hạn chiều rộng container */
    overflow: hidden;
    /* Ẩn phần vượt ra ngoài */
}

.swiper-wrapper {
    display: flex;
    /* Đảm bảo các item trong swiper hiển thị theo hàng ngang */
}

.swiper-slide {
    flex-shrink: 0;
    /* Ngừng co chiều rộng các phần tử */
    width: auto;
    /* Mỗi phần tử có chiều rộng tự động để khớp với nội dung */
}

/* Đảm bảo các nút lọc có một chiều rộng nhất định */
.swiper-slide a {
    width: 120px;
    /* Đặt chiều rộng cố định cho các nút lọc */
    text-align: center;
    /* Căn giữa văn bản */
}
</style>