<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'status', 'total_price', 'shipping_address', 'payment_method', 'recipient_name', 'order_code', 'rate', 'voucher_id',
    ];
// Định nghĩa các trạng thái đơn hàng
    const STATUS_PENDING   = 'pending';
    const STATUS_SHIPPED   = 'shipped';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELED = 'canceled';
    const STATUS_FAILED = 'failed';

// Mảng trạng thái tiếng Việt
    public static function statusList()
    {
        return [
            self::STATUS_PENDING   => 'Chờ lấy hàng',
            self::STATUS_SHIPPED   => 'Đang giao',
            self::STATUS_COMPLETED => 'Đã giao',
            self::STATUS_CANCELED => 'Đã hủy',
            self::STATUS_FAILED => 'Chưa thanh toán',
        ];
    }

// Phương thức giúp lấy trạng thái tiếng Việt
public function getStatusAttribute($value)
{
    $statuses = self::statusList(); // Danh sách trạng thái tiếng Việt

    return $statuses[$value] ?? $value; // Trả về trạng thái tiếng Anh nếu không tìm thấy
}

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    protected static function booted()
    {
        static::updated(function ($order) {
            Log::info("🛒 Đơn hàng {$order->id} cập nhật, trạng thái mới: {$order->status}");
    
            if ($order->wasChanged('status') && $order->status === 'completed') {
                Log::info("✅ Đơn hàng hoàn thành! Đang cập nhật hạng khách hàng...");
                $order->user->updateRank();
            }
        });
    }
}
