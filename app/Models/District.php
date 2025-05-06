<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'city_id'];

    // Quan hệ với bảng cities
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    // Quan hệ với bảng wards
    public function wards()
    {
        return $this->hasMany(Ward::class);
    }
}

