<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'total_amount',
        'subtotal',
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
        'foreman_details_json',
        'billing_details_json',
        'payment_method_details_json',
        'odoo_sale_order_id',
        'odoo_invoice_id',
        'odoo_sync_status',
    ];

    protected $casts = [
        'status'         => 'string',
        'ordered_at'     => 'datetime',
        'total_amount'   => 'decimal:2',
        'subtotal'       => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total'      => 'decimal:2',
        'odoo_sale_order_id' => 'integer',
        'odoo_invoice_id' => 'integer',
        'odoo_sync_status' => 'string',
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
