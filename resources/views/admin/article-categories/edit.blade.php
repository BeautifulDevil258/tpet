@extends('layouts.adminapp')

@section('content')
<div class="container">
    <h1 class="mt-4">Chỉnh sửa Danh Mục</h1>

    <form action="{{ route('article-categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Tên danh mục</label>
            <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
    </form>
</div>
@endsection
