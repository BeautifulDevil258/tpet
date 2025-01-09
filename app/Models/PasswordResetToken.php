<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    protected $table = 'password_reset_tokens';

    protected $fillable = [
        'email', 'token', 'created_at',
    ];

    public $timestamps = false; // Nếu bảng không có các trường created_at và updated_at

    // Chỉ định rằng không có khóa chính tự động tăng
    public $incrementing = false; 

    // Chỉ định trường khóa chính
    protected $primaryKey = 'email'; 

    // Chỉ định kiểu dữ liệu khóa chính là chuỗi
    protected $keyType = 'string'; 
}
