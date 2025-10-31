<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'image_url',
        'active',
        'category_id',
    ];

    protected $casts = [
        'price'       => 'decimal:2',
        'active'      => 'boolean',
        'category_id' => 'integer',
    ];

    // Relaciones
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
                    ->withPivot(['quantity', 'unit_price', 'line_total'])
                    ->withTimestamps();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Accessor para obtener la URL completa de la imagen
    public function getImageUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }
        
        // Si ya empieza con "/", es una ruta relativa desde la raíz pública
        if (str_starts_with($value, '/')) {
            return $value;
        }
        
        // Si no, es una ruta desde storage
        return 'storage/' . $value;
    }

    // Scopes para limpiar el controlador index()
    public function scopeSearch($q, ?string $term)
    {
        if (!$term) return $q;
        return $q->where(fn($w) =>
            $w->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
        );
    }

    public function scopeByCategory($q, $categoryId)
    {
        if (!$categoryId) return $q;
        return $q->where('category_id', (int) $categoryId);
    }

    public function scopeActiveFlag($q, $active)
    {
        if (is_null($active)) return $q;
        return $q->where('active', (bool) $active);
    }
}
