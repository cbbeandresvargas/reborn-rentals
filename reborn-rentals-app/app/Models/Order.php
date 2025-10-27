<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function job()
    {
        return $this->belongsTo(JobLocation::class, 'job_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cupon()
    {
        return $this->belongsTo(Cupon::class, 'cupon_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items')
                    ->withPivot(['quantity', 'unit_price', 'line_total'])
                    ->withTimestamps();
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
