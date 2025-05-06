<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmallCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image', 'large_category_id'];

    // Mối quan hệ với danh mục lớn
    public function largeCategory()
    {
        return $this->belongsTo(LargeCategory::class);
    }

    // Mối quan hệ với sản phẩm
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
