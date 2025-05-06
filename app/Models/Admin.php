<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // Sử dụng Authenticatable cho cả User và Admin
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Các thuộc tính có thể gán hàng loạt.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'birth_date',
        'gender',
    ];

    /**
     * Các thuộc tính nên được ẩn khi được serialize.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Các thuộc tính cần phải ép kiểu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Mã hóa mật khẩu trước khi lưu vào cơ sở dữ liệu.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
    public function checkInCheckOutLogs()
    {
        return $this->hasMany(CheckInCheckOutLog::class, 'admin_id');
    }

}
