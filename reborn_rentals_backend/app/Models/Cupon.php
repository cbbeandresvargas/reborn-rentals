<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Cupon extends Model
{
    protected $table = 'cupons';
    use HasFactory; 
    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'max_uses',
        'min_order_total',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'discount_value'  => 'decimal:2',
        'min_order_total' => 'decimal:2',
        'starts_at'       => 'datetime',
        'expires_at'      => 'datetime',
        'is_active'       => 'boolean',
    ];

    // Un cupón puede aplicarse a muchas órdenes (una por orden)
    public function orders()
    {
        return $this->hasMany(Order::class, 'cupon_id');
    }
}