@extends('layouts.app')

@section('title', 'Giỏ Hàng')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="text-center text-success fw-bold mb-4">Giỏ Hàng Của Bạn</h1>

            @if($cart->items->count() > 0)
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-striped table-hover shadow-lg">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col" class="text-center" colspan="2">Sản Phẩm</th>
                            <th scope="col" class="text-center">Số Lượng</th>
                            <th scope="col" class="text-center">Giá</th>
                            <th scope="col" class="text-center">Tạm tính</th>
                            <th scope="col" class="text-center">Thao Tác</th>
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
                                    <div class="input-group w-50 mx-auto">
                                        <button type="button" class="btn btn-sm btn-outline-success"
                                            onclick="updateQuantity({{ $cartItem->id }}, 'decrease')">-</button>
                                        <input type="number" name="quantity" value="{{ $cartItem->quantity }}" min="1"
                                            max="{{ $cartItem->product->quantity }}"
                                            class="form-control text-center quantity-input"
                                            id="quantity-{{ $cartItem->id }}" data-cart-item-id="{{ $cartItem->id }}">
                                        <button type="button" class="btn btn-sm btn-outline-success"
                                            onclick="updateQuantity({{ $cartItem->id }}, 'increase')">+</button>
                                    </div>
                                </form>
                            </td>
                            <td class="text-center">
                                <p class="text-muted">{{ number_format($cartItem->product->price, 0, ',', '.') }} VNĐ
                                </p>
                            </td>
                            <td class="text-center">
                                <p class="fw-bold text-success">{{ number_format($cartItem->price, 0, ',', '.') }} VNĐ
                                </p>
                            </td>
                            <td class="text-center">
                            <form method="POST" action="{{ route('cart.remove', $cartItem->id) }}"
                                class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm delete-btn"
                                    data-id="{{ $cartItem->id }}"><i class="fas fa-trash-alt"></i> Xóa</button>
                            </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="d-flex flex-column align-items-end mt-4 pt-3 border-top">
                    <h3 class="text-success fw-bold">Tổng Tiền: <span class="text-danger"
                            id="total-price">{{ number_format($cart->totalPrice(), 0, ',', '.') }} VNĐ</span></h3>
                    <a href="{{ route('checkout.index') }}" class="btn btn-success btn-lg px-4 py-2"
                        style="border-radius: 30px;">Tiến Hành Thanh Toán</a>
                </div>
            </div>

            <div class="d-lg-none">
                @foreach($cart->items as $cartItem)
                <div class="card shadow-sm mb-3" id="cart-item-{{ $cartItem->id }}">
                    <div class="card-body d-flex align-items-center swipeable-item">
                        <div class="swipe-content d-flex justify-content-between w-100">
                            <div class="product-info d-flex align-items-center">
                                <img src="{{ asset('images/' . $cartItem->product->image) }}" class="img-fluid rounded"
                                    alt="{{ $cartItem->product->name }}" style="max-width: 80px; max-height: 80px;">
                                <div class="ms-3 flex-grow-1">
                                    <h6 class="fw-bold mb-1">{{ $cartItem->product->name }}</h6>
                                    <p class="text-muted mb-1">Giá:
                                        {{ number_format($cartItem->product->price, 0, ',', '.') }} VNĐ</p>
                                    <p class="fw-bold text-success">Tạm tính:
                                        {{ number_format($cartItem->price, 0, ',', '.') }} VNĐ</p>
                                </div>
                            </div>
                        </div>

                        <!-- Di chuyển form số lượng xuống dưới cùng -->
                        <form action="{{ route('cart.update', $cartItem->id) }}" method="POST"
                            class="update-cart-form w-100 d-flex justify-content-center">
                            @csrf
                            @method('PATCH')
                            <div class="input-group justify-content-center" style="max-width: 100px;">
                                <button type="button" class="btn btn-xs btn-outline-success"
                                    onclick="updateQuantity({{ $cartItem->id }}, 'decrease')">-</button>
                                <input type="number" name="quantity" value="{{ $cartItem->quantity }}" min="1"
                                    max="{{ $cartItem->product->quantity }}"
                                    class="form-control text-center quantity-input"
                                    id="quantity-mobile-{{ $cartItem->id }}" style="width: 10px;">
                                <button type="button" class="btn btn-xs btn-outline-success"
                                    onclick="updateQuantity({{ $cartItem->id }}, 'increase')">+</button>
                            </div>
                        </form>

                        <!-- Nút xóa -->
                        <div class="swipe-delete">
                            <form action="{{ route('cart.remove', $cartItem->id) }}" method="POST" class="ms-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="d-flex flex-column align-items-end mt-4 pt-3 border-top w-100">
                    <h3 class="text-success fw-bold">Tổng Tiền: <span class="text-danger"
                            id="total-price">{{ number_format($cart->totalPrice(), 0, ',', '.') }} VNĐ</span></h3>
                    <a href="{{ route('checkout.index') }}" class="btn btn-success btn-md px-4 py-2 w-100"
                        style="border-radius: 5px;">Tiến Hành Thanh Toán</a>
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-warning text-center py-5">
            <h4 class="fw-bold">Giỏ hàng của bạn chưa có sản phẩm nào!</h4>
            <p class="text-muted">Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm.</p>
            <a href="{{ route('product.index') }}" class="btn btn-primary btn-lg px-5 py-2"
                style="border-radius: 30px;">Mua Sắm Ngay</a>
        </div>
        @endif
    </div>
</div>

<script>
// Hàm để thay đổi giá trị số lượng khi người dùng nhấn nút cộng/trừ
function updateQuantity(cartItemId, action) {
    var quantityInput = document.getElementById('quantity-' + cartItemId);
    var currentQuantity = parseInt(quantityInput.value);

    // Thay đổi số lượng
    if (action === 'increase') {
        quantityInput.value = currentQuantity + 1;
    } else if (action === 'decrease' && currentQuantity > 1) {
        quantityInput.value = currentQuantity - 1;
    }

    // Tự động gửi form khi thay đổi số lượng
    updateCartItem(cartItemId);
}

// Hàm để gửi form tự động khi người dùng thay đổi giá trị
function updateCartItem(cartItemId) {
    var form = document.querySelector(`form[action*='${cartItemId}']`); // Tìm form chứa ID sản phẩm

    // Gửi form tự động
    form.submit();
}

// Sử dụng sự kiện 'input' để gọi hàm khi người dùng thay đổi số lượng mà không cần Enter
document.querySelectorAll('.quantity-input').forEach(function(input) {
    input.addEventListener('input', function() {
        var cartItemId = input.getAttribute('data-cart-item-id');
        updateCartItem(cartItemId);
    });
});

document.querySelectorAll('.swipeable-item').forEach(function(item) {
    let startX;
    let currentX;
    let diff;

    // Khi bắt đầu chạm (touch)
    item.addEventListener('touchstart', function(e) {
        startX = e.touches[0].clientX;
        item.querySelector('.swipe-content').style.transition = 'none'; // Tắt transition khi kéo
    });

    // Khi di chuyển (touchmove)
    item.addEventListener('touchmove', function(e) {
        currentX = e.touches[0].clientX;
        diff = startX - currentX;

        // Chỉ cho phép kéo sang trái
        if (diff > 0) {
            item.querySelector('.swipe-content').style.transform =
                `translateX(-${Math.min(diff, 80)}px)`;
        }
    });

    // Khi kết thúc chạm (touchend)
    item.addEventListener('touchend', function() {
        if (diff > 60) {
            // Nếu kéo đủ xa, hiển thị nút xóa
            item.classList.add('swiped');
        } else {
            // Nếu không kéo đủ xa, khôi phục vị trí
            item.classList.remove('swiped');
            item.querySelector('.swipe-content').style.transform = 'translateX(0)';
        }

        // Tắt transition khi kéo kết thúc
        item.querySelector('.swipe-content').style.transition = 'transform 0.3s ease';
    });
});
</script>

<style>
table {
    text-align-last: center;
}

table th {
    text-transform: uppercase;
    border-top: 1px solid rgb(169, 184, 195);
    border-bottom: 1px solid rgb(169, 184, 195);
    padding: 10px;
}

table,
td {
    border: none;
    vertical-align: middle;
}

td {
    border: none;
    text-align: center;
    justify-content: flex-start;
    align-items: center;
}
.img-fluid {
    width: 100%;
    height: auto;
    object-fit: cover;
    aspect-ratio: 4 / 3;
}
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
    border: none;
    outline: none;
}

input[type="number"].quantity-input {
    border: 1px solid #28a745;
    border-radius: 5px;
    padding: 0.3rem;
}

input[type="number"].quantity-input:focus {
    outline: none;
    box-shadow: none;
    border: 1px solid #28a745;
}

@media (max-width: 992px) {
    table {
        display: none;
    }

    .card {
        width: 100%;
        border-radius: 15px;
        overflow: hidden;
    }

    .card-body {
        display: flex;
        flex-wrap: wrap;
    }

    h3,
    a {
        font-size: 16px;
    }

    p {
        font-size: 12px;
    }

    .swipeable-item {
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .product-info {
        display: flex;
        align-items: center;
    }

    input[type="number"].quantity-input {
        border: 1px solid #28a745;
        border-radius: 5px;
        padding: 0.2rem;
        font-size: 10px;
    }

    input[type="number"].quantity-input:focus {
        outline: none;
        box-shadow: none;
        border: 1px solid #28a745;
    }

    .swipe-delete {
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        background-color: #dc3545;
        color: white;
        width: 80px;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .swipeable-item.swiped .swipe-content {
        transform: translateX(-80px);
    }

    .swipeable-item.swiped .swipe-delete {
        display: flex;
    }
}
</style>

@endsection