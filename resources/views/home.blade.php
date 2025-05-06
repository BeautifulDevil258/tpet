@extends('layouts.app')

@section('title', 'TPet - Home')

@section('content')
<div class="container mt-5 mb-5">
    <!-- Slideshow Section with images -->
    <div id="categoryCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="/images/slide1.jpg" class="d-block w-100" alt="Slide 1">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Chào mừng đến với TPet</h5>
                    <p>Khám phá những sản phẩm tuyệt vời cho thú cưng của bạn.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="/images/slide2.jpg" class="d-block w-100" alt="Slide 2">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Sản phẩm chất lượng</h5>
                    <p>Mua sắm những sản phẩm chất lượng cho thú cưng của bạn.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="/images/slide3.jpg" class="d-block w-100" alt="Slide 3">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Đến với chúng tôi</h5>
                    <p>Những món đồ thú vị đang chờ đón bạn.</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#categoryCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#categoryCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
    <h2 class="mt-5 text-center text-uppercase"
        style="font-family: 'Poppins', sans-serif; font-weight: 800; color: #4caf50; letter-spacing: 3px; text-shadow: 2px 2px 5px rgba(0,0,0,0.1); font-size: 2rem;">
        Sản phẩm bán chạy
    </h2>

    <div class="row g-4">
        @forelse ($bestSellingProducts->take(3) as $product)
        <div class="col-md-4">
            <div class="category-box text-center p-4 rounded-4 shadow-lg"
                style="background-color: #fff8f0; transition: all 0.3s ease;">
                <div class="category-img-container"
                    style="position: relative; width: 100%; overflow: hidden; height: 250px;">
                    <img src="{{ asset('images/' . $product->image) }}" class="img-fluid rounded-3 mb-3"
                        alt="{{ $product->name }}"
                        style="object-fit: cover; width: 100%; height: 100%; border-radius: 10px;">
                </div>
                <h5 class="category-title"
                    style="font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.3rem; color: #4caf50;">
                    {{ $product->name }}
                </h5>
                <p class="category-description" style="color: #555;">
                    Giá: {{ number_format($product->price, 0, ',', '.') }} đ
                </p>
                <a href="{{ route('product.details', $product->id) }}" class="btn btn-pink mt-2"
                    style="font-weight: bold; border-radius: 25px; padding: 8px 18px; background-color: #4caf50; color: #fff; border: none;">
                    Xem Chi Tiết
                </a>
            </div>
        </div>
        @empty
        @endforelse
    </div>
    @if ($bestSellingProducts->count() > 3)
    <div class="text-center mt-4">
        <a href="{{ route('product.index', ['filter' => 'best_selling']) }}" class="btn" style="font-weight: bold; border-radius: 25px; padding: 10px 25px; text-transform: uppercase;
              border: 2px solid #4caf50; color: #4caf50; background-color: transparent; transition: all 0.3s ease;">
            Xem Thêm →
        </a>
    </div>
    @endif
    @foreach ($largeCategories as $largeCategory)
    <h2 class="mt-5 text-center text-uppercase"
        style="font-family: 'Poppins', sans-serif; font-weight: 800; color: #4caf50; letter-spacing: 3px; text-transform: uppercase; text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2); font-size: 2rem; line-height: 1.4;">
        {{ $largeCategory->name }}
    </h2>
    <div class="row g-4">
        @foreach ($largeCategory->smallCategories as $smallCategory)
        <div class="col-md-4">
            <div class="category-box text-center p-4 rounded-4 shadow-lg"
                style="background-color: #f9f7f0; border: none; transition: all 0.3s ease; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);">
                <div class="category-img-container"
                    style="position: relative; width: 100%; overflow: hidden; height: 250px;">
                    <img src="{{ asset('images/' . $smallCategory->image) }}" class="img-fluid rounded-3 mb-3"
                        alt="{{ $smallCategory->name }}"
                        style="object-fit: cover; width: 100%; height: 100%; border-radius: 10px;">
                </div>
                <h5 class="category-title"
                    style="font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.5rem; letter-spacing: 1px; text-transform: capitalize; color: #4caf50;">
                    {{ $smallCategory->name }}
                </h5>
                <p class="category-description"
                    style="font-size: 1rem; font-family: 'Lora', serif; font-style: italic; color: #34495e;">
                    Khám phá các sản phẩm độc đáo trong danh mục này. Hãy bắt đầu hành trình mua sắm của bạn ngay!
                </p>
                <a href="{{ route('product.index', ['small_category_id' => $smallCategory->id]) }}"
                    class="btn btn-success mt-3"
                    style="font-weight: bold; border-radius: 25px; padding: 10px 20px; background-color: #4caf50; color: #ffffff; border: none; transition: background-color 0.3s ease; text-transform: uppercase;">
                    Xem Sản Phẩm
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @endforeach
</div>
@endsection

<style>
/* Kích thước tối đa và căn giữa slideshow */
#categoryCarousel {
    max-width: 1300px;
    /* Giới hạn chiều rộng trên màn hình lớn */
    margin: auto;
    /* Căn giữa slideshow */
    width: 100%;
    /* Chiếm toàn bộ chiều rộng màn hình nhỏ */
}

/* Ảnh trong slideshow */
.carousel-item img {
    width: 100%;
    /* Đầy đủ chiều rộng khung */
    height: 500px;
    /* Chiều cao cố định cho màn hình lớn */
    object-fit: cover;
    /* Cắt ảnh để vừa khung */
    object-position: center;
    /* Căn giữa ảnh */
}

/* Caption của slideshow */
.carousel-caption {
    background: rgba(0, 0, 0, 0.5);
    /* Nền mờ cho caption */
    border-radius: 10px;
    /* Bo góc */
    padding: 10px;
}

/* Điều chỉnh cho màn hình nhỏ hơn (dưới 576px) */
@media (max-width: 576px) {
    .carousel-item img {
        height: 250px;
        /* Giảm chiều cao trên màn hình nhỏ */
    }

    .category-box {
        padding: 20px;
        box-shadow: none;
    }

    /* Điều chỉnh bố cục cho mobile */
    .col-md-4 {
        flex: 1 1 100%;
        /* Mỗi phần chiếm toàn bộ chiều rộng */
        max-width: 100%;
    }
}
</style>