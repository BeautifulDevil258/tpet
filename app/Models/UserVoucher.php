<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVoucher extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'voucher_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function vouchers()
{
    return $this->belongsToMany(Voucher::class, 'user_vouchers', 'user_id', 'voucher_id');
}
}
