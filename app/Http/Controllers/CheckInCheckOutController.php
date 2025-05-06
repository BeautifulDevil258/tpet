<?php
namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\CheckInCheckOutLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CheckInCheckOutController extends Controller
{
    public function index()
    {
        // Lấy thông tin của admin đang đăng nhập
        $employee = Auth::guard('admin')->user();
        
        // Kiểm tra nếu có bất kỳ log check-in/check-out nào của nhân viên
        foreach ($employee->checkInCheckOutLogs as $log) {
            // Chuyển đổi check_in_time và check_out_time thành Carbon nếu chưa phải đối tượng Carbon
            if ($log->check_in_time && !$log->check_in_time instanceof Carbon) {
                $log->check_in_time = Carbon::parse($log->check_in_time);
            }
            if ($log->check_out_time && !$log->check_out_time instanceof Carbon) {
                $log->check_out_time = Carbon::parse($log->check_out_time);
            }
        }

        // Kiểm tra xem nhân viên đã check-in hôm nay chưa
        $latestLog = $employee->checkInCheckOutLogs()->latest()->first();
        $today = Carbon::today(); // Lấy ngày hôm nay
        
        // Kiểm tra xem có log check-in nào trong ngày hôm nay
        $isCheckedInToday = $latestLog && $latestLog->check_in_time && Carbon::parse($latestLog->check_in_time)->isToday();

        return view('admin.checkincheckout.index', compact('employee', 'isCheckedInToday'));
    }
    public function checkIn(Request $request)
    {
        $employee = Auth::guard('admin')->user();
        $checkInThreshold = \Carbon\Carbon::parse('08:00:00'); // Mốc thời gian check-in
        $currentTime = \Carbon\Carbon::now();

        // Kiểm tra nếu đã check-in trong ngày hôm nay
        $latestLog = $employee->checkInCheckOutLogs()->latest()->first();
        $today = Carbon::today();
        $isCheckedInToday = $latestLog && $latestLog->check_in_time && Carbon::parse($latestLog->check_in_time)->isToday();

        if ($isCheckedInToday) {
            return back()->with('error', 'Bạn đã check-in hôm nay rồi.');
        }

        // Nếu check-in sớm, lưu ngay thời gian check-in
        if ($currentTime <= $checkInThreshold) {
            $employee->checkInCheckOutLogs()->create([
                'check_in_time' => $currentTime,
            ]);
            return back()->with('success', 'Check-in thành công.');
        }

        // Nếu check-in muộn, hiển thị form nhập lý do
        return view('admin.checkincheckout.checkin_form', compact('employee', 'currentTime'));
    }

    public function checkOut(Request $request)
    {
        $employee = Auth::guard('admin')->user();
        $checkOutThreshold = \Carbon\Carbon::parse('17:00:00'); // Mốc thời gian check-out
        $currentTime = \Carbon\Carbon::now();

        // Nếu check-out đúng hoặc muộn, lưu thời gian check-out
        if ($currentTime >= $checkOutThreshold) {
            $employee->checkInCheckOutLogs()->latest()->first()->update([
                'check_out_time' => $currentTime,
            ]);
            return back()->with('success', 'Check-out thành công.');
        }

        // Nếu check-out sớm, hiển thị form nhập lý do
        return view('admin.checkincheckout.checkout_form', compact('employee', 'currentTime'));
    }

    public function storeCheckInReason(Request $request)
    {
        $employee = Auth::guard('admin')->user();
        $employee->checkInCheckOutLogs()->create([
            'check_in_time' => \Carbon\Carbon::now(),
            'reason_late' => $request->reason_late,
        ]);
        return redirect()->route('checkincheckout.index')->with('success', 'Lý do check-in muộn đã được lưu.');
    }

    public function storeCheckOutReason(Request $request)
    {
        $employee = Auth::guard('admin')->user();
        $employee->checkInCheckOutLogs()->latest()->first()->update([
            'check_out_time' => \Carbon\Carbon::now(),
            'reason_early' => $request->reason_early,
        ]);
        return redirect()->route('checkincheckout.index')->with('success', 'Lý do check-out sớm đã được lưu.');
    }
}
