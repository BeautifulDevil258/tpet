<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = ['code', 'discount', 'quantity', 'min_score', 'expires_at', 'status'];
    protected $casts = [
        'expires_at' => 'datetime',
    ];
    
    public function decrementQuantity()
    {
        $this->decrement('quantity');
    }
}

