<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    // Thêm thuộc tính 'name' vào mảng $fillable để cho phép mass assignment
    protected $fillable = ['name'];
}