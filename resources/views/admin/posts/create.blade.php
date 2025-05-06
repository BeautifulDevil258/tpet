@extends('layouts.adminapp')

@section('content')
<div class="container">
    <h1 class="mt-4">Thêm bài viết</h1>

    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label>Tiêu đề</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="content">Nội dung</label>
            <textarea id="content" name="content" class="form-control tinymce">{{ old('content') }}</textarea>
        </div>
        <div class="mb-3">
            <label>Danh mục</label>
            <select name="category_id" class="form-control" required>
                @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
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
        <button type="submit" class="btn btn-primary btn-lg" style="border-radius: 5px; padding: 12px 30px;">Thêm bài viế<table></table></button>
    </div>
    </form>
</div>
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