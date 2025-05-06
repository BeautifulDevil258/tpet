@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-success">Đánh giá đơn hàng {{ $order->order_code }}</h1>

    <form action="{{ route('reviews.store', $order->id) }}" method="POST">
        @csrf

        @foreach ($products as $product)
            <div class="mb-4">
                <h5 class="text-primary">{{ $product->name }}</h5>
                <p>Giá: {{ number_format($product->price, 0, ',', '.') }} VND</p>

                <!-- Chọn số sao -->
                <label for="rating-{{ $product->id }}" class="form-label">Số sao:</label>
                <div>
                    @for ($i = 1; $i <= 5; $i++)
                        <label class="me-2">
                            <input type="radio" name="ratings[{{ $product->id }}]" value="{{ $i }}" required>
                            <span class="text-warning">&#9733;</span>
                        </label>
                    @endfor
                </div>

                <!-- Nhận xét -->
                <div class="mt-2">
                    <label for="comment-{{ $product->id }}" class="form-label">Nhận xét:</label>
                    <textarea name="comments[{{ $product->id }}]" id="comment-{{ $product->id }}" class="form-control" rows="3"></textarea>
                </div>
            </div>
        @endforeach

        <button type="submit" class="btn btn-success">Gửi đánh giá</button>
    </form>
</div>
@endsection
