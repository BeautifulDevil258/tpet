<?php
namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Admin::where('role', 'nhanvien');
    
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', $search) 
                  ->orWhere('name', 'like', "%$search%") 
                  ->orWhere('email', 'like', "%$search%");
            });
        }
        
    
        $employees = $query->get();
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }
    public function store(Request $request)
    {
        // Xác thực dữ liệu
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|string|email|max:255|unique:users,email',
            'password'        => 'required|string|min:6',
            'phone'           => 'nullable|string|max:15',
            'birth_date'      => 'nullable|date',
            'gender'          => 'nullable|in:male,female,other',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Tạo một đối tượng User mới
        $employee             = new Admin();
        $employee->name       = $request->name;
        $employee->email      = $request->email;
        $employee->phone      = $request->phone;
        $employee->birth_date = $request->birth_date;
        $employee->gender     = $request->gender;
        $employee->role       = 'nhanvien';
        $employee->password   = Hash::make($request->password); // Mã hóa mật khẩu

        // Lưu thông tin ảnh đại diện nếu có
        if ($request->hasFile('profile_picture')) {
            $image     = $request->file('profile_picture');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $path      = $image->storeAs('public/avatars', $imageName); // Lưu ảnh vào storage

            // Cập nhật đường dẫn ảnh đại diện
            $employee->profile_picture = $path;
        }

        // Lưu thông tin nhân viên vào cơ sở dữ liệu
        $employee->save();

        // Chuyển hướng về trang danh sách nhân viên với thông báo thành công
        return redirect()->route('employees.index')->with('success', 'Thêm nhân viên thành công.');
    }

    public function edit(Admin $employee)
    {
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        // Xác thực dữ liệu
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|string|email|max:255|unique:users,email,' . $id,
            'password'        => 'nullable|string|min:6',
            'phone'           => 'nullable|string|max:15',
            'birth_date'      => 'nullable|date',
            'gender'          => 'nullable|in:male,female,other',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Lấy thông tin nhân viên cần cập nhật
        $employee = Admin::findOrFail($id);

        // Cập nhật thông tin nhân viên
        $employee->name       = $request->name;
        $employee->email      = $request->email;
        $employee->phone      = $request->phone;
        $employee->birth_date = $request->birth_date;
        $employee->gender     = $request->gender;

        // Cập nhật mật khẩu nếu có
        if ($request->password) {
            $employee->password = Hash::make($request->password);
        }

        if ($request->hasFile('profile_picture')) {
            $image     = $request->file('profile_picture');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $path      = $image->storeAs('public/avatars', $imageName); // Lưu ảnh vào storage
            /** @var \App\Models\User $user */
            $employee = Admin::findOrFail($id);
            // Nếu đã có ảnh đại diện trước đó, xóa ảnh cũ
            if ($employee->profile_picture) {
                Storage::delete($employee->profile_picture); // Xóa ảnh cũ
            }

            // Cập nhật đường dẫn ảnh đại diện trong cơ sở dữ liệu
            $employee->profile_picture = $path;
            // Lưu thay đổi vào cơ sở dữ liệu
            $employee->save();

            // Chuyển hướng về trang danh sách nhân viên với thông báo thành công
            return redirect()->route('employees.index')->with('success', 'Cập nhật nhân viên thành công.');
        }
    }

    public function destroy(Admin $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Nhân viên đã bị xóa.');
    }

}
