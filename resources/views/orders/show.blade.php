@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-lg rounded-lg border-0">
    <div class="card-header bg-gradient-to-r from-blue-500 to-green-500 rounded-t-lg">
    <h2 class="font-weight-bold text-2xl text-center" style="color: #28a745;">Chi tiết đơn hàng</h2>
</div>
        <div class="card-body">
            <div class="row">
                <!-- Thông tin đơn hàng -->
                <div class="col-md-6">
                    <h4 class="text-primary font-weight-bold" style="color: #28a745;">Thông tin đơn hàng</h4>
                    <table class="table table-striped">
                        <tr>
                            <th>Mã đơn hàng</th>
                            <td>{{ $order->order_code }}</td>
                        </tr>
                        <tr>
                            <th>Tên người nhận</th>
                            <td>{{ $order->recipient_name }}</td>
                        </tr>
                        <tr>
                            <th>Ngày đặt</th>
                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Trạng thái</th>
                            <td>
                                <span class="badge
                                    @if ($order->status == 'Chờ lấy hàng')
                                        bg-warning
                                    @elseif ($order->status == 'Đang giao')
                                        bg-info
                                    @else
                                        bg-success
                                    @endif">
                                    {{ $order->status }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Địa chỉ giao hàng</th>
                            <td>{{ $order->shipping_address }}</td>
                        </tr>
                        <tr>
                            <th>Tổng tiền</th>
                            <td class="font-weight-bold text-success">{{ number_format($order->total_price, 0, ',', '.') }} VNĐ</td>
                        </tr>
                    </table>
                </div>

                <!-- Danh sách sản phẩm -->
                <div class="col-md-6">
                    <h4 class="text-primary font-weight-bold" style="color: #28a745;">Danh sách sản phẩm</h4>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">ID Sản phẩm</th>
                                <th class="text-center">Tên sản phẩm</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-center">Giá</th>
                                <th class="text-center">Tổng tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderItems as $item)
                                <tr>
                                    <td class="text-center">{{ $item->product->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/' . $item->product->image) }}"
                                                 class="img-fluid rounded-circle mr-2" alt="{{ $item->product->name }}"
                                                 style="width: 50px; height: 50px;">
                                            {{ $item->product->name }}
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-center">{{ number_format($item->product->price, 0, ',', '.') }} VND</td>
                                    <td class="text-center">{{ number_format($item->price, 0, ',', '.') }} VND</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Nút hủy đơn hàng nếu trạng thái là "Chờ lấy hàng" -->
            @if ($order->status == 'Chờ lấy hàng')
                <div class="mt-4 text-center">
                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-lg font-weight-bold px-4 py-2 rounded-pill">
                            <i class="fas fa-times-circle"></i> Hủy đơn hàng
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
