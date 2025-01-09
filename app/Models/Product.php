<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image', 'price', 'quantity', 'description', 'discount_price', 'small_category_id'];

    // Mối quan hệ với danh mục nhỏ
    public function smallCategory()
    {
        return $this->belongsTo(SmallCategory::class);
    }
}
