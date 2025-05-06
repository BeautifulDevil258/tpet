@extends('layouts.adminapp')

@section('content')
<div class="container">
    <h1 class="mt-4">Thêm Danh Mục</h1>

    <form action="{{ route('article-categories.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Tên danh mục</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Thêm</button>
    </form>
</div>
@endsection
