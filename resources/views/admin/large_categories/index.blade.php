@extends('layouts.adminapp')

@section('content')
    <h2>Quản lý Danh Mục Lớn</h2>

    <a href="{{ route('large_categories.create') }}" class="btn btn-primary mb-3">Thêm Danh Mục Lớn</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tên Danh Mục</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($largeCategories as $largeCategory)
            <tr>
                <td>{{ $largeCategory->name }}</td>
                <td>
                    <a href="{{ route('large_categories.edit', $largeCategory->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                    <form action="{{ route('large_categories.destroy', $largeCategory->id) }}" method="POST" style="display: inline;">
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
