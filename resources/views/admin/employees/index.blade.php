@extends('layouts.adminapp')

@section('content')
<h1 class="text-center mb-5" style="font-size: 2.5rem; color: #333;">🐾 Danh Sách Nhân Viên 🐾</h1>

<div class="card p-4 shadow-sm mb-4">
    <form action="{{ route('employees.index') }}" method="GET" class="row g-3 align-items-center">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control shadow-none" placeholder="🔍 Tìm kiếm nhân viên..."
                value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-success"><i class="fas fa-search"></i></button>
        </div>
        <div class="col-md-3 text-end">
            <a href="{{ route('employees.create') }}" class="btn btn-success w-100"><i class="fas fa-user-plus"></i>
                Thêm Nhân Viên</a>
        </div>
    </form>
</div>

<!-- Bảng Danh Sách Nhân Viên -->
<div class="table-responsive">
    <table class="table table-hover align-middle text-center">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>👤 Họ Tên</th>
                <th>📧 Email</th>
                <th>📞 Số Điện Thoại</th>
                <th>🎂 Ngày Sinh</th>
                <th>⚤ Giới Tính</th>
                <th>🖼 Ảnh Đại Diện</th>
                <th>⚙️ Thao Tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
            <tr>
                <td>{{ $employee->id }}</td>
                <td>{{ $employee->name }}</td>
                <td>{{ $employee->email }}</td>
                <td>{{ $employee->phone }}</td>
                <td>{{ $employee->birth_date }}</td>
                <td>{{ $employee->gender == 'male' ? 'Nam' : 'Nữ' }}</td>
                <td>
                    @if($employee->profile_picture)
                    <img src="{{ asset('storage/public/avatars/' . basename($employee->profile_picture)) }}"
                        alt="Profile Picture" width="50" class="rounded-circle border">
                    @else
                    <span class="text-muted">Chưa có ảnh</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-warning btn-sm"><i
                            class="fas fa-edit"></i> Sửa</a>
                            <form method="POST" action="{{ route('employees.destroy', $employee->id) }}"
                                class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm delete-btn"
                                    data-id="{{ $employee->id }}"><i class="fas fa-trash-alt"></i> Xóa</button>
                            </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection