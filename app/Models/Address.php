<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'phone', 'detail', 'ward', 'district', 'city', 'is_default'];

    // Quan hệ một đến nhiều với User (mỗi người dùng có thể có nhiều địa chỉ)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

