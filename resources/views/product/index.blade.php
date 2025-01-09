@extends('layouts.app')

@section('content')
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
        </div>

        <!-- Cột sản phẩm bên phải -->
        <div class="col-md-9">
            <!-- Hiển thị tiêu đề danh mục hoặc tiêu đề tìm kiếm -->
            <h2 class="category-header {{ request('query') ? 'd-none' : '' }}"
                style="font-family: 'Poppins', sans-serif; font-weight: 800; color: #28a745; letter-spacing: 3px; text-transform: uppercase; text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3); font-size: 2rem; line-height: 1.4;">
                {{ $selectedCategoryTitle }}</h2>

            <h2 class="search-header {{ !request('query') ? 'd-none' : '' }}"
                style="font-family: 'Poppins', sans-serif; font-weight: 800; color: #28a745; letter-spacing: 3px; text-transform: uppercase; text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3); font-size: 2rem; line-height: 1.4;">
                Kết quả tìm kiếm cho: "{{ request('query') }}"</h2>
            <!-- Phần hiển thị sản phẩm và sắp xếp theo -->
            <div class="d-flex justify-content-end align-items-center mb-4 gap-3">
                <div class="d-flex align-items-center">
                    <label for="products-per-page" class="me-2 text-muted">Hiển thị:</label>
                    <select id="products-per-page" class="form-select w-auto rounded-3 shadow-none border-secondary"
                        onchange="updateProductsPerPage()">
                        <option value="12" {{ request('per_page') == 12 ? 'selected' : '' }}>12 sản phẩm</option>
                        <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>24 sản phẩm</option>
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
            <!-- Kiểm tra nếu không có sản phẩm -->
            @if($products->isEmpty())
            <p>Không có sản phẩm nào trong danh mục này.</p>
            @else
            <!-- Hiển thị danh sách sản phẩm -->
            <div class="row">
                @foreach($products as $product)
                <div class="col-md-4 mb-4">
                    <div class="card product-card">
                        <div class="card-img-container">
                            <img src="{{ asset('images/'.$product->image) }}" class="card-img-top"
                                alt="{{ $product->name }}">
                        </div>
                        <div class="card-body">
                            <h3 class="card-title text-uppercase">{{ $product->name }}</h3>
                            <p class="text-primary">{{ number_format($product->price, 0, ',', '.') }} VND</>
                            </p>
                            <a href="#" class="btn btn-outline-primary btn-full">Xem chi tiết</a>
                        </div>
                    </div>
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

<!-- JavaScript để cập nhật số lượng sản phẩm và sắp xếp -->
<script>
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

// JavaScript để toggle danh mục con khi click vào danh mục lớn
function toggleSmallCategories(element) {
    let smallCategories = element.querySelector('.small-categories');
    smallCategories.style.display = (smallCategories.style.display === 'block') ? 'none' : 'block';
}

// JavaScript để thiết lập danh mục đang chọn
function setActiveCategory(element) {
    // Loại bỏ lớp active của các danh mục khác
    let activeCategory = document.querySelector('.category-item.active');
    if (activeCategory) {
        activeCategory.classList.remove('active');
    }

    // Thêm lớp active cho danh mục hiện tại
    element.classList.add('active');
}
</script>

@endsection

<!-- CSS cho tỷ lệ ảnh và thẻ sản phẩm -->
<style>
.category-header.d-none {
    display: none;
}

.search-header.d-none {
    display: none;
}

/* Loại bỏ khung chia cột cho danh mục */
.category-item {
    padding: 15px;
    background-color: #f0f0f0;
    /* Màu nền sáng */
    margin-bottom: 10px;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.category-item.active .category-title {
    color: #28a745;
    /* Màu chữ khi chọn danh mục lớn (màu cỏ) */
}

.category-item .category-title {
    color: #333;
    /* Màu chữ danh mục lớn mặc định */
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 5px;
}

.category-item:hover .category-title {
    color: #28a745;
    /* Màu chữ khi hover (màu cỏ) */
}

.category-item.active {
    background-color: #e7f9e6;
    /* Nền sáng khi danh mục lớn được chọn */
}

.small-categories {
    padding-left: 20px;
    margin-top: 10px;
    list-style-type: none;
    /* Ẩn danh mục con theo mặc định */
}

.small-category-item {
    margin-bottom: 5px;
}

.small-category-link {
    color: #555;
    text-decoration: none;
    display: block;
    padding: 5px 0;
    transition: color 0.3s ease;
}

.small-category-link:hover {
    color: #28a745;
    /* Màu chữ khi hover (màu cỏ) */
}

/* Đổi màu chữ khi chọn danh mục nhỏ */
.small-category-item.active-small-category .small-category-link {
    color: #28a745;
    /* Màu chữ khi chọn danh mục nhỏ (màu cỏ) */
}

/* Điều chỉnh khung sản phẩm */
/* Điều chỉnh khung sản phẩm */
.product-card {
    display: flex;
    flex-direction: column;
    height: 100%;
    border-radius: 10px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
    transform: translateY(-10px);
    /* Di chuyển thẻ sản phẩm lên trên khi hover */
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

/* Điều chỉnh khung sản phẩm */
.product-card {
    display: flex;
    flex-direction: column;
    height: 100%;
    border-radius: 10px;
    overflow: hidden;
    max-width: 100%;
    /* Đảm bảo khung không vượt quá 100% */
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.product-card .card-img-container {
    position: relative;
    width: 100%;
    padding-top: 100%;
    /* Tỉ lệ ảnh 1:1 */
    overflow: hidden;
}

.product-card .card-img-top {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .card-img-top {
    transform: scale(1.1);
}

.product-card .card-body {
    padding: 10px;
    flex-grow: 1;
    /* Đảm bảo phần nội dung có thể co giãn */
}

.product-card .card-title {
    font-size: 18px !important;
    font-weight: bold;
    font-family: 'Dancing Script', cursive;
    margin-top: 5px;
    color: #333;
    transition: color 0.3s ease;
}

.product-card .text-primary {
    font-size: 18px;
    font-family: Arial, Helvetica, sans-serif;
    font-weight: bold;
    margin-top: 5px;
    color: #28a745 !important;
    /* Màu giá sản phẩm */
}

.product-card .btn-full {
    margin-top: 5px;
    padding: 10px 20px;
    font-weight: bold;
    background-color: #28a745;
    color: #fff;
    border: none;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.product-card .btn-full:hover {
    background-color: #218838;
    /* Màu nền khi hover */
}

/* Thay đổi kích thước cho màn hình nhỏ */
@media (max-width: 768px) {
    .product-card {
        flex: 1;
        /* Làm cho khung sản phẩm co giãn theo màn hình */
    }

    .card-img-container {
        padding-top: 100%;
        /* Giữ tỉ lệ 1:1 cho ảnh */
    }

    .card-body {
        padding: 10px;
        font-size: 14px;
    }

    .product-card .card-title {
        font-size: 16px;
        margin-top: -10px;
    }

    .product-card .card-text {
        font-size: 12px;
    }

    .product-card .btn-full {
        font-size: 14px;
        padding: 8px 15px;
    }
}

/* Cải thiện hiển thị khi danh sách sản phẩm có nhiều hàng trên điện thoại */
@media (max-width: 576px) {
    .row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .col-md-4 {
        width: 100%;
    }
}
</style>