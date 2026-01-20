<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // ✅ KËTU i shton fushat
    protected $fillable = [
        'user_id',
        'total',
        'status',
        'phone',
        'payment_method',
        'payment_status',
    ];

    // (opsionale por shumë e rekomanduar)
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
