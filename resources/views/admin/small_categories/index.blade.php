@extends('layouts.adminapp')

@section('content')
    <h2>Quản lý Danh Mục Nhỏ</h2>
    <a href="{{ route('small_categories.create') }}" class="btn btn-primary mb-3">Thêm Danh Mục Nhỏ</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tên Danh Mục Nhỏ</th>
                <th>Danh Mục Lớn</th>
                <th>Hình Ảnh</th> <!-- New column for image -->
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($smallCategories as $smallCategory)
            <tr>
                <td>{{ $smallCategory->name }}</td>
                <td>{{ $smallCategory->largeCategory->name }}</td>
                <td>
                    @if ($smallCategory->image)
                        <img src="{{ asset('images/' . $smallCategory->image) }}" alt="{{ $smallCategory->name }}" width="50">
                    @else
                        <span>Chưa có ảnh</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('small_categories.edit', $smallCategory->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                    <form action="{{ route('small_categories.destroy', $smallCategory->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa không?')">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
