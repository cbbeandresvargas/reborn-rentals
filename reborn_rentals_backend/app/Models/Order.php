<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'total_amount',
        'status',
        'discount_total',
        'ordered_at',
        'payment_method',
        'tax_total',
        'transaction_id',
        'notes',
        'job_id',
        'user_id',
        'cupon_id',
    ];

    protected $casts = [
        'status'         => 'boolean',
        'ordered_at'     => 'datetime',
        'total_amount'   => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total'      => 'decimal:2',
    ];

    // 1:1 (lado "belongsTo"): la FK está en orders.job_id
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    // N:1 con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // N:1 con Cupon
    public function cupon()
    {
        return $this->belongsTo(Cupon::class, 'cupon_id');
    }

    // N:M con Products vía order_items
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items')
                    ->withPivot(['quantity', 'unit_price', 'line_total'])
                    ->withTimestamps();
    }

    // Acceso directo a las líneas de la orden
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}