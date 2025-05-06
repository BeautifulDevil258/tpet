@extends('layouts.adminapp')

@section('title', 'Hồ Sơ Người Dùng')

@section('content')
<div class="container">
    <h1 class="mb-4 text-center text-primary">Hồ Sơ Người Dùng</h1>
    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row justify-content-center align-items-center">
        <!-- Cột trái: Avatar -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center">
                    <!-- Avatar -->
                    <div class="avatar-container mx-auto mb-3"
                        style="width: 180px; height: 180px; border-radius: 50%; overflow: hidden;">
                        <img src="{{ Auth::user()->profile_picture ? asset('storage/public/avatars/' . basename(Auth::user()->profile_picture)) : asset('images/avt.jpg') }}"
                            id="preview-avatar" class="img-fluid" alt="Avatar"
                            style="object-fit: cover; width: 100%; height: 100%;">
                    </div>

                    <!-- Form chọn và lưu ảnh -->
                    <form action="{{ route('admin.adminprofile.upload-avatar') }}" method="POST"
                        enctype="multipart/form-data" class="mt-3">
                        @csrf
                        <div class="mb-3">
                            <!-- Nút chọn file tùy chỉnh -->
                            <label for="profile_picture" class="btn btn-primary w-100" style="cursor: pointer;">
                                <i class="fas fa-upload"></i> Chọn ảnh
                            </label>
                            <input type="file" name="profile_picture" id="profile_picture" accept="image/*"
                                class="d-none @error('profile_picture') is-invalid @enderror"
                                onchange="previewImage(event)">
                            @error('profile_picture')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" id="save-button" class="btn btn-primary w-100" style="display: none;">Lưu
                            Ảnh</button>
                    </form>

                    <!-- Nút xóa ảnh -->
                    @if(Auth::user()->profile_picture)
                    <form action="{{ route('admin.adminprofile.remove-avatar') }}" method="POST" class="mt-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">Gỡ Ảnh Đại Diện</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Cột phải: Thông tin cá nhân -->
        <div class="col-lg-7">
            <div class="card shadow-lg border-0">
                <div class="card-body">
                    <h4 class="card-title text-primary mb-4 text-center">Thông Tin Cá Nhân</h4>
                    <form action="{{ route('admin.adminprofile.update') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên</label>
                            <input type="text" name="name" id="name" value="{{ old('name', Auth::user()->name) }}"
                                class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số Điện Thoại</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', Auth::user()->phone) }}"
                                class="form-control @error('phone') is-invalid @enderror">
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="birth_date" class="form-label">Ngày Sinh</label>
                            <div class="input-group">
                                <input type="text" name="birth_date" id="birth_date"
                                    value="{{ old('birth_date', \Carbon\Carbon::parse(Auth::user()->birth_date)->format('d/m/Y')) }}"
                                    class="form-control @error('birth_date') is-invalid @enderror"
                                    placeholder="Chọn ngày">
                                <span class="input-group-text" id="calendar-icon" style="cursor: pointer;">
                                    <i class="fas fa-calendar-alt"></i> <!-- Icon lịch -->
                                </span>
                            </div>
                            @error('birth_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-success w-100">Cập Nhật Thông Tin</button>
                        <button type="button" class="btn btn-outline-warning w-100 mt-3" data-bs-toggle="modal"
                            data-bs-target="#changePasswordModal">Đổi Mật Khẩu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <!-- Modal đổi mật khẩu -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content shadow">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Đổi Mật Khẩu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.adminprofile.update-password') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                            <input type="password" name="current_password" id="current_password"
                                class="form-control @error('current_password') is-invalid @enderror">
                            @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Mật khẩu mới</label>
                            <input type="password" name="new_password" id="new_password"
                                class="form-control @error('new_password') is-invalid @enderror">
                            @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                class="form-control">
                        </div>
                        <button type="submit" class="btn btn-warning w-100">Đổi Mật Khẩu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
// Khởi tạo Flatpickr cho input birth_date
flatpickr("#birth_date", {
    dateFormat: "d/m/Y", // Định dạng ngày/tháng/năm
    locale: "vi" // Tùy chọn ngôn ngữ Tiếng Việt
});

// Mở Flatpickr khi nhấn vào icon lịch
document.getElementById('calendar-icon').addEventListener('click', function() {
    document.getElementById('birth_date')._flatpickr.open();
});

// Xem trước ảnh khi chọn file
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-avatar').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
}
</script>

@endsection
