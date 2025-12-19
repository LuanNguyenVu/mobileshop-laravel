<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- Dòng này quan trọng
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'product_id', 
        'rating', 
        'comment'
    ];

    // Quan hệ với bảng User (Người đánh giá)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với bảng Product (Sản phẩm được đánh giá)
    public function product()
    {
        // Đảm bảo Model Product tồn tại
        return $this->belongsTo(Product::class, 'product_id');
    }
}