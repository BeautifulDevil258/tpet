@extends('layouts.adminapp')

@section('content')
<div class="container py-4">
    <h2 class="text-center text-primary mb-4"><i class="fas fa-paw"></i> Quản lý Danh Mục Nhỏ</h2>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('small_categories.create') }}" class="btn btn-success shadow-sm">
            <i class="fas fa-plus-circle"></i> Thêm Danh Mục Nhỏ
        </a>
        <form action="{{ route('small_categories.import') }}" method="POST" enctype="multipart/form-data" id="import-form"
            style="width: 30%;">
            @csrf
            <div class="form-group">
                <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center"
                    id="choose-file-btn"
                    style="border-radius: 8px; padding: 8px 12px; font-size: 16px; background-color: #3498db; border: none; color: white; width: 100%; text-align: center;">
                    <i class="fas fa-file-excel"></i> Nhập Excel
                </button>
                <input type="file" id="file" name="file" class="form-control" accept=".xlsx, .xls"
                    style="display: none;" onchange="submitForm()">
            </div>
        </form>
        <input type="text" id="search" class="form-control w-50 shadow-sm" placeholder="🔍 Tìm kiếm danh mục...">
    </div>
    
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center bg-white shadow-sm rounded">
            <thead class="bg-primary text-white">
                <tr>
                    <th>Tên Danh Mục Nhỏ</th>
                    <th>Danh Mục Lớn</th>
                    <th>Hình Ảnh</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody id="categoryTable">
                @foreach ($smallCategories as $smallCategory)
                <tr>
                    <td class="align-middle">
                        <i class="fas fa-bone text-warning"></i> {{ $smallCategory->name }}
                    </td>
                    <td class="align-middle">{{ $smallCategory->largeCategory->name }}</td>
                    <td class="align-middle">
                        @if ($smallCategory->image)
                            <img src="{{ asset('images/' . $smallCategory->image) }}" alt="{{ $smallCategory->name }}" class="img-thumbnail" width="50">
                        @else
                            <span class="text-muted">Chưa có ảnh</span>
                        @endif
                    </td>
                    <td class="align-middle">
                        <a href="{{ route('small_categories.edit', $smallCategory->id) }}" class="btn btn-warning btn-sm mx-1 shadow-sm">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        <form method="POST" action="{{ route('small_categories.destroy', $smallCategory->id) }}"
                                class="delete-form d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm delete-btn"
                                    data-id="{{ $smallCategory->id }}"><i class="fas fa-trash-alt"></i> Xóa</button>
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

<script>
// Xử lý khi nhấn nút chọn file
document.getElementById('choose-file-btn').addEventListener('click', function() {
    document.getElementById('file').click();
});

// Hiển thị tên file đã chọn
document.getElementById('file').addEventListener('change', function(event) {
    const fileName = event.target.files[0] ? event.target.files[0].name : 'Chưa chọn file';
    document.getElementById('file-preview').textContent = fileName;
});

// Hàm gửi form khi chọn file xong
function submitForm() {
    document.getElementById('import-form').submit();
}
</script>
@endsection
