<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CheckInCheckOutLog extends Model
{
    use HasFactory;

    // Tên bảng trong cơ sở dữ liệu
    protected $table = 'check_in_check_out_logs'; // Đảm bảo rằng tên bảng trong cơ sở dữ liệu là check_in_check_out_logs hoặc thay đổi tên bảng nếu cần.

    // Các thuộc tính có thể gán (fillable) hoặc các thuộc tính bảo mật (guarded)
    protected $fillable = [
        'admin_id', // ID của nhân viên
        'check_in_time', // Thời gian check-in
        'check_out_time', // Thời gian check-out
        'reason_late',
        'reason_early',
    ];

    // Nếu bạn muốn sử dụng kiểu dữ liệu đặc biệt (như Carbon cho ngày giờ), bạn có thể thêm thuộc tính dates:
    protected $dates = [
        'check_in_time',
        'check_out_time',
    ];

    /**
     * Quan hệ với bảng `admins` (nếu có). Một CheckInCheckOutLog thuộc về một nhân viên (Admin).
     */
    public function employee()
    {
        return $this->belongsTo(Admin::class, 'admin_id'); // Liên kết với bảng Admin
    }

    public function getWorkHoursAttribute()
    {
        if ($this->check_in_time && $this->check_out_time) {
            return Carbon::parse($this->check_in_time)->diffInMinutes(Carbon::parse($this->check_out_time)) / 60;
        }
        return 0;
    }
}
