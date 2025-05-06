<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use App\Mail\OtpMail;
use App\Models\PasswordResetToken;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Hiển thị form đăng ký
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Xử lý đăng nhập
    public function login(Request $request)
{
    // Validate dữ liệu đầu vào
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Kiểm tra nếu email là của admin
    $admin = Admin::where('email', $request->email)->first();
    if ($admin) {
        // Nếu là admin, sử dụng guard admin để đăng nhập
        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->to('/admin')->with('success', 'Đăng nhập thành công');
        }
        return back()->withErrors(['password' => 'Mật khẩu không chính xác.'])->withInput();
    }
    // Kiểm tra nếu email là của user
    $user = User::where('email', $request->email)->first();
    if ($user) {
        // Nếu là user, sử dụng guard web để đăng nhập
        if (Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->to('/')->with('success', 'Đăng nhập thành công');
        }
        return back()->withErrors(['password' => 'Mật khẩu không chính xác.'])->withInput();
    }

    // Nếu email không tồn tại trong cả bảng admin và user
    return back()->withErrors(['email' => 'Email không tồn tại.'])->withInput();
}

    // Đăng ký người dùng
    public function register(Request $request)
    {
        // Validate the data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|digits:10',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
        ]);

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'role' => 'user',
        ]);

        // Log the user in
        Auth::login($user);

        // Redirect to login page or home
        return redirect()->route('login');
    }

    // Đăng xuất
    public function logout(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login'); // Chuyển hướng về trang login admin
        }
    
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('home'); // Chuyển hướng về trang login người dùng
        }
    
        return redirect()->route('login');
    }
    public function showResetForm()
{
    return view('auth.password.email');
}
public function sendResetLinkEmail(Request $request)
{
    $request->validate(['email' => 'required|email']);

    // Tìm user hoặc admin theo email
    $user = User::where('email', $request->email)->first();
    $admin = Admin::where('email', $request->email)->first();

    if (!$user && !$admin) {
        return back()->withErrors(['email' => 'Email không tồn tại.']);
    }

    // Tạo token và lưu vào database
    $token = Str::random(60);

    // Kiểm tra xem token đã tồn tại chưa
    $existingToken = PasswordResetToken::where('email', $request->email)->first();

    if ($existingToken) {
        // Nếu đã tồn tại, cập nhật token và thời gian tạo
        $existingToken->token = $token;
        $existingToken->created_at = now();
        $existingToken->save();
    } else {
        // Nếu không tồn tại, tạo mới một bản ghi
        PasswordResetToken::create([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now(),
        ]);
    }

    // Gửi email chứa liên kết đặt lại mật khẩu
    $recipientType = $user ? 'User' : 'Admin';
    Mail::to($request->email)->send(new ResetPasswordMail($token, $recipientType));

    return back()->with('status', 'Chúng tôi đã gửi liên kết đặt lại mật khẩu đến email của bạn.');
}
public function showResetFormWithToken($token)
{
    return view('auth.password.reset')->with(['token' => $token]);
}

public function reset(Request $request)
{
    $request->validate([
        'password' => 'required|confirmed|min:8',
        'token' => 'required',
    ]);

    // Tìm token trong database
    $passwordResetToken = PasswordResetToken::where('token', $request->token)->first();

    if (!$passwordResetToken) {
        return back()->withErrors(['token' => 'Token không hợp lệ.']);
    }

    // Tìm user hoặc admin với email tương ứng với token
    $user = User::where('email', $passwordResetToken->email)->first();
    $admin = Admin::where('email', $passwordResetToken->email)->first();

    if (!$user && !$admin) {
        return back()->withErrors(['email' => 'Email không tồn tại.']);
    }

    // Reset mật khẩu
    if ($user) {
        $user->password = Hash::make($request->password);
        $user->save();
    } elseif ($admin) {
        $admin->password = Hash::make($request->password);
        $admin->save();
    }

    // Xóa token sau khi đã sử dụng
    $passwordResetToken->delete();

    // Đăng nhập lại sau khi thay đổi mật khẩu
    if ($admin) {
        Auth::guard('admin')->login($admin);
    } elseif ($user) {
        Auth::guard('web')->login($user);
    }

    return redirect('/login')->with('success', 'Mật khẩu đã được đặt lại thành công.');
}
}