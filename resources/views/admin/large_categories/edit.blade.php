@extends('layouts.adminapp')

@section('content')
<div class="container py-4">
    <h2 class="text-center text-primary mb-4"><i class="fas fa-paw"></i> Sửa Danh Mục Lớn</h2>
    
    <div class="card shadow-sm p-4 bg-white rounded">
        <form action="{{ route('large_categories.update', $largeCategory->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="name" class="font-weight-bold"><i class="fas fa-tag"></i> Tên Danh Mục Lớn</label>
                <input type="text" id="name" name="name" class="form-control shadow-sm" value="{{ old('name', $largeCategory->name) }}" required>
            </div>
            
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-success shadow-sm px-4">
                    <i class="fas fa-save"></i> Cập Nhật
                </button>
                <a href="{{ route('large_categories.index') }}" class="btn btn-secondary shadow-sm px-4">
                    <i class="fas fa-times"></i> Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
