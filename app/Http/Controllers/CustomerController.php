<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $customers = User::where('name', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%')
                            ->orWhere('phone', 'like', '%'.$search.'%')
                            ->paginate(10);
        return view('admin.customers.index', compact('customers'));
    }
    

    public function show($id)
    {
        $customer = User::findOrFail($id);
        $currentAddress = $customer->addresses->where('is_default', true)->first();
        return view('admin.customers.show', compact('customer', 'currentAddress'));
    }

    public function edit($id)
    {
        $customer = User::findOrFail($id);
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = User::findOrFail($id);

        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $customer->id,
            'phone'   => 'nullable|digits_between:10,12',
            'address' => 'nullable|string',
            'group'   => 'required|string',
        ]);

        $customer->update($request->all());

        return redirect()->route('customers.index')->with('success', 'Cập nhật khách hàng thành công!');
    }

    public function destroy($id)
    {
        User::destroy($id);
        return redirect()->route('customers.index')->with('success', 'Xóa khách hàng thành công!');
    }
}
