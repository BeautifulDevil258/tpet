@extends('layouts.adminapp')

@section('content')
<h1 class="text-center mb-5" style="font-size: 2.5rem; color: #333;">Thêm Nhân Viên Mới</h1>

<form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data" class="container"
    style="max-width: 800px; background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);" autocomplete="off">
    @csrf

    <div class="form-group">
        <label for="name" class="font-weight-bold">Tên Nhân Viên</label>
        <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required
            style="border-radius: 8px;">
        @error('name')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label for="email" class="font-weight-bold">Email</label>
        <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required
            style="border-radius: 8px;" autocomplete="email">
        @error('email')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label for="password" class="font-weight-bold">Mật khẩu</label>
        <input type="password" id="password" name="password" class="form-control" required
            style="border-radius: 8px;" autocomplete="new-password">
        @error('password')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label for="phone" class="font-weight-bold">Số điện thoại</label>
        <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}"
            style="border-radius: 8px;">
        @error('phone')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label for="birth_date" class="font-weight-bold">Ngày sinh</label>
        <input type="date" id="birth_date" name="birth_date" class="form-control" value="{{ old('birth_date') }}"
            style="border-radius: 8px;">
        @error('birth_date')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label class="font-weight-bold">Giới tính</label>
        <select name="gender" class="form-control" style="border-radius: 8px;">
            <option value="">Chọn giới tính</option>
            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam</option>
            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Khác</option>
        </select>
        @error('gender')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label class="font-weight-bold">Ảnh Đại Diện</label>

        <!-- Nút chọn ảnh -->
        <button type="button" class="btn btn-secondary d-flex mt-3" id="choose-image-btn"
            style="border-radius: 8px; padding: 5px 10px; font-size: 16px;">Chọn Ảnh</button>

        <!-- Input file ẩn -->
        <input type="file" id="profile_picture" name="profile_picture" class="form-control" onchange="previewImage(event)"
            style="display: none;">

        @error('profile_picture')
        <div class="text-danger">{{ $message }}</div>
        @enderror

        <!-- Hiển thị ảnh đã chọn -->
        <div id="image-preview" class="mt-2" style="text-align: center;"></div>
    </div>

    <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary btn-lg" style="border-radius: 50px; padding: 12px 30px;">Thêm Nhân Viên</button>
    </div>
</form>

<script>
// Khi nhấn vào nút "Chọn Ảnh", mở hộp thoại chọn file
document.getElementById('choose-image-btn').addEventListener('click', function() {
    document.getElementById('profile_picture').click();
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
