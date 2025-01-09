<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;  // Sử dụng đúng Auth facade
use App\Models\Admin;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Phương thức để kiểm tra quyền admin
    public function dashboard()
    {
        // Kiểm tra người dùng đã đăng nhập chưa
        if (Auth::check()) {
            $user = Auth::user(); // Lấy người dùng đang đăng nhập

            // Kiểm tra xem người dùng có tồn tại trong bảng admin không
            $admin = Admin::where('email', $user->email)->first();

            // Nếu là admin, cho phép truy cập trang dashboard admin
            if ($admin) {
                return view('admin.dashboard'); // Hiển thị trang dashboard admin
            }
        }

        // Nếu người dùng chưa đăng nhập, chuyển hướng về trang đăng nhập
        return redirect()->route('login');
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
