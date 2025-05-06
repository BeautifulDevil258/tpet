@extends('layouts.adminapp')

@section('content')
<h1 class="text-center mb-5" style="font-size: 2.5rem; color: #333;">Chấm công</h1>

<!-- Thanh tìm kiếm và lọc theo ngày -->
<div class="card p-4 shadow-sm mb-4">
    <form action="{{ route('checkincheckout.index') }}" method="GET" class="row g-3 align-items-center">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control shadow-none" placeholder="🔍 Tìm theo ID, tên hoặc số điện thoại..." value="{{ request('search') }}">
        </div>
        <div class="col-md-4">
            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success w-100"><i class="fas fa-search"></i> Tìm</button>
        </div>
    </form>
</div>

<!-- Phần trên bảng, chứa nút Check-In và Check-Out -->
<div class="row">
    <div class="col-md-4">
        <h5>{{ $employee->name }}</h5>

        @php
        $latestLog = $employee->checkInCheckOutLogs()->latest()->first();
        $currentTime = \Carbon\Carbon::now();
        $hasCheckedInToday = $latestLog && \Carbon\Carbon::parse($latestLog->created_at)->isToday();
        @endphp

        <!-- Nút Check-In -->
        @if (!$hasCheckedInToday || ($latestLog && $latestLog->check_out_time))
        <form action="{{ route('checkincheckout.checkIn', $employee->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary btn-block">Check-In</button>
        </form>
        @elseif ($latestLog && $latestLog->check_in_time && !$latestLog->check_out_time)
        <form action="{{ route('checkincheckout.checkOut',$employee->id) }}" method="POST" class="mt-2">
            @csrf
            <button type="submit" class="btn btn-danger btn-block">Check-Out</button>
        </form>
        @endif
    </div>
</div>

<!-- Bảng lịch sử check-in/check-out -->
<table class="table table-bordered mt-4">
    <thead>
        <tr>
            <th>Ngày</th>
            <th>ID Nhân Viên</th>
            <th>Tên Nhân Viên</th>
            <th>Số Điện Thoại</th>
            <th>Chấm công đi</th>
            <th>Lý do đi muộn</th>
            <th>Chấm công về</th>
            <th>Lý do về sớm</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($employee->checkInCheckOutLogs()->orderBy('created_at', 'desc')->when(request('date'), function($query) {
        return $query->whereDate('created_at', request('date'));
    })->when(request('search'), function($query) {
        $search = request('search');
        return $query->whereHas('employee', function($q) use ($search) {
            $q->where('id', 'like', "%$search%")
              ->orWhere('name', 'like', "%$search%")
              ->orWhere('phone', 'like', "%$search%");
        });
    })->get() as $log)
        <tr>
            <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y') }}</td>
            <td>{{ $log->employee->id }}</td>
            <td>{{ $log->employee->name }}</td>
            <td>{{ $log->employee->phone }}</td>
            <td>{{ $log->check_in_time ? \Carbon\Carbon::parse($log->check_in_time)->format('H:i:s') : 'Chưa check-in' }}</td>
            <td>{{ $log->reason_late ?? 'Không có lý do' }}</td>
            <td>{{ $log->check_out_time ? \Carbon\Carbon::parse($log->check_out_time)->format('H:i:s') : 'Chưa check-out' }}</td>
            <td>{{ $log->reason_early ?? 'Không có lý do' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
