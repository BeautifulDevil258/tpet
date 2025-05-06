<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\UserVoucher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = Voucher::query();
    
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('code', 'like', "%{$search}%");
        }
    
        $vouchers = $query->orderBy('created_at', 'desc')->get();
    
        return view('admin.vouchers.index', compact('vouchers'));
    }
    

    public function create()
    {
        return view('admin.vouchers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:vouchers|max:20',
            'discount' => 'required|integer|min:1|max:100',
            'quantity' => 'required|integer|min:1',
            'min_score' => 'required|integer|min:1'
        ]);

        Voucher::create($request->all());

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher đã được thêm thành công!');
    }

    public function edit($id)
    {
        $voucher = Voucher::findOrFail($id);
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, $id)
    {
        $voucher = Voucher::findOrFail($id);

        $request->validate([
            'code' => 'required|max:20|unique:vouchers,code,' . $id,
            'discount' => 'required|integer|min:1|max:100',
            'quantity' => 'required|integer|min:1',
            'min_score' => 'required|integer|min:1'
        ]);

        $voucher->update($request->all());

        return redirect()->route('vouchers.index')->with('success', 'Voucher đã được cập nhật thành công!');
    }

    public function destroy($id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->delete();

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher đã được xóa!');
    }
    public function spinForm()
    {
        $vouchers = Voucher::where('quantity', '>', 0)->get();
    
        $spinOptions = [];
    
        foreach ($vouchers as $voucher) {
            $spinOptions[] = [
                'label' => $voucher->code,
                'type' => 'voucher',
                'id' => $voucher->id,
            ];
        }
    
        // Thêm các ô "trượt"
        $spinOptions[] = ['label' => 'Chúc bạn may mắn lần sau!', 'type' => 'miss'];
        $spinOptions[] = ['label' => 'Suýt trúng rồi!', 'type' => 'miss'];
        $spinOptions[] = ['label' => 'Xém tí nữa trúng!', 'type' => 'miss'];
        $spinOptions[] = ['label' => 'Ôi không! Hụt rồi 😅', 'type' => 'miss'];
    
        return view('vouchers.spin', compact('spinOptions'));
    }
    
    public function spin(Request $request)
    {
        $user = Auth::user();
    
        $type = $request->input('result_type');
        $label = $request->input('result_label');
        $id = $request->input('result_id');
    
        if ($type === 'miss') {
            return redirect()->back()->with('error', $label);
        }
    
        if ($type === 'voucher') {
            try {
                DB::beginTransaction();
    
                $voucher = Voucher::where('id', $id)->where('quantity', '>', 0)->first();
    
                if (!$voucher) {
                    return redirect()->back()->with('error', 'Voucher không còn khả dụng!');
                }
    
                $voucher->decrement('quantity');
    
                UserVoucher::create([
                    'user_id' => $user->id,
                    'voucher_id' => $voucher->id,
                ]);
    
                DB::commit();
    
                return redirect()->back()->with('success', '🎉 Bạn nhận được voucher: ' . $voucher->code);
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại.');
            }
        }
    
        return redirect()->back()->with('error', 'Kết quả không hợp lệ.');
    }
    
}
