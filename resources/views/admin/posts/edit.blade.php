@extends('layouts.adminapp')

@section('content')
<div class="container">
    <h1 class="mt-4 text-center">✏️ Chỉnh sửa bài viết</h1>

    <div class="card shadow-lg p-4">
        <form action="{{ route('posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="fw-bold">📌 Tiêu đề bài viết</label>
                <input type="text" name="title" class="form-control" value="{{ $post->title }}" required>
            </div>

            <div class="mb-3">
                <label class="fw-bold">📖 Mô tả bài viết</label>
                <textarea name="content" id="content" class="form-control tinymce"
                    required>{{ $post->content }}</textarea>
            </div>

            <div class="mb-3">
                <label class="fw-bold">📂 Danh mục</label>
                <select name="category_id" class="form-select" required>
                    @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ $post->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mt-3">
                <label class="fw-bold">🖼️ Hình ảnh</label>

                <div id="current-image-container" class="mb-2" style="{{ $post->image ? '' : 'display: none;' }}">
                    <img src="{{ asset('storage/' . $post->image) }}" alt="Ảnh hiện tại" id="current-image"
                        style="width: 150px; cursor: pointer; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                    <button type="button" id="change-image-btn" class="btn btn-warning mt-2">Đổi ảnh</button>
                </div>

                <input type="file" id="image" name="image" class="form-control" onchange="previewImage(event)"
                    style="display: none;">
                @error('image')
                <div class="text-danger">{{ $message }}</div>
                @enderror

                <div id="image-preview" class="mt-2" style="display: none;"></div>

                <button type="button" id="cancel-btn" class="btn btn-danger mt-2" style="display: none;">Hủy</button>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-success px-4">✅ Cập nhật bài viết</button>
            </div>
        </form>
    </div>
</div>
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
