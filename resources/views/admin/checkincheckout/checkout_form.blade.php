@extends('layouts.adminapp')

@section('content')
<form action="{{ route('checkincheckout.storeCheckOutReason', $employee->id) }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="reason_early">Lý do check-out sớm:</label>
        <textarea name="reason_early" class="form-control" required></textarea>
    </div>
    <button type="submit" class="btn btn-danger btn-block">Gửi Lý Do</button>
</form>
@endsection
