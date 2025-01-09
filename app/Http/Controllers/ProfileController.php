<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\PasswordChanged;

class ProfileController extends Controller
{
    // Hiển thị thông tin hồ sơ người dùng
    public function show()
    {
        $user = Auth::user();  // Lấy thông tin user đã đăng nhập
        return view('profile.show', compact('user'));  // Trả về view với thông tin user
    }

    // Cập nhật hồ sơ người dùng (bao gồm ảnh đại diện)
    public function updateProfile(Request $request)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user(); // Lấy người dùng hiện tại
    
            // Validate thông tin người dùng
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'birth_date' => 'nullable|date_format:d/m/Y',
            ]);
    
            // Cập nhật thông tin cá nhân
            $user->name = $validated['name'];
            $user->phone = $validated['phone'] ?? $user->phone;
            
            // Nếu có ngày sinh, chuyển ngày sinh từ d/m/Y sang Y-m-d
            if ($validated['birth_date']) {
                $user->birth_date = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['birth_date'])->format('Y-m-d');
            }
    
            // Lưu thay đổi
            $user->save();
    
            return redirect()->route('profile.show')->with('success', 'Cập nhật thông tin thành công');
        } catch (\Exception $e) {
            // In ra lỗi để dễ dàng debug
            return redirect()->route('profile.show')->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    public function uploadAvatar(Request $request)
{
    
    $request->validate([
        'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($request->hasFile('profile_picture')) {
        $image = $request->file('profile_picture');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('public/avatars', $imageName); // Lưu ảnh vào storage
 /** @var \App\Models\User $user */
        $user = Auth::user();

        // Nếu đã có ảnh đại diện trước đó, xóa ảnh cũ
        if ($user->profile_picture) {
            Storage::delete($user->profile_picture); // Xóa ảnh cũ
        }

        // Cập nhật đường dẫn ảnh đại diện trong cơ sở dữ liệu
        $user->profile_picture = $path;
        $user->save();

        return redirect()->route('profile.show')->with('success', 'Ảnh đại diện đã được cập nhật.');
    }

    return redirect()->route('profile.show')->with('error', 'Không có ảnh nào được chọn.');
}   
public function removeAvatar()
{ /** @var \App\Models\User $user */
    $user = Auth::user();

    if ($user->profile_picture) {
        // Xóa file ảnh trên server
        Storage::delete('public/avatars/' . basename($user->profile_picture));
        $user->profile_picture = null;
        $user->save();

        // Trả về thông báo thành công
        return redirect()->route('profile.show')->with('success', 'Ảnh đại diện đã được gỡ bỏ');
    }

    return redirect()->route('profile.show')->with('error', 'Không tìm thấy ảnh để gỡ');
}
    // Cập nhật mật khẩu
    public function updatePassword(Request $request)
    { /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        // Cập nhật mật khẩu mới
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Gửi email thông báo
        Mail::to($user->email)->send(new PasswordChanged($user));

        // Redirect to the correct profile view based on role
            return redirect()->route('profile.show')->with('success', 'Mật khẩu đã được thay đổi và thông báo đã được gửi đến email của bạn!');
    }
}