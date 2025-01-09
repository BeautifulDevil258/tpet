@extends('layouts.adminapp')

@section('content')
    <h1>Thêm Sản Phẩm Mới</h1>

    <!-- Form Thêm Sản Phẩm -->
    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="name">Tên Sản Phẩm</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mt-3">
            <label for="price">Giá</label>
            <input type="number" id="price" name="price" class="form-control" value="{{ old('price') }}" required>
            @error('price')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mt-3">
            <label for="quantity">Số Lượng</label>
            <input type="number" id="quantity" name="quantity" class="form-control" value="{{ old('quantity') }}" required>
            @error('quantity')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mt-3">
            <label for="description">Mô Tả</label>
            <textarea id="description" name="description" class="form-control">{{ old('description') }}</textarea>
            @error('description')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mt-3">
            <label for="discount_price">Giảm Giá</label>
            <input type="number" id="discount_price" name="discount_price" class="form-control" value="{{ old('discount_price') }}">
            @error('discount_price')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Chọn Danh Mục Nhỏ -->
        <div class="form-group mt-3">
            <label for="small_category_id">Danh Mục Nhỏ</label>
            <select id="small_category_id" name="small_category_id" class="form-control" required>
                <option value="">Chọn danh mục nhỏ</option>
                @foreach($smallCategories as $category)
                    <option value="{{ $category->id }}" {{ old('small_category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('small_category_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Hình Ảnh -->
        <div class="form-group mt-3">
            <label for="image">Hình Ảnh</label>
            <input type="file" id="image" name="image" class="form-control" onchange="previewImage(event)">
            @error('image')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Preview Hình Ảnh -->
        <div class="form-group mt-3">
            <img id="imagePreview" src="#" alt="Hình ảnh preview" style="display: none; width: 150px; height: auto;">
        </div>

        <button type="submit" class="btn btn-primary mt-3">Thêm Sản Phẩm</button>
    </form>

    <script>
        // JavaScript để preview hình ảnh khi người dùng chọn
        function previewImage(event) {
            const imagePreview = document.getElementById('imagePreview');
            const file = event.target.files[0];
            const reader = new FileReader();
            
            reader.onload = function() {
                imagePreview.src = reader.result;
                imagePreview.style.display = 'block'; // Hiển thị ảnh khi đã chọn
            };
            reader.readAsDataURL(file);
        }
    </script>
@endsection
