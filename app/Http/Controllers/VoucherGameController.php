<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserVoucher;
use App\Models\Voucher;
use Illuminate\Support\Facades\Auth;

class VoucherGameController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::all(); // Lấy tất cả voucher
        return view('game.voucher_game', compact('vouchers'));
    }

    public function claimVoucher(Request $request)
    {
        $request->validate([
            'voucher_id' => 'required|exists:vouchers,id'
        ]);
    
        $user = Auth::user();
        $voucher = Voucher::findOrFail($request->voucher_id);
    
        // Kiểm tra xem user đã nhận voucher này chưa
        if (UserVoucher::where('user_id', $user->id)->where('voucher_id', $voucher->id)->exists()) {
            return response()->json(['message' => 'Bạn đã nhận voucher này rồi!'], 400);
        }
    
        // Lưu voucher cho user
        UserVoucher::create([
            'user_id' => $user->id,
            'voucher_id' => $voucher->id
        ]);
    
        return response()->json(['message' => 'Chúc mừng! Bạn đã nhận được voucher. Hãy kiểm tra tài khoản của bạn.']);
    }
    
}
