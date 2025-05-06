<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;  // Sử dụng đúng Auth facade
use App\Models\Admin;
use App\Models\Product;
use App\Models\Employee;
use App\Models\Post;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Phương thức để kiểm tra quyền admin
    public function dashboard()
    {
       
    $totalProducts = Product::count();
    $totalEmployees = Admin::where('role', 'nhanvien')->count();
    $totalCustomers = User::count();
    $totalPosts = Post::count();
    $totalOrders = Order::count();

    return view('admin.dashboard', compact('totalProducts', 'totalEmployees', 'totalCustomers', 'totalPosts', 'totalOrders'));
    }


    public function products()
    {
        return view('admin.products');
    }

    public function categories()
    {
        return view('admin.categories');
    }

    public function brands()
    {
        return view('admin.brands');
    }

    public function employees()
    {
        return view('admin.employees');
    }

    public function revenue()
    {
        return view('admin.revenue');
    }

    public function reports()
    {
        return view('admin.reports');
    }
}
