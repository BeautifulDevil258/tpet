@extends('layouts.adminapp')

@section('content')
<div class="container">
    <h1 class="mt-4 text-primary"><i class="fas fa-user"></i> Thông Tin Khách Hàng</h1>

    <div class="card mt-4 shadow-sm">
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th class="bg-light">Tên khách hàng:</th>
                    <td>{{ $customer->name }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Email:</th>
                    <td>{{ $customer->email }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Số điện thoại:</th>
                    <td>{{ $customer->phone ?? 'Chưa cập nhật' }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Địa chỉ:</th>
                    <td>
                        @if($currentAddress)
                        {{ $currentAddress->detail }}, {{ $currentAddress->ward }}, {{ $currentAddress->district }}, {{ $currentAddress->city }}
                        @else
                        <span class="text-muted">Chưa có địa chỉ mặc định</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th class="bg-light">Ngày đăng ký:</th>
                    <td>{{ $customer->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            </table>

            <div class="mt-3">
                <a href="{{ route('customers.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i>
                    Quay lại</a>
                <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning"><i
                        class="fas fa-edit"></i> Chỉnh sửa</a>
            </div>
        </div>
    </div>

    <!-- Danh sách đơn hàng của khách hàng -->
    <h2 class="mt-5 text-success"><i class="fas fa-shopping-cart"></i> Lịch Sử Mua Hàng</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            @if($customer->orders->isEmpty())
            <p class="text-muted">Khách hàng chưa có đơn hàng nào.</p>
            @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Mã Đơn Hàng</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                        <th>Ngày Đặt</th>
                        <th>Chi Tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customer->orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ number_format($order->total_price, 0, ',', '.') }} đ</td>
                        <td>
                            <span class="badge bg-{{ $order->status == 'completed' ? 'success' : 'warning' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.order.show', $order->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> Xem
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>
@endsection