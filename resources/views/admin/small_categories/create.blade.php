@extends('layouts.adminapp')

@section('content')
    <h2>Thêm Danh Mục Nhỏ</h2>

    <form action="{{ route('small_categories.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label for="name" class="form-label">Tên Danh Mục Nhỏ</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $smallCategory->name ?? '') }}" required>
    </div>

    <div class="mb-3">
        <label for="large_category_id" class="form-label">Danh Mục Lớn</label>
        <select class="form-control" id="large_category_id" name="large_category_id" required>
            @foreach ($largeCategories as $largeCategory)
                <option value="{{ $largeCategory->id }}" 
                        {{ isset($smallCategory) && $smallCategory->large_category_id == $largeCategory->id ? 'selected' : '' }}>
                    {{ $largeCategory->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="image" class="form-label">Hình Ảnh</label>
        <input type="file" class="form-control" id="image" name="image" accept="image/*">
        @if (isset($smallCategory) && $smallCategory->image)
            <div class="mt-2">
                <img src="{{ asset('images/' . $smallCategory->image) }}" alt="Current Image" width="100">
            </div>
        @endif
    </div>

    <button type="submit" class="btn btn-success">{{ isset($smallCategory) ? 'Cập Nhật' : 'Thêm Mới' }}</button>
    <a href="{{ route('small_categories.index') }}" class="btn btn-secondary">Hủy</a>
</form>

@endsection
