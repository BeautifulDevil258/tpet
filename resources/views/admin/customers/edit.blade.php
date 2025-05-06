@extends('layouts.adminapp')

@section('content')
<div class="container">
    <h2>Sửa khách hàng</h2>
    <form action="{{ route('customers.update', $customer->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Tên</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $customer->name }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ $customer->email }}" required>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Điện thoại</label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{ $customer->phone }}">
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Địa chỉ</label>
            <input type="text" class="form-control" id="address" name="address" value="{{ $customer->address }}">
        </div>

        <div class="mb-3">
            <label for="group" class="form-label">Nhóm</label>
            <select class="form-control" id="group" name="group">
                <option value="Khách lẻ" {{ $customer->group == 'Khách lẻ' ? 'selected' : '' }}>Khách lẻ</option>
                <option value="VIP" {{ $customer->group == 'VIP' ? 'selected' : '' }}>VIP</option>
                <option value="Khách sỉ" {{ $customer->group == 'Khách sỉ' ? 'selected' : '' }}>Khách sỉ</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Lưu</button>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
