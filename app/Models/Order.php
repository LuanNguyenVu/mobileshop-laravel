<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'user_id', // Nếu user đặt hàng (có thể null nếu khách vãng lai)
        'account_id', // Lưu ý: Database cũ của bạn dùng account_id hay user_id? Hãy thống nhất. Ở đây mình dùng user_id theo chuẩn Laravel auth.
        'receiver_name',
        'receiver_phone',
        'receiver_email',
        'receiver_address',
        'note',
        'payment_method',
        'total_amount',
        'order_status',
        'order_date' // Nếu bạn dùng timestamps (created_at) thì cột này có thể bỏ qua, hoặc map nó
    ];

    // Quan hệ với chi tiết đơn hàng
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    // Quan hệ với người dùng (nếu có)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Hoặc account_id tùy DB của bạn
    }
}