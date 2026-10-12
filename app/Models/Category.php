<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'image',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category', 'slug');
    }

    public function getCoverUrlAttribute(): string
    {
        return $this->image ? asset('storage/' . $this->image) : asset('assets/hero.jpg');
    }
}