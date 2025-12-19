<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 
        'product_variant_id', 
        'product_name', 
        'variant_color', 
        'quantity', 
        'price_at_order'
    ];

    // Quan hệ ngược về Đơn hàng
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // --- QUAN TRỌNG: ĐÂY LÀ HÀM BẠN ĐANG THIẾU ---
    // Hàm này giúp lấy thông tin ảnh, RAM, ROM từ bảng product_variants
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}