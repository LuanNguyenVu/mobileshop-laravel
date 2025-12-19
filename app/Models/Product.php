<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'brand',            // Tương ứng manufacturer cũ
        'os',               // Có thể dùng hoặc dùng operating_system
        'type',             // Loại sản phẩm (nếu có)
        'product_image',
        'rating',
        'ram',
        'rom',
        'camera',
        'battery',
        'status',
        'screen',
        'front_camera',
        'cpu',
        'gpu',
        'operating_system',
        'description',      // Tương ứng article cũ
        'detailed_specs',
    ];

    // Quan hệ với variants
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }


    /**
     * Accessor: Lấy giá bán thấp nhất trong các biến thể để hiển thị ra ngoài danh sách
     * Sử dụng: $product->min_price
     */
    public function getMinPriceAttribute()
    {
        // Lấy biến thể có giá thấp nhất
        $minVariant = $this->variants->sortBy('selling_price')->first();
        return $minVariant ? $minVariant->selling_price : 0;
    }

    public function reviews() {
        return $this->hasMany(Review::class)->orderBy('created_at', 'desc');
    }
}