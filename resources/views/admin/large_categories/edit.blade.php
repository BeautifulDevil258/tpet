@extends('layouts.adminapp')

@section('content')
    <h2>Sửa Danh Mục Lớn</h2>

    <form action="{{ route('large_categories.update', $largeCategory->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Tên Danh Mục Lớn</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $largeCategory->name) }}" required>
        </div>

        <button type="submit" class="btn btn-success">Cập Nhật</button>
        <a href="{{ route('large_categories.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
@endsection
