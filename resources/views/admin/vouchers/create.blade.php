@extends('layouts.adminapp')

@section('content')
<div class="container">
    <h1 class="mt-4 text-primary"><i class="fas fa-plus"></i> Thêm Voucher Mới</h1>

    <div class="card mt-4 shadow-sm">
        <div class="card-body">
            <form action="{{ route('vouchers.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="code" class="form-label">Mã Voucher</label>
                    <input type="text" name="code" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="discount" class="form-label">Giảm Giá (%)</label>
                    <input type="number" name="discount" class="form-control" min="1" max="100" required>
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Số Lượng</label>
                    <input type="number" name="quantity" class="form-control" min="1" required>
                </div>

                <div class="mb-3">
                    <label for="min_score" class="form-label">Điểm tối thiểu</label>
                    <input type="number" name="min_score" class="form-control" min="1" required>
                </div>

                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Thêm Voucher</button>
                <a href="{{ route('vouchers.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
            </form>
        </div>
    </div>
</div>
@endsection
