<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LargeCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Mối quan hệ với danh mục nhỏ
    public function smallCategories()
    {
        return $this->hasMany(SmallCategory::class);
    }

    // Mối quan hệ gián tiếp với sản phẩm thông qua danh mục nhỏ
    public function products()
    {
        return $this->hasManyThrough(Product::class, SmallCategory::class);
    }
}
