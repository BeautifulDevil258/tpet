@extends('layouts.adminapp')

@section('content')
<div class="container">
    <h2 class="text-center mb-4">🐾 Quản lý Khách Hàng 🐾</h2>

    <!-- Thanh tìm kiếm -->
    <form action="{{ route('customers.index') }}" method="GET" class="mb-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control shadow-none" placeholder="🔍 Tìm kiếm khách hàng..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-success"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>ID</th>
                    <th>🐶 Tên</th>
                    <th>📧 Email</th>
                    <th>📞 Điện thoại</th>
                    <th>⭐ Rank</th>
                    <th>📦 Tổng đơn</th>
                    <th>💰 Chi tiêu</th>
                    <th>⚙️ Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customers as $customer)
                <tr class="text-center">
                    <td>{{ $customer->id }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td><span class="badge bg-primary">{{ $customer->rank }}</span></td>
                    <td>{{ $customer->total_orders }}</td>
                    <td><strong class="text-success">{{ number_format($customer->total_spent, 0, ',', '.') }} đ</strong></td>
                    <td>
                        <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Xem</a>
                        <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Sửa</a>
                        <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="delete-form d-inline">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="{{ $customer->id }}">
                                <i class="fas fa-trash-alt"></i> Xóa
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $customers->links() }}
    </div>
</div>
@endsection
