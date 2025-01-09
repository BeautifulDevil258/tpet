@extends('layouts.adminapp')

@section('content')
    <h1>Thêm Danh Mục Lớn</h1>
    <form action="{{ route('large_categories.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Tên Danh Mục</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Thêm</button>
    </form>
@endsection
