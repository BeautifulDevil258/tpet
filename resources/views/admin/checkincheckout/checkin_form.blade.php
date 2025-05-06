@extends('layouts.adminapp')

@section('content')
<form action="{{ route('checkincheckout.storeCheckInReason', $employee->id) }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="reason_late">Lý do check-in muộn:</label>
        <textarea name="reason_late" class="form-control" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary btn-block">Gửi Lý Do</button>
</form>
@endsection
