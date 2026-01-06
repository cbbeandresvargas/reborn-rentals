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

    // ✅ CORRECTO: Accessor con nombre diferente
    public function getImageAttribute()
    {
        if (!$this->image_url) {
            return asset('images/no-image.png'); // Imagen por defecto
        }
        
        // Si ya empieza con "http" es una URL completa
        if (str_starts_with($this->image_url, 'http')) {
            return $this->image_url;
        }
        
        // Si empieza con "/", es una ruta relativa desde la raíz pública
        if (str_starts_with($this->image_url, '/')) {
            return asset($this->image_url);
        }
        
        // Si no, es una ruta desde storage
        return asset('storage/' . $this->image_url);
    }

    // ✅ Accessor con timestamp para evitar caché del navegador
    public function getImageFreshAttribute()
    {
        if (!$this->image_url) {
            return asset('images/no-image.png');
        }
        
        $url = str_starts_with($this->image_url, 'http') 
            ? $this->image_url 
            : (str_starts_with($this->image_url, '/') 
                ? asset($this->image_url) 
                : asset('storage/' . $this->image_url));
        
        return $url . '?v=' . $this->updated_at->timestamp;
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
}