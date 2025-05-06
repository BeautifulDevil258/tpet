@extends('layouts.adminapp')

@section('content')
<div class="container py-4">
    <h2 class="text-center text-primary mb-4"><i class="fas fa-paw"></i> Thêm Danh Mục Lớn</h2>
    
    <div class="card shadow-sm p-4 bg-white rounded">
        <form action="{{ route('large_categories.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name" class="font-weight-bold"><i class="fas fa-tag"></i> Tên Danh Mục</label>
                <input type="text" id="name" name="name" class="form-control shadow-sm" placeholder="Nhập tên danh mục..." required>
            </div>
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-success shadow-sm px-4">
                    <i class="fas fa-plus-circle"></i> Thêm Danh Mục
                </button>
            </div>
        </form>
    </div>
</div>
@endsection