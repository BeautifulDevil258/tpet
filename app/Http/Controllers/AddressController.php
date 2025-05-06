<?php
namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = auth()->user()->addresses()->orderByDesc('is_default')->get();
        return view('addresses.index', compact('addresses'));
    }
    public function create(Request $request)
    {
        if ($request->has('return_url')) {
            session(['return_url' => $request->return_url]);
        }
        return view('addresses.create');
    }
    public function store(Request $request)
    {
        // Xác nhận dữ liệu từ người dùng
        $validated = $request->validate([
            'name'       => 'required|string|max:500',
            'phone'      => 'nullable|string|digits:10',
            'detail'     => 'required|string|max:255',
            'ward'       => 'required|string|max:100',
            'district'   => 'required|string|max:100',
            'city'       => 'required|string|max:100',
            'is_default' => 'nullable|boolean', // Cho phép null hoặc boolean
        ]);

        // Nếu chọn làm địa chỉ mặc định, đặt tất cả địa chỉ khác không mặc định
        if ($request->is_default) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }

        // Nếu is_default không có trong dữ liệu (null), mặc định là false
        $validated['is_default'] = $validated['is_default'] ?? false;

        // Tạo địa chỉ mới cho người dùng
        auth()->user()->addresses()->create($validated);

        $returnUrl = session('return_url', route('addresses.index'));
        session()->forget('return_url'); // Xóa để tránh xung đột sau
        return redirect($returnUrl)->with('success', 'Địa chỉ mới đã được thêm!');
    }

    public function edit(Request $request, $id)
    {
        if ($request->has('return_url')) {
            session(['return_url' => $request->return_url]);
        }
        // Tìm sản phẩm theo ID
        $address = Address::findOrFail($id);
        return view('addresses.edit', compact('address'));
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:500',
            'phone'      => 'nullable|string|digits:10',
            'detail'     => 'required|string|max:255',
            'ward'       => 'required|string|max:100',
            'district'   => 'required|string|max:100',
            'city'       => 'required|string|max:100',
            'is_default' => 'boolean',
        ]);

        $address = auth()->user()->addresses()->findOrFail($id);
        if ($request->is_default) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }

        $address->update($validated);

        $returnUrl = session('return_url', route('addresses.index'));
        session()->forget('return_url');
        return redirect($returnUrl)->with('success', 'Địa chỉ đã được cập nhật!');
    }
    public function destroy($id)
    {
        $address = auth()->user()->addresses()->findOrFail($id);
        $address->delete();

        return redirect()->back()->with('success', 'Địa chỉ đã được xóa!');
    }
}
