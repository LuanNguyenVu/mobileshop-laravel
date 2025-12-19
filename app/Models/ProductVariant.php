<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'color',
        'image',            // Cột ảnh biến thể
        'quantity',
        'selling_price',
        'purchase_price',
        'promotional_price',
        'promotion_end_date',
    ];

    /**
     * Quan hệ ngược: Một biến thể thuộc về một sản phẩm
     * Sử dụng: $variant->product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}