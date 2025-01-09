@extends('layouts.adminapp')

@section('content')
    <h1>Danh Sách Sản Phẩm</h1>

    <!-- Thêm sản phẩm mới -->
    <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">Thêm Sản Phẩm</a>

    <!-- Hiển thị thông báo nếu có -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Bảng Danh Sách Sản Phẩm -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên Sản Phẩm</th>
                <th>Giá</th>
                <th>Số Lượng</th>
                <th>Mô Tả</th>
                <th>Danh mục</th>
                <th>Hình Ảnh</th>
                <th>Thao Tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ number_format($product->price) }} VND</td>
                    <td>{{ $product->quantity }}</td>
                    <td>{{ Str::limit($product->description, 50) }}</td>
                    <td>{{$product->smallCategory->name}}</td>
                    <td>
                        @if($product->image)
                            <img src="{{ asset('images/' . $product->image) }}" alt="Product Image" width="50">
                        @else
                            <span>Chưa có hình ảnh</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning">Sửa</a>
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
