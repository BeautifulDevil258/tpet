@extends('layouts.adminapp')

@section('content')
<h1 class="text-center mb-5" style="font-size: 2.5rem; color: #333;">Thêm Sản Phẩm Mới</h1>

<form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="container"
    style="max-width: 800px; background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);">
    @csrf

    <div class="form-group">
        <label for="name" class="font-weight-bold">Tên Sản Phẩm</label>
        <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required
            style="border-radius: 8px;">
        @error('name')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label for="price" class="font-weight-bold">Giá</label>
        <input type="number" id="price" name="price" class="form-control" value="{{ old('price') }}" required
            style="border-radius: 8px;">
        @error('price')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label for="quantity" class="font-weight-bold">Số Lượng</label>
        <input type="number" id="quantity" name="quantity" class="form-control" value="{{ old('quantity') }}" required
            style="border-radius: 8px;">
        @error('quantity')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label for="description" class="font-weight-bold">Mô Tả</label>
        <textarea id="description" name="description" class="form-control tinymce" rows="4"
            style="border-radius: 8px;">{{ old('description') }}</textarea>
        @error('description')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label for="import_price" class="font-weight-bold">Giá Nhập</label>
        <input type="number" id="import_price" name="import_price" class="form-control"
            value="{{ old('import_price') }}" style="border-radius: 8px;">
        @error('import_price')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label for="small_category_id" class="font-weight-bold">Danh Mục Nhỏ</label>
        <select id="small_category_id" name="small_category_id" class="form-control" required
            style="border-radius: 8px;">
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
    <div class="form-group mt-3">
        <label class="font-weight-bold">Hình Ảnh</label>

        <!-- Nút chọn ảnh -->
        <button type="button" class="btn btn-secondary d-flex mt-3" id="choose-image-btn"
            style="border-radius: 8px; padding: 5px 10px; font-size: 16px;">Chọn Ảnh</button>

        <!-- Input file ẩn -->
        <input type="file" id="image" name="image" class="form-control" onchange="previewImage(event)"
            style="display: none;">

        @error('image')
        <div class="text-danger">{{ $message }}</div>
        @enderror

        <!-- Hiển thị ảnh đã chọn -->
        <div id="image-preview" class="mt-2" style="text-align: center;"></div>
    </div>
    <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary btn-lg" style="border-radius: 50px; padding: 12px 30px;">Thêm Sản
            Phẩm</button>
    </div>
</form>

<script>
// Khi nhấn vào nút "Chọn Hình Ảnh", mở hộp thoại chọn file
document.getElementById('choose-image-btn').addEventListener('click', function() {
    document.getElementById('image').click();
});

// Hiển thị ảnh đã chọn
function previewImage(event) {
    const preview = document.getElementById('image-preview');
    const file = event.target.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.width = 150;
            img.style.borderRadius = '8px';
            img.style.boxShadow = '0px 4px 8px rgba(0, 0, 0, 0.1)';
            preview.innerHTML = ''; // Xóa ảnh cũ nếu có
            preview.appendChild(img);
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endsection