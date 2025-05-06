<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'product_name', 'quantity', 'price'];

    // Quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
    // Phương thức tính tổng tiền của giỏ hàng
    public function totalPrice()
    {
        // Cộng dồn price (giá tạm tính) của tất cả các món hàng trong giỏ hàng
        return $this->items->sum(function ($cartItem) {
            return $cartItem->price; // Đây là giá tạm tính của mỗi sản phẩm
        });
    }
    // Phương thức lấy các sản phẩm trong giỏ hàng theo danh sách ID
    public function getItemsByIds(array $ids)
    {
        return $this->items()->whereIn('product_id', $ids)->get();

    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}
