@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="text-center mb-4 fw-bold text-success">
        <i class="bi bi-geo-alt-fill me-2"></i>Địa Chỉ Giao Hàng
    </h2>

    {{-- Nút thêm địa chỉ --}}
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('addresses.create', ['return_url' => url()->current()]) }}" class="btn btn-success shadow-sm px-4">
            <i class="bi bi-plus-circle me-1"></i> Thêm địa chỉ
        </a>
    </div>

    {{-- Nếu chưa có địa chỉ --}}
    @if($addresses->isEmpty())
    <div class="alert alert-info text-center shadow-sm rounded">
        Bạn chưa có địa chỉ nào. Hãy thêm mới ngay nhé!
    </div>
    @else

    {{-- Danh sách địa chỉ --}}
    <div class="row row-cols-1 row-cols-md-2 g-4">
        @foreach($addresses as $address)
        <div class="col">
            <div class="card h-100 shadow border-0 rounded-4 position-relative">
                <div class="card-body">
                    {{-- Header --}}
                    <h5 class="card-title text-primary mb-2">
                        <i class="bi bi-person-circle me-2"></i> {{ $address->name }}
                    </h5>

                    {{-- Điện thoại --}}
                    <p class="mb-1">
                        <i class="bi bi-telephone-fill me-2 text-muted"></i>
                        <strong>SĐT:</strong> {{ $address->phone ?? 'Không có' }}
                    </p>

                    {{-- Địa chỉ chi tiết --}}
                    <p class="mb-1">
                        <i class="bi bi-geo-alt-fill me-2 text-muted"></i>
                        <strong>Địa chỉ:</strong>
                        {{ $address->detail }}, {{ $address->ward }}, {{ $address->district }}, {{ $address->city }}
                    </p>

                    {{-- Badge mặc định --}}
                    @if ($address->is_default)
                        <span class="badge bg-success position-absolute top-0 end-0 mt-2 me-2 rounded-pill px-3">
                            <i class="bi bi-star-fill me-1"></i> Mặc định
                        </span>
                    @endif
                </div>

                {{-- Hành động --}}
                <div class="card-footer bg-transparent border-0 d-flex justify-content-end gap-2">
                    <a href="{{ route('addresses.edit', ['address' => $address->id, 'return_url' => url()->current()]) }}"
                        class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-pencil-square me-1"></i> Sửa
                    </a>
                    <form action="{{ route('addresses.destroy', $address->id) }}" method="POST"
                        onsubmit="return confirm('Bạn chắc chắn muốn xóa địa chỉ này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash3 me-1"></i> Xóa
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
