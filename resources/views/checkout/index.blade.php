@extends('layouts.app')

@section('title', 'Thanh Toán')

@section('content')
<div class="container my-5">
    <h2 class="text-center mb-4 text-primary">Thông Tin Thanh Toán</h2>

    <div class="row">
        <!-- Địa chỉ nhận hàng -->
        <div id="current-address" class="d-flex justify-content-between align-items-center mb-4 p-3 rounded shadow-sm">
            <div>
                <h4><img src="{{ asset('images/location-icon.svg') }}" alt="Địa chỉ nhận hàng" width="25"> Địa Chỉ Nhận Hàng:</h4>
                @if($currentAddress)
                <p id="address-text">
                    <strong>{{ $currentAddress->name }} - {{ $currentAddress->phone }}</strong>,
                    {{ $currentAddress->detail }},
                    {{ $currentAddress->ward }}, {{ $currentAddress->district }}, {{ $currentAddress->city }}
                </p>
                @else
                <p id="address-text">Chưa có địa chỉ.</p>
                @endif
            </div>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                data-bs-target="#addressModal">Thay đổi địa chỉ</button>
        </div>

        <!-- Modal chọn địa chỉ -->
        <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="addressModalLabel">Chọn Địa Chỉ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="address-form" method="POST" action="{{ route('checkout.updateAddress') }}">
                            @csrf
                            <ul class="list-group custom-list-group" style="max-height: 350px; overflow-y: auto;">
                                @foreach($addresses as $address)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="address-details">
                                        <div class="radio-container">
                                            <input type="radio" name="address_id" class="select-address"
                                                value="{{ $address->id }}" {{ $address->is_default ? 'checked' : '' }}>
                                        </div>
                                        <div class="address-info">
                                            <strong class="address-name">{{ $address->name }}</strong>
                                            <span class="separator"> | {{ $address->phone }}</span>
                                             
                                        </div>
                                        <p class="address-detail">{{ $address->detail }}</p>
                                        <span class="address-location">{{ $address->ward }},
                                            {{ $address->district }}, {{ $address->city }}</span>
                                    </div>

                                    <div class="edit-button-container">
                                        <button type="button"
                                            onclick="window.location='{{ route('addresses.edit', ['address' => $address->id, 'return_url' => url()->current()]) }}'"
                                            class="btn btn-link text-decoration-none">
                                            <i class="fas fa-edit"></i> Sửa
                                        </button>
                                    </div>
                                </li>
                                @endforeach
                            </ul>

                            <!-- Thêm địa chỉ mới -->
                            <button type="button" onclick="window.location='{{ route('addresses.create', ['return_url' => url()->current()]) }}'"
                                class="btn btn-outline-secondary mt-3 w-100">
                                <i class="fas fa-plus"></i> Thêm địa chỉ mới
                            </button>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                <button type="submit" class="btn btn-primary">Xác nhận</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 mt-4 mx-auto">
            <h3 class="text-center text-secondary">Giỏ Hàng</h3>
            <table class="table table-striped table-hover shadow-lg d-none d-md-table">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" class="text-center" colspan="2">Sản Phẩm</th>
                        <th scope="col" class="text-center">Số Lượng</th>
                        <th scope="col" class="text-center">Đơn Giá</th>
                        <th scope="col" class="text-center">Tạm tính</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cart->items as $cartItem)
                    <tr>
                        <td class="text-center">
                            <img src="{{ asset('images/' . $cartItem->product->image) }}"
                                class="img-fluid rounded-start" alt="{{ $cartItem->product->name }}"
                                style="max-width: 100px;">
                        </td>
                        <td class="text-center">
                            <p class="fw-bold">{{ $cartItem->product->name }}</p>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('cart.update', $cartItem->id) }}" method="POST"
                                class="update-cart-form">
                                @csrf
                                @method('PATCH')
                                <div class="text-center">
                                    <p class="text-muted">{{ $cartItem->quantity }}</p>
                                </div>
                            </form>
                        </td>
                        <td class="text-center">
                            <p class="text-muted">{{ number_format($cartItem->product->price, 0, ',', '.') }} VNĐ</p>
                        </td>
                        <td class="text-center">
                            <p class="fw-bold text-success">{{ number_format($cartItem->price, 0, ',', '.') }} VNĐ
                            </p>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-block d-md-none">
                @foreach($cart->items as $cartItem)
                <div class="cart-item mb-4 border rounded p-3">
                    <div class="row">
                        <div class="col-4">
                            <img src="{{ asset('images/' . $cartItem->product->image) }}"
                                class="img-fluid rounded-start" alt="{{ $cartItem->product->name }}"
                                style="max-width: 120%; height: auto;">
                        </div>
                        <div class="col-8">
                            <h5 class="fw-bold">{{ $cartItem->product->name }}</h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="fw-bold text-muted price">{{ number_format($cartItem->price, 0, ',', '.') }}
                                    VNĐ</p>
                                <p class="quantity">x {{ $cartItem->quantity }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <h3 class="text-end mt-3">Tổng tiền:
                <span class="text-danger"
                    id="totalPrice">{{ session('totalPrice') ? number_format(session('totalPrice'), 0, ',', '.') : number_format($totalPrice, 0, ',', '.') }}
                    VNĐ
                </span>
            </h3>
            <!-- Thêm phần modal cho việc chọn voucher -->
            <div class="modal fade" id="voucherModal" tabindex="-1" aria-labelledby="voucherModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="voucherModalLabel">Chọn Voucher</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('checkout.applyVoucher') }}" method="POST" id="voucher-form">
                                @csrf
                                <ul class="list-group custom-list-group" style="max-height: 350px; overflow-y: auto;">
                                    @foreach($vouchers as $voucher)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="voucher-details">
                                            <input type="radio" name="voucher_code" value="{{ $voucher->code }}"
                                                id="voucher_{{ $voucher->code }}">
                                            <label for="voucher_{{ $voucher->code }}">
                                                <strong>{{ $voucher->code }}</strong> - Giảm
                                                {{ number_format($voucher->discount, 0, ',', '.') }} %
                                            </label>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Đóng</button>
                                    <button type="submit" class="btn btn-primary">Chọn Voucher</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thay thế phần chọn voucher ở bên ngoài -->
            <div class="form-group">
                <label for="voucher_code"><i class="fas fa-tag"></i> Chọn mã khuyến mại</label>
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#voucherModal">Chọn Voucher</button>
            </div>
            <form action="{{ route('checkout.process') }}" method="POST" class="mt-3">
                @csrf
                <div class="form-group text-center">
                    <label for="payment_method"><i class="fas fa-money-check-alt"></i> Phương Thức Thanh Toán</label>

                    <div class="payment-option d-flex justify-content-center align-items-center mb-4">
                        <!-- COD Option -->
                        <input type="radio" id="cod" name="payment_method" value="cod" required class="me-3">
                        <img src="{{ asset('images/cod.png') }}" alt="COD"
                            style="width: 40px; height: 40px; margin-right: 10px;">
                        <p class="mb-0">Thanh toán khi nhận hàng</p>
                    </div>

                    <div class="payment-option d-flex justify-content-center align-items-center mb-4">
                        <!-- VNPay Option -->
                        <input type="radio" id="vnpay" name="payment_method" value="vnpay" required class="me-3">
                        <img src="{{ asset('images/vnpay.png') }}" alt="VNPay"
                            style="width: 40px; height: 40px; margin-right: 10px;">
                        <p class="mb-0">Thanh toán qua Vnpay</p>
                    </div>

                </div>
                <button type="submit" class="btn btn-success mt-4 d-block mx-auto w-60">Xác nhận thanh toán</button>
            </form>

        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js"></script>
@endsection

<style>
.form-group {
    max-width: 400px;
    margin: 0 auto;
    display: block;
}

.form-group option {
    text-align: center;
}

.form-group label {
    font-weight: bold;
    text-align: center;
    display: block;
    margin: 5px;
}

.radio-container {
    display: inline-block;
    margin-right: 10px;
}

.address-info {
    display: flex;
    align-items: center;
}

.address-detail,
.address-location {
    display: block;
}

.address-details {
    display: flex;
    flex-direction: column;
}

.custom-list-group {
    font-size: 1.1rem;
}

.modal-dialog-centered {
    display: flex;
    justify-content: center;
    align-items: center;
}

.address-details {
    flex-grow: 1;
}

.edit-button-container {
    display: flex;
    justify-content: flex-end;
    align-items: center;
}

.edit-button-container button {
    margin-left: 10px;
}

.img-fluid {
    width: 100%;
    height: auto;
    object-fit: cover;
    aspect-ratio: 4 / 3;
}

table td {
    vertical-align: middle;
    /* Căn giữa theo chiều dọc */
}

#voucherModal .btn-outline-primary {
    display: block;
    margin: 0 auto;
}

.form-group {
    text-align: center;
}

@media (max-width: 767px) {
    .modal-dialog {
        max-width: 100%;
        margin: 0;
    }

    .col-10 {
        width: 100%;
    }

    .custom-list-group {
        font-size: 1rem;
        max-height: 350px;
        overflow-y: auto;
        width: 100%;
    }

    .modal-body .btn,
    .modal-body form,
    .modal-body .address-details {
        width: 100%;
    }

    .address-details {
        display: block;
        width: 100%;
        text-align: left;
        margin-top: 10px;
    }

    .address-info {
        display: block;
        text-align: left;
        margin-top: 5px;
    }

    .edit-button-container button,
    .modal-footer button {
        width: 100%;
    }

    #current-address {
        flex-direction: column;
        padding: 10px;
        text-align: center;
    }

    #current-address button {
        width: 100%;
        margin-top: 10px;
    }

    table th,
    table td {
        font-size: 0.9rem;
        text-align: center;
    }

    .form-group select {
        width: 100%;
    }

    .btn-success {
        width: 100%;
    }

    .d-md-none .cart-item {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 0;
        /* Loại bỏ padding */
        margin: 0;
        /* Loại bỏ margin */
        background-color: #fff;
        width: 100vw;
        /* Chiếm toàn bộ chiều rộng màn hình */
        box-sizing: border-box;
        /* Đảm bảo không bị chèn padding vào chiều rộng */
    }

    .d-md-none .cart-item .row {
        display: flex;
        align-items: center;
        width: 100%;
        /* Chiếm toàn bộ chiều rộng của phần tử cha */
    }

    .d-md-none .cart-item {
        margin: 0;
        padding: 0;
        width: 100%;
    }

    .col-10 {
        width: 100%;
    }

    .d-md-none .cart-item .col-4,
    .d-md-none .cart-item .col-8 {
        padding: 5px;
        /* Loại bỏ padding của các cột */
        width: 50%;
        /* Đảm bảo mỗi cột chiếm 50% chiều rộng */
    }

    .d-md-none .cart-item .col-4 img {
        width: 100%;
        /* Đảm bảo ảnh chiếm toàn bộ chiều rộng của cột */
        height: auto;
    }

    .d-md-none .cart-item .d-flex {
        justify-content: space-between;
        align-items: center;
        width: 100%;
        /* Đảm bảo các phần tử trong .d-flex chiếm toàn bộ chiều rộng */
    }

    .d-md-none .cart-item .price,
    .d-md-none .cart-item .quantity {
        font-size: 0.9rem;
        color: #555;
        line-height: 1.5;
    }

    .d-md-none .cart-item .quantity {
        margin-left: 5px;
    }

    .d-md-none {
        padding: 0;
        /* Loại bỏ padding của phần tử cha nếu có */
        margin: 0;
        /* Loại bỏ margin của phần tử cha nếu có */
    }

}
</style>