@extends('layouts.app')

@section('title', 'TPet - Home')

@section('content')
<div class="container mt-5 mb-5">
    @foreach ($largeCategories as $largeCategory)
        <h2 class="mt-5 text-center text-uppercase" style="font-family: 'Poppins', sans-serif; font-weight: 800; color: #4caf50; letter-spacing: 3px; text-transform: uppercase; text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2); font-size: 2rem; line-height: 1.4;">
            {{ $largeCategory->name }}
        </h2>
        <div class="row g-4">
            @foreach ($largeCategory->smallCategories as $smallCategory)
                <div class="col-md-4">
                    <div class="card shadow-lg h-100 rounded-4" style="border: none; overflow: hidden;">
                        <img src="{{ asset('images/' . $smallCategory->image) }}" class="card-img-top" alt="{{ $smallCategory->name }}" style="object-fit: cover; height: 250px;">
                        <div class="card-body d-flex flex-column" style="background: linear-gradient(to top, rgba(0, 0, 0, 0.7), rgba(76, 175, 80, 0.5), rgba(255, 255, 255, 0)); transition: background 0.3s ease;">
                            <!-- Title with a darker color for better readability -->
                            <h5 class="card-title mb-4" style="font-family: 'Playfair Display', serif; font-weight: 700; font-size: 1.5rem; letter-spacing: 1px; text-transform: capitalize; text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.4); color: #4caf50;">
                                {{ $smallCategory->name }}
                            </h5>
                            <!-- Text with a softer gray color -->
                            <p class="card-text mb-4" style="font-size: 1rem; font-family: 'Lora', serif; font-style: italic; line-height: 1.6; color:  #34495e;">
                                Khám phá các sản phẩm độc đáo trong danh mục này. Hãy bắt đầu hành trình mua sắm của bạn ngay!
                            </p>
                            <div class="mt-auto">
                                <a href="{{ route('product.index', ['small_category_id' => $smallCategory->id]) }}" class="btn btn-success w-100" style="font-weight: bold; border-radius: 25px; padding: 10px 20px; background-color: #4caf50; color: #ffffff; border: none; transition: background-color 0.3s ease;">
                                    Xem Sản Phẩm
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
@endsection
