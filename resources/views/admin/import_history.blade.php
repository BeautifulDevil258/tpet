@extends('layouts.adminapp')

@section('content')
<div class="container mt-4">
    <h1 class="text-center text-primary"><i class="fas fa-warehouse"></i> Lịch Sử Nhập Hàng</h1>

    <div class="card p-4 shadow-sm">
        <form action="{{ route('import_history.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="🔍 Tìm kiếm theo ID hoặc tên sản phẩm..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            </div>
        </form>
    </div>

    @if ($importHistories->isEmpty())
        <div class="alert alert-warning text-center mt-4">
            <i class="fas fa-exclamation-circle"></i> Không tìm thấy lịch sử nhập hàng nào.
        </div>
    @else
        <div class="table-responsive mt-4">
            <table class="table table-bordered table-striped align-middle">
                <thead class="bg-success text-white text-center">
                    <tr>
                        <th>ID</th>
                        <th>📦 Sản Phẩm</th>
                        <th>📅 Ngày Nhập</th>
                        <th>🔢 Số Lượng</th>
                        <th>💰 Giá Nhập</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($importHistories as $history)
                    <tr class="text-center">
                        <td>#{{ $history->id }}</td>
                        <td>{{ $history->product->name }}</td>
                        <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $history->quantity }}</td>
                        <td><span class="badge bg-primary">{{ number_format($history->import_price) }} VND</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $importHistories->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
@endsection
