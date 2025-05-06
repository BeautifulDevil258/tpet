@extends('layouts.app')

@section('title', 'Kết quả thanh toán VNPAY')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white text-center">
            <h4>Kết quả thanh toán</h4>
        </div>
        <div class="card-body">
            @if ($status === 'success')
                <div class="alert alert-success text-center">
                    <h5>Thanh toán thành công!</h5>
                    <p>Mã giao dịch: <strong>{{ $transactionId }}</strong></p>
                    <p>Số tiền: <strong>{{ number_format($amount, 0, ',', '.') }} VND</strong></p>
                    <p>Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi.</p>
                </div>
            @elseif ($status === 'fail')
                <div class="alert alert-danger text-center">
                    <h5>Thanh toán thất bại!</h5>
                    <p>Lỗi: <strong>{{ $errorMessage }}</strong></p>
                    <p>Vui lòng thử lại hoặc liên hệ với chúng tôi nếu cần hỗ trợ.</p>
                </div>
            @else
                <div class="alert alert-warning text-center">
                    <h5>Thanh toán đang chờ xử lý!</h5>
                    <p>Vui lòng kiểm tra lại thông tin giao dịch sau.</p>
                </div>
            @endif
            <div class="text-center mt-3">
                <a href="{{ route('home') }}" class="btn btn-primary">Về trang chủ</a>
                <a href="{{ route('cart.index') }}" class="btn btn-secondary">Quay lại giỏ hàng</a>
            </div>
        </div>
    </div>
</div>
@endsection
