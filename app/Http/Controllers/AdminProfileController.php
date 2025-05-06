<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\PasswordChanged;
use App\Models\Admin;

class AdminProfileController extends Controller
{
    // Hiển thị thông tin hồ sơ người dùng
    public function index()
    {
        $admin = Auth::guard('admin')->user();  // Lấy thông tin admin đã đăng nhập
        return view('admin.adminprofile.index', compact('admin'));  // Trả về view với thông tin admin
    }

    // Cập nhật hồ sơ người dùng (bao gồm ảnh đại diện)
    public function updateProfile(Request $request)
    {
        try {
            /** @var \App\Models\Admin $admin */
            $admin = Auth::guard('admin')->user(); // Lấy admin hiện tại
    
            // Validate thông tin người dùng
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'birth_date' => 'nullable|date_format:d/m/Y', // Kiểm tra ngày theo định dạng d/m/Y
            ]);
    
            // Cập nhật thông tin cá nhân
            $admin->name = $validated['name'];
            $admin->phone = $validated['phone'] ?? $admin->phone;
            
            // Nếu có ngày sinh, chuyển ngày sinh từ d/m/Y sang Y-m-d
            if ($validated['birth_date']) {
                $admin->birth_date = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['birth_date'])->format('Y-m-d');
            }
    
            // Lưu thay đổi
            $admin->save();
    
            return redirect()->route('admin.adminprofile.index')->with('success', 'Cập nhật thông tin thành công');
        } catch (\Exception $e) {
            // In ra lỗi để dễ dàng debug
            return redirect()->route('admin.adminprofile.index')->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // Cập nhật ảnh đại diện cho admin
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/avatars', $imageName); // Lưu ảnh vào storage
            /** @var \App\Models\Admin $admin */
            $admin = Auth::guard('admin')->user();

            // Nếu đã có ảnh đại diện trước đó, xóa ảnh cũ
            if ($admin->profile_picture) {
                Storage::delete($admin->profile_picture); // Xóa ảnh cũ
            }

            // Cập nhật đường dẫn ảnh đại diện trong cơ sở dữ liệu
            $admin->profile_picture = $path;
            $admin->save();

            return redirect()->route('admin.adminprofile.index')->with('success', 'Ảnh đại diện đã được cập nhật.');
        }

        return redirect()->route('admin.adminprofile.index')->with('error', 'Không có ảnh nào được chọn.');
    }

    // Xóa ảnh đại diện cho admin
    public function removeAvatar()
    {
        /** @var \App\Models\Admin $admin */
        $admin = Auth::guard('admin')->user();

        if ($admin->profile_picture) {
            // Xóa file ảnh trên server
            Storage::delete('public/avatars/' . basename($admin->profile_picture));
            $admin->profile_picture = null;
            $admin->save();

            // Trả về thông báo thành công
            return redirect()->route('admin.adminprofile.index')->with('success', 'Ảnh đại diện đã được gỡ bỏ');
        }

        return redirect()->route('admin.adminprofile.index')->with('error', 'Không tìm thấy ảnh để gỡ');
    }

    // Cập nhật mật khẩu cho admin
    public function updatePassword(Request $request)
    {
        /** @var \App\Models\Admin $admin */
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        // Cập nhật mật khẩu mới
        $admin->password = Hash::make($request->new_password);
        $admin->save();

        // Gửi email thông báo
        Mail::to($admin->email)->send(new PasswordChanged($admin));

        // Redirect to the correct profile view
        return redirect()->route('admin.adminprofile.index')->with('success', 'Mật khẩu đã được thay đổi và thông báo đã được gửi đến email của bạn!');
    }
}
