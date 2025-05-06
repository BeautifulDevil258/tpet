<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = ['cart_id', 'product_id', 'quantity', 'price'];

    // Quan hệ với Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
    // Phương thức tính tổng tiền cho một sản phẩm trong giỏ hàng
    public function getTotalPrice()
    {
        return $this->quantity * $this->product->price;
    }
}
