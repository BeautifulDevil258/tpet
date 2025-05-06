@extends('layouts.adminapp')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="text-primary">🐾 Quản lý Bài Viết</h1>
        <a href="{{ route('posts.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Thêm Bài Viết
        </a>
    </div>

    <!-- Tìm kiếm -->
    <form action="{{ route('posts.index') }}" method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control shadow-none" placeholder="🔍 Tìm kiếm bài viết..."
                value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
        </div>
    </form>

    <div class="card shadow-lg p-3">
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Ảnh</th>
                        <th>Tiêu đề</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($posts as $post)
                    <tr>
                        <td>
                            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" width="80"
                                height="60" class="rounded">
                        </td>
                        <td class="align-middle">{{ $post->title }}</td>
                        <td class="align-middle">{{ $post->created_at->format('d/m/Y') }}</td>
                        <td class="align-middle">
                            <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="delete-form d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm delete-btn"
                                    data-id="{{ $post->id }}">
                                    <i class="fas fa-trash-alt"></i> Xóa
                                </button>
                                
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3 d-flex justify-content-center">
            {{ $posts->links() }}
        </div>
    </div>
</div>
@endsection