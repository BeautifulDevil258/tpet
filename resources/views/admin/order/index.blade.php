@extends('layouts.adminapp')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4 text-primary"><i class="fas fa-paw"></i> Quản Lý Đơn Hàng</h1>

    <div class="card p-4 shadow-sm">
        <form action="{{ route('orders.search') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control shadow-none" placeholder="🔍 Tìm kiếm theo tên hoặc ID đơn hàng" value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="all">📦 Tất cả trạng thái</option>
                    @foreach (\App\Models\Order::statusList() as $key => $value)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Tìm kiếm</button>
            </div>
        </form>
    </div>

    @if ($orders->isEmpty())
        <div class="alert alert-warning text-center mt-4">
            <i class="fas fa-exclamation-circle"></i> Chưa có đơn hàng nào.
        </div>
    @else
        <div class="table-responsive mt-4">
            <table class="table table-bordered table-striped align-middle">
                <thead class="bg-success text-white text-center">
                    <tr>
                        <th>ID</th>
                        <th>🧑 Người Nhận</th>
                        <th>💰 Tổng Tiền</th>
                        <th>📌 Trạng Thái</th>
                        <th>⚙️ Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                    <tr class="text-center">
                        <td><strong>#{{ $order->id }}</strong></td>
                        <td>{{ $order->recipient_name }}</td>
                        <td><span class="badge bg-primary text-white">{{ number_format($order->total_price, 0) }}₫</span></td>
                        <td>{{ \App\Models\Order::statusList()[$order->getRawOriginal('status')] ?? $order->status }}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.order.show', $order->id) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Xem</a>
                                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" id="form-{{ $order->id }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select form-select-sm d-inline w-auto"
                                        onchange="updateStatus({{ $order->id }}, this.value)">
                                        <option value="">🛠 Chọn Trạng Thái</option>
                                        @foreach (\App\Models\Order::statusList() as $key => $value)
                                            <option value="{{ $key }}" {{ $order->status == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>

<script>
function updateStatus(orderId, status) {
    if (status) {
        document.getElementById('form-' + orderId).submit();
    }
}
</script>

<style>
    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
        transition: background-color 0.3s ease-in-out;
    }
    .btn {
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: scale(1.05);
    }
    .gap-2 > * {
        margin-right: 5px;
    }
</style>
@endsection
