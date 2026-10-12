<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Look extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function activeProducts(): BelongsToMany
    {
        return $this->products()->storefront();
    }

    public function lookImages(): HasMany
    {
        return $this->hasMany(LookImage::class)->orderBy('sort_order');
    }

    public function getCoverUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }

        $first = $this->lookImages->first();

        if ($first) {
            return asset('storage/' . $first->image);
        }

        return asset('assets/hero.jpg');
    }
}