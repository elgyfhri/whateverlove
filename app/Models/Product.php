<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'weight',
        'category',
        'article_id',
        'badge',
        'image',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'weight' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeStorefront($query)
    {
        return $query->where('is_active', true)->whereHas('sizes');
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function sizes(): HasMany
    {
        return $this->hasMany(ProductSize::class);
    }

    public function getTotalStockAttribute(): int
    {
        return (int) $this->sizes()->sum('stock');
    }

    public function getInStockAttribute(): bool
    {
        return $this->sizes()->where('stock', '>', 0)->exists();
    }

    public function getCategoryLabelAttribute(): string
    {
        if (!$this->category) {
            return 'Uncategorized';
        }

        return Category::where('slug', $this->category)->value('name') ?? ucfirst($this->category);
    }
}
