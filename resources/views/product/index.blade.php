@extends('layouts.app')

@section('content')
<!-- Thêm noUiSlider CDN vào phần <head> -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nouislider@15.4.0/dist/nouislider.min.css">
<script src="https://cdn.jsdelivr.net/npm/nouislider@15.4.0/dist/nouislider.min.js"></script>

<div class="container">
    <div class="row">
        <!-- Cột mục lục bên trái -->
        <div class="col-md-3">
            <div class="list-group">
                <!-- Lặp qua các danh mục lớn -->
                @foreach($largeCategories as $largeCategory)
                <div class="category-item {{ $largeCategory->id === $defaultCategory->id ? 'active' : '' }}"
                    onclick="toggleSmallCategories(this)">
                    <a href="{{ route('product.index', ['large_category_id' => $largeCategory->id]) }}"
                        class="list-group-item list-group-item-action">
                        <h5 class="category-title">{{ $largeCategory->name }}</h5>
                        <span
                            class="product-count">({{ $largeCategory->smallCategories->sum(fn($smallCategory) => $smallCategory->products->count()) }}
                            sản phẩm)</span>
                    </a>

                    <!-- Danh mục nhỏ -->
                    <ul class="small-categories">
                        @foreach($largeCategory->smallCategories as $smallCategory)
                        <li
                            class="small-category-item {{ request('small_category_id') == $smallCategory->id ? 'active-small-category' : '' }}">
                            <a href="{{ route('product.index', ['small_category_id' => $smallCategory->id, 'large_category_id' => $largeCategory->id]) }}"
                                class="small-category-link">{{ $smallCategory->name }}
                                ({{ $smallCategory->products->count() }} sản phẩm)</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
            <div class="price-filter mt-4">
                <h5>Chọn giá:</h5>
                <div id="price-slider"></div>
                <div class="d-flex justify-content-between">
                    <!-- Hiển thị giá trị min và max dưới slider, sử dụng number_format từ PHP -->
                    <span id="min-price">{{ number_format($minPrice) }} VND</span>
                    <span id="max-price">{{ number_format($maxPrice) }} VND</span>
                </div>
                <!-- Nút lọc -->
                <div class="mt-3 text-center">
                    <button class="btn btn-success" onclick="applyPriceFilter()">Lọc</button>
                </div>
            </div>
        </div>
        <!-- Cột sản phẩm bên phải -->
        <div class="col-md-9">
            <h2 class="category-header {{ request('query') ? 'd-none' : '' }}"
                style="font-family: 'Poppins', sans-serif; font-weight: 800; color: #28a745; letter-spacing: 3px; text-transform: uppercase; text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3); font-size: 2rem; line-height: 1.4;">
                {{ $selectedCategoryTitle }}</h2>

            <h2 class="search-header {{ !request('query') ? 'd-none' : '' }}"
                style="font-family: 'Poppins', sans-serif; font-weight: 800; color: #28a745; letter-spacing: 3px; text-transform: uppercase; text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3); font-size: 2rem; line-height: 1.4;">
                Kết quả tìm kiếm cho: "{{ request('query') }}"</h2>

            <div class="pl d-flex justify-content-end align-items-center mb-4 gap-3">
                <div class="d-flex align-items-center">
                    <label for="products-per-page" class="me-2 text-muted">Hiển thị:</label>
                    <select id="products-per-page" class="form-select w-auto rounded-3 shadow-none border-secondary"
                        onchange="updateProductsPerPage()">
                        <option value="9" {{ request('per_page') == 9 ? 'selected' : '' }}>9 sản phẩm</option>
                        <option value="18" {{ request('per_page') == 18 ? 'selected' : '' }}>18 sản phẩm</option>
                        <option value="36" {{ request('per_page') == 36 ? 'selected' : '' }}>36 sản phẩm</option>
                    </select>
                </div>
                <div class="d-flex align-items-center">
                    <label for="sort-by" class="me-2 text-muted">Sắp xếp theo:</label>
                    <select id="sort-by" class="form-select w-auto rounded-3 shadow-none border-secondary"
                        onchange="updateSortBy()">
                        <option value="default" {{ request('sort_by') == 'default' ? 'selected' : '' }}>Mặc định
                        </option>
                        <option value="name_asc" {{ request('sort_by') == 'name_asc' ? 'selected' : '' }}>Tên A-Z
                        </option>
                        <option value="name_desc" {{ request('sort_by') == 'name_desc' ? 'selected' : '' }}>Tên Z-A
                        </option>
                        <option value="price_asc" {{ request('sort_by') == 'price_asc' ? 'selected' : '' }}>Giá tăng dần
                        </option>
                        <option value="price_desc" {{ request('sort_by') == 'price_desc' ? 'selected' : '' }}>Giá giảm
                            dần</option>
                        <option value="latest" {{ request('sort_by') == 'latest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="oldest" {{ request('sort_by') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                    </select>
                </div>
            </div>
            <!-- Hiển thị sản phẩm -->
            @if($products->isEmpty())
            <p>Không có sản phẩm nào trong danh mục này.</p>
            @else
            <div class="row">
                @foreach($products as $product)
                <div class="col-md-4 col-6 mb-4">
                    <a href="{{ route('product.details', ['id' => $product->id]) }}" class="card-link">
                        <div class="card product-card">
                            <div class="card-img-container">
                                <img src="{{ asset('images/'.$product->image) }}" class="card-img-top"
                                    alt="{{ $product->name }}">
                                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-buy-now">Mua ngay</button>
                                </form>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title text-uppercase">{{ $product->name }}</h3>
                                <p id="price" class="text-primary">{{ number_format($product->price, 0, ',', '.') }} VND
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>

            <!-- Phân trang -->
            <div class="d-flex justify-content-center">
                {{ $products->links('pagination::bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>
</div>
<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/nouislider@15.4.0/dist/nouislider.min.js"></script>
<script>
// Lấy giá trị min_price và max_price từ URL (nếu có)
var urlParams = new URLSearchParams(window.location.search);
var minPriceFromUrl = urlParams.get('min_price');
var maxPriceFromUrl = urlParams.get('max_price');

// Nếu có giá trị min_price và max_price từ URL, sử dụng chúng, nếu không thì sử dụng giá trị mặc định
var minPrice = minPriceFromUrl ? parseFloat(minPriceFromUrl) : {{ $minPrice }};
var maxPrice = maxPriceFromUrl ? parseFloat(maxPriceFromUrl) : {{ $maxPrice }};

// Khởi tạo noUiSlider
var priceSlider = document.getElementById('price-slider');
noUiSlider.create(priceSlider, {
    start: [minPrice, maxPrice], // Giá trị bắt đầu (có thể lấy từ URL hoặc giá trị mặc định)
    connect: true, // Hiển thị thanh kết nối
    range: {
        'min': {{ $minPrice }},
        'max': {{ $maxPrice }}
    },
    step: 1000, // Bước nhảy là 1000 VND
    
    format: {
        to: function(value) {
            return value.toFixed(0); // Hiển thị giá không có phần thập phân
        },
        from: function(value) {
            return value; // Chuyển đổi giá trị từ string sang số
        }
    }
});

// Hàm format số VND cho min và max giá trị (JavaScript)
function formatPrice(value) {
    return value.toLocaleString('vi-VN'); // Định dạng số theo chuẩn Việt Nam (có dấu phẩy)
}

// Cập nhật giá trị min và max khi người dùng kéo slider
priceSlider.noUiSlider.on('update', function(values, handle) {
    // Cập nhật giá trị min và max trong các span, sử dụng hàm formatPrice
    document.getElementById('min-price').textContent = formatPrice(values[0]) + ' VND';
    document.getElementById('max-price').textContent = formatPrice(values[1]) + ' VND';
});

// Hàm áp dụng bộ lọc giá khi người dùng nhấn nút lọc
function applyPriceFilter() {
    let minPrice = priceSlider.noUiSlider.get()[0];
    let maxPrice = priceSlider.noUiSlider.get()[1];

    // Cập nhật URL với các tham số min_price và max_price
    let url = new URL(window.location.href);
    url.searchParams.set('min_price', minPrice);
    url.searchParams.set('max_price', maxPrice);
    window.location.href = url.toString(); // Chuyển hướng đến URL mới với các tham số lọc
}

// Cập nhật các tham số lọc trong URL
function updateProductsPerPage() {
    let perPage = document.getElementById('products-per-page').value;
    let url = new URL(window.location.href);
    url.searchParams.set('per_page', perPage);
    window.location.href = url.toString();
}

function updateSortBy() {
    let sortBy = document.getElementById('sort-by').value;
    let url = new URL(window.location.href);
    url.searchParams.set('sort_by', sortBy);
    window.location.href = url.toString();
}

// Cập nhật các tham số lọc trong URL
function updateProductsPerPage() {
    let perPage = document.getElementById('products-per-page').value;
    let url = new URL(window.location.href);
    url.searchParams.set('per_page', perPage);
    window.location.href = url.toString();
}

function updateSortBy() {
    let sortBy = document.getElementById('sort-by').value;
    let url = new URL(window.location.href);
    url.searchParams.set('sort_by', sortBy);
    window.location.href = url.toString();
}
</script>
@endsection
