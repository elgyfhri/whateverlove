<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'title',
        'link_text',
        'link',
        'image',
        'sort_order',
    ];

    public function getCoverUrlAttribute(): string
    {
        return $this->image ? asset('storage/' . $this->image) : asset('assets/hero.jpg');
    }
}