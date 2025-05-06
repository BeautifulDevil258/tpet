@extends('layouts.app')

@section('title', 'Thêm Địa Chỉ Mới')

@section('content')
<div class="container my-5">
    <div class="text-center mb-4">
        <h2 class="fw-bold text-primary-emphasis">🦴 Thêm Địa Chỉ Mới</h2>
        <p class="text-muted">Nhập thông tin chính xác để thuận tiện cho việc giao hàng nhanh chóng!</p>
    </div>

    <div class="card shadow-sm border-0 rounded-4 p-4" style="background-color: #ffffff;">
        <form action="{{ route('addresses.store') }}" method="POST">
            @csrf
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control border shadow-sm" name="name" id="name"
                            value="{{ old('name') }}" required>
                        <label for="name">👤 Họ Tên</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="tel" class="form-control border shadow-sm" name="phone" id="phone"
                            value="{{ old('phone') }}" required>
                        <label for="phone">📞 Số điện thoại</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-floating">
                        <input type="text" class="form-control border shadow-sm" name="detail" id="detail"
                            value="{{ old('detail') }}" required>
                        <label for="detail">🏠 Chi Tiết (Số Nhà, Đường)</label>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-2">
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="text" class="form-control border shadow-sm" name="ward" id="ward"
                            value="{{ old('ward') }}" required>
                        <label for="ward">🗺️ Phường/Xã</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="text" class="form-control border shadow-sm" name="district" id="district"
                            value="{{ old('district') }}" required>
                        <label for="district">🏞️ Quận/Huyện</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="text" class="form-control border shadow-sm" name="city" id="city"
                            value="{{ old('city') }}" required>
                        <label for="city">🏙️ Thành phố/Tỉnh</label>
                    </div>
                </div>
            </div>
            <div class="form-check form-switch mt-4 d-flex align-items-center">
                <input class="form-check-input me-2" type="checkbox" name="is_default" value="1" id="is_default">
                <label class="form-check-label text-muted mb-0" for="is_default">Đặt làm địa chỉ mặc định</label>
            </div>
            <div class="d-flex justify-content-end gap-3 mt-5">
                <a href="{{ route('checkout.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                    <i class="bi bi-arrow-left-circle"></i> Quay Lại
                </a>
                <button type="submit" class="btn btn-success btn-lg px-4">
                    <i class="bi bi-save"></i> Lưu Địa Chỉ
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
.form-floating>label {
    color: #6c757d;
}

.form-control {
    border-radius: 12px;
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
    transition: all 0.3s ease;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

.btn-outline-secondary:hover {
    background-color: #e2e6ea;
}

.card {
    background-image: linear-gradient(to right, #fdfbfb, #ebedee);
}

h2 {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
</style>
@endpush