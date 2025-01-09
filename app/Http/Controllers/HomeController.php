<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\SmallCategory;
use App\Models\LargeCategory;

class HomeController extends Controller
{
    public function index()
{
    // Lấy tất cả danh mục lớn kèm danh mục nhỏ
    // Lấy tất cả danh mục lớn và danh mục nhỏ liên kết
    $largeCategories = LargeCategory::with('smallCategories')->get();

    return view('home', compact('largeCategories'));
}
}