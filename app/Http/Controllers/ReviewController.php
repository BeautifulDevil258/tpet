<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function create(Order $order)
{
    // Kiểm tra quyền truy cập
    if ($order->user_id !== auth()->id() || $order->status !== 'Đã giao') {
        return redirect()->route('orders.index')->with('error', 'Bạn không thể đánh giá đơn hàng này.');
    }

    // Lấy danh sách sản phẩm trong đơn hàng
    $products = $order->orderItems->map(function ($item) {
        return $item->product;
    });

    return view('reviews.create', compact('order', 'products'));
}
public function store(Request $request, Order $order)
{
    // Kiểm tra quyền truy cập
    if ($order->user_id !== auth()->id() || $order->status !== 'Đã giao') {
        return redirect()->route('orders.index')->with('error', 'Bạn không thể đánh giá đơn hàng này.');
    }

    $request->validate([
        'ratings'  => 'required|array',
        'comments' => 'nullable|array',
    ]);

    foreach ($request->ratings as $productId => $rating) {
        // Lưu đánh giá cho từng sản phẩm
        Review::create([
            'user_id'    => auth()->id(),
            'product_id' => $productId,
            'rating'     => $rating,
            'comment'    => $request->comments[$productId] ?? null,
        ]);
    }

     // Cập nhật trạng thái của đơn hàng thành 'Đã đánh giá'
     $order->rate = '1';
     $order->save();
 
     return redirect()->route('orders.index')->with('success', 'Đánh giá của bạn đã được lưu và trạng thái đơn hàng đã được cập nhật.');
}

}
