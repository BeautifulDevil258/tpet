@extends('layouts.adminapp')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4 text-primary"><i class="fas fa-paw"></i> Quản Lý Sản Phẩm</h1>

    <!-- Bố trí 3 phần Tìm kiếm, Thêm Sản Phẩm, Nhập Excel cạnh nhau -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <!-- Form tìm kiếm sản phẩm -->
        <form action="{{ route('product.search') }}" method="GET" class="d-flex align-items-center"
            style="width: 40%; margin-right: 10px;">
            <input type="text" name="query" class="form-control shadow-none" placeholder="🔍 Tìm kiếm sản phẩm..."
                value="{{ request('query') }}" style="width: 100%;">
            <button type="submit" class="btn btn-success"><i class="fas fa-search"></i></button>
        </form>

        <!-- Nút Thêm sản phẩm -->
        <div style="width: 30%; margin-right: 10px;">
            <a href="{{ route('products.create') }}" class="btn btn-success w-100"><i class="fas fa-plus"></i> Thêm Sản
                Phẩm</a>
        </div>

        <!-- Form nhập Excel -->
        <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data" id="import-form"
            style="width: 30%;">
            @csrf
            <div class="form-group">
                <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center"
                    id="choose-file-btn"
                    style="border-radius: 8px; padding: 8px 12px; font-size: 16px; background-color: #3498db; border: none; color: white; width: 100%; text-align: center;">
                    <i class="fas fa-file-excel"></i> Nhập Excel
                </button>
                <input type="file" id="file" name="file" class="form-control" accept=".xlsx, .xls"
                    style="display: none;" onchange="submitForm()">
            </div>
        </form>
    </div>

    <!-- Hiển thị tên file đã chọn -->
    <div id="file-preview" class="mt-2" style="text-align: center;"></div>

    <!-- Bảng danh sách sản phẩm -->
    @if ($products->isEmpty())
    <div class="alert alert-warning text-center mt-4">
        <i class="fas fa-exclamation-circle"></i> Chưa có sản phẩm nào.
    </div>
    @else
    <div class="table-responsive mt-4">
        <table class="table table-bordered table-striped align-middle">
            <thead class="bg-success text-white text-center">
                <tr>
                    <th>ID</th>
                    <th>📦 Tên Sản Phẩm</th>
                    <th>💰 Giá</th>
                    <th>🔢 Số Lượng</th>
                    <th>📂 Danh Mục</th>
                    <th>🖼 Hình Ảnh</th>
                    <th>⚙️ Hành Động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr class="text-center">
                    <td><strong>#{{ $product->id }}</strong></td>
                    <td>{{ $product->name }}</td>
                    <td><span class="badge bg-primary text-white">{{ number_format($product->price) }} VND</span></td>
                    <td>{{ $product->quantity }}</td>
                    <td>{{ $product->smallCategory->name }}</td>
                    <td>
                        @if($product->image)
                        <img src="{{ asset('images/' . $product->image) }}" alt="Product Image" class="rounded-circle"
                            width="60" height="60">
                        @else
                        <span class="text-muted">Chưa có ảnh</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('products.edit', $product->id) }}"
                                class="delete-form btn btn-warning btn-sm"><i class="fas fa-edit"></i> Sửa</a>
                            <form method="POST" action="{{ route('products.destroy', $product->id) }}"
                                class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm delete-btn"
                                    data-id="{{ $product->id }}"><i class="fas fa-trash-alt"></i> Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $products->links('pagination::bootstrap-4') }}
    </div>
    @endif
</div>

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

.gap-2>* {
    margin-right: 5px;
}
</style>

<script>
// Xử lý khi nhấn nút chọn file
document.getElementById('choose-file-btn').addEventListener('click', function() {
    document.getElementById('file').click();
});

// Hiển thị tên file đã chọn
document.getElementById('file').addEventListener('change', function(event) {
    const fileName = event.target.files[0] ? event.target.files[0].name : 'Chưa chọn file';
    document.getElementById('file-preview').textContent = fileName;
});

// Hàm gửi form khi chọn file xong
function submitForm() {
    document.getElementById('import-form').submit();
}
</script>
@endsection