@extends('layouts.adminapp')

@section('content')
<h1 class="text-center mb-5" style="font-size: 2.5rem; color: #333;">Sửa Thông Tin Nhân Viên</h1>

<form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data" class="container"
    style="max-width: 800px; background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label for="name" class="font-weight-bold">Tên Nhân Viên</label>
        <input type="text" id="name" name="name" class="form-control" value="{{ $employee->name }}" required style="border-radius: 8px;">
    </div>

    <div class="form-group mt-3">
        <label for="email" class="font-weight-bold">Email</label>
        <input type="email" id="email" name="email" class="form-control" value="{{ $employee->email }}" required style="border-radius: 8px;">
    </div>
    <div class="form-group mt-3">
        <label for="phone" class="font-weight-bold">Số Điện Thoại</label>
        <input type="text" id="phone" name="phone" class="form-control" value="{{ $employee->phone }}" style="border-radius: 8px;">
    </div>

    <div class="form-group mt-3">
        <label for="birth_date" class="font-weight-bold">Ngày Sinh</label>
        <input type="date" id="birth_date" name="birth_date" class="form-control" value="{{ $employee->birth_date }}" style="border-radius: 8px;">
    </div>

    <div class="form-group mt-3">
        <label for="gender" class="font-weight-bold">Giới Tính</label>
        <select id="gender" name="gender" class="form-control" style="border-radius: 8px;">
            <option value="male" {{ $employee->gender == 'male' ? 'selected' : '' }}>Nam</option>
            <option value="female" {{ $employee->gender == 'female' ? 'selected' : '' }}>Nữ</option>
        </select>
    </div>

    <div class="form-group mt-3">
    <label class="font-weight-bold">Hình Ảnh</label>

    <div id="current-image-container">
        @if($employee->profile_picture)
            <img src="{{ $employee->profile_picture ? asset('storage/public/avatars/' . basename($employee->profile_picture)) : asset('images/avt.jpg') }}" alt="Profile Image" width="150" id="current-image"
                style="cursor: pointer; border-radius: 8px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); transition: all 0.3s ease;">
            <button type="button" id="change-image-btn" class="btn btn-warning mt-2" style="border-radius: 5px;">Đổi Ảnh</button>
        @else
            <button type="button" id="choose-image-btn" class="btn btn-primary mt-2" style="border-radius: 5px;">Chọn Ảnh</button>
        @endif
    </div>

    <input type="file" id="profile_picture" name="profile_picture" class="form-control" onchange="previewImage(event)" style="display: none;">
    
    @error('image')
        <div class="text-danger">{{ $message }}</div>
    @enderror

    <div id="image-preview" class="mt-2"></div>
    <button type="button" id="cancel-btn" class="btn btn-danger mt-2" style="display: none; border-radius: 5px;">Hủy</button>
</div>

    <div class="text-center mt-4">
        <button type="submit" class="btn btn-success btn-lg" style="border-radius: 50px; padding: 12px 30px;">Cập Nhật</button>
    </div>
</form>


<script>
document.addEventListener("DOMContentLoaded", function() {
    const currentImage = document.getElementById("current-image");
    const chooseImageBtn = document.getElementById("choose-image-btn");
    const changeImageBtn = document.getElementById("change-image-btn");
    const fileInput = document.getElementById("profile_picture");
    const previewContainer = document.getElementById("image-preview");
    const cancelBtn = document.getElementById("cancel-btn");
    
    if (chooseImageBtn) {
        chooseImageBtn.addEventListener("click", () => fileInput.click());
    }

    if (changeImageBtn) {
        changeImageBtn.addEventListener("click", () => fileInput.click());
    }

    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewContainer.innerHTML = `<img src="${e.target.result}" alt="Preview Image" width="150" style="border-radius: 8px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);">`;
                cancelBtn.style.display = "inline-block";

                if (chooseImageBtn) chooseImageBtn.style.display = "none";
                if (changeImageBtn) changeImageBtn.style.display = "none";
                if (currentImage) currentImage.style.display = "none";
            };
            reader.readAsDataURL(file);
        }
    }

    cancelBtn.addEventListener("click", function() {
        previewContainer.innerHTML = "";
        fileInput.value = "";
        cancelBtn.style.display = "none";

        if (chooseImageBtn) chooseImageBtn.style.display = "inline-block";
        if (changeImageBtn) changeImageBtn.style.display = "inline-block";
        if (currentImage) currentImage.style.display = "inline-block";
    });

    fileInput.addEventListener("change", previewImage);
});
</script>

@endsection
