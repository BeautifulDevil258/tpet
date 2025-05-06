@extends('layouts.app')

@section('title', 'Chi Tiết Sản Phẩm')
@section('meta')
<meta property="og:title" content="{{ $product->name }}" />
<meta property="og:description" content="{{ $product->description }}" />
<meta property="og:image" content="{{ asset('images/' . $product->image) }}" />
<meta property="og:url" content="{{ url()->current() }}" />
<meta property="og:type" content="product" />
<meta property="og:site_name" content="{{ config('app.name') }}" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:image" content="{{ asset('images/' . $product->image) }}" />
<meta name="twitter:title" content="{{ $product->name }}" />
<meta name="twitter:description" content="{{ $product->description }}" />
@endsection

@section('content')
<div class="container my-5">
    <div class="row bg-white p-4 rounded shadow-lg">
        <div class="col-md-4 text-center position-relative">
            <div class="zoom-container">
                <img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}"
                    class="img-fluid rounded shadow-lg zoom-img" id="productImage">
            </div>
            <button type="button" class="btn btn-light position-absolute top-50 end-0 translate-middle-y"
                style="font-size: 2rem; background: rgba(0, 0, 0, 0.5); color: white; border: none; border-radius: 50%; padding: 0.2rem; margin-right: 20px;"
                data-bs-toggle="modal" data-bs-target="#imageModal">
                <i class="fa fa-expand"></i>
            </button>
            <p class="text-muted mt-3">Danh mục: <strong>{{ $product->smallCategory->name }}</strong>
                ({{ $product->smallCategory->largeCategory->name }})</p>
        </div>

        <!-- Modal xem toàn bộ ảnh -->
        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imageModalLabel">Hình ảnh sản phẩm</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}"
                            class="img-fluid">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <h1 class="h3 text-success fw-bold">{{ $product->name }}</h1>
            <p class="text-muted">Đã bán: <span class="text-warning fw-bold">{{ $totalSold }}</span></p>
            <p>
                @for ($i = 1; $i <= floor($product->rating); $i++)
                    <i class="bi bi-star-fill text-warning"></i> <!-- Hiển thị sao đầy -->
                    @endfor

                    @if ($product->rating - floor($product->rating) >= 0.5)
                    <i class="bi bi-star-half text-warning"></i> <!-- Hiển thị nửa sao nếu có -->
                    @endif

                    @for ($i = 1; $i <= 5 - ceil($product->rating); $i++)
                        <i class="bi bi-star text-warning"></i> <!-- Hiển thị sao trống -->
                        @endfor

                        <span class="ms-2">{{ $product->review_count }} đánh giá</span>
            </p>

            <p class="text-muted">Còn: {{ $product->quantity }}</p>
            <div class="product-price-container p-3 rounded-3 shadow-sm"
                style="background: rgba(240, 240, 240, 0.8); border: 2px dashed #399203;">
                <p class="on-store text-uppercase text-danger fw-bold">Giá tại cửa hàng</p>
                <bdi class="fw-bold text-success" style="font-size: 28px;">
                    {{ number_format($product->price, 0, ',', '.') }} VNĐ
                </bdi>
            </div>

            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                @csrf
                <div class="d-flex align-items-center mt-3 mb-3">
                    <button type="button" class="btn btn-outline-success btn-circle" id="decreaseQuantity"
                        style="padding: 0.2rem; width: 35px; height: 35px; font-size: 1.5rem;"
                        onclick="updateQuantity('decrease')">
                        <i class="bi bi-dash"></i>
                    </button>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $product->quantity }}"
                        class="text-center mx-2"
                        style="width: 40px; font-size: 1.2rem; border: 2px solid #28a745; border-radius: 5px;" required>
                    <button type="button" class="btn btn-outline-success btn-circle" id="increaseQuantity"
                        style="padding: 0.2rem; width: 35px; height: 35px; font-size: 1.5rem;"
                        onclick="updateQuantity('increase')">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>
                <button type="submit" id="apply" class="btn btn-success btn-lg px-5 shadow-lg w-100">
                    <i class="bi bi-bag-check-fill me-2"></i> Mua Ngay
                </button>
            </form>
        </div>

        <div class="col-md-3">
            <h5 class="text-success fw-bold text-uppercase">Shop Cam Kết</h5>
            <div class="border p-3 rounded shadow-sm bg-light">
                <ul class="list-unstyled">
                    @foreach([
                    ['icon' => 'bi-truck', 'text' => 'Giao hàng nhanh chóng', 'sub' => 'Chỉ từ 2 giờ nội thành'],
                    ['icon' => 'bi-check-circle', 'text' => 'Cam kết 100% giống hình', 'sub' => 'Đã kiểm tra kỹ càng'],
                    ['icon' => 'bi-bag-check', 'text' => 'Hàng hóa chính hãng', 'sub' => 'Đa dạng & phong phú'],
                    ['icon' => 'bi-tags', 'text' => 'Giá rẻ', 'sub' => 'Khuyến mãi không ngừng'],
                    ['icon' => 'bi-arrow-counterclockwise', 'text' => 'Đổi trả ngay', 'sub' => 'Nhanh chóng & dễ dàng'],
                    ] as $commit)
                    <li class="d-flex align-items-center mb-3">
                        <i class="bi {{ $commit['icon'] }} text-success me-3 fs-4"></i>
                        <div>
                            <span class="fw-bold">{{ $commit['text'] }}</span>
                            <small class="d-block text-muted">{{ $commit['sub'] }}</small>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="product-description">
            <h3>Mô tả</h3>
            {!! $product->description !!}
        </div>

        <span class="review-title">ĐÁNH GIÁ TỪ KHÁCH HÀNG</span>
        <div class="product-review">
            <h3 class="review-for">{{ $product->reviews->count() }} đánh giá cho {{ $product->name }}</h3>

            @if ($product->reviews->isEmpty())
            <p class="no-review">Chưa có đánh giá nào.</p>
            @else
            <ul class="review-list">
                @foreach ($product->reviews as $review)
                <li class="review-item">
                    <div class="review-header">
                        <div class="review-avatar">
                            <img src="{{ $review->user->profile_picture ? asset('storage/public/avatars/' . basename($review->user->profile_picture)) : asset('images/default-avatar.png') }}"
                                alt="Avatar" class="avatar-img">
                        </div>
                        <div class="review-rating-name">
                            <div class="review-rating">
                                @for ($i = 1; $i <= 5; $i++) <span
                                    class="star{{ $i <= $review->rating ? ' filled' : '' }}">★</span>
                                    @endfor
                            </div>
                            <div class="review-info">
                                <strong class="review-name">{{ $review->user->name }}</strong> -
                                <span class="review-time">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="review-comment">{{ $review->comment }}</p>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</div>
@endsection

<script>
function updateQuantity(action) {
    let quantityInput = document.getElementById('quantity');
    let currentQuantity = parseInt(quantityInput.value);
    let maxQuantity = parseInt(quantityInput.max);

    if (action === 'increase' && currentQuantity < maxQuantity) {
        quantityInput.value = currentQuantity + 1;
    } else if (action === 'decrease' && currentQuantity > 1) {
        quantityInput.value = currentQuantity - 1;
    }
}
</script>

<style>
/* Zoom effect */
.zoom-container {
    position: relative;
    overflow: hidden;
}

.zoom-img {
    transition: transform 0.3s ease-in-out;
    object-fit: cover;
}

.zoom-container:hover .zoom-img {
    transform: scale(1.5);
}

#productImage {
    width: 100%;
    height: auto;
    object-fit: cover;
    aspect-ratio: 4 / 3;
}
#apply {
    border-radius: 5px;
    margin-bottom: 20px;
    background-color: #2c97230f;
    color: #2c9723;
    border: 1px solid #2c97237a;
    transition: all 0.3s ease;
}

#apply:hover {
    background-color: #d1d1d1;
}

#quantity {
    text-align: center;
    font-size: 18px;
    border: 1px solid #ddd;
    height: 35px;
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

.product-description {
    border: 1px solid rgba(0, 0, 0, .1);
    padding: 10px;
    margin-bottom: 20px;
    /* Thêm khoảng cách dưới */
    top: 50px;
}

.product-description img {
    margin-left: auto;
    margin-right: auto;
    display: block;
}

.review-title {
    border-bottom: 2px solid rgba(0, 0, 0, .1);
    margin-bottom: -2px;
    margin-right: 15px;
    padding-bottom: 7.5px;
    font-weight: bold;
    font-size: 20px;
}

.review-for {
    margin-top: 10px;
    font-size: 20px;
    font-weight: 500;
    font-style: normal;
}

.product-review .review-list {
    list-style-type: none;
    padding: 0;
    margin-left: 10px;
}

.product-review .review-item {
    margin-bottom: 20px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 15px;
}

.product-review .review-header {
    display: flex;
    align-items: flex-start;
}

.product-review .review-avatar {
    margin-right: 15px;
}

.product-review .avatar-img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
}

.product-review .review-rating-name {
    display: flex;
    flex-direction: column;
}

.product-review .review-rating {
    display: flex;
    margin-bottom: 5px;
}

.product-review .review-rating .star {
    font-size: 18px;
    color: #ccc;
    margin-right: 2px;
}

.product-review .review-rating .star.filled {
    color: #ffd700;
    /* Gold color for filled stars */
}

.product-review .review-info {
    display: flex;
    align-items: center;
}

.product-review .review-name {
    font-weight: bold;
    margin-right: 5px;
}

.product-review .review-time {
    font-size: 0.9em;
    color: #666;
    margin-left: 5px;
}

.product-review .review-comment {
    margin-top: 10px;
    font-size: 1em;
}
</style>