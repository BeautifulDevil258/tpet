@extends('layouts.adminapp')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center text-primary"><i class="fas fa-paw"></i> Quản lý Danh Mục Lớn</h2>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('large_categories.create') }}" class="btn btn-success shadow-sm">
            <i class="fas fa-plus-circle"></i> Thêm Danh Mục
        </a>
        <input type="text" id="search" class="form-control w-50 shadow-sm" placeholder="🔍 Tìm kiếm danh mục...">
    </div>
    
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center bg-white shadow-sm rounded">
            <thead class="bg-primary text-white">
                <tr>
                    <th>Tên Danh Mục</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody id="categoryTable">
                @foreach ($largeCategories as $largeCategory)
                <tr>
                    <td class="align-middle">
                        <i class="fas fa-bone text-warning"></i> {{ $largeCategory->name }}
                    </td>
                    <td class="align-middle">
                        <a href="{{ route('large_categories.edit', $largeCategory->id) }}" class="btn btn-warning btn-sm mx-1 shadow-sm">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        <form method="POST" action="{{ route('large_categories.destroy', $largeCategory->id) }}"
                                class="delete-form" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm delete-btn"
                                    data-id="{{ $largeCategory->id }}"><i class="fas fa-trash-alt"></i> Xóa</button>
                            </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    document.getElementById('search').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#categoryTable tr');
        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
</script>
@endsection