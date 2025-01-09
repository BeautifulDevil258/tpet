@extends('layouts.adminapp')

@section('content')
    <h1>Chỉnh Sửa Sản Phẩm</h1>

    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Tên Sản Phẩm</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mt-3">
            <label for="price">Giá</label>
            <input type="number" id="price" name="price" class="form-control" value="{{ old('price', $product->price) }}" required>
            @error('price')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mt-3">
            <label for="quantity">Số Lượng</label>
            <input type="number" id="quantity" name="quantity" class="form-control" value="{{ old('quantity', $product->quantity) }}" required>
            @error('quantity')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mt-3">
            <label for="description">Mô Tả</label>
            <textarea id="description" name="description" class="form-control">{{ old('description', $product->description) }}</textarea>
            @error('description')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mt-3">
            <label for="discount_price">Giảm Giá</label>
            <input type="number" id="discount_price" name="discount_price" class="form-control" value="{{ old('discount_price', $product->discount_price) }}">
            @error('discount_price')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mt-3">
            <label for="image">Hình Ảnh</label>
            <input type="file" id="image" name="image" class="form-control">
            @error('image')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mt-3">
            <label for="small_category_id">Danh Mục Nhỏ</label>
            <select id="small_category_id" name="small_category_id" class="form-control" required>
                <option value="">Chọn danh mục nhỏ</option>
                @foreach($smallCategories as $category)
                    <option value="{{ $category->id }}" 
                        {{ old('small_category_id', $product->small_category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('small_category_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary mt-3">Cập Nhật Sản Phẩm</button>
    </form>
@endsection
