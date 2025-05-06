<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image', 'price', 'import_price', 'quantity', 'description', 'small_category_id'];

    // Mối quan hệ với danh mục nhỏ
    public function smallCategory()
    {
        return $this->belongsTo(SmallCategory::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function importHistory()
    {
        return $this->hasMany(ImportHistory::class);
    }
}
