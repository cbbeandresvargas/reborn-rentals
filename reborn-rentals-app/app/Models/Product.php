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
        'stock',
        'image_url',
        'active',
        'hidden',
        'category_id',
        'odoo_product_id',
    ];

    protected $casts = [
        'price'       => 'decimal:2',
        'stock'       => 'integer',
        'active'      => 'boolean',
        'hidden'      => 'boolean',
        'category_id' => 'integer',
        'odoo_product_id' => 'integer',
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

    // ✅ Accessor que acepta AMBAS rutas
    public function getImageAttribute()
    {
        if (!$this->image_url) {
            return asset('images/no-image.png');
        }
        
        // Si ya es una URL completa (http/https)
        if (str_starts_with($this->image_url, 'http')) {
            return $this->image_url;
        }
        
        // Si ya empieza con "/" (ruta absoluta)
        if (str_starts_with($this->image_url, '/')) {
            return asset(ltrim($this->image_url, '/'));
        }
        
        // Detectar si la imagen existe en public/ directamente
        if (file_exists(public_path($this->image_url))) {
            return asset($this->image_url);
        }
        
        // Si no existe en public/, asumir que está en storage/
        return asset('storage/' . $this->image_url);
    }

    // ✅ Accessor con timestamp (evita caché del navegador)
    public function getImageFreshAttribute()
    {
        if (!$this->image_url) {
            return asset('images/no-image.png');
        }
        
        $baseUrl = $this->image; // Usa el accessor anterior
        
        return $baseUrl . '?v=' . $this->updated_at->timestamp;
    }

    // Scopes
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

    // Scope para obtener solo productos visibles para compradores
    // Un producto es visible si está activo Y no está oculto
    public function scopeVisible($q)
    {
        return $q->where('active', true)->where('hidden', false);
    }
}