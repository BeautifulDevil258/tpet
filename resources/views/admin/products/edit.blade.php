@extends('layouts.adminapp')

@section('content')
<h1 class="text-center mb-5" style="font-size: 2.5rem; color: #333;">Chỉnh Sửa Sản Phẩm</h1>

<form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="container" style="max-width: 800px; background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label for="name" class="font-weight-bold">Tên Sản Phẩm</label>
        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $product->name) }}" required style="border-radius: 8px;">
        @error('name')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label for="price" class="font-weight-bold">Giá</label>
        <input type="number" id="price" name="price" class="form-control" value="{{ old('price', $product->price) }}" required style="border-radius: 8px;">
        @error('price')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label for="quantity" class="font-weight-bold">Số Lượng</label>
        <input type="number" id="quantity" name="quantity" class="form-control" value="{{ old('quantity', $product->quantity) }}" required style="border-radius: 8px;">
        @error('quantity')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label for="description" class="font-weight-bold">Mô Tả</label>
        <textarea id="description" name="description" class="form-control" rows="4" style="border-radius: 8px;">{{ old('description', $product->description) }}</textarea>
        @error('description')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label for="discount_price" class="font-weight-bold">Giá Nhập</label>
        <input type="number" id="import_price" name="import_price" class="form-control" value="{{ old('import_price', $product->import_price) }}" style="border-radius: 8px;">
        @error('import_price')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label for="small_category_id" class="font-weight-bold">Danh Mục Nhỏ</label>
        <select id="small_category_id" name="small_category_id" class="form-control" required style="border-radius: 8px;">
            <option value="">Chọn danh mục nhỏ</option>
            @foreach($smallCategories as $category)
            <option value="{{ $category->id }}" {{ old('small_category_id', $product->small_category_id) == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
            @endforeach
        </select>
        @error('small_category_id')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label class="font-weight-bold">Hình Ảnh</label>
        @if ($product->image)
        <div class="mb-2" id="current-image-container">
            <img src="{{ asset('images/'.$product->image) }}" alt="Product Image" width="150" id="current-image" style="cursor: pointer; border-radius: 8px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); transition: all 0.3s ease;">
            <button type="button" id="change-image-btn" class="btn btn-warning mt-2" style="border-radius: 5px;">Đổi Ảnh</button>
        </div>
        @else
        <div class="mb-2" id="current-image-container"></div>
        @endif
        <input type="file" id="image" name="image" class="form-control" onchange="previewImage(event)" style="display: none;">
        @error('image')
        <div class="text-danger">{{ $message }}</div>
        @enderror
        <div id="image-preview" class="mt-2"></div>
        <button type="button" id="cancel-btn" class="btn btn-danger mt-2" style="display: none; border-radius: 5px;">Hủy</button>
    </div>

    <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary btn-lg" style="border-radius: 50px; padding: 12px 30px;">Cập Nhật Sản Phẩm</button>
    </div>
</form>

<script>
    // Hiển thị ảnh mới khi người dùng chọn ảnh
    function previewImage(event) {
        const preview = document.getElementById('image-preview');
        const currentImage = document.getElementById('current-image');
        const currentImageContainer = document.getElementById('current-image-container');
        const cancelButton = document.getElementById('cancel-btn');

        // Xóa ảnh cũ nếu có
        preview.innerHTML = '';

        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Hiển thị ảnh đã chọn
                const img = document.createElement('img');
                img.src = e.target.result;
                img.width = 150;
                img.style.borderRadius = '8px';
                img.style.boxShadow = '0px 4px 8px rgba(0, 0, 0, 0.1)';
                preview.appendChild(img);

                // Ẩn ảnh hiện tại nếu có
                if (currentImage) {
                    currentImageContainer.style.display = 'none';
                }

                // Hiển thị nút hủy
                cancelButton.style.display = 'inline-block';
            };
            reader.readAsDataURL(file);
        }
    }

    // Mở hộp thoại chọn ảnh khi người dùng nhấn vào ảnh hoặc nút "Thay Đổi Ảnh"
    document.getElementById('change-image-btn')?.addEventListener('click', function() {
        document.getElementById('image').click();
    });

    document.getElementById('current-image')?.addEventListener('click', function() {
        document.getElementById('image').click();
    });

    // Hủy bỏ lựa chọn ảnh và khôi phục ảnh cũ
    document.getElementById('cancel-btn')?.addEventListener('click', function() {
        const currentImageContainer = document.getElementById('current-image-container');
        const preview = document.getElementById('image-preview');
        const cancelButton = document.getElementById('cancel-btn');

        // Ẩn ảnh mới và nút hủy
        preview.innerHTML = '';
        cancelButton.style.display = 'none';

        // Hiển thị lại ảnh cũ
        currentImageContainer.style.display = 'block';
    });
</script>
@endsection
