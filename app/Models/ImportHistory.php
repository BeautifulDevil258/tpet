<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportHistory extends Model
{
    use HasFactory;

    protected $table = 'import_history';

    protected $fillable = [
        'product_id', 'import_price', 'quantity', 'total_cost', 'import_date'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
