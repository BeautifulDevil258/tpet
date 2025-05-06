<?php
namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Cart;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Đăng ký view composer cho tất cả các view
        View::composer('*', function ($view) {
            // Tính toán số lượng giỏ hàng của người dùng hiện tại
            $cartCount = CartItem::whereHas('cart', function ($query) {
                $query->where('user_id', auth()->id());
            })->count();
            // Chia sẻ biến cartCount với tất cả các view
            $view->with('cartCount', $cartCount);
        });
    }

    public function register()
    {
        //
    }
}
