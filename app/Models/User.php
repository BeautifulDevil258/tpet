<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'birth_date',
        'gender',
        'rank',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
    // Quan hệ một đến nhiều với Address
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
     // Tính tổng số đơn hàng đã hoàn thành
     public function getTotalOrdersAttribute()
     {
         return $this->orders()->where('status', 'completed')->count();
     }
     public function vouchers()
     {
         // Sử dụng phương thức belongsToMany để khai báo quan hệ nhiều-nhiều
         return $this->belongsToMany(Voucher::class, 'user_vouchers', 'user_id', 'voucher_id');
     }
 
     // Tính tổng tiền của các đơn hàng đã hoàn thành
     public function getTotalSpentAttribute()
     {
         return $this->orders()->where('status', 'completed')->sum('total_price');
     }
     public function updateRank()
     {
         $totalSpent = $this->orders()->whereIn('status', ['Đã giao', 'completed'])->sum('total_price');
         Log::info("🏆 Cập nhật hạng khách hàng {$this->id}: Tổng chi tiêu = {$totalSpent}");
     
         if ($totalSpent >= 10_000_000) {
             $this->rank = 'Vàng';
         } elseif ($totalSpent >= 5_000_000) {
             $this->rank = 'Bạc';
         } else {
             $this->rank = 'Đồng';
         }
     
         $this->save();
         Log::info("✔️ Hạng mới của khách hàng {$this->id}: {$this->rank}");
     }
}
