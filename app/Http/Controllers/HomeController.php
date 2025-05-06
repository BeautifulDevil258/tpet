<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\SmallCategory;
use App\Models\LargeCategory;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy tất cả danh mục lớn kèm danh mục nhỏ
        $largeCategories = LargeCategory::with('smallCategories')->get();

        // Lấy 6 sản phẩm bán chạy nhất dựa trên tổng số lượng đã đặt hàng
        $bestSellingProducts = Product::join('order_items', 'products.id', '=', 'order_items.product_id')
            ->select(
                'products.id',
                'products.name',
                'products.image',
                'products.price',
                DB::raw('SUM(order_items.quantity) as total_sold')
            )
            ->groupBy('products.id', 'products.name', 'products.image', 'products.price')
            ->orderByDesc('total_sold')
            ->take(6)
            ->get();

        return view('home', compact('largeCategories', 'bestSellingProducts'));
    }
}
