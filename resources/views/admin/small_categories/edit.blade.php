@extends('layouts.adminapp')

@section('content')
<div class="container py-4">
    <h2 class="text-center text-primary mb-4"><i class="fas fa-paw"></i> Sửa Danh Mục Nhỏ</h2>
    
    <div class="card shadow-sm p-3 bg-white rounded">
        <form action="{{ route('small_categories.update', $smallCategory->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name" class="font-weight-bold"><i class="fas fa-tag"></i> Tên Danh Mục Nhỏ</label>
                <input type="text" id="name" name="name" class="form-control shadow-sm" value="{{ old('name', $smallCategory->name) }}" placeholder="Nhập tên danh mục nhỏ..." required>
            </div>

            <div class="form-group mt-3">
                <label for="large_category_id" class="font-weight-bold"><i class="fas fa-layer-group"></i> Danh Mục Lớn</label>
                <select class="form-control shadow-sm" id="large_category_id" name="large_category_id" required>
                    @foreach ($largeCategories as $largeCategory)
                        <option value="{{ $largeCategory->id }}" 
                            {{ $smallCategory->large_category_id == $largeCategory->id ? 'selected' : '' }}>
                            {{ $largeCategory->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Hình Ảnh -->
            <div class="form-group mt-3">
                <label class="font-weight-bold" style="font-size: 18px; color: #34495e;">Hình Ảnh</label>

                <!-- Nút chọn ảnh -->
                <button type="button" class="btn btn-primary d-flex mt-3" id="choose-image-btn"
                    style="border-radius: 8px; padding: 8px 12px; font-size: 16px; background-color: #3498db; border: none; color: white; cursor: pointer;">
                    <i class="fas fa-image"></i> Chọn Ảnh
                </button>

                <!-- Input file ẩn -->
                <input type="file" id="image" name="image" class="form-control" onchange="previewImage(event)"
                    style="display: none;">

                @error('image')
                <div class="text-danger" style="font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                @enderror

                <!-- Hiển thị ảnh đã chọn hoặc ảnh cũ -->
                <div id="image-preview" class="mt-2" style="text-align: center;">
                    @if ($smallCategory->image)
                        <img src="{{ asset('images/' . $smallCategory->image) }}" alt="{{ $smallCategory->name }}" width="150" style="border-radius: 8px; border: 2px solid #ddd;">
                    @else
                        <span>Chưa có ảnh</span>
                    @endif
                </div>
            </div>

            <!-- Nút Cập Nhật hoặc Hủy -->
            <div class="d-flex justify-content-center mt-4">
                <button type="submit" class="btn btn-success" style="border-radius: 8px; padding: 10px 15px; font-size: 16px; margin-right: 10px;">
                    Cập Nhật
                </button>
                <a href="{{ route('small_categories.index') }}" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 15px; font-size: 16px;">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    // Xử lý khi nhấn nút chọn ảnh
    document.getElementById('choose-image-btn').addEventListener('click', function() {
        document.getElementById('image').click();
    });

    // Xử lý khi người dùng chọn ảnh
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const preview = document.getElementById('image-preview');
            preview.innerHTML = `<img src="${reader.result}" alt="Image Preview" width="150" style="border-radius: 8px; border: 2px solid #ddd;">`;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

@endsection
