@extends('layouts.adminapp')

@section('content')
<div class="container">
    <h1 class="mt-4 text-primary"><i class="fas fa-ticket-alt"></i> Quản lý Voucher</h1>

    <!-- Thanh tìm kiếm và nút thêm voucher trên cùng một hàng -->
    <div class="row mt-3">
        <div class="col-md-6">
            <form action="{{ route('vouchers.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control shadow-none" placeholder="Nhập mã hoặc tên voucher..."
                        value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('vouchers.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Thêm Voucher
            </a>
        </div>
    </div>

    <!-- Danh sách voucher -->
    <div class="card mt-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-list"></i> Danh sách Voucher</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Mã Voucher</th>
                        <th>Giảm Giá</th>
                        <th>Số Lượng</th>
                        <th>Ngày Tạo</th>
                        <th>Ngày Hết Hạn</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vouchers as $voucher)
                    <tr>
                        <td><span class="badge bg-info text-white">{{ $voucher->code }}</span></td>
                        <td><strong class="text-danger">{{ $voucher->discount }}%</strong></td>
                        <td>{{ $voucher->quantity }}</td>
                        <td>{{ $voucher->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($voucher->expires_at)->format('d/m/Y H:i') }}
                        </td>

                        <td>
                            <a href="{{ route('vouchers.edit', $voucher->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            <form action="{{ route('vouchers.destroy', $voucher->id) }}" method="POST"
                                class="d-inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm delete-btn"
                                    data-id="{{ $voucher->id }}">
                                    <i class="fas fa-trash-alt"></i> Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection