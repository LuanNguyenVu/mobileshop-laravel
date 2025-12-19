<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    // QUAN TRỌNG: Phải khớp với tên cột trong DB của bạn
    protected $fillable = ['cart_id', 'product_variant_id', 'quantity'];

    public function variant()
    {
        // Liên kết với model ProductVariant qua khóa ngoại 'product_variant_id'
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}