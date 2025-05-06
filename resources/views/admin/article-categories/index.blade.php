@extends('layouts.adminapp')

@section('content')
<div class="container">
    <h1 class="mt-4 text-primary"><i class="fas fa-list"></i> Quản lý Danh Mục</h1>

    <div class="d-flex justify-content-end mt-3">
        <a href="{{ route('article-categories.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Thêm Danh Mục
        </a>
    </div>

    <div class="card mt-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-list"></i> Danh sách Danh Mục</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Tên danh mục</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('article-categories.edit', $category->id) }}"
                                class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            <form action="{{ route('article-categories.destroy', $category->id) }}" method="POST"
                                class="d-inline-block delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm delete-btn"
                                    data-id="{{ $category->id }}">
                                    <i class="fas fa-trash-alt"></i> Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection